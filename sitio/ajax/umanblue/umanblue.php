<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	
	$db  = DB::getInstance();
	$gen = new General();

	$img = $gen->getImagenesEquipo();

	$sql = "SELECT 
		cu.ID_CAJAUMAN, 
		cu.CODIGOCAJA, 
		uc.NUMCAMION,
		uc.ID_CAMION,
		cu.IPUMAN,
		cu.IP_WIFI,
		cu.IP_LAN,
		us.TELEFONO,
		cu.TIMEOUTSENSOR,
		cu.UMBRALBATERIA,
		cu.ESTADO

		FROM uman_cajauman cu LEFT JOIN uman_camion uc ON cu.ID_CAJAUMAN=uc.ID_CAJAUMAN 
		LEFT JOIN uman_sim us ON cu.ID_SIM=us.ID
		ORDER BY cu.CODIGOCAJA";

	$res = $db->query($sql);

	$data['data'] = array();

	$perfilactivo = $_SESSION[session_id()]['perfilactivo'];

	if($res->count() > 0){
		foreach($res->results() as $d){
			$params = "{$d->ID_CAJAUMAN},'{$d->CODIGOCAJA}','{$d->NUMCAMION}','{$d->IP_WIFI}','{$d->IP_LAN}','{$d->TELEFONO}'";
			$dx = array(
				'btn'=>'<button type="button" class="btn btn-primary" onclick="editar('.$params.')"><i class="fa fa-pencil" aria-hidden="true"></i></button>',
				'id'=>$d->ID_CAJAUMAN,
				'codigo'=>$d->CODIGOCAJA,
				'equipo'=>$img[$d->ID_CAMION]['DIV36'].' '.$d->NUMCAMION,
				'ip_publica'=>'<a href="http://'.$d->IPUMAN.'" target="_blank">'.$d->IPUMAN.'</a>',
				'ip_wifi'=>$d->IP_WIFI,
				'ip_lan'=>$d->IP_LAN,
				'sim'=>$d->TELEFONO,
				'timeout'=>$d->TIMEOUTSENSOR,
				'bateria'=>$d->UMBRALBATERIA,
				'leds'=>'<img src="'.$GLOBALS['ASSETS'].'/img/led'.($d->ESTADO == 0 ? 'apagado' : 'encendido').'.png" class="led" />',
				'buzzer'=>'<img src="'.$GLOBALS['ASSETS'].'/img/sirena'.($d->ESTADO == 0 ? 'apagada' : 'encendida').'.png" class="sirena" />',
			);

			if($perfilactivo->id != 5 && $perfilactivo->id != 7){
				unset($dx['btn']);
				unset($dx['ip_publica']);
				unset($dx['ip_wifi']);
				unset($dx['ip_lan']);
				unset($dx['sim']);
			}
			$data['data'][] = $dx;
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);

?>