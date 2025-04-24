<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$name = $description = $price = $stock = $category_id = $image = "";
$name_err = $description_err = $price_err = $stock_err = $category_err = $image_err = "";

// Image handling and compression function
function compressImage($source, $destination, $quality) {
    if (!extension_loaded('gd')) {
        // If GD is not available, just copy the file
        return copy($source, $destination);
    }
    
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    }
    
    // Save the compressed image
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg($image, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        imagepng($image, $destination, 9);
    }
    
    if (isset($image)) {
        imagedestroy($image);
    }
    
    return $destination;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter product name.";
    } else{
        $name = trim($_POST["name"]);
    }
    
    // Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter product description.";
    } else{
        $description = trim($_POST["description"]);
    }
    
    // Validate price
    if(empty(trim($_POST["price"]))){
        $price_err = "Please enter product price.";
    } elseif(!is_numeric(trim($_POST["price"])) || trim($_POST["price"]) <= 0){
        $price_err = "Please enter a valid positive price.";
    } else{
        $price = trim($_POST["price"]);
    }
    
    // Validate stock
    if(empty(trim($_POST["stock"]))){
        $stock_err = "Please enter product stock.";
    } elseif(!is_numeric(trim($_POST["stock"])) || trim($_POST["stock"]) < 0){
        $stock_err = "Please enter a valid non-negative stock quantity.";
    } else{
        $stock = trim($_POST["stock"]);
    }
    
    // Validate category
    if(empty(trim($_POST["category_id"]))){
        $category_err = "Please select product category.";
    } else{
        // Verify category exists
        $cat_check_sql = "SELECT id FROM categories WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $cat_check_sql)){
            $param_category_id = trim($_POST["category_id"]);
            mysqli_stmt_bind_param($stmt, "i", $param_category_id);
            if(mysqli_stmt_execute($stmt)){
                $cat_result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($cat_result) == 0){
                    $category_err = "Selected category does not exist.";
                } else {
                    $category_id = $param_category_id;
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Handle image upload with compression
    if(empty($_FILES["image"]["name"])){
        $image_err = "Please select an image for the product.";
    } else {
        $target_dir = dirname(dirname(__FILE__)) . "/uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            $image_err = "File is not an image.";
        }
        // Check file size (5MB max)
        elseif ($_FILES["image"]["size"] > 5000000) {
            $image_err = "File is too large. Maximum size is 5MB.";
        }
        // Allow certain file formats
        elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $image_err = "Only JPG, JPEG & PNG files are allowed.";
        }
        // If everything is ok, try to upload file
        else {
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                if (!extension_loaded('gd')) {
                    // If GD is not available, just use the uploaded file
                    $image = $new_filename;
                } else {
                    // Try to compress the image
                    $compressed_file = compressImage($target_file, $target_file, 75);
                    if($compressed_file){
                        $image = $new_filename;
                    } else {
                        $image_err = "Error compressing image.";
                    }
                }
            } else {
                $image_err = "Error uploading file.";
            }
        }
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($description_err) && empty($price_err) && 
       empty($stock_err) && empty($category_err) && empty($image_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssdiis", $name, $description, $price, $stock, $category_id, $image);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to products page
                header("location: products.php?success=Product added successfully");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
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
                        <a href="products.php" class="nav-link active">
                            <i class="bi bi-laptop"></i> Products
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="orders.php" class="nav-link">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="users.php" class="nav-link">
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
                    <h2>Add New Product</h2>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Products
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Product Name</label>
                                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                        <span class="invalid-feedback"><?php echo $name_err; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Category</label>
                                        <select name="category_id" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>">
                                            <option value="">Select Category</option>
                                            <?php
                                            // Fetch categories from database
                                            $cat_sql = "SELECT * FROM categories ORDER BY name";
                                            $cat_result = mysqli_query($conn, $cat_sql);
                                            while($cat = mysqli_fetch_assoc($cat_result)) {
                                                $selected = ($category_id == $cat['id']) ? 'selected' : '';
                                                echo "<option value='" . $cat['id'] . "' " . $selected . ">" . htmlspecialchars($cat['name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <span class="invalid-feedback"><?php echo $category_err; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                                <span class="invalid-feedback"><?php echo $description_err; ?></span>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Price (₹)</label>
                                        <input type="number" name="price" step="1" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                                        <span class="invalid-feedback"><?php echo $price_err; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control <?php echo (!empty($stock_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $stock; ?>">
                                        <span class="invalid-feedback"><?php echo $stock_err; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Product Image</label>
                                <input type="file" name="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                                <span class="invalid-feedback"><?php echo $image_err; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary" value="Add Product">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 