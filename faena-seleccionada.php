<?php
	require 'sitio/autoload.php';

	$faena         = isset($_REQUEST['faena']) ? $_REQUEST['faena'] : null;
	$nombrefaena   = null;
	$nombreempresa = null;

	@session_start();
	$sess_id = session_id();
	$db = DB2::getInstance();

	if($faena != null){
		$fff = explode('/', $faena);
		// print_r($fff);

		if(count($fff)>1){
			$faena 		     = isset($fff[0]) ? $fff[0] : null;
			$nombrefaena   = isset($fff[1]) ? $fff[1] : null;
			$nombreempresa = isset($fff[2]) ? $fff[2] : null;
		}
		else{
			$dato = $db->query(sprintf("SELECT * FROM uman_faenas WHERE nombre_db='%s'", $faena));
			// print_r($dato->results());
			if($dato->count() > 0){
				$dato = $dato->results()[0];
				$nombrefaena   = $dato->nombre_faena;
				$nombreempresa = $dato->nombre_empresa;
			}
			else{
				$faena         = null;
				$nombrefaena   = null;
				$nombreempresa = null;
			}
		}
	}

	// print_r(array('faena'=>$faena, 'nombre'=>$nombrefaena, 'empresa'=>$nombreempresa));exit();

	if($faena != null && $nombrefaena != null){
		$usuario	= $_SESSION[$sess_id]['user'];
		$password	= $_SESSION[$sess_id]['pass'];
		// print_r($_SESSION);exit();

		$usr = $db->query(sprintf("SELECT * FROM uman_usuarios WHERE USUARIO='%s'", $usuario));
		$usr = $usr->count() > 0 ? $usr->results()[0] : null;
		// var_dump($usr);exit();
		if($usr != null){
			// echo "$password === $usr->PASS   ".($password === $usr->PASS);exit();
			if( $password === $usr->PASS ) {
				$_SESSION[$sess_id]['faena']		  	= $faena;
				$_SESSION[$sess_id]['nombrefaena'] 	= $nombrefaena;
				$_SESSION[$sess_id]['empresa']      = $nombreempresa;

				if(isset($_GET['withref'])) $url = $_SERVER['HTTP_REFERER'];
				else $url = 'sitio/?s='.$_SESSION[$sess_id]['predefinido'];
				// echo 'ok';exit();
				header("Location: $url");
			}
			else {
				$_SESSION = null;
				session_destroy();
				header("Location: index.php?ERROR=".md5('0'));
			}
		}
		else{
			$_SESSION = null;
			session_destroy();
			header("Location: index.php?ERROR=".md5('0'));
		}
	}
	else{
		$_SESSION = null;
		session_destroy();
		header("Location: index.php?ERROR=".md5('2'));
	}