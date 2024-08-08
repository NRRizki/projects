<?php
include 'session_start.php';
include 'db_connect.php';

$classID = isset($_GET['classID']) ? $_GET['classID'] : null;
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'UserID';
$orderDir = isset($_GET['orderDir']) ? $_GET['orderDir'] : 'ASC';
$randomize = isset($_GET['randomize']) ? $_GET['randomize'] : false;

// Handle removal of student from class
if (isset($_GET['remove']) && isset($_GET['userID'])) {
    $userID = $_GET['userID'];
    $deleteQuery = $conn->prepare("DELETE FROM enrollmentlist WHERE ClassID = ? AND UserID = ?");
    if (!$deleteQuery) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $deleteQuery->bind_param('ss', $classID, $userID);
    if ($deleteQuery->execute()) {
        echo "<script>alert('Student removed successfully');</script>";
    } else {
        echo "<script>alert('Failed to remove student');</script>";
    }
    $deleteQuery->close();
}

// Fetch class details
$classQuery = $conn->prepare("SELECT c.CourseCode, c.GroupNumber, co.CourseName FROM classlist c JOIN courselist co ON c.CourseCode = co.CourseCode WHERE c.ClassID = ?");
if (!$classQuery) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$classQuery->bind_param('s', $classID);
$classQuery->execute();
$classResult = $classQuery->get_result();
$class = $classResult->fetch_assoc();
$classQuery->close();

// Fetch enrolled students
if ($randomize) {
    $studentsQuery = "SELECT u.UserID, u.UserFullName, s.HPnum FROM enrollmentlist e JOIN user u ON e.UserID = u.UserID JOIN student s ON e.UserID = s.UserID WHERE e.ClassID = ? ORDER BY RAND()";
} else {
    $studentsQuery = "SELECT u.UserID, u.UserFullName, s.HPnum FROM enrollmentlist e JOIN user u ON e.UserID = u.UserID JOIN student s ON e.UserID = s.UserID WHERE e.ClassID = ? ORDER BY $orderBy $orderDir";
}
$stmt = $conn->prepare($studentsQuery);
if (!$stmt) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('s', $classID);
$stmt->execute();
$studentsResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .button {
            display: inline-block;
            padding: 10px 15px;
            margin-right: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .btn-remove {
            background-color: #dc3545;
        }

        .btn-remove:hover {
            background-color: #c82333;
        }
    </style>
    <script>
        function confirmRemove(userID) {
            if (confirm("Are you sure you want to remove this student from the class?")) {
                window.location.href = "?classID=<?php echo urlencode($classID); ?>&remove=true&userID=" + encodeURIComponent(userID);
            }
        }
    </script>
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Class Details for <?php echo htmlspecialchars($class['CourseCode']) . "G" . htmlspecialchars($class['GroupNumber']) . " - " . htmlspecialchars($class['CourseName']); ?></h2>
        
        <div>
            <a href="?classID=<?php echo urlencode($classID); ?>&orderBy=UserID&orderDir=<?php echo $orderDir === 'ASC' ? 'DESC' : 'ASC'; ?>" class="button">By Matric Number</a>
            <a href="?classID=<?php echo urlencode($classID); ?>&orderBy=UserFullName&orderDir=<?php echo $orderDir === 'ASC' ? 'DESC' : 'ASC'; ?>" class="button">By Name</a>
            <a href="?classID=<?php echo urlencode($classID); ?>&randomize=true" class="button">Randomize</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Matric Number</th>
                    <th>Full Name</th>
                    <th>Phone Number</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($studentsResult->num_rows > 0) {
                    $count = 1;
                    while ($row = $studentsResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UserFullName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['HPnum']) . "</td>";
                        echo "<td><button class='button btn-remove' onclick=\"confirmRemove('" . htmlspecialchars($row['UserID']) . "')\">Remove</button></td>";
                        echo "</tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='5'>No students enrolled yet</td></tr>";
                }
                ?>
            </tbody>
        </table>
        
        <a href="enroll_students.php?classID=<?php echo urlencode($classID); ?>" class="button">Enroll Students</a>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
