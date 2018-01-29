<?php
	require '../autoload.php';
	error_reporting(E_ALL & ~E_NOTICE);

	$array = array();
	$i=0;
	$db = DB::getInstance();

	$rango_presion[]=-100;$rango_presion[]=-90;
	$rango_presion[]=-90;$rango_presion[]=-80;
	$rango_presion[]=-80;$rango_presion[]=-70;
	$rango_presion[]=-70;$rango_presion[]=-60;
	$rango_presion[]=-60;$rango_presion[]=-50;
	$rango_presion[]=-50;$rango_presion[]=-40;
	$rango_presion[]=-40;$rango_presion[]=-30;
	$rango_presion[]=-30;$rango_presion[]=-20;
	$rango_presion[]=-20;$rango_presion[]=-10;
	$rango_presion[]=-10;$rango_presion[]=0;
	$rango_presion[]=0;$rango_presion[]=10;
	$rango_presion[]=10;$rango_presion[]=20;
	$rango_presion[]=20;$rango_presion[]=30;
	$rango_presion[]=30;$rango_presion[]=40;
	$rango_presion[]=40;$rango_presion[]=50;
	$rango_presion[]=50;$rango_presion[]=60;

	$arreglo_pres = array_fill(0,20,0);

	//Cantidad de neumáticos utilizados en faena:
	$sql = "SELECT DATE_FORMAT(min(fecha_evento),'%d/%m/%Y %H:%i') AS min_fecha_evento, DATE_FORMAT(max(fecha_evento), '%d/%m/%Y %H:%i') AS max_fecha_evento, COUNT(*) AS cant_neum 
		FROM uman_ultimoevento 
		WHERE eventopresion>30";
	$cant_neum = $db->query($sql);
	$cant_neum = $cant_neum->results()[0];
	$cant_neumx = $cant_neum->cant_neum;
	$min_fecha_evento = $cant_neum->min_fecha_evento;
	$max_fecha_evento = $cant_neum->max_fecha_evento;

	$sql = "SELECT DISTINCT ID_CAMION, NUMCAMION, NUMFLOTA, tipo
  	FROM uman_camion uc INNER JOIN uman_neumatico_camion unc ON uc.ID_CAMION=unc.ID_EQUIPO;";
	$camiones = $db->query($sql);
	$camiones = $camiones->results();
	
	foreach($camiones as $c){
		$sql = 'SELECT CODSENSOR, TIPO, POSICION, NOMENCLATURA, NUMIDENTI, un.ID_NEUMATICO, PIF, up.MARCA  
			FROM uman_sensores us INNER JOIN uman_neumaticos un ON us.ID_SENSOR=un.ID_SENSOR 
			INNER JOIN uman_neumatico_camion unc ON unc.ID_NEUMATICO=un.ID_NEUMATICO 
					INNER JOIN uman_plantilla up ON up.ID_PLANTILLA=un.ID_PLANTILLA 
					INNER JOIN uman_posicion upos ON upos.ID=unc.ID_POSICION 
			WHERE unc.ID_EQUIPO='.$c->ID_CAMION.'
			ORDER BY ID_POSICION';
			// echo $sql;exit();
		$dx     = $db->query($sql);
		$dx     = $dx->results();
		$equipo = $c->ID_CAMION;		
		
  	foreach($dx as $dxx){
			$posicion 		 = $dxx->POSICION;
			$id_neumatico  = $dxx->ID_NEUMATICO;
		 	$marcaneum     = $dxx->MARCANEUM;
		 	$tipo_sensor   = $dxx->TIPO;
			$pif 					 = $dxx->PIF;
			// print_r($dxx);
   		$sql = "SELECT fecha_evento, eventopresion, eventotemperatura 
				FROM uman_ultimoevento 
				WHERE numequipo='$equipo' AND posicion='$posicion' 
				ORDER BY fecha_evento DESC 
				LIMIT 1;";
			$due = $db->query($sql)->results()[0];
			// echo $sql;
				
			// print_r($due);exit();
			$presion = $due->eventopresion;

			if($presion>30){
				$valor1=(($presion*100)/$pif)-100;
				// $valor1=str_replace(',','.',$valor1);
				// echo "$valor1\n";
						
				$desviacion=round($valor1);
						
				$d=intval($desviacion);

				if($d>=$rango_presion[0] && $d<$rango_presion[1]){ $arreglo_pres[0]++; }
				if($d>=$rango_presion[2] && $d<$rango_presion[3]){ $arreglo_pres[1]++; }
				if($d>=$rango_presion[4] && $d<$rango_presion[5]){ $arreglo_pres[2]++; }
				if($d>=$rango_presion[6] && $d<$rango_presion[7]){ $arreglo_pres[3]++; }
				if($d>=$rango_presion[8] && $d<$rango_presion[9]){ $arreglo_pres[4]++; }
				if($d>=$rango_presion[10] && $d<$rango_presion[11]){ $arreglo_pres[5]++; }
				if($d>=$rango_presion[12] && $d<$rango_presion[13]){ $arreglo_pres[6]++; }
				if($d>=$rango_presion[14] && $d<$rango_presion[15]){ $arreglo_pres[7]++; }
				if($d>=$rango_presion[16] && $d<$rango_presion[17]){ $arreglo_pres[8]++; }
				if($d>=$rango_presion[18] && $d<$rango_presion[19]){ $arreglo_pres[9]++; }
				if($d>=$rango_presion[20] && $d<$rango_presion[21]){ $arreglo_pres[10]++; }
				if($d>=$rango_presion[22] && $d<$rango_presion[23]){ $arreglo_pres[11]++; }
				if($d>=$rango_presion[24] && $d<$rango_presion[25]){ $arreglo_pres[12]++; }
				if($d>=$rango_presion[26] && $d<$rango_presion[27]){ $arreglo_pres[13]++; }
				if($d>=$rango_presion[28] && $d<$rango_presion[29]){ $arreglo_pres[14]++; }
				if($d>=$rango_presion[30] && $d<$rango_presion[31]){ $arreglo_pres[15]++; }	
			}
			// print_r($arreglo_pres);
		}
	}
	
	// echo $cant_neumx.'####';
	for($i=0; $i<=15; $i++){
		$avg = "avg$i"; $$avg = 0;
	}
	if($cant_neumx > 0){ 
		for($i=0; $i<=15; $i++){
			$avg = "avg$i";
			$$avg = ($arreglo_pres[$i]*100)/$cant_neumx;
		}
	}
		
 $json = array(
	array("presion" => '[-100% -91%]',"min" => '0.0',"max" => $arreglo_pres[0],"avg" => $avg0),
  array("presion" => '[-90% -81%] ',"min" => '0.0',"max" => $arreglo_pres[1],"avg" => $avg1),
  array("presion" => '[-80% -71%]',"min" => '0.0',"max" => $arreglo_pres[2],"avg" => $avg2),
  array("presion" => '[-70% -61%]',"min" => '0.0',"max" => $arreglo_pres[3],"avg" => $avg3),
  array("presion" => '[-60% -51%]',"min" => '0.0',"max" => $arreglo_pres[4],"avg" => $avg4),
  array("presion" => '[-50% -41%]',"min" => '0.0',"max" => $arreglo_pres[5],"avg" => $avg5),
  array("presion" => '[-40% -31%]',"min" => '0.0',"max" => $arreglo_pres[6],"avg" => $avg6),
  array("presion" => '[-30% -21%]',"min" => '0.0',"max" => $arreglo_pres[7],"avg" => $avg7),
  array("presion" => '[-20% -11%]',"min" => '0.0',"max" => $arreglo_pres[8],"avg" => $avg8),
  array("presion" => '[-10% -1%]',"min" => '0.0',"max" => $arreglo_pres[9],"avg" => $avg9),
  array("presion" => '[0% 10%]',"min" => '0.0',"max" => $arreglo_pres[10],"avg" => $avg10),
  array("presion" => '[11% 20%]',"min" => '0.0',"max" => $arreglo_pres[11],"avg" => $avg11),
  array("presion" => '[21% 30%]',"min" => '0.0',"max" => $arreglo_pres[12],"avg" => $avg12),
  array("presion" => '[31% 40%]',"min" => '0.0',"max" => $arreglo_pres[13],"avg" => $avg13),
  array("presion" => '[41% 50%]',"min" => '0.0',"max" => $arreglo_pres[14],"avg" => $avg14),
	array("presion" => '[51% 60%]',"min" => '0.0',"max" => $arreglo_pres[15],"avg" => $avg15),
	array("presion" => 'fecha', 'min'=>$min_fecha_evento, 'max'=>$max_fecha_evento)
 );
	
 echo json_encode($json);
?>