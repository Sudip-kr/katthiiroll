<?php
require_once '../db_config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$item_id = (int)$_POST['item_id'];

if (isset($_SESSION['cart'][$item_id])) {
    unset($_SESSION['cart'][$item_id]);
}

echo json_encode(['success' => true]);
?>