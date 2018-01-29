<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	
	$db       = DB::getInstance();

	$id       = isset($_POST['id'])       ? $_POST['id']       : NULL;
	$modo     = isset($_POST['modo'])     ? $_POST['modo']     : NULL;
	$compania = isset($_POST['compania']) ? $_POST['compania'] : NULL;
	$telefono = isset($_POST['telefono']) ? $_POST['telefono'] : NULL;
	$estado   = isset($_POST['estado'])   ? $_POST['estado']   : NULL;

	$data     = array();

	if($modo == 'nueva'){
		$error = '';
		if($compania == NULL) $error .= 'Debe seleccionar la compañía telefónica.<br/>';
		// 15_01_2018 CT - Se eliminan validaciones con prefijo y largo maximo
	    if($telefono == NULL) $error .= 'Debe ingresar el número telefónico.<br/>';

		$estado = 'Disponible';

		if($error == ''){
			//Verificar que el número no exista antes de insertar
			$sql = sprintf("SELECT * FROM uman_sim WHERE TELEFONO='%s';", $telefono);
			$res = $db->query($sql);

			if($res->count() > 0){
				$data = array(
					'type'=>'error',
					'title'=>'Nuevo registro',
					'text'=>'El registro ya existe en la base de datos, por favor selecciónelo desde la tabla y modifique los datos necesarios.',
				);
			}
			else{
				$sql = sprintf("INSERT INTO uman_sim VALUES(NULL, '%s', '%s', '%s', NULL);", $telefono, $compania, $estado);

				$res = $db->query($sql);

				if($res){
					$data = array(
						'type'=>'success',
						'title'=>'Nuevo registro',
						'text'=>'El registro se ha creado con éxito.',
					);
				}
				else{
					$data = array(
						'type'=>'error',
						'title'=>$db->getPDO()->errorInfo[1],
						'text'=>$db->getPDO()->errorInfo[2],
					);
				}
			}
		}
		else{
			$data = array(
				'title'=>'Faltan campos',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'editar'){
		$error = '';
		if(!is_numeric($id)) $error .= 'El ID del registro no corresponde a un dato válido. <br/>';
		if($compania == NULL) $error .= 'Debe seleccionar la compañía telefónica.<br/>';
		// 15_01_2018 CT - Se eliminan validaciones con prefijo y largo maximo
		if($telefono == NULL) $error .= 'Debe ingresar el número telefónico.<br/>';

		if(in_array(array('Disponible','Uso', 'Baja') ,$estado)) $error .= 'El valor enviado de estado no es un valor permitido.<br/>';
		
		if($error == ''){
			$sql = sprintf("UPDATE uman_sim SET TELEFONO='%s', COMPANIA='%s', ESTADO='%s' WHERE ID=%d", $telefono, $compania, $estado, $id);

			$res = $db->query($sql);

			if($res){
				$data = array(
					'type'=>'success',
					'title'=>'Registro modificado',
					'text'=>'El registro se ha modificado con éxito.',
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>$db->getPDO()->errorInfo[1],
					'text'=>$db->getPDO()->errorInfo[2],
				);
			}
		}
		else{
			$data = array(
				'title'=>'Error',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'eliminar'){
		$error = '';
		if(!is_numeric($id)) $error .= 'El ID del registro no corresponde a un dato válido. <br/>';

		if($error == ''){
			$sql = sprintf("DELETE FROM uman_sim WHERE ID=%d;",$id);

			$res = $db->query($sql);

			if($res){
				$data = array(
					'type'=>'success',
					'title'=>'Registro eliminado',
					'text'=>'El registro se ha eliminado con éxito.',
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>$db->getPDO()->errorInfo[1],
					'text'=>$db->getPDO()->errorInfo[2],
				);
			}
		}
		else{
			$data = array(
				'title'=>'Error',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'obtener'){
		if(is_numeric($id)){
			$sql = sprintf("SELECT * FROM uman_sim WHERE ID=%d;", $id);

			$res = $db->query($sql);

			if($res->count() == 1){
				$res = $res->results()[0];
				$data['data'] = array(
					'id'=>$res->ID,
					'telefono'=>$res->TELEFONO,
					'compania'=>$res->COMPANIA,
					'estado'=>$res->ESTADO
				);
				$data['type'] = 'success';
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>'Registro inexistente',
					'text'=>'El registro no existe, es probable que haya sido eliminado, por favor actualice la página para verificar y vuelva a intentar.',
					'data'=>array()
				);
			}
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);