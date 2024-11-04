<?php
include 'dbconfig.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userName = $conn->real_escape_string($_POST['userName']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);

    // togli id_att dalla query perchè dovrebbe essere auto-incrementale n (checcka)
    $query = "INSERT INTO saver (username, title_att, desc_att, saved) VALUES (?, ?, ?, 1)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $userName, $title, $description);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Activity saved successfully';
    } else {
        $response['message'] = 'Error saving activity: ' . $conn->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid request method';
}

$conn->close();
echo json_encode($response);
?>