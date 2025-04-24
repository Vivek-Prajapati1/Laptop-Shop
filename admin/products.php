<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Handle product activation/deactivation and featured status
if(isset($_POST['toggle_status']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $new_status = $_POST['status'];
    
    $sql = "UPDATE products SET status = ? WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $new_status, $product_id);
        if(mysqli_stmt_execute($stmt)){
            $_SESSION['success'] = "Product status updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating product status.";
        }
        header("location: products.php");
        exit;
    }
}

// Handle featured status toggle
if(isset($_POST['toggle_featured']) && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $new_featured = $_POST['new_state'] === 'true' ? 1 : 0;
    
    $sql = "UPDATE products SET featured = ? WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "ii", $new_featured, $product_id);
        if(mysqli_stmt_execute($stmt)){
            echo json_encode(['success' => true, 'featured' => $new_featured]);
            exit;
        }
    }
    echo json_encode(['success' => false]);
    exit;
}

// Add status column if it doesn't exist
$check_status_column = "SHOW COLUMNS FROM products LIKE 'status'";
$result = mysqli_query($conn, $check_status_column);
if (mysqli_num_rows($result) == 0) {
    mysqli_query($conn, "ALTER TABLE products ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
}

// Get all products with their categories
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$result = mysqli_query($conn, $sql);

// Handle query error
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Function to get correct image path
function getImagePath($imageName) {
    if(empty($imageName)) {
        return "../uploads/default-laptop.svg";
    }
    
    $imagePath = "../uploads/" . $imageName;
    
    // Check if file exists
    if(file_exists($imagePath)) {
        return $imagePath;
    }
    
    // Try to find a case-insensitive match
    $files = scandir("../uploads");
    foreach ($files as $file) {
        if (strtolower($file) === strtolower($imageName)) {
            return "../uploads/" . $file;
        }
    }
    
    // Return default image if not found
    return "../uploads/default-laptop.svg";
}

// Function to get image dimensions and validate
function validateImage($path) {
    $size = getimagesize($path);
    if ($size === false) {
        return false;
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-bg: #ffffff;
            --secondary-bg: #343a40;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --accent-color: #0d6efd;
        }
        
        body {
            background-color: var(--primary-bg);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 30px;
            min-height: 100vh;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 2px;
            background: white;
        }
        
        .table > tbody > tr > td {
            vertical-align: middle;
        }
        
        .image-preview {
            position: relative;
            display: inline-block;
        }
        
        .image-preview .zoom {
            display: none;
            position: absolute;
            z-index: 999;
            width: 300px;
            height: 300px;
            border: 2px solid #ddd;
            border-radius: 4px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            object-fit: contain;
            padding: 4px;
            left: 100%;
            top: 0;
        }
        
        .image-preview:hover .zoom {
            display: block;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
        }

        .featured-toggle {
            width: 40px;
            height: 24px;
            position: relative;
            display: inline-block;
        }

        .featured-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .featured-toggle .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .featured-toggle .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .featured-toggle input:checked + .slider {
            background-color: #ffc107;
        }

        .featured-toggle input:checked + .slider:before {
            transform: translateX(16px);
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .table-responsive {
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-wrap: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Products</h2>
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
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
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td>
                                            <div class="image-preview">
                                                <img src="<?php echo getImagePath($row['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                                     class="product-image">
                                                <img src="<?php echo getImagePath($row['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                                     class="zoom">
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td>₹<?php echo number_format($row['price'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php echo $row['stock'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                <?php echo $row['stock']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $row['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                                <button type="submit" name="toggle_status" class="btn btn-sm <?php echo $row['status'] === 'active' ? 'btn-success' : 'btn-secondary'; ?>">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-inline featured-form">
                                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="new_state" value="<?php echo $row['featured'] ? 'false' : 'true'; ?>">
                                                <label class="featured-toggle">
                                                    <input type="checkbox" 
                                                           class="featured-checkbox"
                                                           <?php echo $row['featured'] ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-primary btn-sm" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Are you sure you want to delete this product?')"
                                                   title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle featured toggle
            document.querySelectorAll('.featured-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const form = this.closest('.featured-form');
                    const formData = new FormData(form);
                    formData.append('toggle_featured', '1');

                    fetch('products.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the checkbox state
                            this.checked = data.featured === 1;
                            // Update the hidden input for next toggle
                            form.querySelector('input[name="new_state"]').value = data.featured ? 'false' : 'true';
                            
                            // Show success message
                            const alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.innerHTML = `
                                Product ${data.featured ? 'added to' : 'removed from'} featured!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            document.querySelector('.main-content').insertBefore(alert, document.querySelector('.card'));
                            
                            // Auto dismiss alert after 3 seconds
                            setTimeout(() => {
                                alert.remove();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show error message
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-danger alert-dismissible fade show';
                        alert.innerHTML = `
                            Error updating featured status!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        document.querySelector('.main-content').insertBefore(alert, document.querySelector('.card'));
                    });
                });
            });
        });
    </script>
</body>
</html> 