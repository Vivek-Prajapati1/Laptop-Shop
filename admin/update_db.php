<?php
require_once '../includes/db.php';

// Check if status column exists
$check_column = "SHOW COLUMNS FROM products LIKE 'status'";
$result = mysqli_query($conn, $check_column);

if (mysqli_num_rows($result) == 0) {
    // Add status column if it doesn't exist
    $add_column = "ALTER TABLE products ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'";
    if (mysqli_query($conn, $add_column)) {
        echo "Status column added successfully!";
    } else {
        echo "Error adding status column: " . mysqli_error($conn);
    }
} else {
    echo "Status column already exists!";
}

// Set all existing products to active
$update_status = "UPDATE products SET status = 'active' WHERE status IS NULL";
mysqli_query($conn, $update_status);

echo "<br><br><a href='products.php'>Return to Products</a>";
?> 