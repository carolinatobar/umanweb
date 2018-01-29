<?php
ini_set("default_charset", "utf-8");
date_default_timezone_set("Etc/GMT+3"); 
// exit();
//ON TIMEOUT AUTOMATIC ALARM RECOGNITION
 $host           = 'localhost';
// $host           = '192.168.20.100';
 $db             = 'uman_sitio';
 $user           = 'uman_sistemas';
 $password       = 'b@ilac1999';
//  $user           = 'server';
//  $password       = 'radagast';

 $pdo            = new PDO("mysql:host=$host;dbname=$db;",$user,$password);

 error_reporting(E_ALL);

 $fn = 'log.html';
 $content = '<html><body><h1>'.date("d/m/Y H:i:s").'</h1>';

 $timeout = 30; //en minutos
 /*
 - OBTENER TODAS LOS NOMBRES DE LAS BASES DE DATOS
 - CONECTARSE A CADA UNA PARA OBTENER LOS DATOS Y REALIZAR LA VERIFICACIÓN DE TIEMPO 
  DE LA ÚLTIMA TRANSMISIÓN DE DATOS Y LA FECHA/HORA ACTUAL
*/
//  echo date("d-m-Y H:i:s");
 
 $sql = "SELECT * FROM uman_faenas;";
 $bdName = query($sql, $pdo);
 $db = null;
 
//  print count($bdName);
//  print_r($bdName);

 if(count($bdName) > 0){
  foreach($bdName as $bdn){
   $content .= '<h1>*** '.$bdn->nombre_db."</h1>\n";
   echo '<h1>*** '.$bdn->nombre_db."</h1>\n";
   $nm = "uman_{$bdn->nombre_db}";
   if($bdn->nombre_db!=''){
    $pdo            = new PDO("mysql:host=$host;dbname=$nm;",$user,$password);

    //Si hay equipos cuya última transmisión está dentro del rango se considerará para verificar si tiene alarma
    // $sql = "SELECT * FROM 
    //   (SELECT numequipo, fecha_evento 
    //   FROM uman_ultimoevento uue INNER JOIN uman_alarmas ua ON uue.numequipo=ua.ALARMANUMCAMION 
    //   WHERE fecha_evento <= DATE_SUB(NOW(), INTERVAL {$timeout} MINUTE) AND ALARMAESTADO = 0
    //   ORDER BY fecha_evento DESC
    //   ) as evt
    //   GROUP BY evt.numequipo";

    // $sql = "SELECT * FROM
    //   (SELECT DISTINCTROW id, numequipo, fecha_evento, ROUND(TIME_TO_SEC(TIMEDIFF(NOW(), fecha_evento))/60) AS dif
    //    FROM uman_ultimoevento uue INNER JOIN uman_alarmas ua ON uue.numequipo=ua.ALARMANUMCAMION 
    //    WHERE ALARMAESTADO = 0 AND fecha_evento != '0000-00-00 00:00:00' 
    //    ORDER BY fecha_evento ASC) AS evt
    //   WHERE evt.dif >= {$timeout};";

    $sql = "SELECT * FROM
      (SELECT DISTINCTROW idalarma, alarmanumcamion, alarmafecha, ROUND(TIME_TO_SEC(TIMEDIFF(NOW(), alarmafecha))/60) AS dif
       FROM uman_alarmas
       WHERE ALARMAESTADO = 0 AND alarmafecha != '0000-00-00 00:00:00'  
       ORDER BY alarmafecha ASC) AS evt
      WHERE evt.dif >= {$timeout};";

    $equipos = query($sql, $pdo);

    $content .= "<span> - {$bdn->nombre_db}</span>: <span>".count($equipos)." equipos</span><br/>";
    echo "<span> - {$bdn->nombre_db}</span>: <span>".count($equipos)." equipos</span><br/>";
    //  EQUIPOS CON TIMEOUT
    if(count($equipos) > 0){
      $content .= "<h5>EQUIPOS CON TIMEOUT</h5>\n";
      echo "<h5>EQUIPOS CON TIMEOUT</h5>\n";
      foreach($equipos as $e){
        echo "&nbsp;&nbsp; {$e->alarmanumcamion} - {$e->alarmafecha} \n<br/>";
        $sql = "UPDATE uman_alarmas ua SET 
          ua.ALARMAESTADO = 2, 
          ua.ALARMAFECHARECONOCEUMANWEB = NOW(), 
          ua.COMENTARIOS = 'RECONOCIMIENTO AUTOMATICO POR TIMEOUT'  
          WHERE ua.ALARMANUMCAMION = {$e->alarmanumcamion} AND ua.ALARMAESTADO = 0 AND ua.IDALARMA = {$e->idalarma}; ";
        // echo $sql;
        query($sql, $pdo);
      }
    }
    //  EQUIPOS SIN TIMEOUT
    else{
      $content .= '<h5>EQUIPOS SIN TIMEOUT</h5>'."\n";
      echo '<h5>EQUIPOS SIN TIMEOUT</h5>'."\n";
      $tipo_alarma  = array(
        'timeout'=>8,
        'bateria_baja'=>16,
        'temperatura'=>32,
        'presion_baja'=>64,
        'presion_alta'=>128
      );
      $mensajeArr = array();
      //OBTENER EQUIPOS ALARMADOS
      $sql = "SELECT * FROM uman_alarmas WHERE ALARMAESTADO=0;";
      $alr = query($sql, $pdo);
      if(count($alr) > 0){
        $reconocer = array();
        foreach($alr as $a){
          if($a->ALARMATIPO == $tipo_alarma['temperatura']){
            $sql = "SELECT * FROM uman_ultimoevento 
              WHERE numequipo={$a->ALARMANUMCAMION} 
              AND posicion={$a->ALARMAPOSICION} 
              AND UNIX_TIMESTAMP(fecha_evento) > UNIX_TIMESTAMP(DATE_FORMAT('{$a->ALARMAFECHA}','%Y-%m-%d %H:%i:%s')) 
              AND eventotemperatura < tempmax 
              ORDER BY fecha_evento ASC
              LIMIT 1;";
            $alarma = query($sql,$pdo);
            if(count($alarma) > 0){
              $reconocer[] = $a->IDALARMA;
              $mensajeArr[$a->IDALARMA] = utf8_encode('RECONOCIMIENTO AUTOMATICO POR EVENTO DE TEMPERATURA NORMALIZADO '.$alarma->eventotemperatura);
            }
          }
          else if($a->ALARMATIPO == $tipo_alarma['presion_baja'] || $a->ALARMATIPO == $tipo_alarma['presion_alta']){
            $sql = "SELECT * FROM uman_ultimoevento 
              WHERE numequipo={$a->ALARMANUMCAMION} 
              AND posicion={$a->ALARMAPOSICION} 
              AND UNIX_TIMESTAMP(fecha_evento) > UNIX_TIMESTAMP(DATE_FORMAT('{$a->ALARMAFECHA}','%Y-%m-%d %H:%i:%s')) 
              AND eventopresion > presmin AND eventopresion < presmax;";
            $alarma = query($sql,$pdo);
            if(count($alarma) > 0){
              $reconocer[] = $a->IDALARMA;
              $mensajeArr[$a->IDALARMA] = utf8_encode('RECONOCIMIENTO AUTOMATICO POR EVENTO DE PRESION NORMALIZADO '.$alarma->eventopresion);
            }
          }
        }

        // SI EL ARRAEGLO TIENE IDS DE ALARMAS, SE RECONOCERÁN AUTOMÁTICAMENTE
        if(count($reconocer)>0){
          foreach($reconocer as $r){
            $sql = "UPDATE uman_alarmas ua 
              SET ua.ALARMAESTADO = 2, ua.ALARMAFECHARECONOCEUMANWEB = NOW(), ua.COMENTARIOS = '{$mensajeArr[$r]}'  
              WHERE ua.IDALARMA={$r} AND ua.ALARMAESTADO = 0;";
            // echo "$sql\n";
            $result = query($sql,$pdo);
            if($result) $content .= '<h4>>> Alarma '.$r.' reconocida'."</h4>\n";
          }
        }
      }
    }

    $content .= '<br/>';

    $equipos = null;
    $db = null;
   }
  }

  echo "<h1>FIN</h1>";
  $content .= '</body></html>';

  $handler = fopen($fn,"w");
  fwrite($handler,$content);
  fclose($handler);
  
 }

 function query($sql, $pdo){

    $_error = false;
    $_query = null; $_results = null; $_count = null;
    if($_query = $pdo->prepare($sql)){

      if($_query->execute()){
        $_results = $_query->fetchAll(PDO::FETCH_OBJ);
        $_count = $_query->rowCount();
        $_query->closeCursor();
      }
      else{
        $_error = true;
        $arr = $_query->errorInfo();
        $errorInfo = $arr[2];
        $content .= "<strong>$errorInfo</strong>";
        // die($arr[2]);
        // Error::lanzar($_query->errorInfo());
      }
    }
    else {
      $_error = true;
      $arr = $_query->errorInfo();
      $errorInfo = $arr[2];
      $content .= "<strong>$errorInfo</strong>";
      // Error::lanzar($_query->errorInfo());
    }

    echo '<div> >> '.count($_results).'   '.$sql."</div>";
    return $_results;
  }