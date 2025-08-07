<?php
require 'backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $payment_type = $_POST['payment_type'];
    $amount = (float)$_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $description = $_POST['description'] ?? '';
    $product_id = $_POST['product_id'] ?? null;
    
    try {
        // Validar miembro existe
        $stmt = $pdo->prepare("SELECT id FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Miembro no encontrado");
        }
        
        // Procesar según tipo de pago
        switch ($payment_type) {
            case 'membresia':
                $description = $description ?: "Pago de membresía";
                break;
                
            case 'producto':
                if ($product_id) {
                    $stmt = $pdo->prepare("SELECT name, price FROM gym_products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    
                    if ($product) {
                        $description = $description ?: "Compra de " . $product['name'];
                        $amount = $product['price']; // Forzar el precio del producto
                    }
                }
                break;
                
            case 'recarga':
                $description = $description ?: "Recarga de saldo";
                break;
                
            default:
                throw new Exception("Tipo de pago no válido");
        }
        
        // Registrar el pago
        $stmt = $pdo->prepare("
            INSERT INTO payments (
                member_id, 
                amount, 
                payment_method, 
                payment_type, 
                description,
                payment_date
            ) VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $member_id,
            $amount,
            $payment_method,
            $payment_type,
            $description
        ]);
        
        // Procesamiento adicional para recargas (si tienes tabla de saldos)
        if ($payment_type === 'recarga') {
            $stmt = $pdo->prepare("
                INSERT INTO member_balances (member_id, balance) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE balance = balance + ?
            ");
            $stmt->execute([$member_id, $amount, $amount]);
        }
        
        // Redirigir con éxito
        header("Location: member_profile.php?member_id=$member_id&payment_success=1");
        exit;
        
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    // Redirigir con error
    header("Location: payment.php?error=" . urlencode($error));
    exit;
}

// Redirección por defecto
header("Location: payment.php");
exit;