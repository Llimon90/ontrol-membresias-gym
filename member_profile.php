<?php
require 'backend/config.php';

$member_id = $_GET['id'] ?? 0;

// Obtener información básica del miembro con duración de membresía
$member = $pdo->prepare("SELECT m.*, ms.name AS membership_name, ms.duration_days, ms.price AS membership_price 
                        FROM members m 
                        JOIN memberships ms ON m.membership_id = ms.id 
                        WHERE m.id = ?");
$member->execute([$member_id]);
$member = $member->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header('Location: index.php?error=member_not_found');
    exit;
}

// Obtener saldo actual
$balance = $pdo->prepare("SELECT balance FROM member_balances WHERE member_id = ?");
$balance->execute([$member_id]);
$balance = $balance->fetchColumn() ?? 0;

// Obtener historial de pagos (últimos 10)
$payments = $pdo->prepare("SELECT * FROM payments 
                          WHERE member_id = ? 
                          ORDER BY payment_date DESC 
                          LIMIT 10");
$payments->execute([$member_id]);
$payments = $payments->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos adquiridos (últimos 10)
$products = $pdo->prepare("SELECT p.name, p.price, py.payment_date 
                          FROM payments py
                          JOIN gym_products p ON py.description LIKE CONCAT('%', p.name, '%')
                          WHERE py.member_id = ? AND py.payment_type = 'producto'
                          ORDER BY py.payment_date DESC
                          LIMIT 10");
$products->execute([$member_id]);
$products = $products->fetchAll(PDO::FETCH_ASSOC);

// Calcular días restantes de membresía
$today = new DateTime();
$end_date = new DateTime($member['end_date']);
$interval = $today->diff($end_date);
$days_remaining = $interval->days;
$membership_status = ($end_date < $today) ? 'Vencida' : 'Activa';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil de <?= htmlspecialchars($member['name']) ?> | Gimnasio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --warning: #f8961e;
      --danger: #f72585;
      --dark: #212529;
      --light: #f8f9fa;
      --gray: #6c757d;
      --bg-dark: #1a1a2e;
      --bg-card: #16213e;
      --text-primary: #ffffff;
      --text-secondary: #e2e2e2;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--bg-dark);
      color: var(--text-primary);
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .header h1 {
      font-size: 28px;
      color: var(--success);
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: 500;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      font-size: 14px;
      text-decoration: none;
    }

    .btn-primary {
      background-color: var(--primary);
      color: white;
    }

    .btn-primary:hover {
      background-color: var(--secondary);
    }

    .btn-danger {
      background-color: var(--danger);
      color: white;
    }

    .btn-danger:hover {
      background-color: #d1146a;
    }

    .btn-success {
      background-color: var(--success);
      color: white;
    }

    .btn-success:hover {
      background-color: #3ab4d9;
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 13px;
    }

    .card {
      background-color: var(--bg-card);
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-title {
      font-size: 20px;
      margin-bottom: 20px;
      color: var(--success);
      display: flex;
      align-items: center;
    }

    .card-title i {
      margin-right: 10px;
    }

    .profile-header {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .profile-avatar {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background-color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 20px;
      font-size: 40px;
      color: white;
    }

    .profile-info h2 {
      font-size: 24px;
      margin-bottom: 5px;
      color: var(--text-primary);
    }

    .profile-info p {
      color: var(--text-secondary);
      margin-bottom: 5px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .info-card {
      background-color: rgba(255, 255, 255, 0.05);
      padding: 15px;
      border-radius: 8px;
    }

    .info-card h3 {
      font-size: 14px;
      color: var(--text-secondary);
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .info-card .value {
      font-size: 20px;
      font-weight: bold;
    }

    .table-responsive {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th {
      background-color: rgba(67, 97, 238, 0.2);
      color: var(--text-primary);
      padding: 12px 15px;
      text-align: left;
      font-weight: 500;
    }

    td {
      padding: 12px 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      color: var(--text-secondary);
    }

    tr:hover td {
      background-color: rgba(255, 255, 255, 0.03);
    }

    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }

    .status-active {
      background-color: rgba(76, 201, 240, 0.2);
      color: var(--success);
    }

    .status-expiring {
      background-color: rgba(248, 150, 30, 0.2);
      color: var(--warning);
    }

    .status-expired {
      background-color: rgba(247, 37, 133, 0.2);
      color: var(--danger);
    }

    .actions {
      display: flex;
      gap: 5px;
    }

    .profile-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background-color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 32px;
      font-weight: bold;
      margin-right: 20px;
    }

    .info-card {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      padding: 15px;
      transition: transform 0.3s ease;
    }

    .info-card:hover {
      transform: translateY(-3px);
      background: rgba(255, 255, 255, 0.08);
    }

    .profile-tabs {
      display: flex;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 20px;
    }

    .profile-tab {
      padding: 10px 20px;
      cursor: pointer;
      border-bottom: 2px solid transparent;
      transition: all 0.3s;
    }

    .profile-tab.active {
      border-bottom: 2px solid var(--success);
      color: var(--success);
    }

    /* Modal styles */
    .modal {
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      display: none;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: var(--bg-card);
      padding: 25px;
      border-radius: 10px;
      width: 400px;
      max-width: 90%;
      position: relative;
    }

    .close-modal {
      position: absolute;
      top: 15px;
      right: 15px;
      font-size: 24px;
      cursor: pointer;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
    }

    .form-group input, 
    .form-group select {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid rgba(255,255,255,0.2);
      background-color: rgba(255,255,255,0.1);
      color: white;
    }

    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
    }

    .alert-success {
      background-color: rgba(76, 201, 240, 0.2);
      border: 1px solid var(--success);
      color: var(--success);
    }

    .alert-error {
      background-color: rgba(247, 37, 133, 0.2);
      border: 1px solid var(--danger);
      color: var(--danger);
    }

    @media (max-width: 768px) {
      .profile-header {
        flex-direction: column;
        text-align: center;
      }
      
      .profile-avatar {
        margin: 0 auto 15px;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-user"></i> Perfil de Miembro</h1>
      <a href="index.php" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
      </a>
    </div>

    <!-- Mostrar mensajes -->
    <?php if (isset($_GET['payment_success'])): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Operación realizada con éxito
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <!-- Encabezado del perfil -->
    <div class="profile-header">
      <div class="profile-avatar">
        <?= strtoupper(substr($member['name'], 0, 1)) ?>
      </div>
      <div class="profile-info">
        <h2><?= htmlspecialchars($member['name']) ?></h2>
        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($member['phone']) ?></p>
        <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($member['email']) ?></p>
        <p>
          <span class="status-badge <?= $membership_status === 'Activa' ? 'status-active' : 'status-expired' ?>">
            <i class="fas fa-<?= $membership_status === 'Activa' ? 'check' : 'exclamation' ?>-circle"></i> 
            Membresía <?= $membership_status ?>
          </span>
        </p>
      </div>
    </div>

    <!-- Resumen rápido -->
    <div class="info-grid">
      <div class="info-card">
        <h3>Saldo Actual</h3>
        <div class="value">$<?= number_format($balance, 2) ?></div>
        <button onclick="showRechargeModal()" class="btn btn-success btn-sm" style="margin-top: 10px;">
          <i class="fas fa-money-bill-wave"></i> Recargar Saldo
        </button>
      </div>
      
      <div class="info-card">
        <h3>Membresía</h3>
        <div class="value"><?= htmlspecialchars($member['membership_name']) ?></div>
      </div>
      
      <div class="info-card">
        <h3>Estado</h3>
        <div class="value">
          <span class="status-badge <?= $membership_status === 'Activa' ? 'status-active' : 'status-expired' ?>">
            <?= $membership_status ?>
          </span>
        </div>
      </div>
      
      <div class="info-card">
        <h3>Días Restantes</h3>
        <div class="value">
          <?php if ($membership_status === 'Activa'): ?>
            <?= $days_remaining ?> día<?= $days_remaining != 1 ? 's' : '' ?>
          <?php else: ?>
            <span class="status-badge status-expired">
              <i class="fas fa-exclamation-triangle"></i> Vencida
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Detalles de membresía -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-id-card"></i> Detalles de Membresía</h2>
      
      <div class="info-grid">
        <div>
          <h3>Fecha de Inicio</h3>
          <p><?= date('d/m/Y', strtotime($member['start_date'])) ?></p>
        </div>
        
        <div>
          <h3>Fecha de Vencimiento</h3>
          <p><?= date('d/m/Y', strtotime($member['end_date'])) ?></p>
        </div>
        
        <div>
          <h3>Duración Total</h3>
          <p>
            <?= $member['duration_days'] ?> día<?= $member['duration_days'] != 1 ? 's' : '' ?>
            (<?= floor($member['duration_days']/30) ?> mes<?= floor($member['duration_days']/30) != 1 ? 'es' : '' ?>)
          </p>
        </div>
        
        <div>
          <h3>Acciones</h3>
          <div class="actions">
            <form method="post" action="process_payment.php">
              <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
              <input type="hidden" name="payment_type" value="membresia">
              <input type="hidden" name="amount" value="<?= $member['membership_price'] ?>">
              <input type="hidden" name="payment_method" value="efectivo">
              <input type="hidden" name="description" value="Renovación de <?= $member['membership_name'] ?>">
              
              <button type="submit" class="btn btn-primary btn-sm" 
                      onclick="return confirm('¿Confirmar renovación por $<?= number_format($member['membership_price'], 2) ?>?')">
                  <i class="fas fa-sync-alt"></i> Renovar
              </button>
            </form>
            <a href="#" class="btn btn-success btn-sm">
              <i class="fas fa-print"></i> Imprimir
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Historial de pagos -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-history"></i> Últimos Pagos</h2>
      
      <div class="table-responsive">
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
              <td><?= ucfirst($payment['payment_type']) ?></td>
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
      </div>
      
      <a href="payment_history.php?member_id=<?= $member_id ?>" class="btn btn-primary" style="margin-top: 15px;">
        <i class="fas fa-list"></i> Ver Historial Completo
      </a>
    </div>

    <!-- Productos adquiridos -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-shopping-bag"></i> Productos Adquiridos</h2>
      
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Producto</th>
              <th>Precio</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($products as $product): ?>
            <tr>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td>$<?= number_format($product['price'], 2) ?></td>
              <td><?= date('d/m/Y', strtotime($product['payment_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
            <tr>
              <td colspan="3" style="text-align: center;">No hay productos adquiridos</td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-bolt"></i> Acciones Rápidas</h2>
      
      <div class="actions" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="#" class="btn btn-success">
          <i class="fas fa-money-bill-wave"></i> Registrar Pago
        </a>
        <a href="edit_member.php?id=<?= $member_id ?>" class="btn btn-primary">
          <i class="fas fa-edit"></i> Editar Perfil
        </a>
        <a href="#" class="btn btn-danger">
          <i class="fas fa-envelope"></i> Enviar Recordatorio
        </a>
        <a href="#" class="btn btn-primary">
          <i class="fas fa-qrcode"></i> Generar QR
        </a>
      </div>
    </div>
  </div>

  <!-- Modal para recarga de saldo -->
  <div id="rechargeModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="hideRechargeModal()">&times;</span>
      <h3><i class="fas fa-money-bill-wave"></i> Recargar Saldo</h3>
      <form method="post" action="process_payment.php">
        <input type="hidden" name="member_id" value="<?= $member_id ?>">
        <input type="hidden" name="payment_type" value="recarga">
        
        <div class="form-group">
          <label for="amount">Monto a recargar:</label>
          <input type="number" id="amount" name="amount" step="0.01" min="1" required>
        </div>
        
        <div class="form-group">
          <label for="payment_method">Método de pago:</label>
          <select id="payment_method" name="payment_method" required>
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia</option>
          </select>
        </div>
        
        <button type="submit" class="btn btn-success">
          <i class="fas fa-check"></i> Confirmar Recarga
        </button>
      </form>
    </div>
  </div>

  <script>
    // Funciones para el modal de recarga
    function showRechargeModal() {
      document.getElementById('rechargeModal').style.display = 'flex';
    }

    function hideRechargeModal() {
      document.getElementById('rechargeModal').style.display = 'none';
    }

    // Cerrar modal al hacer clic fuera
    window.onclick = function(event) {
      if (event.target == document.getElementById('rechargeModal')) {
        hideRechargeModal();
      }
    }

    // Limpiar parámetros de URL después de mostrar mensajes
    <?php if (isset($_GET['payment_success']) || isset($_GET['error'])): ?>
      if (window.history.replaceState) {
        const cleanUrl = window.location.pathname + '?id=<?= $member_id ?>';
        window.history.replaceState({}, document.title, cleanUrl);
      }
    <?php endif; ?>
  </script>
</body>
</html>