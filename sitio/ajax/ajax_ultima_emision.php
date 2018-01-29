<?php
	require '../autoload.php';

	// session_start();
	$acc = new Acceso(true);

	$db  = DB::getInstance();

	$equipo 		    = 0;
	$data_equipos 	= $db->query("SELECT * FROM uman_camion WHERE NUMFLOTA != '0'");

	foreach( $data_equipos->results() as $info_equipos) {

		$id           = $info_equipos->ID_CAMION;
		$numcamion    = $info_equipos->NUMCAMION;
		$id_caja      = $info_equipos->ID_CAJAUMAN;

		$data_caja    = $db->query("SELECT CODIGOCAJA FROM uman_cajauman WHERE ID_CAJAUMAN='$id_caja'");
		$info_caja    = $data_caja->count() > 0 ? $data_caja->results()[0] : NULL;

		$codigocaja   = 0;
		@$codigocaja  = $info_caja->CODIGOCAJA;

		$data_gps     = $db->query("SELECT FECHA_DESCARGA FROM uman_ultimogps WHERE ID_EQUIPO='$id' order by FECHA_DESCARGA desc LIMIT 1");
		$info_gps     = $data_gps->count() > 0 ? $data_gps->results()[0] : null;

		$data_evento  = $db->query("SELECT FECHA_DESCARGA FROM uman_ultimoevento WHERE NUMEQUIPO='$id' order by FECHA_DESCARGA desc LIMIT 1");
		$info_evento  = $data_evento->count() > 0 ? $data_evento->results()[0] : null;

		$data_reporte = $db->query("SELECT fecha FROM uman_reportes_id WHERE id_caja='$codigocaja' order by fecha desc LIMIT 1");
		$info_reporte = $data_reporte->count() > 0 ? $data_reporte->results()[0] : null;

		$data_ultimogps = $db->query("SELECT uman_ultimogps.* FROM uman_ultimogps INNER JOIN(uman_camion INNER JOIN uman_cajauman ON uman_camion.ID_CAJAUMAN=uman_cajauman.ID_CAJAUMAN) ON uman_ultimogps.ID_EQUIPO=uman_camion.ID_CAMION WHERE CODIGOCAJA=$codigocaja");
		$info_ultimogps = $data_ultimogps->count() > 0 ? $data_ultimogps->results()[0] : null;

		$data_avisos  = $db->query("SELECT * FROM uman_umanblue_avisos WHERE CODIGOCAJA='$codigocaja' LIMIT 1");
		$info_avisos  = $data_avisos->count() > 0 ? $data_avisos->results()[0] : null;

		$data_caja    = $db->query("SELECT * FROM uman_estado_umanblue WHERE UMAN_BLUE='$codigocaja';");
		if($data_caja->count() > 0){
			$fecha = '';
			if($data_caja->results()[0]->FECHA_FLAG_UMANBLUE != '0000-00-00 00:00:00'){
				$fecha = '['.(new DateTime($data_caja->results()[0]->FECHA_FLAG_UMANBLUE))->format("d/m/Y H:i:s").']';
			}
			$estado_caja  = [
				'estado'=>$data_caja->results()[0]->FLAG_UMANBLUE, 
				'fecha'=>$fecha
			];
		 }else{
			$estado_caja  = [
				'estado'=>0, 
				'fecha'=>''
			];
		}

		$data[$equipo]['ID_CAMION']          = $id;
		$data[$equipo]['NUMCAMION']          = $info_equipos->NUMCAMION;
		$data[$equipo]['CODIGOCAJA']         = $info_caja->CODIGOCAJA;
		$data[$equipo]['FECHA_DESCARGA_GPS'] = (new DateTime($info_gps->FECHA_DESCARGA))->format("d/m/Y H:i:s");
		$data[$equipo]['FECHA_DESCARGA_EVE'] = (new DateTime($info_evento->FECHA_DESCARGA))->format("d/m/Y H:i:s");
		$data[$equipo]['FECHA_DESCARGA_ID']  = (new DateTime($info_reporte->fecha))->format("d/m/Y H:i:s");
		$data[$equipo]['FECHA_ULTIMO_GPS']   = (new DateTime($info_ultimogps->FECHAGPS))->format("d/m/Y H:i:s");
		$data[$equipo]['FECHA_AVISOS']	     = (new DateTime($info_avisos->FECHA_ERROR))->format("d/m/Y H:i:s");
		$data[$equipo]['STRING_AVISOS']	     = $info_avisos->STRING;
		$data[$equipo]['AVISO']              = '<small>'.$info_avisos->FECHA_ERROR.'</small><br/>'.$info_avisos->STRING;
		$data[$equipo]['ESTADO']             = $estado_caja['estado'] == 1 ? '<span class="bg-danger">Act. Pendiente</span>' : '<span class="bg-success">Actualizada '.$estado_caja['fecha'].'</span>';

		$equipo++;

	}

	header("Content-Type: application/json");
	echo json_encode( array('data'=>$data) );

?>