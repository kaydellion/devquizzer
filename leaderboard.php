<?php include 'header.php'; ?>
<main class="main">

<section>
<div class="row bg-dark p-5">
<div class="col-lg-4 col-12 h5 text-light">
<a href="index.php" class="text-light">Home >>  </a> <span class="text-primary">Leaderboard</span></div>
<div class="col-lg-8 col-12">
<div class="table-responsive">
    <table class="table table-striped table-bordered table-dark text-light mt-3">
        <thead class="bg-primary">
            <tr>
                <th class="text-center">#</th>
                <th>User</th>
                <th class="text-center">Reward Points</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql = "SELECT * FROM ".$siteprefix."users WHERE type != 'admin' ORDER BY reward_points DESC";
            $sql2 = mysqli_query($con, $sql);
            $i=1;
            while ($row = mysqli_fetch_array($sql2)) {
                $name = $row['name'];
                $reward_points = $row['reward_points'];
                ?>
                <tr class="align-middle">
                    <td class="text-center">
                        <?php if($i <= 3): ?>
                            <span class="badge bg-<?php echo ($i == 1) ? 'warning' : (($i == 2) ? 'secondary' : 'danger'); ?> rounded-pill">
                                <?php echo $i; ?>
                            </span>
                        <?php else: ?>
                            <?php echo $i; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $name; ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success"><?php echo number_format($reward_points); ?></span>
                    </td>
                </tr>
            <?php $i++; } ?>
        </tbody>
    </table>
</div>
</div> 
</div>




</section>
</main>
<?php include 'footer.php'; ?>