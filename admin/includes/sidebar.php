<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<div class="col-md-3 col-lg-2 px-0 sidebar">
    <h4>LaptopShop Admin</h4>
    <nav>
        <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="products.php" class="<?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
            <i class="fas fa-laptop"></i> Products
        </a>
        <a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Orders
        </a>
        <a href="users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Categories
        </a>
        <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<style>
    .sidebar {
        min-height: 100vh;
        background: #2c3e50;
        padding: 20px;
        position: fixed;
        left: 0;
        top: 0;
        width: 250px;
        z-index: 1000;
    }
    .sidebar h4 {
        color: #ecf0f1;
        font-size: 1.5rem;
        padding-bottom: 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar nav {
        margin-top: 20px;
    }
    .sidebar a {
        color: #ecf0f1;
        text-decoration: none;
        display: block;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 5px;
        transition: all 0.3s ease;
    }
    .sidebar a:hover {
        background: rgba(255,255,255,0.1);
        transform: translateX(5px);
    }
    .sidebar a.active {
        background: #3498db;
        color: white;
    }
    .sidebar i {
        width: 25px;
        margin-right: 10px;
    }
    .main-content {
        margin-left: 250px;
        padding: 20px;
        background: #f8f9fa;
    }
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            position: relative;
            min-height: auto;
        }
        .main-content {
            margin-left: 0;
        }
    }
</style> 