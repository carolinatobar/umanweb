<?php
	/*
	 * Consulta dentro de la carpeta si la marca tiene imagen, de lo contrario
   * coloca una imagen por defecto.
   */
	error_reporting(E_ALL);
	$marca = isset($_GET['marca']) ? strtolower($_GET['marca']) : 'acme';
	$tam   = isset($_GET['tam'])   ? $_GET['tam'] : 'lg';

	$filename = "{$marca}-{$tam}.png";
	$filesize = 0;

	if(file_exists($filename)){
		$filesize = filesize($filename);
		header("Content-Type: image/png");
		header('Content-Length: ' . $filesize);
		readfile($filename);
	}
	else{
		$filesize = filesize("sin-foto.png");
		header("Content-Type: image/png");
		header('Content-Length: ' . $filesize);
		readfile("sin-foto.png");
	}