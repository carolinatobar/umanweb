<?php
	require 'autoload.php';

	// session_start();
	$acc = new Acceso(true);

	if(isset($_GET['perfil']) && isset($_SESSION[session_id()]['user']) && isset($_SESSION[session_id()]['pass'])){
		$perfilnuevo	= $_GET['perfil'];
		$usuario 		  = $_SESSION[session_id()]['user'];

		$perfiles = (new Usuario())->obtenerPerfiles($_SESSION[session_id()]['id']);

		$cambiar  = FALSE;

		foreach($perfiles as $p){
			if($p->id == $perfilnuevo){
				$cambiar = TRUE;
				$_SESSION[session_id()]['perfilactivo'] = $p;
				$_SESSION[session_id()]['predefinido']  = $p->modulo_predefinido;
			}
		}

		if($cambiar === TRUE){			
			header("Location: ./?s=".$_SESSION[session_id()]['predefinido']);
		}
		else{
			Error::lanzar2('<center>Ud. no posee privilegios suficientes para cambiar de perfil.</center>');
		}
	} 
	else { header("Location: ../index.php?ERROR=".md5(0)); }

?>
