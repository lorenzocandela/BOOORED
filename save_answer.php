<?php
include 'dbconfig.php';

// logga i dati inviati
$userName = isset($_POST['userName']) ? $_POST['userName'] : '';
$answer = isset($_POST['answer']) ? $_POST['answer'] : '';
$step = isset($_POST['step']) ? intval($_POST['step']) : 0;

error_log("Received data - User: $userName, Step: $step, Answer: $answer");

if ($userName && $answer && $step) {
    $userName = $conn->real_escape_string($userName);
    $answer = $conn->real_escape_string($answer);
    
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `$userName` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        step INT NOT NULL,
        answer TEXT NOT NULL
    )";
    if ($conn->query($createTableQuery) === FALSE) {
        error_log("Table creation error: " . $conn->error);
        echo "Table creation error: " . $conn->error;
        exit();
    }
    
    $insertQuery = "INSERT INTO `$userName` (step, answer) VALUES ($step, '$answer')";
    if ($conn->query($insertQuery) === TRUE) {
        error_log("Insert successful for step $step with answer: $answer");
        echo "Success";
    } else {
        error_log("Insert error: " . $conn->error);
        echo "Insert error: " . $conn->error;
    }
} else {
    echo "Invalid input";
}

$conn->close();
?>