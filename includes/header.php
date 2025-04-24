<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopShop - Your One-Stop Laptop Store</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        // Tailwind Configuration
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f6ff',
                            100: '#e0ecff',
                            200: '#c0d8ff',
                            300: '#91bbff',
                            400: '#609bff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-down': 'slideDown 0.5s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-100%)' },
                            '100%': { transform: 'translateY(0)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(100%)' },
                            '100%': { transform: 'translateY(0)' },
                        },
                    },
                },
            },
        }

        // Check for dark mode preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --header-height: 4rem;
        }

        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        /* Smooth transitions for dark mode */
        .dark body {
            background-color: #111827;
            color: #f3f4f6;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Navbar animations */
        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: currentColor;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Cart badge animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .cart-badge {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-900 transition-colors duration-200">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and primary nav -->
                <div class="flex">
                    <a href="index.php" class="flex items-center flex-shrink-0 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors duration-200">
                        <i class="fas fa-laptop text-2xl mr-2"></i>
                        <span class="font-semibold text-xl">LaptopShop</span>
                    </a>
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                        <a href="index.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary-600 dark:text-primary-400' : ''; ?>">
                            <i class="fas fa-home mr-2"></i>
                            Home
                        </a>
                        <a href="shop.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'text-primary-600 dark:text-primary-400' : ''; ?>">
                            <i class="fas fa-shopping-bag mr-2"></i>
                            Shop
                        </a>
                        <a href="about.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-primary-600 dark:text-primary-400' : ''; ?>">
                            <i class="fas fa-info-circle mr-2"></i>
                            About
                        </a>
                        <a href="contact.php" class="nav-link inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'text-primary-600 dark:text-primary-400' : ''; ?>">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact
                        </a>
                    </div>
                </div>

                <!-- Secondary nav -->
                <div class="hidden sm:flex sm:items-center sm:space-x-3">
                    <!-- Dark mode toggle -->
                    <button id="theme-toggle" class="p-2 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg transition-colors duration-200">
                        <i class="fas fa-sun hidden dark:inline-block"></i>
                        <i class="fas fa-moon inline-block dark:hidden"></i>
                    </button>

                    <!-- Cart -->
                    <a href="cart.php" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if(!empty($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-badge absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <!-- User menu -->
                    <?php if(!empty($_SESSION['user_id'])): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-200">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="text-sm font-medium"><?php echo !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?></span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                            <a href="user_dashboard.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                            <a href="edit_profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Edit Profile</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center space-x-2">
                        <a href="login.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors duration-200">
                            Login
                        </a>
                        <a href="register.php" class="inline-flex items-center px-4 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 border border-primary-600 dark:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900 rounded-lg transition-colors duration-200">
                            Register
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" class="mobile-menu-button inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors duration-200" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="index.php" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900' : ''; ?>">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
                <a href="shop.php" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900' : ''; ?>">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Shop
                </a>
                <a href="about.php" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900' : ''; ?>">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>
                <a href="contact.php" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900' : ''; ?>">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                <?php if(!empty($_SESSION['user_id'])): ?>
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle text-2xl text-gray-500 dark:text-gray-400"></i>
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800 dark:text-gray-200"><?php echo !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?></div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="user_dashboard.php" class="block px-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                        Dashboard
                    </a>
                    <a href="edit_profile.php" class="block px-4 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                        Edit Profile
                    </a>
                    <a href="logout.php" class="block px-4 py-2 text-base font-medium text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                        Logout
                    </a>
                </div>
                <?php else: ?>
                <div class="px-4 space-y-2">
                    <a href="login.php" class="block w-full px-4 py-2 text-center text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors duration-200">
                        Login
                    </a>
                    <a href="register.php" class="block w-full px-4 py-2 text-center text-sm font-medium text-primary-600 dark:text-primary-400 border border-primary-600 dark:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900 rounded-lg transition-colors duration-200">
                        Register
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="pt-16"> <!-- Add padding top to account for fixed navbar -->

    <!-- Alpine.js for dropdowns -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Dark mode toggle script -->
    <script>
        // Dark mode toggle
        document.getElementById('theme-toggle').addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark')
                localStorage.theme = 'light'
            } else {
                document.documentElement.classList.add('dark')
                localStorage.theme = 'dark'
            }
        });

        // Mobile menu toggle
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>