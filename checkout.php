<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login to continue with checkout.";
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$total = 0;
$items = [];

// Check if this is a "Buy Now" checkout
if (isset($_GET['buy_now']) && isset($_SESSION['buy_now'])) {
    // Get product details for buy now
    $buy_now = $_SESSION['buy_now'];
    $product_query = "SELECT * FROM products WHERE id = ? AND status = 'active'";
    $stmt = mysqli_prepare($conn, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $buy_now['product_id']);
    mysqli_stmt_execute($stmt);
    $product_result = mysqli_stmt_get_result($stmt);
    
    if ($product = mysqli_fetch_assoc($product_result)) {
        $items[] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $buy_now['quantity'],
            'subtotal' => $product['price'] * $buy_now['quantity'],
            'image' => $product['image'],
            'stock' => $product['stock']
        ];
        $total = $product['price'] * $buy_now['quantity'];
    } else {
        $_SESSION['error'] = "Product not found or no longer available.";
        header('Location: shop.php');
        exit();
    }
} else {
    // Get cart items
    $cart_query = "SELECT c.*, p.name, p.price, p.image, p.stock 
                   FROM cart c
                   JOIN products p ON c.product_id = p.id
                   WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $cart_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $cart_result = mysqli_stmt_get_result($stmt);

    // Calculate total
    while ($item = mysqli_fetch_assoc($cart_result)) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $items[] = $item;
    }

    if (empty($items)) {
        header('Location: cart.php');
        exit();
    }
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate shipping information
    $shipping_name = trim($_POST['name'] ?? '');
    $shipping_email = trim($_POST['email'] ?? '');
    $shipping_address = trim($_POST['address'] ?? '');
    $shipping_city = trim($_POST['city'] ?? '');
    $shipping_state = trim($_POST['state'] ?? '');
    $shipping_zip = trim($_POST['zip'] ?? '');

    if (empty($shipping_name) || empty($shipping_email) || empty($shipping_address) || 
        empty($shipping_city) || empty($shipping_state) || empty($shipping_zip)) {
        $error = "Please fill in all shipping information.";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Create order
            $order_query = "INSERT INTO orders (user_id, total_amount, shipping_name, shipping_email, 
                                              shipping_address, shipping_city, shipping_state, shipping_zip, 
                                              status, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            $stmt = mysqli_prepare($conn, $order_query);
            mysqli_stmt_bind_param($stmt, "idssssss", $user_id, $total, $shipping_name, $shipping_email, 
                                 $shipping_address, $shipping_city, $shipping_state, $shipping_zip);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($conn);

            // Add order details
            $detail_query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $detail_query);
            
            foreach ($items as $item) {
                mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                mysqli_stmt_execute($stmt);

                // Update product stock
                $update_stock = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stock_stmt = mysqli_prepare($conn, $update_stock);
                mysqli_stmt_bind_param($stock_stmt, "ii", $item['quantity'], $item['product_id']);
                mysqli_stmt_execute($stock_stmt);
            }

            // If this was a cart checkout, clear the cart
            if (!isset($_GET['buy_now'])) {
                $clear_cart = "DELETE FROM cart WHERE user_id = ?";
                $stmt = mysqli_prepare($conn, $clear_cart);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
            } else {
                // Clear buy now session data
                unset($_SESSION['buy_now']);
            }

            // Commit transaction
            mysqli_commit($conn);
            
            // Redirect to success page
            header("Location: order_success.php?id=" . $order_id);
            exit();
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}

// Get user details for pre-filling the form
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);

// Include header after all potential redirects
require_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="text-3xl font-bold mb-8 text-white">Checkout</h1>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-gray-900 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-6 text-white">Order Summary</h2>
            <div class="space-y-4">
                <?php foreach ($items as $item): ?>
                    <div class="flex items-center space-x-4 py-4 border-b border-gray-700">
                        <div class="flex-shrink-0 w-20 h-20">
                            <?php
                            $image_path = "uploads/" . htmlspecialchars($item['image']);
                            $default_image = "uploads/default-laptop.svg";
                            $display_image = (!empty($item['image']) && file_exists($image_path)) ? $image_path : $default_image;
                            ?>
                            <img src="<?php echo htmlspecialchars($display_image); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-full h-full object-cover rounded">
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-white"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <?php if (isset($_GET['buy_now'])): ?>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="text-gray-400">Quantity:</label>
                                <input type="number" 
                                       id="quantity-selector"
                                       value="<?php echo $item['quantity']; ?>" 
                                       min="1" 
                                       max="<?php echo $item['stock']; ?>" 
                                       class="w-20 px-2 py-1 bg-gray-800 border border-gray-700 rounded text-white focus:outline-none focus:border-blue-500"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       onblur="validateQuantity(this)">
                                <span class="text-gray-400">(Max: <?php echo $item['stock']; ?>)</span>
                            </div>
                            <?php else: ?>
                            <p class="text-gray-400">
                                Quantity: <?php echo $item['quantity']; ?> × 
                                ₹<?php echo number_format($item['price'], 0); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white" id="subtotal-<?php echo $item['product_id']; ?>">
                                ₹<?php echo number_format($item['subtotal'], 0); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="border-t border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center text-lg font-bold text-white">
                        <span>Total Amount</span>
                        <span id="total-amount">₹<?php echo number_format($total, 0); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Information -->
        <div class="bg-gray-900 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-6 text-white">Shipping Information</h2>
            <form method="POST" action="" class="space-y-6" id="checkout-form">
                <input type="hidden" name="quantity" id="quantity-input" value="<?php echo $items[0]['quantity']; ?>">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Full Name</label>
                    <input type="text" id="name" name="name" required
                           value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                  shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                    <input type="email" id="email" name="email" required
                           value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                  shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-300">Shipping Address</label>
                    <textarea id="address" name="address" rows="3" required
                              class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                     shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="city" class="block text-sm font-medium text-gray-300">City</label>
                    <input type="text" id="city" name="city" required
                           value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>"
                           class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                  shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-300">State</label>
                        <input type="text" id="state" name="state" required
                               value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                      shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="zip" class="block text-sm font-medium text-gray-300">PIN Code</label>
                        <input type="text" id="zip" name="zip" required
                               value="<?php echo htmlspecialchars($user['pincode'] ?? ''); ?>"
                               class="mt-1 block w-full rounded-md bg-gray-800 border-gray-700 text-white 
                                      shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 
                               transition duration-300 font-semibold shadow-sm">
                    Place Order (<span id="button-total">₹<?php echo number_format($total, 0); ?></span>)
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function validateQuantity(input) {
    let quantity = parseInt(input.value);
    const maxStock = parseInt(input.max);
    const price = <?php echo $items[0]['price']; ?>;
    
    // Validate quantity
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
        input.value = 1;
    } else if (quantity > maxStock) {
        quantity = maxStock;
        input.value = maxStock;
    }
    
    updatePrice(quantity, price);
}

function updatePrice(quantity, price) {
    const subtotal = quantity * price;
    document.getElementById('subtotal-<?php echo $items[0]['product_id']; ?>').textContent = '₹' + subtotal.toLocaleString();
    document.getElementById('total-amount').textContent = '₹' + subtotal.toLocaleString();
    document.getElementById('button-total').textContent = '₹' + subtotal.toLocaleString();
    document.getElementById('quantity-input').value = quantity;
    
    // Update session via AJAX
    fetch('update_buy_now_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to update quantity:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Handle input events
const quantityInput = document.getElementById('quantity-selector');
if (quantityInput) {
    // Prevent form submission on Enter
    quantityInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            this.blur();
        }
    });

    // Update price only when user stops typing
    let timeout = null;
    quantityInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const input = this;
        timeout = setTimeout(function() {
            if (input.value) {
                validateQuantity(input);
            }
        }, 500);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?> 