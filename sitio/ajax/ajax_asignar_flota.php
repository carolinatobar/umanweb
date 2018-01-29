<?php

require '../autoload.php';

//  'eqp_id='+eqp_id+"&flt_id="+flt_id,

$eqp_id = isset($_POST['eqp_id']) && $_POST['eqp_id'] != '' ? $_POST['eqp_id']  : '';
$flt_id = isset($_POST['flt_id']) && $_POST['flt_id'] != '' ? $_POST['flt_id']  : '';


if($eqp_id != '' && $flt_id != ''){

  $obj_eqp  = new Equipo();

  $array_update['NUMFLOTA'] = $flt_id;

  try{

    $updated  = $obj_eqp->modificar($eqp_id, $array_update);

    if($updated){
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
