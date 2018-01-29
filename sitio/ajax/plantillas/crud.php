<?php
	require '../../autoload.php';

	$acc = new Acceso(true);

	$db = DB::getInstance();

	$id = isset($_POST['id']) ? $_POST['id'] : NULL;
	$ac = isset($_POST['ac']) ? $_POST['ac'] : NULL;

	// var_dump($_POST);

	header("Content-Type: application/json");
	$data = array('type'=>'error', 'html'=>true, 'title'=>'Asignar Neumático');

	if($id != NULL){
		if(is_numeric($id)){			
			if($ac == 'eliminar'){
				$sql = sprintf("SELECT * FROM uman_neumaticos WHERE ID_PLANTILLA=%d;", $id);
				$res = $db->query($sql);
				if($res->count()==0){
					$sql = sprintf("DELETE FROM uman_plantilla WHERE ID_PLANTILLA=%d;", $id);
					$res = $db->query($sql);

					(new Historial())->borra_plantilla($id);

					Core::actualizar_umanblue();
		
					if($res){
						$data['type'] = 'success';
						$data['text']  = 'El registro ha sido eliminado';
						$data['title'] = 'Registro eliminado';
					}
					else{
						$data['type']  = 'error';
						$data['text']  = $db->getPDO()->errorInfo[2];
						$data['title'] = $db->getPDO()->errorInfo[1];
					}
				}else{
					$data['type']  = 'error';
					$data['text']  = "Plantilla en uso";
				}
			}
			else if($ac == 'obtener'){
				$sql = sprintf("SELECT * FROM uman_plantilla WHERE ID_PLANTILLA=%d;",$id);

				$d = $db->query($sql);

				if($d->count() >= 1){
					$d = $d->results()[0];
					
					$sql = sprintf("SELECT * FROM uman_neumaticos WHERE ID_PLANTILLA=%d;", $id);
					$res = $db->query($sql);
					$used=1;
					if($res->count()==0){
						$used=0;
					}

					$data['data'] = array(
						'id'=>$d->ID_PLANTILLA,
						'marca'=>$d->MARCA,
						'modelo'=>$d->MODELO,
						'dimension'=>$d->DIMENSION,
						'tmax'=>$d->TEMPMAX,
						'pmax'=>$d->PRESMAX,
						'pmin'=>$d->PRESMIN,
						'eje'=>$d->EJE,
						'compuesto'=>$d->COMPUESTO,
						'sensor'=>$d->SENSOR,
						'pif'=>$d->PIF,
						'prealarma'=>$d->PRE_ALARMA,
						'used'=>$used
					);

					$data['type']  = 'success';
					$data['text']  = '';
					$data['title'] = '';
				}
				else{
					$data['type']  = 'error';
					$data['text']  = 'No se ha encontrado el registro seleccionado, es probable que haya sido eliminado o el parámetro enviado no correspoonda.';
					$data['title'] = 'Registro no encontrado';
				}
			}
			else if($ac == 'editar'){
				$sql = sprintf("SELECT * FROM uman_neumaticos WHERE ID_PLANTILLA=%d;", $id);
				$res = $db->query($sql);
				if($res->count()==0){
					$marca 		= isset($_POST['marca'])     ? $_POST['marca']     : NULL;
					$modelo 	= isset($_POST['modelo'])    ? $_POST['modelo']    : NULL;
					$temp_max 	= isset($_POST['tempmax'])   ? $_POST['tempmax']   : NULL;
					$pres_min 	= isset($_POST['presmin'])   ? $_POST['presmin']   : NULL;
					$pres_max 	= isset($_POST['presmax'])   ? $_POST['presmax']   : NULL;
					$eje 		= isset($_POST['eje'])       ? $_POST['eje']       : NULL;
					$compuesto 	= isset($_POST['compuesto']) ? $_POST['compuesto'] : NULL;
					$dimension 	= isset($_POST['dimension']) ? $_POST['dimension'] : NULL;
					$sensor 	= isset($_POST['sensor'])    ? $_POST['sensor']    : NULL;
					$pif		= isset($_POST['pif'])       ? $_POST['pif']       : NULL;
					$pre_alarma = isset($_POST['pre_alarma'])? $_POST['pre_alarma']: NULL;

					$error = '';
					if(!is_numeric($pre_alarma))   $error .= '<li>Debe ingresar un valor numérico</li>';
					else if($pre_alarma>$temp_max) $error .= '<li>El valor de la pre-alarma debe ser inferior a la temperatura máxima.</li>';
					if(!is_numeric($temp_max))     $error .= '<li>Debe ingresar la temperatura máxima.</li>';
					if(!is_numeric($pres_max))     $error .= '<li>Debe ingresar la presión máxima.</li>';
					if(!is_numeric($pres_min))     $error .= '<li>Debe ingresar la presión mínima.</li>';
					if(!is_numeric($eje))          $error .= '<li>Debe seleccionar el eje de destino.</li>';
					if(!is_numeric($pif))          $error .= '<li>Debe ingresar la presión de inflado en frío.</li>';
					if($sensor == NULL)            $error .= '<li>Debe indicar el tipo de sensor.</li>';
					if($pres_min >= $pres_max)     $error .= '<li>La presión mínima debe ser inferior a la presión máxima.</li>';

					if($error == ''){
						//Para no agregar valores nulos o reemplazar por valor en blanco.
						$marca     = $marca != NULL ? $marca : '';
						$compuesto = $compuesto != NULL ? $compuesto : '';
						$modelo    = $modelo != NULL ? $modelo : '';
						$dimension = $dimension != NULL ? $dimension : '';

						$sql = sprintf("UPDATE uman_plantilla SET 
							MARCA='%s',
							MODELO='%s',
							DIMENSION='%s',
							TEMPMAX=%d,
							PRESMAX=%d,
							PRESMIN=%d,
							EJE=%d,
							COMPUESTO='%s',
							SENSOR='%s',
							PIF=%d,
							PRE_ALARMA=%d  
						WHERE ID_PLANTILLA=%d;",
						$marca, $modelo, $dimension, $temp_max, $pres_max, $pres_min, $eje, $compuesto, $sensor, $pif, $pre_alarma, $id);
						// echo $sql;
						$res = $db->query($sql);

						(new Historial())->modifica_plantilla($id);

						Core::actualizar_umanblue();

						if($res){
							$data['type'] = 'success';
							$data['text']  = 'El registro ha sido modificado';
							$data['title'] = 'Registro modificado';
						}
						else{
							$data['type']  = 'error';
							$data['text']  = $db->getPDO()->errorInfo[2];
							$data['title'] = $db->getPDO()->errorInfo[1];
						}
					}
					else{
						$data['type']  = 'error';
						$data['text']  = $error;
						$data['title'] = 'Debe completar los campos obligatorios';
					}
				}else{
					$temp_max 	= isset($_POST['tempmax'])   ? $_POST['tempmax']   : NULL;
					$pres_min 	= isset($_POST['presmin'])   ? $_POST['presmin']   : NULL;
					$pres_max 	= isset($_POST['presmax'])   ? $_POST['presmax']   : NULL;
					$pif		= isset($_POST['pif'])       ? $_POST['pif']       : NULL;
					$pre_alarma = isset($_POST['pre_alarma'])? $_POST['pre_alarma']: NULL;

					$error = '';
					if(!is_numeric($pre_alarma))   $error .= '<li>Debe ingresar un valor numérico</li>';
					else if($pre_alarma>$temp_max) $error .= '<li>El valor de la pre-alarma debe ser inferior a la temperatura máxima.</li>';
					if(!is_numeric($temp_max))     $error .= '<li>Debe ingresar la temperatura máxima.</li>';
					if(!is_numeric($pres_max))     $error .= '<li>Debe ingresar la presión máxima.</li>';
					if(!is_numeric($pres_min))     $error .= '<li>Debe ingresar la presión mínima.</li>';
					if(!is_numeric($pif))          $error .= '<li>Debe ingresar la presión de inflado en frío.</li>';
					if($pres_min >= $pres_max)     $error .= '<li>La presión mínima debe ser inferior a la presión máxima.</li>';

					if($error == ''){
						//Para no agregar valores nulos o reemplazar por valor en blanco.
						
						$sql = sprintf("UPDATE uman_plantilla SET 
							TEMPMAX=%d,
							PRESMAX=%d,
							PRESMIN=%d,
							PIF=%d,
							PRE_ALARMA=%d  
						WHERE ID_PLANTILLA=%d;",
						$temp_max, $pres_max, $pres_min, $pif, $pre_alarma, $id);
						// echo $sql;
						$res = $db->query($sql);

						(new Historial())->modifica_plantilla($id);

						Core::actualizar_umanblue();

						if($res){
							$data['type'] = 'success';
							$data['text']  = 'El registro ha sido modificado';
							$data['title'] = 'Registro modificado';
						}
						else{
							$data['type']  = 'error';
							$data['text']  = $db->getPDO()->errorInfo[2];
							$data['title'] = $db->getPDO()->errorInfo[1];
						}
					}
					else{
						$data['type']  = 'error';
						$data['text']  = $error;
						$data['title'] = 'Debe completar los campos obligatorios';
					}
				}
			}
		}
	}
	else{
		if($ac == 'nueva'){
			$marca 		= isset($_POST['marca'])     ? $_POST['marca']     : NULL;
			$modelo 	= isset($_POST['modelo'])    ? $_POST['modelo']    : NULL;
			$temp_max 	= isset($_POST['tempmax'])   ? $_POST['tempmax']   : NULL;
			$pres_min 	= isset($_POST['presmin'])   ? $_POST['presmin']   : NULL;
			$pres_max 	= isset($_POST['presmax'])   ? $_POST['presmax']   : NULL;
			$eje 		= isset($_POST['eje'])       ? $_POST['eje']       : NULL;
			$compuesto 	= isset($_POST['compuesto']) ? $_POST['compuesto'] : NULL;
			$dimension 	= isset($_POST['dimension']) ? $_POST['dimension'] : NULL;
			$sensor 	= isset($_POST['sensor'])    ? $_POST['sensor']    : NULL;
			$pif		= isset($_POST['pif'])       ? $_POST['pif']       : NULL;
			$pre_alarma = isset($_POST['pre_alarma'])? $_POST['pre_alarma']: NULL;

			$error = '';
			if(!is_numeric($pre_alarma)) $error .= '<li>Debe ingresar un valor numérico</li>';
			else if($pre_alarma>$temp_max)$error .= '<li>El valor de la pre-alarma debe ser inferior a la temperatura máxima.</li>';
			if(!is_numeric($temp_max))   $error .= '<li>Debe ingresar la temperatura máxima.</li>';
			if(!is_numeric($pres_max))   $error .= '<li>Debe ingresar la presión máxima.</li>';
			if(!is_numeric($pres_min))   $error .= '<li>Debe ingresar la presión mínima.</li>';
			if(!is_numeric($eje))        $error .= '<li>Debe seleccionar el eje de destino.</li>';
			if(!is_numeric($pif))        $error .= '<li>Debe ingresar la presión de inflado en frío.</li>';
			if($sensor == NULL)          $error .= '<li>Debe indicar el tipo de sensor.</li>';
			if($pres_min >= $pres_max)   $error .= '<li>La presión mínima debe ser inferior a la presión máxima.</li>';

			if($error == ''){
				$sql = sprintf("INSERT INTO uman_plantilla VALUES ('','%s','%s','%s',%d,%d,%d,%d,'%s','%s',%d,%d)",
					$marca, $modelo, $dimension, $temp_max, $pres_max, $pres_min, $eje, $compuesto, $sensor, $pif, $pre_alarma);

				$res = $db->query($sql);

				(new Historial())->crea_plantilla();
				
				Core::actualizar_umanblue();

				if($res){
					$data['type'] = 'success';
					$data['text']  = 'El registro ha sido creado';
				}
				else{
					$data['type']  = 'error';
					$data['text']  = $db->getPDO()->errorInfo[2];
					$data['title'] = $db->getPDO()->errorInfo[1];
				}
			}
			else{
				$data['type']  = 'error';
				$data['text']  = '<ul>Debe completar los campos obligatorios</ul>'.$error;
			}
		}
		else if($ac == 'obtener-plantilla'){
			$neu  = isset($_POST['neu']) && $_POST['neu'] != '' ? $_POST['neu']  : '';
			$pos  = isset($_POST['pos']) && $_POST['pos'] != '' ? $_POST['pos']  : '';
			$eje  = $pos != '' && $pos < 3 ? 1 : 2;

			$obj_neu  = new Neumatico();
			$mtr_neu  = $obj_neu->get_full($neu);

			if(isset($mtr_neu)){
			  $marca		  = isset($mtr_neu[0]->MARCA)     && $mtr_neu[0]->MARCA     != '' ? $mtr_neu[0]->MARCA      : '';
			  $modelo     = isset($mtr_neu[0]->MODELO)    && $mtr_neu[0]->MODELO    != '' ? $mtr_neu[0]->MODELO     : '';
			  $dimension	= isset($mtr_neu[0]->DIMENSION) && $mtr_neu[0]->DIMENSION != '' ? $mtr_neu[0]->DIMENSION  : '';
			  $compuesto  = isset($mtr_neu[0]->COMPUESTO) && $mtr_neu[0]->COMPUESTO != '' ? $mtr_neu[0]->COMPUESTO  : '';
			  $sensor     = isset($mtr_neu[0]->S_TIPO)    && $mtr_neu[0]->S_TIPO    != '' ? $mtr_neu[0]->S_TIPO     : NULL;
			  $template = $obj_neu->check_template($eje, $marca, $modelo, $dimension, $compuesto, $sensor);

			  if(isset($template[0]->ID_PLANTILLA) && $template[0]->ID_PLANTILLA != ''){
			  	$data['data']['id'] = $template[0]->ID_PLANTILLA;
			  	$data['type']       = 'success';
			  } else {
			    $data['data']['id'] = NULL;
			    $data['text']       = 'No existe una plantilla para este neumático en este eje.';
			  }
			} else {
			  $data['data']['id'] = NULL;
			  $data['text']       = 'No se ha podido obtener la información del neumático.';
			}
		}
		else{
			$data['type']  = 'error';
			$data['text']  = 'Parámetro debe ser un número entero.';
		}
	}
	

	echo json_encode($data);