<?php
	/*
	 * Muestra errores utilizando toda la pantalla, al obligar al explorador a ser redireccionado al 
	 * directorio 'sitio/errors/', el cual recibe el tipode error y crea la plantilla para mostrar
	 * adecuadamente la información del error
	 */
	require '../autoload.php';
	include 'error_codes.php';

	session_start();

	$error_data = isset($_SESSION[session_id()]['ERROR']) ? $_SESSION[session_id()]['ERROR'] : NULL;
	$error_code = isset($_REQUEST['error']) ? $_REQUEST['error'] : NULL;

	$__type__    = 'danger'; // danger, primary, default, info, warning
	$__title__   = '';
	$__content__ = '';
	$__footer__  = '';

	if($error_code != NULL){
		if($_ERROR->offsetExists("e_{$error_code}")){
			$e = $_ERROR["e_{$error_code}"];
			$__type__    = $e->type;
			$__title__   = $e->title;
			$__content__ = $e->text;
			$__footer__  = $e->footer;
		}
		else{
			$__type__    = 'warning';
			$__title__   = 'Lo sentimos';
			$__content__ = 'No se hemos podido identificar el tipo de error, por lo que no podemos entregar mayor información.';
			$__footer__  = '
			<div class="btn-group">
				<a role="button" href="../../" target="_self" class="btn btn-default">Ir al login</a>
				<a role="button" href="../" target="_self" class="btn btn-default">Volver al sitio</a>
			</div>
			';
		}
	}
	else if($error_data != NULL){
		$__type__    = isset($error_data['type'])    ? $error_data['type']    : 'danger';
		$__title__   = isset($error_data['title'])   ? $error_data['title']   : 'Error';
		$__content__ = isset($error_data['content']) ? $error_data['content'] : '';
		$__footer__  = isset($error_data['footer'])  ? $error_data['footer']  : '';
	}
	else{
		$__type__    = 'warning';
		$__title__   = 'Lo sentimos';
		$__content__ = 'No se hemos podido identificar el tipo de error, por lo que no podemos entregar mayor información.';
		$__footer__  = '
		<div class="btn-group">
			<a role="button" href="../../" target="_self" class="btn btn-default">Ir al login</a>
			<a role="button" href="../" target="_self" class="btn btn-default">Volver al sitio</a>
		</div>
		';
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>UmanWeb</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
		<link rel="stylesheet prefetch" href="http://fonts.googleapis.com/css?family=Open+Sans" />
		<link rel="stylesheet" href="../assets/css/login.css" />
		<script type="text/javascript" src="../assets/jqwidgets/scripts/jquery-1.11.1.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css">
		<script src="../assets/sweetalert/sweetalert.min.js"></script>
		<link rel="stylesheet" type="text/css" href="../assets/sweetalert/sweetalert.css" />
	</head>
	<body style="background-image:url(../assets/img/bg_login.jpg); background-size:cover;">
		
		<div class="container">
			<br/>
			<div class="panel panel-<?=$__type__?> clearfix" style="width:50%; margin:5% 25%;">
				<div class="panel-heading"><h3 class="bg-<?=$__type__?>"><center><?=$__title__?></center></h3></div>
				<div class="panel-body text-center">
					<?=$__content__?>
					<p>&nbsp;</p>
				</div>
				<div class="panel-footer text-center">
					<?=$__footer__?>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		// EVITAR goBack() EN BROWSER
		(function (global) {
			if(typeof (global) === "undefined"){ throw new Error("window is undefined"); }
				var _hash = "!";
				var noBackPlease = function () {
					global.location.href += "#";
						// making sure we have the fruit available for juice....
						// 50 milliseconds for just once do not cost much (^__^)
					global.setTimeout(function () { global.location.href += "!"; }, 50);
				};
				
				// Earlier we had setInerval here....
				global.onhashchange = function () {
					if (global.location.hash !== _hash) {
						global.location.hash = _hash;
					}
				};
				global.onload = function () {
					noBackPlease();
					// disables backspace on page except on input fields and textarea..
					document.body.onkeydown = function (e) {
					var elm = e.target.nodeName.toLowerCase();
					if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
						e.preventDefault();
					}
					// stopping event bubbling up the DOM tree..
					e.stopPropagation();
				};
			};
		})(window);
	</script>
</body>
</html>