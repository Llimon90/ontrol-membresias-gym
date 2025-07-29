<?php include 'conexion.php'; ?>
<form action="guardar.php" method="POST">
    <label>Nombre: <input type="text" name="nombre" required></label><br>
    <label>Inicio: <input type="date" name="inicio" required></label><br>
    <label>Fin: <input type="date" name="fin" required></label><br>
    <button type="submit">Guardar</button>
</form>
