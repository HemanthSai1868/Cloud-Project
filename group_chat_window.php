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

$conn->query("UPDATE users SET last_activity = NOW() WHERE id = " . $_SESSION['user_id']);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h2 {
            text-align: center;
        }
        #chat-box {
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }
        textarea {
            width: 100%;
            height: 50px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Group Chat</h2>
        <div id="chat-box"></div>
        <textarea id="message" placeholder="Type your message..."></textarea>
        <button id="send">Send</button>
    </div>
    <script>
        const chatBox = document.getElementById('chat-box');
        const messageInput = document.getElementById('message');
        const sendButton = document.getElementById('send');

        function fetchMessages() {
            fetch('fetch_group_messages.php')
                .then(response => response.json())
                .then(data => {
                    chatBox.innerHTML = '';
                    data.messages.forEach(msg => {
                        chatBox.innerHTML += `<p><strong>${msg.sender}:</strong> ${msg.message}</p>`;
                    });
                });
        }

        sendButton.addEventListener('click', () => {
            const message = messageInput.value;
            if (message.trim() !== '') {
                fetch('send_group_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message })
                }).then(() => {
                    messageInput.value = '';
                    fetchMessages();
                });
            }
        });

        setInterval(fetchMessages, 1000);
    </script>
</body>
</html>
