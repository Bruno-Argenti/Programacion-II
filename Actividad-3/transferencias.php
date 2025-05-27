<?php
require("conexion-db.php");

$cuentaOrigen = 1;
$cuentaDestino = 999; //ID inexistente para probar rollback
$monto = 500;

try {
    $pdo->beginTransaction();

    // Restar de cuenta origen
    $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo - :monto WHERE id = :id");
    $stmt->execute([':monto' => $monto, ':id' => $cuentaOrigen]);

    // Sumar a cuenta destino 
    $stmt = $pdo->prepare("UPDATE cuentas SET saldo = saldo + :monto WHERE id = :id");
    $stmt->execute([':monto' => $monto, ':id' => $cuentaDestino]);

    $pdo->commit();
    echo "Transferencia realizada correctamente.";
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Error en la transferencia, cambios cancelados " . $e->getMessage();
}
?>
