<?php
include 'session_start.php';

try {
    $dsn = "mysql:host=$servername;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $userID = $_POST['UserID'];
        $userPword = password_hash($_POST['UserPword'], PASSWORD_BCRYPT);
        $userFullName = $_POST['UserFullName'];
        $userNickname = $_POST['UserNickname'];
        $programCode = $_POST['ProgramCode'];
        $hpNum = $_POST['HPnum'];

        $sql = "INSERT INTO user (UserID, UserPword, UserType, UserFullName, UserNickname) VALUES (?, ?, 'Student', ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userID, $userPword, $userFullName, $userNickname]);

        $sql = "INSERT INTO student (UserID, ProgramCode, HPnum) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userID, $programCode, $hpNum]);

        echo "Student created successfully.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Student</title>
</head>
<body>
    <h1>Create Student</h1>
    <form method="post">
        <label for="UserID">UserID:</label>
        <input type="text" id="UserID" name="UserID" required><br>
        <label for="UserPword">Password:</label>
        <input type="password" id="UserPword" name="UserPword" required><br>
        <label for="UserFullName">Full Name:</label>
        <input type="text" id="UserFullName" name="UserFullName" required><br>
        <label for="UserNickname">Nickname:</label>
        <input type="text" id="UserNickname" name="UserNickname" required><br>
        <label for="ProgramCode">Program Code:</label>
        <input type="text" id="ProgramCode" name="ProgramCode" required><br>
        <label for="HPnum">Phone Number:</label>
        <input type="text" id="HPnum" name="HPnum"><br>
        <button type="submit">Create</button>
    </form>
</body>
</html>
