<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

if (!isset($_GET['receiver_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Receiver ID missing']);
    exit;
}

$receiver_id = intval($_GET['receiver_id']);

$conn = new mysqli("localhost", "root", "", "chat_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT messages.message, messages.created_at, 
               CASE 
                   WHEN messages.sender_id = ? THEN 'You' 
                   ELSE users.username 
               END AS sender 
        FROM messages 
        JOIN users ON messages.sender_id = users.id 
        WHERE (messages.sender_id = ? AND messages.receiver_id = ?) 
           OR (messages.sender_id = ? AND messages.receiver_id = ?) 
        ORDER BY messages.created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $_SESSION['user_id'], $_SESSION['user_id'], $receiver_id, $receiver_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();
echo json_encode(['messages' => $messages]);
?>
