<?php
require_once '../config/database.php';

class MemberController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT m.*, ms.name as membership_name FROM members m JOIN memberships ms ON m.membership_id = ms.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("SELECT m.*, ms.name as membership_name FROM members m JOIN memberships ms ON m.membership_id = ms.id WHERE m.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        // Validar y procesar datos
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO members (name, email, phone, photo, membership_id, start_date, end_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['photo'] ?? 'default.jpg',
            $data['membership_id'],
            $data['start_date'],
            $data['end_date'],
            $data['status'] ?? 'active'
        ]);
    }

    // Más métodos para update, delete, etc.
}
?>