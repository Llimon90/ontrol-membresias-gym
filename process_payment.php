<?php
require 'backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_membership'])) {
    $member_id = $_POST['member_id'];
    $payment_method = 'efectivo'; // Método por defecto para renovaciones automáticas
    
    try {
        // 1. Obtener información del miembro y su membresía
        $stmt = $pdo->prepare("
            SELECT 
                m.id,
                m.membership_id, 
                m.end_date,
                ms.name as membership_name,
                ms.duration_days,
                ms.amount
            FROM members m
            JOIN memberships ms ON m.membership_id = ms.id
            WHERE m.id = ?
        ");
        $stmt->execute([$member_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            throw new Exception("No se encontró el miembro o su tipo de membresía");
        }
        
        // 2. Calcular nueva fecha de vencimiento
        $new_end_date = date('Y-m-d', strtotime($data['end_date'] . " + {$data['duration_days']} days"));
        
        // 3. Registrar el pago
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
            $data['amount'],
            $payment_method,
            'renovación',
            "Renovación automática de {$data['membership_name']} por {$data['duration_days']} días"
        ]);
        
        // 4. Actualizar la fecha de vencimiento del miembro
        $stmt = $pdo->prepare("UPDATE members SET end_date = ? WHERE id = ?");
        $stmt->execute([$new_end_date, $member_id]);
        
        // Redirigir con mensaje de éxito
        header("Location: member_profile.php?member_id={$member_id}&renewal_success=1");
        exit;
        
    } catch (PDOException $e) {
        // Manejo de errores de base de datos
        header("Location: member_profile.php?member_id={$member_id}&error=".urlencode("Error de base de datos: ".$e->getMessage()));
        exit;
    } catch (Exception $e) {
        // Manejo de otros errores
        header("Location: member_profile.php?member_id={$member_id}&error=".urlencode($e->getMessage()));
        exit;
    }
}

// Si se accede directamente al archivo sin parámetros POST
header("Location: member_profile.php");
exit;