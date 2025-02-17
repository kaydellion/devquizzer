<?php include "header.php"; 
$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    header("Location: games.php");
    exit();
}
$course_query = "SELECT title FROM ".$siteprefix."courses WHERE s = '$course_id'";
$course_result = mysqli_query($con, $course_query);
$course_row = mysqli_fetch_assoc($course_result);
?>

<div class="container-xxl flex-grow-1 container-p-y">

        <!-- Sections List -->
        <div class="card">
                <h5 class="card-header">(<?php echo htmlspecialchars($course_row['title']); ?>) Game Levels</h5>
                <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                                <thead>
                                        <tr>
                                                <th>Level</th>
                                                <th>Title</th>
                                                <th>Points</th>
                                                <th>Last Updated</th>
                                                <th>Actions</th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?php 
                                        $query = "SELECT * FROM ".$siteprefix."game_tasks WHERE course_id = '$course_id' ORDER BY level";
                                        $result = mysqli_query($con, $query);
                                        while ($row = mysqli_fetch_assoc($result)) {
                                                ?>
                                                <tr>
                                                        <td><?php echo $row['level']; ?></td>
                                                        <td><?php echo $row['title']; ?></td>
                                                        <td><?php echo $row['points']; ?></td>
                                                        <td><?php echo formatDateTime2($row['created_date']); ?></td>
                                                        <td>
                                                                <div class="dropdown">
                                                                <button type="button" class="btn btn-primary text-small dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                                <i class="bx bx-dots-vertical-rounded"></i>Manage</button>
                                                                        <div class="dropdown-menu">
                                                                                <a class="dropdown-item" href="edit-game.php?id=<?php echo $row['s']; ?>"><i class="bx bx-edit-alt me-1"></i> Edit</a>
                                                                                <a class="dropdown-item delete" href="delete.php?action=delete&table=game_tasks&item=<?php echo $row['s']; ?>&page=<?php echo $current_page; ?>">
                                                                                        <i class="bx bx-trash me-1"></i> Delete
                                                                                </a>
                                                                        </div>
                                                                </div>
                                                        </td>
                                                </tr>
                                        <?php } ?>
                                </tbody>
                        </table>
                </div>
        </div>
</div>


<?php include "footer.php"; ?>
