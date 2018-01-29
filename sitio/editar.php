<?php
	require 'autoload.php';

	$acc = new Acceso(true);

	$db = DB::getInstance();
	
	if ( isset ( $_GET['modo'] ) ) {
		$modo = $_GET['modo'];
	} else  {
		if ( isset ( $_POST['modo'] ) ) {
			$modo = $_POST['modo'];
		} else {
			header("Location: ./index.php?ERROR");
		}	
	}

	if ( $modo == "sensor" ) {
		$sensor = $_GET['sensor'];
		$tipo 	= $_GET['tipo'];
		$db->query("UPDATE uman_sensores SET TIPO='$tipo' WHERE CODSENSOR='$sensor'");
		//print $sensor." // ".$modo." // ".$tipo." // ".$link;
		// include_once("./ajax/ajax_actualizar_umanblue.php");
		actualizar_umanblue();
		
		header("Location: index.php?s=ingresar-sensores");

	} else if ( $modo == "umanblue" ) {

		$id 		= $_POST['id_cax'];
		$cod_uman 	= $_POST['codigocaja'];
		$arr_equipo = explode( "/" , $_POST['equipo'] );
		$id_equipo	= $arr_equipo[0]; 
		$equipo 	= $arr_equipo[1];
		$ip_wifi 	= $_POST['ip_wifi'];
		$ip_lan 	= $_POST['ip_lan'];
		$id_sim 	= $_POST['sim'];

		$db->query("UPDATE uman_cajauman SET CODIGOCAJA='$cod_uman',IP_WIFI='$ip_wifi',IP_LAN='$ip_lan',ID_SIM='$id_sim' WHERE ID_CAJAUMAN='$id'");
		$db->query("UPDATE uman_camion SET ID_CAJAUMAN='0' WHERE ID_CAJAUMAN='$id'");
		$db->query("UPDATE uman_camion SET ID_CAJAUMAN='$id' WHERE NUMCAMION='$equipo'");
		$db->query("UPDATE uman_estado_umanblue SET ID_CAMION='id_$equipo[0]' WHERE UMAN_BLUE='$cod_uman'");
		//print $id." // ".$cod_uman." // ".$ip_wifi." // ".$ip_lan." // ".$id_sim;;

		// include_once("./ajax/ajax_actualizar_umanblue.php");
		actualizar_umanblue();
		header("Location: index.php?s=umanblue");


	} else if ( $modo == "neumaticos" ) {
		$id 				= $_POST['id'];
		$numidenti 			= $_POST['t1'];
		$marca 				= $_POST['marca'];
		$modelo 			= $_POST['modelo'];
		$compuesto 			= $_POST['compuesto'];
		$dimension 			= $_POST['dimension'];
		$numero_fuego		= $_POST['numero_fuego'];

		// include_once("./ajax/ajax_actualizar_umanblue.php");
		actualizar_umanblue();

		$db->query("UPDATE uman_neumaticos SET NUMIDENTI='$numidenti', MARCA='$marca', MODELO='$modelo', DIMENSION='$dimension', COMPUESTO='$compuesto', NUMEROFUEGO='$numero_fuego' WHERE ID_NEUMATICO='$id'");
		//print $id." // ".$cod_uman." // ".$ip_wifi." // ".$ip_lan." // ".$id_sim;;
		header("Location: index.php?s=neumaticos");
	} else if ( $modo == "documentos" ) {

		$id 				= $_POST['id'];
		$nombre 			= $_POST['nombre'];
		$documento 			= $_POST['documento'];
		$descripcion		= $_POST['descripcion'];

		$perfiles = "";

		$nombreperfiles = array("monitoreo","monitoreo-bailac","config-sensores","faena","tecnicos","administrativos");

		foreach ( $nombreperfiles as $perfil ) {
		  if ( isset ( $_POST[$perfil] ) ) {
		    if ( $perfiles != "" ) {
		      $perfiles = $perfiles.",";
		    }
		    $perfiles = $perfiles.$perfil;
		  }
		}

		$db->query("UPDATE documentos SET documento='$documento', nombre='$nombre', descripcion='$descripcion', perfiles='$perfiles' WHERE id='$id'");
		//print $id." // ".$cod_uman." // ".$ip_wifi." // ".$ip_lan." // ".$id_sim;;
		header("Location: index.php?s=libreria");
	} else if ( $modo == "plantillas" ) {

		$id 				= $_POST['id'];
		$marca 				= $_POST['marca'];
		$modelo 			= $_POST['modelo'];
		$tempmax 			= $_POST['temp_max'];
		$presmin 			= $_POST['pres_min'];
		$presmax 			= $_POST['pres_max'];
		$eje 				= $_POST['eje'];
		$compuesto 			= $_POST['compuesto'];
		$dimension 			= $_POST['dimension'];
		$sensor 			= $_POST['sensor'];
		$pif 				= $_POST['pif'];

		$query = "UPDATE uman_plantilla SET MARCA='$marca', MODELO='$modelo', DIMENSION='$dimension', TEMPMAX='$tempmax', PRESMAX='$presmax', PRESMIN='$presmin', EJE='$eje', COMPUESTO='$compuesto', SENSOR='$sensor', PIF='$pif' WHERE ID_PLANTILLA='$id'";
		$db->query($query);
		//print $query;
		// include_once("./ajax/ajax_actualizar_umanblue.php");
		actualizar_umanblue();
		header("Location: index.php?s=plantillas&modificado");

	} else if ( $modo == "telefono" ) {

		$id 				= $_POST['id'];
		$telefono			= $_POST['telefono'];
		$compania			= $_POST['compania'];

		$db->query("UPDATE uman_sim SET TELEFONO='$telefono', COMPANIA='$compania' WHERE ID='$id'");

		header("Location: index.php?s=sim&modificado");

	}


	function actualizar_umanblue(){
		$obj_eqp_n  	= new Equipo();
		$arr_camion   	= $obj_eqp_n->listar();

		foreach ( $arr_camion as $camion ) {
			if ( $camion->ID_CAJAUMAN != 0 ) {
					$obj_eqp_n->actualizarEstadoUmanBlue($camion);
			}
		}
	}
?>