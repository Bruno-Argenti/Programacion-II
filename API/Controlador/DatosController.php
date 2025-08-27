<?php
namespace Controlador;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modelo\DatosModel;

class DatosController {
    public function mostrarDiscos(Request $request, Response $response) {
        $model = new DatosModel();
        $data  = $model->getDiscos();
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function altaDisco(Request $request, Response $response) {
        $input = $request->getParsedBody();
        $model = new DatosModel();
        $data  = $model->insertarDisco($input);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
