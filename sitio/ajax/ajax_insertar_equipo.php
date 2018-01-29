<?php

require '../autoload.php';

//  codigo=asdas&neumaticos=4&caja=&flota=

$codigo     = isset($_POST['codigo'])     && $_POST['codigo'] != ''     ? $_POST['codigo']      : '';
$tipo       = isset($_POST['tipo'])       && $_POST['tipo'] != ''       ? $_POST['tipo']        : '';


if($codigo != ''){

  $obj_eqp  = new Equipo();

  $array_insert['NUMCAMION']      = $codigo;
  $array_insert['tipo']           = $tipo;
  $array_insert['NUMFLOTA']       = "0";
  $array_insert['ID_CAJAUMAN']    = "0";



  try{

    $return   = $obj_eqp->insertar($array_insert) ? 'OK' : 'ERROR';
    $return   = $obj_eqp->crear_ultimo($codigo,$tipo) ? 'OK' : 'ERROR';
    echo $return;
    
  } catch (Exception $e){
    var_dump($e);
  }

} else {
  echo "NO_POST";
}