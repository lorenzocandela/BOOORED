<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Credenziali e dettagli del database
$servername = "46.252.158.186"; // IP del server MySQL di Netsons
$username = "fjnhdtgc_wp982";
$password = "Ciaociam23.";
$dbname = "fjnhdtgc_wp982";

// Crea la connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully.<br>";

// Esegui una query di prova
$sql = "SELECT 1";
$result = $conn->query($sql);

if ($result) {
    echo "Query executed successfully.";
} else {
    echo "Query failed: " . $conn->error;
}

// Chiudi la connessione
$conn->close();
?>
