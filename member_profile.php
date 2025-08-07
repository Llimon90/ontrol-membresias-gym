<?php
require 'backend/config.php';

$member_id = $_GET['id'] ?? 0;

// Obtener información básica del miembro con duración de membresía
$member = $pdo->prepare("SELECT m.*, ms.name AS membership_name, ms.duration_days 
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
    
    // Calcular nueva fecha de vencimiento
    $new_end_date = clone $today;
    $new_end_date->add(new DateInterval("P{$duration_days}D"));
    
    // Actualizar en la base de datos
    $stmt = $pdo->prepare("UPDATE members SET start_date = ?, end_date = ? WHERE id = ?");
    $stmt->execute([
        $today->format('Y-m-d'),
        $new_end_date->format('Y-m-d'),
        $member_id
    ]);
    
    // Registrar el pago de renovación
    $stmt = $pdo->prepare("INSERT INTO payments (member_id, amount, payment_date, payment_type, description) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $member_id,
        0, // Monto - ajustar según tu lógica de precios
        $today->format('Y-m-d H:i:s'),
        'membresia',
        'Renovación automática de membresía ' . $member['membership_name']
    ]);
    
    // Redirigir para evitar reenvío del formulario
    header("Location: member_profile.php?id=$member_id&success=membership_renewed");
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

    /* Avatar circular */
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

    /* Tarjetas de información */
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

    /* Pestañas de perfil */
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

    /* Responsive para móviles */
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
        <a href="#" class="btn btn-success btn-sm" style="margin-top: 10px;">
          <i class="fas fa-money-bill-wave"></i> Recargar Saldo
        </a>
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

    <?php
// Al inicio del archivo, antes de cualquier HTML
require 'backend/config.php';

// Mostrar mensajes de éxito/error
if (isset($_GET['renewal_success'])) {
    echo '<div class="alert alert-success">Membresía renovada exitosamente!</div>';
} elseif (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">Error: '.htmlspecialchars($_GET['error']).'</div>';
}

// Obtener datos del miembro
$member_id = $_GET['member_id'] ?? null;
if ($member_id) {
    $stmt = $pdo->prepare("SELECT m.*, ms.duration_days FROM members m JOIN memberships ms ON m.membership_id = ms.id WHERE m.id = ?");
    $stmt->execute([$member_id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener últimos pagos
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE member_id = ? ORDER BY payment_date DESC LIMIT 5");
    $stmt->execute([$member_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

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
                <form method="post" action="process_payment.php" style="display: inline;">
                    <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
                    <input type="hidden" name="renew_membership" value="1">
                    <button type="submit" class="btn btn-primary btn-sm" 
                            onclick="return confirm('¿Confirmar renovación de membresía? La nueva fecha de vencimiento será <?= date('d/m/Y', strtotime($member['end_date'] . " + {$member['duration_days']} days")) ?>')">
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

  <script>
  // Mostrar mensaje de éxito si se renovó
  <?php if (isset($_GET['success']) && $_GET['success'] === 'membership_renewed'): ?>
    alert('Membresía renovada exitosamente. Nueva fecha de vencimiento: <?= date('d/m/Y', strtotime($member['end_date'])) ?>');
    window.history.replaceState({}, document.title, window.location.pathname + '?id=<?= $member_id ?>');
  <?php endif; ?>

  // Funcionalidad para recargar saldo
  document.querySelector('.btn-success').addEventListener('click', function(e) {
    e.preventDefault();
    const amount = prompt('Ingrese el monto a recargar:');
    if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
      // Aquí deberías hacer una llamada AJAX para procesar la recarga
      fetch('process_payment.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `member_id=<?= $member_id ?>&amount=${amount}&type=recarga`
      })
      .then(response => response.json())
      .then(data => {
        if(data.success) {
          alert(`Recarga de $${amount} realizada con éxito`);
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
  </script>
</body>
</html>