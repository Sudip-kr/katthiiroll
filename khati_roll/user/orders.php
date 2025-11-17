<?php
require_once '../db_config.php';
requireLogin('user');

$user = getCurrentUser();
$orders = $conn->query("SELECT o.*, 
    (SELECT GROUP_CONCAT(CONCAT(mi.name, ' (x', oi.quantity, ')') SEPARATOR ', ')
     FROM order_items oi 
     JOIN menu_items mi ON oi.menu_item_id = mi.id 
     WHERE oi.order_id = o.id) as items_summary
    FROM orders o 
    WHERE o.user_id = {$user['id']} 
    ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Khati Roll</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">🌯 Khati Roll</h1>
            <div class="flex items-center gap-6">
                <nav class="flex gap-6">
                    <a href="<?php echo BASE_URL; ?>/user/home.php" class="text-gray-600 hover:text-yellow-500">Menu</a>
                    <a href="<?php echo BASE_URL; ?>/user/orders.php" class="text-yellow-500 font-semibold">My Orders</a>
                </nav>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Logout</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6">My Orders</h2>

        <?php if ($orders->num_rows > 0): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Order ID</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Time Slot</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $orders->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold">#<?php echo $order['id']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($order['items_summary']); ?></td>
                        <td class="px-6 py-4 font-semibold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($order['time_slot']); ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                <?php 
                                    switch($order['status']) {
                                        case 'completed': echo 'bg-green-100 text-green-800'; break;
                                        case 'active': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-yellow-100 text-yellow-800';
                                    }
                                ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">Start ordering delicious rolls!</p>
            <a href="<?php echo BASE_URL; ?>/user/home.php" 
               class="inline-block px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">
                Browse Menu
            </a>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>