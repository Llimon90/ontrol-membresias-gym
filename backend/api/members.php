<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/Member.php';

$database = new Database();
$db = $database->connect();

$member = new Member($db);

// Obtener acción
$action = isset($_GET['action']) ? $_GET['action'] : die();

switch ($action) {
    case 'read':
        $result = $member->read();
        $num = $result->rowCount();

        if($num > 0) {
            $members_arr = array();
            
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                
                $member_item = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'photo' => $photo,
                    'membership_id' => $membership_id,
                    'membership_name' => $membership_name,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'status' => $status
                );
                
                array_push($members_arr, $member_item);
            }
            
            echo json_encode($members_arr);
        } else {
            echo json_encode(array('message' => 'No se encontraron miembros'));
        }
        break;
        
    case 'create':
        // Aquí iría el código para crear un nuevo miembro
        break;
        
    case 'update':
        // Aquí iría el código para actualizar un miembro
        break;
        
    case 'delete':
        // Aquí iría el código para eliminar un miembro
        break;
        
    default:
        echo json_encode(array('message' => 'Acción no válida'));
        break;
}
?>