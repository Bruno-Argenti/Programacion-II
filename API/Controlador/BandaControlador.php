<?php
namespace Controlador;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modelo\ConexionBD;
use PDO;

class BandaControlador {

    /* MOSTRAR TODAS LAS BANDAS */
    public function mostrarBandas(Request $request, Response $response) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->query("SELECT * FROM bandas");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /* MOSTRAR BANDA POR ID */
    public function mostrarBanda(Request $request, Response $response, $args) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->prepare("SELECT * FROM bandas WHERE id_band = ?");
        $stmt->execute([$args['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "Banda no encontrada"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(404);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /* ALTA BANDA */
    public function altaBanda(Request $request, Response $response) {
        $input = $request->getParsedBody();

        $db = (new ConexionBD())->getConexion();
        $sql = "INSERT INTO bandas (nombre_band) VALUES (:nombre_band)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nombre_band', $input['nombre_band']);
        $stmt->execute();

        $nuevoId = $db->lastInsertId();
        $nuevo = $db->prepare("SELECT * FROM bandas WHERE id_band = ?");
        $nuevo->execute([$nuevoId]);
        $data = $nuevo->fetch(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /* MODIFICAR BANDA POR ID */
    public function actualizarBanda(Request $request, Response $response, $args) {
        $input = $request->getParsedBody();

        $db = (new ConexionBD())->getConexion();
        $sql = "UPDATE bandas SET nombre_band = :nombre_band WHERE id_band = :id_band";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nombre_band', $input['nombre_band']);
        $stmt->bindValue(':id_band', $args['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $select = $db->prepare("SELECT * FROM bandas WHERE id_band = ?");
            $select->execute([$args['id']]);
            $data = $select->fetch(PDO::FETCH_ASSOC);
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "No se pudo actualizar"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    /* ELIMINAR BANDA POR ID */
    public function eliminarBanda(Request $request, Response $response, $args) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->prepare("DELETE FROM bandas WHERE id_band = ?");
        if ($stmt->execute([$args['id']])) {
            $response->getBody()->write(json_encode(["mensaje" => "Banda eliminada"], JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "No se pudo eliminar"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
