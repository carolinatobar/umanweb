<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$db  = DB::getInstance();

	$gen = new General();
	$usar_fuego = $gen->getParamValue('verneumaticosegun', 'fuego');
	$img        = $gen->getImagenesEquipo();

	$sql = "SELECT s.ID_SENSOR, s.CODSENSOR, s.TIPO, s.ESTADO, n.ID_NEUMATICO, n.NUMIDENTI, n.NUMEROFUEGO, c.ID_CAMION, c.NUMCAMION 
		FROM uman_sensores s LEFT JOIN uman_neumaticos n ON s.ID_SENSOR=n.ID_SENSOR
		LEFT JOIN uman_neumatico_camion nc ON nc.ID_NEUMATICO=n.ID_NEUMATICO
		LEFT JOIN uman_camion c ON c.ID_CAMION=nc.ID_EQUIPO 
		ORDER BY CODSENSOR ASC;";
	$res = $db->query($sql);

	$data = array('data'=>array(), 'type'=>'success');

	foreach($res->results() as $r){
		$neumatico = $r->NUMIDENTI;
		if($usar_fuego == 'fuego') $neumatico = $r->NUMEROFUEGO;
		if($neumatico != '' || $neumatico != NULL) $neumatico = $r->NUMIDENTI;

		$data['data'][] = array(
			'btn'=>'<button class="btn btn-primary btn-xs" onclick="modificar('.$r->ID_SENSOR.');"><i class="fa fa-pencil" aria-hidden="true"></i></button>
			<button class="btn btn-danger btn-xs" onclick="eliminar('.$r->ID_SENSOR.');"><i class="fa fa-trash" aria-hidden="true"></i></button>',
			'codigo'=>$r->CODSENSOR,
			'tipo'=>Core::imagen_sensor($r->TIPO, 24, 'float: left !important;').' '.$r->TIPO,
			'neumatico'=>$neumatico,
			'equipo'=>$img[$r->ID_CAMION]['DIV24'].' '.$r->NUMCAMION,
			'estado'=>$r->ESTADO,
		);
	}

	header("Content-Type: application/json");
	echo json_encode($data);