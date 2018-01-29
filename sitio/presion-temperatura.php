<?php
  require '../../autoload.php';
  error_reporting(E_ALL);

	session_start();
	$archivo = $_SESSION[session_id()]['nombrefaena'] .' - '. $_SESSION[session_id()]['user'];	

	$equipo = isset($_GET['equipo']) ? $_GET['equipo'] : null;
  $fecha  = isset($_GET['fecha'])  ? $_GET['fecha']  : null;
  $tipo   = isset($_GET['tipo'])   ? strtolower($_GET['tipo']) : null;

  $archivo = 'presion-temperatura - EQ_'.$equipo;

  if($fecha != null) {
    if(stripos($fecha,' - ') > 0){
      $fecha = explode(" - ", $fecha);
      if($fecha[0] != $fecha[1]){
        $rango = "$fecha[0] - $fecha[1]";
        $fecha = "UNIX_TIMESTAMP(FECHA) 
          BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
      }
      else{
        $rango = $fecha[0];
        $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(FECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
      }
    }
    else{
      $fecha = $fecha;
      $rango = $fecha;
      $fecha = "UNIX_TIMESTAMP(FECHA) > UNIX_TIMESTAMP(STR_TO_DATE('$fecha','%d/%m/%Y'))";
    }
  } 
  else {
    $fecha1 = date('d/m/Y H:i:s', time() - (24*3600));
    $fecha2 = date('d/m/Y H:i:s', time());
    $fecha = "UNIX_TIMESTAMP(FECHA) 
    BETWEEN UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 24 HOUR)) AND UNIX_TIMESTAMP(NOW())";

    $rango = "$fecha1 - $fecha2";
  }

  $equipo = isset($_REQUEST['equipo']) ? $_REQUEST['equipo'] : null;
  $fecha  = isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : null;

  $f = $fecha;
    //Default Time
  $df_time1  = "00:00:00";
  $df_time2  = "23:59:59";

  if($fecha == null)
  {
    $ayer   = date("m/d/Y H:i:s",time() - ($tiempo*3600));
    $hoy    = date("m/d/Y H:i:s",time());
    $fecha  = "$ayer - $hoy";
  }

  $fecha = explode(" - ", $fecha);
  if($fecha[0] != $fecha[1])
  {
    $fecha = "UNIX_TIMESTAMP(ue.EVENTOFECHA) 
    BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]','%d/%m/%Y %H:%i'))";
  }
  else
  {
    $fecha = "UNIX_TIMESTAMP(STR_TO_DATE(DATE_FORMAT(ue.EVENTOFECHA,'%d/%m/%Y'),'%d/%m/%Y')) = UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]','%d/%m/%Y'))";
  }    

  if(is_numeric($equipo) && $fecha != '')
  {
    $archivo .= ' - EQ' . $equipo . ' - ' . trim(str_replace(' ','',str_replace('/','',str_replace(':','',str_replace(' - ','_',$f)))));
    // echo $archivo; exit();
    $sql = "SELECT 
      uc.NUMCAMION,
      ue.EVENTOCODSENSOR,
      ue.EVENTOPOSICION,
      ue.EVENTOFECHA,
      ue.EVENTOTEMPERATURA,
      ue.EVENTOPRESION,
      ue.EVENTODESCARGA,
      ue.TEMPMAX,
      ue.PRESMIN,
      ue.PRESMAX 
      FROM uman_eventos ue INNER JOIN uman_camion uc ON uc.ID_CAMION = ue.EVENTONUMCAMION 
      WHERE uc.ID_CAMION = $equipo AND $fecha 
      ORDER BY EVENTOFECHA DESC";        
    $db = DB::getInstance();
    
    $data = $db->query($sql);
    if($data->count() > 0){
      if($tipo == 'json'){
        header("Content-type: text/json");
        echo json_encode($data->results());
      }
      else if($tipo == 'csv'){
        $csv = '"CAMION","SENSOR","POSICION","FECHA","FECHADESCARGA","TEMPERATURA","TEMPMAX","PRESION","PRESMIN","PRESMAX"'."\n";

        foreach($data as $d){
          $fecha = (new DateTime($d->EVENTOFCHA))->format("d/m/Y H:i:s");
          $fechaDescarga = (new DateTime($d->EVENTOFECHADESCARGA))->format("d/m/Y H:i:s");
          $csv .= '"'.$d->NUMCAMION.'","'.$d->EVENTOCODSENSOR.'","'.$d->EVENTOPOSICION.'","'.$fecha.'","'.$fechaDescarga.'",
          "'.$d->EVENTOTEMPERATURA.'","'.$d->TEMPMAX.'","'.$d->EVENTOPRESION.'","'.$d->PRESMIN.'","'.$d->PRESMAX.'"'."\n";
        }
      }
    }
  }