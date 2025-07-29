<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../models/Payment.php';

$database = new Database();
$db = $database->connect();

$payment = new Payment($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $member_id = isset($_GET['member_id']) ? $_GET['member_id'] : null;
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        if ($member_id) {
            // Obtener pagos por miembro
            $stmt = $payment->read_by_member($member_id);
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $payments_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $payment_item = array(
                        'id' => $id,
                        'amount' => $amount,
                        'payment_date' => $payment_date,
                        'payment_method' => $payment_method,
                        'receipt_number' => $receipt_number,
                        'notes' => $notes,
                        'created_at' => $created_at
                    );
                    
                    array_push($payments_arr, $payment_item);
                }
                
                echo json_encode($payments_arr);
            } else {
                echo json_encode(array('message' => 'No se encontraron pagos para este miembro'));
            }
        }
        elseif ($action === 'monthly') {
            // Obtener ingresos mensuales
            $stmt = $payment->get_monthly_income();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $income_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $income_item = array(
                        'year' => $year,
                        'month' => $month,
                        'payments_count' => $payments_count,
                        'total_income' => $total_income
                    );
                    
                    array_push($income_arr, $income_item);
                }
                
                echo json_encode($income_arr);
            } else {
                echo json_encode(array('message' => 'No se encontraron registros de pagos'));
            }
        }
        elseif ($action === 'current_month') {
            // Obtener ingresos del mes actual
            $income = $payment->get_current_month_income();
            echo json_encode($income);
        }
        else {
            // Obtener todos los pagos
            $stmt = $payment->read();
            $num = $stmt->rowCount();
            
            if($num > 0) {
                $payments_arr = array();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    
                    $payment_item = array(
                        'id' => $id,
                        'member_id' => $member_id,
                        'member_name' => $member_name,
                        'amount' => $amount,
                        'payment_date' => $payment_date,
                        'payment_method' => $payment_method,
                        'receipt_number' => $receipt_number,
                        'notes' => $notes,
                        'created_at' => $created_at
                    );
                    
                    array_push($payments_arr, $payment_item);
                }
                
                echo json_encode($payments_arr);
            } else {
                echo json_encode(array('message' => 'No se encontraron pagos'));
            }
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        $payment->member_id = $data->member_id;
        $payment->amount = $data->amount;
        $payment->payment_date = $data->payment_date;
        $payment->payment_method = $data->payment_method;
        $payment->receipt_number = $data->receipt_number ?? null;
        $payment->notes = $data->notes ?? null;
        
        if($payment->create()) {
            echo json_encode(array('success' => true, 'message' => 'Pago registrado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al registrar pago'));
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        
        $payment->id = htmlspecialchars(strip_tags($data->id));
        $stmt->bindParam(':id', $payment->id);
        
        if($stmt->execute()) {
            echo json_encode(array('success' => true, 'message' => 'Pago eliminado correctamente'));
        } else {
            echo json_encode(array('success' => false, 'message' => 'Error al eliminar pago'));
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(array('message' => 'Método no permitido'));
        break;
}
?>