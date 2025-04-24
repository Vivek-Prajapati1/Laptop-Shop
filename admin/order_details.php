<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if order ID is provided
if(!isset($_GET['id'])){
    header("location: orders.php");
    exit;
}

$order_id = intval($_GET['id']);

// Get order details with user information
$query = "SELECT o.*, u.name, u.email, u.phone, u.address, u.city, u.state, u.zip,
          od.quantity, od.price as item_price, p.name as product_name, p.description, p.image
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.id
          LEFT JOIN order_details od ON o.id = od.order_id
          LEFT JOIN products p ON od.product_id = p.id
          WHERE o.id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0){
    header("location: orders.php");
    exit;
}

$order = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0);

// Calculate total
$total = 0;
$items = [];
while($item = mysqli_fetch_assoc($result)){
    $item['subtotal'] = $item['quantity'] * $item['item_price'];
    $total += $item['subtotal'];
    $items[] = $item;
}

// Get status color
function getStatusColor($status) {
    return match($status) {
        'delivered' => 'success',
        'processing' => 'info',
        'shipped' => 'primary',
        'cancelled' => 'danger',
        default => 'warning'
    };
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
       /* Alternate CSS for a modern frosted-glass aesthetic */
:root {
    --bg-main: #eef1f5;
    --bg-card: rgba(255, 255, 255, 0.8);
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --accent: #6366f1;
    --border-color: rgba(0, 0, 0, 0.08);
    --blur: 10px;
}

body {
    background: var(--bg-main);
    color: var(--text-primary);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    transition: all 0.3s ease;
}

.page-header {
    background: linear-gradient(to right,rgb(34, 33, 36),rgb(25, 26, 29));
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 2rem 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.back-button {
    color: white;
    border-color: rgba(255, 255, 255, 0.4);
    transition: all 0.3s ease;
}

.back-button:hover {
    background-color: white;
    color: var(--accent);
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.card {
    background: var(--bg-card);
    border: none;
    border-radius: 1rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(var(--blur));
    -webkit-backdrop-filter: blur(var(--blur));
    transition: all 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.card-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.info-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.info-value {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.table {
    color: var(--text-primary);
    border-radius: 12px;
    overflow: hidden;
}

.table thead th {
    background-color: #f1f5f9;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.04em;
    border-bottom: 2px solid var(--border-color);
    padding: 1rem;
}

.table tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.table tbody tr:hover {
    background-color: #f8fafc;
}

.badge {
    border-radius: 999px;
    font-weight: 500;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
    transition: transform 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}

.badge.bg-success {
    background-color: #22c55e !important;
}

.badge.bg-info {
    background-color: #3b82f6 !important;
}

.badge.bg-primary {
    background-color: #6366f1 !important;
}

.badge.bg-warning {
    background-color: #f59e0b !important;
}

.badge.bg-danger {
    background-color: #ef4444 !important;
}

.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    transition: transform 0.2s ease;
}

.product-image:hover {
    transform: scale(1.1);
}

.product-name {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.total-row {
    font-weight: 600;
    background-color: #f1f5f9;
}

.price {
    font-family: 'Fira Code', monospace;
    font-weight: 600;
    color: #1e293b;
}

    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="m-0">Order #<?php echo $order_id; ?></h2>
                <a href="orders.php" class="btn btn-outline-light back-button">
                    <i class="bi bi-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="row g-4">
            <!-- Customer Information -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-person me-2"></i>Customer Information
                        </h5>
                        <div class="info-label">Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['name'] ?? 'N/A'); ?></div>
                        
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></div>
                        
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></div>
                        
                        <div class="info-label">Shipping Address</div>
                        <div class="info-value">
                            <?php
                            $address_parts = array_filter([
                                $order['address'],
                                $order['city'],
                                $order['state'],
                                $order['zip']
                            ]);
                            echo htmlspecialchars(implode(', ', $address_parts) ?: 'N/A');
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>Order Information
                        </h5>
                        <div class="info-label">Order Date</div>
                        <div class="info-value"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                        
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-<?php echo getStatusColor($order['status']); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="info-label">Total Amount</div>
                        <div class="info-value price">₹<?php echo number_format($total, 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-box me-2"></i>Order Items
                        </h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 50%">Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php
                                                $image_path = !empty($item['image']) ? "../uploads/" . $item['image'] : "../uploads/default-laptop.svg";
                                                ?>
                                                <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                     class="product-image me-3"
                                                     onerror="this.src='../uploads/default-laptop.svg'">
                                                <div>
                                                    <div class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="price">₹<?php echo number_format($item['item_price'], 0); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td class="text-end price">₹<?php echo number_format($item['subtotal'], 0); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-end">Total:</td>
                                        <td class="text-end price">₹<?php echo number_format($total, 0); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 