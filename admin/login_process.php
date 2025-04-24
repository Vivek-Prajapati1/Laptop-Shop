<?php
session_start();
require_once "../includes/config.php";

// Check if username and password are set
if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("location: index.php?error=1");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Validate input
if (empty($username) || empty($password)) {
    header("location: index.php?error=1");
    exit;
}

// Prepare a select statement
$sql = "SELECT id, username, password FROM admins WHERE username = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    
    // Set parameters
    $param_username = $username;
    
    // Attempt to execute the prepared statement
    if (mysqli_stmt_execute($stmt)) {
        // Store result
        mysqli_stmt_store_result($stmt);
        
        // Check if username exists
        if (mysqli_stmt_num_rows($stmt) == 1) {
            // Bind result variables
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
            
            if (mysqli_stmt_fetch($stmt)) {
                if (password_verify($password, $hashed_password)) {
                    // Password is correct, start a new session
                    session_start();
                    
                    // Store data in session variables
                    $_SESSION["admin_loggedin"] = true;
                    $_SESSION["admin_id"] = $id;
                    $_SESSION["admin_username"] = $username;
                    
                    // Redirect to dashboard
                    header("location: dashboard.php");
                    exit;
                } else {
                    // Password is not valid
                    header("location: index.php?error=1");
                    exit;
                }
            }
        } else {
            // Username doesn't exist
            header("location: index.php?error=1");
            exit;
        }
    } else {
        header("location: index.php?error=1");
        exit;
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($link);
?> 