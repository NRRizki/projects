<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

// Get the UserID to be removed
if (isset($_POST['UserID'])) {
    $userID = $_POST['UserID'];

    // Database connection
    include 'db_connect.php';

    // Prepare and execute the query
    $stmt = $conn->prepare("DELETE FROM USER WHERE UserID = ? AND UserType = 'Lecturer'");
    $stmt->bind_param("s", $userID);

    if ($stmt->execute()) {
        header('Location: remove_lecturer.php?success=1');
    } else {
        header('Location: remove_lecturer.php?error=1');
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    header('Location: remove_lecturer.php');
}
?>
