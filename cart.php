<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $update_query = "UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_query);
                mysqli_stmt_bind_param($update_stmt, "iii", $quantity, $cart_id, $user_id);
                mysqli_stmt_execute($update_stmt);
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $delete_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "ii", $cart_id, $user_id);
        mysqli_stmt_execute($delete_stmt);
    }
}

// Get cart items
$query = "SELECT c.cart_id, c.quantity, p.* 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$total = 0;
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['cart_message']); ?></span>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <form method="POST" action="">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($item = mysqli_fetch_assoc($result)): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                        ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="h-16 w-16 object-cover rounded"
                                             onerror="this.src='images/default-laptop.svg'">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₹<?php echo number_format($item['price'], 0); ?></div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                   <div class="text-sm text-gray-900">
                                   <?php echo htmlspecialchars($item['quantity']); ?>
                                   </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">₹<?php echo number_format($item_total, 0); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="" class="inline">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                        <button type="submit" 
                                                name="remove_item" 
                                                class="text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <div class="text-2xl font-bold">
                    Total: ₹<?php echo number_format($total, 0); ?>
                </div>
                <div class="space-x-4">
                    <button type="submit" 
                            name="update_cart" 
                            class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        Update Cart
                    </button>
                    <a href="checkout.php" 
                       class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="text-center py-12">
            <div class="text-gray-500 mb-4">Your cart is empty</div>
            <a href="shop.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 