<?php

public class coche{
	public $marca;
	public $modelo;
	public $color;

public function detalles(){

	echo "información del coche: {this->marca}, {this->modelo}, {this->color} "

   }

}


$auto = new coche();
$auto->marca = "Honda";
$auto->modelo = "2015";
$auto->color = "Negro";
$auto->detalles();

?>
