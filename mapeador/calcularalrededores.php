<?php


$lugar = "mato_verde";


$xmin = $_GET['xmin'];
$xmax = $_GET['xmax'];
$zmin = $_GET['zmin'];
$zmax = $_GET['zmax'];

$cantidad = 2479;

$rangoX = round(($xmax - $xmin)*1000000)/1000000;
$rangoZ = round(($zmax - $zmin)*1000000)/1000000;



$prop = $rangoZ/$rangoX;

$cantidadX = floor(sqrt($cantidad/$prop));
$cantidadZ = floor($cantidadX*$prop);

$totalpuntos = $cantidadX * $cantidadZ;

if ( $cantidadX > $cantidadZ ) {
	while ( ( $cantidadX * ( $cantidadZ + 1 ) ) <= $cantidad ) {
		$cantidadZ++;
	}
} else if ( $cantidadX < $cantidadZ ) {
	while ( ( $cantidadZ  * ( $cantidadX + 1 ) ) <= $cantidad ) {
		$cantidadX++;
	}
}
	
print "Rango X: ".$rangoX." || ".$rangoZ." || ".$prop."<p>";
print "X: ".$cantidadX." || Z: ".$cantidadZ." || Total de puntos: ".($cantidadZ*$cantidadX)."<p>";


if ( isset ( $_GET['ico'] ) ) {
	$ico  = $_GET['ico'];
} else {
	$ico = 0;
}

$key[0] = "AIzaSyAgLKMCZ5f8GUW4l5x2l6vVrOw3yWYy078";
$key[1] = "AIzaSyCsstvCPcQIpjc3caNxEK-joFFJohOqp2c";

$total = 0 + $ico;

print ($xmax)." // ";
print ($xmin)." // ";
print ($zmin)." // ";
print ($zmax)." <p> ";

//$ico = $ico;
//print "<body onload=window.open('http://localhost/umanweb2/sitio/graficos/gps-3d/calcularalrededores.php?xmin=-32.665859&ymin=0&zmin=-71.143112&xmax=-32.656178&ymax=991&zmax=-71.115677&ico=".$ico."')>";

$file = "mapa_".$lugar.".txt";
$escalaX = round((($xmax - $xmin)/$cantidadX)*1000000)/1000000;
$maxX = round(($xmax - $xmin)*1000000)/1000000;
print "Escala de eje X: ".$escalaX." // Desplazamiento total: ".$maxX."<br>";
$escalaZ = round((($zmax - $zmin)/$cantidadZ)*1000000)/1000000;
$maxZ = round(($zmax - $zmin)*1000000)/1000000;
print "Escala de eje Z: ".$escalaZ." // Desplazamiento total: ".$maxZ."<p>";

print "Puntos a sensar:<p>";

for ( $i = $ico ; $i < $cantidadX ; $i++ ) {

	if ( $i != $ico ) {
		$buffer = "#";

    	print $buffer;

    	if (file_exists($file)) {
        	$buffer = file_get_contents($file) . $buffer;
    	}

    	$success = file_put_contents($file, $buffer);
	}

	for ( $j = 0 ; $j < $cantidadZ ; $j++ ) {
		
		$z[$total] = ( $escalaZ*$j ) + $zmin;
		$x[$total] = ( $escalaX*$i ) + $xmin;
		$json = "https://maps.googleapis.com/maps/api/elevation/json?locations=".$z[$total].",".$x[$total]."&key=".$key[1];
		print "X: ".$x[$total]." // Z: ".$z[$total]." // Link: ".$json."<br>";
		$str = file_get_contents($json);
		$json = json_decode($str, true);
		$altura[$total] = round($json['results'][0]['elevation']);
		print "<b>ALTURA:".$altura[$total]." msnm</b><br>";
		print "<p>";
		echo '<pre>' . print_r($json, true) . '</pre>';
		
		print "<p>";

		//$query = "INSERT INTO uman_mapa_soldado (ID,X,Y,ALTURA) values ('','".$x[$total]."','".$z[$total]."','".$altura[$total]."')";
		//mysql_query($query);

    	$buffer = $x[$total]."|".$altura[$total]."|".$z[$total];

    	print $buffer;

    	if (file_exists($file)) {
        	$buffer = file_get_contents($file) . "," . $buffer;
    	}

    	$success = file_put_contents($file, $buffer);

		print "<p><p>".$total."<p><p>";

		$total++;



	}

}



		$buffer = $xmin."|".$xmax."|".$zmin."|".$zmax;

    	print $buffer;

    	$success = file_put_contents($lugar.".info", $buffer);


?>
