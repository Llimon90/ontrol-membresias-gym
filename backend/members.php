<?php
header('Content-Type: application/json');
require 'config.php';
$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $stmt = $pdo->query(
      "SELECT m.*, ms.name AS membership_name 
       FROM members m 
       JOIN memberships ms ON m.membership_id = ms.id"
    );
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $membership_id = $_POST['membership_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';

    if (!$name || !$phone || !$membership_id || !$start_date) {
        echo json_encode(['success'=>false,'message'=>'Campos obligatorios faltan']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
    $stmt->execute([$membership_id]);
    $m = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$m) {
        echo json_encode(['success'=>false,'message'=>'Tipo de membresía no válido']);
        exit;
    }
    $duration = $m['duration_days'];
    $end_date = date('Y-m-d', strtotime("$start_date + $duration days"));

    $ins = $pdo->prepare(
      "INSERT INTO members (name,email,phone,membership_id,start_date,end_date) 
       VALUES (?,?,?,?,?,?)"
    );
    $ins->execute([$name,$email,$phone,$membership_id,$start_date,$end_date]);

    echo json_encode(['success'=>true]);
    exit;
}

// Similarmente se pueden implementar 'update' y 'delete'
echo json_encode(['success'=>false,'message'=>'Acción inválida']);
