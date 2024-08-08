<?php
session_start();
include 'db_connect.php';

// Get the class ID from the query string
$classID = isset($_GET['classID']) ? $_GET['classID'] : '';

// Fetch class details from the database
$classQuery = "SELECT ClassID, CourseCode, GroupNumber FROM classlist WHERE ClassID = '$classID'";
$classResult = $conn->query($classQuery);
$class = $classResult->fetch_assoc();

if (!$class) {
    header("Location: remove_class.php?error=1");
    exit();
}

// Handle confirmation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirm'])) {
        $deleteQuery = "DELETE FROM classlist WHERE ClassID = '$classID'";
        if ($conn->query($deleteQuery) === TRUE) {
            header("Location: remove_class.php?success=1");
            exit();
        } else {
            header("Location: remove_class.php?error=1");
            exit();
        }
    } else {
        header("Location: remove_class.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Remove Class</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Confirm Remove Class</h2>

        <p>Are you sure you want to remove the following class?</p>
        <p><strong>Class ID:</strong> <?php echo htmlspecialchars($class['ClassID']); ?></p>
        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($class['CourseCode']); ?></p>
        <p><strong>Group Number:</strong> <?php echo htmlspecialchars($class['GroupNumber']); ?></p>

        <form method="post" action="confirm_remove_class.php?classID=<?php echo urlencode($classID); ?>">
            <button type="submit" name="confirm" class="btn red">Confirm</button>
            <button type="submit" name="cancel" class="btn">Cancel</button>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
