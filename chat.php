<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "chat_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$receiver_id = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : null;

if ($receiver_id) {
    $result = $conn->query("SELECT m.message, u1.username AS sender, u2.username AS receiver, m.created_at
                            FROM messages m
                            JOIN users u1 ON m.sender_id = u1.id
                            JOIN users u2 ON m.receiver_id = u2.id
                            WHERE (m.sender_id = $user_id AND m.receiver_id = $receiver_id)
                            OR (m.sender_id = $receiver_id AND m.receiver_id = $user_id)
                            ORDER BY m.created_at ASC");
} else {
    echo "No receiver selected!";
    exit;
}

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Chat with User</h2>
        <div class="chat-box">
            <?php foreach ($messages as $message) { ?>
                <div class="message">
                    <strong><?php echo $message['sender']; ?>:</strong>
                    <?php echo $message['message']; ?>
                    <span class="timestamp"><?php echo $message['created_at']; ?></span>
                </div>
            <?php } ?>
        </div>
        <form method="post" action="send_message.php">
            <input type="hidden" name="receiver_id" value="<?php echo $receiver_id; ?>">
            <textarea name="message" required></textarea><br>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
