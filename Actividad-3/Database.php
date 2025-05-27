<?php

class Database {
    private $pdo;

    public function __construct(){
        $this->pdo = new PDO("mysql:host=localhost;dbname=Actividad-3", "root", "bruno123");
    } 
     public function createUser($email,$estado){
        $sql = "INSERT INTO usuarios (email, estado) VALUES (?, ?)";
        $stml = $this->pdo->prepare($sql);
        $stml->execute([$email, $estado]);
    }
    
    public function getUserByid($id){
        $sql = "SELECT * FROM usuarios WHERE id = ? ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>