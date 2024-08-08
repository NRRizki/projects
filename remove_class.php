<?php
include 'session_start.php';
include 'db_connect.php';

// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Check for success or error messages in the URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMessage = 'Class removed successfully!';
}

if (isset($_GET['error']) && $_GET['error'] == 1) {
    $errorMessage = 'Failed to remove class. Please try again.';
}

// Fetch classes for the table
$classesQuery = "SELECT ClassID, CourseCode, GroupNumber FROM classlist WHERE UserID = '" . $_SESSION['UserID'] . "'";
$classesResult = $conn->query($classesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Class</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Remove Class</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Course Code</th>
                    <th>Group Number</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($classesResult && $classesResult->num_rows > 0) {
                    while ($row = $classesResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ClassID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CourseCode']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['GroupNumber']) . "</td>";
                        echo "<td><a href='confirm_remove_class.php?ClassID=" . urlencode($row['ClassID']) . "' class='btn-remove'>Remove</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No classes found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
