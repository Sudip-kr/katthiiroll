<?php
require_once '../db_config.php';
requireLogin('admin');

$menu_items = $conn->query("SELECT * FROM menu_items ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Admin</title>
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
                <a href="<?php echo BASE_URL; ?>/admin/menu.php" class="flex items-center gap-3 px-6 py-3 bg-yellow-100 text-yellow-700 font-semibold">
                    <span class="material-symbols-outlined">restaurant_menu</span> Menu Items
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="flex items-center gap-3 px-6 py-3 text-gray-700 hover:bg-gray-100">
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
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold">Menu Management</h1>
                <button onclick="showAddModal()" class="px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">
                    + Add New Item
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Image</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Price</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $menu_items->fetch_assoc()): ?>
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="w-16 h-16 object-cover rounded">
                            </td>
                            <td class="px-6 py-4 font-semibold"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="px-6 py-4">₹<?php echo number_format($item['price'], 2); ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $item['is_available'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $item['is_available'] ? 'Available' : 'Unavailable'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="deleteItem(<?php echo $item['id']; ?>)" 
                                        class="text-red-600 hover:text-red-800">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-8 rounded-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Add New Menu Item</h2>
            <form action="<?php echo BASE_URL; ?>/admin/menu_add.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Item Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" required class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Price (₹)</label>
                    <input type="number" name="price" step="0.01" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Image URL</label>
                    <input type="url" name="image_url" required class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 py-2 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">
                        Add Item
                    </button>
                    <button type="button" onclick="hideAddModal()" class="flex-1 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const BASE_URL = '<?php echo BASE_URL; ?>';
    function showAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function hideAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }
    function deleteItem(id) {
        if (confirm('Delete this menu item?')) {
            window.location.href = BASE_URL + '/admin/menu_delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>