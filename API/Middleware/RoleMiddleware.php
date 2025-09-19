<?php
namespace Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;

class RoleMiddleware 
{
    private array $allowedRoles;

    public function __construct(array $allowedRoles) 
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function __invoke(Request $request, Handler $handler): Response 
    {
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
        
        // Verifica que el rol del usuario esté en los roles permitidos
        if (!in_array($userRole, $this->allowedRoles)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => 'Acceso denegado. No tiene permisos suficientes para esta acción'
            ], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        // Si el rol es válido, continúa al controlador
        return $handler->handle($request);
    }
}