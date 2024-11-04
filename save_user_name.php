<?php
include 'dbconfig.php';

$userName = $_POST['userName'];
$userName = $conn->real_escape_string($userName);

if (preg_match('/^[a-zA-Z0-9_]+$/', $userName)) {
    $createTableQuery = "CREATE TABLE `$userName` (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        step INT(2) NOT NULL,
        answer TEXT NOT NULL
    )";
    if ($conn->query($createTableQuery) === TRUE) {
        echo "Table created successfully";
    } else {
        echo "Error creating table: " . $conn->error;
    }
} else {
    echo "Invalid username";
}

$conn->close();
?>
