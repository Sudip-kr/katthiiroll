<?php
require_once '../db_config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$item_id = (int)$_POST['item_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$item_id])) {
    $_SESSION['cart'][$item_id] += $quantity;
} else {
    $_SESSION['cart'][$item_id] = $quantity;
}

echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
?>