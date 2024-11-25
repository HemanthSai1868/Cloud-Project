<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['message']) || trim($data['message']) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Message cannot be empty']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "chat_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("INSERT INTO group_messages (sender_id, message, created_at) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $_SESSION['user_id'], $data['message']);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send message']);
}

$stmt->close();
$conn->close();
?>
