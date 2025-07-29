<?php
class Member {
    private $conn;
    private $table = 'members';

    public $id;
    public $name;
    public $email;
    public $phone;
    public $photo;
    public $membership_id;
    public $start_date;
    public $end_date;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los miembros
    public function read() {
        $query = 'SELECT m.*, ms.name as membership_name 
                  FROM ' . $this->table . ' m
                  LEFT JOIN memberships ms ON m.membership_id = ms.id
                  ORDER BY m.name';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener un miembro por ID
    public function read_single() {
        $query = 'SELECT m.*, ms.name as membership_name 
                  FROM ' . $this->table . ' m
                  LEFT JOIN memberships ms ON m.membership_id = ms.id
                  WHERE m.id = ? LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->photo = $row['photo'];
            $this->membership_id = $row['membership_id'];
            $this->membership_name = $row['membership_name'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
        }
    }

    // Crear un nuevo miembro
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET name = :name, 
                      email = :email, 
                      phone = :phone, 
                      photo = :photo, 
                      membership_id = :membership_id, 
                      start_date = :start_date, 
                      end_date = :end_date, 
                      status = :status';
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->membership_id = htmlspecialchars(strip_tags($this->membership_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Vincular parámetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':photo', $this->photo);
        $stmt->bindParam(':membership_id', $this->membership_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        
        if($stmt->execute()) {
            return true;
        }
        
        error_log("Error: " . $stmt->error);
        return false;
    }

    // Actualizar miembro
    public function update() {
        $query = 'UPDATE ' . $this->table . ' 
                  SET name = :name, 
                      email = :email, 
                      phone = :phone, 
                      photo = :photo, 
                      membership_id = :membership_id, 
                      start_date = :start_date, 
                      end_date = :end_date, 
                      status = :status
                  WHERE id = :id';
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->membership_id = htmlspecialchars(strip_tags($this->membership_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Vincular parámetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':photo', $this->photo);
        $stmt->bindParam(':membership_id', $this->membership_id);
        $stmt->bindParam(':start_date', $this->start_date);
        $stmt->bindParam(':end_date', $this->end_date);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        error_log("Error: " . $stmt->error);
        return false;
    }

    // Eliminar miembro
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        error_log("Error: " . $stmt->error);
        return false;
    }

    // Buscar miembros por nombre
    public function search($keywords) {
        $query = 'SELECT m.*, ms.name as membership_name 
                  FROM ' . $this->table . ' m
                  LEFT JOIN memberships ms ON m.membership_id = ms.id
                  WHERE m.name LIKE ? 
                  ORDER BY m.name';
        
        $stmt = $this->conn->prepare($query);
        
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        $stmt->bindParam(1, $keywords);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener miembros con membresía próxima a vencer
    public function get_expiring_members($days = 7) {
        $query = 'SELECT m.*, ms.name as membership_name, 
                  DATEDIFF(m.end_date, CURDATE()) as days_left
                  FROM ' . $this->table . ' m
                  LEFT JOIN memberships ms ON m.membership_id = ms.id
                  WHERE m.status = "active" 
                  AND m.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                  ORDER BY m.end_date ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $days, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
    }

    // Contar miembros activos
    public function count_active_members() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE status = "active"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>