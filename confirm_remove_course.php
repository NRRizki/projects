<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_GET['CourseCode'])) {
    header('Location: remove_course.php');
    exit;
}

$courseCode = $_GET['CourseCode'];

// Database connection
include 'db_connect.php';

// Fetch course details
$stmt = $conn->prepare("SELECT CourseName FROM CourseList WHERE CourseCode = ?");
$stmt->bind_param("s", $courseCode);
$stmt->execute();
$stmt->bind_result($courseName);
$stmt->fetch();
$stmt->close();

if (!$courseName) {
    header('Location: remove_course.php?error=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        // Delete course
        $stmt = $conn->prepare("DELETE FROM CourseList WHERE CourseCode = ?");
        $stmt->bind_param("s", $courseCode);

        if ($stmt->execute()) {
            header('Location: remove_course.php?success=1');
        } else {
            header('Location: remove_course.php?error=1');
        }

        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Redirect to remove_course.php if cancelled
        header('Location: remove_course.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Remove Course</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <h2>Confirm Remove Course</h2>
    <p>Are you sure you want to remove the course with the following details?</p>
    <p><strong>Course Code:</strong> <?php echo htmlspecialchars($courseCode); ?></p>
    <p><strong>Course Name:</strong> <?php echo htmlspecialchars($courseName); ?></p>

    <form method="post">
        <button type="submit" name="confirm" class="btn">Yes, Remove</button>
        <a href="remove_course.php" class="btn red">Cancel</a>
    </form>

    <?php include 'footer.php'; ?>
</body>
</html>
