<?php
include 'session_start.php';

// Fetch the user's nickname from the session
$nickname = $_SESSION['UserNickName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Welcome, <?php echo htmlspecialchars($nickname); ?>!</h2>
        <p>Here you can manage your classes and perform other tasks relevant to your role.</p>
    </main>

    <?php include('footer.php'); ?>
</body>
</html>
