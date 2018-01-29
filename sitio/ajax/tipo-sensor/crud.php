<?php
	require '../../autoload.php';

	$accion = isset($_POST['ac']) ? $_POST['ac'] : NULL;

	$data = array('type'=>'error', 'html'=>true, 'title'=>'Tipos de Sensores', 'text'=>'', 'data'=>array());

	if($accion == 'crear'){

		$error       = '';

		$tipo        = isset($_POST['tipo'])        ? $_POST['tipo']        : NULL;
		$nombre      = isset($_POST['nombre'])      ? $_POST['nombre']      : NULL;
		$medicion    = isset($_POST['medicion'])    ? $_POST['medicion']    : NULL;
		$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : NULL;
		$imagen      = isset($_FILES['imagen'])     ? $_FILES['imagen']     : NULL;

		if($tipo == NULL || $tipo == '')
			$error = '<li>Debe ingresar la nomenclatura que servirá para identificar al sensor. Este valor debe ser único.</li>';
		if($nombre == NULL || $nombre == '')
			$error = '<li>Debe ingresar el nombre de sensor, este debe ser único e irrepetible.</li>';
		if($medicion == NULL || count($medicion) == 0)
			$error = '<li>Debe seleccionar al menos un tipo de medición que el sensor realizará.</li>';
		if($descripcion == NULL || $descripcion == '')
			$error = '<li>Debe ingresar una breve descripción del sensor, esto ayudará a comprender su propósito.</li>';
		if($imagen == NULL)
			$error = '<li>Debe seleccionar la imagen que se mostrará en toda la aplicación, esta imagen debe ser representativa del sensor.</li>';
		if(stripos($imagen['type'], 'image/') === FALSE)
			$error = '<li>Debe seleccionar una imagen válida.</li>';
		if($imagen['size'] > 1048576)
			$error = '<li>La imagen no debe superar 1Mb.</li>';

		if($error == ''){
			$dt = file_get_contents($imagen['tmp_name']);
			$base64 = 'data:'.$imagen['type'].';base64,'.base64_encode($dt);

			$mide_temperatura = 0;
			$mide_presion     = 0;
			$mide_humedad     = 0;
			$mide_combustible = 0;
			$mide_gas         = 0;

			foreach($medicion as $med){
				if($med ==  'temperatura') $mide_temperatura = 1;
				if($med ==  'presion')     $mide_presion = 1;
				if($med ==  'humedad')     $mide_humedad = 1;
				if($med ==  'combustible') $mide_combustible = 1;
				if($med ==  'gas')         $mide_gas = 1;
			}

			$db = DB::getInstance();

			$sql = sprintf("SELECT * FROM uman_tiposensor WHERE tipo='%s' AND nombre='%s';", 
				strtolower($tipo), $nombre);
			$res = $db->query($sql);

			if($res->count() == 0){
				$sql = sprintf("INSERT INTO uman_tiposensor
					VALUES(NULL, '%s', %d, %d, %d, %d, %d, '%s', '%s', '%s');",
					$tipo, $mide_temperatura, $mide_presion, $mide_humedad, $mide_combustible, $mide_gas, $nombre, $descripcion, $base64
				);

				$res = $db->query($sql);

				if($res){
					$data['text'] = 'El nuevo tipo de sensor se ha agregado con éxito.';
					$data['type'] = 'success';
				}
				else{
					$data['text'] = $db->getPDO()->errorInfo[2];
				}
			}
			else{
				$data['text'] = '<li>Ya existe un sensor con ese nombre o con la misma nomenclatura, por favor revise la tabla.</li>';
			}
		}
		else{
			$data['text'] = $error;
		}
	}
	else if($accion == 'modificar'){
		$error       = '';

		$tipo        = isset($_POST['tipo'])        ? $_POST['tipo']        : NULL;
		$nombre      = isset($_POST['nombre'])      ? $_POST['nombre']      : NULL;
		$medicion    = isset($_POST['medicion'])    ? $_POST['medicion']    : NULL;
		$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : NULL;
		$imagen      = isset($_FILES['imagen'])     ? $_FILES['imagen']     : NULL;
		$id          = isset($_POST['id'])          ? $_POST['id']          : NULL;

		if(!is_numeric($id)) 
			$error .= '<li>El ID enviado no corresponde a un dato válido, por favor asegúrese de haber seleccionado el objeto desde la tabla.</li>';
		// if($tipo == NULL || $tipo == '')
		// 	$error = '<li>Debe ingresar la nomenclatura que servirá para identificar al sensor. Este valor debe ser único.</li>';
		// if($nombre == NULL || $nombre == '')
		// 	$error = '<li>Debe ingresar el nombre de sensor, este debe ser único e irrepetible.</li>';
		// if($medicion == NULL || count($medicion) == 0)
		// 	$error = '<li>Debe seleccionar al menos un tipo de medición que el sensor realizará.</li>';
		// if($descripcion == NULL || $descripcion == '')
		// 	$error = '<li>Debe ingresar una breve descripción del sensor, esto ayudará a comprender su propósito.</li>';
		// if($imagen == NULL)
		// 	$error = '<li>Debe seleccionar la imagen que se mostrará en toda la aplicación, esta imagen debe ser representativa del sensor.</li>';
		if($imagen != NULL){
			if(stripos($imagen['type'], 'image/') === FALSE)
				$error = '<li>Debe seleccionar una imagen válida.</li>';
			if($imagen['size'] > 1048576)
				$error = '<li>La imagen no debe superar 1Mb.</li>';
		}

		if($error == ''){
			$base64 = '';
			if($imagen != NULL){
				$dt = file_get_contents($imagen['tmp_name']);
				$base64 = 'data:'.$imagen['type'].';base64,'.base64_encode($dt);
			}

			$mide_temperatura = 0;
			$mide_presion     = 0;
			$mide_humedad     = 0;
			$mide_combustible = 0;
			$mide_gas         = 0;

			foreach($medicion as $med){
				if($med ==  'temperatura') $mide_temperatura = 1;
				if($med ==  'presion')     $mide_presion = 1;
				if($med ==  'humedad')     $mide_humedad = 1;
				if($med ==  'combustible') $mide_combustible = 1;
				if($med ==  'gas')         $mide_gas = 1;
			}

			$db = DB::getInstance();

			$sql = sprintf("SELECT * FROM uman_tiposensor WHERE tipo='%s' AND nombre='%s' AND id!=%d;", 
				strtolower($tipo), $nombre, $id);
			$res = $db->query($sql);

			if($res->count() == 0){

				$sql = sprintf("SELECT * FROM uman_tiposensor WHERE id=%d;", $id);
				$res = $db->query($sql);

				if($res->count() == 0){
					$data['text'] = 'Al parecer el ID proporcionado no existe en la base de datos.';
				}
				else{
					$res = $res->results()[0];
					if($medicion == NULL || count($medicion) == 0){
						$mide_temperatura = $res->mide_temperatura;
						$mide_presion     = $res->mide_presion;
						$mide_humedad     = $res->mide_humedad;
						$mide_combustible = $res->mide_combustible;
						$mide_gas         = $res->mide_gas;
					}
					if($base64 == '') $base64 = $res->imagen;
					if($nombre == NULL || $nombre == '') $nombre = $res->nombre;
					if($tipo == NULL || $tipo == '') $tipo = $res->tipo;
					if($descripcion == NULL || $descripcion == '') $descripcion = $res->descripcion;

					$sql = sprintf("UPDATE uman_tiposensor
						SET tipo='%s', mide_temperatura=%d, mide_presion=%d, mide_humedad=%d, mide_combustible=%d, mide_gas=%d, 
						nombre='%s', descripcion='%s', imagen='%s' 
						WHERE id=%d;",
						$tipo, $mide_temperatura, $mide_presion, $mide_humedad, $mide_combustible, $mide_gas, $nombre, 
						$descripcion, $base64, $id);

					$res = $db->query($sql);

					if($res){
						$data['text'] = 'El tipo de sensor se ha modificado con éxito.';
						$data['type'] = 'success';
					}
					else{
						$data['text'] = $db->getPDO()->errorInfo[2];
					}
				}				
			}
			else{
				$data['text'] = '<li>Ya existe un sensor con ese nombre o con la misma nomenclatura, por favor revise la tabla.</li>';
			}
		}
		else{
			$data['text'] = $error;
		}
	}
	else if($accion == 'eliminar'){
		$error = '';
		$id    = isset($_POST['id']) ? $_POST['id'] : NULL;

		if(!is_numeric($id)) $error .= '<li>El ID enviado no corresponde a un dato válido, por favor asegúrese de haber seleccionado el objeto desde la tabla.</li>';

		if($error == ''){
			$db = DB::getInstance();
			$sql = sprintf("DELETE FROM uman_tiposensor WHERE id=%d;",$id);
			$res = $db->query($sql);

			if($res){
				$data['text'] = 'El registro se ha eliminado con éxito.';
				$data['type'] = 'success';
			}
			else{
				$data['text'] = $db->getPDO()->errorInfo[2];
			}
		}
		else{
			$data['text'] = $error;	
		}
	}
	else if($accion == 'obtener'){
		$error = '';
		$id    = isset($_POST['id']) ? $_POST['id'] : NULL;

		if(!is_numeric($id)) 
			$error .= '<li>El ID enviado no corresponde a un dato válido, por favor asegúrese de haber seleccionado el objeto desde la tabla.</li>';

		if($error == ''){
			$db  = DB::getInstance();
			$sql = sprintf("SELECT * FROM uman_tiposensor WHERE id=%d;", $id);
			$res = $db->query($sql);

			if($res->count() == 1){
				$data['data'] = $res->results()[0];
				$data['type'] = 'success';
			}
			else{
				$data['text'] = $db->getPDO()->errorInfo[2];
			}
		}
		else{
			$data['text'] = $error;
		}
	}

	header("Content-type: application/json");
	echo json_encode($data);
?>