<?php

$equipo = $_GET['equipo'];

include('../../conectar.php');

$data = mysql_query("SELECT X,ALTURA,Y,RAPIDEZ,DIRECCION,FECHA FROM uman_ultimogps WHERE NUMCAMION='$equipo' LIMIT 1");


$info=mysql_fetch_array($data);
$X=$info['X'];
$ALTURA=$info['ALTURA'];
$Y=$info['Y'];
$RAPIDEZ=$info['RAPIDEZ'];
$DIRECCION=$info['DIRECCION'];
$FECHA=$info['FECHA'];

if($X!=false){
 
   echo json_encode(array('encontrado'=>true,
'X'=>$X,
'ALTURA'=>$ALTURA,
'Y'=>$Y,
'RAPIDEZ'=>$RAPIDEZ,
'DIRECCION'=>$DIRECCION,
'FECHA'=>$FECHA));

}else{
 
   echo json_encode(array(
        'encontrado'=>false
    ));

}



?>








