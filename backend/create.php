<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $membership_id = $_POST['membership_id'] ?? '';
  $start_date = $_POST['start_date'] ?? '';

  if (!$name || !$phone || !$membership_id || !$start_date) {
    $error = "Complete todos los campos requeridos.";
  } else {
    $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
    $stmt->execute([$membership_id]);
    $m = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$m) {
      $error = "Tipo de membresía no válido.";
    } else {
      $duration = $m['duration_days'];
      $end_date = date('Y-m-d', strtotime("$start_date + $duration days"));
      $insert = $pdo->prepare("INSERT INTO members (name,email,phone,membership_id,start_date,end_date) VALUES (?,?,?,?,?,?)");
      $insert->execute([$name,$email,$phone,$membership_id,$start_date,$end_date]);
      header('Location: index.php');
      exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Alta Miembro</title></head>
<body>
<h2>Agregar Miembro</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  Nombre: <input name="name" required><br>
  Email: <input name="email"><br>
  Teléfono: <input name="phone" required><br>
  Membresía:
  <select name="membership_id" required>
    <option value="">Seleccione...</option>
    <?php
    $ms = $pdo->query("SELECT * FROM memberships")->fetchAll();
    foreach($ms as $row) {
      echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
    }
    ?>
  </select><br>
  Fecha inicio: <input type="date" name="start_date" required><br>
  <button type="submit">Guardar</button>
</form>
<a href="index.php">Volver a lista</a>
</body>
</html>
