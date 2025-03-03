<?php include 'header.php'; ?>
<main class="main">

<section>
<div class="row bg-dark p-5">
  <div class="col-lg-2 col-12">
    <img src="<?php echo $profile_picture; ?>" alt="Avatar" class="img-fluid rounded-circle">
  </div>
  <div class="col-lg-10 col-12 d-flex align-items-center pt-3 mb-5">
    <div class="d-flex flex-column w-100">
        <div class="d-flex">
        <?php include "links.php"; ?>
        </div>
        <h2 class="title text-primary text-bold mt-3 mb-5">Hi, <?php echo htmlspecialchars($name); ?></h2>
    <?php
    // Fetch last 4 notifications
    $sql = "SELECT message, date FROM ".$siteprefix."notifications WHERE user = ? ORDER BY date";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    ?>
    <?php foreach ($notifications as $notification): ?>
    <li class="notification-item bg-light p-3 mb-2 rounded w-100">
        <p class="text-dark mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
        <small class="text-muted"><?php echo htmlspecialchars($notification['date']); ?></small>
    </li>
    <?php endforeach; ?>
    </div>
  </div> 
</div>






</section>
</main>
<?php include 'footer.php'; ?>