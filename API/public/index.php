<?php
// SOLO PARA DEBUG 
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Controlador\DiscoControlador;
use Controlador\BandaControlador;
use Controlador\AuthControlador;
use Modelo\ConexionBD;
use Middleware\RoleMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use Firebase\JWT\JWT;

require __DIR__ . '/../vendor/autoload.php';

//Se carga el archivo .env (variables de entorno)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

/* MIDDLEWARE JWT - Configurado para devolver array */
$app->add(new JwtAuthentication([
    "path" => ["/discos", "/bandas"], 
    "secret" => $_ENV['JWT_SECRET'], 
    "algorithm" => ["HS256"], 
    "attribute" => "token", 
    "secure" => false, // Sólo para desarrollo, ya que sólo se permite HTTPS
    "before" => function ($request, $arguments) {
        // Convierte el token de objeto a array después de decodificar
        $tokenData = $arguments["decoded"];
        $tokenArray = json_decode(json_encode($tokenData), true);
        return $request->withAttribute("token", $tokenArray);
    },
    "error" => function ($response, $arguments) {
        $data = ["error" => "Token inválido o no enviado"];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader("Content-Type", "application/json")->withStatus(401);
    }
]));


// RUTAS PÚBLICAS

/* ENDPOINT PÚBLICO - Login con Autenticación Básica

 * Genera y retorna un token para autenticación JWT si las credenciales son correctas */
$app->post('/login', function (Request $request, Response $response) {
    $db = (new ConexionBD())->getConexion();
    $auth = new AuthControlador($db);
    return $auth->login($request, $response);
});

// RUTAS PROTEGIDAS - LECTURA (Todos los roles autenticados)

/* Endpoints que requieren autenticación JWT - Accesibles para TODOS los roles autenticados */

    //Discos
$app->get('/discos', [DiscoControlador::class, 'mostrarDiscos']);
$app->get('/discos/{id}', [DiscoControlador::class, 'mostrarDisco']);

    //Bandas
$app->get('/bandas', [BandaControlador::class, 'mostrarBandas']);
$app->get('/bandas/{id}', [BandaControlador::class, 'mostrarBanda']);


// RUTAS PROTEGIDAS - ADMINISTRACIÓN (Solo ADMIN)

    /* ENDPOINTS CRUD Discos - Requieren autenticación JWT + rol ADMIN (1) */
$app->post('/discos/alta/', [DiscoControlador::class, 'altaDisco'])
   ->add(new RoleMiddleware([1])); 

$app->put('/discos/modificar/{id}', [DiscoControlador::class, 'actualizarDisco'])
   ->add(new RoleMiddleware([1])); 

$app->delete('/discos/baja/{id}', [DiscoControlador::class, 'eliminarDisco'])
   ->add(new RoleMiddleware([1])); 

   /* ENDPOINTS CRUD Discos - Requieren autenticación JWT + rol ADMIN (1) */
$app->post('/bandas/alta/', [BandaControlador::class, 'altaBanda'])
   ->add(new RoleMiddleware([1]));

$app->put('/bandas/modificar/{id}', [BandaControlador::class, 'actualizarBanda'])
   ->add(new RoleMiddleware([1]));

$app->delete('/bandas/baja/{id}', [BandaControlador::class, 'eliminarBanda'])
   ->add(new RoleMiddleware([1]));



/* Manejo de errores globales: Captura cualquier excepción no manejada y retorna un error en formato JSON */
try {
    $app->run();
} catch (Throwable $e) {
    echo json_encode([
        "error" => "Error interno del servidor",
        "message" => $e->getMessage()
    ]);
}