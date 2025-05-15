<?php
$host = "localhost";      
$db   = 'Actividad-3';
$user = 'root';
$pass = 'bruno123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    echo " Error de conexión: " . $e->getMessage();
}

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Excepciones en caso de error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Fetch por defecto en array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                     // Sentencias preparadas reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Manejar error de conexión (log y mensaje genérico al usuario)
    error_log($e->getMessage());
    exit('Error al conectarse a la base de datos.');
}

?>
