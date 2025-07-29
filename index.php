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
/* styles.css */

/* Paleta basada en tema Dracula */
:root {
  --bg-primary: #282a36;
  --bg-secondary: #44475a;
  --bg-card: #373846;
  --text-primary: #f8f8f2;
  --text-secondary: #6272a4;
  --accent-green: #50fa7b;
  --accent-orange: #ffb86c;
  --accent-pink: #ff79c6;
  --accent-purple: #bd93f9;
}

/* Forzar esquema oscuro preferido */
:root {
  color-scheme: dark;
}

/* Base */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  background-color: var(--bg-primary);
  color: var(--text-primary);
  font-family: 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
  line-height: 1.5;
  padding: 20px;
}

/* Tipografía clara y legible en móviles y desktop */
h1, h2, h3 {
  color: var(--text-primary);
  margin-bottom: 1rem;
}

p, label {
  color: var(--text-secondary);
}

/* Contenedores responsivos */
.container {
  max-width: 900px;
  margin: auto;
  padding: 10px;
}

/* Tarjetas */
.card {
  background-color: var(--bg-card);
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.4);
}

/* Formularios */
form {
  width: 100%;
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 5px;
}
input[type="text"],
input[type="email"],
input[type="date"],
select {
  width: 100%;
  padding: 10px;
  background-color: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--text-secondary);
  border-radius: 4px;
}
input:focus, select:focus {
  outline: none;
  border-color: var(--accent-green);
}

/* Botones vistosos */
.btn {
  display: inline-block;
  background-color: var(--accent-orange);
  color: var(--text-primary);
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color .3s ease;
}
.btn:hover {
  background-color: var(--accent-pink);
}

/* Tablas responsive */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
th, td {
  padding: 12px;
  border-bottom: 1px solid var(--bg-secondary);
  text-align: left;
}
th {
  background-color: var(--bg-secondary);
}
tr:nth-child(even) {
  background-color: var(--bg-card);
}
/* Acción botones en tabla */
.btn-small {
  padding: 6px 10px;
  font-size: 0.9rem;
}
.btn-delete {
  background-color: #ff5555;
}
.btn-delete:hover {
  background-color: #ff4444;
}

/* Responsive: columnas al vuelo */
@media (max-width: 600px) {
  body {
    padding: 10px;
    font-size: 15px;
  }
  table, thead, tbody, th, td, tr {
    display: block;
  }
  thead {
    display: none;
  }
  tr {
    margin-bottom: 1rem;
    background: var(--bg-card);
    padding: 10px;
    border-radius: 4px;
  }
  td {
    padding: 8px;
    position: relative;
  }
  td::before {
    content: attr(data-label);
    position: absolute;
    left: 8px;
    top: 8px;
    font-weight: bold;
    color: var(--text-secondary);
  }
}

/* Mensajes */
.text-success { color: var(--accent-green); }
.text-error { color: var(--accent-pink); }

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
