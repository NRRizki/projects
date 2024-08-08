<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturers List</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Lecturers List</h2>

        <?php
        // Database connection
        include 'db_connect.php';

        // Fetch all lecturers
        $query = "SELECT UserID, UserFullName FROM USER WHERE UserType = 'Lecturer'";
        $result = $conn->query($query);

        // Check if query execution was successful
        if (!$result) {
            echo "<p class='error-message'>Failed to retrieve lecturers. Please try again later.</p>";
        } else {
            if ($result->num_rows > 0) {
                echo "<table>
                        <thead>
                            <tr>
                                <th>Staff ID</th>
                                <th>Full Name</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['UserID']) . "</td>
                            <td>" . htmlspecialchars($row['UserFullName']) . "</td>
                            <td><a href='edit_lecturer.php?UserID=" . urlencode($row['UserID']) . "' class='btn'>Edit</a></td>
                          </tr>";
                }

                echo "</tbody>
                      </table>";
            } else {
                echo "<p>No lecturers found.</p>";
            }
        }

        $conn->close();
        ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
