<?php
	require '../../autoload.php';

	$acc = new Acceso(true);
	$db  = DB::getInstance();
	$gen = new General();

	$modo = isset($_POST['modo']) ? $_POST['modo'] : NULL;

	$data = array(
		'type'=>'',
		'title'=>'Ingreso de Neumáticos',
		'text'=>'',
		'html'=>true,
	);

	$neumsegun    = $gen->getParamValue('verneumaticosegun');

	if($modo == 'crear'){
		$numidenti 		= isset($_POST['t1']) ? $_POST['t1'] : null;
		$marca 			= isset($_POST['marca']) ? $_POST['marca'] : null;
		$modelo 		= isset($_POST['modelo']) ? $_POST['modelo'] : null;
		$compuesto 		= isset($_POST['compuesto']) ? $_POST['compuesto'] : null;
		$dimension 		= isset($_POST['dimension']) ? $_POST['dimension'] : null;
		$numero_fuego	= isset($_POST['numero_fuego']) ? $_POST['numero_fuego'] : null;

		// var_dump($_POST);

		$mensaje = '';
		if($numero_fuego == null) $mensaje .= '<li>Debe ingresar el número de fuego.</li>';
		if($numidenti == null)    $mensaje .= '<li>Debe ingresar el nombre del neumático.</li>';
		if($marca == null)        $mensaje .= '<li>Debe ingresar la marca.</li>';
		if($compuesto == null)    $mensaje .= '<li>Debe ingresar el nombre del compuesto.</li>';
		if($dimension == null)    $mensaje .= '<li>Debe ingresar la dimensión.</li>';

		if($mensaje == ''){
			$db = DB::getInstance();

			//Verificar que el número de fuego ni numidenti no exista
			$sql = sprintf("SELECT * FROM uman_neumaticos 
				WHERE NUMIDENTI='%s' OR NUMEROFUEGO=%d;", $numidenti, $numero_fuego);
			$n = $db->query($sql);

			if($n->count() > 0){
				$data['type'] = 'error';
				$errores = '';
				foreach($n->results() as $nx){
					if($nx->NUMIDENTI == $numidenti){
						if(!isset($errores['NUMIDENTI'])) $errores['NUMIDENTI'] = 'NÚM IDENTIFICADOR';
					}
					if($nx->NUMEROFUEGO == $numero_fuego){
						if(!isset($errores['NUMFUEGO'])) $errores['NUMFUEGO'] = 'NÚM. DE FUEGO';
					}
				}

				$mensaje = '<li>No es posible continuar debido a que ';
				if(isset($errores['NUMIDENTI']) && isset($errores['NUMFUEGO'])) 
					$mensaje .= $errores['NUMIDENTI'].' y '.$errores['NUMFUEGO'].' ya se encuentran asignados a otro(s) neumático(s).';
				else{
					if(!isset($errores['NUMIDENTI'])) $mensaje .= $errores['NUMIDENTI'];
					if(!isset($errores['NUMFUEGO'])) $mensaje .= $errores['NUMFUEGO'];
					$mensaje .= ' ya se encuentra asignado a otro neumático.';
				}
				$mensaje .='</li></ul>';
			}
			else{
				$sql = "INSERT INTO uman_neumaticos  
					VALUES ('','%s','0','DISPONIBLE','%s','%s','%s','%s','%s','0');";
			   	$sql = sprintf($sql, $numidenti, $marca, $modelo, $dimension, $compuesto, $numero_fuego);
			   
			   	$db = $db->query($sql);
   
			   	if(!$db->error()){
				   $data['type'] = 'success';

				   if($neumsegun == 'fuego') $wich = $numero_fuego;
				   else $wich = $numidenti;
				   
				   $mensaje = "<li>El neumático $wich se ha ingresado correctamente.</li></ul>";

				   (new Historial())->creacion_neumatico($db->getPDO()->lastInsertId());
				   Core::actualizar_umanblue();
			   	}
			   	else{
				   $data['type'] = 'error';
				   $mensaje = "<li>{$db->errorInfo}</li></ul>";
			   	}
			}
		}
		else{
			$data['type'] = 'error';			
		}
		$data['text'] = '<ul>'.$mensaje.'</ul>';
	}
	else if($modo == 'obtener'){
		$id_neumatico = isset($_POST['id_neumatico']) ? $_POST['id_neumatico'] : null;

		if($id_neumatico != null && is_numeric($id_neumatico)){
			$n = new Neumatico();
			$n = $n->get_full($id_neumatico);

			header("Content-type: application/json");
			echo json_encode($n);
			exit();
		}
	}
	else if($modo == 'editar'){
		$numidenti 		= isset($_POST['t1']) ? $_POST['t1'] : null;
		$marca 			= isset($_POST['marca']) ? $_POST['marca'] : null;
		$modelo 		= isset($_POST['modelo']) ? $_POST['modelo'] : null;
		$compuesto 		= isset($_POST['compuesto']) ? $_POST['compuesto'] : null;
		$dimension 		= isset($_POST['dimension']) ? $_POST['dimension'] : null;
		$numero_fuego	= isset($_POST['numero_fuego']) ? $_POST['numero_fuego'] : null;
		$id             = isset($_POST['id']) ? $_POST['id'] : null;
		$mensaje = '';

		if($numidenti == NULL && $numero_fuego == NULL) $mensaje .= '<li>Debe especificar un ID y/o un Número de Fuego</li>';
		
		if($mensaje == ''){
			if($id != NULL){
				$db = DB::getInstance();
				$sql = sprintf("UPDATE uman_neumaticos 
					SET NUMIDENTI='%s', MARCA='%s', MODELO='%s', DIMENSION='%s', COMPUESTO='%s', NUMEROFUEGO='%s' 
					WHERE ID_NEUMATICO=%d", $numidenti, $marca, $modelo, $dimension, $compuesto, $numero_fuego, $id);
				$db = $db->query($sql);

				$data['type'] = 'success';
				if($neumsegun == 'fuego') $wich = $numero_fuego;
				else $wich = $numidenti;

				$mensaje = "<li>El neumático $wich se ha modificado correctamente.</li></ul>";
				(new Historial())->modificacion_neumatico($id);
				Core::actualizar_umanblue();
			}
			else{
				$data['type'] = 'error';
				$mensaje = "<li>No ha especificado un ID de neumático válido.</li></ul>";
			}
		}
		else{
			$data['type']	 = 'error';
		}
		$data['text'] = $mensaje;
	}
	else if($modo == 'eliminar'){
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;
		$mensaje = '';
		if($id != NULL){
			$dx = $db->query("SELECT * FROM uman_neumaticos WHERE ID_NEUMATICO='{$id}';");
			$dx = $dx->results()[0];
			
			if($neumsegun == 'fuego') $wich = $dx->NUMEROFUEGO;
			else $wich = $dx->NUMIDENTI;
			// 17_01_2018 CT - Se modifica funcion para eliminar
			$res = $db->query("DELETE FROM uman_neumaticos WHERE ID_NEUMATICO='{$id}'");
			//$data['type'] = 'success';
            if($res) {
                $data = array(
                    'title' => 'Ingreso de Sensores',
                    'text' => 'El neumático $wich se ha eliminado.',
                    'type' => 'success',
                    'html' => false
                );
            }
			$mensaje = "<li>El neumático $wich se ha eliminado.</li></ul>";
			(new Historial())->eliminacion_neumatico($id);
			Core::actualizar_umanblue();
		}
		else{
			$data['type'] = 'error';
			$mensaje = "<li>No ha especificado un ID de neumático válido.</li></ul>";
		}
		$data['text'] = $mensaje;
	}
	/*else{
		$data['type'] = 'error';
		$data['text'] = 'Al parecer ha enviado una consulta vacía o los datos enviados no están permitidos, por favor intente nuevamente.';
	}*/

	header("Content-Type: application/json");
	echo json_encode($data);