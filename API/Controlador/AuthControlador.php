<?php
namespace Controlador;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthControlador
{
    private $db;
    private $secret;

    public function __construct($db)
    {
        $this->db = $db;
        $this->secret = "bruno123"; //Clave para firmar el token
    }

    public function login(Request $request, Response $response): Response
    {
        $headers = $request->getHeader('Authorization');
        if (empty($headers)) {
            return $this->unauthorized($response, "Falta el header Authorization");
        }

        $auth = $headers[0];
        if (strpos($auth, 'Basic ') !== 0) {
            return $this->unauthorized($response, "Formato de autenticación inválido");
        }

        $encoded = substr($auth, 6);
        $decoded = base64_decode($encoded);
        [$email, $password] = explode(':', $decoded);

        $stmt = $this->db->prepare("
            SELECT id_usuario, email_usuario, tipo_rol
            FROM usuarios
            WHERE email_usuario = :email AND password_usuario = :password");
        
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, \PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        $payload = [
            "iss" => "api.disqueria.local",
            "aud" => "api.disqueria.local",
            "iat" => time(),
            "exp" => time() + 3600,
            "data" => [
                "id_usuario" => $usuario['id_usuario'],
                "email" => $usuario['email_usuario'],
                "rol" => $usuario['tipo_rol']
            ]
        ];

        $token = JWT::encode($payload, $this->secret, 'HS256');
        $response->getBody()->write(json_encode(["token" => $token]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function unauthorized(Response $response, string $mensaje): Response
    {
        $response->getBody()->write(json_encode(["error" => $mensaje]));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}

