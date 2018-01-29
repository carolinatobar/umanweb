<?php
require '../../autoload.php';

$acc = new Acceso(true);

//  'v_equipo='+v_equipo+"&v_posicion="+v_posicion+"&v_neumatico="+v_neumatico;

$neumatico  = isset($_POST['v_neumatico'])  && $_POST['v_neumatico']  != ''   ? $_POST['v_neumatico'] : '';
$sensor     = isset($_POST['v_sensor'])     && $_POST['v_sensor']     != ''   ? $_POST['v_sensor']    : '';
$fecha      = isset($_POST['v_fecha']) ? $_POST['v_fecha'] : null;


$data = array();
if($neumatico != '' && $sensor != ''){

  $obj_neu  = new Neumatico();
  $obj_eqp  = new Equipo();

  $neu_update['ID_SENSOR']  = $sensor;

  try{
    $db = DB::getInstance();
    $data_sens_anterior   = $db->query("SELECT ID_SENSOR FROM uman_neumaticos WHERE ID_NEUMATICO='$neumatico'");
    $arr_sens_anterior    = $data_sens_anterior->results();
    $id_sensor_anterior   = $arr_sens_anterior[0]->ID_SENSOR;

    $db->query("UPDATE uman_sensores SET ESTADO='DISPONIBLE' WHERE ID_SENSOR='$id_sensor_anterior'");
    $db->query("UPDATE uman_sensores SET ESTADO='USO' WHERE ID_SENSOR='$sensor'");
    $updated_neumatico  = $obj_neu->modificar($neumatico, $neu_update);

    if($updated_neumatico){
      (new Historial())->instalacion_sensor($sensor, $neumatico, $fecha);
      $data = array(
        'title'=>'Sensor actualizado',
        'text'=>'El sensor se ha asignado correctamente al neumático.',
        'type'=>'success'
      );

      Core::actualizar_umanblue();
    }
  } catch (Exception $e){
    $data = array(
      'title'=>'Ha ocurrido un error',
      'text'=>'El sensor no se ha asignado al neumático debido a un error: '.$e->getMessage(),
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
