<?php
  require 'autoload.php';

  $acc = new Acceso();

  $db = DB::getInstance();
  
  $documento = $_POST['documento'];
  $nombre     = $_POST['nombre']; 
  $descripcion = $_POST['descripcion'];

  $allowedExts = array(
    "pdf", 
    "doc", 
    "docx"
  ); 

  $filers = explode(".", $_FILES["file"]["name"]);
  $extension = end($filers);
  $filex = $filers[0];

  //print $extension;

  if ( ! ( in_array($extension, $allowedExts ) ) ) {
    die('Please provide another file type [E/2].');
  }

  $perfiles = "";

  $nombreperfiles = array("monitoreo","monitoreo-bailac","config-sensores","faena","tecnicos","administrativos");

  foreach ( $nombreperfiles as $perfil ) {
    if ( isset ( $_POST[$perfil] ) ) {
      if ( $perfiles != "" ) {
        $perfiles = $perfiles.",";
      }
      $perfiles = $perfiles.$perfil;
    }
  }
 


  $archivo =  $_FILES["file"]["name"];

  $contador = 1;
  $archivo = $filex.".".$extension;

  while ( file_exists( "uploads/documentos/".$archivo ) ) {
    $archivo = $filex."(".$contador.")".$extension;
    $contador++;
  }

  print $faena." // ".$link." // ".$archivo;
  move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/documentos/".$archivo);
  mysql_query("INSERT INTO documentos VALUES ('', '$documento', '$nombre', '$archivo', '$descripcion','$perfiles')");

  //header("Location: index.php?s=libreria");

?>