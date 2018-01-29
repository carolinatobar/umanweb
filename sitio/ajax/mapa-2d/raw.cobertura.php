<?php
	require '../../autoload.php';

  $acc = new Acceso(true);
  $gen = new General(); 

  $tiempo  = isset($_REQUEST['tiempo'])  ? $_REQUEST['tiempo']  : NULL;  
  $fecha   = isset($_REQUEST['fecha'])   ? $_REQUEST['fecha']   : NULL;
  $rango   = isset($_REQUEST['fecha'])   ? $_REQUEST['fecha']   : NULL;
  $formato = isset($_REQUEST['formato']) ? $_REQUEST['formato'] : NULL;

	if($fecha != NULL) {
		if(stripos($fecha,' - ') > 0){
			$fecha = explode(" - ", $fecha);
			if($fecha[0] != $fecha[1]){
				$fecha = "UNIX_TIMESTAMP(FECHA) 
					BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
			}
			else{
				$fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(FECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
			}
		}
		else{			
			$fecha = "UNIX_TIMESTAMP(FECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
		}
	} 
	else {
		$fecha2 = date('d/m/Y H:i:s', time());
		if($tiempo == NULL){
			$fecha1 = date('d/m/Y 00:00:00', time());
			$fecha = "UNIX_TIMESTAMP(FECHA) 
			BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha1','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(NOW())";
		}
		else{
			$fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
			$fecha = "UNIX_TIMESTAMP(FECHA) 
			BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$tiempo} HOUR)) AND UNIX_TIMESTAMP(NOW())";
		}
	}

  $db = DB::getInstance();

  $sql = "SELECT X, Y, COUNT(*) AS 'total' 
    FROM uman_cobertura 
    WHERE $fecha 
    GROUP BY X, Y";
  // echo $sql;
  $data = $db->query($sql);

  $mapData = array();
  // echo $data->count().'<br/>';
  $count1 = count($data);
  if($data->count() > 0){
    $data = $data->results();
    $csv  = '';
    $json = array();
    $GeoJSON = array();
    foreach($data as $l => $d){
    	$json[] = array(floatval($d->X), floatval($d->Y), intval($d->total));
    	$csv   .= floatval($d->X) .', '. floatval($d->Y) .', '. intval($d->total) ."\n";
    	$GeoJSON[] = array(
    		"type"=>"Feature",
        "geometry"=> array(
          "type"=>"Point",
          "coordinates"=>array(floatval($d->X), floatval($d->Y))
        ),
        "properties"=>array(
        	"total"=>intval($d->total)
        )
    	);
    }
  }  

  if($formato == 'json'){
  	header("Content-Type: application/json");
  	echo json_encode($json);
  }
  else if($formato == 'csv'){
  	header("Content-Type: text/csv");
  	echo $csv;
  }
  else if($formato == 'GeoJSON'){
  	header("Content-Type: application/json");
  	echo json_encode($GeoJSON);	
  }
?>