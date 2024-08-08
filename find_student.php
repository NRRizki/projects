<?php
include 'session_start.php';
include 'db_connect.php';

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$searchBy = isset($_POST['searchBy']) ? $_POST['searchBy'] : 'UserFullName';
$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';

$sql = "SELECT u.UserID, u.UserFullName, u.UserNickname, s.HPnum, s.ProgramCode FROM user u JOIN student s ON u.UserID = s.UserID WHERE $searchBy LIKE ?";
$stmt = $mysqli->prepare($sql);
$keyword = "%$keyword%";
$stmt->bind_param('s', $keyword);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Find Student</title>
</head>
<body>
    <h1>Find Student</h1>
    <form method="post">
        <select name="searchBy">
            <option value="UserFullName">Name</option>
            <option value="HPnum">Phone Number</option>
            <option value="UserID">Matric Number</option>
            <option value="ProgramCode">Program</option>
        </select>
        <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
        <button type="submit">Search</button>
    </form>
    <table border="1">
        <tr>
            <th>Matric Number</th>
            <th>Full Name</th>
            <th>Nickname</th>
            <th>Phone Number</th>
            <th>Program Code</th>
            <th>Class IDs</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                <td><?php echo htmlspecialchars($row['UserFullName']); ?></td>
                <td><?php echo htmlspecialchars($row['UserNickname']); ?></td>
                <td><?php echo htmlspecialchars($row['HPnum']); ?></td>
                <td><?php echo htmlspecialchars($row['ProgramCode']); ?></td>
                <td>
                    <?php
                    $classSQL = "SELECT ClassID FROM enrollmentlist WHERE UserID = ?";
                    $classStmt = $mysqli->prepare($classSQL);
                    $classStmt->bind_param('s', $row['UserID']);
                    $classStmt->execute();
                    $classResult = $classStmt->get_result();
                    $classes = [];
                    while ($classRow = $classResult->fetch_assoc()) {
                        $classes[] = htmlspecialchars($classRow['ClassID']);
                    }
                    echo implode(', ', $classes);
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php
$mysqli->close();
?>
