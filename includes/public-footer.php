<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<footer class="footer">
    <div class="wrap">
        Barangay Surong Guisad · Community Disaster Risk Reduction and
        Emergency Response Management <a href="<?= isset($_SESSION['user_id']) ? 'logout.php' : 'login.php' ?>" style="display: inline; color: inherit; text-decoration: none; font: inherit; padding: 0; margin: 0;">System</a>
    </div>
</footer>
