<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$db = DB2::getInstance();

	$sql = "SELECT * FROM uman_faenas
		ORDER BY nombre_faena ASC;";

	$res = $db->query($sql);

	$data = array('type'=>'error', 'html'=>true, 'title'=>'Gestión de Faenas', 'data'=>array());
	if($res->count()>0){
		$data['type'] = 'success';
		foreach($res->results() as $faena){
			$data['data'][] = array(
				'btn'=>'<button onClick="modificar(this);" class="btn btn-primary btn-xs edit" data-faena="'.$faena->id.'" data-toggle="tooltip" data-placement="right" title="Modificar Faena '.($faena->nombre_faena).'"><i class="fa fa-edit" aria-hidden="true"></i></button>'.
					'&nbsp;&nbsp;'.
					'<button onClick="eliminar(this);" class="btn btn-danger btn-xs delete" data-faena="'.$faena->id.'" data-toggle="tooltip" data-placement="right" title="Eliminar Faena '.($faena->nombre_faena).'"><i class="fa fa-remove" aria-hidden="true"></i></button>',
				'nombre'=>$faena->nombre_faena,
				'empresa'=>$faena->nombre_empresa,
				'nombre_db'=>$faena->nombre_db
			);
		}
	}
	else{
		$data['text'] = 'No hay faenas creadas';
	}

	header("Content-Type: application/json");
	echo json_encode($data);

?>