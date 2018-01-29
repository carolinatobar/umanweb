<?php
  require '../../autoload.php';

  $acc = new Acceso(true);

  $db  = DB::getInstance();
  $dx = $db->query("SELECT * FROM uman_sim ORDER BY COMPANIA");

  $data = array();
  $data['data'] = array();
  foreach($dx->results() as $d){
    $data['data'][] = array(
      'btn'=>'<button class="btn btn-info btn-xs pull-left" onclick="editar('.$d->ID.');"><i class="fa fa-pencil"></i></button>'.
             '<button class="btn btn-danger btn-xs pull-left" onclick="eliminar('.$d->ID.');"><i class="fa fa-trash"></i></button>',
      'telefono'=>$d->TELEFONO,
      'compania'=>$d->COMPANIA,
      'estado'=>'<span class="'.$d->ESTADO.'">'.$d->ESTADO.'</span>',
    );
    // print "<tr onclick=modaleditar('".$datos['ID']."','".$datos['TELEFONO']."','".$datos['COMPANIA']."')>";    
  }

  $data['type'] = 'success';

  header("Content-Type: application/json");
  echo json_encode($data);
?>