<?php
require_once 'header.php';
require_once 'functions.php';

echo "<div class='content'><h2>Log out</h2>";
if (isset($_SESSION['user'])) {
    destroySession();
    echo "<p>You have been logged out. Please <a href='index.php'>click here</a> to refresh the screen.</div>";
}
else echo "<p>You cannot log out because you are not logged in</p>";
echo "</div>";

$page = basename($_SERVER['PHP_SELF']);
require_once 'footer.php';
?>