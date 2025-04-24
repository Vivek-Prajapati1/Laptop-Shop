<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if the request is POST and has the required data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array(strtolower($status), $valid_statuses)) {
        $_SESSION['error'] = "Invalid status provided";
        header("location: orders.php");
        exit;
    }
    
    // Update the order status
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
        
        if(mysqli_stmt_execute($stmt)){
            $_SESSION['success'] = "Order status updated successfully";
        } else {
            $_SESSION['error'] = "Error updating order status";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['error'] = "Database error occurred";
    }
} else {
    $_SESSION['error'] = "Invalid request";
}

// Redirect back to orders page
header("location: orders.php");
exit;
?> 