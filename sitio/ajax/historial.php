<?php
	require '../autoload.php';

	session_start();
	$acc  = new Acceso($_SESSION, session_id());
	$db   = DB::getInstance();
	$db2  = DB2::getInstance();
	$gen  = new General();
	$data = array('data'=>array());

	$vns  = $gen->getParamValue('verneumaticosegun');
	$img  = $gen->getImagenesEquipo();
	$nom  = $gen->getNomenclaturas(); 

	$sql  = "SELECT
		DATE_FORMAT(h.FECHA,'%Y-%m-%d') AS FECHA,
		DATE_FORMAT(h.FECHA,'%d/%m/%Y') AS FECHA2,
		DATE_FORMAT(h.FECHA,'%H:%i:%s') AS HORA,
		h.ACCION, 
		h.ID_POSICION,
		n.NUMIDENTI,
		n.NUMEROFUEGO,
		s.CODSENSOR,
		s.TIPO,
		c.NUMCAMION,
		c.ID_CAMION,
		ID_USUARIO 
		FROM 
		uman_historial AS h 
		LEFT JOIN uman_neumaticos AS n ON h.ID_NEUMATICO = n.ID_NEUMATICO
		LEFT JOIN uman_sensores AS s ON h.ID_SENSOR = s.ID_SENSOR
		LEFT JOIN uman_camion AS c ON h.ID_CAMION = c.ID_CAMION 
		ORDER BY h.FECHA DESC;";

	$res  = $db->query($sql);

	if($res->count() > 0){
		foreach($res->results() as $r){
			
			$codigo = $r->NUMIDENTI;
			if($vns == 'fuego' && $r->NUMEROFUEGO != '') $codigo = $r->NUMEROFUEGO;

			$img_sensor = Core::imagen_sensor($r->TIPO, 36, 'float: left !important;').' ';
			$img_camion = $img[$r->ID_CAMION]['DIV36'].' ';

			$usuario = $db2->query("SELECT * FROM uman_usuarios WHERE ID_USUARIO={$r->ID_USUARIO}");
			$usuario = $usuario->count() > 0 ? $usuario->results()[0]->NOMBRE : '-';

			$data['data'][] = array(
				'fecha'=>['fecha1'=>$r->FECHA, 'fecha2'=>$r->FECHA2],
				'hora'=>$r->HORA,
				'accion'=>$r->ACCION,
				'posicion'=>($r->ID_POSICION >0 ? $nom[$r->ID_POSICION] : ''),
				'cod_neum'=>$codigo,
				'cod_sen'=>$img_sensor.$r->CODSENSOR,
				'num_cam'=>$img_camion.$r->NUMCAMION,
				'usuario'=>$usuario
			);
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);

?>