<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Remove Lecturer</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'layout.php'; ?>

    <main>
        <h2>Remove Lecturer</h2>

        <?php
        // Initialize success and error messages
        $successMessage = '';
        $errorMessage = '';

        // Check for success or error messages in the URL
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            $successMessage = 'Lecturer removed successfully!';
        }

        if (isset($_GET['error']) && $_GET['error'] == 1) {
            $errorMessage = 'Failed to remove lecturer. Please try again.';
        }
        ?>

        <?php if ($successMessage): ?>
            <p class="success-message"><?php echo htmlspecialchars($successMessage); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Full Name</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                include 'db_connect.php';

                // Fetch all lecturers except admins
                $result = $conn->query("SELECT UserID, UserFullName FROM USER WHERE UserType = 'Lecturer'");

                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                    <td><?php echo htmlspecialchars($row['UserFullName']); ?></td>
                    <td>
                        <a href="confirm_remove.php?UserID=<?php echo urlencode($row['UserID']); ?>" class="btn-remove">Remove</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
