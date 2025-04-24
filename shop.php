<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Get selected category from URL parameter
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Fetch all categories
$categories_query = "SELECT * FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_query);

// Prepare products query based on category selection
$products_query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.status = 'active'";

if ($selected_category !== 'all' && is_numeric($selected_category)) {
    $products_query .= " AND p.category_id = " . intval($selected_category);
}

$products_query .= " ORDER BY p.name ASC";
$products_result = mysqli_query($conn, $products_query);
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-white mb-4">Shop Laptops</h1>
            <p class="text-xl text-gray-200">Find the perfect laptop for your needs</p>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 ">
    <!-- Search and Filter Section -->
    <div class="flex flex-col md:flex-row gap-6 mb-8">
        <!-- Search Bar -->
        <div class="flex-1 ">
            <div class="relative">
                <input type="text" 
                       id="search" 
                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-black-300 focus:ring-2 focus:ring-blue-600 text-black "
                       placeholder="Search laptops...">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Category Sidebar -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Categories</h2>
                <div class="space-y-2">
                    <a href="?category=all" 
                       class="category-link <?php echo $selected_category === 'all' ? 'active' : ''; ?>">
                        All Categories
                        <span class="ml-auto bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                            <?php echo mysqli_num_rows($products_result); ?>
                        </span>
                    </a>
                    <?php while($category = mysqli_fetch_assoc($categories_result)): 
                        // Count products in this category
                        $count_query = "SELECT COUNT(*) as count FROM products WHERE category_id = " . $category['id'];
                        $count_result = mysqli_query($conn, $count_query);
                        $count = mysqli_fetch_assoc($count_result)['count'];
                    ?>
                        <a href="?category=<?php echo $category['id']; ?>" 
                           class="category-link <?php echo $selected_category == $category['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                            <span class="ml-auto bg-gray-100 text-gray-600 px-2 py-1 rounded-full text-xs">
                                <?php echo $count; ?>
                            </span>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="lg:w-3/4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($product = mysqli_fetch_assoc($products_result)): ?>
                    <div class="product-card">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <!-- Product Image with Link -->
                            <a href="product.php?id=<?php echo $product['id']; ?>" class="block relative aspect-w-16 aspect-h-12">
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
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                     onerror="this.onerror=null; this.src='<?php echo htmlspecialchars($default_image); ?>';">
                                
                                <?php if ($product['stock'] <= 5): ?>
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-medium">
                                            Low Stock
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <div class="p-4">
                                <div class="mb-2">
                                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                </div>
                                <!-- Product Name with Link -->
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="block">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600 transition-colors">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h3>
                                </a>
                                <!-- Short Description -->
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    <?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?>
                                </p>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-2xl font-bold text-gray-900">₹<?php echo number_format($product['price'], 0); ?></span>
                                </div>
                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <form method="POST" action="add_to_cart.php" class="flex-1">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" 
                                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                            Add to Cart
                                        </button>
                                    </form>
                                    <form method="POST" action="buy_now.php" class="flex-1">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" 
                                                class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                            Buy Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<style>
.category-link {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: #4B5563;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.category-link:hover {
    background-color: #F3F4F6;
    color: #2563EB;
}

.category-link.active {
    background-color: #EFF6FF;
    color: #2563EB;
    font-weight: 600;
}

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

.product-card {
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-4px);
}

/* Line clamp for description */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Sticky sidebar styles */
@media (min-width: 1024px) {
    .sticky {
        position: sticky;
        top: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const productCards = document.querySelectorAll('.product-card');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        productCards.forEach(card => {
            const productName = card.querySelector('h3').textContent.toLowerCase();
            const productCategory = card.querySelector('.text-blue-600').textContent.toLowerCase();

            if (productName.includes(searchTerm) || productCategory.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?> 