<?php
require_once '../db_config.php';
requireLogin('staff');

$active_orders = $conn->query("SELECT o.*, u.username,
    (SELECT GROUP_CONCAT(CONCAT(mi.name, ' (x', oi.quantity, ')') SEPARATOR ', ')
     FROM order_items oi 
     JOIN menu_items mi ON oi.menu_item_id = mi.id 
     WHERE oi.order_id = o.id) as items_summary,
    TIMESTAMPDIFF(SECOND, o.created_at, NOW()) as elapsed_seconds
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.status IN ('pending', 'active') 
    ORDER BY o.created_at DESC");

$recent_orders = $conn->query("SELECT o.*, u.username
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.status = 'completed' 
    ORDER BY o.updated_at DESC 
    LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Orders - Khati Roll</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">🌯 Khati Roll - Staff Panel</h1>
            <div class="flex gap-4 items-center">
                <button onclick="location.reload()" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    Refresh
                </button>
                <a href="<?php echo BASE_URL; ?>/logout.php" 
                   class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Logout</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-900">Active Orders</h2>
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold">
                        <?php echo $active_orders->num_rows; ?> Active
                    </span>
                </div>
                
                <div class="space-y-4">
                    <?php if ($active_orders->num_rows > 0): ?>
                        <?php while($order = $active_orders->fetch_assoc()): 
                            $minutes = floor($order['elapsed_seconds'] / 60);
                            $seconds = $order['elapsed_seconds'] % 60;
                            $timer_color = $minutes < 15 ? 'bg-green-100 text-green-700' : 
                                          ($minutes < 25 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700');
                        ?>
                        <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-bold text-gray-900">Order #<?php echo $order['id']; ?></span> | 
                                        <?php echo date('h:i A', strtotime($order['created_at'])); ?>
                                        <?php if($order['username']): ?>
                                         | Customer: <span class="font-semibold"><?php echo htmlspecialchars($order['username']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-semibold mt-2 <?php echo $timer_color; ?>">
                                        <span class="material-symbols-outlined text-base">timer</span>
                                        <span><?php echo $minutes; ?>m <?php echo $seconds; ?>s elapsed</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <h3 class="font-bold text-lg mb-2 text-gray-900">
                                    <?php echo htmlspecialchars($order['items_summary']); ?>
                                </h3>
                                <div class="flex justify-between items-center">
                                    <p class="text-gray-700">
                                        <span class="font-semibold text-xl text-green-600">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="material-symbols-outlined text-sm align-middle">schedule</span>
                                        Time Slot: <span class="font-semibold"><?php echo htmlspecialchars($order['time_slot']); ?></span>
                                    </p>
                                </div>
                            </div>
                            
                            <button onclick="fulfillOrder(<?php echo $order['id']; ?>)" 
                                    class="w-full py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500 transition flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">check_circle</span>
                                Mark as Fulfilled
                            </button>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="bg-white p-12 rounded-lg shadow text-center">
                            <div class="text-6xl mb-4">🎉</div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">All caught up!</h3>
                            <p class="text-gray-600">No active orders at the moment.</p>
                            <p class="text-sm text-gray-500 mt-2">New orders will appear here automatically.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="bg-white p-6 rounded-lg shadow sticky top-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-900">Recently Fulfilled</h2>
                    <div class="space-y-3">
                        <?php if ($recent_orders->num_rows > 0): ?>
                            <?php while($order = $recent_orders->fetch_assoc()): ?>
                            <div class="flex items-center gap-4 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex-shrink-0 bg-green-500 text-white rounded-full h-10 w-10 flex items-center justify-center">
                                    <span class="material-symbols-outlined">check</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">Order #<?php echo $order['id']; ?></p>
                                    <p class="text-xs text-gray-600">
                                        <?php echo htmlspecialchars($order['username'] ?: 'Guest'); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo date('h:i A', strtotime($order['updated_at'])); ?>
                                    </p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm text-center py-8">No completed orders yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    const BASE_URL = '<?php echo BASE_URL; ?>';

    function fulfillOrder(orderId) {
        // Show confirmation dialog
        if (!confirm('Mark Order #' + orderId + ' as fulfilled?\n\nThis action will notify the customer that their order is ready.')) {
            return;
        }

        // Disable button to prevent double clicks
        const buttons = document.querySelectorAll('button[onclick*="fulfillOrder(' + orderId + ')"]');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Processing...';
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        });

        // Send request to update order
        fetch(BASE_URL + '/staff/order_update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId + '&status=completed'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                alert('✅ Order #' + orderId + ' marked as fulfilled!');
                // Reload page to show updated list
                location.reload();
            } else {
                // Show error message
                alert('❌ Failed to update order: ' + (data.message || 'Unknown error'));
                // Re-enable button
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Mark as Fulfilled';
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error updating order: ' + error.message);
            // Re-enable button
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined">check_circle</span> Mark as Fulfilled';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        });
    }

    // Auto-refresh every 30 seconds to show new orders
    setTimeout(() => {
        console.log('Auto-refreshing page...');
        location.reload();
    }, 30000);

    // Show countdown timer
    let countdown = 30;
    setInterval(() => {
        countdown--;
        if (countdown <= 0) countdown = 30;
    }, 1000);
    </script>
</body>
</html>