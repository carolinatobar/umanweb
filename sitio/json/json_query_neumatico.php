<?php

$id  = isset($_GET['id']) && is_numeric(trim($_GET['id'])) && trim($_GET['id']) > 0 ? trim($_GET['id']) : false;

if($id){

  include_once '../class/Core.php';
  require_once '../class/Neumatico.php';

  $obj_neu = new Neumatico();
  $arr_dat  = $obj_neu->get_full($id);

  if(isset($arr_dat) && count($arr_dat) > 0){

    echo  json_encode($arr_dat);

  } else {
    return false;
  }


} else {
  return false;
}
