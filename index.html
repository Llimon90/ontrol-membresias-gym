<?php
require 'config.php';

// borrar
if (isset($_GET['delete'])) {
  $del = $pdo->prepare("DELETE FROM members WHERE id = ?");
  $del->execute([$_GET['delete']]);
}

// editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
  $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
  $stmt->execute([$_POST['membership_id']]);
  $m = $stmt->fetch(PDO::FETCH_ASSOC);
  $end_date = date('Y-m-d', strtotime("{$_POST['start_date']} + {$m['duration_days']} days"));
  $upd = $pdo->prepare("UPDATE members SET name=?,email=?,phone=?,membership_id=?,start_date=?,end_date=? WHERE id=?");
  $upd->execute([$_POST['name'],$_POST['email'],$_POST['phone'],$_POST['membership_id'],$_POST['start_date'],$end_date,$_POST['edit_id']]);
  header('Location: index.php');
  exit;
}

$members = $pdo->query("SELECT m.*, ms.name AS mname FROM members m JOIN memberships ms ON m.membership_id=ms.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Miembros</title></head>
<body>
<a href="create.php">Agregar Miembro</a>
<table border="1">
<tr><th>Nombre</th><th>Membresía</th><th>Inicio</th><th>Fin</th><th>Acciones</th></tr>
<?php foreach($members as $u): ?>
<tr>
  <td><?=htmlspecialchars($u['name'])?></td>
  <td><?=htmlspecialchars($u['mname'])?></td>
  <td><?=htmlspecialchars($u['start_date'])?></td>
  <td><?=htmlspecialchars($u['end_date'])?></td>
  <td>
    <a href="?edit=<?= $u['id'] ?>">Editar</a> |
    <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('¿Eliminar?')">Eliminar</a>
  </td>
</tr>
<?php endforeach ?>
</table>

<?php if (isset($_GET['edit'])):
  $e = $pdo->prepare("SELECT * FROM members WHERE id=?");
  $e->execute([$_GET['edit']]);
  $u = $e->fetch(PDO::FETCH_ASSOC);
?>
<hr>
<h3>Editar Miembro</h3>
<form method="post">
  <input type="hidden" name="edit_id" value="<?= $u['id'] ?>">
  Nombre: <input name="name" value="<?=htmlspecialchars($u['name'])?>" required><br>
  Email: <input name="email" value="<?=htmlspecialchars($u['email'])?>"><br>
  Teléfono: <input name="phone" value="<?=htmlspecialchars($u['phone'])?>" required><br>
  Membresía:
  <select name="membership_id" required>
    <?php foreach($ms as $row): ?>
      <option value="<?=$row['id']?>" <?= $row['id']==$u['membership_id']?'selected':'' ?>><?=$row['name']?></option>
    <?php endforeach ?>
  </select><br>
  Inicio: <input type="date" name="start_date" value="<?= $u['start_date'] ?>" required><br>
  <button type="submit">Guardar cambios</button>
</form>
<?php endif; ?>
</body>
</html>
