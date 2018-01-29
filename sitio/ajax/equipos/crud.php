<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	$db   = DB::getInstance();

	$id   = isset($_POST['id'])   ? $_POST['id']   : NULL;
	$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : NULL;
	$num  = isset($_POST['num'])  ? $_POST['num']  : NULL;
	$modo = isset($_POST['modo']) ? $_POST['modo'] : NULL;

	$data = array(
		'type'=>'error',
		'title'=>'',
		'text'=>''
	);

	if($modo == 'nuevo'){
		$error = '';
		if(!is_numeric($tipo)) $error .= 'El tipo seleccionado no corresponde a un dato válido.<br/>';
		if($num == NULL) $error .= 'Debe especificar un número de equipo. Puede contener valores alfanuméricos.<br/>';

		if($error == ''){
			//Verificar que no exista el camión antes de insertar
			$sql = sprintf("SELECT * FROM uman_camion WHERE NUMCAMION='%s';", $num);
			$res = $db->query($sql);

			if($res->count() > 0){
				$data = array(
					'type'=>'error',
					'title'=>'Nuevo registro',
					'text'=>'El registro ya existe en la base de datos, por favor selecciónelo desde la tabla y modifique los datos necesarios.',
				);
			}
			else{
				$sql = sprintf("INSERT INTO uman_camion VALUES(NULL, '%s', 0, NULL, %d);", $num, $tipo);
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
		if(!is_numeric($tipo)) $error .= 'El tipo seleccionado no corresponde a un dato válido.<br/>';
		if($num == NULL) $error .= 'Debe especificar un número de equipo. Puede contener valores alfanuméricos.<br/>';
		if(!is_numeric($id)) $error .= 'El ID seleccionado no corresponde a un dato válido.<br/>';

		if($error == ''){
			//Verificar que no exista otro camión con el mismo número antes de continuar
			$sql = sprintf("SELECT * FROM uman_camion WHERE NUMCAMION='%s' AND ID_CAMION!=%d;", $num, $id);
			$res = $db->query($sql);

			if($res->count() > 0){
				$data = array(
					'type'=>'error',
					'title'=>'Modificar Registro',
					'text'=>'Ya existe otro camión con el mismo nombre, por favor asegúrese de ingresar los datos correctamente.',
				);
			}
			else{
				$sql = sprintf("UPDATE uman_camion SET NUMCAMION='%s', tipo=%d WHERE ID_CAMION=%d;", $num, $tipo, $id);
				$res = $db->query($sql);

				if($res){
					$data = array(
						'type'=>'success',
						'title'=>'Modificar Registro',
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
	else if($modo == 'eliminar'){
		$error = '';
		if(!is_numeric($id)) $error .= 'El ID seleccionado no corresponde a un dato válido.<br/>';

		if($error == ''){
			$sql = sprintf("DELETE FROM uman_camion WHERE ID_CAMION=%d;",$id);
			$res = $db->query($sql);

			if($res){
				$data = array(
					'type'=>'success',
					'title'=>'Eliminar Registro',
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
				'title'=>'Faltan campos',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'obtener'){
		$error = '';
		if(!is_numeric($id)) $error .= 'El ID seleccionado no corresponde a un dato válido.<br/>';

		if($error == ''){
			$sql = sprintf("SELECT * FROM uman_camion WHERE ID_CAMION=%d;",$id);
			$res = $db->query($sql);

			if($res->count() == 1){
				$res = $res->results()[0];
				$data = array(
					'type'=>'success',
					'data'=>array(
						'id'=>$res->ID_CAMION,
						'num'=>$res->NUMCAMION,
						'tipo'=>$res->tipo,
					),
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
				'title'=>'Faltan campos',				
				'text'=>'No es posible continuar porque se han detectado los siguientes errores: <br/>'.$error,
				'type'=>'error',
				'html'=>true,
			);
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);