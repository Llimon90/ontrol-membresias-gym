<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../controllers/MemberController.php';

$memberController = new MemberController();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $member = $memberController->getById($_GET['id']);
            echo json_encode($member);
        } else {
            $members = $memberController->getAll();
            echo json_encode($members);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $memberController->create($data);
        echo json_encode(['success' => $result]);
        break;
    case 'PUT':
        // Implementar actualización
        break;
    case 'DELETE':
        // Implementar eliminación
        break;
}
?>