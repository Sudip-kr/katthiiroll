<?php
require_once '../db_config.php';
requireLogin('admin');

$feedbacks = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Admin</title>
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
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-gray-900">🌯 Khati Roll</h1>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" 
                   class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
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
                   class="flex items-center gap-3 px-6 py-3 bg-yellow-100 text-yellow-700 font-semibold">
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

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900">Customer Feedback</h1>
                <p class="text-gray-600 mt-2">View all customer reviews and suggestions</p>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <?php if($feedbacks->num_rows > 0): ?>
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">User</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Feedback Message</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($fb = $feedbacks->fetch_assoc()): ?>
                            <tr class="border-t border-l-4 border-l-yellow-400 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-600">#<?php echo $fb['id']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-yellow-600">person</span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($fb['username']); ?></p>
                                            <?php if($fb['user_id']): ?>
                                                <p class="text-xs text-gray-500">User ID: <?php echo $fb['user_id']; ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-gray-700"><?php echo htmlspecialchars($fb['message']); ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-gray-400 text-sm">schedule</span>
                                        <div>
                                            <p><?php echo date('M d, Y', strtotime($fb['created_at'])); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo date('h:i A', strtotime($fb['created_at'])); ?></p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-16 px-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <span class="material-symbols-outlined text-5xl text-gray-400">comment</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Feedback Yet</h3>
                        <p class="text-gray-600 mb-6">
                            Customer feedback will appear here once they start sharing their thoughts.
                        </p>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                            <p class="text-sm text-blue-800">
                                💡 <strong>Tip:</strong> Encourage customers to leave feedback by providing great service!
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($feedbacks->num_rows > 0): ?>
            <!-- Statistics -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Total Feedbacks</p>
                            <p class="text-3xl font-bold text-gray-900"><?php echo $feedbacks->num_rows; ?></p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-green-500">rate_review</span>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">This Week</p>
                            <p class="text-3xl font-bold text-gray-900">
                                <?php 
                                $week_count = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['count'];
                                echo $week_count;
                                ?>
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-blue-500">calendar_today</span>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Today</p>
                            <p class="text-3xl font-bold text-gray-900">
                                <?php 
                                $today_count = $conn->query("SELECT COUNT(*) as count FROM feedback WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
                                echo $today_count;
                                ?>
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-purple-500">today</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>