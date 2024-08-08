<?php
include 'session_start.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $_SESSION['UserNickName']; ?>'s Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($_SESSION['UserNickName']); ?>'s Page</h1>
    </header>
    <nav>
        <ul>
            <?php if ($_SESSION['UserType'] == 'Lecturer') { ?>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="classes.php">My Classes</a></li>
                <li><a href="find_student.php">Find Student</a></li>
                <li><a href="create_class.php">Create Class</a></li>
                <li><a href="remove_class.php">Remove Class</a></li>
            <?php } elseif ($_SESSION['UserType'] == 'Admin') { ?>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="lecturers_list.php">View Lecturers</a></li>
                <li><a href="students_list.php">View Students</a></li>
                <li><a href="register_lecturer.php">Register Lecturer</a></li>
                <li><a href="register_student.php">Register Student</a></li>
                <li><a href="create_course.php">Create Course</a></li>
                <li><a href="remove_course.php">Remove Course</a></li>
                <li><a href="remove_lecturer.php">Remove Lecturer</a></li>
                <li><a href="remove_student.php">Remove Student</a></li>
            <?php } elseif ($_SESSION['UserType'] == 'Student') { ?>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="update_details.php">Update Personal Details</a></li>
            <?php } ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>