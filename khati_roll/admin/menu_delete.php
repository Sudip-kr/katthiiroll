<?php
require_once '../db_config.php';
requireLogin('admin');

$id = (int)$_GET['id'];
$conn->query("DELETE FROM menu_items WHERE id = $id");
header('Location: ' . BASE_URL . '/admin/menu.php');
exit;
?>