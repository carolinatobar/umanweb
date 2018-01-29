<?php

@session_start();
$sess_id = session_id();

/* idiomas que soportamos */
$sup_lang=array("es", "en", "po");

/* recogemos el valor almacenado, de haberlo */

if ( $_SESSION[$sess_id]["lang"] != "" ) {
  $lang     = $_SESSION[$sess_id]["lang"];
} else {
  $cli_lang = explode(",", $HTTP_ACCEPT_LANGUAGE);

  /* es un poco lioso, pero no hay más remedio - ejemplo: */
  /* si el cliente indica 'es-es', seleccionaremos 'es' */
  /* en el momento que un idioma coincide, dejamos de buscar */

  for ( $i = 0 ; $i < count( $cli_lang ) && !isset( $lang ) ; $i++ ) {
    for ( $j = 0 ; $j < count( $sup_lang ) ; $j++ ) {
      if ( !strncmp( $cli_lang[$i] , $sup_lang[$j] , strlen( $sup_lang[$j] ) ) ) {
        $lang = $sup_lang[$j];
        break;
      }
    }
  }
}

/* podemos cambiar de lenguaje con un GET */
/* y esta decisión manda sobre lo que diga el navegador */
if ( isset($_GET["lang"]) && $_GET["lang"] != "" ) {
  $lang   = $_GET["lang"];
}

switch( $lang ) {
  /* por defecto hemos quedado que es castellano */
  default:
  case "es":
    include_once("idiomas/es.inc.php");
    $_SESSION[$sess_id]["lang"]="es";
  break;

  case "en":
    include_once("idiomas/en.inc.php");
    $_SESSION[$sess_id]["lang"]="en";
  break;

  case "po":
    include_once("idiomas/po.inc.php");
    $_SESSION[$sess_id]["lang"]="po";
  break;

  case "de":
    include_once("idiomas/de.inc.php");
    $_SESSION[$sess_id]["lang"]="de";
  break;
}