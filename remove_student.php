<?php
include 'session_start.php';
include 'db_connect.php';

if ($_SESSION['UserType'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Handle removal if confirmed
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Delete from student table
    $deleteStudentQuery = "DELETE FROM student WHERE UserID = ?";
    $stmtStudent = $conn->prepare($deleteStudentQuery);
    $stmtStudent->bind_param("s", $userID);

    // Delete from user table
    $deleteUserQuery = "DELETE FROM user WHERE UserID = ?";
    $stmtUser = $conn->prepare($deleteUserQuery);
    $stmtUser->bind_param("s", $userID);

    if ($stmtStudent->execute() && $stmtUser->execute()) {
        $successMessage = 'Student removed successfully!';
    } else {
        $errorMessage = 'Failed to remove student. Please try again.';
    }

    $stmtStudent->close();
    $stmtUser->close();
}

// Fetch students for the table
$studentsQuery = "SELECT UserID, UserFullName FROM user WHERE UserType = 'Student'";
$studentsResult = $conn->query($studentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Student</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script>
        function confirmRemove(userID) {
            if (confirm("Are you sure you want to remove this student?")) {
                window.location.href = "remove_student.php?action=remove&userID=" + userID;
            }
        }
    </script>
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Remove Student</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Matric Number</th>
                    <th>Full Name</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($studentsResult->num_rows > 0) {
                    while ($row = $studentsResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserFullName']) . "</td>";
                        echo "<td><button onclick=\"confirmRemove('" . htmlspecialchars($row['UserID']) . "')\" class='btn-remove'>Remove</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
