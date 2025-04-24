<?php
require_once 'includes/db.php';

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Columns to add
$columns = [
    'city' => 'VARCHAR(100)',
    'state' => 'VARCHAR(100)',
    'zip' => 'VARCHAR(20)'
];

$success = true;
$messages = [];

// Add each column if it doesn't exist
foreach ($columns as $column => $type) {
    if (!columnExists($conn, 'users', $column)) {
        $query = "ALTER TABLE users ADD COLUMN `$column` $type DEFAULT NULL";
        if (!mysqli_query($conn, $query)) {
            $success = false;
            $messages[] = "Error adding column $column: " . mysqli_error($conn);
        } else {
            $messages[] = "Successfully added column: $column";
        }
    } else {
        $messages[] = "Column $column already exists";
    }
}

// Output results
echo "<div style='font-family: Arial, sans-serif; padding: 20px;'>";
if ($success) {
    echo "<h2 style='color: green;'>Database updated successfully!</h2>";
} else {
    echo "<h2 style='color: red;'>Some errors occurred:</h2>";
}

echo "<ul>";
foreach ($messages as $message) {
    echo "<li>" . htmlspecialchars($message) . "</li>";
}
echo "</ul>";

if ($success) {
    echo "<p>You can now return to the <a href='edit_profile.php'>Edit Profile</a> page.</p>";
}
echo "</div>";

mysqli_close($conn); 