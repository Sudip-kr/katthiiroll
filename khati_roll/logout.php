<?php
require_once 'db_config.php';
session_destroy();
header('Location: ' . BASE_URL . '/');
exit;
?>