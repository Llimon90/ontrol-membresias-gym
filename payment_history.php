<?php
require 'backend/config.php';

$member_id = $_GET['member_id'] ?? 0;
$member = $pdo->query("SELECT * FROM members WHERE id = $member_id")->fetch(PDO::FETCH_ASSOC);
$payments = $pdo->query("SELECT * FROM payments 
                        WHERE member_id = $member_id 
                        ORDER BY payment_date DESC")->fetchAll(PDO::FETCH_ASSOC);
$balance = $pdo->query("SELECT balance FROM member_balances 
                       WHERE member_id = $member_id")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pagos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Usa los mismos estilos que en index.php */
        body { background-color: #1a1a2e; color: white; font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: #16213e; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #2d3748; }
        th { background: rgba(67, 97, 238, 0.2); }
        .btn { padding: 8px 15px; border-radius: 5px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4361ee; color: white; }
        .status-badge { padding: 4px 8px; border-radius: 20px; font-size: 12px; }
        .status-active { background: rgba(76, 201, 240, 0.2); color: #4cc9f0; }
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
                    <?php foreach($payments as $payment): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></td>
                        <td>$<?= number_format($payment['amount'], 2) ?></td>
                        <td>
                            <?= ucfirst($payment['payment_type']) ?>
                        </td>
                        <td><?= ucfirst($payment['payment_method']) ?></td>
                        <td><?= htmlspecialchars($payment['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay registros de pagos</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <a href="index.php" class="btn btn-primary" style="margin-top: 15px;">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>