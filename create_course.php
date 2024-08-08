<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

$successMessage = '';
$errorMessage = '';
$courseCode = $courseName = '';

// Form handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $courseCode = $_POST['CourseCode'];
    $courseName = $_POST['CourseName'];

    // Validate form data
    if (empty($courseCode)) {
        $errorMessage = 'Course Code is required.';
    } elseif (empty($courseName)) {
        $errorMessage = 'Course Name is required.';
    } else {
        // Database connection
        include 'db_connect.php';

        // Check if CourseCode already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM CourseList WHERE CourseCode = ?");
        $stmt->bind_param("s", $courseCode);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errorMessage = 'Course Code already exists in the database.';
        } else {
            // Prepare and execute the query
            $stmt = $conn->prepare("INSERT INTO CourseList (CourseCode, CourseName) VALUES (?, ?)");
            $stmt->bind_param("ss", $courseCode, $courseName);

            if ($stmt->execute()) {
                $successMessage = 'Course added successfully!';
                // Clear input fields after successful insertion
                $courseCode = $courseName = '';
                $errorMessage = ''; // Clear error message
            } else {
                $errorMessage = 'Error: ' . $conn->error;
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Course</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Create Course</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage && !$successMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="textbox">
                <label for="CourseCode">Course Code:</label>
                <input type="text" id="CourseCode" name="CourseCode" value="<?php echo htmlspecialchars($courseCode); ?>">
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($courseCode) && !$successMessage): ?>
                    <p class="error-message">Course Code is required.</p>
                <?php endif; ?>
            </div>
            <div class="textbox">
                <label for="CourseName">Course Name:</label>
                <input type="text" id="CourseName" name="CourseName" value="<?php echo htmlspecialchars($courseName); ?>">
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($courseName) && !$successMessage): ?>
                    <p class="error-message">Course Name is required.</p>
                <?php endif; ?>
            </div>
            <input type="submit" class="btn" value="Add Course">
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
