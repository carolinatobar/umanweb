<style>
.table-responsive
{
	width: 100%;
    overflow-x: auto;
}
.table
{
	width: 100%;
}
</style>

<div id="#contenedor-alarmas">bla
	<div class='container-fluid'>
		<div class='row-fluid'>
			<?php
				$alarmaTemp = 32;
				$alarmaPresSup = 128;
				$alarmaPresInf = 64;
				$alarmaTimeOut = 8;
				$LimitAlarmList = 10;
				$TextSize = "12px";


				//************************************************Tabla Alarmas de Temperatura************************************

				$info = mysql_query("SELECT *
					FROM(	SELECT ALARMANUMCAMION, ALARMAPOSICION, ALARMAVALOR, time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)), max(ALARMAFECHA)
						FROM uman_alarmas 
						WHERE ALARMATIPO='$alarmaTemp' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR )
						GROUP BY ALARMANUMCAMION, ALARMAPOSICION 
						ORDER BY ALARMAFECHA ASC
						LIMIT $LimitAlarmList
						)
						tmp ORDER BY tmp.ALARMAVALOR DESC"
						);
			?>	

			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="color: white; background-color: red ; background-image: none; font-size:25px">Temperatura</div>
			
					<div class="panel panel-body">
						<table class="table table-sm">
							<thead>
								<?php echo "<tr style='background-color: white; font-size:$TextSize'>"; ?>
									<th class="text-center">Equipo</th>
									<th class="text-center">Pos</th>
									<th class="text-center">Temp</th>
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php  	
									while ( $data = mysql_fetch_array ( $info ) ) 
									{
										echo "<tr style='font-size:$TextSize ; text-align:center'>";
										echo "<td>" . $data[0] . "</td>";
										echo "<td>" . $data[1] . "</td>";
										echo "<td>" . $data[2] . " ºC </td>";
										echo "<td>" . intval($data[3]/60) . "</td>";
										echo "</tr>";
									}
								?>
							<tbody>
						</table>
					</div>
				</div>
			</div>

			<?php
				//************************************************Tabla Alarmas de Presion Baja********************************************

				$info = mysql_query("SELECT *
					FROM(	SELECT ALARMANUMCAMION, ALARMAPOSICION, ALARMAVALOR, time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)), max(ALARMAFECHA)
						FROM uman_alarmas 
						WHERE ALARMATIPO='$alarmaPresInf' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR)
						GROUP BY ALARMANUMCAMION, ALARMAPOSICION 
						ORDER BY ALARMAFECHA DESC
						LIMIT $LimitAlarmList)	
						tmp ORDER BY tmp.ALARMAVALOR DESC"
					);
			?>		
			
			<div class="col-sm-6">
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="background-color: yellow ; background-image: none; font-size:25px">Presión Baja</div>
						
					<div class="panel panel-body">	
						<table class="table table-sm">
							<thead>
								<?php echo "<tr style='background-color: white; font-size:$TextSize'>"; ?>
									<th class="text-center">Equipo</th>
									<th class="text-center">Pos</th>
									<th class="text-center">P. Baja</th>			    			
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php  
									while ( $data = mysql_fetch_array ( $info ) ) 
									{
										echo "<tr style='font-size:$TextSize ; text-align:center'>";
										echo "<td>" . $data[0] . "</td>";
										echo "<td>" . $data[1] . "</td>";
										echo "<td>" . $data[2] . " PSI </td>";
										echo "<td>" . intval($data[3]/60) . "</td>";
										echo "</tr>";
									}
								?>
							<tbody>		
						</table>
					</div>	

				</div>
			</div>
		</div>
	</div>

	<div class='container-fluid'>
		<div class='row-fluid'>
			<?php 	
				//************************************************Tabla Alarmas de Presion Alta****************************************************
				
				$info = mysql_query("SELECT *
					FROM(	SELECT  
							ALARMANUMCAMION, 
													ALARMAPOSICION, 
													ALARMAVALOR, 
													time_to_sec(TIMEDIFF(NOW(),ALARMAFECHA)), 
													max(ALARMAFECHA)
											FROM uman_alarmas 
											WHERE ALARMATIPO='$alarmaPresSup' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 1 HOUR)
											GROUP BY ALARMANUMCAMION, ALARMAPOSICION 
											ORDER BY ALARMAFECHA ASC
											LIMIT $LimitAlarmList
										)
										tmp ORDER BY tmp.ALARMAVALOR DESC"
									);
			?>	    

			<div class="col-sm-6"> 
				<div class="panel panel-default">
					<div class="panel-heading text-center" style="background-color: orange ; background-image: none; font-size:25px">Presión Alta</div>

					<div class="panel panel-body">	
						<table class="table table-sm">
							<thead>
								<?php echo "<tr style='background-color: white ; font-size:$TextSize'>"; ?>
									<th class="text-center">Equipo</th>
									<th class="text-center">Pos</th>
									<th class="text-center">P. Alta</th>
									<th class="text-center">Minutos</th>
								</tr>
							</thead>
							<tbody>
								<?php
									while ( $data = mysql_fetch_array ( $info ) ) 
									{
										echo "<tr style='font-size:$TextSize ; text-align:center'>";
										echo "<td>" . $data[0] . "</td>";
										echo "<td>" . $data[1] . "</td>";
										echo "<td>" . $data[2] . " PSI </td>";
										echo "<td>" . intval($data[3]/60) . "</td>";
										echo "</tr>";
									}
								?>
							<tbody>		
						</table>
					</div>	

				</div>
			</div>	

			<?php 
				//************************************************Tabla Alarmas de Time Out**********************************************************
																																		//DATEDIFF(CURDATE(),ALARMAFECHA)																	//LIMIT $LimitAlarmList
				$info = mysql_query("SELECT ALARMANUMCAMION, ALARMAPOSICION, ALARMACODSENSOR, time_to_sec( TIMEDIFF( NOW(),ALARMAFECHA ) )
								FROM (SELECT *
										FROM uman_alarmas
										WHERE ALARMATIPO = '$alarmaTimeOut' AND ALARMAFECHA > DATE_SUB(NOW(), INTERVAL 360 DAY)
										GROUP BY ALARMANUMCAMION, ALARMAPOSICION
									)AS t1
									LEFT OUTER JOIN (SELECT EVENTOCODSENSOR  
														FROM uman_eventos 
														WHERE EVENTOFECHA > DATE_SUB(NOW(), INTERVAL 2 DAY) 
														GROUP BY EVENTOCODSENSOR
														) AS t2 
									ON t1.ALARMACODSENSOR = t2.EVENTOCODSENSOR
										WHERE t2.EVENTOCODSENSOR IS NULL
										ORDER BY ALARMAFECHA ASC
										LIMIT $LimitAlarmList"
									);
			?>	

			<div class="col-sm-6">
				<div class="panel panel-default">
				<div class="panel-heading text-center" style="color: white; background-color: gray ; background-image: none; font-size:25px">Sensor sin transmitir</div>

				<div class="panel panel-body">	
					<table class="table table-sm">
						<thead>
							<?php echo "<tr style='background-color: white ; font-size:$TextSize'>"; ?>
								<th class="text-center">Equipo</th>
								<th class="text-center">Pos</th>
								<th class="text-center">Sensor</th>
								<th class="text-center">Días</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while ( $data = mysql_fetch_array ( $info ) ) 
								{
									echo "<tr style='font-size:$TextSize ; text-align:center'>";
									echo "<td>" . $data[0] . "</td>";
									echo "<td>" . $data[1] . "</td>";
									echo "<td>" . $data[2] . "</td>";
									echo "<td>" . intval($data[3]/86400) . "</td>";
									echo "</tr>";
								}
							?>
						<tbody>	
					</table>
				</div>	
				
			</div>
		</div>
			
	</div>

</div>

<p>&nbsp;</p>

<script type="text/javascript">
	setInterval(function(){
		$.get('showAlarmas.php #contenedor-alarmas', function(response, status, xhr){
			console.log(response);
			$("#contenedor-alarmas").html(response);
		});
	},10000);
</script>