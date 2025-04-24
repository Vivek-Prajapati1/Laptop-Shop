<?php
/* Database credentials */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'laptopshop');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

/* Check connection */
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

/* Set charset to ensure proper encoding */
mysqli_set_charset($link, "utf8mb4");

/* Enable error reporting for debugging */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* Start session if not already started */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Function to sanitize input */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/* Function to generate random string */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/* Function to format price */
function format_price($price) {
    return number_format($price, 2);
}

/* Function to check if user is logged in */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/* Function to check if admin is logged in */
function is_admin_logged_in() {
    return isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true;
}

/* Function to redirect with message */
function redirect_with_message($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit;
}
?> 