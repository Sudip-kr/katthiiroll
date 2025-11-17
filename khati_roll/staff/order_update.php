<?php
require_once '../db_config.php';
requireLogin('staff');

// Set JSON header
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

// Validate inputs
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required parameters: order_id or status'
    ]);
    exit;
}

$order_id = (int)$_POST['order_id'];
$status = $conn->real_escape_string($_POST['status']);

// Validate status
$valid_statuses = ['pending', 'active', 'completed', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid status value'
    ]);
    exit;
}

// Check if order exists
$check_order = $conn->query("SELECT id FROM orders WHERE id = $order_id");
if ($check_order->num_rows === 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Order not found'
    ]);
    exit;
}

// Update order status
$sql = "UPDATE orders SET status = '$status', updated_at = NOW() WHERE id = $order_id";

if ($conn->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Order updated successfully',
        'order_id' => $order_id,
        'new_status' => $status
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $conn->error
    ]);
}
?>