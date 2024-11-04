<?php
include 'dbconfig.php';

$userName = $_POST['userName'];
$userName = $conn->real_escape_string($userName);

$result = $conn->query("SHOW TABLES LIKE '$userName'");
echo $result->num_rows > 0 ? 'exists' : 'not_exists';

$conn->close();
?>
