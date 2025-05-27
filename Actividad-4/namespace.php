<?php

require_once ('clases/Base/Persona.php');
require_once ('Modelos/Empleado.php');
require_once ('Modelos/Usuario.php');

use Modelos\Usuario;
use Modelos\Empleado;

$usuario = new Usuario;
$usuario->decirHola();

$empleado = new Empleado();
echo $empleado->saludar();
echo $empleado->trabajar();
