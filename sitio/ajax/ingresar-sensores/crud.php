<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$modo = isset($_POST['modo']) ? $_POST['modo'] : NULL;

	$data = array(); 

	if($modo == 'agregar'){
		$codigo = isset($_POST['c']) ? $_POST['c'] : NULL;
		$tipo   = isset($_POST['t']) ? $_POST['t'] : NULL;

		if($codigo != NULL && $tipo != NULL){
			if(count($codigo) == count($tipo)){
				$db    = DB::getInstance();
				$sql   = '';
				$error = '';
				for($i=0; $i<count($codigo); $i++){
					//Verificar si el código ya existe
					$tsql  = sprintf("SELECT * FROM uman_sensores WHERE CODSENSOR='%s';", $codigo[$i]);
					$res   = $db->query($tsql);

					if($res->count() == 0){
						$sql .= sprintf("INSERT INTO uman_sensores VALUES(NULL, '%s', '%s', 'DISPONIBLE',  NOW(), NULL);\n",
							$codigo[$i], $tipo[$i]);
					}
					else{
						$error .= '<li>Ya existe un sensor con el código '.$codigo[$i].'. (Se ha omitido la inserción).</li>';
					}
				}

				if($sql != ''){
					$res = $db->query($sql);
					// echo $sql;
					if($res){
						foreach($codigo as $c){
							$sql = sprintf("SELECT * FROM uman_sensores WHERE CODSENSOR='%s';", $c);
							$res = $db->query($sql);
							if($res->count() == 1) (new Historial())->creacion_sensor($res->results()[0]->ID_SENSOR);
						}
						if($error == '')							
							$data = array(
								'title'=>'Ingreso de Sensores',
								'text'=>'Todos los sensores se han agregado exitosamente.',
								'type'=>'success',
								'html'=>false,
							);
						else
							$data = array(
							'title'=>'Ingreso de Sensores',
							'text'=>'Algunos sensores no pudieron ser agregados.<br/><ul>'.$error.'</ul>',
							'type'=>'warning',
							'html'=>true,
						);
					}
					else{
						$data = array(
							'title'=>'Ingreso de Sensores',
							'text'=>'ha ocurrido un problema al intentar ingresar los sensores.<br/>'.$db->getPDO()->errorInfo()[2],
							'type'=>'error',
							'html'=>true,
						);
					}
				}
				else{
					$data = array(
						'title'=>'Ingreso de Sensores',
						'text'=>'Al parecer todos los sensores que intenta ingresar ya se encuentran en la base de datos, puede utilizar los filtros para buscarlos si desea ver o modificar su información.',
						'type'=>'info',
						'html'=>false,
					);
				}
			}
			else{
				$data = array(
					'title'=>'Ingreso de Sensores',
					'text'=>'No se ha realizado la operación ya que los datos no coinciden, por favor verifique la información y si el problema persiste contáctese con soporte o el administrador.',
					'type'=>'error',
				);
			}
		}
		else{
			$data = array(
				'title'=>'Ingreso de Sensores',
				'text'=>'ha enviado una consulta vacía, por favor verifique los datos antes de enviar la información.',
				'type'=>'error',
			);
		}
	}
	else if($modo == 'editar'){
	 // 17_01_2018 CT - Se agrega el codigo del sensor para su edición
		$id     = isset($_POST['id'])     ? $_POST['id']     : NULL;
        $codigo = isset($_POST['codigo']) ? $_POST['codigo'] : NULL;
		$tipo   = isset($_POST['tipo'])   ? $_POST['tipo']   : NULL;
		$estado = isset($_POST['estado']) ? $_POST['estado'] : NULL;
		$razon  = isset($_POST['razon'])  ? $_POST['razon']  : NULL;


		if($id != NULL && $codigo != NULL){
			$db  = DB::getInstance();

                $sql = sprintf("UPDATE uman_sensores 
				SET  CODSENSOR='%s', TIPO='%s', ESTADO='%s', BAJA='%d' 
				WHERE ID_SENSOR=%d;", $codigo, $tipo, $estado, $razon, $id);

			$res = $db->query($sql);

          if($res){
	  // 17_01_2018 CT - AAl modificar el sensor actualizar actualizar el codigo y FLAG_SERV_UMAN 
              Core::actualizar_umanblue($id);
              (new Historial())-> modificacion_sensor($id);
				$data = array(
					'title'=>'Ingreso de Sensores',
					'text'=>'El sensor se ha modificado exitosamente de la base de datos.',
					'type'=>'success',
					'html'=>false,
				);

			}
			else{
				$data = array(
					'title'=>'Ingreso de Sensores',
					'text'=>'Ha ocurrido un problema al intentar modificar el sensor.<br/>'.$db->getPDO()->errorInfo()[2],
					'type'=>'error',
					'html'=>true,
				);
			}
		}
		else{
			$data = array(
				'title'=>'Ingreso de Sensores',
				'text'=>'No se ha podido determinar el sensor que desea modificar, por favor verifique la información antes de enviar.',
				'type'=>'error',
				'html'=>true,
			);
		}
	}
	else if($modo == 'eliminar'){
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;
        $db    = DB::getInstance();
// 17_01_2018 CT - Se agrega validación que no permite eliminar sensores si este se encuentran en uso
            //Verificar si el sensor se encuentra en uso
            $tsql = sprintf("SELECT * FROM uman_sensores WHERE ID_SENSOR=%d AND ESTADO = 'USO';", $id);
            $res1 = $db->query($tsql);

        if($res1->count() == 0){
            if($id != NULL){
                $sql = sprintf("DELETE FROM uman_sensores WHERE ID_SENSOR=%d AND ESTADO != 'USO';", $id);
                $res = $db->query($sql);

                if($res){

                    (new Historial())->eliminacion_sensor($id);
                    $data = array(
                        'title'=>'Ingreso de Sensores',
                        'text'=>'El sensor se ha eliminado exitosamente de la base de datos.',
                        'type'=>'success',
                        'html'=>false
                    );
                }
                else{
                    $data = array(
                        'title'=>'Ingreso de Sensores',
                        'text'=>'Ha ocurrido un problema al intentar eliminar el sensor.<br/>'.$db->getPDO()->errorInfo()[2],
                        'type'=>'error',
                        'html'=>true
                    );
                }
            }
            else{
                $data = array(
                    'title'=>'Ingreso de Sensores',
                    'text'=>'No se ha podido determinar el sensor que desea eliminar, por favor verifique la información antes de enviar.',
                    'type'=>'error',
                    'html'=>true
                );
            }
        }
        else{
            $data = array(
                'title'=>'Ingreso de Sensores',
                'text'=>'No se permite eliminar sensores si este se encuentra en uso.',
                'type'=>'error',
                'html'=>true
            );
        }


	}
	else if($modo == 'obtener'){
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;

		$data = array(
			'title'=>'Ingreso de Sensores',
			'text'=>'',
			'type'=>'error',
			'html'=>false,
		);

		if($id != NULL && is_numeric($id)){
			$db  = DB::getInstance();
			$sql = sprintf("SELECT * FROM uman_sensores WHERE ID_SENSOR=%d;", $id);
			$res = $db->query($sql);

			if($res->count() == 1){
				$res = $res->results()[0];
				$data['data'] = array(
					'id'=>$res->ID_SENSOR,
					'codigo'=>$res->CODSENSOR,
					'tipo'=>$res->TIPO,
					'estado'=>$res->ESTADO,
					'baja'=>$res->BAJA,
				);
				$data['type'] = 'success';
			}
			else{
				$data['text'] = 'No se ha encontrado el sensor seleccionado, es probable que haya sido eliminado, por favor actualice la página y compruebe que aún exista.';
			}
		}
		else{
			$data['text'] = 'El id recibido no corresponde a un dato válido, por favor verifique la información enviada y vuelva a intentarlo.';
		}
	}

	header("Content-Type: application/json");
	echo json_encode($data);