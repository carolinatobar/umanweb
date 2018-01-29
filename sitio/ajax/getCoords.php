<?php

$equipo = $_GET['equipo'];
$dato 	= date("Y-m-d H:i:s",$_GET['dato']+10800);

//print $dato;

//$dato 	= strtotime("2017-04-28 00:21:01");
//$equipo = "10007";

session_start();
$sess_id = session_id();
$faena 		= $_SESSION[$sess_id]['faena'];

include('../conectar.php');
$link         = Conectar( $faena );

$data_equipo = mysql_query("SELECT ID_CAMION FROM uman_camion WHERE NUMCAMION='$equipo'");
$info_equipo = mysql_fetch_array( $data_equipo );

$id_equipo   = $info_equipo['ID_CAMION'];

$data = mysql_query("SELECT X,Y,rapidez,direccion,FECHAGPS FROM uman_gps WHERE EQUIPO='$id_equipo' AND FECHAGPS='$dato' LIMIT 1");

$info 		= mysql_fetch_array($data);
$X 			= $info['X'];
$Y 			= $info['Y'];
$RAPIDEZ 	= $info['rapidez'];
$DIRECCION 	= $info['direccion'];
$FECHA 		= $info['FECHAGPS'];

if( $X != false ) {
 
   echo json_encode(array('encontrado'=>true,
		'X'=>$X,
		'Y'=>$Y,
		'RAPIDEZ'=>$RAPIDEZ,
		'DIRECCION'=>$DIRECCION,
		'FECHA' => $FECHA
	));

} else {
 
   echo json_encode(array(
        'encontrado'=>false,
        'FECHA'=>$dato." no encontrado"
    ));

}


//	echo json_encode(array('encontrado'=>true,
//		'dato'=>$dato));


?>



