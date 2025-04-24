<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (isset($_POST['quantity']) && isset($_SESSION['buy_now'])) {
    $quantity = intval($_POST['quantity']);
    
    // Validate quantity against product stock
    $product_query = "SELECT stock FROM products WHERE id = ? AND status = 'active'";
    $stmt = mysqli_prepare($conn, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['buy_now']['product_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($product = mysqli_fetch_assoc($result)) {
        $max_stock = intval($product['stock']);
        
        // Ensure quantity is within valid range
        if ($quantity < 1) {
            $quantity = 1;
        } elseif ($quantity > $max_stock) {
            $quantity = $max_stock;
        }
        
        // Update session with validated quantity
        $_SESSION['buy_now']['quantity'] = $quantity;
        $_SESSION['buy_now']['total'] = $_SESSION['buy_now']['price'] * $quantity;
        
        echo json_encode([
            'success' => true,
            'quantity' => $quantity,
            'total' => $_SESSION['buy_now']['total'],
            'max_stock' => $max_stock
        ]);
        exit;
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid quantity or product not found'
]);
?> 