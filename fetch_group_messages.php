<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$conn = new mysqli("localhost", "root", "", "chat_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT users.username AS sender, group_messages.message 
        FROM group_messages 
        JOIN users ON group_messages.sender_id = users.id 
        ORDER BY group_messages.created_at ASC";

$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

$conn->close();
echo json_encode(['messages' => $messages]);
?>
