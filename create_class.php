<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course = $_POST['course'];
    $groupNumber = $_POST['groupNumber'];

    // Extract CourseCode and CourseName from the selected course
    $courseParts = explode(' - ', $course);
    $courseCode = $courseParts[0];

    // Generate ClassID
    $classID = $courseCode . 'G' . $groupNumber;

    // Check if the ClassID already exists
    $checkQuery = "SELECT * FROM classlist WHERE ClassID = '$classID'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        $error = "There is already $classID in the database";
    } else {
        $userID = $_SESSION['UserID'];
        $insertQuery = "INSERT INTO classlist (ClassID, CourseCode, GroupNumber, UserID) VALUES ('$classID', '$courseCode', '$groupNumber', '$userID')";
        if ($conn->query($insertQuery) === TRUE) {
            $success = "Class creation successful";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// Fetch courses for the dropdown
$coursesQuery = "SELECT CourseCode, CourseName FROM courselist";
$coursesResult = $conn->query($coursesQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Class</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <div class="container">
            <h1>Create Class</h1>
            <?php if (isset($error)) { echo "<div class='error-message'>$error</div>"; } ?>
            <?php if (isset($success)) { echo "<div class='success-message'>$success</div>"; } ?>
            <form method="post" action="create_class.php">
                <div class="textbox">
                    <label for="course">Course:</label>
                    <select id="course" name="course" required>
                        <?php
                        if ($coursesResult->num_rows > 0) {
                            while($row = $coursesResult->fetch_assoc()) {
                                echo "<option value='" . $row['CourseCode'] . " - " . $row['CourseName'] . "'>" . $row['CourseCode'] . " - " . $row['CourseName'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="textbox">
                    <label for="groupNumber">Group Number:</label>
                    <input type="text" id="groupNumber" name="groupNumber" required>
                </div>
                <button type="submit" class="btn">Create Class</button>
            </form>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
