<?php
include 'session_start.php';
include 'db_connect.php';

$isAdmin = $_SESSION['UserType'] == 'Admin';
$userID = $isAdmin ? $_GET['userID'] : $_SESSION['UserID'];

if (!$isAdmin && $_SESSION['UserType'] != 'Student') {
    header('Location: login.php');
    exit();
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nickname = $_POST['nickname'];
    $hpnum = $_POST['hpnum'];
    $programCode = $_POST['program'];

    if (!empty($nickname) && !empty($programCode)) {
        $updateUserQuery = "UPDATE user SET UserNickname = ? WHERE UserID = ?";
        $stmtUser = $conn->prepare($updateUserQuery);
        $stmtUser->bind_param("ss", $nickname, $userID);

        $updateStudentQuery = "UPDATE student SET HPnum = ?, ProgramCode = ? WHERE UserID = ?";
        $stmtStudent = $conn->prepare($updateStudentQuery);
        $stmtStudent->bind_param("sss", $hpnum, $programCode, $userID);

        if ($stmtUser->execute() && $stmtStudent->execute()) {
            $successMessage = "Details updated successfully!";
        } else {
            $errorMessage = "Failed to update details. Please try again.";
        }
        $stmtUser->close();
        $stmtStudent->close();
    } else {
        $errorMessage = "Nickname and Program are required.";
    }
}

$userQuery = "SELECT UserFullName, UserNickname, UserID FROM user WHERE UserID = ?";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("s", $userID);
$stmtUser->execute();
$userResult = $stmtUser->get_result();
$user = $userResult->fetch_assoc();

$studentQuery = "SELECT HPnum, ProgramCode FROM student WHERE UserID = ?";
$stmtStudent = $conn->prepare($studentQuery);
$stmtStudent->bind_param("s", $userID);
$stmtStudent->execute();
$studentResult = $stmtStudent->get_result();
$student = $studentResult->fetch_assoc();

$programQuery = "SELECT ProgramCode, ProgramName FROM programlist";
$programResult = $conn->query($programQuery);

$stmtUser->close();
$stmtStudent->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Personal Details</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>
    <main>
        <h2>Update Personal Details</h2>
        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form action="update_details.php?userID=<?php echo urlencode($userID); ?>" method="post">
            <label for="fullName">Full Name:</label>
            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['UserFullName']); ?>" readonly><br>
            
            <label for="userID">Student ID:</label>
            <input type="text" id="userID" name="userID" value="<?php echo htmlspecialchars($user['UserID']); ?>" readonly><br>
            
            <label for="program">Program:</label>
            <select id="program" name="program">
                <?php while ($program = $programResult->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($program['ProgramCode']); ?>" <?php if ($program['ProgramCode'] == $student['ProgramCode']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($program['ProgramCode']) . ' - ' . htmlspecialchars($program['ProgramName']); ?>
                    </option>
                <?php endwhile; ?>
            </select><br>

            <label for="nickname">Nickname:</label>
            <input type="text" id="nickname" name="nickname" value="<?php echo htmlspecialchars($user['UserNickname']); ?>" required><br>

            <label for="hpnum">HP Number:</label>
            <input type="text" id="hpnum" name="hpnum" value="<?php echo htmlspecialchars($student['HPnum']); ?>"><br>

            <input type="submit" value="Update Details">
        </form>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
