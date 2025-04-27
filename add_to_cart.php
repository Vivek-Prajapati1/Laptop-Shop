<?php
require_once 'includes/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if product_id and quantity are set
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Check if the product already exists in the cart
$check_query = "SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
$check_stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $product_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) > 0) {
    // Update existing cart item
    $cart_item = mysqli_fetch_assoc($result);
    $new_quantity = max(1, $cart_item['quantity'] + $quantity);
    
    $update_query = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_item['cart_id']);
    mysqli_stmt_execute($update_stmt);
} else {
    // Add new cart item
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "iii", $user_id, $product_id, $quantity);
    mysqli_stmt_execute($insert_stmt);
}

// Add success message to session
$_SESSION['cart_message'] = 'Item added to cart successfully!';

// Redirect to cart page
header('Location: cart.php');
exit();
?>
