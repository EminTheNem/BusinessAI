<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'login_sample_db';

$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(!$con) {
    die("Failed to connect: " . mysqli_connect_error());
}


// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ... le reste de votre code
?>