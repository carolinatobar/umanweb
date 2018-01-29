<?php

require '../autoload.php';

$id   = isset($_POST['id'])   && $_POST['id']   != '' ? $_POST['id']    : '';
$num  = isset($_POST['num'])  && $_POST['num']  != '' ? $_POST['num']   : ''; 

if($id != ''){

  $obj_eqp  = new Equipo();

  try{

    $deleted  = $obj_eqp->eliminar($id,$num);

    if($deleted){
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
