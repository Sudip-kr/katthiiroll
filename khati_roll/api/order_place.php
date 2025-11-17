<?php
require_once '../db_config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$time_slot = $conn->real_escape_string($_POST['time_slot']);
$user_id = $_SESSION['user_id'];
$total = 0;

// Calculate total
foreach ($cart as $item_id => $quantity) {
    $result = $conn->query("SELECT price FROM menu_items WHERE id = $item_id");
    if ($row = $result->fetch_assoc()) {
        $total += $row['price'] * $quantity;
    }
}

// Add tax
$tax = $total * 0.05;
$final_total = $total + $tax;

// Create order
$sql = "INSERT INTO orders (user_id, total_amount, time_slot, status) VALUES ($user_id, $final_total, '$time_slot', 'pending')";
if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    // Add order items
    foreach ($cart as $item_id => $quantity) {
        $result = $conn->query("SELECT price FROM menu_items WHERE id = $item_id");
        if ($row = $result->fetch_assoc()) {
            $price = $row['price'];
            $conn->query("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES ($order_id, $item_id, $quantity, $price)");
        }
    }
    
    // Clear cart
    unset($_SESSION['cart']);
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order failed: ' . $conn->error]);
}
?>