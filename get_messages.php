<?php
session_start();
include("connection.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

if (isset($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    $user_id = $_SESSION['user_id'];
    
    // Vérifier que l'utilisateur a le droit de voir ces messages
    $check_query = "SELECT * FROM chat_sessions WHERE id = '$chat_id' AND user_id = '$user_id'";
    $check_result = mysqli_query($con, $check_query);
    
    if ($check_result && mysqli_num_rows($check_result) > 0) {
        // L'utilisateur a le droit, on récupère les messages
        $query = "SELECT * FROM chat_messages WHERE chat_id = '$chat_id' ORDER BY created_at ASC";
        $result = mysqli_query($con, $query);
        
        $messages = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $messages[] = [
                    'sender' => $row['sender'],
                    'content' => $row['content']
                ];
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($messages);
        
    } else {
        // L'utilisateur n'a pas le droit de voir ces messages
        header('Content-Type: application/json');
        echo json_encode([]);
    }
    
} else {
    // Aucun chat_id fourni
    header('Content-Type: application/json');
    echo json_encode([]);
}

exit;
?>