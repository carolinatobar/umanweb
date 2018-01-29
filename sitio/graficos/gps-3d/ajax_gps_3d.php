<?php

include("../../conectar.php");
$faena 			= $_GET['faena'];
$link         	= Conectar( $faena );

error_reporting(0);
date_default_timezone_set("America/Santiago");

$equipo 		= 0;
$data_equípos 	= mysql_query("SELECT * FROM uman_camion WHERE NUMFLOTA != '0'");

while( $info_equipos = mysql_fetch_array( $data_equípos ) ) {

	$id 			= $info_equipos['ID_CAMION'];
	$data_gps 		= mysql_query("SELECT * FROM uman_ultimogps WHERE ID_EQUIPO='$id'");
	$data[$equipo] 	= mysql_fetch_array( $data_gps );

	mysql_free_result($data_gps);
	$equipo++;

}

echo json_encode( $data );

?>