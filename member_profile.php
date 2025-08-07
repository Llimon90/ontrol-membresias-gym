<?php
require 'backend/config.php';

session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$member_id = $_GET['id'] ?? 0;

// Obtener información completa del miembro
$member = $pdo->prepare("SELECT m.*, ms.name AS membership_name, ms.duration_days, ms.price 
                        FROM members m 
                        JOIN memberships ms ON m.membership_id = ms.id 
                        WHERE m.id = ?");
$member->execute([$member_id]);
$member = $member->fetch(PDO::FETCH_ASSOC);

if (!$member) {
    header('Location: index.php?error=member_not_found');
    exit;
}

// Procesar renovación si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['renew_membership'])) {
    $today = new DateTime();
    $duration_days = $member['duration_days'];
    $membership_price = $member['price'];
    $payment_amount = floatval($_POST['payment_amount'] ?? 0);
    
    // Validar el pago
    if ($payment_amount < $membership_price) {
        $error = "El monto mínimo para renovar es $" . number_format($membership_price, 2);
    } else {
        try {
            $pdo->beginTransaction();
            
            // Calcular nueva fecha de vencimiento
            $new_end_date = clone $today;
            $new_end_date->add(new DateInterval("P{$duration_days}D"));
            
            // Actualizar membresía
            $stmt = $pdo->prepare("UPDATE members SET start_date = ?, end_date = ? WHERE id = ?");
            $stmt->execute([
                $today->format('Y-m-d'),
                $new_end_date->format('Y-m-d'),
                $member_id
            ]);
            
            // Registrar el pago
            $stmt = $pdo->prepare("INSERT INTO payments (member_id, amount, payment_date, payment_type, description, user_id) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $member_id,
                $payment_amount,
                $today->format('Y-m-d H:i:s'),
                'membresia',
                'Renovación de ' . $member['membership_name'],
                $_SESSION['user_id']
            ]);
            
            // Actualizar saldo del miembro (si aplica)
            $stmt = $pdo->prepare("UPDATE member_balances SET balance = balance - ? WHERE member_id = ?");
            $stmt->execute([$payment_amount, $member_id]);
            
            $pdo->commit();
            
            // Redirigir para evitar reenvío del formulario
            header("Location: member_profile.php?id=$member_id&success=membership_renewed");
            exit;
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error al procesar la renovación: " . $e->getMessage();
        }
    }
}

// Obtener saldo actual del miembro
$balance = $pdo->prepare("SELECT balance FROM member_balances WHERE member_id = ?");
$balance->execute([$member_id]);
$balance = $balance->fetchColumn() ?? 0;

// Obtener historial de pagos recientes
$payments = $pdo->prepare("SELECT p.*, u.username AS user_name 
                          FROM payments p
                          LEFT JOIN users u ON p.user_id = u.id
                          WHERE p.member_id = ? 
                          ORDER BY p.payment_date DESC 
                          LIMIT 10");
$payments->execute([$member_id]);
$payments = $payments->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos adquiridos recientemente
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
  <title>Perfil de <?= htmlspecialchars($member['name']) ?> | Sistema Gimnasio</title>
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

    .btn-warning {
      background-color: var(--warning);
      color: white;
    }

    .btn-warning:hover {
      background-color: #e07e0c;
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
      gap: 10px;
      flex-wrap: wrap;
    }

    /* Modal de renovación */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: var(--bg-card);
      padding: 25px;
      border-radius: 10px;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .modal-header h3 {
      margin: 0;
      color: var(--success);
    }

    .close-modal {
      background: none;
      border: none;
      color: var(--text-secondary);
      font-size: 24px;
      cursor: pointer;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: var(--text-secondary);
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid #444;
      background-color: rgba(255,255,255,0.1);
      color: var(--text-primary);
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    /* Responsive */
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
      
      .actions {
        justify-content: center;
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
        <button id="rechargeBtn" class="btn btn-success btn-sm" style="margin-top: 10px;">
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
          <h3>Duración y Precio</h3>
          <p>
            <?= $member['duration_days'] ?> día<?= $member['duration_days'] != 1 ? 's' : '' ?>
            <br>
            <strong>$<?= number_format($member['price'], 2) ?></strong>
          </p>
        </div>
        
        <div>
          <h3>Acciones</h3>
          <div class="actions">
            <button id="renewBtn" class="btn btn-primary btn-sm">
              <i class="fas fa-sync-alt"></i> Renovar
            </button>
            <button class="btn btn-success btn-sm">
              <i class="fas fa-print"></i> Imprimir
            </button>
            <a href="edit_member.php?id=<?= $member_id ?>" class="btn btn-warning btn-sm">
              <i class="fas fa-edit"></i> Editar
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
              <th>Registrado por</th>
              <th>Descripción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($payments as $payment): ?>
            <tr>
              <td><?= date('d/m/Y H:i', strtotime($payment['payment_date'])) ?></td>
              <td>$<?= number_format($payment['amount'], 2) ?></td>
              <td><?= ucfirst($payment['payment_type']) ?></td>
              <td><?= htmlspecialchars($payment['user_name'] ?? 'Sistema') ?></td>
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
      
      <div class="actions">
        <button id="quickPaymentBtn" class="btn btn-success">
          <i class="fas fa-money-bill-wave"></i> Registrar Pago
        </button>
        <a href="edit_member.php?id=<?= $member_id ?>" class="btn btn-primary">
          <i class="fas fa-edit"></i> Editar Perfil
        </a>
        <button id="reminderBtn" class="btn btn-danger">
          <i class="fas fa-envelope"></i> Enviar Recordatorio
        </button>
        <button class="btn btn-primary">
          <i class="fas fa-qrcode"></i> Generar QR
        </button>
      </div>
    </div>
  </div>

  <!-- Modal de renovación -->
  <div id="renewModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-sync-alt"></i> Renovar Membresía</h3>
        <button class="close-modal">&times;</button>
      </div>
      
      <form id="renewForm" method="post">
        <input type="hidden" name="renew_membership" value="1">
        
        <div class="form-group">
          <label>Miembro</label>
          <input type="text" value="<?= htmlspecialchars($member['name']) ?>" readonly>
        </div>
        
        <div class="form-group">
          <label>Tipo de Membresía</label>
          <input type="text" value="<?= htmlspecialchars($member['membership_name']) ?>" readonly>
        </div>
        
        <div class="form-group">
          <label>Duración</label>
          <input type="text" value="<?= $member['duration_days'] ?> días" readonly>
        </div>
        
        <div class="form-group">
          <label>Precio</label>
          <input type="text" value="$<?= number_format($member['price'], 2) ?>" readonly>
        </div>
        
        <div class="form-group">
          <label for="payment_amount">Monto de Pago *</label>
          <input type="number" step="0.01" min="<?= $member['price'] ?>" 
                 name="payment_amount" id="payment_amount" 
                 value="<?= $member['price'] ?>" required>
        </div>
        
        <div class="form-group">
          <label>Nueva Fecha de Vencimiento</label>
          <input type="text" value="<?= date('d/m/Y', strtotime("+".$member['duration_days']." days")) ?>" readonly>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-danger close-modal">
            <i class="fas fa-times"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-check"></i> Confirmar Renovación
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal de recarga de saldo -->
  <div id="rechargeModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i class="fas fa-money-bill-wave"></i> Recargar Saldo</h3>
        <button class="close-modal">&times;</button>
      </div>
      
      <form id="rechargeForm">
        <div class="form-group">
          <label for="recharge_amount">Monto a Recargar *</label>
          <input type="number" step="0.01" min="0.01" id="recharge_amount" required>
        </div>
        
        <div class="form-group">
          <label for="recharge_method">Método de Pago *</label>
          <select id="recharge_method" required>
            <option value="">Seleccionar...</option>
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia</option>
          </select>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-danger close-modal">
            <i class="fas fa-times"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check"></i> Confirmar Recarga
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
  // Mostrar mensajes del servidor
  <?php if (isset($_GET['success']) && $_GET['success'] === 'membership_renewed'): ?>
    alert('Membresía renovada exitosamente. Nueva fecha de vencimiento: <?= date('d/m/Y', strtotime("+".$member['duration_days']." days")) ?>');
    window.history.replaceState({}, document.title, window.location.pathname + '?id=<?= $member_id ?>');
  <?php endif; ?>

  <?php if (isset($error)): ?>
    alert('<?= addslashes($error) ?>');
  <?php endif; ?>

  // Funcionalidad de los modales
  const renewModal = document.getElementById('renewModal');
  const rechargeModal = document.getElementById('rechargeModal');
  
  // Mostrar modal de renovación
  document.getElementById('renewBtn').addEventListener('click', () => {
    renewModal.style.display = 'flex';
    document.getElementById('payment_amount').focus();
  });
  
  // Mostrar modal de recarga
  document.getElementById('rechargeBtn').addEventListener('click', () => {
    rechargeModal.style.display = 'flex';
    document.getElementById('recharge_amount').focus();
  });
  
  // Cerrar modales
  document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      renewModal.style.display = 'none';
      rechargeModal.style.display = 'none';
    });
  });
  
  // Cerrar al hacer clic fuera del modal
  window.addEventListener('click', (e) => {
    if (e.target === renewModal) renewModal.style.display = 'none';
    if (e.target === rechargeModal) rechargeModal.style.display = 'none';
  });
  
  // Enviar recordatorio
  document.getElementById('reminderBtn').addEventListener('click', () => {
    if (confirm('¿Enviar recordatorio de pago a este miembro?')) {
      fetch('send_reminder.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `member_id=<?= $member_id ?>`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Recordatorio enviado con éxito');
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al enviar el recordatorio');
      });
    }
  });
  
  // Procesar recarga de saldo
  document.getElementById('rechargeForm').addEventListener('submit', (e) => {
    e.preventDefault();
    
    const amount = parseFloat(document.getElementById('recharge_amount').value);
    const method = document.getElementById('recharge_method').value;
    
    if (amount > 0 && method) {
      fetch('process_payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `member_id=<?= $member_id ?>&amount=${amount}&type=recarga&method=${method}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(`Recarga de $${amount.toFixed(2)} realizada con éxito`);
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la recarga');
      });
    }
  });
  
  // Registrar pago rápido
  document.getElementById('quickPaymentBtn').addEventListener('click', () => {
    const amount = prompt('Ingrese el monto del pago:');
    if (amount && !isNaN(amount) {
      fetch('process_payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `member_id=<?= $member_id ?>&amount=${amount}&type=otro`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(`Pago de $${amount} registrado con éxito`);
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al registrar el pago');
      });
    }
  });
  </script>
</body>
</html>