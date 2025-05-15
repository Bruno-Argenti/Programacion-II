<?php
public class rectangulo{

	public $largo;
	public $ancho;
}

	public function calcularArea() {
		return $this->largo * $this ->ancho;
}

$elRectangulo = new rectangulo;
$elRectangulo->largo = 5;
$elRectangulo->ancho = 3;


echo "El área del rectángulo es: ". $elRectangulo->calcularArea();

?>
