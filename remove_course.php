<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Check for success or error messages in the URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = 'Course removed successfully!';
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
    $errorMessage = 'Failed to remove course. Please try again.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Course</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <h2>Remove Course</h2>

    <?php if ($successMessage): ?>
        <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            include 'db_connect.php';

            // Fetch all courses
            $result = $conn->query("SELECT CourseCode, CourseName FROM CourseList");

            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($row['CourseCode']); ?></td>
                <td><?php echo htmlspecialchars($row['CourseName']); ?></td>
                <td>
                    <a href="confirm_remove_course.php?CourseCode=<?php echo urlencode($row['CourseCode']); ?>" class="btn-remove">Remove</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php include 'footer.php'; ?>
</body>
</html>
