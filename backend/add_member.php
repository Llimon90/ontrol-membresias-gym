<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$db_host = 'localhost';
$db_name = 'u179371012_gimnasio';
$db_user = 'u179371012_231';
$db_pass = 'Gym2025*2025';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()]));
}

// Procesar los datos del formulario
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$membership_id = $_POST['membership_id'] ?? '';
$start_date = $_POST['start_date'] ?? '';

// Validar datos requeridos
if (empty($name) || empty($phone) || empty($membership_id) || empty($start_date)) {
    echo json_encode(['success' => false, 'message' => 'Faltan campos obligatorios']);
    exit;
}

// Obtener duración de la membresía para calcular la fecha de fin
try {
    $stmt = $pdo->prepare("SELECT duration_days FROM memberships WHERE id = ?");
    $stmt->execute([$membership_id]);
    $membership = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$membership) {
        echo json_encode(['success' => false, 'message' => 'Tipo de membresía no válido']);
        exit;
    }
    
    $duration_days = $membership['duration_days'];
    $end_date = date('Y-m-d', strtotime($start_date . " + $duration_days days"));
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al obtener información de la membresía']);
    exit;
}

// Procesar la foto si se subió
$photo_path = '';
if (!empty($_FILES['photo']['name'])) {
    $upload_dir = 'uploads/members/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES['photo']['name']);
    $target_file = $upload_dir . $file_name;
    
    // Validar y mover el archivo
    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
        $photo_path = $target_file;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al subir la foto']);
        exit;
    }
}

// Insertar en la base de datos
try {
    $stmt = $pdo->prepare("INSERT INTO members (name, email, phone, photo, membership_id, start_date, end_date, status) 
                          VALUES (:name, :email, :phone, :photo, :membership_id, :start_date, :end_date, 'active')");
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':photo', $photo_path);
    $stmt->bindParam(':membership_id', $membership_id);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Miembro agregado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()]);
}
?>