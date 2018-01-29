<?php
	require 'autoload.php';

	session_start();
	$sess_id = session_id();
	$acceso = new Acceso($_SESSION, $sess_id);
	header("Content-Type: text/json");
 	if(!$acceso->Permitido()){
		echo json_encode(array('error'=>true, 'success'=>false, 'msg'=>$acceso->MensajeError()));
	}
	else{
		$usuario    = $_SESSION[$sess_id]['user'];
		$equipo     = (isset($_POST['equipo']))?$_POST['equipo']:NULL;
		$comment_id = (isset($_POST['comentario']))?$_POST['comentario']:NULL;

		if($equipo!=NULL && $comment_id!=NULL){
		 $db = DB::getInstance();
		 $sql = "SELECT * FROM uman_tipo_reconocimiento WHERE id=$comment_id";
		 $reconocimiento = $db->query($sql);
		 $reconocimiento = $reconocimiento->count() > 0 ? $reconocimiento->results()[0] : '';

		 $sql = "UPDATE uman_alarmas 
		  SET 
			 ALARMAESTADO='1', 
			 ALARMAFECHARECONOCEUMANWEB=NOW(), 
			 USUARIO='$usuario', 
			 COMENTARIOS='$reconocimiento->descripcion' 
			WHERE ALARMANUMCAMION='$equipo' AND ALARMAESTADO='0'";
		 $recon = $db->query($sql);

			echo json_encode(array('success'=>true, 'error'=>false, 'msg'=>''));
		}
		else{
			if($equipo == NULL)     $msg = 'No ha seleccionado un equipo.';
			if($comment_id == NULL) $msg = 'Debe seleccionar un comentario.';
			echo json_encode(array('error'=>true, 'success'=>false, 'msg'=>$msg));
		}
	}
?>