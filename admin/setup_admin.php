<?php
require_once '../includes/db.php';

// Check if admins table exists, if not create it
$query = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $query)) {
    die("Error creating admins table: " . mysqli_error($conn));
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validate input
    if (empty($username)) {
        $errors[] = "Please enter a username.";
    }
    if (empty($password)) {
        $errors[] = "Please enter a password.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username already exists
    $query = "SELECT id FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $errors[] = "This username is already taken.";
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert admin
        $query = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Admin account created successfully! You can now login.";
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin Account - LaptopShop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Setup Admin Account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Create your first admin account
                </p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                    <div class="mt-4">
                        <a href="login.php" class="text-green-700 hover:text-green-900 font-semibold">
                            Go to Login Page →
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <form class="mt-8 space-y-6" method="POST" action="">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="username" class="sr-only">Username</label>
                            <input id="username" name="username" type="text" required 
                                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                   placeholder="Username">
                        </div>
                        <div>
                            <label for="password" class="sr-only">Password</label>
                            <input id="password" name="password" type="password" required 
                                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                   placeholder="Password">
                        </div>
                        <div>
                            <label for="confirm_password" class="sr-only">Confirm Password</label>
                            <input id="confirm_password" name="confirm_password" type="password" required 
                                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                                   placeholder="Confirm Password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Admin Account
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 