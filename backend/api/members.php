<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/Member.php';

// Inicializar la base de datos y conectar
$database = new Database();
$db = $database->connect();

// Inicializar el objeto Member
$member = new Member($db);

// Obtener el método de la solicitud
$method = $_SERVER['REQUEST_METHOD'];

// Procesar la solicitud según el método
switch ($method) {
    case 'GET':
        // Obtener parámetros
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $days = isset($_GET['days']) ? $_GET['days'] : 7;
        
        if ($action === 'single' && $id) {
            // Obtener un solo miembro
            $member->id = $id;
            $member->read_single();
            
            if($member->name) {
                $member_arr = array(
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'photo' => $member->photo,
                    'membership_id' => $member->membership_id,
                    'membership_name' => $member->membership_name,
                    'start_date' => $member->start_date,
                    'end_date' => $member->end_date,
                    'status' => $member->status,
                    'created_at' => $member->created_at
                );
                
                echo json_encode($member_arr);
            } else {
                echo json_encode(array('message' => 'Miembro no encontrado'));
            }
        } 
        elseif ($action === 'expiring') {
            // Obtener miembros próximos a vencer
            $stmt = $member->get_expiring_members($days);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $members_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $member_item = array(
                        'id' => $id,
                        'name' => $name,
                        'membership_name' => $membership_name,
                        'end_date' => $end_date,
                        'days_left' => $days_left
                    );
                    
                    array_push($members_arr, $member_item);
                }
                
                echo json_encode($members_arr);
            } else {
                echo json_encode(array('message' => 'No hay miembros con membresía próxima a vencer'));
            }
        }
        elseif (!empty($search)) {
            // Buscar miembros
            $stmt = $member->search($search);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $members_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        }
        else {
            // Obtener todos los miembros
            $stmt = $member->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $members_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        }
        break;
        
    case 'POST':
        // Crear un nuevo miembro
        $data = json_decode(file_get_contents("php://input"));
        
        $member->name = $data->name;
        $member->email = $data->email;
        $member->phone = $data->phone;
        $member->photo = $data->photo ?? 'default.jpg';
        $member->membership_id = $data->membership_id;
        $member->start_date = $data->start_date;
        $member->end_date = $data->end_date;
        $member->status = $data->status ?? 'active';
        
        if($member->create()) {
            echo json_encode(array('success' => true, 'message' => 'Miembro creado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al crear miembro'));
        }
        break;
        
    case 'PUT':
        // Actualizar miembro
        $data = json_decode(file_get_contents("php://input"));
        
        $member->id = $data->id;
        $member->name = $data->name;
        $member->email = $data->email;
        $member->phone = $data->phone;
        $member->photo = $data->photo;
        $member->membership_id = $data->membership_id;
        $member->start_date = $data->start_date;
        $member->end_date = $data->end_date;
        $member->status = $data->status;
        
        if($member->update()) {
            echo json_encode(array('success' => true, 'message' => 'Miembro actualizado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al actualizar miembro'));
        }
        break;
        
    case 'DELETE':
        // Eliminar miembro
        $data = json_decode(file_get_contents("php://input"));
        
        $member->id = $data->id;
        
        if($member->delete()) {
            echo json_encode(array('success' => true, 'message' => 'Miembro eliminado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al eliminar miembro'));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array('message' => 'Método no permitido'));
        break;
}
?>