<?php

	require '../../autoload.php';

	error_reporting(0);
	// date_default_timezone_set("America/Santiago");

	$db = DB::getInstance();

	$equipo = array();
	$data   = $db->query("SELECT * FROM uman_camion WHERE NUMFLOTA != '0'");

	if($data->count()>0){		
		foreach($data->results() as $d){
			$id 			 = $d->ID_CAMION;
			$data_gps  = $db->query("SELECT * FROM uman_ultimogps WHERE ID_EQUIPO='$id'");
			$equipo[]  = $data_gps->results()[0];
		}
	}

	echo json_encode( $equipo );

?>