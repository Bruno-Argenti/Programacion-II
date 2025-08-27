<?php
namespace Modelo;

class DatosModel {
    public function getDiscos() {
        // Simulación de BD: datos fijos como si fueran un SELECT
        return [
            ["id" => 1, "Disco" => "Master Of Puppets", "Banda" => "Metallica", "precio" => 20000],
            ["id" => 2, "Disco" => "Dynasty", "Banda" => "Kiss", "precio" => 20000]
        ];
    }

    public function insertarDisco() {
        // Simulación de INSERT
        return [
            "id_disc"  => "3",
            "banda_disc" => "Use Your Ilussion I",
            "banda_nombre" => "Gun N Roses",
        ];
    }
}
