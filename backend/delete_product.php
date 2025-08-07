<?php
require_once 'config.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php?error=invalid_id');
    exit;
}

$productId = $_GET['id'];

try {
    // Opción 1: Eliminar físicamente el producto
    // $stmt = $pdo->prepare("DELETE FROM gym_products WHERE id = ?");
    
    // Opción 2: Marcar como inactivo (recomendado)
    $stmt = $pdo->prepare("UPDATE gym_products SET is_active = FALSE WHERE id = ?");
    
    $stmt->execute([$productId]);
    
    header('Location: products.php?success=product_deleted');
} catch(PDOException $e) {
    header('Location: products.php?error=delete_failed');
}
?>