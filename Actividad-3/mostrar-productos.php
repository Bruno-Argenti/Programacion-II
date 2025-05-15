<?php
require ("conexion-db.php");
  $stmt = $pdo->query("SELECT nombre, precio FROM productos ORDER BY id DESC");
  $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Se muestran los productos ordenados por ID des
    echo "\n\nLista de productos:\n";
    echo "---------------------------\n";
    foreach ($productos as $producto) {
        echo "Nombre: {$producto['nombre']} | Precio: {$producto['precio']}\n\n";
    }
?>
