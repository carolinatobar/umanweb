<?php
require '../autoload.php';

//  codigo=asdas&neumaticos=4&caja=&flota=

$name = isset($_POST['name']) && $_POST['name'] != '' ? $_POST['name']  : '';

if($name != ''){

  $obj_flt  = new Flota();

  $array_insert['NOMBRE'] = $name;

  try{

    $return   = $obj_flt->insertar($array_insert) ? 'OK' : 'ERROR';
    echo $return;

  } catch (Exception $e){
    var_dump($e);
  }


} else {
  echo "NO_POST";
}
