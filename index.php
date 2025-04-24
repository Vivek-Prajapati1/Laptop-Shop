<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Get featured products (only active AND featured ones)
$featured_query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.status = 'active' AND p.featured = 1 
                  ORDER BY p.id DESC LIMIT 8";
$featured_result = mysqli_query($conn, $featured_query);

// Add featured column if it doesn't exist
$check_column = "SHOW COLUMNS FROM products LIKE 'featured'";
$result = mysqli_query($conn, $check_column);
if (mysqli_num_rows($result) == 0) {
    mysqli_query($conn, "ALTER TABLE products ADD COLUMN featured TINYINT(1) NOT NULL DEFAULT 0");
}
?>

<!-- Main Hero Section -->
<div class="hero-section relative min-h-screen overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900">
    <!-- Animated Background -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="absolute inset-0 bg-gradient-radial from-blue-500/30 to-transparent"></div>
    </div>

    <!-- Floating Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="floating-element absolute top-1/4 left-1/4 w-32 h-32 bg-blue-400/10 rounded-full blur-xl"></div>
        <div class="floating-element absolute top-1/3 right-1/4 w-40 h-40 bg-blue-300/10 rounded-full blur-xl" style="animation-delay: -2s;"></div>
        <div class="floating-element absolute bottom-1/4 left-1/3 w-24 h-24 bg-yellow-400/10 rounded-full blur-xl" style="animation-delay: -4s;"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Text Content -->
            <div class="text-center lg:text-left fade-in-up">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
                    Find Your Perfect 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-200">
                        Laptop
                    </span>
                </h1>
                <p class="text-xl text-gray-300 mb-8 max-w-xl mx-auto lg:mx-0">
                    Discover our wide selection of laptops from top brands. Get the best prices and exceptional customer service.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="shop.php" class="glass-button">
                        Shop Now
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                    <a href="#featured" class="outline-button">
                        View Featured
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Image Section -->
            <div class="relative fade-in-right">
                <div class="relative z-10 transform hover:scale-105 transition-transform duration-500">
                    <div class="glass-card p-6 rounded-2xl">
                        <img src="uploads/laptop_hub_bg.webp" alt="Featured Laptop" class="w-full h-auto rounded-lg">
                    </div>
                </div>
                <!-- Decorative Elements -->
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-yellow-400/20 rounded-full blur-xl"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-blue-400/20 rounded-full blur-xl"></div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<div id="featured" class="relative bg-gray-50 overflow-hidden">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6">
        <!-- Section Header -->
        <div class="text-center mb-12 fade-in-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                Featured Products
            </h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full mb-4"></div>
            <p class="text-lg text-gray-600">Explore our selection of premium laptops</p>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(mysqli_num_rows($featured_result) > 0): ?>
                <?php while($product = mysqli_fetch_assoc($featured_result)): ?>
                    <div class="product-card fade-in-up" style="animation-delay: <?php echo mysqli_num_rows($featured_result) * 0.1; ?>s">
                        <div class="relative group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300">
                            <!-- Image Container -->
                            <div class="relative overflow-hidden rounded-t-xl aspect-w-16 aspect-h-12">
                                <?php
                                $image_path = "uploads/" . htmlspecialchars($product['image']);
                                $default_image = "uploads/default-laptop.svg";
                                
                                // Check if image exists and is readable
                                if (!empty($product['image']) && file_exists($image_path) && is_readable($image_path)) {
                                    // Get file extension
                                    $ext = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
                                    // Check if it's an allowed image type
                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                        $display_image = $image_path;
                                    } else {
                                        $display_image = $default_image;
                                    }
                                } else {
                                    $display_image = $default_image;
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($display_image); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                                     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($default_image); ?>';">
                                
                                <!-- Overlay on Hover -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="absolute bottom-4 left-4 right-4">
                                        <a href="product.php?id=<?php echo $product['id']; ?>" 
                                           class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600/90 hover:bg-blue-700 rounded-lg backdrop-blur-sm transition-all duration-300">
                                            View Details
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <!-- Stock Badge -->
                                <?php if ($product['stock'] <= 5): ?>
                                    <div class="absolute top-4 right-4 z-10">
                                        <span class="stock-badge">
                                            <svg class="w-3 h-3 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            Low Stock
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="p-6">
                                <!-- Category -->
                                <div class="flex items-center mb-3">
                                    <span class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-full">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>

                                <!-- Price and Actions -->
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-500 mb-1">Price</span>
                                        <span class="text-2xl font-bold text-gray-900">
                                            ₹<?php echo number_format($product['price'], 0); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="flex items-center space-x-2">
                                        <form method="POST" action="add_to_cart.php" class="inline">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No featured products available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">
                Why Choose Us
            </h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto rounded-full mb-4"></div>
            <p class="text-lg text-gray-600">Experience the best laptop shopping experience</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 ">
            <!-- Feature 1 -->
            <div class="feature-card fade-in-up border-2 border-black-100" style="animation-delay: 0.2s " >
                <div class="feature-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4 ">Quality Guaranteed</h3>
                <p class="text-gray-600">All our products are genuine and come with manufacturer warranty</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card fade-in-up border-2 border-black-100" style="animation-delay: 0.4s">
                <div class="feature-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Best Prices</h3>
                <p class="text-gray-600">We offer competitive prices and regular discounts</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card fade-in-up border-2 border-black-100" style="animation-delay: 0.6s">
                <div class="feature-icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Fast Shipping</h3>
                <p class="text-gray-600">Quick delivery with real-time tracking</p>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="relative bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
    </div>
    <div class="relative max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="text-center lg:text-left mb-8 lg:mb-0">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Ready to dive in?</span>
                    <span class="block text-yellow-400">Start shopping today.</span>
                </h2>
                <p class="mt-4 text-lg text-gray-300 max-w-lg">
                    Join thousands of satisfied customers who have found their perfect laptop with us.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="shop.php" class="glass-button">
                    Get Started
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="#featured" class="outline-button">
                    Learn More
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.fade-in-up {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
}

.fade-in-right {
    opacity: 0;
    animation: fadeInRight 0.6s ease-out forwards;
}

.floating-element {
    animation: float 6s ease-in-out infinite;
}

/* Glass Effect */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
}

/* Buttons */
.glass-button {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    color: #1a56db;
    font-weight: 600;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.glass-button:hover {
    background: #fbbf24;
    color: white;
    transform: translateY(-2px);
}

.outline-button {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: transparent;
    color: white;
    font-weight: 600;
    border-radius: 0.5rem;
    border: 2px solid rgba(255, 255, 255, 0.6);
    transition: all 0.3s ease;
}

.outline-button:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    transform: translateY(-2px);
}

/* Product Cards Enhanced */
.product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

.product-card:hover {
    transform: translateY(-8px);
}

.stock-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: rgba(239, 68, 68, 0.95);
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 9999px;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
}

/* Line Clamp for Title */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Aspect Ratio Container */
.aspect-w-16 {
    position: relative;
    padding-bottom: 75%;
}

.aspect-w-16 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

/* Enhanced Hover Effects */
.group:hover .group-hover\:translate-y-0 {
    transform: translateY(0);
}

/* Card Shadow Effects */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.hover\:shadow-2xl:hover {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Feature Cards */
.feature-card {
    padding: 2rem;
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.feature-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 3rem;
    height: 3rem;
    background: #1a56db;
    color: white;
    border-radius: 0.75rem;
    margin-bottom: 1rem;
}

/* Background Pattern */
.bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Gradient Background */
.bg-gradient-radial {
    background: radial-gradient(circle at center, var(--tw-gradient-from), var(--tw-gradient-to));
}
</style>

<?php require_once 'includes/footer.php'; ?> 