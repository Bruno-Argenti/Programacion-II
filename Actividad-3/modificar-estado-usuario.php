<?php
require("conexion-db.php");

$nuevoEstado = "Activo"; 
$idUsuario = 1;          

try {
    $sql = "UPDATE usuarios SET estado = :estado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':estado' => $nuevoEstado,
        ':id' => $idUsuario
    ]);
    echo "Estado actualizado correctamente.";
} catch (PDOException $e) {
    echo "Error al actualizar estado: " . $e->getMessage();
}
?>
