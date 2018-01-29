<?php
require('../../../autoload.php');

// error_reporting(E_ALL);

$direccion    = $_GET['direccion']; 
$tipo_equipo  = $_GET['tipo']; 
$zoom         = isset($_GET['zoom']) ? $_GET['zoom'] : 18;
$color        = $_GET['color'];

$db = DB::getInstance();
$db = $db->query("SELECT CLASS_IMG FROM uman_tipo_equipo WHERE ID=$tipo_equipo;");
$class_img = ($db->count()>0) ? $db->results() : '';
if($class_img != '')
{
 $class_img = $class_img[0]->CLASS_IMG;
}

$dir = str_replace("°", "", $direccion);

$base = 50;

$dir  = './';
$camion = $class_img.'.png';
$marcador = $color.'.png';

$transparent = 0;

$imageBase = imagecreatefrompng($dir.'base.png');
$transparent = imagecolorallocatealpha($imageBase,0,0,0,127);
imagealphablending($imageBase, false);
imageSaveAlpha($imageBase, true);

$imgPointer = imagecreatefrompng($dir.$marcador);
imagealphablending($imgPointer, false);
imageSaveAlpha($imgPointer, true);
$transparent = imagecolorallocatealpha($imgPointer,0,0,0,127);
$imgPointer = imagerotate($imgPointer,360-floatval($direccion),$transparent,0);
imagealphablending($imgPointer, false);
imageSaveAlpha($imgPointer, true);

$imgEquipo = imagecreatefrompng($dir.$camion);	
imagealphablending($imgPointer, false);
imageSaveAlpha($imgEquipo, true);

$img = imagecreatetruecolor(imagesx($imageBase), imagesy($imageBase));
imagealphablending($img, true);
imageSaveAlpha($img, true);
$transparent = imagecolorallocatealpha($imageBase,0,0,0,127);
imagefill($img,0,0,$transparent);

$w = imagesx($imgPointer);
$angulo = $direccion;
$a = (imagesy($imgPointer) - imagesy($imageBase))/2;
$b = (imagesx($imgPointer) - imagesx($imageBase))/2;

imagecopy($img, $imgPointer, -$b, -$a, 0, 0, imagesx($imgPointer), imagesy($imgPointer));
imagecopy($img, $imgEquipo, 0, 0, 0, 0, imagesx($imgEquipo), imagesy($imgEquipo));
// $color_texto = imagecolorallocate($img, 0, 0, 255);
// imagestring($img, 5, 10, 0, 'a: '.$a, $color_texto);
// imagestring($img, 5, 10, 30, 'b: '.$b, $color_texto);
// imagestring($img, 5, 10, 60, 'angulo: '.$angulo, $color_texto);
// imagestring($img, 5, 10, 90, 'w: '.$w, $color_texto);

header("Content-type: image/png");
imagepng($img);
imagedestroy($img);
imagedestroy($imageBase);
imagedestroy($imgPointer);
imagedestroy($imgEquipo);
?>