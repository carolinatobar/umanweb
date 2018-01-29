<?php
  require 'autoload.php';
  
  session_start();
  $time = $_SESSION[session_id()]['expira'];

  //si $_SESSION[session_id()]['ACTUALIZAR_CAJA'] == true verifica la 
  //tabla uman_estado_umanblue para ver si la caja realizó la 
  //actualización con los nuevos datos
  if($_SESSION[session_id()]['ACTUALIZAR_CAJA'] && count($_SESSION[session_id()]['CAJAS']) > 0){
    foreach($_SESSION[session_id()]['CAJAS'] as $caja){
      $db = DB::getInstance();
      $sql = sprintf("SELECT FLAG_SERV_UMAN, FECHA_FLAG_SERV_UMAN 
        FROM uman_estado_umanblue 
        WHERE UMAN_BLUE=%d;", $caja);
      $res = $db->query($sql);
      if($res->count() > 0){
        if($res->results()[0]->FLAG_SERV_UMAN == 0){
          $fecha = (new DateTime($res->results()[0]->FECHA_FLAG_SERV_UMAN))->format("d/m/Y H:i:s");
          $noti[] = array(
            't'=>'Caja '.$caja.' actualizada.',
            'b'=>"La caja $caja se ha actualizado con fecha {$fecha}.",
            'i'=>''
          );
        }
      }
    }
  }
  $noti['cajas'] = $_SESSION[session_id()]['CAJAS'];
  $_SESSION[session_id()]['CAJAS'] = null;
  unset($_SESSION[session_id()]['CAJAS']);
  $_SESSION[session_id()]['ACTUALIZAR_CAJA'] = false;

  $ahora  = new DateTime(date("Y-m-d H:i:s"));
  $expira = new DateTime(date("Y-m-d H:i:s", $time));

  
  if($expira > $ahora) {
    $time = $ahora->diff($expira);
    $sesion = array(
      'hor'=>$time->format("%h"),
      'min'=>$time->format("%i"),
      'seg'=>$time->format("%s"),
    );
  }
  else{
    $sesion = array(
      'hor'=>0,
      'min'=>0,
      'seg'=>0,
    );

    if($time > 0){
      $sess_id = session_id();
      session_destroy();
      unset($_SESSION[$sess_id]['user']);
      unset($_SESSION[$sess_id]['pass']);
      unset($_SESSION[$sess_id]['perfil']);
      unset($_SESSION[$sess_id]['perfilactivo']);
    }
  }

  $data = array(
    'fecha'=>date("d/m/Y"),
    'dia'=>date("d"),
    'mes'=>date("m"),
    'anio'=>date("Y"),
    'hora'=>date("H"),
    'min'=>date("i"),
    'seg'=>date("s"),
    'sesion'=>$sesion,
    'timestamp'=> $_SESSION[session_id()]['expira'],
    'notificaciones'=>$noti
  );

  header("Content-type: application/json");
  echo json_encode($data);
?>
