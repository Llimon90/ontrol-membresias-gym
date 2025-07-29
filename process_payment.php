<?php
require 'backend/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_type = $_POST['payment_type'];
    $description = $_POST['description'] ?? '';
    
    // Registrar el pago
    $stmt = $pdo->prepare("INSERT INTO payments 
                          (member_id, amount, payment_method, payment_type, description) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$member_id, $amount, $payment_method, $payment_type, $description]);
    
    // Actualizar el saldo si es una recarga
    if ($payment_type === 'recarga') {
        $stmt = $pdo->prepare("INSERT INTO member_balances (member_id, balance) 
                              VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE balance = balance + ?");
        $stmt->execute([$member_id, $amount, $amount]);
    }
    
    header('Location: index.php?payment_success=1');
    exit;
}