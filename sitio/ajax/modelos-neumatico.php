<?php
  require '../autoload.php';

  $acc = new Acceso(true);

  $marca = isset($_REQUEST['marca']) ? $_REQUEST['marca'] : null;

  $db = DB::getInstance();

  if($marca != null) $sql = "SELECT DISTINCT MODELO FROM uman_neumaticos WHERE MARCA LIKE '%{$marca}%';";
  else $sql = "SELECT DISTINCT MODELO FROM uman_neumaticos;";

  $modelos = $db->query($sql);

  $data = array();
  
  if($modelos->count() > 0)
  {
    foreach($modelos->results() as $m)
    {
      $data[] = $m->MODELO;
    }
  }

  header("Content-Type: text/json");
  echo json_encode($data);