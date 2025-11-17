<?php
require_once '../db_config.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $image_url = $conn->real_escape_string($_POST['image_url']);
    
    $sql = "INSERT INTO menu_items (name, description, price, image_url) 
            VALUES ('$name', '$description', $price, '$image_url')";
    
    if ($conn->query($sql)) {
        header('Location: ' . BASE_URL . '/admin/menu.php');
    } else {
        die('Error: ' . $conn->error);
    }
}
?>