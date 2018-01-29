<?php
	require 'autoload.php';

	$acc = new Acceso(true);

	$db = DB::getInstance();

	if ( isset ( $_GET['modo'] ) ) {
		$modo = $_GET['modo'];
	} else  {
		header("Location: ./index.php?ERROR");
	}

	if ( $modo == "sensor" ) {

		$cod_sensor = $_GET['sensor'];
		$db->query("DELETE FROM uman_sensores WHERE CODSENSOR='".$cod_sensor."'");
		
		header("Location: ./index.php?s=ingresar-sensores&borrado");

	} else 	if ( $modo == "sim" ) {

		$telefono = $_GET['telefono'];
		$db->query("DELETE FROM uman_sim WHERE TELEFONO='".$telefono."'");

		header("Location: ./index.php?s=sim&borrado");

	} else 	if ( $modo == "neumaticos" ) {

		$id = $_GET['id'];
		$db->query("DELETE FROM uman_neumaticos WHERE ID_NEUMATICO='".$id."'");

		header("Location: ./index.php?s=neumaticos&borrado");

	} else 	if ( $modo == "plantillas" ) {

		$id 	= $_GET['id'];
		$db->query("DELETE FROM uman_plantilla WHERE ID_PLANTILLA='".$id."'");
		header("Location: ./index.php?s=plantillas&borrado");

	} else 	if ( $modo == "umanblue" ) {

		$id = $_GET['id'];
		$datos = $db->query("SELECT ID_SIM FROM uman_cajauman WHERE ID_CAJAUMAN='$id'");
		$info  = $datos->count() > 0 ? $datos->results()[0] : NULL;

		$db->query("DELETE FROM uman_cajauman WHERE ID_CAJAUMAN='".$id."'");
		
		if($info != NULL){
			$sim = $info->ID_SIM;
			$db->query("UPDATE uman_sim SET ESTADO='Disponible' WHERE ID='$sim'");
		}

		header("Location: ./index.php?s=umanblue&borrado");

	} else 	if ( $modo == "libreria" ) {

		$id 	= $_GET['id'];
		$datos 	= $db->query("SELECT archivo FROM documentos WHERE id='$id'");
		$info  	= $datos->count() > 0 ? $datos->results()[0] : NULL;

		if($info!=NULL){
			$archivo = "uploads/documentos/".$info['archivo'];

			$db->query("DELETE FROM documentos WHERE id='".$id."'");

			if ( file_exists( $archivo) ) {
				unlink( $archivo );
			}
		}

		header("Location: ./index.php?s=libreria&borrado");
	}
?>