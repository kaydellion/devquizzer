<?php
// user_analytics.php - Individual User Dashboard
require_once 'backend/connect.php';
require_once 'adaptive_engine.php';

// Get user ID from session/cookie
$user_id = $_COOKIE['userID'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Get user info
$stmt = $con->prepare("SELECT name, email FROM {$siteprefix}users WHERE s = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch skill mastery data
function get_user_skills($con, $user_id, $siteprefix) {
    $query = "SELECT us.skill_id, us.p_learned, s.skill_name 
              FROM user_skills us 
              LEFT JOIN {$siteprefix}skills s ON us.skill_id = s.s 
              WHERE us.user_id = ? 
              ORDER BY us.p_learned DESC";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $skills = [];
    while ($row = $result->fetch_assoc()) {
        $skills[] = [
            'skill_id' => $row['skill_id'],
            'skill_name' => $row['skill_name'] ?? "Skill " . $row['skill_id'],
            'mastery_level' => round($row['p_learned'] * 100, 1),
            'mastery_probability' => $row['p_learned']
        ];
    }
    $stmt->close();
    return $skills;
}

// Get learning progress over time
function get_user_progress($con, $user_id, $siteprefix) {
    $query = "SELECT 
                DATE(submission_date) as date,
                AVG(percentage) as avg_score,
                COUNT(*) as quiz_count
              FROM {$siteprefix}submissions 
              WHERE user_id = ? AND submission_date IS NOT NULL
              GROUP BY DATE(submission_date) 
              ORDER BY date ASC 
              LIMIT 30";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $progress = [];
    while ($row = $result->fetch_assoc()) {
        $progress[] = [
            'date' => $row['date'],
            'avg_score' => round($row['avg_score'], 1),
            'quiz_count' => $row['quiz_count']
        ];
    }
    $stmt->close();
    return $progress;
}

// Get recent quiz performance
function get_user_recent_quizzes($con, $user_id, $siteprefix) {
    $query = "SELECT 
                s.s as submission_id,
                q.title as quiz_title,
                c.course_name,
                s.score,
                s.percentage,
                s.points,
                s.submission_date
              FROM {$siteprefix}submissions s
              JOIN {$siteprefix}quiz q ON s.quiz_id = q.s
              JOIN {$siteprefix}courses c ON s.course_id = c.s
              WHERE s.user_id = ?
              ORDER BY s.submission_date DESC
              LIMIT 10";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
    $stmt->close();
    return $quizzes;
}

// Get user statistics
function get_user_stats($con, $user_id, $siteprefix) {
    // Total quizzes taken
    $stmt = $con->prepare("SELECT COUNT(*) as total_quizzes FROM {$siteprefix}submissions WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_quizzes = $stmt->get_result()->fetch_assoc()['total_quizzes'];
    $stmt->close();
    
    // Average score
    $stmt = $con->prepare("SELECT AVG(percentage) as avg_score FROM {$siteprefix}submissions WHERE user_id = ? AND percentage IS NOT NULL");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $avg_score = $stmt->get_result()->fetch_assoc()['avg_score'] ?? 0;
    $stmt->close();
    
    // Total points earned
    $stmt = $con->prepare("SELECT SUM(points) as total_points FROM {$siteprefix}submissions WHERE user_id = ? AND points IS NOT NULL");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $total_points = $stmt->get_result()->fetch_assoc()['total_points'] ?? 0;
    $stmt->close();
    
    // Certificates earned
    $stmt = $con->prepare("SELECT COUNT(*) as certificates FROM {$siteprefix}enrolled_courses WHERE user_id = ? AND certificate = 1");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $certificates = $stmt->get_result()->fetch_assoc()['certificates'];
    $stmt->close();
    
    // Average skill mastery
    $stmt = $con->prepare("SELECT AVG(p_learned) as avg_mastery FROM user_skills WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $avg_mastery = $result['avg_mastery'] ? round($result['avg_mastery'] * 100, 1) : 0;
    $stmt->close();
    
    return [
        'total_quizzes' => $total_quizzes,
        'avg_score' => round($avg_score, 1),
        'total_points' => $total_points,
        'certificates' => $certificates,
        'avg_mastery' => $avg_mastery
    ];
}

// Fetch all data
$skills = get_user_skills($con, $user_id, $siteprefix);
$progress = get_user_progress($con, $user_id, $siteprefix);
$recent_quizzes = get_user_recent_quizzes($con, $user_id, $siteprefix);
$stats = get_user_stats($con, $user_id, $siteprefix);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Learning Analytics</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 15px;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .skill-progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .mastery-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        
        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
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
                    <h1 class="mb-2"><i class="fas fa-chart-line me-2"></i>My Learning Analytics</h1>
                    <p class="mb-0 opacity-75">Welcome back, <?= htmlspecialchars($user_info['name'] ?? 'Student') ?>! Track your learning progress and skill development.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group">
                        <a href="dashboard.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-clipboard-list text-primary fs-1 mb-2"></i>
                        <h3 class="card-title text-primary mb-1"><?= $stats['total_quizzes'] ?></h3>
                        <p class="card-text text-muted small">Total Quizzes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-percentage text-success fs-1 mb-2"></i>
                        <h3 class="card-title text-success mb-1"><?= $stats['avg_score'] ?>%</h3>
                        <p class="card-text text-muted small">Average Score</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-coins text-warning fs-1 mb-2"></i>
                        <h3 class="card-title text-warning mb-1"><?= number_format($stats['total_points']) ?></h3>
                        <p class="card-text text-muted small">Total Points</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-certificate text-info fs-1 mb-2"></i>
                        <h3 class="card-title text-info mb-1"><?= $stats['certificates'] ?></h3>
                        <p class="card-text text-muted small">Certificates</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2 mb-3">
                <div class="card stats-card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-brain text-danger fs-1 mb-2"></i>
                        <h3 class="card-title text-danger mb-1"><?= $stats['avg_mastery'] ?>%</h3>
                        <p class="card-text text-muted small">Skill Mastery</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Learning Progress Over Time</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="progressChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-brain me-2"></i>Skill Mastery (BKT)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($skills)): ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-info-circle fs-3 mb-2"></i>
                                <p>No skill data available yet. Complete some quizzes to see your progress!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($skills as $skill): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold"><?= htmlspecialchars($skill['skill_name']) ?></small>
                                    <span class="badge mastery-badge
                                        <?php if ($skill['mastery_level'] >= 80): ?>bg-success
                                        <?php elseif ($skill['mastery_level'] >= 60): ?>bg-warning
                                        <?php else: ?>bg-danger<?php endif; ?>">
                                        <?= $skill['mastery_level'] ?>%
                                    </span>
                                </div>
                                <div class="progress skill-progress">
                                    <div class="progress-bar 
                                        <?php if ($skill['mastery_level'] >= 80): ?>bg-success
                                        <?php elseif ($skill['mastery_level'] >= 60): ?>bg-warning
                                        <?php else: ?>bg-danger<?php endif; ?>" 
                                         style="width: <?= $skill['mastery_level'] ?>%">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Quizzes Table -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Quiz Performance</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_quizzes)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-clipboard-list fs-3 mb-2"></i>
                        <p>No quiz attempts found. Start taking quizzes to track your progress!</p>
                        <a href="courses.php" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Quiz Title</th>
                                    <th>Course</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Points</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_quizzes as $quiz): ?>
                                <tr>
                                    <td><?= htmlspecialchars($quiz['quiz_title']) ?></td>
                                    <td><?= htmlspecialchars($quiz['course_name']) ?></td>
                                    <td><?= $quiz['score'] ?></td>
                                    <td><?= round($quiz['percentage'], 1) ?>%</td>
                                    <td>
                                        <?php if ($quiz['points']): ?>
                                            <span class="badge bg-success"><?= $quiz['points'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">0</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($quiz['submission_date'])) ?></td>
                                    <td>
                                        <?php if ($quiz['percentage'] >= 70): ?>
                                            <span class="badge bg-success">Passed</span>
                                        <?php elseif ($quiz['percentage'] >= 50): ?>
                                            <span class="badge bg-warning">Needs Review</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- AI Insights Card -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>AI Learning Insights</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Strengths</h6>
                        <ul class="list-unstyled">
                            <?php 
                            $strong_skills = array_filter($skills, function($skill) { return $skill['mastery_level'] >= 80; });
                            if (empty($strong_skills)): ?>
                                <li><i class="fas fa-info-circle text-muted me-2"></i>Complete more quizzes to identify your strengths</li>
                            <?php else: ?>
                                <?php foreach (array_slice($strong_skills, 0, 3) as $skill): ?>
                                <li><i class="fas fa-check-circle text-success me-2"></i><?= htmlspecialchars($skill['skill_name']) ?> (<?= $skill['mastery_level'] ?>%)</li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger">Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <?php 
                            $weak_skills = array_filter($skills, function($skill) { return $skill['mastery_level'] < 60; });
                            if (empty($weak_skills)): ?>
                                <li><i class="fas fa-trophy text-success me-2"></i>Great job! No major weaknesses identified</li>
                            <?php else: ?>
                                <?php foreach (array_slice($weak_skills, 0, 3) as $skill): ?>
                                <li><i class="fas fa-exclamation-triangle text-warning me-2"></i><?= htmlspecialchars($skill['skill_name']) ?> (<?= $skill['mastery_level'] ?>%)</li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Progress Chart
        const ctx = document.getElementById('progressChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($progress, 'date')) ?>,
                datasets: [{
                    label: 'Average Score (%)',
                    data: <?= json_encode(array_column($progress, 'avg_score')) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    </script>
</body>
</html>