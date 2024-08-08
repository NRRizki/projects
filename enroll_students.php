<?php
include 'session_start.php';
include 'db_connect.php';

$classID = isset($_GET['classID']) ? $_GET['classID'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricNumber = trim($_POST['matricNumber']);

    if ($matricNumber) {
        // Check if the user exists and get their type
        $userCheckQuery = $conn->prepare("SELECT UserType FROM user WHERE UserID = ?");
        if (!$userCheckQuery) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $userCheckQuery->bind_param('s', $matricNumber);
        $userCheckQuery->execute();
        $userCheckResult = $userCheckQuery->get_result();

        if ($userCheckResult->num_rows === 0) {
            echo "<script>alert('The Student is not present in the system');</script>";
        } else {
            $userType = $userCheckResult->fetch_assoc()['UserType'];
            if ($userType == 'Lecturer') {
                echo "<script>alert('This user is a Lecturer');</script>";
            } elseif ($userType == 'Admin') {
                echo "<script>alert('This user is an Admin');</script>";
            } elseif ($userType == 'Student') {
                // Check if the student is already enrolled in the class
                $enrollmentCheckQuery = $conn->prepare("SELECT * FROM enrollmentlist WHERE ClassID = ? AND UserID = ?");
                if (!$enrollmentCheckQuery) {
                    die('Prepare failed: ' . htmlspecialchars($conn->error));
                }
                $enrollmentCheckQuery->bind_param('ss', $classID, $matricNumber);
                $enrollmentCheckQuery->execute();
                $enrollmentCheckResult = $enrollmentCheckQuery->get_result();

                if ($enrollmentCheckResult->num_rows > 0) {
                    echo "<script>alert('Student already enrolled in this group');</script>";
                } else {
                    // Enroll the student
                    $enrollQuery = $conn->prepare("INSERT INTO enrollmentlist (ClassID, UserID) VALUES (?, ?)");
                    if (!$enrollQuery) {
                        die('Prepare failed: ' . htmlspecialchars($conn->error));
                    }
                    $enrollQuery->bind_param('ss', $classID, $matricNumber);
                    if ($enrollQuery->execute()) {
                        echo "<script>alert('Enrollment successful');</script>";
                    } else {
                        echo "<script>alert('Enrollment failed');</script>";
                    }
                    $enrollQuery->close();
                }
                $enrollmentCheckQuery->close();
            }
        }
        $userCheckQuery->close();
    } else {
        echo "<script>alert('Please enter a Matric Number');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Students</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-container input[type="submit"],
        .form-container a.back-button {
            padding: 10px 20px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .form-container input[type="submit"]:hover,
        .form-container a.back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Enroll Students to <?php echo htmlspecialchars($classID); ?></h2>

        <div class="form-container">
            <form method="POST" action="enroll_students.php?classID=<?php echo urlencode($classID); ?>">
                <label for="matricNumber">Matric Number:</label>
                <input type="text" id="matricNumber" name="matricNumber" required>
                <input type="submit" value="Enroll">
            </form>
            <a href="class_details.php?classID=<?php echo urlencode($classID); ?>" class="back-button">Back to <?php echo htmlspecialchars($classID); ?></a>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
