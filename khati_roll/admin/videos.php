<?php
require_once '../db_config.php';
requireLogin('admin');

$videos = $conn->query("SELECT * FROM video_banners ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Banners - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6">
                <h1 class="text-xl font-bold">🌯 Khati Roll</h1>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            <nav class="mt-6">
                <a href="/admin/dashboard.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
                <a href="/admin/menu.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">restaurant_menu</span>
                    Menu Items
                </a>
                <a href="/admin/orders.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    Orders
                </a>
                <a href="/admin/feedback.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
                    <span class="material-symbols-outlined">thumb_up</span>
                    Feedbacks
                </a>
                <a href="/admin/videos.php" class="flex items-center gap-3 px-6 py-3 bg-yellow-100 text-yellow-700 font-semibold">
                    <span class="material-symbols-outlined">slideshow</span>
                    Video Banners
                </a>
                <a href="/logout.php" class="flex items-center gap-3 px-6 py-3 text-red-600 hover:bg-red-50 mt-4">
                    <span class="material-symbols-outlined">logout</span>
                    Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold">Video Banners</h1>
                <button onclick="alert('Add video feature - Coming soon!')" 
                        class="px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">
                    + Upload New Banner
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Preview</th>
                            <th class="px-6 py-3 text-left">Video URL</th>
                            <th class="px-6 py-3 text-left">Duration</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($videos->num_rows > 0): ?>
                            <?php while($video = $videos->fetch_assoc()): ?>
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="w-32 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl text-gray-400">videocam</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-sm"><?php echo htmlspecialchars($video['video_url']); ?></td>
                                <td class="px-6 py-4"><?php echo $video['duration']; ?>s</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $video['status']=='active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo ucfirst($video['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-red-600 hover:text-red-800">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No video banners yet. Click "Upload New Banner" to add one.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>