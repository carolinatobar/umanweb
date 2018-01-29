<?php
  require 'autoload.php';

  $acc = new Acceso(true);

  $gen = new General();
  $nomenclatura = $gen->getNomenclaturas();

  $y = isset($_POST['y']) ? $_POST['y'] : date("Y");
  $m = isset($_POST['m']) ? $_POST['m'] : NULL;

  $end_month   = ($y == date("Y")) ? intval(date("n")) : 12;
  $end_month   = ($m == NULL) ? $end_month : $m;
  $start_month = ($m == NULL) ? 1 : $m;
  
  $db         = DB::getInstance();
  $data       = array();

  $equipos    = array();
  $posiciones = array();
  $cant_dias  = array();

  for($m=$start_month; $m<=$end_month; $m++){
    $mes  = array();
    $ud   = date("t", mktime(0,0,0,$m,1,$y));

    $cant_dias[$m] = intval($ud);
    
    //Obtener totales por mes
    $sql = "SELECT COUNT(x.fecha) AS total, x.*
      FROM (
        SELECT DATE_FORMAT(EVENTOFECHA,'%d') AS fecha, EVENTONUMCAMION AS idcamion, uc.NUMCAMION AS camion, EVENTOPOSICION AS posicion 
        FROM uman_eventos ue INNER JOIN uman_camion uc ON ue.EVENTONUMCAMION=uc.ID_CAMION
        WHERE EVENTOFECHA BETWEEN '$y-$m-01 00:00:00' AND '$y-$m-$ud 23:59:59' 
        ORDER BY camion ASC, posicion ASC, fecha ASC 
      ) AS x
      GROUP BY x.camion, x.posicion, x.fecha";
    // echo $sql;
    $dx   = $db->query($sql);

    $posiciones = array();
    if($dx->count() > 0){
      foreach($dx->results() as $d){
        if(!array_key_exists($d->idcamion, $equipos)) $equipos[$d->idcamion] = $d->camion;

        if(!array_key_exists($d->idcamion, $posiciones)) $posiciones[$d->idcamion][] = $d->posicion;          
        else 
          if(!in_array($d->posicion, $posiciones[$d->idcamion])) $posiciones[$d->idcamion][] = $d->posicion;

        $mes[$d->fecha][$d->idcamion][$d->posicion] = array(
          'equipo'   => $d->camion,
          'idequipo'   => intval($d->idcamion),
          'posicion' => $nomenclatura[$d->posicion],
          'dia'      => $d->fecha,
          'total'    => intval($d->total)
        );
      }
      $data[$m] = $mes;
    }
  }

  $data['equipos']    = $equipos;
  $data['posiciones'] = $posiciones;
  $data['cant_dias']  = $cant_dias;
  $data['type']       = 'success';

  header("Content-Type: application/json");
  echo json_encode($data);