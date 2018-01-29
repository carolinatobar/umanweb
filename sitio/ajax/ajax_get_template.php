<?php
require '../autoload.php';

$neu  = isset($_GET['neu']) && $_GET['neu'] != '' ? $_GET['neu']  : '';
$pos  = isset($_GET['pos']) && $_GET['pos'] != '' ? $_GET['pos']  : '';
$eje  = $pos != '' && $pos < 3 ? 1 : 2;


$obj_neu  = new Neumatico();
$mtr_neu  = $obj_neu->get_full($neu);

if(isset($mtr_neu)){

  $marca		  = isset($mtr_neu[0]->MARCA)     && $mtr_neu[0]->MARCA     != '' ? $mtr_neu[0]->MARCA      : '';

  $modelo     = isset($mtr_neu[0]->MODELO)    && $mtr_neu[0]->MODELO    != '' ? $mtr_neu[0]->MODELO     : '';

  $dimension	= isset($mtr_neu[0]->DIMENSION) && $mtr_neu[0]->DIMENSION != '' ? $mtr_neu[0]->DIMENSION  : '';
  
  $compuesto  = isset($mtr_neu[0]->COMPUESTO) && $mtr_neu[0]->COMPUESTO != '' ? $mtr_neu[0]->COMPUESTO  : '';


  $template = $obj_neu->check_template($eje, $marca, $modelo, $dimension, $compuesto);

  if(isset($template[0]->ID_PLANTILLA) && $template[0]->ID_PLANTILLA != ''){
    $return['id'] = $template[0]->ID_PLANTILLA;
    echo json_encode($return);
  } else {
    return false;
  }


} else {
  return false;
}
