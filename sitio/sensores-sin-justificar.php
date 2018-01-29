<?php

	$datos_sensores = mysql_query("
		SELECT 
		s.ID_SENSOR,
		s.CODSENSOR,
		s.FECHA_INGRESO,
		h.FECHA
		FROM uman_sensores AS s 
		LEFT JOIN uman_historial AS h ON s.ID_SENSOR = h.ID_SENSOR
		WHERE s.BAJA = '4'
		AND h.ACCION = 'Sensor dado de baja'
		ORDER BY h.FECHA");
?>
<div class="cc-divider">Sensores dados de baja por justificar</div>

<table class="table table-hover table-striped">
<thead style="font-weight: 600">
<td>Sensor</td>
<td>Fecha de ingreso</td>
<td>Fecha de baja</td>
<td>Justificacion de baja</td>
<td></td>
</thead>
<?php

	while ( $info_sensores = mysql_fetch_array( $datos_sensores ) ) {
		print "<tr valign='middle'>\n";
		print "<td><form action='procesar-guardado.php' method='POST'><input type='hidden' name='modo' value='justificar-sensor'>";
		print "<input type='hidden' name='id' value='".$info_sensores['ID_SENSOR']."'>";
		print $info_sensores['CODSENSOR']."</td>\n";
		print "<td>".$info_sensores['FECHA_INGRESO']."</td>\n";
		print "<td>".$info_sensores['FECHA']."</td>\n";
		print "<td><select class='form-control' name='justificacion'>
		<option value='1'>Fin de vida util</option>
		<option value='2'>Falla</option>
		<option value='3'>Desprendimiento</option>
		</select></td>";
		print "<td><input type='submit' class='btn btn-primary' value='Justificar'></form></td>";
		print "</tr>";
	}

?>
</table>