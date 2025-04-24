<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zip = trim($_POST['zip']);

    // Validate required fields
    if (empty($name) || empty($email)) {
        $error_message = "Name and email are required fields.";
    } else {
        // Check if email already exists for another user
        $email_check = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $email_check);
        mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "This email is already in use by another account.";
        } else {
            // Update user information
            $update_query = "UPDATE users SET 
                name = ?, 
                email = ?, 
                phone = ?,
                address = ?,
                city = ?,
                state = ?,
                zip = ?
                WHERE id = ?";

            if ($stmt = mysqli_prepare($conn, $update_query)) {
                mysqli_stmt_bind_param($stmt, "sssssssi", 
                    $name, $email, $phone, $address, $city, $state, $zip, $user_id);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Profile updated successfully!";
                    // Update session data
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                } else {
                    $error_message = "Error updating profile: " . mysqli_error($conn);
                }

                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Error preparing statement: " . mysqli_error($conn);
            }
        }
    }
}

// Fetch current user data
$user_query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-#1a1d25 rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Edit Profile</h2>
                <a href="user_dashboard.php" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="edit_profile.php">
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-white-700 mb-2" for="name">Name *</label>
                                <input type="text" id="name" name="name" 
                                    value="<?php echo htmlspecialchars($user['name']); ?>" 
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black"
                                    required>
                            </div>
                            <div>
                                <label class="block text-white-700 mb-2" for="email">Email *</label>
                                <input type="email" id="email" name="email" 
                                    value="<?php echo htmlspecialchars($user['email']); ?>" 
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black"
                                    required>
                            </div>
                            <div>
                                <label class="block text-white-700 mb-2" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" 
                                    value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 mb-2" for="address">Address</label>
                                <input type="text" id="address" name="address" 
                                    value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" 
                                    class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-gray-700 mb-2" for="city">City</label>
                                    <input type="text" id="city" name="city" 
                                        value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" 
                                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black">
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-2" for="state">State</label>
                                    <input type="text" id="state" name="state" 
                                        value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>" 
                                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black">
                                </div>
                                <div>
                                    <label class="block text-gray-700 mb-2" for="zip">Pin Code</label>
                                    <input type="text" id="zip" name="zip" 
                                        value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>" 
                                        class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500  text-black">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition duration-300">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
