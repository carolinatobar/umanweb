<?php
	require 'autoload.php';

	$acc = new Acceso(true);
	$db = DB::getInstance();

	// echo(json_encode($_POST));exit();
	if ( isset($_POST['modo'] ) ) {
		$modo = $_POST['modo'];
	} else {
		header("Location: index.php?s=error");
	}

	$faena = $_SESSION[session_id()]['faena'];


	if ( $modo == "umanblue" ) {

		$codigocaja 		= $_POST['codigocaja'];
		$equipo 			= explode('/', $_POST['equipo']);
		$ip_wifi 			= $_POST['ip_wifi'];
		$ip_lan 			= $_POST['ip_lan'];
		$sim 				= $_POST['sim'];

		// Momentaneo
		$transmision 		= "GPRS/WIFI/LAN";

		$db->query("INSERT INTO uman_cajauman VALUES ('','','10000','$codigocaja','60','1','1','$transmision','$ip_wifi','$ip_lan','$sim')");
		$db->query("INSERT INTO uman_estado_umanblue VALUES (
			'','$codigocaja','$equipo[0]','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','','','',
			'','','','','','','','','')"); // 97 CAMPOS

		$dato 				= $db->query("SELECT * FROM uman_cajauman ORDER BY ID_CAJAUMAN desc LIMIT 1");
		$cajauman 	  = $dato->count() > 0 ? $dato->results()[0] : null;
		$id_caja 			= $cajauman->ID_CAJAUMAN;

		$db->query("UPDATE uman_camion SET ID_CAJAUMAN='$id_caja' WHERE ID_CAMION='$equipo[0]'");

		$db->query("UPDATE uman_sim SET ESTADO='En uso' WHERE ID='$sim'");

		//print $sim;

		header("Location: index.php?s=umanblue&guardado=true");

	// Guardado de NEUMATICOS
	//-------------------------
	} 
	else if ( $modo == "neumaticos" ) 
	{
		$numidenti 		= isset($_POST['t1']) ? $_POST['t1'] : null;
		$marca 				= isset($_POST['marca']) ? $_POST['marca'] : null;
		$modelo 			= isset($_POST['modelo']) ? $_POST['modelo'] : null;
		$compuesto 		= isset($_POST['compuesto']) ? $_POST['compuesto'] : null;
		$dimension 		= isset($_POST['dimension']) ? $_POST['dimension'] : null;
		$numero_fuego	= isset($_POST['numero_fuego']) ? $_POST['numero_fuego'] : null;

		$mensaje = '<ul>';
		$codigo = 400;

		if($numero_fuego && $numidenti && $marca && $modelo && $compuesto && $dimension)
		{

			$sql = "INSERT INTO uman_neumaticos  
			 VALUES ('','%s','0','DISPONIBLE','%s','%s','%s','%s','%s','0');";
			$sql = sprintf($sql, $numidenti, $marca, $modelo, $dimension, $compuesto, $numero_fuego);

			$db = DB::getInstance();
			$db = $db->query($sql);

			if(!$db->error())
			{
				$codigo = 200;
				$mensaje = "<li>El neumático $id se ha ingresado correctamente.</li></ul>";
			}
			else
			{
				$codigo = 400;
				$mensaje = "<li>{$db->errorInfo}</li></ul>";
			}
		}
		else
		{
			$codigo = 400;
			if($numero_fuego == null) $mensaje .= '<li>Debe ingresar el número de fuego.</li>';
			if($numidenti == null)    $mensaje .= '<li>Debe ingresar el nombre del neumático.</li>';
			if($marca == null)        $mensaje .= '<li>Debe ingresar la marca.</li>';
			if($compuesto == null)    $mensaje .= '<li>Debe ingresar el nombre del compuesto.</li>';
			if($dimension == null)    $mensaje .= '<li>Debe ingresar la dimensión.</li>';

			$mensaje .= '</ul>';
		}
		
		header("Content-type: text/json");
		echo json_encode(['response'=>$mensaje, 'type'=>$codigo]);
		
		// mysql_query("INSERT INTO uman_neumaticos VALUES ('','$numidenti','0','DISPONIBLE','$marca','$modelo','$dimension','$compuesto','$numero_fuego','0')");
		//print $modo." // ".$numidenti." // ".$marca." // ".$modelo." // ".$compuesto." // ".$dimension." // ".$numero_fuego;
		// header("Location: index.php?s=neumaticos&guardado=true");
	} 
	else if ( $modo == "editar-neumatico" ) 
	{
		$id           = isset($_POST['id']) ? $_POST['id'] : null;
		$numidenti 		= isset($_POST['t1']) ? $_POST['t1'] : null;
		$marca 				= isset($_POST['marca']) ? $_POST['marca'] : null;
		$modelo 			= isset($_POST['modelo']) ? $_POST['modelo'] : null;
		$compuesto 		= isset($_POST['compuesto']) ? $_POST['compuesto'] : null;
		$dimension 		= isset($_POST['dimension']) ? $_POST['dimension'] : null;
		$numero_fuego	= isset($_POST['numero_fuego']) ? $_POST['numero_fuego'] : null;

		$mensaje = '<ul>';
		$codigo = 400;

		if($id && $numero_fuego && $numidenti)
		{

			$sql = "UPDATE uman_neumaticos SET 
			 NUMIDENTI='%s', MARCA='%s', MODELO='%s', DIMENSION='%s', COMPUESTO='%s', NUMEROFUEGO='%s' 
			 WHERE ID_NEUMATICO=%d;";
			$sql = sprintf($sql, $numidenti, $marca, $modelo, $dimension, $compuesto, $numero_fuego, $id);

			$db = DB::getInstance();
			$db = $db->query($sql);

			if(!$db->error())
			{
				$codigo = 200;
				$mensaje = "<li>El neumático $id se ha modificado correctamente.</li></ul>";
			}
			else
			{
				$codigo = 400;
				$mensaje = "<li>{$db->errorInfo}</li></ul>";
			}
		}
		else
		{
			$codigo = 400;
			if($id == null)           $mensaje .= '<li>El ID del neumático debe ser un valor numérico.</li>';
			if($numero_fuego == null) $mensaje .= '<li>Debe ingresar el número de fuego del neumático.</li>';
			if($numidenti == null)    $mensaje .= '<li>Debe especificar el nombre del neumático.</li>';

			$mensaje .= '</ul>';
		}
		
		header("Content-type: text/json");
		echo json_encode(['response'=>$mensaje, 'type'=>$codigo]);
		// mysql_query("UPDATE INTO uman_neumaticos VALUES ('','$numidenti','0','DISPONIBLE','$marca','$modelo','$dimension','$compuesto','$numero_fuego','0')");

		// header("Location: index.php?s=neumaticos&guardado=true");
	} 
	else if ( $modo == "eliminar-neumatico" )
	{
		$id = isset($_POST['id']) ? $_POST['id'] : null;

		$mensaje = '<ul>';
		$codigo = 400;

		if(is_numeric($id))
		{
			$sql = sprintf("DELETE FROM uman_neumaticos WHERE ID_NEUMATICO=%d;", $id);
			$db = DB::getInstance();
			$db = $db->query($sql);

			if(!$db->error())
			{
				$codigo = 200;
				$mensaje .= '<li>Neumático ID #'.$id.' eliminado.</li>';
				$mensaje .= '</ul>';
			}
			else
			{
				$codigo = 400;
				$mensaje .= '<li>'.$db->errorInfo.'</li>';

				$mensaje .= '</ul>';
			}
		}
		else
		{
			$codigo = 400;
			if($id == null)           $mensaje .= '<li>El ID del neumático NO debe ser nulo.</li>';
			else if(!is_numeric($id)) $mensaje .= '<li>El ID del neumático debe ser un valor numérico.</li>';
			else 										  $mensaje .= '<li>Se produjo elgún problema con el ID <strong>#'.$id.'</strong></li>';

			$mensaje .= '</ul>';
		}

		header("Content-type: text/json");
		echo json_encode(['response'=>$mensaje, 'type'=>$codigo]);
	} 
	else if ( $modo == "sensores" ) 
	{

		$cuenta = 1;
		$sensores_no_ingresados = "";

		while ( isset ( $_POST[ 't'.$cuenta ] ) ) {

			$cod_sensor = $_POST['t'.$cuenta];
			$tipo 		= $_POST['s'.$cuenta];

			$data_sensor = $db->query("SELECT ID_SENSOR FROM uman_sensores WHERE CODSENSOR='$cod_sensor'");
			// $info_sensor = mysql_fetch_array( $data_sensor );
			if ( $data_sensor->count() <= 0 ) {
				if ( $cod_sensor != "" ) {
					$db->query("INSERT INTO uman_sensores VALUES ('','$cod_sensor','$tipo','DISPONIBLE',NOW(),'')");
				}				
			} else {
				$sensores_no_ingresados = $sensores_no_ingresados.",".$cod_sensor;
			}

			$cuenta++;
		}

		header("Location: index.php?s=ingresar-sensores&guardado=true&sensores_no_ingresados=".$sensores_no_ingresados);

	} 
	else if ( $modo == "plantillas" ) 
	{

		$marca 			= $_POST['marca'];
		$modelo 		= $_POST['modelo'];
		$temp_max 	= $_POST['temp_max'];
		$pres_min 	= $_POST['pres_min'];
		$pres_max 	= $_POST['pres_max'];
		$eje 				= $_POST['eje'];
		$compuesto 	= $_POST['compuesto'];
		$dimension 	= $_POST['dimension'];
		$sensor 		= $_POST['sensor'];
		$pif				= $_POST['pif'];

		//print $marca." // ".$modelo." // ".$temp_max." // ".$pres_min." // ".$pres_max." // ".$eje." // ".$compuesto." // ".$dimension." // ".$sensor;
		

		$db->query("INSERT INTO uman_plantilla VALUES ('','$marca','$modelo','$dimension','$temp_max','$pres_max','$pres_min','$eje','$compuesto','$sensor','$pif')");

		header("Location: index.php?s=plantillas&guardado=true");

		// TELEFONOS
		//-------------
	} 
	else if ( $modo == "sim" ) 
	{

		$telefono 			= $_POST['telefono'];
		$compania 			= $_POST['compania'];

		$db->query("INSERT INTO uman_sim VALUES ('','$telefono','$compania','Disponible')");

		header("Location: index.php?s=sim&guardado=true");

	}
	

?>