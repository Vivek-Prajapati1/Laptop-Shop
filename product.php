<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Get product ID from URL
if (!isset($_GET['id'])) {
    header('Location: shop.php');
    exit();
}

$product_id = intval($_GET['id']);

// Get product details with category
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = ? AND p.status = 'active'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: shop.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Get related products
$related_query = "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE p.category_id = ? AND p.id != ? AND p.status = 'active' 
                 LIMIT 3";
$stmt = mysqli_prepare($conn, $related_query);
mysqli_stmt_bind_param($stmt, "ii", $product['category_id'], $product_id);
mysqli_stmt_execute($stmt);
$related_result = mysqli_stmt_get_result($stmt);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="index.php" class="text-gray-400 hover:text-blue-600">
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="shop.php" class="text-gray-400 hover:text-blue-600">Shop</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-400"><?php echo htmlspecialchars($product['name']); ?></span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
            <!-- Product Image -->
            <div class="relative">
                <?php
                $image_path = "uploads/" . htmlspecialchars($product['image']);
                $default_image = "uploads/default-laptop.svg";
                
                if (!empty($product['image']) && file_exists($image_path) && is_readable($image_path)) {
                    $ext = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
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
                     class="w-full h-auto rounded-lg shadow-md">
                
                <?php if ($product['stock'] <= 5): ?>
                    <div class="absolute top-4 right-4">
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Low Stock
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <span class="text-sm font-medium text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>
                <div class="text-3xl font-bold text-gray-900">
                    ₹<?php echo number_format($product['price'], 0); ?>
                </div>
                <div class="prose max-w-none text-gray-600">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <!-- Stock Status -->
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600">Availability:</span>
                    <?php if ($product['stock'] > 0): ?>
                        <span class="text-green-600 font-medium">In Stock (<?php echo $product['stock']; ?> units)</span>
                    <?php else: ?>
                        <span class="text-red-600 font-medium">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4">
                <div class="flex flex-col md:flex-row gap-4 mt-6">
    <!-- Add to Cart Form -->
    <form method="POST" action="add_to_cart.php" class="flex-1 bg-white p-4 rounded-lg shadow-md">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

        <div class="mb-4">
            <label class="flex items-center text-sm font-medium text-gray-700">
                Quantity:
                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>"
                       class="ml-2 w-20 px-2 py-1 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </label>
        </div>

        <button type="submit" 
                class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-sm">
            Add to Cart
        </button>
    </form>

    <!-- Buy Now Form -->
    <form method="POST" action="buy_now.php" class="flex-1 bg-white p-4 rounded-lg shadow-md flex items-end">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="quantity" value="1">

        <button type="submit" 
                class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold shadow-sm">
            Buy Now
        </button>
    </form>
</div>

                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (mysqli_num_rows($related_result) > 0): ?>
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php while($related = mysqli_fetch_assoc($related_result)): ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <a href="product.php?id=<?php echo $related['id']; ?>" class="block">
                        <?php
                        $related_image = "uploads/" . htmlspecialchars($related['image']);
                        if (!empty($related['image']) && file_exists($related_image) && is_readable($related_image)) {
                            $ext = strtolower(pathinfo($related_image, PATHINFO_EXTENSION));
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                $display_image = $related_image;
                            } else {
                                $display_image = $default_image;
                            }
                        } else {
                            $display_image = $default_image;
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($display_image); ?>" 
                             alt="<?php echo htmlspecialchars($related['name']); ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <?php echo htmlspecialchars($related['name']); ?>
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-gray-900">
                                    ₹<?php echo number_format($related['price'], 0); ?>
                                </span>
                                <span class="text-sm text-blue-600 hover:text-blue-700">View Details →</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 