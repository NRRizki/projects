<?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'wad_project';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST['userid'];
    $password = $_POST['password'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM USER WHERE UserID=? AND UserPword=?");
    $stmt->bind_param("ss", $userid, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
        echo "Error: " . $conn->error;
    } elseif ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['UserNickName'] = $user['UserNickName'];
        $_SESSION['UserType'] = $user['UserType'];
        // Login successful, redirect to dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        // Login failed, display error message
        $error_message = 'Invalid User ID or password';
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>Login</h1>
            <form method="post">
                <div class="textbox">
                    <label for="userid">User ID:</label>
                    <input type="text" id="userid" name="userid" required>
                </div>
                <div class="textbox">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <input type="submit" class="btn" value="Login">
            </form>
            <p id="error-message">
                <?php
                if (!empty($error_message)) {
                    echo $error_message;
                }
                ?>
            </p>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
