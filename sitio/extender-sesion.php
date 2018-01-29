<?php
  require 'autoload.php';

  ini_set("session.cookie_lifetime",$GLOBALS['SESSION_LENGTH']);
  ini_set("session.gc_maxlifetime",$GLOBALS['SESSION_LENGTH']);

  // $acc = new Acceso(true);
  session_start();

  $tiempo_sesion = $_SESSION[session_id()]['perfilactivo']->tiempo_sesion;
  $_SESSION[session_id()]['expira'] = time() + $tiempo_sesion;

  $nuevo_plazo = time() + $tiempo_sesion;
  $fecha_hora = date("d/m/Y H:i:s", $nuevo_plazo);

  header("Content-Type: application/json");  
  echo json_encode(
  	array(
	  	'title'=>'Operación exitosa',
	  	'text'=>'Sesión ha sido extendida hasta '.$fecha_hora,
	  	'type'=>'success')
  	);
?>