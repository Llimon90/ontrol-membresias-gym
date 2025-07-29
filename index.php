<?php
require 'backend/config.php';

// Obtener estadísticas
$stats = [
    'total_members' => $pdo->query("SELECT COUNT(*) FROM members")->fetchColumn(),
    'active_members' => $pdo->query("SELECT COUNT(*) FROM members WHERE end_date >= CURDATE()")->fetchColumn(),
    'expiring_members' => $pdo->query("SELECT COUNT(*) FROM members WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetchColumn(),
    'expired_members' => $pdo->query("SELECT COUNT(*) FROM members WHERE end_date < CURDATE()")->fetchColumn(),
    'popular_membership' => $pdo->query("SELECT ms.name, COUNT(m.id) as count FROM members m JOIN memberships ms ON m.membership_id=ms.id GROUP BY m.membership_id ORDER BY count DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC)
];

// Procesar formulario (igual que antes)
$error = '';
$editing = false;
$editId = 0;
$editData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $membership_id = $_POST['membership_id'];
    $start_date = $_POST['start_date'];
    if ($name && $phone && $membership_id && $start_date) {
        $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
        $stmt->execute([$membership_id]);
        $m = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($m) {
            $end_date = date('Y-m-d', strtotime("$start_date + {$m['duration_days']} days"));
            if (!empty($_POST['edit_id'])) {
                $upd = $pdo->prepare("UPDATE members SET name=?,email=?,phone=?,membership_id=?,start_date=?,end_date=? WHERE id=?");
                $upd->execute([$name,$email,$phone,$membership_id,$start_date,$end_date,$_POST['edit_id']]);
            } else {
                $ins = $pdo->prepare("INSERT INTO members (name,email,phone,membership_id,start_date,end_date)
                                     VALUES (?,?,?,?,?,?)");
                $ins->execute([$name,$email,$phone,$membership_id,$start_date,$end_date]);
            }
            header('Location: index.php');
            exit;
        } else {
            $error = 'Tipo de membresía no válido.';
        }
    } else {
        $error = '¡Por favor complete todos los campos obligatorios!';
    }
}

if (isset($_GET['edit'])) {
    $editing = true;
    $editId = intval($_GET['edit']);
    $s = $pdo->prepare("SELECT * FROM members WHERE id=?");
    $s->execute([$editId]);
    $editData = $s->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['delete'])) {
    $del = $pdo->prepare("DELETE FROM members WHERE id=?");
    $del->execute([intval($_GET['delete'])]);
    header('Location: index.php');
    exit;
}

$memberships = $pdo->query("SELECT * FROM memberships")->fetchAll(PDO::FETCH_ASSOC);
$members = $pdo->query("SELECT m.*, ms.name AS membership_name
                        FROM members m
                        JOIN memberships ms ON m.membership_id=ms.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Gimnasio | Gestión de Miembros</title>
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
      --text-primary: #a3a3a3ff;
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

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background-color: var(--bg-card);
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card h3 {
      font-size: 14px;
      color: var(--text-secondary);
      margin-bottom: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .stat-card .value {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .stat-card .label {
      display: flex;
      align-items: center;
      font-size: 14px;
      color: var(--text-secondary);
    }

    .stat-card .label i {
      margin-right: 8px;
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

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: var(--text-secondary);
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px 15px;
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 5px;
      color: var(--text-primary);
      font-size: 14px;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
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

    .text-error {
      color: var(--danger);
      font-size: 14px;
      margin-top: 5px;
    }

    .text-success {
      color: var(--success);
    }

    @media (max-width: 768px) {
      .stats-grid {
        grid-template-columns: 1fr 1fr;
      }
      
      .form-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 480px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      
      .header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .header h1 {
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1><i class="fas fa-dumbbell"></i> Dashboard Gimnasio</h1>
      <div>
        <span class="status-badge status-active">
          <i class="fas fa-circle"></i> <?= date('d/m/Y H:i') ?>
        </span>
      </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Miembros</h3>
        <div class="value"><?= $stats['total_members'] ?></div>
        <div class="label"><i class="fas fa-users"></i> Todos los registros</div>
      </div>
      
      <div class="stat-card">
        <h3>Miembros Activos</h3>
        <div class="value"><?= $stats['active_members'] ?></div>
        <div class="label"><i class="fas fa-check-circle"></i> Membresías vigentes</div>
      </div>
      
      <div class="stat-card">
        <h3>Por Vencer</h3>
        <div class="value"><?= $stats['expiring_members'] ?></div>
        <div class="label"><i class="fas fa-clock"></i> Vencen en 7 días</div>
      </div>
      
      <div class="stat-card">
        <h3>Vencidos</h3>
        <div class="value"><?= $stats['expired_members'] ?></div>
        <div class="label"><i class="fas fa-exclamation-circle"></i> Necesitan renovación</div>
      </div>
    </div>

    <!-- Formulario de miembros -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-user-plus"></i> <?= $editing ? 'Editar Miembro' : 'Agregar Nuevo Miembro' ?></h2>
      <?php if ($error): ?>
        <div class="text-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form method="post">
        <input type="hidden" name="edit_id" value="<?= $editing ? $editId : '' ?>">
        <div class="form-grid">
          <div class="form-group">
            <label>Nombre Completo *</label>
            <input name="name" value="<?= $editing ? htmlspecialchars($editData['name']) : '' ?>" required>
          </div>
          
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $editing ? htmlspecialchars($editData['email']) : '' ?>">
          </div>
          
          <div class="form-group">
            <label>Teléfono *</label>
            <input name="phone" value="<?= $editing ? htmlspecialchars($editData['phone']) : '' ?>" required>
          </div>
          
          <div class="form-group">
            <label>Tipo de Membresía *</label>
            <select name="membership_id" required>
              <option value="">Seleccione...</option>
              <?php foreach($memberships as $ms): ?>
                <option value="<?= $ms['id'] ?>" <?= $editing && $ms['id'] == $editData['membership_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($ms['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label>Fecha de Inicio *</label>
            <input type="date" name="start_date" value="<?= $editing ? $editData['start_date'] : '' ?>" required>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <?= $editing ? 'Actualizar Miembro' : 'Guardar Miembro' ?>
        </button>
        
        <?php if ($editing): ?>
          <a href="index.php" class="btn btn-danger" style="margin-left: 10px;">
            <i class="fas fa-times"></i> Cancelar
          </a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Listado de miembros -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-users"></i> Listado de Miembros</h2>
      
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Contacto</th>
              <th>Membresía</th>
              <th>Estado</th>
              <th>Vencimiento</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($members as $m): 
              $status = '';
              $today = new DateTime();
              $end_date = new DateTime($m['end_date']);
              $interval = $today->diff($end_date);
              
              if ($end_date < $today) {
                $status = 'status-expired';
                $status_text = 'Vencido';
              } elseif ($interval->days <= 7) {
                $status = 'status-expiring';
                $status_text = 'Por vencer';
              } else {
                $status = 'status-active';
                $status_text = 'Activo';
              }
            ?>
              <tr>
                <td><?= htmlspecialchars($m['id']) ?></td>
                <td>
    <a href="member_profile.php?id=<?= $m['id'] ?>" style="color: var(--success); text-decoration: none;">
        <i class="fas fa-user"></i> <?= htmlspecialchars($m['name']) ?>
    </a>
</td>
                <td>
                  <div><?= htmlspecialchars($m['phone']) ?></div>
                  <small style="color: var(--gray);"><?= htmlspecialchars($m['email']) ?></small>
                </td>
                <td><?= htmlspecialchars($m['membership_name']) ?></td>
                <td>
                  <span class="status-badge <?= $status ?>">
                    <?= $status_text ?>
                  </span>
                </td>
                <td><?= date('d/m/Y', strtotime($m['end_date'])) ?></td>
                <td>
                  <div class="actions">
                    <a href="?edit=<?= $m['id'] ?>" class="btn btn-primary btn-sm">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="?delete=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este miembro?')">
                      <i class="fas fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Sección de membresías por vencer -->
    <div class="card">
      <h2 class="card-title"><i class="fas fa-clock"></i> Membresías por Vencer (próximos 7 días)</h2>
      
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Membresía</th>
              <th>Días Restantes</th>
              <th>Fecha Vencimiento</th>
              <th>Contacto</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $expiring = $pdo->query("SELECT m.*, ms.name AS membership_name 
                                    FROM members m 
                                    JOIN memberships ms ON m.membership_id=ms.id 
                                    WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                                    ORDER BY end_date ASC")->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($expiring as $m): 
              $end_date = new DateTime($m['end_date']);
              $today = new DateTime();
              $interval = $today->diff($end_date);
              $days_left = $interval->days;
            ?>
              <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['membership_name']) ?></td>
                <td>
                  <span class="status-badge status-expiring">
                    <?= $days_left ?> día<?= $days_left != 1 ? 's' : '' ?>
                  </span>
                </td>
                <td><?= date('d/m/Y', strtotime($m['end_date'])) ?></td>
                <td>
                  <a href="tel:<?= htmlspecialchars($m['phone']) ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-phone"></i> Llamar
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
            
            <?php if (empty($expiring)): ?>
              <tr>
                <td colspan="5" style="text-align: center; color: var(--success);">
                  <i class="fas fa-check-circle"></i> No hay membresías por vencer en los próximos 7 días
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sección de Saldo y Pagos -->
<div class="card">
    <h2 class="card-title"><i class="fas fa-wallet"></i> Gestión de Pagos y Saldos</h2>
    
    <div class="tabs" style="margin-bottom: 20px;">
        <button class="tab-btn active" onclick="openTab(event, 'balance-tab')">Saldos</button>
        <button class="tab-btn" onclick="openTab(event, 'payment-tab')">Registrar Pago</button>
        <button class="tab-btn" onclick="openTab(event, 'products-tab')">Productos/Servicios</button>
    </div>
    
    <!-- Tab de Saldos -->
    <div id="balance-tab" class="tab-content" style="display: block;">
        <table>
            <thead>
                <tr>
                    <th>Miembro</th>
                    <th>Saldo Actual</th>
                    <th>Última Actualización</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $balances = $pdo->query("SELECT m.name, mb.balance, mb.last_updated, m.id 
                                        FROM member_balances mb
                                        JOIN members m ON mb.member_id = m.id
                                        ORDER BY mb.last_updated DESC")->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($balances as $balance): ?>
                <tr>
                    <td><?= htmlspecialchars($balance['name']) ?></td>
                    <td>$<?= number_format($balance['balance'], 2) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($balance['last_updated'])) ?></td>
                    <td>
                        <a href="#" onclick="showPaymentForm(<?= $balance['id'] ?>, '<?= htmlspecialchars($balance['name']) ?>')" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-money-bill-wave"></i> Recargar
                        </a>
                        <a href="payment_history.php?member_id=<?= $balance['id'] ?>" 
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-history"></i> Historial
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Tab de Registrar Pago -->
    <div id="payment-tab" class="tab-content" style="display: none;">
        <form id="payment-form" method="post" action="process_payment.php">
            <div class="form-grid">
                <div class="form-group">
                    <label>Miembro *</label>
                    <select name="member_id" id="payment-member" required>
                        <option value="">Seleccionar miembro...</option>
                        <?php foreach($members as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['phone']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Pago *</label>
                    <select name="payment_type" required>
                        <option value="membresia">Membresía</option>
                        <option value="producto">Producto/Servicio</option>
                        <option value="recarga">Recarga de Saldo</option>
                    </select>
                </div>
                
                <div class="form-group" id="product-group" style="display: none;">
                    <label>Producto/Servicio</label>
                    <select name="product_id">
                        <option value="">Seleccionar producto...</option>
                        <?php
                        $products = $pdo->query("SELECT * FROM gym_products WHERE is_active = TRUE")->fetchAll(PDO::FETCH_ASSOC);
                        foreach($products as $p): ?>
                        <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>">
                            <?= htmlspecialchars($p['name']) ?> ($<?= number_format($p['price'], 2) ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Monto *</label>
                    <input type="number" name="amount" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label>Método de Pago *</label>
                    <select name="payment_method" required>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Referencia/Descripción</label>
                    <input type="text" name="description" placeholder="Ej: Pago mensualidad enero">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Registrar Pago
            </button>
        </form>
    </div>
    
    <!-- Tab de Productos/Servicios -->
    <div id="products-tab" class="tab-content" style="display: none;">
        <button class="btn btn-success" style="margin-bottom: 15px;" onclick="showProductModal()">
            <i class="fas fa-plus"></i> Agregar Producto/Servicio
        </button>
        
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['description']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <span class="status-badge <?= $p['is_active'] ? 'status-active' : 'status-expired' ?>">
                            <?= $p['is_active'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editProduct(<?= $p['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="confirmDeleteProduct(<?= $p['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para recarga rápida -->
<div id="quickPaymentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('quickPaymentModal')">&times;</span>
        <h3>Recargar saldo a <span id="member-name"></span></h3>
        <form id="quick-payment-form" method="post" action="process_payment.php">
            <input type="hidden" name="member_id" id="quick-member-id">
            <input type="hidden" name="payment_type" value="recarga">
            
            <div class="form-group">
                <label>Monto *</label>
                <input type="number" name="amount" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label>Método de Pago *</label>
                <select name="payment_method" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-money-bill-wave"></i> Recargar Saldo
            </button>
        </form>
    </div>
</div>

<!-- Modal para productos -->
<div id="productModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal('productModal')">&times;</span>
        <h3 id="product-modal-title">Agregar Producto/Servicio</h3>
        <form id="product-form" method="post" action="process_product.php">
            <input type="hidden" name="product_id" id="product-id">
            
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Precio *</label>
                <input type="number" name="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" checked> Activo
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Producto
            </button>
        </form>
    </div>
</div>

<script>
// Funciones para las pestañas
function openTab(evt, tabName) {
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
    }
    
    const tabButtons = document.getElementsByClassName("tab-btn");
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].className = tabButtons[i].className.replace(" active", "");
    }
    
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

// Mostrar/ocultar campo de productos según tipo de pago
document.querySelector('select[name="payment_type"]').addEventListener('change', function() {
    const productGroup = document.getElementById('product-group');
    productGroup.style.display = this.value === 'producto' ? 'block' : 'none';
    
    // Si selecciona producto, actualizar el monto automáticamente
    if (this.value === 'producto') {
        document.querySelector('select[name="product_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                document.querySelector('input[name="amount"]').value = selectedOption.getAttribute('data-price');
            }
        });
    }
});

// Modal para recarga rápida
function showPaymentForm(memberId, memberName) {
    document.getElementById('quick-member-id').value = memberId;
    document.getElementById('member-name').textContent = memberName;
    document.getElementById('quickPaymentModal').style.display = 'block';
}

// Modal para productos
function showProductModal(productId = 0) {
    const modal = document.getElementById('productModal');
    const title = document.getElementById('product-modal-title');
    
    if (productId > 0) {
        // Aquí deberías hacer una llamada AJAX para obtener los datos del producto
        // y rellenar el formulario, o pasar los datos de otra forma
        title.textContent = 'Editar Producto';
        document.getElementById('product-id').value = productId;
        // Ejemplo de cómo rellenar (deberías obtener los datos reales):
        // document.querySelector('#product-form input[name="name"]').value = 'Nombre del producto';
    } else {
        title.textContent = 'Agregar Producto/Servicio';
        document.getElementById('product-id').value = '';
        document.getElementById('product-form').reset();
    }
    
    modal.style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function confirmDeleteProduct(productId) {
    if (confirm('¿Estás seguro de eliminar este producto?')) {
        // Aquí deberías hacer una llamada AJAX o redireccionar para eliminar
        window.location.href = 'delete_product.php?id=' + productId;
    }
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<style>
/* Estilos para los modales */
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
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: var(--text-primary);
}

/* Estilos para las pestañas */
.tabs {
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 20px;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    color: var(--text-secondary);
    font-weight: 500;
    transition: all 0.3s;
}

.tab-btn.active {
    color: var(--success);
    border-bottom: 2px solid var(--success);
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
</body>
</html>