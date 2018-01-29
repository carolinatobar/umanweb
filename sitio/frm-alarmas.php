<?php 
	require 'autoload.php';

	@session_start();
	$acceso = new Acceso($_SESSION, session_id());
	$gen    = new General();

	$unidad_temp  = $gen->getParamValue('unidad_temperatura');
	$unidad_pres  = $gen->getParamValue('unidad_presion');
	$nomenclatura = $gen->getNomenclaturas();
	$img_equipo   = $gen->getImagenesEquipo();
	$timeout      = $gen->getParamValue('timeout');

	$db = DB::getInstance();
	$alarmaTemp     = 32;
	$alarmaPresSup  = 128;
	$alarmaPresInf  = 64;
	$alarmaTimeOut  = 8;
	$LimitAlarmList = 10;
	$TextSize       = "12px";

	$fecha = date("d/m/Y H:i", mktime(date("H")-1, date("i"), date("s"), date("n"), date("j"), date("Y")));

	//Alarma temperatura
	$sql = "SELECT * FROM 
		(SELECT ALARMANUMCAMION AS 'numequipo', ALARMAPOSICION AS 'posicion', ALARMAVALOR AS 'valor', 
			time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)) AS 'TIEMPO',
			DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS 'fecha' 
			FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION
			WHERE ALARMATIPO='$alarmaTemp' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR )
			ORDER BY ALARMAFECHA ASC LIMIT $LimitAlarmList)
		tmp ORDER BY tmp.valor DESC";
	// $sql = "SELECT * FROM 
	// 	(SELECT numequipo, NUMCAMION, posicion, time_to_sec(TIMEDIFF(NOW(),fecha_evento)) AS TIEMPO, max(fecha_evento), 
	// 		DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fecha, eventotemperatura AS valor  
	// 		FROM uman_ultimoevento INNER JOIN uman_camion ON numequipo=ID_CAMION
	// 		WHERE eventotemperatura > tempmax AND fecha_evento > DATE_SUB(NOW(), INTERVAL 1 HOUR )
	// 		GROUP BY numequipo, posicion 
	// 		ORDER BY fecha_evento ASC LIMIT $LimitAlarmList)
	// 	tmp ORDER BY tmp.valor DESC";
	// echo $sql; exit();
	$temperatura = $db->query($sql);
	$temperatura = $temperatura->count() > 0 ? $temperatura->results() : array();

	//Alarma presión alta
	/*$sql = "SELECT * FROM
		(SELECT ALARMANUMCAMION AS 'numequipo', ALARMAPOSICION AS 'posicion', ALARMAVALOR AS 'valor', 
		time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)) AS TIEMPO, max(ALARMAFECHA), 
			DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS 'fecha', NUMCAMION
			FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION 
			WHERE ALARMATIPO='$alarmaPresSup' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR)
			GROUP BY ALARMANUMCAMION, ALARMAPOSICION 
			ORDER BY ALARMAFECHA ASC LIMIT $LimitAlarmList)
		tmp ORDER BY tmp.valor DESC";
		*/
		$sql = "SELECT * FROM
		(SELECT ALARMANUMCAMION AS 'numequipo', ALARMAPOSICION AS 'posicion', ALARMAVALOR AS 'valor', 
		time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)) AS TIEMPO, 
			DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS 'fecha', NUMCAMION
			FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION 
			WHERE ALARMATIPO='$alarmaPresSup' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR )
			ORDER BY ALARMAFECHA ASC LIMIT $LimitAlarmList)
		tmp ORDER BY tmp.valor DESC";
	// $sql = "SELECT * FROM 
	// 	(SELECT numequipo, NUMCAMION, posicion, time_to_sec(TIMEDIFF(NOW(),fecha_evento)) AS TIEMPO, max(fecha_evento), 
	// 		DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fecha, eventotemperatura AS valor  
	// 		FROM uman_ultimoevento INNER JOIN uman_camion ON numequipo=ID_CAMION
	// 		WHERE eventopresion > presmax AND fecha_evento > DATE_SUB(NOW(), INTERVAL 1 HOUR )
	// 		GROUP BY numequipo, posicion 
	// 		ORDER BY fecha_evento ASC LIMIT $LimitAlarmList)
	// 	tmp ORDER BY tmp.valor DESC";
	$presion_alta = $db->query($sql);
	$presion_alta = $presion_alta->count() > 0 ? $presion_alta->results() : array();

	//Alarma presión baja
	/*$sql = "SELECT * FROM
		(SELECT ALARMANUMCAMION AS 'numequipo', ALARMAPOSICION AS 'posicion', ALARMAVALOR AS 'valor', 
			time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)) AS 'TIEMPO', max(ALARMAFECHA), 
			DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS 'fecha', NUMCAMION  
			FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION 
			WHERE ALARMATIPO='$alarmaPresInf' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR)
			GROUP BY ALARMANUMCAMION, ALARMAPOSICION 
			ORDER BY ALARMAFECHA DESC LIMIT $LimitAlarmList) tmp 
		ORDER BY tmp.valor DESC";*/
$sql = "SELECT * FROM
		(SELECT ALARMANUMCAMION AS 'numequipo', ALARMAPOSICION AS 'posicion', ALARMAVALOR AS 'valor', 
			time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)) AS 'TIEMPO', 
			DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS 'fecha', NUMCAMION  
			FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION 
			WHERE ALARMATIPO='$alarmaPresInf' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR )
			ORDER BY ALARMAFECHA DESC LIMIT $LimitAlarmList) tmp 
		ORDER BY tmp.valor DESC";
	// $sql = "SELECT * FROM 
	// 	(SELECT numequipo, NUMCAMION, posicion, time_to_sec(TIMEDIFF(NOW(),fecha_evento)) AS TIEMPO, max(fecha_evento), 
	// 		DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fecha, eventotemperatura AS valor  
	// 		FROM uman_ultimoevento INNER JOIN uman_camion ON numequipo=ID_CAMION
	// 		WHERE eventopresion < presmin AND fecha_evento > DATE_SUB(NOW(), INTERVAL 1 HOUR )
	// 		GROUP BY numequipo, posicion 
	// 		ORDER BY fecha_evento ASC LIMIT $LimitAlarmList)
	// 	tmp ORDER BY tmp.valor DESC";
	$presion_baja = $db->query($sql);
	$presion_baja = $presion_baja->count() > 0 ? $presion_baja->results() : array();

	//Alarma timeout
	// $sql = "SELECT ALARMANUMCAMION, ALARMAPOSICION, ALARMACODSENSOR, time_to_sec( TIMEDIFF( NOW(),ALARMAFECHA )) AS TIEMPO, NUMCAMION, 
	// 	DATE_FORMAT(ALARMAFECHA,'%d/%m/%Y %H:%i:%s') AS ALARMAFECHA 
	// 	FROM (
	// 		SELECT * FROM uman_alarmas ua INNER JOIN uman_camion uc ON ua.ALARMANUMCAMION=uc.ID_CAMION 
	// 		WHERE ALARMATIPO = '$alarmaTimeOut' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL {$timeout} DAY)
	// 		GROUP BY ALARMANUMCAMION, ALARMAPOSICION
	// 	) AS t1
	// 	LEFT OUTER JOIN (
	// 		SELECT EVENTOCODSENSOR FROM uman_eventos 
	// 		WHERE EVENTOFECHA > DATE_SUB(NOW(), INTERVAL 2 DAY) 
	// 		GROUP BY EVENTOCODSENSOR
	// 	) AS t2 ON t1.ALARMACODSENSOR = t2.EVENTOCODSENSOR
	// 	WHERE t2.EVENTOCODSENSOR IS NOT NULL
	// 	ORDER BY ALARMAFECHA ASC
	// 	LIMIT $LimitAlarmList";
	$sql = "SELECT numequipo, NUMCAMION, posicion, SENSOR, time_to_sec( TIMEDIFF( NOW(), fecha_evento )) AS TIEMPO, 
		DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i:%s') AS fecha 
		FROM uman_ultimoevento INNER JOIN uman_camion ON numequipo=ID_CAMION 
		WHERE fecha_evento < DATE_SUB(NOW(), INTERVAL {$timeout} MINUTE ) AND fecha_evento NOT LIKE '%0000%' 
		GROUP BY numequipo, posicion
		ORDER BY fecha_evento ASC
		LIMIT $LimitAlarmList";

	$timeout = $db->query($sql);
	$timeout = $timeout->count() > 0 ? $timeout->results() : array();
?>
<style>
	<?php include('assets/css/detalle-equipo.css') ?>
	<?php include('assets/css/uman/tabla.css') ?>
	.table-responsive
	{
		width: 100%;
		/* overflow-x: auto; */
	}
	.table
	{
		width: 100%;
	}
	td{
		text-align: center;
	}
	th{
		font-weight: 800;
	}
	.panel-heading{
		font-size: 90%;
		font-weight: bold;
	}
	@media (max-width: 426px){
		table{
			font-size: 70%;
		}
		td div.icono-x24{
			width: 16px;
			height: 16px;
			background-size: 16px;
		}
		.panel-heading{
			font-size: 12px;
		}
	}
	@media (max-width: 320px){
		table{
			font-size: 60%;
		}
		.panel-body{
			padding: 0 !important;
		}
		td{
			padding: 8px 4px !important;
		}
	}
</style>
<div class="container">
 	<div class="cc-divider">Alarmas Flota desde <?=$fecha?></div>
	<div id="contenedor-alarmas">
		<div class="row">

			<!-- ALARMAS DE TEMPERATURA -->
			<div class="<?php Core::col(6,6,12,12) ?>">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="color: white; background: red;">Temperatura</div>	
					<div class="panel panel-body">
						<table class="table table-sm" width="100%">
							<thead>
								<tr>
									<th class="text-center">Equipo</th>
									<th class="text-center">Posición</th>
									<th class="text-center">T&deg;</th>
									<th class="text-center">Fecha/Hora</th>
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($temperatura as $t){
										echo "<tr>";
										echo '<td>'.$img_equipo[$t->numequipo]['DIV'] . $t->NUMCAMION . '</td>';
										echo "<td>" . $nomenclatura[$t->posicion] . "</td>";
										echo "<td>" . Core::tpConvert($t->valor,$unidad_temp) . "</td>";
										echo "<td>" . $t->fecha . "</td>";
										echo "<td>" . intval($t->TIEMPO/60) . "</td>";
										echo "</tr>";
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
				
			<!-- ALARMAS DE PRESIÓN BAJA -->
			<div class="<?php Core::col(6,6,12,12) ?>">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="color: #263238; background-color: yellow;">Presión Baja</div>		
					<div class="panel panel-body">	
						<table class="table table-sm">
							<thead>
								<tr>
									<th class="text-center">Equipo</th>
									<th class="text-center">Posición</th>
									<th class="text-center">P&deg;</th>
									<th class="text-center">Fecha/Hora</th>
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($presion_baja as $p){
										echo "<tr>";
										echo '<td>'.$img_equipo[$p->numequipo]['DIV'] . $p->NUMCAMION . '</td>';
										echo "<td>" . $nomenclatura[$p->posicion] . "</td>";
										echo "<td>" . Core::tpConvert($p->valor,$unidad_pres) . "</td>";
										echo "<td>" . $p->fecha . "</td>";
										echo "<td>" . intval($p->TIEMPO/60) . "</td>";
										echo "</tr>";
									}
								?>
							</tbody>		
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- ALARMAS DE PRESIÓN ALTA -->
			<div class="<?php Core::col(6,6,12,12) ?>">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="color: #263238; background-color: orange ;">Presión Alta</div>		
					<div class="panel panel-body">	
						<table class="table table-sm">
							<thead>
								<tr>
									<th class="text-center">Equipo</th>
									<th class="text-center">Posición</th>
									<th class="text-center">P&deg;</th>
									<th class="text-center">Fecha/Hora</th>
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($presion_alta as $p){
										echo "<tr>";
										echo '<td>'.$img_equipo[$p->numequipo]['DIV'] . $p->NUMCAMION . '</td>';
										echo "<td>" . $nomenclatura[$p->posicion] . "</td>";
										echo "<td>" . Core::tpConvert($p->valor,$unidad_pres) . "</td>";
										echo "<td>" . $p->fecha . "</td>";
										echo "<td>" . intval($p->TIEMPO/60) . "</td>";
										echo "</tr>";
									}
								?>
							</tbody>		
						</table>
					</div>
				</div>
			</div>

			<!-- ALARMAS TIMEOUT -->
			<div class="<?php Core::col(6,6,12,12) ?>">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="color: white; background-color: gray;">Sensor sin transmitir</div>		
					<div class="panel panel-body">	
						<table class="table table-sm">
							<thead>
								<tr>
									<th class="text-center">Equipo</th>
									<th class="text-center">Posición</th>
									<th class="text-center">Sensor</th>
									<th class="text-center">Fecha/Hora</th>
									<th class="text-center">Tiempo</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($timeout as $p){
										$t = $p->TIEMPO;
										$tiempo = '';
										if($t<=60) $tiempo = "{$t} segs.";
										else if($t>60 && $t<3600){
											$t = intval($t/60);
											$tiempo = "{$t} min.";
										}
										else if($t>=3600 && $t<86400){
											$t = intval($t/3600);
											$tiempo = "{$t} hrs.";
										}
										else{
											$t = intval($t/86400);
											$tiempo = "{$t} días.";
										}
										echo "<tr>";
										echo '<td>'.$img_equipo[$p->numequipo]['DIV'] . $p->NUMCAMION . '</td>';
										echo "<td>" . $nomenclatura[$p->posicion] . "</td>";
										echo "<td>" . $p->SENSOR . "</td>";
										echo "<td>" . $p->fecha . "</td>";
										echo "<td>" . $tiempo . "</td>";
										echo "</tr>";
									}
								?>
							</tbody>		
						</table>
					</div>
				</div>
			</div>

		</div>
	</div>

	<p>&nbsp;</p>

	<script type="text/javascript">
		/*
		setInterval(function(){
			$("#contenedor-alarmas").load('frm-alarmas.php #contenedor-alarmas', function(response, status, xhr){
				console.log(status);
				// console.log(response);
				// $("#contenedor-alarmas").html(response);
			});
		},10000);*/
	</script>
</div>