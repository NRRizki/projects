<?php
include 'session_start.php';
include 'db_connect.php';
include 'layout.php';

// Get the class ID from the URL
$classID = isset($_GET['classID']) ? $_GET['classID'] : '';

// Fetch the class details
$query = "SELECT c.CourseCode, c.GroupNumber, cl.CourseName 
          FROM classlist c 
          JOIN courselist cl ON c.CourseCode = cl.CourseCode 
          WHERE c.ClassID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $classID);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

// Fetch all lecturers
$lecturersQuery = "SELECT UserID, UserFullName FROM user WHERE UserType = 'Lecturer'";
$lecturersResult = $conn->query($lecturersQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newLecturerID = $_POST['newLecturer'];
    
    // Update the classlist table with the new lecturer ID
    $updateQuery = "UPDATE classlist SET UserID = ? WHERE ClassID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param('ss', $newLecturerID, $classID);

    if ($updateStmt->execute()) {
        echo "<script>alert('Class successfully transferred!'); window.location.href='classes.php';</script>";
    } else {
        echo "<script>alert('Error transferring class.');</script>";
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer Class</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .btn-transfer {
            background-color: #FFA500;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-transfer:hover {
            background-color: #FF8C00;
        }
    </style>
    <script>
        function confirmTransfer() {
            var lecturerDropdown = document.getElementById("newLecturer");
            var selectedLecturerName = lecturerDropdown.options[lecturerDropdown.selectedIndex].text;
            return confirm("Are you sure transferring <?php echo $classID; ?> to " + selectedLecturerName + "?");
        }
    </script>
</head>
<body>
    <main>
        <h2>Transfer Class</h2>
        <p>Transfer <?php echo htmlspecialchars($class['CourseCode'] . "G" . $class['GroupNumber'] . " - " . $class['CourseName']); ?></p>
        <form method="post" onsubmit="return confirmTransfer();">
            <label for="newLecturer">Select Lecturer:</label>
            <select name="newLecturer" id="newLecturer" required>
                <?php while ($lecturer = $lecturersResult->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($lecturer['UserID']); ?>">
                        <?php echo htmlspecialchars($lecturer['UserFullName']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" class="btn-transfer">Transfer Class</button>
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
