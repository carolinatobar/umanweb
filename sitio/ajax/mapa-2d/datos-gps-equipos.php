<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	$gen = new General();

	$db = DB::getInstance();

	$ambar      = $gen->getParamValue('pre_alarma', 0);
	$timeout    = $gen->getParamvalue('timeout', 30);
	$oeem2dceet = $gen->getParamValue('oeem2dceet', 0); //Ocultar Equipos En Mapa 2D Cuando Están En Timeout

	$data = array();

	if($oeem2dceet == 1){
	 	$sql = "SELECT cam.ID_CAMION, cam.NUMCAMION, tipo as TIPO_EQUIPO, X, Y, RAPIDEZ, ALTURA, NUMFLOTA, 
			DIRECCION, DATE_FORMAT(FECHAGPS,'%d/%m/%Y %H:%i:%s') AS FECHA_ORD, FECHAGPS  
			FROM uman_camion cam INNER JOIN uman_ultimogps ugps ON cam.ID_CAMION=ugps.ID_EQUIPO 
			WHERE FECHAGPS >= DATE_SUB(NOW(), INTERVAL {$timeout} MINUTE)
			ORDER BY cam.ID_CAMION ASC";
	}else{
		$sql = "SELECT cam.ID_CAMION, cam.NUMCAMION, tipo as TIPO_EQUIPO, X, Y, RAPIDEZ, ALTURA, NUMFLOTA, 
			DIRECCION, DATE_FORMAT(FECHAGPS,'%d/%m/%Y %H:%i:%s') AS FECHA_ORD, FECHAGPS  
			FROM uman_camion cam INNER JOIN uman_ultimogps ugps ON cam.ID_CAMION=ugps.ID_EQUIPO 
			ORDER BY cam.ID_CAMION ASC";
	}

 	$equipos = $db->query($sql);

	if($equipos->count()>0){
		foreach($equipos->results() as $r){
			$e['id_equipo']   = $r->ID_CAMION;
			$e['nom_equipo']  = $r->NUMCAMION;
			$e['numflota']    = $r->NUMFLOTA;
			$e['tipo_equipo'] = $r->TIPO_EQUIPO;
			$e['lat']         = floatVal($r->X);
			$e['lng']         = floatVal($r->Y);
			$e['rapidez']     = $r->RAPIDEZ;
			$e['direccion']   = $r->DIRECCION;
			$e['altura']      = $r->ALTURA;
			$e['fecha']       = $r->FECHA_ORD;
			$e['color']       = 'gray';

			$neu_cam = $db->query("SELECT ID_POSICION FROM uman_neumatico_camion WHERE ID_EQUIPO={$r->ID_CAMION}");
			$neu_cam = ($neu_cam->count()>0) ? $neu_cam = $neu_cam->results() : $neu_cam = NULL;
			$colorx = array_fill(1,16,'none');
			if($neu_cam != NULL){
				foreach($neu_cam as $nc){
					$colorx[$nc->ID_POSICION] = 'gray';
				}
			}

			for($i=1; $i<=16; $i++)
			{ 
				$e['presion_evento'.$i]      = ''; 
				$e['temperatura_evento'.$i]  = ''; 
				$e['fecha_evento'.$i]        = ''; 
				$e['color'.$i]               = $colorx[$i]; 
				$e['sensor'.$i]              = '';
				$e['pif'.$i]                 = 0;
				$e['presion_recomendada'.$i] = 0;
			}

			$sql = "SELECT unc.ID_POSICION, us.TIPO, up.PIF, up.PRE_ALARMA  
				FROM uman_neumatico_camion unc INNER JOIN uman_neumaticos un ON unc.ID_NEUMATICO=un.ID_NEUMATICO 
				INNER JOIN uman_plantilla up ON up.ID_PLANTILLA=un.ID_PLANTILLA 
				INNER JOIN uman_sensores us ON us.ID_SENSOR=un.ID_SENSOR 
				WHERE unc.ID_EQUIPO={$r->ID_CAMION} 
				ORDER BY unc.ID_POSICION ASC";
			$de = $db->query($sql);

			if($de->count()>0)
			{
				foreach($de->results() as $dee)
				{
					$e['sensor'.$dee->ID_POSICION]    = $dee->TIPO;
					$e['pif'.$dee->ID_POSICION]       = $dee->PIF;
					$e['prealarma'.$dee->ID_POSICION] = $dee->PRE_ALARMA;
				}
			}
			
			if($oeem2dceet == 1){
				$sql = "SELECT 
					DISTINCT(posicion), eventopresion, eventotemperatura, DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fechaordenada, fecha_evento,
					tempmax, presmax, presmin  
					FROM uman_ultimoevento 
					WHERE numequipo=$r->ID_CAMION and fecha_evento > DATE_SUB(NOW(), INTERVAL {$timeout} MINUTE) 
					ORDER BY posicion ASC, fecha_evento DESC";
			}else{
				$sql = "SELECT 
					DISTINCT(posicion), eventopresion, eventotemperatura, DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fechaordenada, fecha_evento,
					tempmax, presmax, presmin  
					FROM uman_ultimoevento 
					WHERE numequipo=$r->ID_CAMION 
					ORDER BY posicion ASC, fecha_evento DESC";
			}
			// echo $sql;exit();
			$ue = $db->query($sql);
			if($ue->count()>0)
			{
				$color = '';
				$colors = array();
				foreach($ue->results() as $de)
				{					
					$color = '';
					if($e['sensor'.$de->posicion] != ''){
						$d1 = new DateTime($de->fecha_evento);
						$d2 = new DateTime(date("Y-m-d H:i:s"));
						$diff = $d1->diff($d2);
						$diff = ($diff->format("%d")*24*60)+($diff->format("%i"));
						if($diff >= 30) $color = 'gray';
						else{
							$color = Core::determinar_color_equipo($de->eventopresion, $de->eventotemperatura, 
							$de->presmax, $de->presmin, $de->tempmax, $ambar, $e['prealarma'.$de->posicion]);
						}
						$colors[] = $color;
					}

					
					$e['presion_evento'.$de->posicion]     = $de->eventopresion;
					$e['temperatura_evento'.$de->posicion] = $de->eventotemperatura;
					$e['fecha_evento'.$de->posicion]       = $de->fechaordenada;
					$e['color'.$de->posicion]              = $color;

					$temp_k                 = $de->eventotemperatura + 273.15;
					$ratio                  = $temp_k / ( 18 + 273.15 );
					$e['presion_recomendada'.$de->posicion] = round( $e['pif'.$de->posicion] * $ratio );
				}
				if(in_array('black', $colors)) $e['color'] = 'black';
				if(in_array('lilac', $colors)) $e['color'] = 'lilac';
				if(in_array('orange',$colors)) $e['color'] = 'orange';
				if(in_array('yellow',$colors)) $e['color'] = 'yellow';
				if(in_array('red',   $colors)) $e['color'] = 'red';
			}

			$data['data'][] = $e;
		}
	}
 
  $data['type'] = 'success';

	header("Content-Type: application/json");
  echo json_encode($data);
?>