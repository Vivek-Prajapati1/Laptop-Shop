<?php
// Database credentials
$servername = "localhost";  // Server name (usually localhost in XAMPP)
$username = "root";         // Default username for XAMPP
$password = "";             // Default password for XAMPP is empty
$database = "laptopshop";   // Your database name (change if different)

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
