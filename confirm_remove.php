<?php
include 'session_start.php';

// Check if the user is an admin
if ($_SESSION['UserType'] !== 'Admin') {
    header('Location: dashboard.php');
    exit;
}

if (!isset($_GET['UserID'])) {
    header('Location: remove_lecturer.php');
    exit;
}

$userID = $_GET['UserID'];

// Database connection
include 'db_connect.php';

// Fetch lecturer's full name
$stmt = $conn->prepare("SELECT UserFullName FROM USER WHERE UserID = ? AND UserType = 'Lecturer'");
$stmt->bind_param("s", $userID);
$stmt->execute();
$stmt->bind_result($userFullName);
$stmt->fetch();
$stmt->close();

if (!$userFullName) {
    header('Location: remove_lecturer.php?error=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        // Delete lecturer
        $stmt = $conn->prepare("DELETE FROM USER WHERE UserID = ? AND UserType = 'Lecturer'");
        $stmt->bind_param("s", $userID);

        if ($stmt->execute()) {
            header('Location: remove_lecturer.php?success=1');
        } else {
            header('Location: remove_lecturer.php?error=1');
        }

        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Redirect to remove_lecturer.php if cancelled
        header('Location: remove_lecturer.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Remove Lecturer</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .btn {
            width: 100px;
            padding: 10px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
            text-align: center;
            display: inline-block;
        }

        .btn-confirm {
            background-color: #5bc0de; /* Confirm button color */
        }

        .btn-confirm:hover {
            background-color: #31b0d5; /* Hover effect for confirm button */
        }

        .btn-cancel {
            background-color: #d9534f; /* Cancel button color */
            color: white;
        }

        .btn-cancel:hover {
            background-color: #c9302c; /* Hover effect for cancel button */
        }

        .btn-container {
            display: flex;
            gap: 10px; /* Space between buttons */
        }
    </style>
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Confirm Remove Lecturer</h2>
        <p>Are you sure you want to remove the lecturer with the following details?</p>
        <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($userID); ?></p>
        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($userFullName); ?></p>

        <form method="post">
            <div class="btn-container">
                <button type="submit" name="confirm" class="btn btn-confirm">Yes, Remove</button>
                <a href="remove_lecturer.php" class="btn btn-cancel">Cancel</a>
            </div>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
