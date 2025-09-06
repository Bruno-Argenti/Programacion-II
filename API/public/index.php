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

/* MIDDLEWARE JWT - Configurado para devolver array */
$app->add(new JwtAuthentication([
    "path" => ["/discos"], 
    "secret" => "bruno123", 
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

/* ENDPOINT PÚBLICO que usa Autenticación básica
 * Genera y retorna un token para autenticación JWT si las credenciales son correctas */
$app->post('/login', function (Request $request, Response $response) {
    $db = (new ConexionBD())->getConexion();
    $auth = new AuthControlador($db);
    return $auth->login($request, $response);
});

/* Endpoints que requieren autenticación JWT - Accesibles para TODOS los roles autenticados */
$app->get('/discos', [DiscoControlador::class, 'mostrarDiscos']);
$app->get('/discos/{id}', [DiscoControlador::class, 'mostrarDisco']);

/* ENDPOINTS CRUD Discos - Requieren autenticación JWT + rol ADMIN (1) */
$app->post('/discos/alta/', [DiscoControlador::class, 'altaDisco'])
   ->add(function (Request $request, $handler) {
       // Obtiene los datos del token (ya validado por JWT)
       $tokenData = $request->getAttribute('token');
       
       // Verifica que existan datos del token
       if (!$tokenData || !isset($tokenData['data']) || !isset($tokenData['data']['rol'])) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Token inválido o incompleto'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
       }

       // Obtiene el rol del usuario del token
       $userRole = $tokenData['data']['rol'];
       
       // Verifica que el rol del usuario sea ADMIN (1)
       if ($userRole !== 1) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Acceso denegado. Solo los administradores pueden crear discos'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
       }

       // Si el rol es válido, continúa al controlador
       return $handler->handle($request);
   });

$app->put('/discos/modificar/{id}', [DiscoControlador::class, 'actualizarDisco'])
   ->add(function (Request $request, $handler) {
       // Obtiene los datos del token (ya validado por JWT)
       $tokenData = $request->getAttribute('token');
       
       // Verifica que existan datos del token
       if (!$tokenData || !isset($tokenData['data']) || !isset($tokenData['data']['rol'])) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Token inválido o incompleto'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
       }

       // Obtiene el rol del usuario del token
       $userRole = $tokenData['data']['rol'];
       
       // Verifica que el rol del usuario sea ADMIN (1)
       if ($userRole !== 1) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Acceso denegado. Solo los administradores pueden modificar discos'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
       }

       // Si el rol es válido, continúa al controlador
       return $handler->handle($request);
   });

$app->delete('/discos/baja/{id}', [DiscoControlador::class, 'eliminarDisco'])
   ->add(function (Request $request, $handler) {
       // Obtiene los datos del token (ya validado por JWT)
       $tokenData = $request->getAttribute('token');
       
       // Verifica que existan datos del token
       if (!$tokenData || !isset($tokenData['data']) || !isset($tokenData['data']['rol'])) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Token inválido o incompleto'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
       }

       // Obtiene el rol del usuario del token
       $userRole = $tokenData['data']['rol'];
       
       // Verifica que el rol del usuario sea ADMIN (1)
       if ($userRole !== 1) {
           $response = new \Slim\Psr7\Response();
           $response->getBody()->write(json_encode([
               'error' => 'Acceso denegado. Solo los administradores pueden eliminar discos'
           ], JSON_UNESCAPED_UNICODE));
           return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
       }

       // Si el rol es válido, continúa al controlador
       return $handler->handle($request);
   });

/* Captura cualquier excepción no manejada y retorna un error JSON */
try {
    $app->run();
} catch (Throwable $e) {
    echo json_encode([
        "error" => "Error interno del servidor",
        "message" => $e->getMessage()
    ]);
}