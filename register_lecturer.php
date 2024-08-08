<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

$successMessage = '';
$errorMessage = '';
$userID = $userPword = $userFullName = $userNickname = '';

// Form handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $userID = $_POST['UserID'];
    $userPword = $_POST['UserPword'];
    $userFullName = $_POST['UserFullName'];
    $userNickname = $_POST['UserNickname'];

    // Validate form data
    if (empty($userID)) {
        $errorMessage = 'User ID is required.';
    } elseif (empty($userPword)) {
        $errorMessage = 'Password is required.';
    } elseif (empty($userFullName)) {
        $errorMessage = 'Full Name is required.';
    } elseif (empty($userNickname)) {
        $errorMessage = 'Nickname is required.';
    } else {
        // Database connection
        include 'db_connect.php';

        // Check if UserID already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM USER WHERE UserID = ?");
        $stmt->bind_param("s", $userID);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errorMessage = 'Staff ID already present in database.';
        } else {
            // Prepare and execute the query
            $stmt = $conn->prepare("INSERT INTO USER (UserID, UserPword, UserFullName, UserNickname, UserType) VALUES (?, ?, ?, ?, 'Lecturer')");
            $stmt->bind_param("ssss", $userID, $userPword, $userFullName, $userNickname);

            if ($stmt->execute()) {
                $successMessage = 'Lecturer registered successfully!';
                // Clear input fields after successful registration
                $userID = $userPword = $userFullName = $userNickname = '';
            } else {
                $errorMessage = 'Error: ' . $conn->error;
            }

            // Close statement and connection
            $stmt->close();
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Lecturer</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Register Lecturer</h2>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage && !$successMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="textbox">
                <label for="UserID">User ID (Staff ID):</label>
                <input type="text" id="UserID" name="UserID" value="<?php echo htmlspecialchars($userID); ?>" required>
            </div>
            <div class="textbox">
                <label for="UserPword">Password:</label>
                <input type="password" id="UserPword" name="UserPword" value="<?php echo htmlspecialchars($userPword); ?>" required>
            </div>
            <div class="textbox">
                <label for="UserFullName">Full Name:</label>
                <input type="text" id="UserFullName" name="UserFullName" value="<?php echo htmlspecialchars($userFullName); ?>" required>
            </div>
            <div class="textbox">
                <label for="UserNickname">Nickname:</label>
                <input type="text" id="UserNickname" name="UserNickname" value="<?php echo htmlspecialchars($userNickname); ?>" required>
            </div>
            <input type="submit" class="btn" value="Register Lecturer">
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
