<?php
require_once 'db_config.php';

$error = '';
$requested_role = isset($_GET['role']) ? $_GET['role'] : 'user';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            switch($user['role']) {
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
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'User not found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Khati Roll</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    <?php 
                    switch($requested_role) {
                        case 'admin': echo 'Admin Login'; break;
                        case 'staff': echo 'Staff Login'; break;
                        default: echo 'User Login'; break;
                    }
                    ?>
                </h1>
                <p class="text-gray-600 mt-2">Enter your credentials</p>
            </div>

            <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-transparent">
                </div>
                
                <button type="submit" class="w-full bg-yellow-400 text-gray-900 font-bold py-3 rounded-lg hover:bg-yellow-500 transition">
                    Login
                </button>
            </form>

            <div class="mt-6 text-center space-y-3">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="<?php echo BASE_URL; ?>/register.php" class="text-yellow-600 hover:underline font-semibold">Sign Up</a>
                </p>
                
                <div class="pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-2">Quick Login:</p>
                    <div class="flex gap-2">
                        <a href="?role=user" class="flex-1 text-center px-3 py-2 text-sm bg-yellow-100 rounded hover:bg-yellow-200 <?php echo $requested_role == 'user' ? 'ring-2 ring-yellow-400' : ''; ?>">User</a>
                        <a href="?role=staff" class="flex-1 text-center px-3 py-2 text-sm bg-blue-100 rounded hover:bg-blue-200 <?php echo $requested_role == 'staff' ? 'ring-2 ring-blue-400' : ''; ?>">Staff</a>
                        <a href="?role=admin" class="flex-1 text-center px-3 py-2 text-sm bg-red-100 rounded hover:bg-red-200 <?php echo $requested_role == 'admin' ? 'ring-2 ring-red-400' : ''; ?>">Admin</a>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 pt-2">
                    <a href="<?php echo BASE_URL; ?>/" class="text-gray-600 hover:underline">← Back to Home</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>