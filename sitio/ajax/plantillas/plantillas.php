<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$gen = new General();

	$utemp = $gen->getParamValue('unidad_temperatura');
	$upres = $gen->getParamValue('unidad_presion');

	$db = DB::getInstance();

	$plantilla  = $db->query("SELECT * FROM uman_plantilla ORDER BY MARCA ASC");
	$data = array('data'=>array(), 'type'=>'success');

  foreach($plantilla->results() as $datos ) {
  	
  	$d = array(
  		'btn'=>'<button class="btn btn-xs btn-primary" onclick="editar('.$datos->ID_PLANTILLA.');" data-modo="editar"><i class="fa fa-pencil"></i></button>
				<button class="btn btn-xs btn-danger" onclick="eliminar('.$datos->ID_PLANTILLA.');"><i class="fa fa-times"></i></button>',
			'id'=>$datos->ID_PLANTILLA,
  		'eje'=>($datos->EJE == 0) ? 'Todos' : $datos->EJE,
  		'marca'=>$datos->MARCA,
  		'dimension'=>$datos->DIMENSION,
      'compuesto'=>$datos->COMPUESTO,
  		'sensor'=>'<center>'.Core::imagen_sensor($datos->SENSOR, 24).'<br/>'.$datos->SENSOR.'</center>',
  		'tmax'=>Core::tpConvert($datos->TEMPMAX, $utemp, true),
  		'pmin'=>Core::tpConvert($datos->PRESMIN, $upres, true),
  		'pmax'=>Core::tpConvert($datos->PRESMAX, $upres, true),
  		'pif'=>Core::tpConvert($datos->PIF, $upres, true)
  	);

  	$data['data'][] = $d;
  }

  header("Content-Type: application/json");
  echo json_encode($data);
?>