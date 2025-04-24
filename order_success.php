<?php
session_start();
require_once 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get order details with updated status
$query = "SELECT o.*, od.quantity, od.price as item_price, p.name, p.description 
          FROM orders o 
          JOIN order_details od ON o.id = od.order_id 
          JOIN products p ON od.product_id = p.id 
          WHERE o.id = ? AND o.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$order = mysqli_fetch_assoc($result);
$total = $order['total_amount'];
mysqli_data_seek($result, 0);

// Include header after all potential redirects
require_once 'includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-black rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <div class="text-green-500 text-6xl mb-4">✓</div>
            <h1 class="text-3xl font-bold mb-2 text-white">Order Confirmed!</h1>
            <p class="text-gray-400">Thank you for your purchase. Your order has been received.</p>
        </div>

        <div class="border-t border-b border-gray-700 py-6 mb-6">
            <div class="grid grid-cols-2 gap-4 text-white">
                <div>
                    <p class="text-gray-400">Order Number</p>
                    <p class="font-semibold">#<?php echo $order_id; ?></p>
                </div>
                <div>
                    <p class="text-gray-400">Date</p>
                    <p class="font-semibold"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                </div>
                <div>
                    <p class="text-gray-400">Total Amount</p>
                    <p class="font-semibold">₹<?php echo number_format($total, 0); ?></p>
                </div>
                <div id="status-section">
                    <p class="text-gray-400">Status</p>
                    <p class="font-semibold flex items-center gap-2">
                        <span id="status-dot" class="w-3 h-3 rounded-full inline-block
                            <?php 
                            $status_color = match($order['status']) {
                                'delivered' => 'bg-green-500',
                                'processing' => 'bg-blue-500',
                                'shipped' => 'bg-indigo-500',
                                'cancelled' => 'bg-red-500',
                                'confirmed' => 'bg-blue-500',
                                default => 'bg-yellow-500'
                            };
                            echo $status_color;
                            ?>"></span>
                        <span id="status-text" class="text-white">
                            <?php 
                            // Show order progress stages
                            $stages = [
                                'confirmed' => 'Order Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled'
                            ];
                            echo $stages[$order['status']] ?? ucfirst($order['status']); 
                            ?>
                        </span>
                    </p>
                    
                    <!-- Order Progress Bar -->
                    <div class="mt-4 relative" id="progress-bar">
                        <div class="flex justify-between mb-2">
                            <?php
                            $progress_stages = ['confirmed', 'processing', 'shipped', 'delivered'];
                            $current_stage = array_search($order['status'], $progress_stages);
                            if ($current_stage === false) $current_stage = -1;
                            
                            foreach ($progress_stages as $index => $stage): 
                                $is_active = $index <= $current_stage;
                                $dot_color = $is_active ? $status_color : 'bg-gray-600';
                            ?>
                                <div class="flex flex-col items-center">
                                    <div class="w-4 h-4 rounded-full progress-dot <?php echo $dot_color; ?>" data-stage="<?php echo $stage; ?>"></div>
                                    <span class="text-xs mt-1 text-gray-400">
                                        <?php echo ucfirst($stage); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="absolute top-2 left-0 h-0.5 bg-gray-600 w-full -z-10"></div>
                        <div id="progress-line" class="absolute top-2 left-0 h-0.5 <?php echo $status_color; ?> transition-all" 
                             style="width: <?php echo ($current_stage >= 0 ? ($current_stage / (count($progress_stages) - 1)) * 100 : 0); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4 text-white">Order Details</h2>
            <div class="space-y-4">
                <?php while ($item = mysqli_fetch_assoc($result)): ?>
                    <div class="flex justify-between items-center text-white">
                        <div>
                            <h3 class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-400"><?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?></p>
                        </div>
                        <div class="text-right">
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p class="font-semibold">₹<?php echo number_format($item['item_price'] * $item['quantity'], 0); ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="text-center">
            <a href="shop.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
function updateOrderStatus() {
    fetch(`check_order_status.php?id=<?php echo $order_id; ?>`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update status text and dot color
                const statusDot = document.getElementById('status-dot');
                const statusText = document.getElementById('status-text');
                const progressLine = document.getElementById('progress-line');
                const progressDots = document.querySelectorAll('.progress-dot');
                
                // Remove old color classes
                statusDot.className = 'w-3 h-3 rounded-full inline-block ' + data.status_color;
                progressLine.className = 'absolute top-2 left-0 h-0.5 ' + data.status_color + ' transition-all';
                
                // Update progress width
                const stages = ['confirmed', 'processing', 'shipped', 'delivered'];
                const currentStage = stages.indexOf(data.status);
                if (currentStage >= 0) {
                    const progress = (currentStage / (stages.length - 1)) * 100;
                    progressLine.style.width = progress + '%';
                    
                    // Update progress dots
                    progressDots.forEach((dot, index) => {
                        if (index <= currentStage) {
                            dot.className = 'w-4 h-4 rounded-full progress-dot ' + data.status_color;
                        } else {
                            dot.className = 'w-4 h-4 rounded-full progress-dot bg-gray-600';
                        }
                    });
                }
                
                // Update status text
                statusText.textContent = data.status_text;
                
                // If order is cancelled, hide progress bar
                if (data.status === 'cancelled') {
                    document.getElementById('progress-bar').style.display = 'none';
                } else {
                    document.getElementById('progress-bar').style.display = 'block';
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update status every 5 seconds
setInterval(updateOrderStatus, 5000);

// Initial update
updateOrderStatus();
</script>

<?php require_once 'includes/footer.php'; ?> 