<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['UserID'])) {
    echo "User is not logged in. Redirecting to login page.";
    header("Location: index.php");
    exit();
}
?>
