<?php
require ("conexion-db.php");
$sql = "INSERT INTO productos (nombre, precio) VALUES (:nombre, :precio)";
  $stmt = $pdo->prepare($sql);
  
  $productos = [
      ['nombre' => 'Alfajor', 'precio' => 100],
      ['nombre' => 'Agua', 'precio' => 1000],
      ['nombre' => 'Arroz', 'precio' => 3000],
  ];

  foreach ($productos as $producto) {
    $stmt->execute([
        ':nombre' => $producto['nombre'],
        ':precio' => $producto['precio'],
    ]);    
  }
  ?>