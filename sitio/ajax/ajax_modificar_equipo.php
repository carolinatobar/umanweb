<?php
require '../autoload.php';

//  codigo=asdas&neumaticos=4&caja=&flota=

$id         = isset($_POST['id'])         && $_POST['id'] != ''         ? $_POST['id']          : '';
$codigo     = isset($_POST['codigo'])     && $_POST['codigo'] != ''     ? $_POST['codigo']      : '';
$tipo       = isset($_POST['tipo'])       && $_POST['tipo'] != ''       ? $_POST['tipo']        : '';


if($id != '' && $codigo != '' && $tipo !=''){

  $obj_eqp  = new Equipo();

  $array_update['NUMCAMION']      = $codigo;
  $array_update['tipo']           = $tipo;

  try{

    $updated  = $obj_eqp->modificar($id, $array_update);

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
