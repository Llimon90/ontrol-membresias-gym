<?php
include 'conexion.php';
$nombre = $_POST['nombre'];
$inicio = $_POST['inicio'];
$fin = $_POST['fin'];
$sql = "INSERT INTO membresias (nombre, inicio, fin) VALUES ('$nombre', '$inicio', '$fin')";
$conn->query($sql);
header('Location: ../public/index.html');
?>
