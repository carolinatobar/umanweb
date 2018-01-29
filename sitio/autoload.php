<?php
error_reporting(E_ERROR);
// error_reporting(E_ERROR | E_WARNING);
// error_reporting(E_ALL);
// ini_set("display_errors", "1"); // shows all errors
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
ini_set("memory_limit", "64M");
ini_set("default_chartset", "UTF-8");

@define("DEBUG", false);

/*
Carga de clases para no tener que importar en cada módulo
*/
spl_autoload_register(function($fxName){
 $class = __DIR__ . '/classes/'.$fxName.'.php';
 require $class;
});

/*
Cálculo de ruta de llamada para determinar rutas principales
*/
// print_r($_SERVER);exit();
$self = explode('/',$_SERVER['REQUEST_URI']);
$url = '';
$inSitio = false;
foreach($self as $s){  
  if($s=='sitio'){
    $url .= "{$s}/";
    $inSitio = true;
    break;
  }
  else{
    if(stripos($s,'.php')===false) $url .= "{$s}/";
  }
}

if($inSitio===false) $url .= 'sitio/';

/*
Rutas globales
*/
$GLOBALS['ROOT_DIR']           = "{$url}";
$GLOBALS['ASSETS']             = "{$url}assets/";
$GLOBALS['ERRORS']             = "{$url}errors/";
$GLOBALS['TEMPLATES']          = "{$url}templates/";
$GLOBALS['LOGIN']              = "{$url}../";
$GLOBALS['SQLITE_BD']          = "{$url}sql/";
$GLOBALS['ERROR_CODES']        = "{$url}errors/error_codes.php";
$GLOBALS['TEMP_UPLOAD_IMAGE']  = "{$url}_temp/uploads/image/";

/*
Parámetros globales de sesión
*/
$GLOBALS['SESSION_TIMEOUT']    = "{$url}errors/?error=".md5('session_timeout'); // Mensaje de término de sesión forzada por tiempo.
$GLOBALS['SESSION_WARNING']    = 40; // (en segundos) antes de que la sesión termine envía mensaje para extender sesión o terminar.

/*
API here.com
*/
//$GLOBALS['HERE']['ID']         = 'tPpShACnHW0ovnpwQ6IJ';
//$GLOBALS['HERE']['CODE']       = 'vPYtvr3FruOUO-GbflHuQg';

$GLOBALS['HERE']['ID']         = 'Zy0k0jyCE5NTRwF4eXd4';
$GLOBALS['HERE']['CODE']       = 'DEP3vZM2u1_eKVlcOekIaQ';



/*
API google.com
*/
$GLOBALS['GOOGLE']['KEY']      = 'AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw';
// $GLOBALS['GOOGLE']['KEY']      = 'AIzaSyAv1PFXOWfH5w1wYOTY7Ed4ewZ3vVpa5qM';
$GLOBALS['GOOGLE']['LIBRARY']  = 'visualization,geometry,drawing';

/*
Se debe cambiar con el horario de verano e invierno
o ver manera de dejar parametrizable en base de datos. 
En "uman_sitio" habría que crear una tabla "parámetros" que 
almacene los parámetros globales que no dependan de faena
*/
date_default_timezone_set("Etc/GMT+3"); 
// date_default_timezone_set("America/Santiago");

/*
Verificación de versión mínima de PHP instalada
*/
$actual_ver   = explode('.', phpversion());
$required_ver = explode('.', '5.4.4');
// var_dump([$actual_ver, $required_ver]);

VersionControl::verificarVersion($actual_ver, $required_ver);

/*
TODO: Agregar control de versión de explorador para manejar 
incompatibilidades, quizá se podría incorporar en la misma clase.
*/