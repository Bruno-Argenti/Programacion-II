<?php
namespace Modelo;

use PDO;
use PDOException;

class ConexionBD {
    private $host = "localhost";
    private $db_name = "api_disqueria";
    private $username = "root"; 
    private $password = "bruno123";     
    private $conn;

    public function __construct() {
        $this->conectar();
    }

    private function conectar() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion() {
        return $this->conn;
    }
}
