<?php include "header.php"; ?>

<?php
// Get analytics for all users
$query = "SELECT u.name, COUNT(s.s) as total_submissions,
          AVG(s.score) as avg_score, MAX(s.score) as highest_score,
          COUNT(DISTINCT s.quiz_id) as unique_quizzes
          FROM {$siteprefix}users u 
          LEFT JOIN {$siteprefix}submissions s ON u.s = s.user_id
          WHERE u.type = 'user'
          GROUP BY u.s
          ORDER BY total_submissions DESC";
$result = $con->query($query);
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
                <th>Total Submissions</th>
                <th>Average Score</th>
                <th>Highest Score</th>
                <th>Unique Quizzes</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo $row['total_submissions']; ?></td>
                <td><?php echo number_format($row['avg_score'], 2); ?>%</td>
                <td><?php echo $row['highest_score']; ?></td>
                <td><?php echo $row['unique_quizzes']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


</div></div></div></div></div>
<?php include "footer.php"; ?>
