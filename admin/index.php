<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopShop Admin - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-gray-800 p-8 rounded-lg shadow-lg w-96">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">LaptopShop Admin</h1>
                <p class="text-gray-400">Please login to continue</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                Invalid credentials. Please try again.
            </div>
            <?php endif; ?>

            <form action="login_process.php" method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-gray-300 mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500">
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                    Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>