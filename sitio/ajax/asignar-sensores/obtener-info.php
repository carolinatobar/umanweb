<?php
  require '../../autoload.php';

  $acc = new Acceso(true);

  $nid = isset($_POST['nid']) ? $_POST['nid'] : NULL;
  $sid = isset($_POST['sid']) ? $_POST['sid'] : NULL;

  $modo = isset($_POST['modo']) ? $_POST['modo'] : NULL;
  $id   = isset($_POST['id']) ? $_POST['id'] : NULL;

  $data = array(
    'type'=>'error',
    'title'=>'Error',
    'text'=>'Ha enviado una consulta vacía.'
  );

  if($modo == NULL){
    if($sid && $nid){
      $sensor = (new Sensor())->get_sensor_full($sid);
      $neumat = (new Neumatico())->get_full($nid);

      $data = array(
        'type'=>'success',
        'neumatico'=>$neumat[0],
        'sensor'=>$sensor[0]
      );
    }
    else{
      $data = array(
        'type'=>'error',
        'title'=>'Error',
        'text'=>'Los datos enviados no son válidos, por favor vuelta a intentar y si el problema persiste contacte a soporte técnico.'
      );
    }
  }
  else if($modo == 'sensor'){
    if($id==NULL && $sid!=NULL) $id=$sid;
    $sensor = (new Sensor())->get_sensor_full($id);
    $data = array(
      'type'=>'success',
      'sensor'=>$sensor[0]
    );
  }
  else if($modo == 'neumatico'){
    if($id==NULL && $nid!=NULL) $id=$nid;
    $neumat = (new Neumatico())->get_full($id);
    $data = array(
      'type'=>'success',
      'neumatico'=>$neumat[0],
    );
  }

  header("Content-type: application/json");
  echo json_encode($data);