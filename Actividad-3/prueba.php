<?php

require_once 'Database.php';

$db = new Database();
$db->createUser('roman.argenti@gmail.com', 'inactivo');

$usuario = $db->getUserByid(1);
print_r($usuario);

?>
