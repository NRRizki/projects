<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_GET['UserID'])) {
    header('Location: lecturers_list.php');
    exit;
}

$userID = $_GET['UserID'];

// Database connection
include 'db_connect.php';

// Fetch lecturer details
$stmt = $conn->prepare("SELECT UserFullName, UserNickname FROM USER WHERE UserID = ? AND UserType = 'Lecturer'");
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($userFullName, $userNickname);
$stmt->fetch();
$stmt->close();

if (!$userFullName) {
    header('Location: lecturers_list.php?error=1');
    exit;
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['UserPword'];
    $newFullName = $_POST['UserFullName'];
    $newNickname = $_POST['UserNickname'];

    if (empty($newPassword) || empty($newFullName) || empty($newNickname)) {
        $errorMessage = 'All fields are required.';
    } else {
        // Update lecturer details
        $stmt = $conn->prepare("UPDATE USER SET UserPword = ?, UserFullName = ?, UserNickname = ? WHERE UserID = ? AND UserType = 'Lecturer'");
        $stmt->bind_param("ssss", $newPassword, $newFullName, $newNickname, $userID);

        if ($stmt->execute()) {
            $successMessage = 'Lecturer details updated successfully!';
            // Update local variables
            $userFullName = $newFullName;
            $userNickname = $newNickname;
        } else {
            $errorMessage = 'Failed to update lecturer details. Please try again.';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Lecturer</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Edit Lecturer</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="textbox">
                <label for="UserPword">Password:</label>
                <input type="password" id="UserPword" name="UserPword" value="<?php echo htmlspecialchars($_POST['UserPword'] ?? ''); ?>">
            </div>
            <div class="textbox">
                <label for="UserFullName">Full Name:</label>
                <input type="text" id="UserFullName" name="UserFullName" value="<?php echo htmlspecialchars($userFullName); ?>">
            </div>
            <div class="textbox">
                <label for="UserNickname">Nickname:</label>
                <input type="text" id="UserNickname" name="UserNickname" value="<?php echo htmlspecialchars($userNickname); ?>">
            </div>
            <button type="submit" class="btn">Update</button>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
