<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Process delete operation
if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])){
    $id = trim($_GET["id"]);
    
    // Check if category has products
    $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
    if($stmt = mysqli_prepare($conn, $check_sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            if($row['count'] > 0){
                header("location: categories.php?error=Cannot delete category with associated products");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Delete category
    $sql = "DELETE FROM categories WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        if(mysqli_stmt_execute($stmt)){
            header("location: categories.php?success=Category deleted successfully");
            exit();
        } else{
            header("location: categories.php?error=Something went wrong");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

// Process add operation
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "add"){
    $name = trim($_POST["name"]);
    
    if(empty($name)){
        header("location: categories.php?error=Please enter category name");
        exit();
    }
    
    // Check if category already exists
    $check_sql = "SELECT id FROM categories WHERE name = ?";
    if($stmt = mysqli_prepare($conn, $check_sql)){
        mysqli_stmt_bind_param($stmt, "s", $name);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) > 0){
                header("location: categories.php?error=Category already exists");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Insert category
    $sql = "INSERT INTO categories (name) VALUES (?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "s", $name);
        if(mysqli_stmt_execute($stmt)){
            header("location: categories.php?success=Category added successfully");
            exit();
        } else{
            header("location: categories.php?error=Something went wrong");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

// Process edit operation
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "edit"){
    $id = trim($_POST["id"]);
    $name = trim($_POST["name"]);
    
    if(empty($name)){
        header("location: categories.php?error=Please enter category name");
        exit();
    }
    
    // Check if category already exists
    $check_sql = "SELECT id FROM categories WHERE name = ? AND id != ?";
    if($stmt = mysqli_prepare($conn, $check_sql)){
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) > 0){
                header("location: categories.php?error=Category already exists");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    // Update category
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        if(mysqli_stmt_execute($stmt)){
            header("location: categories.php?success=Category updated successfully");
            exit();
        } else{
            header("location: categories.php?error=Something went wrong");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all categories
$categories = [];
$sql = "SELECT c.*, COUNT(p.id) as product_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.name";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin Panel</title>
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
                    <h2>Categories Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
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

                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Categories Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Products Count</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo $row['product_count']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary"
                                                        onclick="editCategory(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')"
                                                        data-bs-toggle="tooltip"
                                                        title="Edit Category">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($row['product_count'] == 0): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger"
                                                        onclick="deleteCategory(<?php echo $row['id']; ?>)"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Category">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
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

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="categoryName" name="name" required>
                        </div>
                        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="update_category.php" method="POST">
                        <input type="hidden" name="category_id" id="editCategoryId">
                        <div class="mb-3">
                            <label for="editCategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Category</button>
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

        function editCategory(id, name) {
            document.getElementById('editCategoryId').value = id;
            document.getElementById('editCategoryName').value = name;
            var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editModal.show();
        }

        function deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                window.location.href = 'delete_category.php?id=' + categoryId;
            }
        }
    </script>
</body>
</html> 