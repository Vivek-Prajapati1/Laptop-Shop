<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Convert price to rupees
function convertToRupees($dollars) {
    return round($dollars * 83.50); // Current USD to INR rate
}

// Get user details
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Get recent orders with item count and total
$orders_query = "SELECT o.*, 
                COUNT(od.id) as item_count,
                SUM(od.price * od.quantity) as total_amount,
                GROUP_CONCAT(DISTINCT p.name) as product_names
                FROM orders o 
                LEFT JOIN order_details od ON o.id = od.order_id
                LEFT JOIN products p ON od.product_id = p.id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5";
$stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$orders_result = mysqli_stmt_get_result($stmt);
?>

<div class="container mx-auto px-4 py-10">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-white drop-shadow">My Dashboard</h1>
        <a href="edit_profile.php" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-blue-400 hover:text-white hover:bg-blue-600 transition-colors duration-300 rounded-lg border border-blue-500">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-8 mb-10">
        <!-- Personal Information -->
        <div class="bg-gray-900 rounded-2xl p-6 shadow-lg hover:shadow-blue-600 transition-shadow duration-300">
            <h2 class="text-2xl font-semibold mb-4 text-white">Personal Information</h2>
            <div class="space-y-3 text-white text-base">
                <p><span class="text-gray-400 font-medium">Name:</span> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><span class="text-gray-400 font-medium">Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><span class="text-gray-400 font-medium">Phone:</span> <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                <p><span class="text-gray-400 font-medium">Member Since:</span> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="bg-gray-900 rounded-2xl p-6 shadow-lg hover:shadow-purple-600 transition-shadow duration-300">
            <h2 class="text-2xl font-semibold mb-4 text-white">Shipping Address</h2>
            <p class="text-gray-300 text-base">
                <?php echo $user['address'] ? nl2br(htmlspecialchars($user['address'])) : 'No address provided'; ?>
            </p>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-gray-900 rounded-2xl p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-white">Recent Orders</h2>
            <a href="orders.php" class="text-blue-400 hover:text-white hover:underline transition">View All Orders</a>
        </div>

        <div class="overflow-x-auto rounded-lg">
            <table class="w-full text-left text-white">
                <thead class="bg-gray-800 text-sm text-gray-300 uppercase">
                    <tr>
                        <th class="py-3 px-4">Order ID</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Items</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                    <tr class="border-b border-gray-700 hover:bg-gray-800 transition-colors duration-200">
                        <td class="py-3 px-4">#<?php echo $order['id']; ?></td>
                        <td class="py-3 px-4"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                        <td class="py-3 px-4"><?php echo $order['item_count']; ?></td>
                        <td class="py-3 px-4">₹<?php echo number_format($order['total_amount'], 0); ?></td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php 
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
                        </td>
                        <td class="py-3 px-4">
                            <a href="order_details.php?id=<?php echo $order['id']; ?>" 
                               class="text-blue-400 hover:text-blue-200 hover:underline transition">View Details</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?> 