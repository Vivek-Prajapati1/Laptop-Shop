<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get current order status
$query = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($order = mysqli_fetch_assoc($result)) {
    // Get status color
    $status_color = match($order['status']) {
        'delivered' => 'bg-green-500',
        'processing' => 'bg-blue-500',
        'shipped' => 'bg-indigo-500',
        'cancelled' => 'bg-red-500',
        'confirmed' => 'bg-blue-500',
        default => 'bg-yellow-500'
    };

    // Get status text
    $status_text = match($order['status']) {
        'confirmed' => 'Order Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        default => ucfirst($order['status'])
    };

    echo json_encode([
        'success' => true,
        'status' => $order['status'],
        'status_text' => $status_text,
        'status_color' => $status_color
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
}
?> 