<?php
require_once '../db_config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$message = $conn->real_escape_string($_POST['message']);
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$sql = "INSERT INTO feedback (user_id, username, message) VALUES ($user_id, '$username', '$message')";
if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
}
?>