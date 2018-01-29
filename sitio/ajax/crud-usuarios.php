<?php 
	require '../autoload.php';
	// error_reporting(E_ALL);

	$acc = new Acceso(true);

	$modo = isset($_POST['modo']) ? $_POST['modo'] : '';

	// var_dump($modo);

	$db = DB2::getInstance();

	if($modo == "nuevo-usuario" ) {
		$faena      = $_POST['faena'];
		$nombre 	  = $_POST['nombre'];
		$email 		  = $_POST['email'];
		$usuario 	  = $_POST['usuario'];
		$perfil     = $_POST['perfil'];
		$estado     = $_POST['estado'];
		$password 	= $_POST['password'];
		$password_2 = $_POST['password_2'];

		$db = DB2::getInstance();

		$nfaena = $faena;
		if(is_numeric($faena)){
			$nfaena = $db->query("SELECT * FROM uman_faenas WHERE id=$faena");
			$nfaena = $nfaena->count() > 0 ? $nfaena->results()[0]->nombre_db : $faena;
		}


		$pass = ($password==$password_2 && $password!='') ? crypt($password) : NULL;

		$errors = NULL;

		//Verificar nombre de usuario
		$sql = sprintf("SELECT * FROM uman_usuarios WHERE USUARIO='%s';", $usuario);
		$res = $db->query($sql);

		if($res->count() > 0) $errors .= '<li>El nombre de usuario ingresado ya existe, por favor cree uno nuevo o bien edite el usuario existente.</li>';
		if($faena == NULL)    $errors .= '<li>Debe seleccionar una faena.</li>';
		if($nombre == NULL)   $errors .= '<li>Debe ingresar el nombre del usuario.</li>';
		if($usuario == NULL)  $errors .= '<li>Debe ingresar un nombre de usuario para que pueda acceder al sitio.</li>';
		if($perfil == NULL)   $errors .= '<li>Debe asignarle un perfil al usuario.</li>';
		if($pass == NULL)     $errors .= '<li>Debe ingresar una contraseña, no sse permiten contraseñas en blanco.</li>';

		if($errors==NULL){
			$sql = sprintf("INSERT INTO uman_usuarios 
				VALUES (NULL,'%s','%s','%s','%s','',%d,'','es');", 
			 $usuario,$nombre,$pass,$email,$estado);
			$res = $db->query($sql);
			$id_usuario = $db->getPDO()->lastInsertId();

			/*
			 * Asignación de perfiles en caso de tener más de uno
			 */
				$sql = '';
				if(is_array($perfil)){
					foreach($perfil as $p){
						$sql .= sprintf("INSERT INTO uman_perfil_usuario VALUES (NULL, %d, %d);\n", $p, $id_usuario);
					}
				}
				else{
					$sql = sprintf("INSERT INTO uman_perfil_usuario VALUES (NULL, %d, %d);\n", $perfil, $id_usuario);
				}

				$res2 = FALSE;
				if($sql != '') $res2 = $db->query($sql);

			/*
			 * Asgnación de faenas en caso de tener más de una
			 */
				$sql = '';
				if(is_array($faena)){
					foreach($faena as $p){
						$sql .= sprintf("INSERT INTO uman_faena_usuario VALUES (NULL, %d, %d);\n", $id_usuario, $p);
					}
				}
				else{
					$sql = sprintf("INSERT INTO uman_faena_usuario VALUES(NULL, %d, %d);", $id_usuario, $faena);
				}

				$res3 = FALSE;
				if($sql != '') $res3 = $db->query($sql);
	
			if($res){
				$text = '';
				if($res2 !== FALSE && $res3 !== FALSE) $text = 'El usuario ha sido creado exitosamente.';
				else{
					$text = 'El usuario se ha creado, pero se han detectado un problema: <br/>';
					if($res2 === FALSE) $text .= 'El o los perfiles seleccionados no se han asignado correctamente.<br/>';
					if($res3 === FALSE) $text .= 'La o las faenas seleccionadas no se han asignado correctamente.<br/>';
				}
				$data = array(
					'type'=>'success',
					'title'=>'Usuario creado',
					'text'=>$text,
					'html'=>true,
				);
			} else {
				$data = array(
					'type'=>'error',
					'title'=>'Ha ocurrido un error',
					'text'=>$db->getPDO()->errorInfo[2]
				);
			}
		}
		else{
			$data = array(
				'type'=>'error',
				'title'=>'Error de validación de datos',
				'html'=>true,
				'text'=>'<h3>No se ha podido continuar debido a los siguientes errores:</h3> <br/><ul>'.$errors.'</ul>'
			);
		}

		header("Content-Type: application/json");
		echo json_encode($data);
	}

	//Desde módulo de administración de usuarios
	if($modo == 'editar-usuario' ) {

		// print_r($_POST); exit();

		$id 		= $_POST['id'];
		$faena      = $_POST['faena'];
		$nombre 	= $_POST['nombre'];
		$email 		= $_POST['email'];
		$usuario 	= $_POST['usuario'];
		$perfil     = $_POST['perfil'];
		$estado     = $_POST['estado'];

		$password 	= $_POST['password'];
		$password_2 = $_POST['password_2'];

		$nfaena = $faena;
		if(is_numeric($faena)){
			$nfaena = $db->query("SELECT * FROM uman_faenas WHERE id=$faena");
			$nfaena = $nfaena->count() > 0 ? $nfaena->results()[0]->nombre_db : $faena;
		}

		$pass = ($password==$password_2 && $password!='') ? crypt($password) : NULL;

		$errors = NULL;
		
		if($faena==NULL) $errors .= '<li>Debe seleccionar una faena.</li>';
		if($nombre==NULL) $errors .= '<li>Debe ingresar el nombre del usuario.</li>';
		if($usuario==NULL) $errors .= '<li>Debe ingresar un nombre de usuario para que pueda acceder al sitio.</li>';
		if($perfil==NULL) $errors .= '<li>Debe asignarle un perfil al usuario.</li>';
		// if($pass==NULL) $errors .= '<li>Debe ingresar una contraseña, no sse permiten contraseñas en blanco.</li>';		
		
		if($errors==NULL){			
			$sql = sprintf("UPDATE uman_usuarios SET 
				USUARIO='%s', 
				NOMBRE='%s', 
				".($pass!=NULL ? "PASS='$pass'," : '')."
				CORREO='%s', 
				ACTIVO=%d 
				WHERE ID_USUARIO='%d'", $usuario, $nombre, $email, $estado, $id);
			$res = $db->query($sql);

			$sql = sprintf("DELETE FROM uman_perfil_usuario WHERE id_usuario=%d;",$id);
			$db->query($sql);
			$sql = sprintf("DELETE FROM uman_faena_usuario WHERE id_usuario=%d;",$id);
			$db->query($sql);

			/*
			 * Asignación de perfiles en caso de tener más de uno
			 */
				$sql = '';
				if(is_array($perfil)){
					foreach($perfil as $p){
						$sql .= sprintf("INSERT INTO uman_perfil_usuario VALUES (NULL, %d, %d);\n", $p, $id);
					}
				}
				else{
					$sql = sprintf("INSERT INTO uman_perfil_usuario VALUES (NULL, %d, %d);\n", $perfil, $id);
				}

				$res2 = FALSE;
				if($sql != '') $res2 = $db->query($sql);

			/*
			 * Asgnación de faenas en caso de tener más de una
			 */
				$sql = '';
				if(is_array($faena)){
					foreach($faena as $p){
						$sql .= sprintf("INSERT INTO uman_faena_usuario VALUES (NULL, %d, %d);\n", $id, $p);
					}
				}
				else{
					$sql = sprintf("INSERT INTO uman_faena_usuario VALUES(NULL, %d, %d);", $id, $faena);
				}

				$res3 = FALSE;
				if($sql != '') $res3 = $db->query($sql);
	
			if($res){
				$text = '';
				if($res2 !== FALSE && $res3 !== FALSE) $text = 'El usuario ha sido modificado exitosamente.';
				else{
					$text = 'El usuario se ha modificado, pero se han detectado problemas: <br/>';
					if($res2 === FALSE) $text .= 'El o los perfiles seleccionados no se han asignado correctamente.<br/>';
					if($res3 === FALSE) $text .= 'La o las faenas seleccionadas no se han asignado correctamente.<br/>';
				}
				$data = array(
					'type'=>'success',
					'title'=>'Usuario modificado',
					'text'=>$text,
					'html'=>true,
				);
			} else {
				$data = array(
					'type'=>'error',
					'title'=>'Ha ocurrido un error',
					'text'=>$db->getPDO()->errorInfo[2]
				);
			}
		}
		else{
			$data = array(
				'type'=>'error',
				'title'=>'Error de validación de datos',
				'html'=>true,
				'text'=>'<h3>No se ha podido continuar debido a los siguientes errores:</h3> <br/><ul>'.$errors.'</ul>'
			);
		}

		header("Content-Type: application/json");
		echo json_encode($data);
	}

	//Desde perfil personal
	if($modo == 'actualizar-usuario'){
		// $usuario  = isset($_SESSION['user']) ? $_SESSION['user'] : null;
		$usuario  = isset($_POST['ui']) ? $_POST['ui'] : null;
		$email    = isset($_POST['email']) ? $_POST['email'] : null;
		$pass     = isset($_POST['pass']) ? $_POST['pass'] : null;
		$passw1   = isset($_POST['pass1']) ? $_POST['pass1'] : null;
		$passw2   = isset($_POST['pass2']) ? $_POST['pass2'] : null;
		$idioma   = isset($_POST['idioma']) ? $_POST['idioma'] : null;

		$set_column = '';
		$relogin = false;
		if($passw1==$passw2){
			if($passw1!='' && $passw1!=null){
				$set_column .= sprintf(" PASS='%s',",crypt($passw1,$passw2));
				$relogin = true;
			}
			else{
				//Error
			}
		}
		else{
			//Error
		}

		if($email!=null){
			$set_column .= sprintf(" CORREO='%s',", $email);
		}
		
		// if(in_array($idioma, array('es','en','po','de')){
		// 	$set_column .= sprintf("IDIOMA='%s',", $idioma);
		// }

		$key = $_SESSION[session_id()]['pass'];

		// var_dump([$_SESSION[session_id()]['pass'], crypt($pass, $key)]);

		if(crypt($pass, $key) != $_SESSION[session_id()]['pass']){
			$data = array(
				'title'=>'Error',
				'text'=>'Su contraseña actual no coincide con la ingresada, por favor asegúrese de ingresar correctamente los datos requeridos',
				'type'=>'error',
			);
		}
		else{
	
			$set_column = substr($set_column,0,strlen($set_column)-1);
			
			$db = DB2::getInstance();
			$sql = "UPDATE uman_usuarios SET {$set_column} WHERE ID_USUARIO={$usuario};";
			// echo $sql;
			$res = $db->query($sql);
			$data = array();
	
			if($res){
				$data = array(
					'title'=>'Datos actualizados',
					'text'=>'Sus datos se han actualizado correctamente.',
					'type'=>'success',
					'relogin'=>$relogin
				);
			}
			else{
				$data = array(
					'title'=>'Error',
					'text'=>'Ha ocurrido un error al intentar actualizar sus datos: <br/>'.$db->errorInfo()[2],
					'html'=>true,
					'type'=>'error',
				);
			}
		}

		header("Content-type: application/json");
		echo json_encode($data);
	}

	if($modo == 'obtener-usuario') {
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;

		if(is_numeric($id)){
			$db = DB2::getInstance();
			$sql = sprintf("SELECT * FROM uman_usuarios WHERE ID_USUARIO=%d;", $id);
			$data = $db->query($sql);

			if($data->count() == 1){
				$data = $data->results()[0];

				$perfil = array();
				$faena  = array();

				$perfiles = (new Usuario())->obtenerPerfiles($id);
				foreach($perfiles as $p) $perfil[] = $p->id;
				
				$faenas   = (new Usuario())->obtenerFaenas($id);
				foreach($faenas as $f) $faena[] = $f->id;

				$sql = "";
				$data = array(
					'data'=>array(
						'nombre'=>utf8_encode($data->NOMBRE),
						'usuario'=>$data->USUARIO,
						'email'=>$data->CORREO,
						'perfil'=>$perfil,
						'activo'=>$data->ACTIVO,
						'faena'=>$faena
					),
					'type'=>'success'
				);
			}
			else{
				$data = array(
					'type'=>'info',
					'title'=>'Usuario no encontrado',
					'text'=>'Al parecer el usuario solicitado no ha sido encontrado, por favor actualice la página y si el problema persiste, contácte al soporte técnico.'
				);
			}
		}
		else{
			$data = array(
				'type'=>'error',
				'title'=>'Tipo de dato no soportado',
				'text'=>'Al parecer está enviando datos que no se encuentran soportados para esta consulta.'
			);
		}

		header("Content-Type: application/json");
		echo json_encode($data);
	}

	//Desde módulo de administración de usuarios
	if($modo == 'eliminar-usuario'){
		$id = isset($_POST['id']) ? $_POST['id'] : NULL;

		if($id!=NULL && is_numeric($id)){
			$db = DB2::getInstance();
			$sql = sprintf("DELETE FROM uman_usuarios WHERE ID_USUARIO=%d;",$id);
			$res = $db->query($sql);

			if($res){

				$sql = sprintf("DELETE FROM uman_perfil_usuario WHERE id_usuario=%d;",$id);
				$db->query($sql);
				$sql = sprintf("DELETE FROM uman_faena_usuario WHERE id_usuario=%d;",$id);
				$db->query($sql);
				$data = array(
					'type'=>'success',
					'title'=>'Usuario eliminado',
					'text'=>'El usuario ha sido eliminado de la base de datos.'
				);
			}
			else{
				$data = array(
					'type'=>'error',
					'title'=>'Ha ocurrido un error',
					'text'=>$db->getPDO()->errorInfo[2]
				);
			}
		}
		else {
			$data = array(
				'type'=>'error',
				'title'=>'Tipo de dato no soportado',
				'text'=>'Al parecer está enviando datos que no se encuentran soportados para esta consulta.'
			);
		}

		header("Content-Type: application/json");
		echo json_encode($data);
	}

	//Desde módulo de administración de usuarios
	if($modo == 'obtener-tabla'){
		// var_dump($_SESSION[session_id()]);
		$db = DB2::getInstance();
		$res = $db->query("SELECT * FROM uman_usuarios")  or die(mysql_error());
		$data = array('data'=>array());
		$esRoot = false;
		foreach($_SESSION[session_id()]['perfiles'] as $perfil){ if($perfil->nombre == 'Root'){ $esRoot = true; break; } }

    foreach($res->results() as $usr){ 
    	//Obtener perfiles del usuario
    	$usrRoot = false;
    	$perfiles = (new Usuario())->obtenerPerfiles($usr->ID_USUARIO);
    	foreach($perfiles as $perfil){ if($perfil->nombre == 'Root'){ $usrRoot = true; break; } }

    	if($usrRoot === true && $esRoot === false){
    		//No debe estar permitido poder crear, editar o eliminar SuperUsuarios para perfiles que no sean SuperUsuarios
    		//Lo ideal no es llenar de SuperUsuarios el sistema, lo óptimo es respetar el perfilado y que cada perfil 
    		//realice las tareas que debe o tiene asignadas según sus permisos.
    	}
    	else{
	    	//Obtener faenas a las que tiene acceso
	    	$faenas = (new Usuario())->obtenerFaenas($usr->ID_USUARIO);
	    	$faenasArray = array();
	    	foreach($faenas as $faena){ $faenasArray[] = utf8_encode($faena->nombre_faena).' '; }

	    	$perfilesArray = array();
	    	foreach($perfiles as $perfil){ $perfilesArray[] = utf8_encode($perfil->nombre).' '; }

	      $estado = $usr->ACTIVO == 1 ? 'Habilitado' : 'Deshabilitado';

				$data["data"][] = array(
					'btn' => '<button onClick="modificar(this);" class="btn btn-primary btn-xs edit" data-usuario="'.$usr->ID_USUARIO.'" data-toggle="tooltip" data-placement="right" title="Modificar Usuario '.utf8_encode($usr->NOMBRE).'"><i class="fa fa-edit" aria-hidden="true"></i></button>'.
					'&nbsp;&nbsp;'.
					'<button onClick="eliminar(this);" class="btn btn-danger btn-xs delete" data-usuario="'.$usr->ID_USUARIO.'" data-toggle="tooltip" data-placement="right" title="Eliminar Usuario '.utf8_encode($usr->NOMBRE).'"><i class="fa fa-remove" aria-hidden="true"></i></button>',
					// 'btn'=>'',
					'usuario' => ($usr->USUARIO),
					'nombre'  => ($usr->NOMBRE),
					'correo'  => ($usr->CORREO),
					'perfil'  => $perfilesArray,
					'faena'   => $faenasArray,
					'estado'  => $estado
				);
			}
		}

		// var_dump($data);

		header("Content-type: application/json");
		echo json_encode($data);
	}

?>