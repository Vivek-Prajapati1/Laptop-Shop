<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to continue with purchase.";
    $_SESSION['redirect_after_login'] = 'buy_now.php?' . http_build_query($_POST);
    header('Location: login.php');
    exit();
}

// Check if product_id and quantity are set
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: shop.php');
    exit();
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);

// Get product details
$query = "SELECT * FROM products WHERE id = ? AND status = 'active'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Product not found or unavailable.";
    header('Location: shop.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Check stock availability
if ($product['stock'] < $quantity) {
    $_SESSION['error'] = "Sorry, requested quantity is not available.";
    header('Location: shop.php');
    exit();
}

// Store buy now information in session
$_SESSION['buy_now'] = [
    'product_id' => $product_id,
    'quantity' => $quantity,
    'price' => $product['price'],
    'total' => $product['price'] * $quantity
];

// Redirect to checkout
header('Location: checkout.php?buy_now=1');
exit();
?> 