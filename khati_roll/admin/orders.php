<?php
require_once '../db_config.php';
requireLogin('admin');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = $filter != 'all' ? "WHERE o.status = '$filter'" : '';

$orders = $conn->query("SELECT o.*, u.username,
    (SELECT GROUP_CONCAT(CONCAT(mi.name, ' (x', oi.quantity, ')') SEPARATOR ', ')
     FROM order_items oi 
     JOIN menu_items mi ON oi.menu_item_id = mi.id 
     WHERE oi.order_id = o.id) as items_summary
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    $where
    ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold">🌯 Khati Roll</h1>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">dashboard</span> Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/menu.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">restaurant_menu</span> Menu Items
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="flex items-center gap-3 px-6 py-3 bg-yellow-100 text-yellow-700 font-semibold">
                    <span class="material-symbols-outlined">shopping_cart</span> Orders
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/feedback.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">thumb_up</span> Feedbacks
                </a>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="flex items-center gap-3 px-6 py-3 text-red-600 hover:bg-red-50 mt-4">
                    <span class="material-symbols-outlined">logout</span> Logout
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <h1 class="text-4xl font-bold mb-8">Order Management</h1>

            <div class="flex gap-2 mb-6">
                <a href="?filter=all" class="px-4 py-2 rounded-lg <?php echo $filter=='all' ? 'bg-yellow-400 text-gray-900' : 'bg-gray-200'; ?>">All</a>
                <a href="?filter=pending" class="px-4 py-2 rounded-lg <?php echo $filter=='pending' ? 'bg-yellow-400 text-gray-900' : 'bg-gray-200'; ?>">Pending</a>
                <a href="?filter=active" class="px-4 py-2 rounded-lg <?php echo $filter=='active' ? 'bg-yellow-400 text-gray-900' : 'bg-gray-200'; ?>">Active</a>
                <a href="?filter=completed" class="px-4 py-2 rounded-lg <?php echo $filter=='completed' ? 'bg-yellow-400 text-gray-900' : 'bg-gray-200'; ?>">Completed</a>
                <a href="?filter=cancelled" class="px-4 py-2 rounded-lg <?php echo $filter=='cancelled' ? 'bg-yellow-400 text-gray-900' : 'bg-gray-200'; ?>">Cancelled</a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Items</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Time Slot</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders->fetch_assoc()): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">#<?php echo $order['id']; ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['username'] ?: 'Guest'); ?></td>
                            <td class="px-6 py-4 text-sm"><?php echo htmlspecialchars($order['items_summary']); ?></td>
                            <td class="px-6 py-4">₹<?php echo number_format($order['total_amount'], 2); ?></td>
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
                            <td class="px-6 py-4 text-sm"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>