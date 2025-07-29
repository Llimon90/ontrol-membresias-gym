<?php
require 'backend/config.php';

$error = '';
$editing = false;
$editId = 0;
$editData = null;

// Procesar Create o Update
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

// Preparar edición
if (isset($_GET['edit'])) {
    $editing = true;
    $editId = intval($_GET['edit']);
    $s = $pdo->prepare("SELECT * FROM members WHERE id=?");
    $s->execute([$editId]);
    $editData = $s->fetch(PDO::FETCH_ASSOC);
}

// Eliminar
if (isset($_GET['delete'])) {
    $del = $pdo->prepare("DELETE FROM members WHERE id=?");
    $del->execute([intval($_GET['delete'])]);
    header('Location: index.php');
    exit;
}

// Obtener listas
$memberships = $pdo->query("SELECT * FROM memberships")->fetchAll(PDO::FETCH_ASSOC);
$members = $pdo->query("SELECT m.*, ms.name AS membership_name
                        FROM members m
                        JOIN memberships ms ON m.membership_id=ms.id")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF‑8">
  <meta name="viewport" content="width=device‑width,initial‑scale=1">
  <title>Gestión Miembros | CRUD PHP</title>
  <style>
    body { font-family: Arial, sans-serif; margin:20px; background:#f4f4f4; }
    .form-group { margin-bottom:10px; }
    label{display:block;margin-bottom:5px;}
    input, select{width:100%;padding:8px;}
    .error { color:#a00; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th,td{padding:8px;border:1px solid #ccc;}
    th { background:#eee; }
    a { text-decoration:none;color:#069; }
    a:hover { text-decoration:underline; }
    .btn { padding:6px 12px; margin-right:5px; background:#069;color:#fff;border:none;border-radius:4px;cursor:pointer; }
    .btn-delete { background:#c22; }
  </style>
</head>
<body>
  <h1>Gestión de Miembros</h1>
  <?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>

  <form method="post">
    <input type="hidden" name="edit_id" value="<?= $editing ? $editId : '' ?>">
    <div class="form-group"><label>Nombre *</label>
      <input name="name" value="<?= $editing ? htmlspecialchars($editData['name']) : '' ?>" required></div>
    <div class="form-group"><label>Email</label>
      <input type="email" name="email" value="<?= $editing ? htmlspecialchars($editData['email']) : '' ?>"></div>
    <div class="form-group"><label>Teléfono *</label>
      <input name="phone" value="<?= $editing ? htmlspecialchars($editData['phone']) : '' ?>" required></div>
    <div class="form-group"><label>Membresía *</label>
      <select name="membership_id" required>
        <option value="">Seleccione...</option>
        <?php foreach($memberships as $ms): ?>
          <option value="<?=$ms['id']?>" <?=$editing && $ms['id']==$editData['membership_id']?'selected':''?>>
            <?=htmlspecialchars($ms['name'])?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group"><label>Fecha de inicio *</label>
      <input type="date" name="start_date" value="<?= $editing ? $editData['start_date'] : '' ?>" required></div>
    <button type="submit" class="btn"><?= $editing ? 'Actualizar miembro' : 'Agregar miembro' ?></button>
    <?php if ($editing): ?><a href="index.php">Cancelar edición</a><?php endif; ?>
  </form>

  <h2>Listado de Miembros</h2>
  <table>
    <thead><tr>
      <th>ID</th><th>Nombre</th><th>Teléfono</th>
      <th>Membresía</th><th>Inicio</th><th>Fin</th><th>Acciones</th>
    </tr></thead>
    <tbody>
    <?php foreach($members as $m): ?>
      <tr>
        <td><?=htmlspecialchars($m['id'])?></td>
        <td><?=htmlspecialchars($m['name'])?></td>
        <td><?=htmlspecialchars($m['phone'])?></td>
        <td><?=htmlspecialchars($m['membership_name'])?></td>
        <td><?=htmlspecialchars($m['start_date'])?></td>
        <td><?=htmlspecialchars($m['end_date'])?></td>
        <td>
          <a class="btn" href="?edit=<?= $m['id'] ?>">Editar</a>
          <a class="btn btn-delete" href="?delete=<?= $m['id'] ?>" onclick="return confirm('¿Eliminar este miembro?')">Eliminar</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
