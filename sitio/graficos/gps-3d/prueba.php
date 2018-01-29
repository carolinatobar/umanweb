<?php

include("../../conectar.php");

$totalmapa = 0;


$infomapa = mysql_query("SELECT X,ALTURA,Y FROM uman_mapa_soldado");

    while ( $mapa = mysql_fetch_array ( $infomapa ) ) {
    	if($totalmapa >= 1000) {
    	if ( $totalmapa != 0 ) {
    		print ",";
    	}

    	print $mapa['X']."|".$mapa['ALTURA']."|".$mapa['Y'];
    }
		$totalmapa++;
	}


?>