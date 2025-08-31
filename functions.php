<?php
function check_login($con)
{
    // Vérifie si l'utilisateur est connecté
    if(isset($_SESSION['user_id']))
    {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE user_id = ? LIMIT 1";
        
        // Utilisation d'une requête préparée pour la sécurité
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $id); // Changé de "i" à "s" car user_id est un string
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if($result && mysqli_num_rows($result) > 0)
        {
            return mysqli_fetch_assoc($result); // Retourne les données utilisateur
        }
    }
    
    // Retourne false si non connecté ou session invalide
    return false;
}

function random_num($length) 
{
    $text = "";
    if($length < 5) {
        $length = 5; // Longueur minimale de 5 chiffres
    }

    $len = rand(4, $length); // Longueur aléatoire entre 4 et $length

    for ($i = 0; $i < $len; $i++) {
        $text .= rand(0, 9); // Ajoute un chiffre aléatoire
    }
    return $text;
}

// Fonction pour sécuriser les entrées
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ... le reste de votre code
?>