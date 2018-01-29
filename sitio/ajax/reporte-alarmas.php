<?php
  require '../autoload.php';

  $acc = new Acceso(true);
  error_reporting(E_ALL);

  $gen = new General();
  $nomenclatura = $gen->getNomenclaturas();
  $unidad_temp  = $gen->getParamValue('unidad_temperatura');
  $unidad_pres  = $gen->getParamValue('unidad_presion');

	$db = DB::getInstance();
	$tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : 24;
	$titulo = '';

	if ( isset ( $_POST['equipo'] ) ) {
		$equipo = $_POST['equipo'];
		$modo 	= $_POST['modo'];
		$nomequipo = $db->query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=$equipo;");
		$nomequipo = ($nomequipo->count()>0)?$nomequipo->results()[0]->NUMCAMION:$equipo;
	} else {
		$data = array(
      'type'=>'error',
      'text'=>'Debe seleccionar un equipo para poder obtener la información.',
      'title'=>'Datos faltantes',
      'data'=>array(
        'posicion'=>'',
        'sensor'=>'',
        'tipo_alarma'=>'',
        'valor'=>'',
        'fecha_alarma'=>'',
        'hora_alarma'=>'',
        'fecha_reconop'=>'',
        'hora_reconop'=>'',
        'fecha_reconuw'=>'',
        'hora_reconuw'=>'',
        'comentario'=>'',
        'usuario'=>'',
      )
    );
	}

	if ( isset ( $_POST['fecha'] ) ) {
			$fecha = explode(" - ", $_POST['fecha']);
			$fecha1 = $fecha[0];
			$fecha2 = $fecha[1];
	} else {
			$fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
			$fecha2 = date('d/m/Y H:i:s', time());

			$titulo = "<h4>DESDE <strong>{$fecha1}</strong>  HASTA <strong>{$fecha2}</strong></h4>";
	}

	$fecha = "UNIX_TIMESTAMP(ALARMAFECHA) 
		BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha1','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha2','%d/%m/%Y %H:%i'))";

	$alarma[8] 		= "Timeout";
	$alarma[16] 	= "Bateria baja";
	$alarma[32] 	= "Temperatura";
	$alarma[64] 	= "Presi&oacute;n baja";
	$alarma[128] 	= "Presi&oacute;n alta"; 

	$sql = "SELECT * 
		FROM uman_alarmas 
		WHERE ALARMANUMCAMION='$equipo' AND $fecha 
		ORDER BY ALARMAFECHA DESC";

	$d = $db->query($sql);
  if($d->count()>0){
    $d = $d->results();
    
    foreach($d as $info){
      $fecha_alarma = date_format(date_create($info->ALARMAFECHA),"d/m/Y");
      $hora_alarma = date_format(date_create($info->ALARMAFECHA),"H:i");
      $fecha_operador = date_format(date_create($info->ALARMAFECHARECONOCE),"d/m/Y");
      $hora_operador = date_format(date_create($info->ALARMAFECHARECONOCE),"H:i");
      $fecha_umanweb = date_format(date_create($info->ALARMAFECHARECONOCEUMANWEB),"d/m/Y");
      $hora_umanweb = date_format(date_create($info->ALARMAFECHARECONOCEUMANWEB),"H:i");
      $tam = 10;
      $comentario = '-';
      // if(strlen($info->COMENTARIOS)>$tam){
      //   $corto = substr($info->COMENTARIOS,0,$tam);
      //   $largo = $info->COMENTARIOS;

      //   $comentario = '<a role="button" tabindex="0" data-trigger="hover" data-toggle="popover" data-placement="top" title="Comentario de Reconocimiento" data-content="'.utf8_encode($largo).'" >'.
      //   utf8_encode($corto) . '...</a>';
      // }
      // else $comentario = utf8_encode($info->COMENTARIOS);
      $comentario = ($info->COMENTARIOS != '' ? $info->COMENTARIOS : '-');

      $alarma_unidad = '';
      if($info->ALARMATIPO == 32) $valor = Core::tpConvert($info->ALARMAVALOR,$unidad_temp,true);
      else if($info->ALARMATIPO == 64 || $info->ALARMATIPO == 128) $valor = Core::tpConvert($info->ALARMAVALOR,$unidad_pres,true);
      else $valor = $info->ALARMAVALOR == 8 ? '--' : $info->ALARMAVALOR;

      $data['data'][] = array(
        'posicion'=>$nomenclatura[$info->ALARMAPOSICION],
        'sensor'=>$info->ALARMACODSENSOR,
        'tipo_alarma'=>$alarma[$info->ALARMATIPO],
        'valor'=>$valor,
        'fecha_alarma'=>($info->ALARMAFECHA != '0000-00-00 00:00:00' ? $fecha_alarma : '-'),
        'hora_alarma'=>($info->ALARMAFECHA != '0000-00-00 00:00:00' ? $hora_alarma : '-'),
        'fecha_reconop'=>($info->ALARMAFECHARECONOCE != '0000-00-00 00:00:00' ? $fecha_operador : '-' ),
        'hora_reconop'=>($info->ALARMAFECHARECONOCE != '0000-00-00 00:00:00' ? $hora_operador : '-' ),
        'fecha_reconuw'=>($info->ALARMAFECHARECONOCEUMANWEB != '0000-00-00 00:00:00' ? $fecha_umanweb : '-'),
        'hora_reconuw'=>($info->ALARMAFECHARECONOCEUMANWEB != '0000-00-00 00:00:00' ? $hora_umanweb : '-'),
        'comentario'=>utf8_encode($comentario),
        'usuario'=>($info->USUARIO == '' ? '-' : $info->USUARIO),
      );
    }
    $data['type'] = 'success';
  }
  else{
    $data['data'] = array(      
      'posicion'=>'',
      'sensor'=>'',
      'tipo_alarma'=>'',
      'valor'=>'',
      'fecha_alarma'=>'',
      'fecha_reconop'=>'',
      'fecha_reconuw'=>'',
      'comentario'=>'',
      'usuario'=>'',
    );
    $data['type'] = 'info';
    $data['text'] = 'No hay alarmas registradas en el rango de tiempo indicado.';
    $data['title'] = 'Reporte de Alarmas';
  }

  header("Content-Type: application/json");
  echo json_encode($data);