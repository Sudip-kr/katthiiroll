<?php
require_once '../db_config.php';
requireLogin('admin');

$stats = [
    'menu_items' => $conn->query("SELECT COUNT(*) as count FROM menu_items")->fetch_assoc()['count'],
    'total_orders' => $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'],
    'total_feedback' => $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'],
    'recent_orders' => $conn->query("SELECT COUNT(*) as count FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count']
];

$latest_orders = $conn->query("SELECT o.*, u.username 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC 
    LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Khati Roll</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-gray-900">🌯 Khati Roll</h1>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" 
                   class="flex items-center gap-3 px-6 py-3 bg-yellow-100 text-yellow-700 font-semibold">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/menu.php" 
                   class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">restaurant_menu</span>
                    Menu Items
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/orders.php" 
                   class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    Orders
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/feedback.php" 
                   class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">thumb_up</span>
                    Feedbacks
                </a>
                <a href="<?php echo BASE_URL; ?>/logout.php" 
                   class="flex items-center gap-3 px-6 py-3 text-red-600 hover:bg-red-50 mt-4">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <h1 class="text-4xl font-bold mb-8 text-gray-900">Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 mb-2">Total Menu Items</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['menu_items']; ?></p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-yellow-500">restaurant_menu</span>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 mb-2">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['total_orders']; ?></p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-blue-500">shopping_cart</span>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 mb-2">Total Feedbacks</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['total_feedback']; ?></p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-green-500">thumb_up</span>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 mb-2">Last 7 Days</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $stats['recent_orders']; ?></p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-purple-500">trending_up</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-900">Latest Orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Order ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Customer</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = $latest_orders->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold">#<?php echo $order['id']; ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($order['username'] ?: 'Guest'); ?></td>
                                <td class="px-6 py-4 font-semibold">₹<?php echo number_format($order['total_amount'], 2); ?></td>
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
                                    <?php echo date('M d, h:i A', strtotime($order['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>