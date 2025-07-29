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

/* 1. Definición de variables de color */
:root {
  /* Colores por defecto (modo claro opcional) */
  --bg: #ffffff;
  --bg-secondary: #f0f0f0;
  --card: #ffffff;
  --text-primary: #2d2d2d;
  --text-secondary: #555555;
  --accent: #4a90e2;
  --success: #28a745;
  --danger: #dc3545;
}

/* 2. Modo oscuro automático */
@media (prefers-color-scheme: dark) {
  :root {
    --bg: #282a36;
    --bg-secondary: #44475a;
    --card: #373846;
    --text-primary: #f8f8f2;
    --text-secondary: #6272a4;
    --accent: #bd93f9;
    --success: #50fa7b;
    --danger: #ff5555;
  }
}

/* 3. Estilos base responsive */
*,
*:before,
*:after {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background-color: var(--bg);
  color: var(--text-primary);
  line-height: 1.5;
  padding: 1rem;
}

/* Contenedor centrado */
.container {
  max-width: 960px;
  margin: auto;
  padding: 1rem;
}

/* Tarjetas y secciones */
.card {
  background-color: var(--card);
  border-radius: 8px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Formularios */
.form-group {
  margin-bottom: 1rem;
}
label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--text-secondary);
}
input[type="text"],
input[type="email"],
input[type="date"],
select {
  width: 100%;
  padding: 0.75rem;
  background-color: var(--bg-secondary);
  color: var(--text-primary);
  border: 1px solid var(--accent);
  border-radius: 4px;
}
input:focus,
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(189, 147, 249, 0.4);
}

/* Botones */
.btn {
  display: inline-block;
  background-color: var(--accent);
  color: var(--text-primary);
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration: none;
}
.btn:hover {
  background-color: #9e7de5;
}

/* Botones pequeños */
.btn-small {
  font-size: 0.9rem;
  padding: 0.5rem 1rem;
}
.btn-delete {
  background-color: var(--danger);
}
.btn-delete:hover {
  background-color: #e14c4c;
}

/* Tablas responsivas */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
th, td {
  padding: 0.75rem;
  border-bottom: 1px solid var(--bg-secondary);
  text-align: left;
}
th {
  background-color: var(--bg-secondary);
}
tr:nth-child(even) {
  background-color: var(--card);
}

/* Etiquetas para móviles */
@media (max-width: 600px) {
  table, thead, tbody, th, td, tr {
    display: block;
  }
  thead {
    display: none;
  }
  tr {
    margin-bottom: 1rem;
    background-color: var(--card);
    border-radius: 4px;
    padding: 0.5rem;
  }
  td {
    position: relative;
    padding-left: 50%;
    text-align: right;
  }
  td::before {
    content: attr(data-label);
    position: absolute;
    left: 0.75rem;
    width: 45%;
    padding-right: 0.5rem;
    font-weight: bold;
    color: var(--text-secondary);
    text-align: left;
  }
}

/* Mensajes */
.text-success {
  color: var(--success);
}
.text-error {
  color: var(--danger);
}


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
