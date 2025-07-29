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

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET name = :name, email = :email, phone = :phone, 
                  photo = :photo, membership_id = :membership_id, 
                  start_date = :start_date, end_date = :end_date, status = :status';

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->photo = htmlspecialchars(strip_tags($this->photo));
        $this->membership_id = htmlspecialchars(strip_tags($this->membership_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind data
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

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Otros métodos como update, delete, search, etc.
}
?>