<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] === 'create_chat') {
        $user_id = $data['user_id'];
        $query = "INSERT INTO chat_sessions (user_id, title) VALUES ('$user_id', 'Nouvelle Conversation')";
        mysqli_query($con, $query);
        $chat_id = mysqli_insert_id($con);
        
        echo json_encode(['success' => true, 'chat_id' => $chat_id]);
    }
    
    if ($data['action'] === 'save_message') {
        $chat_id = $data['chat_id'];
        $sender = $data['sender'];
        $content = mysqli_real_escape_string($con, $data['content']);
        
        $query = "INSERT INTO chat_messages (chat_id, sender, content) 
                 VALUES ('$chat_id', '$sender', '$content')";
        mysqli_query($con, $query);
        
        echo json_encode(['success' => true]);
    }
}
?>