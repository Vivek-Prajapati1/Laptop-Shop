<?php
require_once 'includes/header.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);
    
    // Here you would typically send an email or save to database
    // For now, we'll just show a success message
    $success = "Thank you for your message! We'll get back to you soon.";
}
?>

<!-- Contact Section -->
<section class="py-16">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center mb-12">Contact Us</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Contact Information -->
            <div>
                <h2 class="text-2xl font-semibold mb-6">Get in Touch</h2>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-blue-600 text-xl mt-1 mr-4"></i>
                        <div>
                            <h3 class="font-semibold">Our Location</h3>
                            <p class="text-gray-600">36003 rajkot, Rajkot, CA 94025</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-phone text-blue-600 text-xl mt-1 mr-4"></i>    
                        <div>
                            <h3 class="font-semibold">Phone</h3>
                            <p class="text-gray-600">+91 6203059664</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-envelope text-blue-600 text-xl mt-1 mr-4"></i>
                        <div>
                            <h3 class="font-semibold">Email</h3>
                            <p class="text-gray-600">info@laptopshop.com</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-clock text-blue-600 text-xl mt-1 mr-4"></i>
                        <div>
                            <h3 class="font-semibold">Business Hours</h3>
                            <p class="text-gray-600">Monday - Friday: 9:00 AM - 6:00 PM</p>
                            <p class="text-gray-600">Saturday: 10:00 AM - 4:00 PM</p>
                            <p class="text-gray-600">Sunday: Closed</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <h3 class="font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-facebook-f text-2xl"></i>
                        </a>
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                        <a href="#" class="text-blue-600 hover:text-blue-800">
                            <i class="fab fa-linkedin-in text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="bg-black p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-6">Send Us a Message</h2>
                
                <?php if (isset($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-4">
                        <label for="name" class="block text-white-700 font-medium mb-2">Name</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-black">
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-white-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-black">
                    </div>
                    
                    <div class="mb-4">
                        <label for="subject" class="block text-white-700 font-medium mb-2">Subject</label>
                        <input type="text" id="subject" name="subject" required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-black">
                    </div>
                    
                    <div class="mb-6">
                        <label for="message" class="block text-white-700 font-medium mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" required
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500 text-black"></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?> 