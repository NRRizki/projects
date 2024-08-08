<?php
include 'session_start.php';
include 'db_connect.php';

if ($_SESSION['UserType'] != 'Admin') {
    header('Location: login.php');
    exit();
}

// Fetch students
$studentsQuery = "SELECT user.UserID, user.UserFullName, user.UserNickname, student.HPnum, student.ProgramCode FROM user JOIN student ON user.UserID = student.UserID";
$studentsResult = $conn->query($studentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students List</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>
    <main>
        <h2>Students List</h2>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Nickname</th>
                    <th>HP Number</th>
                    <th>Program</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($studentsResult->num_rows > 0) {
                    while ($row = $studentsResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserFullName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserNickname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['HPnum']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProgramCode']) . "</td>";
                        echo "<td><a href='update_details.php?userID=" . urlencode($row['UserID']) . "' class='btn-edit'>Edit</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
