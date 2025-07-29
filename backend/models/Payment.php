<?php
class Payment {
    private $conn;
    private $table = 'payments';

    public $id;
    public $member_id;
    public $amount;
    public $payment_date;
    public $payment_method;
    public $receipt_number;
    public $notes;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los pagos
    public function read() {
        $query = 'SELECT p.*, m.name as member_name 
                  FROM ' . $this->table . ' p
                  LEFT JOIN members m ON p.member_id = m.id
                  ORDER BY p.payment_date DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener pagos por miembro
    public function read_by_member($member_id) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE member_id = ? 
                  ORDER BY payment_date DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $member_id);
        $stmt->execute();
        return $stmt;
    }

    // Crear un nuevo pago
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET member_id = :member_id, 
                      amount = :amount, 
                      payment_date = :payment_date, 
                      payment_method = :payment_method, 
                      receipt_number = :receipt_number, 
                      notes = :notes';
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->member_id = htmlspecialchars(strip_tags($this->member_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->payment_date = htmlspecialchars(strip_tags($this->payment_date));
        $this->payment_method = htmlspecialchars(strip_tags($this->payment_method));
        $this->receipt_number = htmlspecialchars(strip_tags($this->receipt_number));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        
        // Vincular parámetros
        $stmt->bindParam(':member_id', $this->member_id);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':payment_date', $this->payment_date);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':receipt_number', $this->receipt_number);
        $stmt->bindParam(':notes', $this->notes);
        
        if($stmt->execute()) {
            return true;
        }
        
        error_log("Error: " . $stmt->error);
        return false;
    }

    // Obtener ingresos mensuales
    public function get_monthly_income() {
        $query = 'SELECT 
                  YEAR(payment_date) as year, 
                  MONTH(payment_date) as month, 
                  COUNT(*) as payments_count, 
                  SUM(amount) as total_income
                  FROM ' . $this->table . ' 
                  GROUP BY YEAR(payment_date), MONTH(payment_date)
                  ORDER BY year DESC, month DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener ingresos del mes actual
    public function get_current_month_income() {
        $query = 'SELECT COUNT(*) as payments_count, SUM(amount) as total_income
                  FROM ' . $this->table . ' 
                  WHERE YEAR(payment_date) = YEAR(CURDATE()) 
                  AND MONTH(payment_date) = MONTH(CURDATE())';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }
}
?>