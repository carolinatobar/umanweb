<?php
require '../autoload.php';

$id   = isset($_POST['id'])   && $_POST['id']   != '' ? $_POST['id']    : '';

if($id != ''){

  $obj_flt  = new Flota();

  try{

    $deleted  = $obj_flt->eliminar($id);

    if($deleted){

      $obj_eqp  = new Equipo();
      $reseted  = $obj_eqp->reset_flota($id);

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
