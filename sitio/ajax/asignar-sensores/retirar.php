<?php
require '../../autoload.php';

$acc = new Acceso(true);

//  'v_neumatico='+v_neumatico+"&v_sensor="+v_sensor;

$neumatico  = isset($_POST['v_neumatico'])  && $_POST['v_neumatico']  != ''   ? $_POST['v_neumatico'] : '';
$sensor     = isset($_POST['v_sensor'])     && $_POST['v_sensor']     != ''   ? $_POST['v_sensor']    : '';
$fecha      = isset($_POST['v_fecha']) ? $_POST['v_fecha'] : null;

if($fecha) $fecha = (new DateTime($fecha))->format("Y-m-d H:i:s");

$data = array();
if($neumatico != '' && $sensor != ''){

  $obj_neu  = new Neumatico();
  $neu_update['ID_SENSOR']  = 0;

  try{

    $updated_neumatico  = $obj_neu->modificar($neumatico, $neu_update);

    if($updated_neumatico){
      (new Historial())->retiro_sensor($sensor, $neumatico, $fecha);
      $data = array(
        'title'=>'Sensor actualizado',
        'text'=>'El sensor se ha desvinculado correctamente del neumático.',
        'type'=>'success'
      );
      Core::actualizar_umanblue();
    }
    else {
      $data = array(
        'title'=>'Ha ocurrido un error',
        'text'=>'El sensor no se ha desvinculado al neumático debido a un error '.$updated_neumatico->getPDO()->errorInfo[2],
        'type'=>'error'
      );
    }
  } catch (Exception $e){
    $data = array(
      'title'=>'Ha ocurrido un error',
      'text'=>'El sensor no se ha desvinculado al neumático debido a un error: '.$e->getMessage(),
      'type'=>'error'
    );
  }

}
else {
  $data = array(
    'title'=>'Error',
    'text'=>'No se ha podido realizar la operación debido a que los datos enviados no son válidos, por favor vuelva a intentar y si el problema persiste, contacte a soporte técnico.',
    'type'=>'error'
  );
}

header("Content-type: application/json");
echo json_encode($data);
