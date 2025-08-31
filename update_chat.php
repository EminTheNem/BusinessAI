<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    
    if ($data['action'] === 'rename') {
        $chat_id = $data['chat_id'];
        $new_title = mysqli_real_escape_string($con, $data['new_title']);
        
        // Vérifier que l'utilisateur possède cette conversation
        $check_query = "SELECT * FROM chat_sessions WHERE id = '$chat_id' AND user_id = '$user_id'";
        $check_result = mysqli_query($con, $check_query);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $query = "UPDATE chat_sessions SET title = '$new_title' WHERE id = '$chat_id'";
            mysqli_query($con, $query);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
        }
    }
    
    if ($data['action'] === 'delete') {
        $chat_id = $data['chat_id'];
        
        // Vérifier que l'utilisateur possède cette conversation
        $check_query = "SELECT * FROM chat_sessions WHERE id = '$chat_id' AND user_id = '$user_id'";
        $check_result = mysqli_query($con, $check_query);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            // Supprimer d'abord les messages
            $delete_messages = "DELETE FROM chat_messages WHERE chat_id = '$chat_id'";
            mysqli_query($con, $delete_messages);
            
            // Puis supprimer la conversation
            $delete_chat = "DELETE FROM chat_sessions WHERE id = '$chat_id'";
            mysqli_query($con, $delete_chat);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
        }
    }
}
?>