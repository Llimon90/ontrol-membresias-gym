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

// Procesar renovación o recarga si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Procesar renovación de membresía
        if (isset($_POST['renew_membership'])) {
            $today = new DateTime();
            $duration_days = $member['duration_days'];
            
            // Calcular nueva fecha de vencimiento
            $new_end_date = clone $today;
            $new_end_date->add(new DateInterval("P{$duration_days}D"));
            
            // Registrar el pago de renovación
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
                $member['membership_price'],
                'efectivo', // Método por defecto, puedes cambiarlo
                'membresia',
                'Renovación de membresía ' . $member['membership_name']
            ]);
            
            // Actualizar fechas de membresía
            $stmt = $pdo->prepare("UPDATE members SET start_date = ?, end_date = ? WHERE id = ?");
            $stmt->execute([
                $today->format('Y-m-d'),
                $new_end_date->format('Y-m-d'),
                $member_id
            ]);
            
            // Redirigir con éxito
            header("Location: member_profile.php?id=$member_id&success=membership_renewed");
            exit;
        }
        
        // Procesar recarga de saldo
        if (isset($_POST['recharge_balance'])) {
            $amount = (float)$_POST['amount'];
            $payment_method = $_POST['payment_method'] ?? 'efectivo';
            
            if ($amount <= 0) {
                throw new Exception("El monto debe ser mayor que cero");
            }
            
            // Registrar el pago de recarga
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
                'recarga',
                'Recarga de saldo'
            ]);
            
            // Actualizar el saldo del miembro
            $stmt = $pdo->prepare("
                INSERT INTO member_balances (member_id, balance) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE balance = balance + ?
            ");
            $stmt->execute([$member_id, $amount, $amount]);
            
            // Redirigir con éxito
            header("Location: member_profile.php?id=$member_id&success=balance_recharged");
            exit;
        }
        
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    
    // Redirigir con error
    header("Location: member_profile.php?id=$member_id&error=" . urlencode($error));
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
    /* [Estilos anteriores se mantienen igual] */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
    }
    
    .modal-content {
      background-color: var(--bg-card);
      margin: 10% auto;
      padding: 25px;
      border-radius: 10px;
      width: 400px;
      max-width: 90%;
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .close {
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
    
    .form-group input, .form-group select {
      width: 100%;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid rgba(255,255,255,0.2);
      background-color: rgba(255,255,255,0.05);
      color: white;
    }
    
    .alert {
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    
    .alert-success {
      background-color: rgba(76, 201, 240, 0.2);
      color: var(--success);
      border: 1px solid var(--success);
    }
    
    .alert-danger {
      background-color: rgba(247, 37, 133, 0.2);
      color: var(--danger);
      border: 1px solid var(--danger);
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Mostrar mensajes de éxito/error -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'membership_renewed'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Membresía renovada exitosamente!
      </div>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === 'balance_recharged'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Saldo recargado exitosamente!
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

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
          <h3>Duración Total</h3>
          <p>
            <?= $member['duration_days'] ?> día<?= $member['duration_days'] != 1 ? 's' : '' ?>
            (<?= floor($member['duration_days']/30) ?> mes<?= floor($member['duration_days']/30) != 1 ? 'es' : '' ?>)
          </p>
        </div>
        
        <div>
          <h3>Acciones</h3>
          <div class="actions">
            <form method="post" style="display: inline;">
              <input type="hidden" name="renew_membership" value="1">
              <button type="submit" class="btn btn-primary btn-sm" 
                      onclick="return confirm('¿Confirmar renovación de membresía por $<?= number_format($member['membership_price'], 2) ?>? La nueva fecha de vencimiento será <?= date('d/m/Y', strtotime($member['end_date'] . " + {$member['duration_days']} days")) ?>')">
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

    <!-- [Resto del código HTML se mantiene igual] -->

    <!-- Modal para recarga de saldo -->
    <div id="rechargeModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-money-bill-wave"></i> Recargar Saldo</h3>
          <span class="close">&times;</span>
        </div>
        <form method="post" id="rechargeForm">
          <div class="form-group">
            <label for="amount">Monto a recargar:</label>
            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
          </div>
          <div class="form-group">
            <label for="payment_method">Método de pago:</label>
            <select id="payment_method" name="payment_method" required>
              <option value="efectivo">Efectivo</option>
              <option value="tarjeta">Tarjeta</option>
              <option value="transferencia">Transferencia</option>
            </select>
          </div>
          <input type="hidden" name="recharge_balance" value="1">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check"></i> Confirmar Recarga
          </button>
        </form>
      </div>
    </div>

    <script>
      // Mostrar modal de recarga
      const rechargeBtn = document.getElementById('rechargeBtn');
      const modal = document.getElementById('rechargeModal');
      const closeBtn = document.querySelector('.close');
      
      rechargeBtn.onclick = function() {
        modal.style.display = 'block';
      }
      
      closeBtn.onclick = function() {
        modal.style.display = 'none';
      }
      
      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = 'none';
        }
      }
      
      // Validar formulario de recarga
      document.getElementById('rechargeForm').onsubmit = function(e) {
        const amount = parseFloat(document.getElementById('amount').value);
        if (isNaN(amount) || amount <= 0) {
          alert('Por favor ingrese un monto válido mayor que cero');
          return false;
        }
        return true;
      }
      
      // Mostrar mensaje de renovación exitosa
      <?php if (isset($_GET['success']) && $_GET['success'] === 'membership_renewed'): ?>
        alert('Membresía renovada exitosamente por $<?= number_format($member['membership_price'], 2) ?>. Nueva fecha de vencimiento: <?= date('d/m/Y', strtotime($member['end_date'] . " + {$member['duration_days']} days")) ?>');
      <?php endif; ?>
    </script>
  </div>
</body>
</html>