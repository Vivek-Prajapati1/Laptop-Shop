<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get all orders with user information
$orders = mysqli_query($conn, "SELECT o.*, 
                              u.name as customer_name,
                              u.address as shipping_address,
                              u.city as shipping_city,
                              u.state as shipping_state,
                              u.zip as shipping_zip,
                              u.phone as shipping_phone
                              FROM orders o 
                              LEFT JOIN users u ON o.user_id = u.id 
                              ORDER BY o.created_at DESC");

// Status color mapping
function getStatusColor($status) {
    switch(strtolower($status)) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Orders Management</h2>
                    <div class="btn-group">
                        <a href="?status=all" class="btn btn-outline-primary">All Orders</a>
                        <a href="?status=pending" class="btn btn-outline-warning">Pending</a>
                        <a href="?status=processing" class="btn btn-outline-info">Processing</a>
                        <a href="?status=shipped" class="btn btn-outline-primary">Shipped</a>
                        <a href="?status=delivered" class="btn btn-outline-success">Delivered</a>
                        <a href="?status=cancelled" class="btn btn-outline-danger">Cancelled</a>
                    </div>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Total Amount</th>
                                        <th>Shipping Details</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $status_filter = isset($_GET['status']) && $_GET['status'] != 'all' 
                                        ? "WHERE o.status = '" . mysqli_real_escape_string($conn, $_GET['status']) . "'"
                                        : "";
                                    
                                    $query = "SELECT o.*, 
                                              u.name as customer_name,
                                              u.address as shipping_address,
                                              u.city as shipping_city,
                                              u.state as shipping_state,
                                              u.zip as shipping_zip,
                                              u.phone as shipping_phone
                                              FROM orders o 
                                              LEFT JOIN users u ON o.user_id = u.id 
                                              $status_filter
                                              ORDER BY o.created_at DESC";
                                    $result = mysqli_query($conn, $query);
                                    
                                    while ($order = mysqli_fetch_assoc($result)) {
                                        $statusColor = getStatusColor($order['status']);
                                        ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($order['customer_name']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($order['shipping_phone']); ?></small>
                                            </td>
                                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                                                <?php echo htmlspecialchars($order['shipping_city']); ?>, 
                                                <?php echo htmlspecialchars($order['shipping_state']); ?> 
                                                <?php echo htmlspecialchars($order['shipping_zip']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $statusColor; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                                                   class="btn btn-sm btn-primary"
                                                   data-bs-toggle="tooltip"
                                                   title="View Order Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-info"
                                                        onclick="updateStatus(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>')"
                                                        data-bs-toggle="tooltip"
                                                        title="Update Status">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="update_order_status.php" method="POST">
                        <input type="hidden" name="order_id" id="orderIdInput">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="statusInput" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        function updateStatus(orderId, currentStatus) {
            document.getElementById('orderIdInput').value = orderId;
            document.getElementById('statusInput').value = currentStatus;
            var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
            statusModal.show();
        }
    </script>
</body>
</html> 