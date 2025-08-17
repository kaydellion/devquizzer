<?php
// admin_analytics.php - Admin Dashboard for All Users
include "header.php";
require_once '../../adaptive_engine.php';


// Check admin privileges (modify based on your system)
$stmt = $con->prepare("SELECT type FROM {$siteprefix}users WHERE s = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_role = $stmt->get_result()->fetch_assoc()['type'] ?? '';
$stmt->close();

if ($user_role !== 'admin' && $user_role !== 'instructor') {
    header('Location: user_analytics.php');
    exit;
}

// Get filter parameters
$selected_course = $_GET['course_id'] ?? 9 ;
$selected_user = $_GET['user_id'] ?? '';
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Get all courses for filter
function get_all_courses($con, $siteprefix) {
    $query = "SELECT s,title FROM {$siteprefix}courses ORDER BY title";
    $result = $con->query($query);
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    return $courses;
}

// Get all users for filter
function get_all_users($con, $siteprefix) {
    $query = "SELECT s, name, email FROM {$siteprefix}users WHERE type != 'admin' ORDER BY name LIMIT 100";
    $result = $con->query($query);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    return $users;
}

// Get system-wide statistics
function get_system_stats($con, $siteprefix, $course_filter = '', $date_from = '', $date_to = '') {
    $where_conditions = [];
    $params = [];
    $types = '';
    
    if ($course_filter) {
        $where_conditions[] = "s.course_id = ?";
        $params[] = $course_filter;
        $types .= 'i';
    }
    
    if ($date_from && $date_to) {
        $where_conditions[] = "DATE(s.submission_date) BETWEEN ? AND ?";
        $params[] = $date_from;
        $params[] = $date_to;
        $types .= 'ss';
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Total users
    $total_users_query = "SELECT COUNT(DISTINCT s.user_id) as total_users FROM {$siteprefix}submissions s $where_clause";
    $stmt = $con->prepare($total_users_query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $total_users = $stmt->get_result()->fetch_assoc()['total_users'];
    $stmt->close();
    
    // Total quizzes completed
    $total_quizzes_query = "SELECT COUNT(*) as total_quizzes FROM {$siteprefix}submissions s $where_clause";
    $stmt = $con->prepare($total_quizzes_query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $total_quizzes = $stmt->get_result()->fetch_assoc()['total_quizzes'];
    $stmt->close();
    
    // Average system score
    $avg_score_query = "SELECT AVG(percentage) as avg_score FROM {$siteprefix}submissions s $where_clause AND s.percentage IS NOT NULL";
    $stmt = $con->prepare($avg_score_query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $avg_score = $stmt->get_result()->fetch_assoc()['avg_score'] ?? 0;
    $stmt->close();
    
    // Certificates issued
    $cert_where = $course_filter ? "WHERE course_id = $course_filter" : '';
    $certificates = $con->query("SELECT COUNT(*) as certificates FROM {$siteprefix}enrolled_courses $cert_where AND certificate = 1")->fetch_assoc()['certificates'];
    
    // Average skill mastery across all users
    $avg_mastery = $con->query("SELECT AVG(p_learned) as avg_mastery FROM user_skills")->fetch_assoc()['avg_mastery'] ?? 0;
    
    return [
        'total_users' => $total_users,
        'total_quizzes' => $total_quizzes,
        'avg_score' => round($avg_score, 1),
        'certificates' => $certificates,
        'avg_mastery' => round($avg_mastery * 100, 1)
    ];
}

// Get top performing users
function get_top_users($con, $siteprefix, $course_filter = '', $limit = 10) {
    $where_clause = $course_filter ? "WHERE s.course_id = $course_filter" : '';
    
    $query = "SELECT 
                u.s as user_id,
                u.name,
                u.email,
                AVG(s.percentage) as avg_score,
                COUNT(s.s) as quiz_count,
                SUM(s.points) as total_points
              FROM {$siteprefix}users u
              JOIN {$siteprefix}submissions s ON u.s = s.user_id
              $where_clause
              GROUP BY u.s, u.name, u.email
              HAVING quiz_count >= 3
              ORDER BY avg_score DESC
              LIMIT ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    return $users;
}

// Get struggling users
function get_struggling_users($con, $siteprefix, $course_filter = '', $limit = 10) {
    $where_clause = $course_filter ? "WHERE s.course_id = $course_filter" : '';
    
    $query = "SELECT 
                u.s as user_id,
                u.name,
                u.email,
                AVG(s.percentage) as avg_score,
                COUNT(s.s) as quiz_count,
                MAX(s.submission_date) as last_attempt
              FROM {$siteprefix}users u
              JOIN {$siteprefix}submissions s ON u.s = s.user_id
              $where_clause
              GROUP BY u.s, u.name, u.email
              HAVING avg_score < 60 AND quiz_count >= 2
              ORDER BY avg_score ASC
              LIMIT ?";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    return $users;
}

// Get skill analytics across all users
function get_skill_analytics($con, $siteprefix) {
    $query = "SELECT 
                us.skill_id,
                s.skill_name,
                AVG(us.p_learned) as avg_mastery,
                COUNT(us.user_id) as user_count,
                MIN(us.p_learned) as min_mastery,
                MAX(us.p_learned) as max_mastery
              FROM user_skills us
              LEFT JOIN {$siteprefix}skills s ON us.skill_id = s.s
              GROUP BY us.skill_id, s.skill_name
              ORDER BY avg_mastery DESC";
    
    $result = $con->query($query);
    $skills = [];
    while ($row = $result->fetch_assoc()) {
        $skills[] = [
            'skill_id' => $row['skill_id'],
            'skill_name' => $row['skill_name'] ?? "Skill " . $row['skill_id'],
            'avg_mastery' => round($row['avg_mastery'] * 100, 1),
            'user_count' => $row['user_count'],
            'min_mastery' => round($row['min_mastery'] * 100, 1),
            'max_mastery' => round($row['max_mastery'] * 100, 1)
        ];
    }
    return $skills;
}

// Get daily activity data
function get_daily_activity($con, $siteprefix, $days = 30) {
    $query = "SELECT 
                DATE(submission_date) as date,
                COUNT(*) as quiz_count,
                COUNT(DISTINCT user_id) as active_users,
                AVG(percentage) as avg_score
              FROM {$siteprefix}submissions 
              WHERE submission_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              GROUP BY DATE(submission_date)
              ORDER BY date ASC";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activity = [];
    while ($row = $result->fetch_assoc()) {
        $activity[] = [
            'date' => $row['date'],
            'quiz_count' => $row['quiz_count'],
            'active_users' => $row['active_users'],
            'avg_score' => round($row['avg_score'], 1)
        ];
    }
    $stmt->close();
    return $activity;
}

// Get Q-learning insights
function get_ql_insights($con) {
    $query = "SELECT 
                state_hash,
                action_id,
                AVG(reward) as avg_reward,
                COUNT(*) as frequency,
                COUNT(DISTINCT user_id) as user_count
              FROM rl_log 
              GROUP BY state_hash, action_id
              HAVING frequency >= 5
              ORDER BY avg_reward DESC
              LIMIT 20";
    
    $result = $con->query($query);
    $insights = [];
    while ($row = $result->fetch_assoc()) {
        $insights[] = [
            'state' => substr($row['state_hash'], 0, 30) . '...',
            'action' => $row['action_id'],
            'avg_reward' => round($row['avg_reward'], 3),
            'frequency' => $row['frequency'],
            'user_count' => $row['user_count']
        ];
    }
    return $insights;
}

// Get course performance
function get_course_performance($con, $siteprefix) {
    $query = "SELECT 
                c.s as course_id,
                c.title,
                COUNT(DISTINCT s.user_id) as enrolled_users,
                COUNT(s.s) as total_submissions,
                AVG(s.percentage) as avg_score,
                COUNT(CASE WHEN s.percentage >= 70 THEN 1 END) as passing_submissions,
                COUNT(DISTINCT ec.user_id) as certified_users
              FROM {$siteprefix}courses c
              LEFT JOIN {$siteprefix}submissions s ON c.s = s.course_id
              LEFT JOIN {$siteprefix}enrolled_courses ec ON c.s = ec.course_id AND ec.certificate = 1
              GROUP BY c.s, c.title
              HAVING total_submissions > 0
              ORDER BY avg_score DESC";
    
    $result = $con->query($query);
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $pass_rate = $row['total_submissions'] > 0 ? round(($row['passing_submissions'] / $row['total_submissions']) * 100, 1) : 0;
        $courses[] = [
            'course_id' => $row['course_id'],
            'course_name' => $row['title'],
            'enrolled_users' => $row['enrolled_users'],
            'total_submissions' => $row['total_submissions'],
            'avg_score' => round($row['avg_score'], 1),
            'pass_rate' => $pass_rate,
            'certified_users' => $row['certified_users']
        ];
    }
    return $courses;
}

// Fetch all data
$courses = get_all_courses($con, $siteprefix);
$users = get_all_users($con, $siteprefix);
$stats = get_system_stats($con, $siteprefix, $selected_course, $date_from, $date_to);
$top_users = get_top_users($con, $siteprefix, $selected_course);
$struggling_users = get_struggling_users($con, $siteprefix, $selected_course);
$skill_analytics = get_skill_analytics($con, $siteprefix);
$daily_activity = get_daily_activity($con, $siteprefix);
$ql_insights = get_ql_insights($con);
$course_performance = get_course_performance($con, $siteprefix);
?>


    <style>
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .filter-card {
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2 text-white"><i class="fas fa-chart-bar me-2"></i>Admin Analytics Dashboard</h1>
                    <p class="mb-0 opacity-75">Comprehensive overview of learning analytics and system performance</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group">
                        <a href="index.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i>Back to Admin
                        </a>
                        <button class="btn btn-outline-light" onclick="exportData()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filters -->
        <div class="card filter-card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Course Filter</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            <?php foreach ($courses as $course): ?>
                            <option value="<?= $course['s'] ?>" <?= $selected_course == $course['s'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['title']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="admin_analytics.php" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- System Statistics -->
        <div class="row mb-4">
            <div class="col-md mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-users text-primary fs-1 mb-2"></i>
                        <h3 class="card-title text-primary mb-1"><?= number_format($stats['total_users']) ?></h3>
                        <p class="card-text text-muted small">Active Users</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-clipboard-list text-success fs-1 mb-2"></i>
                        <h3 class="card-title text-success mb-1"><?= number_format($stats['total_quizzes']) ?></h3>
                        <p class="card-text text-muted small">Quizzes Completed</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-percentage text-warning fs-1 mb-2"></i>
                        <h3 class="card-title text-warning mb-1"><?= $stats['avg_score'] ?>%</h3>
                        <p class="card-text text-muted small">System Average</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-certificate text-info fs-1 mb-2"></i>
                        <h3 class="card-title text-info mb-1"><?= number_format($stats['certificates']) ?></h3>
                        <p class="card-text text-muted small">Certificates Issued</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-brain text-danger fs-1 mb-2"></i>
                        <h3 class="card-title text-danger mb-1"><?= $stats['avg_mastery'] ?>%</h3>
                        <p class="card-text text-muted small">Avg Skill Mastery</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Daily Activity Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="activityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-brain me-2"></i>Global Skill Mastery</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($skill_analytics)): ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-info-circle fs-3 mb-2"></i>
                                <p>No skill data available yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_slice($skill_analytics, 0, 8) as $skill): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold"><?= htmlspecialchars($skill['skill_name']) ?></small>
                                    <span class="badge 
                                        <?php if ($skill['avg_mastery'] >= 80): ?>bg-success
                                        <?php elseif ($skill['avg_mastery'] >= 60): ?>bg-warning
                                        <?php else: ?>bg-danger<?php endif; ?>">
                                        <?= $skill['avg_mastery'] ?>%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar 
                                        <?php if ($skill['avg_mastery'] >= 80): ?>bg-success
                                        <?php elseif ($skill['avg_mastery'] >= 60): ?>bg-warning
                                        <?php else: ?>bg-danger<?php endif; ?>" 
                                         style="width: <?= $skill['avg_mastery'] ?>%">
                                    </div>
                                </div>
                                <small class="text-muted"><?= $skill['user_count'] ?> users</small>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Performance Tables -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Performers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Avg Score</th>
                                        <th>Quizzes</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_users as $user): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($user['name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success"><?= round($user['avg_score'], 1) ?>%</span></td>
                                        <td><?= $user['quiz_count'] ?></td>
                                        <td><?= number_format($user['total_points']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Users Needing Support</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Avg Score</th>
                                        <th>Attempts</th>
                                        <th>Last Activity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($struggling_users as $user): ?>
                                    <tr>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($user['name']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger"><?= round($user['avg_score'], 1) ?>%</span></td>
                                        <td><?= $user['quiz_count'] ?></td>
                                        <td><?= date('M j', strtotime($user['last_attempt'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Performance -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Course Performance Analysis</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Course Name</th>
                                <th>Enrolled</th>
                                <th>Submissions</th>
                                <th>Avg Score</th>
                                <th>Pass Rate</th>
                                <th>Certified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($course_performance as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_name']) ?></td>
                                <td><?= $course['enrolled_users'] ?></td>
                                <td><?= $course['total_submissions'] ?></td>
                                <td>
                                    <span class="badge 
                                        <?php if ($course['avg_score'] >= 80): ?>bg-success
                                        <?php elseif ($course['avg_score'] >= 60): ?>bg-warning
                                        <?php else: ?>bg-danger<?php endif; ?>">
                                        <?= $course['avg_score'] ?>%
                                    </span>
                                </td>
                                <td><?= $course['pass_rate'] ?>%</td>
                                <td><?= $course['certified_users'] ?></td>
                                <td>
                                    <a href="?course_id=<?= $course['course_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Q-Learning Insights -->
        <?php if (!empty($ql_insights)): ?>
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-robot me-2"></i>AI Learning Algorithm Insights</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Learning State</th>
                                <th>Action</th>
                                <th>Avg Reward</th>
                                <th>Frequency</th>
                                <th>Users Affected</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ql_insights as $insight): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($insight['state']) ?></code></td>
                                <td><?= $insight['action'] ?></td>
                                <td><?= $insight['avg_reward'] ?></td>
                                <td><?= $insight['frequency'] ?></td>
                                <td><?= $insight['user_count'] ?></td>
                                <td>
                                    <?php if ($insight['avg_reward'] > 0.5): ?>
                                        <span class="badge bg-success">Excellent</span>
                                    <?php elseif ($insight['avg_reward'] > 0): ?>
                                        <span class="badge bg-warning">Good</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Needs Tuning</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Insight:</strong> Monitor negative reward patterns to identify areas where the AI recommendation system may need adjustment.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Activity Chart
        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($daily_activity, 'date')) ?>,
                datasets: [
                    {
                        label: 'Quiz Attempts',
                        data: <?= json_encode(array_column($daily_activity, 'quiz_count')) ?>,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Active Users',
                        data: <?= json_encode(array_column($daily_activity, 'active_users')) ?>,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Avg Score (%)',
                        data: <?= json_encode(array_column($daily_activity, 'avg_score')) ?>,
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left'
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        max: 100,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        function exportData() {
            // Simple CSV export functionality
            const data = [
                ['Metric', 'Value'],
                ['Total Users', '<?= $stats['total_users'] ?>'],
                ['Total Quizzes', '<?= $stats['total_quizzes'] ?>'],
                ['Average Score', '<?= $stats['avg_score'] ?>%'],
                ['Certificates Issued', '<?= $stats['certificates'] ?>'],
                ['Avg Skill Mastery', '<?= $stats['avg_mastery'] ?>%']
            ];
            
            const csv = data.map(row => row.join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'analytics_export_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
<?php include "footer.php"; ?>