<?php
// adaptive_engine.php
// Requires $con as mysqli connection

/*********************
 *  Hyperparameters
 *********************/
define('BKT_P_TRANS', 0.10);   // probability of learning between attempts (transition)
define('BKT_P_GUESS', 0.2);    // guess probability (student answers correctly despite not knowing)
define('BKT_P_SLIP', 0.1);     // slip probability (student knows but answers incorrectly)

define('RL_ALPHA', 0.2);       // learning rate
define('RL_GAMMA', 0.9);       // discount factor
define('RL_EPSILON', 0.15);    // epsilon for epsilon-greedy exploration (you can schedule this)

/*********************
 *  Utilities
 *********************/
function fetch_user_skill($con, $user_id, $skill_id) {
    $stmt = $con->prepare("SELECT p_learned FROM user_skills WHERE user_id=? AND skill_id=?");
    $stmt->bind_param("ii", $user_id, $skill_id);
    $stmt->execute();
    $p = null;
    $stmt->bind_result($p);
    if ($stmt->fetch()) {
        $stmt->close();
        return (double)$p;
    }
    $stmt->close();
    // default prior if not existing
    $p0 = 0.2;
    $ins = $con->prepare("INSERT INTO user_skills (user_id, skill_id, p_learned) VALUES (?, ?, ?)");
    $ins->bind_param("iid", $user_id, $skill_id, $p0);
    $ins->execute();
    $ins->close();
    return $p0;
}

function update_user_skill_bkt($con, $user_id, $skill_id, $observed_correct) {
    // Implements Bayesian Knowledge Tracing update for a single skill
    $p = fetch_user_skill($con, $user_id, $skill_id);

    // 1) Compute P(correct) given current knowledge
    $p_correct = $p * (1 - BKT_P_SLIP) + (1 - $p) * BKT_P_GUESS;

    // 2) Posterior P(L|obs) using Bayes
    if ($observed_correct) {
        $p_post = ($p * (1 - BKT_P_SLIP)) / max(1e-9, $p_correct);
    } else {
        $p_post = ($p * BKT_P_SLIP) / max(1e-9, (1 - $p_correct));
    }

    // 3) Apply transition (learning)
    $p_next = $p_post + (1 - $p_post) * BKT_P_TRANS;

    // clamp
    $p_next = max(0.0, min(1.0, $p_next));

    // save
    $upd = $con->prepare("UPDATE user_skills SET p_learned=? WHERE user_id=? AND skill_id=?");
    $upd->bind_param("dii", $p_next, $user_id, $skill_id);
    $upd->execute();
    if ($upd->affected_rows == 0) {
        // if row didn't exist for some reason insert
        $ins = $con->prepare("INSERT INTO user_skills (user_id, skill_id, p_learned) VALUES (?, ?, ?)");
        $ins->bind_param("iid", $user_id, $skill_id, $p_next);
        $ins->execute();
        $ins->close();
    }
    $upd->close();

    return $p_next;
}

/*********************
 *  State representation
 *  Create a compact "state_hash" string for indexing Q-table.
 *  Simple design: for N skills, use top-K skill ids and their discretized probs
 *********************/
function build_state_hash($con, $user_id, $skill_list = [], $buckets = 5) {
    // skill_list is an array of skill_ids we care about (all skills in the course/section)
    if (empty($skill_list)) {
        return 'global'; // fallback state
    }
    $parts = [];
    foreach ($skill_list as $skill_id) {
        $p = fetch_user_skill($con, $user_id, $skill_id);
        // discretize into buckets 0..(buckets-1)
        $bucket = (int)floor($p * $buckets);
        if ($bucket >= $buckets) $bucket = $buckets - 1;
        $parts[] = $skill_id . ':' . $bucket;
    }
    return implode('|', $parts); // example: "3:2|5:4|7:1"
}

/*********************
 *  Q-table helpers
 *********************/
function q_get($con, $state_hash, $action_id) {
    $stmt = $con->prepare("SELECT q_value FROM q_table WHERE state_hash=? AND action_id=?");
    $stmt->bind_param("si", $state_hash, $action_id);
    $stmt->execute();
    $q = null;
    $stmt->bind_result($q);
    if ($stmt->fetch()) {
        $stmt->close();
        return (double)$q;
    }
    $stmt->close();
    // initialize with 0
    $ins = $con->prepare("INSERT IGNORE INTO q_table (state_hash, action_id, q_value) VALUES (?, ?, 0.0)");
    $ins->bind_param("si", $state_hash, $action_id);
    $ins->execute();
    $ins->close();
    return 0.0;
}

function q_set($con, $state_hash, $action_id, $q_value) {
    $stmt = $con->prepare("INSERT INTO q_table (state_hash, action_id, q_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE q_value=?");
    $stmt->bind_param("sidd", $state_hash, $action_id, $q_value, $q_value);
    $stmt->execute();
    $stmt->close();
}

/*********************
 *  Action selection (epsilon-greedy)
 *  Candidate actions = array of question ids for selection
 *********************/
function select_action($con, $state_hash, $candidate_actions = []) {
    if (empty($candidate_actions)) return null;
    // epsilon-greedy
    if (mt_rand() / mt_getrandmax() < RL_EPSILON) {
        // explore
        return $candidate_actions[array_rand($candidate_actions)];
    }
    // exploit: pick action with highest Q
    $best = null; $best_q = -INF;
    foreach ($candidate_actions as $a) {
        $q = q_get($con, $state_hash, $a);
        if ($q > $best_q) { $best_q = $q; $best = $a; }
    }
    // if all q are 0, pick random to encourage exploration
    if ($best === null) return $candidate_actions[array_rand($candidate_actions)];
    return $best;
}

/*********************
 *  Q-learning update
 *********************/
function q_update($con, $state_hash, $action_id, $reward, $next_state_hash, $candidate_next_actions = []) {
    $q_current = q_get($con, $state_hash, $action_id);

    // estimate max future Q
    $max_next_q = 0.0;
    if (!empty($candidate_next_actions)) {
        $max_next_q = -INF;
        foreach ($candidate_next_actions as $a) {
            $q = q_get($con, $next_state_hash, $a);
            if ($q > $max_next_q) $max_next_q = $q;
        }
        if ($max_next_q === -INF) $max_next_q = 0.0;
    }

    // Q-learning rule
    $new_q = $q_current + RL_ALPHA * ($reward + RL_GAMMA * $max_next_q - $q_current);

    q_set($con, $state_hash, $action_id, $new_q);

    // log transition
    $stmt = $con->prepare("INSERT INTO rl_log (user_id, state_hash, action_id, reward, next_state_hash) VALUES (?, ?, ?, ?, ?)");
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $stmt->bind_param("isids", $user_id, $state_hash, $action_id, $reward, $next_state_hash);
    $stmt->execute();
    $stmt->close();

    return $new_q;
}

?>