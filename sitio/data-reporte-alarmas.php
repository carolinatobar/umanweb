<?php
	require 'autoload.php';

	$acc = new Acceso(true);
	// error_reporting(E_ALL);

	$gen = new General();
	$nomenclatura = $gen->getNomenclaturas();

	$db = DB::getInstance();
	// date_default_timezone_set('america/santiago');
	$tiempo = isset($_POST['tiempo']) ? $_POST['tiempo'] : 24;
	$titulo = '';

	if ( isset ( $_POST['equipo'] ) ) {
		$equipo = $_POST['equipo'];
		$modo 	= $_POST['modo'];
		$nomequipo = $db->query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION=$equipo;");
		$nomequipo = ($nomequipo->count()>0)?$nomequipo->results()[0]->NUMCAMION:$equipo;
	} else {
		print "ERROR";
		// exit(0);
	}
	// print_r($_POST);
	if ( isset ( $_POST['fecha'] ) ) {
			$fecha = explode(" - ", $_POST['fecha']);
			$fecha1 = $fecha[0];
			$fecha2 = $fecha[1];
	} else {
			$fecha1 = date('d/m/Y H:i:s', time() - ($tiempo*3600));
			$fecha2 = date('d/m/Y H:i:s', time());

			$titulo = "<h4>DESDE <strong>{$fecha1}</strong>  HASTA <strong>{$fecha2}</strong></h4>";
	}

	$fecha = "UNIX_TIMESTAMP(ALARMAFECHA) 
		BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha1','%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha2','%d/%m/%Y %H:%i'))";

	$alarma[8] 		= "Timeout";
	$alarma[16] 	= "Bateria baja";
	$alarma[32] 	= "Temperatura";
	$alarma[64] 	= "Presi&oacute;n baja";
	$alarma[128] 	= "Presi&oacute;n alta"; 

	$sql = "SELECT * 
		FROM uman_alarmas 
		WHERE ALARMANUMCAMION='$equipo' AND $fecha 
		ORDER BY ALARMAFECHA DESC";
	// echo $sql;

	$data = $db->query($sql);
		if($data->count()>0){
			$data = $data->results();
?>

<?php
	if($modo=='modal'){
?>
<style>
	table{
    background-color: white !important;
  }
  table th{
    text-align: center;
    font-weight: 800;
    font-size: 80%;
  }
  table td{
    padding: 2px !important;
    text-align: center;
    font-size: 70% !important;
	}

</style>
<?php
	echo $titulo;
	}
?>
<div class="table-responsive" style="overflow-y:auto;">
	<table class="table table-hover display" id="tabla-reporte" style="background: white;">
		<thead>
			<tr>
				<th width="30">Pos.</th>
				<th width="40">Sensor</th>
				<th width="70">Tipo</th>
				<th width="40">Valor</th>
				<th width="50">Fecha Alarma</th>
				<th width="30">Hora Alarma</th>
				<th width="50">Fecha Operador</th>
				<th width="50">Fecha UMANWEB</th>
				<th width="30">Hora UMANWeb</th>
				<th>Comentarios</th>
				<th width="50">Usuario</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td><input type="text" class="form-control foot-filter" data-index="0" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="1" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="2" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="3" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="4" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="5" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="6" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="7" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="8" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="9" style="width:100% !important"></td>
				<td><input type="text" class="form-control foot-filter" data-index="10" style="width:100% !important"></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
				foreach($data as $info){
					$fecha_alarma = date_format(date_create($info->ALARMAFECHA),"d/m/Y");
					$fecha_operador = date_format(date_create($info->ALARMAFECHARECONOCE),"d/m/Y H:i");
					$fecha_umanweb = date_format(date_create($info->ALARMAFECHARECONOCEUMANWEB),"d/m/Y");

					$hora_alarma = date_format(date_create($info->ALARMAFECHA),"H:i");
					$hora_operador = date_format(date_create($info->ALARMAFECHARECONOCE),"H:i");
					$hora_umanweb = date_format(date_create($info->ALARMAFECHARECONOCEUMANWEB),"H:i");

					print "<tr>";
					print "<td>".$nomenclatura[$info->ALARMAPOSICION]."</td>";
					print "<td>".$info->ALARMACODSENSOR."</td>";
					print "<td>".$alarma[$info->ALARMATIPO]."</td>";
					print "<td>";
					print ( $info->ALARMATIPO != 8 ) ? $info->ALARMAVALOR : "--";
					print "</td>";
					print "<td>".($info->ALARMAFECHA != '0000-00-00 00:00:00' ? $fecha_alarma : '-')."</td>";
					print '<td>'.($info->ALARMAFECHA != '0000-00-00 00:00:00' ? $hora_alarma : '-').'</td>';
					print "<td>".($info->ALARMAFECHARECONOCE != '0000-00-00 00:00:00' ? $fecha_operador : '-' )."</td>";
					// print '<td>'.($info->ALARMAFECHARECONOCE != '0000-00-00 00:00:00' ? $hora_operador : '-' ).'</td>';
					print "<td>".($info->ALARMAFECHARECONOCEUMANWEB != '0000-00-00 00:00:00' ? $fecha_umanweb : '-')."</td>";
					print '<td>'.($info->ALARMAFECHARECONOCEUMANWEB != '0000-00-00 00:00:00' ? $hora_umanweb : '-').'</td>';
					print '<td>';
					$tam = 10;
					if(strlen($info->COMENTARIOS)>$tam){
						$corto = substr($info->COMENTARIOS,0,$tam);
						$largo = $info->COMENTARIOS;

						print '<a role="button" tabindex="0" data-trigger="hover" data-toggle="popover" data-placement="top" title="Comentario de Reconocimiento" data-content="'.utf8_encode($largo).'" >';
						print utf8_encode($corto);
						print '...</a>';
					}
					else print utf8_encode($info->COMENTARIOS);
					print '--</td>';
					print "<td>".($info->USUARIO == '' ? '-' : $info->USUARIO)."</td>";
					print "</tr>";
				}
			?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var table;
	$(function(){
		$(function () {
			$('[data-toggle="popover"]').popover()
		});
		
		table = $("#tabla-reporte").DataTable({
			dom: 'Brtip',
			retrieve: true,
			searching: true,
			order: [4, 'desc'],
			responsive: false,
			pageLength: 15,
			buttons: {
				buttons:[
					{
						extend: 'excelHtml5',
						text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
						className: 'btn btn-info',
						filename: 'Reporte de Alarmas',
						title: 'Reporte de Alarmas'
					}, 
					{
						extend: 'print',
						text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
						key:{
							key: 'p',
							altKey: true
						},
						className: 'btn btn-info',
						title: 'Reporte de Alarmas'
					}
				]
			},
			pagingType: "full_numbers",
			language: {
				url: "assets/datatables-1.10.15/lang/Spanish.json",
				loadingRecords: '<div class="loader show"></div>'
			}
		});

		$(".foot-filter").on( 'keyup change', function () {
			table.columns($(this).data('index')).search( this.value ).draw();
		});
	});
</script>
<?php
	}
	else{
		echo $titulo;
		echo '<div class="alert alert-warning" role="alert">El equipo '.$nomequipo.' no tiene alarmas registradas en la fecha seleccionada.</div>';
	}
?>