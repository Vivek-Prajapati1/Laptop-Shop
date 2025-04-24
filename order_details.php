<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: user_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['id']);

// Get order details with products
$query = "SELECT o.*, od.quantity, od.price as item_price, p.name, p.description, p.image
          FROM orders o
          JOIN order_details od ON o.id = od.order_id
          JOIN products p ON od.product_id = p.id
          WHERE o.id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: user_dashboard.php');
    exit();
}

$order = mysqli_fetch_assoc($result);
mysqli_data_seek($result, 0);

// Calculate total
$total = 0;
$items = [];
while ($item = mysqli_fetch_assoc($result)) {
    $item['subtotal'] = $item['item_price'] * $item['quantity'];
    $total += $item['subtotal'];
    $items[] = $item;
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-black rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-white">Order Details</h1>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                <?php 
                $status_color = match($order['status']) {
                    'delivered' => 'bg-green-500',
                    'processing' => 'bg-blue-500',
                    'shipped' => 'bg-indigo-500',
                    'cancelled' => 'bg-red-500',
                    'confirmed' => 'bg-blue-500',
                    default => 'bg-yellow-500'
                };
                echo $status_color . ' text-white';
                ?>">
                <?php 
                $status_text = match($order['status']) {
                    'delivered' => 'Delivered',
                    'processing' => 'Processing',
                    'shipped' => 'Shipped',
                    'cancelled' => 'Cancelled',
                    'confirmed' => 'Confirmed',
                    default => 'Pending'
                };
                echo $status_text;
                ?>
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold mb-2 text-white">Order Information</h2>
                <div class="space-y-2 text-gray-400">
                    <p>Order ID: <span class="text-white">#<?php echo $order_id; ?></span></p>
                    <p>Date: <span class="text-white"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span></p>
                    <p>Total Amount: <span class="text-white">₹<?php echo number_format($total, 0); ?></span></p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700 pt-6">
            <h2 class="text-lg font-semibold mb-4 text-white">Order Items</h2>
            <div class="space-y-4">
                <?php foreach ($items as $item): ?>
                <div class="flex items-center justify-between p-4 bg-gray-900 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <?php
                        // Handle image path
                        $image_path = !empty($item['image']) ? "uploads/" . $item['image'] : "uploads/default-laptop.svg";
                        ?>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="w-16 h-16 object-cover rounded"
                             onerror="this.src='uploads/default-laptop.svg'">
                        <div>
                            <h3 class="font-semibold text-white"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-sm text-gray-400"><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?></p>
                            <p class="text-sm text-gray-400">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-white">₹<?php echo number_format($item['subtotal'], 0); ?></p>
                        <p class="text-sm text-gray-400">₹<?php echo number_format($item['item_price'], 0); ?> each</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-6 text-right">
            <p class="text-lg font-semibold text-white">Total: ₹<?php echo number_format($total, 0); ?></p>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="user_dashboard.php" class="text-blue-500 hover:text-blue-600">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 