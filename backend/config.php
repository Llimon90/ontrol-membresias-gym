<?php
define('DB_SERVER','localhost');
define('DB_USERNAME','u179371012_231');
define('DB_PASSWORD','Gym2025*2025');
define('DB_NAME','u179371012_gimnasio');

try {
  $pdo = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME.";charset=utf8", DB_USERNAME, DB_PASSWORD);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  die("Error al conectar: ".$e->getMessage());
}
?>


<?php
// config.php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=u179371012_gimnasio;charset=utf8", "u179371012_231", "Gym2025*2025");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => "DB Error: ".$e->getMessage()]));
}
