<?php
require_once '../db_config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'items' => []]);
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$items = [];
$total = 0;

foreach ($cart as $item_id => $quantity) {
    $result = $conn->query("SELECT * FROM menu_items WHERE id = $item_id");
    if ($row = $result->fetch_assoc()) {
        $row['quantity'] = $quantity;
        $row['subtotal'] = $row['price'] * $quantity;
        $total += $row['subtotal'];
        $items[] = $row;
    }
}

echo json_encode(['success' => true, 'items' => $items, 'total' => $total]);
?>