<?php
require '../../autoload.php';
error_reporting(E_ALL);

$acc = new Acceso(true);

$old_sensor = isset($_POST['old_sensor'])  ? $_POST['old_sensor']  : 0;
$new_sensor = isset($_POST['new_sensor'])  ? $_POST['new_sensor']  : 0;
$neum_id    = isset($_POST['neum_id'])     ? $_POST['neum_id']     : NULL;
$baja       = isset($_POST['motivo_baja']) ? $_POST['motivo_baja'] : NULL;
$finst      = isset($_POST['finst'])       ? $_POST['finst']       : NULL;
$fdinst     = isset($_POST['fdinst'])      ? $_POST['fdinst']      : NULL;

$verNeumaticoPor = (new General())->getParamvalue('verneumaticosegun');

// if($finst != NULL) $finst = (new DateTime($finst))->format("Y-m-d H:i:s");
// if($fdinst != NULL) $fdinst = (new DateTime($fdinst))->format("Y-m-d H:i:s");
// echo 'pass'; exit();

// var_dump([$fdinst, $finst]);

$data = array();
if(is_numeric($old_sensor) && is_numeric($new_sensor) && $neum_id!=NULL){

  try{
    $db = DB::getInstance();

    $re = $db->query("SELECT * FROM uman_neumaticos WHERE ID_NEUMATICO!=$neum_id AND ID_SENSOR=$new_sensor;");
    if($re->count() > 0 && $new_sensor>0){
      $re = $re->results()[0];
      $neum = ($verNeumaticoPor=='fuego' ? $re->NUMEROFUEGO : $re->NUMIDENTI);
      $data = array(
        'title'=>'No se puede continuar con la operación',
        'text'=>'El sensor está asignado a otro neumático ('.$neum.'). Puede desvincular el sensor del neumático ('.$neum.') y volver a intentar. '.
        '<br/><br/><small>Si cree que es un error, pruebe actualizando la página y vuelva a intentarlo, de lo contrario, si el problema persiste, por favor contacte a soporte técnico.</small>',
        'html'=>true,
        'type'=>'error'
      );
    }
    else{
      if($old_sensor!=0){
        //Si tiene baja se da de baja, de lo contrario quedará disponible en stock para su uso
        if(is_numeric($baja)){
          $os = $db->query("UPDATE uman_sensores SET ESTADO='BAJA', BAJA=$baja, FECHA_BAJA='$fdinst' WHERE ID_SENSOR=$old_sensor;");
          if($os){
            (new Historial())->retiro_sensor($old_sensor, $neum_id, $fdinst);
            (new Historial())->baja_sensor($old_sensor, $neum_id, $fdinst);
          }
        }
        else{
          $os = $db->query("UPDATE uman_sensores SET ESTADO='DISPONIBLE' WHERE ID_SENSOR=$old_sensor;");
          if($os) (new Historial())->retiro_sensor($old_sensor, $neum_id, $fdinst);
        }
      }

      $ns = $db->query("UPDATE uman_sensores SET ESTADO='USO' WHERE ID_SENSOR=$new_sensor;");
      $ne = $db->query("UPDATE uman_neumaticos SET ID_SENSOR=$new_sensor WHERE ID_NEUMATICO=$neum_id;");

      $msj = 'El sensor se ha asignado correctamente al neumático.';
      if($new_sensor==0) $msj = 'El sensor se ha desvinculado del neumático.';
      if($ne){
        if($new_sensor != 0)
          (new Historial())->instalacion_sensor($new_sensor, $neum_id, $finst);
        $data = array(
          'title'=>'Sensor actualizado',
          'text'=>$msj,
          'type'=>'success'
        );

        Core::actualizar_umanblue();
      }
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
