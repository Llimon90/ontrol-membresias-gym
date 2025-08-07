<?php
require 'backend/config.php';

// Validar y sanitizar el member_id
$member_id = filter_input(INPUT_GET, 'member_id', FILTER_VALIDATE_INT);

if (!$member_id || $member_id <= 0) {
    die("ID de miembro no válido");
}

// Consulta preparada para obtener datos del miembro
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    die("Miembro no encontrado");
}

// Consulta preparada para pagos
$stmt = $pdo->prepare("SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC");
$stmt->execute([$member_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta preparada para saldo
$stmt = $pdo->prepare("SELECT balance FROM member_balances WHERE member_id = ?");
$stmt->execute([$member_id]);
$balance = $stmt->fetchColumn();
$balance = $balance ?: 0.00; // Valor por defecto si es null
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #1a1a2e; color: white; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: #16213e; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #2d3748; }
        th { background: rgba(67, 97, 238, 0.2); }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; display: inline-block; margin-top: 15px; }
        .btn-primary { background: #4361ee; color: white; }
        .btn-primary:hover { background: #3a56d4; }
        .status-badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; }
        .status-active { background: rgba(76, 201, 240, 0.2); color: #4cc9f0; }
        .no-data { text-align: center; padding: 20px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2><i class="fas fa-history"></i> Historial de Pagos: <?= htmlspecialchars($member['name']) ?></h2>
            <p>Saldo actual: <strong>$<?= number_format($balance, 2) ?></strong></p>
            
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach($payments as $payment): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></td>
                            <td>$<?= number_format($payment['amount'], 2) ?></td>
                            <td><?= ucfirst($payment['payment_type']) ?></td>
                            <td><?= ucfirst($payment['payment_method']) ?></td>
                            <td><?= htmlspecialchars($payment['description']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">
                                <i class="fas fa-info-circle"></i> No hay registros de pagos
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>