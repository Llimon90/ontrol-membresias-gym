<?php
require_once 'config.php';

header('Content-Type: application/json');

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
    exit;
}

$productId = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM gym_products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($product) {
        echo json_encode(['success' => true, 'product' => $product]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos']);
}
?>