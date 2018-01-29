<?php

require_once '../autoload.php';

$obj_eqp_n  	= new Equipo();
$arr_camion   	= $obj_eqp_n->listar();

foreach ( $arr_camion as $camion ) {
	if ( $camion->ID_CAJAUMAN != 0 ) {
			$obj_eqp_n->actualizarEstadoUmanBlue($camion);
	}

}

?>