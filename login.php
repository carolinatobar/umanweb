<?php

	require 'sitio/autoload.php';

	$usuario	= $_POST['user'];
	$password	= $_POST['pass'];
	$antes 		= $_POST['antes'];

	$db = DB2::getInstance();
	$sql = sprintf("SELECT u.*, p.id AS 'id_perfil', p.nombre AS 'perfil', p.tiempo_sesion, p.modulo_predefinido  
		FROM uman_usuarios u INNER JOIN uman_perfil_usuario up ON u.ID_USUARIO=up.id_usuario
		INNER JOIN uman_perfil p ON p.id=up.id_perfil 
		WHERE USUARIO='%s' LIMIT 1;", $usuario);

	// echo $sql;exit();

	$datos = $db->query($sql)

		or die(Render::make(array('title'=>'error','content'=>$db->errorInfo)));

	if($datos->count() == 1){
		$datos = $datos->results()[0]; //var_dump($datos);exit();

		if(crypt($password, $datos->PASS) === $datos->PASS){

			$tiempo_sesion = $datos->tiempo_sesion;

			//Si el usuario se encuentra inactivo muestra un mensaje en pantalla indicando su estado y no permite continuar 
			//con la carga del sistema, tampoco permite el acceso a los demás módulos
			if($datos->ACTIVO == 0){
				$r = new Render();
				$r->make('error_fullpage', array(
					'content'=>'<div><center>El usuario se encuentra inactivo.</center> <br/> <small>Para mayor información contáctese con el administrador o con soporte técnico.</small></div><br/>'.
					'<a href="'.$GLOBALS['LOGIN'].'" class="btn btn-info center-block">Volver</a>',
					'title'=>'No es posible acceder al sitio'
				));
			}

			//Obtener listado de perfiles asignados al usuario
			$perfiles = (new Usuario())->obtenerPerfiles($datos->ID_USUARIO);

			//Obtener listado de faenas asignadas al usuario
			$faenas   = (new Usuario())->obtenerFaenas($datos->ID_USUARIO);

			//Destruye todas las sesiones abiertas si es que las hubiera
			// 10_01_2018 CT - Problemas de inicio de sesion
			//@session_destroy();
			//$_SESSION = NULL;
			//unset($_SESSION);
			//Establece el tiempo de vida de la sesión
			//ini_set("session.cookie_lifetime", $tiempo_sesion);
			//ini_set("session.gc_maxlifetime",  $tiempo_sesion);

			session_start();

			$sess_id = session_id();

			$_SESSION[$sess_id]['csrf_token']   = base64_encode(openssl_random_pseudo_bytes(32));

			$_SESSION[$sess_id]['user']         = $usuario;
			$_SESSION[$sess_id]['nombre']       = $datos->NOMBRE;
			$_SESSION[$sess_id]['id']           = $datos->ID_USUARIO;
			$_SESSION[$sess_id]['pass']         = $datos->PASS; //TODO: evaluar si es factible eliminar esta variable
			//Listado de perfiles
			$_SESSION[$sess_id]['perfiles']     = $perfiles;
			$_SESSION[$sess_id]['faenas']       = $faenas;
			$_SESSION[$sess_id]['lang']         = $datos->IDIOMA;
			//Carga el módulo predefinido que se mostrará al desplegar por primera vez la plataforma,
			//este módulo se cargará en caso de que no se encuentre el link recibido en sitio/index.php.
			$_SESSION[$sess_id]['predefinido']  = $datos->modulo_predefinido;
			//perfilactivo será el primer perfil que tenga el modulo_predefinido 'monitoreo', sino, toma el primero de la lista
			$_SESSION[$sess_id]['perfilactivo'] = $perfiles[0];
			foreach($perfiles as $px){
				if($px->modulo_predefinido == 'monitoreo'){
					$_SESSION[$sess_id]['perfilactivo'] = $px;
					$_SESSION[$sess_id]['predefinido']  = $px->modulo_predefinido;
					break;
				}
			}
			//0 es sesión infinita o hasta que se cierre el explorador
			$_SESSION[$sess_id]['expira']       = ($tiempo_sesion == 0) ? 0 : time() + $tiempo_sesion; 

			/*
			 * TODO: trasladar a clase Acceso u otra para registrar movimientos y comportamientos de usuarios
			 */
			$ip                                 = $_SERVER['REMOTE_ADDR'];
			$db->query(sprintf("INSERT INTO uman_historial_login VALUES ('','%s',NOW(),'%s','%s')",$usuario,$ip,$antes));

			if(count($faenas) > 1){
				$ok = true;
				include("seleccionar-faena.php");
			}
			else if(count($faenas) == 1){
				$_SESSION[$sess_id]['faena']       = $faenas[0]->nombre_db;
				$_SESSION[$sess_id]['nombrefaena'] = $faenas[0]->nombre_faena;
				$_SESSION[$sess_id]['empresa']     = $faenas[0]->nombre_empresa;

				header("Location: sitio/?s=".$datos->modulo_predefinido);
			}
			else{ //Sin faena asignada
				header("Location: index.php?ERROR=".md5('3'));
			}
		}
		else header("Location: index.php?ERROR=".md5('0'));
	}
	else{
		header("Location: index.php?ERROR=".md5('1'));
	}
	?>