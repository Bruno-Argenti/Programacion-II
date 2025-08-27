<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Controlador\DatosController;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Rutas API
$app->get('/discos', [DatosController::class, 'mostrarDiscos']);
$app->post('/alta/disco', [DatosController::class, 'altaDisco']);

$app->run();
