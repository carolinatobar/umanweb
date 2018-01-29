<?php

include("../../conectar.php");

$xmin = $_GET['xmin'];
$xmax = $_GET['xmax'];
$ymin = $_GET['ymin'];
$ymax = $_GET['ymax'];
$zmin = $_GET['zmin'];
$zmax = $_GET['zmax'];

$cuadrado = 2;
$total = 0;

print ($xmax)." // ";
print ($xmin)." // ";
print ($ymin)." // ";
print ($ymax)." // ";
print ($zmin)." // ";
print ($zmax)." <p> ";

$escalaX = round((($xmax - $xmin)/49)*1000000)/1000000;
$maxX = round(($xmax - $xmin)*1000000)/1000000;
print "Escala de eje X: ".$escalaX." // Desplazamiento total: ".$maxX."<br>";
$escalaZ = round((($zmax - $zmin)/49)*1000000)/1000000;
$maxZ = round(($zmax - $zmin)*1000000)/1000000;
print "Escala de eje Z: ".$escalaZ." // Desplazamiento total: ".$maxZ."<p>";

print "Puntos a sensar:<p>";

for ( $i = 0 ; $i < $cuadrado ; $i++ ) {

	

	for ( $j = 0 ; $j < $cuadrado ; $j++ ) {
		
		$z[$total] = ( $escalaZ*$j ) + $zmin;
		$x[$total] = ( $escalaX*$i ) + $xmin;
		$json = "https://maps.googleapis.com/maps/api/elevation/json?locations=".$x[$total].",".$z[$total]."&key=AIzaSyAgLKMCZ5f8GUW4l5x2l6vVrOw3yWYy078";
		print "X: ".$x[$total]." // Z: ".$z[$total]." // Link: ".$json."<br>";
		$str = file_get_contents($json);
		$json = json_decode($str, true);
		$altura[$total] = round($json['results'][0]['elevation']);
		print "<b>ALTURA:".$altura[$total]." msnm</b><br>";
		print "<p>";
		echo '<pre>' . print_r($json, true) . '</pre>';
		
		print "<p>";

		$query = "INSERT INTO uman_mapa_soldado (ID,X,Y,ALTURA) values ('','".$x[$total]."','".$z[$total]."','".$altura[$total]."')";
		mysql_query($query);

		$total++;
	 }
}
?>
<a href="mapa.php?equipo=10000">Volver</a>