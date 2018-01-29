<?php

require '../autoload.php';

$obj_gnr  = new General();

//REALIZANDO LOOP DE GUARDADO
//--------------------------
if(isset($_POST) && count($_POST) > 0){
  foreach($_POST as $pos_num => $pos_val){
    try{
      $updated  = $obj_gnr->update_position($pos_num, array("NOMENCLATURA" => $pos_val));
      $results[]  = "OK";
    } catch (Exception $e){
      $results[]  = "NOT";
    }
  }


  //COMPROBANDO QUE EL LOOP DE GUARDADO ESTE OK
  if(in_array("NOT", $results)){
    echo "ERROR|Todos o algunas posicion no se han podido actualizar.";
  } else {
    echo "OK";
  }

} else {
  echo "NO_POST";
}
