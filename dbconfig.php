<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "beta_db_BORED";

// conn
$conn = new mysqli($servername, $username, $password, $dbname);

// check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
