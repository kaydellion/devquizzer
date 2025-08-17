<?php include "header.php"; ?>

<?php
// Get analytics for all users
if ($type == 'admin') {
    $query = "SELECT u.name, q.course_id,  q.title as quiz_title, COUNT(s.s) as total_submissions,
              AVG(s.score) as avg_score, MAX(s.score) as highest_score, u.s as user_id,
              COUNT(DISTINCT s.quiz_id) as unique_quizzes
              FROM {$siteprefix}users u 
              LEFT JOIN {$siteprefix}submissions s ON u.s = s.user_id
              LEFT JOIN {$siteprefix}quiz q ON q.s = s.quiz_id
              WHERE u.type = 'user'
              GROUP BY q.s
              ORDER BY total_submissions DESC";
    $result = $con->query($query);
}
// Get analytics for a specific user
else {
    $query = "SELECT u.name, q.course_id,  q.title as quiz_title, COUNT(s.s) as total_submissions,
              AVG(s.score) as avg_score, MAX(s.score) as highest_score, u.s as user_id,
              COUNT(DISTINCT s.quiz_id) as unique_quizzes
              FROM {$siteprefix}users u 
              LEFT JOIN {$siteprefix}submissions s ON u.s = s.user_id
              LEFT JOIN {$siteprefix}quiz q ON q.s = s.quiz_id
              WHERE u.type = 'user' AND q.updated_by='$user_id'
              GROUP BY q.s
              ORDER BY total_submissions DESC";
    $result = $con->query($query);
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container mt-4">
                        <h3>Quiz Statistics</h3>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quiz Title</th>
                                    <th>Total Submissions</th>
                                    <th>Average Score</th>
                                    <th>Highest Score</th>
                                    <th>Unique Quizzes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo $row['quiz_title']; ?></td>
                                        <td><?php echo $row['total_submissions']; ?></td>
                                        <td><?php echo is_null($row['avg_score']) ? '0.00' : number_format($row['avg_score'], 2); ?>%</td>
                                        <td><?php echo $row['highest_score']; ?></td>
                                        <td><?php echo $row['unique_quizzes']; ?></td>
                                        <td class="text-end">
                                            <a href="analytics.php?course_id=<?php echo $row['course_id']; ?>&user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; ?>