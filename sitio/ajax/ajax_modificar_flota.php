<?php
require '../autoload.php';

$id   = isset($_POST['id'])   && $_POST['id']   != '' ? $_POST['id']    : '';
$name = isset($_POST['name']) && $_POST['name'] != '' ? $_POST['name']  : '';


if($id != '' && $name != ''){

  $obj_flt  = new Flota();

  $array_update['NOMBRE'] = $name;

  try{

    $updated  = $obj_flt->modificar($id, $array_update);

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
