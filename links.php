<?php
$current_page = basename($_SERVER['PHP_SELF']);

function generateLink($page, $icon, $text, $current_page) {
    $active_class = ($current_page == $page) ? 'text-primary' : 'text-light';
    if ($page == 'logout.php') {
        $active_class = 'text-danger';
    }
    echo "<a href=\"$page\" class=\"align-items-center m-2 $active_class\"><i class=\"$icon mr-2\"></i> $text</a>";
}
?>

<?php
generateLink('certificates.php', 'bi bi-award-fill', 'Certificates', $current_page);
generateLink('notifications.php', 'bi bi-bell', 'Notifications', $current_page);
generateLink('settings.php', 'bi bi-gear', 'Settings', $current_page);
generateLink('logout.php', 'bi bi-door-open', 'Logout', $current_page);
?>
