<?php
require 'backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? 0;
    $name = $_POST['name'];
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($product_id > 0) {
        // Actualizar producto existente
        $stmt = $pdo->prepare("UPDATE gym_products 
                              SET name = ?, description = ?, price = ?, is_active = ? 
                              WHERE id = ?");
        $stmt->execute([$name, $description, $price, $is_active, $product_id]);
    } else {
        // Crear nuevo producto
        $stmt = $pdo->prepare("INSERT INTO gym_products 
                              (name, description, price, is_active) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $is_active]);
    }
    
    header('Location: index.php?product_success=1');
    exit;
}