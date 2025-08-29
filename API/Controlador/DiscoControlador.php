<?php
namespace Controlador;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Modelo\ConexionBD;
use PDO;

class DiscoControlador {

    /* MOSTRAR COLECCIÓN DE DISCOS */
    public function mostrarDiscos(Request $request, Response $response) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->query("SELECT * FROM vista_discos");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /* MOSTRAR DISCO POR ID*/
    public function mostrarDisco(Request $request, Response $response, $args) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->prepare("SELECT * FROM vista_discos WHERE id_disc = ?");
        $stmt->execute([$args['id']]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "Disco no encontrado"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(404);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    /* ALTA DISCO */
    public function altaDisco(Request $request, Response $response) {
        $input = $request->getParsedBody();

        $db = (new ConexionBD())->getConexion();
        $sql = "INSERT INTO discos (nombre_disc, id_banda, precio_disc) VALUES (:nombre_disc, :id_banda, :precio_disc)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nombre_disc', $input['nombre_disc']);
        $stmt->bindValue(':id_banda', $input['id_banda'], PDO::PARAM_INT);
        $stmt->bindValue(':precio_disc', $input['precio_disc']);
        $stmt->execute();

        $nuevoId = $db->lastInsertId();
        $nuevo = $db->prepare("SELECT * FROM vista_discos WHERE id_disc = ?");
        $nuevo->execute([$nuevoId]);
        $data = $nuevo->fetch(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /* MODIFICAR DISCO POR ID */
    public function actualizarDisco(Request $request, Response $response, $args) {
        $input = $request->getParsedBody();

        $db = (new ConexionBD())->getConexion();
        $sql = "UPDATE discos SET nombre_disc = :nombre_disc, id_banda = :id_banda, precio_disc = :precio_disc 
                WHERE id_disc = :id_disc";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':nombre_disc', $input['nombre_disc']);
        $stmt->bindValue(':id_banda', $input['id_banda'], PDO::PARAM_INT);
        $stmt->bindValue(':precio_disc', $input['precio_disc']);
        $stmt->bindValue(':id_disc', $args['id'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $select = $db->prepare("SELECT * FROM vista_discos WHERE id_disc = ?");
            $select->execute([$args['id']]);
            $data = $select->fetch(PDO::FETCH_ASSOC);
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "No se pudo actualizar"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    /* ELIMINAR DISCO POR ID */
    public function eliminarDisco(Request $request, Response $response, $args) {
        $db = (new ConexionBD())->getConexion();
        $stmt = $db->prepare("DELETE FROM discos WHERE id_disc = ?");
        if ($stmt->execute([$args['id']])) {
            $response->getBody()->write(json_encode(["mensaje" => "Disco eliminado"], JSON_UNESCAPED_UNICODE));
        } else {
            $response->getBody()->write(json_encode(["error" => "No se pudo eliminar"], JSON_UNESCAPED_UNICODE));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
