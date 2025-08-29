<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Controlador\DiscoControlador;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

//ENDPOINTS PARA ADMINISTRAR CRUD DISCOS
$app->get('/discos', [DiscoControlador::class, 'mostrarDiscos']);
$app->get('/discos/{id}', [DiscoControlador::class, 'mostrarDisco']);
$app->post('/discos/alta/', [DiscoControlador::class, 'altaDisco']);
$app->put('/discos/modificar/{id}', [DiscoControlador::class, 'actualizarDisco']);
$app->delete('/discos/baja/{id}', [DiscoControlador::class, 'eliminarDisco']);

$app->run();

