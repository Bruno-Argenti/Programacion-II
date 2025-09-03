<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Controlador\DiscoControlador;
use Controlador\AuthControlador;
use Modelo\ConexionBD;
use Tuupola\Middleware\JwtAuthentication;
use Firebase\JWT\JWT;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// MIDDLEWARE JWT
$app->add(new JwtAuthentication([
    "path" => ["/discos"], // Rutas que protege 
    "secret" => "bruno123",
    "algorithm" => ["HS256"],
    "attribute" => "token",
    "secure" => false,
    "error" => function ($response, $arguments) {
        $data = ["error" => "Token inválido o no enviado"];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader("Content-Type", "application/json")->withStatus(401);
    }
]));

// ENDPOINT QUE AUTENTICA Y DA EL TOKEN AL USUARIO
$app->post('/login', function (Request $request, Response $response) {
    $db = (new ConexionBD())->getConexion();
    $auth = new AuthControlador($db);
    return $auth->login($request, $response);
});

// ENDPOINTS PARA CRUD (Protegidos por autenticación JWT)
$app->get('/discos', [DiscoControlador::class, 'mostrarDiscos']);
$app->get('/discos/{id}', [DiscoControlador::class, 'mostrarDisco']);
$app->post('/discos/alta/', [DiscoControlador::class, 'altaDisco']);
$app->put('/discos/modificar/{id}', [DiscoControlador::class, 'actualizarDisco']);
$app->delete('/discos/baja/{id}', [DiscoControlador::class, 'eliminarDisco']);


// Para manejo de errores
try {
    $app->run();
} catch (Throwable $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
