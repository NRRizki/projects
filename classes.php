<?php
include 'session_start.php';
include 'db_connect.php';
include 'layout.php';

$lecturerID = $_SESSION['UserID'];

$query = "SELECT c.ClassID, c.CourseCode, c.GroupNumber, cl.CourseName 
          FROM classlist c 
          JOIN courselist cl ON c.CourseCode = cl.CourseCode 
          WHERE c.UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $lecturerID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Classes</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .btn-update, .btn-transfer {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-update {
            background-color: #4CAF50;
            color: white;
        }
        .btn-update:hover {
            background-color: #45a049;
        }
        .btn-transfer {
            background-color: #FFA500;
            color: white;
        }
        .btn-transfer:hover {
            background-color: #FF8C00;
        }
    </style>
</head>
<body>
    <main>
        <h2>My Classes</h2>
        <table>
            <thead>
                <tr>
                    <th>Group</th>
                    <th>Num of Participants</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) {
                    $classID = $row['ClassID'];

                    // Count participants
                    $participantsQuery = "SELECT COUNT(*) as participantCount FROM enrollmentlist WHERE ClassID = ?";
                    $participantsStmt = $conn->prepare($participantsQuery);
                    $participantsStmt->bind_param('s', $classID);
                    $participantsStmt->execute();
                    $participantsResult = $participantsStmt->get_result();
                    $participantsRow = $participantsResult->fetch_assoc();
                    $participants = $participantsRow['participantCount'];

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['CourseCode']) . "G" . htmlspecialchars($row['GroupNumber']) . " - " . htmlspecialchars($row['CourseName']) . "</td>";
                    echo "<td>" . htmlspecialchars($participants) . "</td>";
                    echo "<td><a href='class_details.php?classID=" . urlencode($classID) . "' class='btn-update'>Class Details</a>
                          <a href='transfer_class.php?classID=" . urlencode($classID) . "' class='btn-transfer'>Transfer Class</a></td>";
                    echo "</tr>";
                } ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
