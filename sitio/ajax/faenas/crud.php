<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$id      = isset($_POST['id'])             ? $_POST['id']             : NULL;
	$nombre  = isset($_POST['nombre_faena'])   ? $_POST['nombre_faena']   : NULL;
	$empresa = isset($_POST['nombre_empresa']) ? $_POST['nombre_empresa'] : NULL;
	$accion  = isset($_POST['ac'])             ? $_POST['ac']             : NULL;

	$data = array('type'=>'error', 'title'=>'Gestión de Faenas', 'html'=>true, 'data'=>array());

	$db = DB2::getInstance();

	if($accion == 'crear'){
		$error = '';
		if($nombre == NULL || $nombre == '')  $error .= '<li>Debe proporcionar un nombre de Faena.</li>';
		if($empresa == NULL || $nombre == '') $error .= '<li>Debe ingresar la Empresa a la que corresponde la faena.</li>';

		$nombre_db = str_replace(' ', '_', $nombre);
		$nombre_db = preg_replace('/[^a-zA-Z]/', '', $nombre_db);

		if($nombre_db == '' || strlen($nombre_db) <= 5) $error .= '<li>El nombre de la faena contiene demasiados carácteres especiales, por lo que no se puede crear un nombre válido para la base de datos.</li>';

		$sql = sprintf("SELECT * FROM uman_faenas WHERE nombre_faena = '%s' OR nombre_db = '%s';", $nombre_faena, $nombre_db);
		$res = $db->query($sql);
		if($res->count()>0) $error .= '<li>Ya existe una faena con el nombre proporcionado, intente con otro o modifique desde la tabla.</li>';

		if($error == ''){
			/*
			 * TODO: Crear script SQL para cambiar el nombre de la base de datos segun los nuevos datos recibidos
			 * preferiblemente en una clase nueva especialmente ideada para ello y que permita el acceso completo 
			 * para poder ejecutar consultas a nivel de servidor.
			 */
			$data['type'] = 'success';
		}
		else{
			$data['text'] = "<ul>No se ha podido continuar porque se han detectado los siguientes errores:{$error}</ul>";
		}
	}

	else if($accion == 'obtener'){
		if($id != NULL && is_numeric($id)){
			$sql = sprintf("SELECT * FROM uman_faenas WHERE id=%d;", $id);
			$res = $db->query($sql);

			if($res->count()==1){
				$data['data'] = array(
					'id'=>$res->id,
					'nombre'=>$res->nombre_faena,
					'empresa'=>$res->nombre_empresa,
					'nombre_db'=>$res->nombre_db
				);
			}
			else{
				$data['text'] = 'No se ha encontrado faena con el ID proporcionado.';	
			}
		}
		else{
			$data['text'] = 'Debe seleccionar una faena de la tabla, No ha proporcionado un ID válido.';
		}
	}

	else if($accion == 'modificar'){
		if($id != NULL && is_numeric($id)){
			if($nombre != NULL && $nombre != '') $error .= '<li>Debe proporcionar un nombre de Faena.</li>';
			if($empresa != NULL && $nombre != '') $error .= '<li>Debe ingresar la Empresa a la que corresponde la faena.</li>';

			$sql = sprintf("SELECT * FROM uman_faenas 
				WHERE nombre_faena = '%s' OR nombre_db = '%s' AND id!=%d", $nombre_faena, $nombre_db, $id);
			$res = $db->query($sql);
			if($res->count()>0) $error .= '<li>Ya existe una faena con el nombre proporcionado, intente con otro o modifique desde la tabla.</li>';

			if($error == ''){				
				$sql = sprintf("UPDATE uman_faenas SET nombre_faena='%s', nombre_empresa='%s', nombre_db='%s' 
					WHERE id=%d;", $nombre, $empresa, $nombre_db, $id);
				$res = $db->query($sql);

				if($res){
					/*
					 * TODO: Crear script SQL para cambiar el nombre de la base de datos segun los nuevos datos recibidos
					 * preferiblemente en una clase nueva especialmente ideada para ello y que permita el acceso completo 
					 * para poder ejecutar consultas a nivel de servidor.
					 */

					$data['type'] = 'success';
					$data['text'] = 'Se han actualizado correctamente los datos de la Faena.';
				}
				else{
					$data['text'] = 'Al parecer ha ocurrido un error al intentar eliminar el registro.<br/>'.$db->errorInfo[1];
				}
			}
			else{
				$data['text'] = "<ul>No se ha podido continuar porque se han detectado los siguientes errores:{$error}</ul>";
			}
		}
		else{
			$data['text'] = 'Debe seleccionar una faena de la tabla, No ha proporcionado un ID válido.';
		}
	}

	else if($accion == 'eliminar'){
		if($id != NULL && is_numeric($id)){
			$sql = sprintf("DELETE FROM uman_faenas WHERE id=%d;", $id);
			$res = $db->query($sql);

			if($res){
				$data['type'] = 'success';
				$data['text'] = 'El registro se ha eliminado correctamente.';
			}
			else{
				$data['text'] = 'Al parecer ha ocurrido un error al intentar eliminar el registro.<br/>'.$db->errorInfo[1];
			}
		}
		else{
			$data['text'] = 'Debe seleccionar una faena de la tabla, No ha proporcionado un ID válido.';
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);

?>