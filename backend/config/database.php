<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'u179371012_gimnasio';
    private $username = 'u179371012_231';
    private $password = 'Gym2025*2025';
    private $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES 'utf8'");
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            die(json_encode(['success' => false, 'message' => 'Error de conexión con la base de datos']));
        }

        return $this->conn;
    }
}
?>