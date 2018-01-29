<?php
require '../autoload.php';

//  'v_equipo='+v_equipo+"&v_posicion="+v_posicion+"&v_neumatico="+v_neumatico;

$equipo     = isset($_POST['v_equipo'])     && $_POST['v_equipo']     != ''   ? $_POST['v_equipo']    : '';
$posicion   = isset($_POST['v_posicion'])   && $_POST['v_posicion']   != ''   ? $_POST['v_posicion']  : '';
$neumatico  = isset($_POST['v_neumatico'])  && $_POST['v_neumatico']  != ''   ? $_POST['v_neumatico'] : '';


if($equipo != '' && $posicion != '' && $neumatico != ''){

  $obj_eqp  = new Equipo();
  $field    = "NEUM".$posicion;
  $array_update[$field]  = $neumatico;

  $obj_neu  = new Neumatico();
  $neu_update['ESTADO']  = 'USO';

  try{

    $updated_equipo     = $obj_eqp->modificar($equipo, $array_update);
    $updated_neumatico  = $obj_neu->modificar($neumatico, $neu_update);

    if($updated_equipo){
      echo "OK";
    } else {
      echo "NOT";
    }


  } catch (Exception $e){
    var_dump($e);
  }


} else {
  echo "NO_POST";
}
