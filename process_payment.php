<?php
require 'backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_membership'])) {
    $member_id = $_POST['member_id'];
    
    try {
        // 1. Obtener información del miembro y su tipo de membresía
        $stmt = $pdo->prepare("SELECT m.membership_type_id, ms.amount, ms.duration_days 
                              FROM members m
                              JOIN memberships ms ON m.membership_type_id = ms.id
                              WHERE m.id = ?");
        $stmt->execute([$member_id]);
        $membership = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$membership) {
            throw new Exception("Miembro o tipo de membresía no encontrado");
        }
        
        $amount = $membership['amount'];
        $duration_days = $membership['duration_days'];
        
        // 2. Calcular nueva fecha de vencimiento
        $stmt = $pdo->prepare("SELECT end_date FROM members WHERE id = ?");
        $stmt->execute([$member_id]);
        $current_end_date = $stmt->fetchColumn();
        
        $new_end_date = date('Y-m-d', strtotime($current_end_date . " + $duration_days days"));
        
        // 3. Registrar el pago
        $payment_data = [
            'member_id' => $member_id,
            'amount' => $amount,
            'payment_method' => $_POST['payment_method'] ?? 'efectivo', // Valor por defecto
            'payment_type' => 'membresía',
            'description' => 'Renovación de membresía ' . $membership['name'] . ' por ' . $duration_days . ' días'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO payments 
                              (member_id, amount, payment_method, payment_type, description) 
                              VALUES (:member_id, :amount, :payment_method, :payment_type, :description)");
        $stmt->execute($payment_data);
        
        // 4. Actualizar la fecha de vencimiento del miembro
        $stmt = $pdo->prepare("UPDATE members SET end_date = ? WHERE id = ?");
        $stmt->execute([$new_end_date, $member_id]);
        
        header('Location: member_details.php?member_id='.$member_id.'&renewal_success=1');
        exit;
        
    } catch (Exception $e) {
        // Manejo de errores
        header('Location: member_details.php?member_id='.$member_id.'&error='.urlencode($e->getMessage()));
        exit;
    }
}