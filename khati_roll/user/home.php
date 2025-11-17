<?php
require_once '../db_config.php';
requireLogin('user');

$user = getCurrentUser();
$menu_items = $conn->query("SELECT * FROM menu_items WHERE is_available = 1");

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_items = [];
$subtotal = 0;

foreach ($cart as $item_id => $quantity) {
    $result = $conn->query("SELECT * FROM menu_items WHERE id = $item_id");
    if ($row = $result->fetch_assoc()) {
        $row['quantity'] = $quantity;
        $row['subtotal'] = $row['price'] * $quantity;
        $subtotal += $row['subtotal'];
        $cart_items[] = $row;
    }
}

$tax = $subtotal * 0.05;
$total = $subtotal + $tax;

// Handle feedback submission
$feedback_success = '';
$feedback_error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $message = $conn->real_escape_string($_POST['feedback_message']);
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    
    if (!empty($message)) {
        $sql = "INSERT INTO feedback (user_id, username, message) VALUES ($user_id, '$username', '$message')";
        if ($conn->query($sql)) {
            $feedback_success = 'Thank you for your feedback!';
        } else {
            $feedback_error = 'Failed to submit feedback. Please try again.';
        }
    } else {
        $feedback_error = 'Please enter your feedback message.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Khati Roll</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">🌯 Khati Roll</h1>
            <div class="flex items-center gap-6">
                <nav class="flex gap-6">
                    <a href="<?php echo BASE_URL; ?>/user/home.php" class="text-yellow-500 font-semibold">Menu</a>
                    <a href="<?php echo BASE_URL; ?>/user/orders.php" class="text-gray-600 hover:text-yellow-500">My Orders</a>
                    <button onclick="openFeedbackModal()" class="text-gray-600 hover:text-yellow-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">rate_review</span>
                        Feedback
                    </button>
                </nav>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">Hello, <?php echo htmlspecialchars($user['username']); ?>!</span>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <main class="flex-1 p-6 lg:mr-96">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-3xl font-bold mb-6">Our Menu</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($item = $menu_items->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($item['description']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-yellow-500 font-bold text-xl">₹<?php echo number_format($item['price'], 2); ?></span>
                                <button onclick="addToCart(<?php echo $item['id']; ?>)" 
                                        class="px-4 py-2 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500 transition">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>

        <aside class="fixed right-0 top-16 h-[calc(100vh-4rem)] w-96 bg-white border-l shadow-lg flex flex-col">
            <div class="p-6 border-b">
                <h2 class="text-2xl font-bold">Your Order</h2>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <?php if (empty($cart_items)): ?>
                <div class="text-center py-20">
                    <span class="material-symbols-outlined text-6xl text-gray-300">shopping_cart</span>
                    <p class="mt-4 text-gray-500">Your cart is empty</p>
                </div>
                <?php else: ?>
                    <?php foreach ($cart_items as $item): ?>
                    <div class="flex items-center gap-4 mb-4 p-2 rounded hover:bg-gray-50">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="w-16 h-16 object-cover rounded">
                        <div class="flex-1">
                            <h4 class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="text-yellow-500 font-bold">₹<?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" 
                                    class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 font-bold">-</button>
                            <span class="w-8 text-center font-semibold"><?php echo $item['quantity']; ?></span>
                            <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" 
                                    class="w-8 h-8 bg-gray-200 rounded-full hover:bg-gray-300 font-bold">+</button>
                        </div>
                        <button onclick="removeFromCart(<?php echo $item['id']; ?>)" 
                                class="text-red-500 hover:text-red-700">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if (!empty($cart_items)): ?>
            <div class="p-6 border-t bg-gray-50">
                <div class="mb-4">
                    <h3 class="font-bold mb-3">Select Time Slot</h3>
                    <div class="flex gap-2 flex-wrap">
                        <button onclick="selectTimeSlot('30min')" class="time-slot px-4 py-2 bg-yellow-400 text-gray-900 font-semibold rounded-lg">30min</button>
                        <button onclick="selectTimeSlot('40min')" class="time-slot px-4 py-2 bg-gray-200 rounded-lg hover:bg-yellow-400">40min</button>
                        <button onclick="selectTimeSlot('50min')" class="time-slot px-4 py-2 bg-gray-200 rounded-lg hover:bg-yellow-400">50min</button>
                        <button onclick="selectTimeSlot('1hr')" class="time-slot px-4 py-2 bg-gray-200 rounded-lg hover:bg-yellow-400">1hr</button>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold">₹<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Taxes (5%)</span>
                        <span class="font-semibold">₹<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Total</span>
                        <span>₹<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>

                <button onclick="placeOrder()" 
                        class="w-full py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">
                    Confirm Order
                </button>
            </div>
            <?php endif; ?>
        </aside>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Share Your Feedback</h2>
                <button onclick="closeFeedbackModal()" class="text-gray-500 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <?php if ($feedback_success): ?>
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                <?php echo htmlspecialchars($feedback_success); ?>
            </div>
            <?php endif; ?>

            <?php if ($feedback_error): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                <?php echo htmlspecialchars($feedback_error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        How was your experience?
                    </label>
                    <textarea 
                        name="feedback_message" 
                        required 
                        rows="5" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent"
                        placeholder="Tell us what you think about our food and service..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="submit_feedback" 
                            class="flex-1 py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">send</span>
                        Submit Feedback
                    </button>
                    <button type="button" onclick="closeFeedbackModal()" 
                            class="px-6 py-3 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>

            <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    💡 Your feedback helps us improve our service and menu!
                </p>
            </div>
        </div>
    </div>

    <script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    let selectedTimeSlot = '30min';

    function addToCart(itemId) {
        fetch(BASE_URL + '/api/cart_add.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'item_id=' + itemId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function removeFromCart(itemId) {
        fetch(BASE_URL + '/api/cart_remove.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'item_id=' + itemId
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function updateQuantity(itemId, change) {
        if (change < 0) {
            removeFromCart(itemId);
        } else {
            addToCart(itemId);
        }
    }

    function selectTimeSlot(slot) {
        selectedTimeSlot = slot;
        document.querySelectorAll('.time-slot').forEach(btn => {
            btn.classList.remove('bg-yellow-400', 'text-gray-900');
            btn.classList.add('bg-gray-200');
        });
        event.target.classList.remove('bg-gray-200');
        event.target.classList.add('bg-yellow-400', 'text-gray-900');
    }

    function placeOrder() {
        if (!confirm('Confirm your order?')) return;
        
        fetch(BASE_URL + '/api/order_place.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'time_slot=' + selectedTimeSlot
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully! Order ID: #' + data.order_id);
                location.href = BASE_URL + '/user/orders.php';
            } else {
                alert('Order failed: ' + (data.message || 'Unknown error'));
            }
        });
    }

    function openFeedbackModal() {
        document.getElementById('feedbackModal').classList.remove('hidden');
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').classList.add('hidden');
    }

    // Auto-open feedback modal if there's a success/error message
    <?php if ($feedback_success || $feedback_error): ?>
    window.addEventListener('DOMContentLoaded', function() {
        openFeedbackModal();
        <?php if ($feedback_success): ?>
        setTimeout(function() {
            closeFeedbackModal();
        }, 3000);
        <?php endif; ?>
    });
    <?php endif; ?>

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFeedbackModal();
        }
    });
    </script>
</body>
</html>