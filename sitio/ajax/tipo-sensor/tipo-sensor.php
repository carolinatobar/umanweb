<?php
	require '../../autoload.php';

  $acc = new Acceso(true);
  $db  = DB::getInstance();

  $sql = "SELECT * FROM uman_tiposensor";
  $res = $db->query($sql); 

  foreach($res->results() as $ts){
  	$data['data'][] = array(
      'btn'=>'<button class="btn btn-info btn-xs" onclick="modificar(this);" data-id="'.$ts->id.'">
        <i class="fa fa-pencil" aria-hidden="true"></i>
      </button>
      <button class="btn btn-danger btn-xs" onclick="eliminar(this);" data-id="'.$ts->id.'">
        <i class="fa fa-trash" aria-hidden="true"></i>
      </button>',
			'nomenclatura'=>$ts->tipo,
			'nombre'=>'<img src="'.$ts->imagen.'" style="float: center !important; width: auto !important;" class="icono-x36" /><span style="float: right; margin-top: 18px;">'.$ts->nombre.'</span>',
      'temperatura'=>'<i class="fa fa-'.($ts->mide_temperatura == 1 ? 'check text-success': 'times text-danger').' fa-2x" aria-hidden="true"></i>',
      'presion'=>'<i class="fa fa-'.($ts->mide_presion == 1 ? 'check text-success': 'times text-danger').' fa-2x" aria-hidden="true"></i>',
      'humedad'=>'<i class="fa fa-'.($ts->mide_humedad == 1 ? 'check text-success': 'times text-danger').' fa-2x" aria-hidden="true"></i>',
      'combustible'=>'<i class="fa fa-'.($ts->mide_combustible == 1 ? 'check text-success': 'times text-danger').' fa-2x" aria-hidden="true"></i>',
      'gas'=>'<i class="fa fa-'.($ts->mide_gas == 1 ? 'check text-success': 'times text-danger').' fa-2x" aria-hidden="true"></i>',
      'descripcion'=>($ts->descripcion)
    );
  }

  header("Content-type: application/json");
  echo json_encode($data);

 ?>