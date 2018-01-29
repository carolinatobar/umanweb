<?php

	session_start();
	$sess_id = session_id();

	if ( isset ( $_GET['idioma'] ) && isset ( $_SESSION[$sess_id]['user'] ) && isset ( $_SESSION[$sess_id]['pass'] ) ) {

		include("../conectar.php");

		$idiomanuevo	= $_GET['idioma'];
		$usuario 		= $_SESSION[$sess_id]['user'];

	  	$datos 			= mysql_query("SELECT * FROM uman_usuarios WHERE USUARIO='$usuario'");
		$infol 			= mysql_fetch_array($datos);

		$perfil 			= $infol['PERFIL'];
		$id_usuario 	= $infol['ID_USUARIO'];
		    
		if ( $_SESSION[$sess_id]['pass'] != $infol['PASS'] ) {
			header("Location: ../login.php");
		}
		$_SESSION[$sess_id]['lang'] = $idiomanuevo;
		header("Location: ./");

	} else {
		print "ERROR<br>";
		exit(0);
	}

?>