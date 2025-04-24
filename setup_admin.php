<?php
require_once 'includes/db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare an insert statement
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Admin account created successfully!";
            } else {
                $error = "Something went wrong. Please try again later.";
            }
            
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
    <title>Setup Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .wrapper {
            width: 360px;
            padding: 20px;
            margin: 100px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2 class="text-center mb-4">Create Admin Account</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>    
            <div class="form-group mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group text-center">
                <input type="submit" class="btn btn-primary" value="Create Admin Account">
            </div>
        </form>
    </div>
</body>
</html> 