<?php
require_once 'db_config.php';

if (isLoggedIn()) {
    switch($_SESSION['role']) {
        case 'admin':
            header('Location: ' . BASE_URL . '/admin/dashboard.php');
            break;
        case 'staff':
            header('Location: ' . BASE_URL . '/staff/orders.php');
            break;
        default:
            header('Location: ' . BASE_URL . '/user/home.php');
            break;
    }
    exit;
}

$menu_items = $conn->query("SELECT * FROM menu_items WHERE is_available = 1 LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khati Roll - Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">🌯 Khati Roll</h1>
            <div class="flex gap-4">
                <a href="<?php echo BASE_URL; ?>/login.php?role=user" class="px-6 py-2 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">User Login</a>
                <a href="<?php echo BASE_URL; ?>/login.php?role=staff" class="px-6 py-2 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600">Staff Login</a>
                <a href="<?php echo BASE_URL; ?>/login.php?role=admin" class="px-6 py-2 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600">Admin Login</a>
            </div>
        </div>
    </header>

    <section class="bg-gradient-to-r from-yellow-400 to-orange-400 py-20">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-5xl font-extrabold text-white mb-4">Welcome to Khati Roll</h2>
            <p class="text-xl text-white mb-8">Freshly Made, Incredibly Delicious Veggie Rolls</p>
            <a href="<?php echo BASE_URL; ?>/login.php?role=user" class="inline-block px-8 py-4 bg-white text-gray-900 font-bold rounded-lg text-lg hover:bg-gray-100">Order Now</a>
        </div>
    </section>

    <section class="container mx-auto px-4 py-16">
        <h3 class="text-3xl font-bold mb-8 text-center">Our Popular Rolls</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php while($item = $menu_items->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p class="text-yellow-500 font-bold text-xl">₹<?php echo number_format($item['price'], 2); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <section class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h3 class="text-3xl font-bold mb-8 text-center">Access Your Panel</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-6xl mb-4">👤</div>
                    <h4 class="text-2xl font-bold mb-4">User Panel</h4>
                    <p class="text-gray-600 mb-6">Browse menu, place orders</p>
                    <a href="<?php echo BASE_URL; ?>/login.php?role=user" class="block w-full px-6 py-3 bg-yellow-400 text-gray-900 font-bold rounded-lg hover:bg-yellow-500">Login as User</a>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-6xl mb-4">👨‍🍳</div>
                    <h4 class="text-2xl font-bold mb-4">Staff Panel</h4>
                    <p class="text-gray-600 mb-6">Manage incoming orders</p>
                    <a href="<?php echo BASE_URL; ?>/login.php?role=staff" class="block w-full px-6 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600">Login as Staff</a>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg text-center">
                    <div class="text-6xl mb-4">⚙️</div>
                    <h4 class="text-2xl font-bold mb-4">Admin Panel</h4>
                    <p class="text-gray-600 mb-6">Full management access</p>
                    <a href="<?php echo BASE_URL; ?>/login.php?role=admin" class="block w-full px-6 py-3 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600">Login as Admin</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2024 Khati Roll. All rights reserved.</p>
            <p class="mt-2 text-sm text-gray-400">Default: admin/admin123 | staff/staff123</p>
        </div>
    </footer>
</body>
</html>