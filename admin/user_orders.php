<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get user details and orders
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id = trim($_GET["id"]);
    
    // Get user details
    $sql = "SELECT * FROM users WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            
            if(mysqli_num_rows($result) == 1){
                $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Get user's orders
                $orders_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
                if($orders_stmt = mysqli_prepare($conn, $orders_sql)){
                    mysqli_stmt_bind_param($orders_stmt, "i", $id);
                    mysqli_stmt_execute($orders_stmt);
                    $orders_result = mysqli_stmt_get_result($orders_stmt);
                    $orders = mysqli_fetch_all($orders_result, MYSQLI_ASSOC);
                }
            } else{
                header("location: users.php");
                exit();
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    mysqli_stmt_close($stmt);
} else{
    header("location: users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #f8f9fa;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h3 class="mb-4">Admin Panel</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="dashboard.php" class="nav-link">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="products.php" class="nav-link">
                            <i class="bi bi-laptop"></i> Products
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="orders.php" class="nav-link">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="users.php" class="nav-link active">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="categories.php" class="nav-link">
                            <i class="bi bi-tags"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item mt-4">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Orders for <?php echo $user['name']; ?></h2>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">User Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                                <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
                                <p><strong>Address:</strong> <?php echo $user['address']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['status'] == 'delivered' ? 'success' : 
                                                    ($order['status'] == 'processing' ? 'warning' : 
                                                        ($order['status'] == 'shipped' ? 'info' : 
                                                            ($order['status'] == 'cancelled' ? 'danger' : 'secondary'))); 
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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