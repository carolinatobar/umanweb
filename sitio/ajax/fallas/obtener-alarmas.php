<?php
  require '../../autoload.php';

  $acc = new Acceso(true);

  $gen = new General();

  $porcentaje_tmax = $gen->getParamValue('desviotemp');
  $porcentaje_pmin = $gen->getParamValue('desviopresmin');
  $porcentaje_pmax = $gen->getParamValue('desviopresmax');
  $unidad_pres     = $gen->getParamValue('unidad_presion');
  $unidad_temp     = $gen->getParamValue('unidad_temperatura');
  $nomenclatura    = $gen->getNomenclaturas();

  $fecha    = isset($_POST['fecha']) ? $_POST['fecha'] : NULL;
  $equipo   = isset($_POST['equipo']) ? intval($_POST['equipo']) : NULL;
  $posicion = isset($_POST['posicion']) ? intval($_POST['posicion']) : NULL;
  $tipo     = isset($_POST['tipo']) ? intval($_POST['tipo']) : NULL;

  $sql = '';
  $data = '';

  if($fecha != NULL && $equipo != NULL && $posicion != NULL){
    if(stripos($fecha,' - ') !== false && is_numeric($equipo) && is_numeric($posicion)){
      $fecha = explode(' - ', $fecha);
      $sql = "SELECT DISTINCTROW 
          EVENTOPRESION, EVENTOTEMPERATURA, ALARMAFECHA, ALARMAESTADO, COMENTARIOS, ALARMAFECHARECONOCE, ALARMAFECHARECONOCEUMANWEB, USUARIO 
        FROM uman_alarmas
        WHERE UNIX_TIMESTAMP(ALARMAFECHA) BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]', '%d/%m/%Y %H:%i')) AND UNIX_TIMESTAMP(DATE_ADD(STR_TO_DATE('$fecha[1]', '%d/%m/%Y %H:%i'), INTERVAL 59 SECOND)) 
          AND ALARMANUMCAMION = $equipo AND ALARMAPOSICION = $posicion AND ALARMATIPO = $tipo 
        ORDER BY ALARMAFECHA ASC";
        // echo "$sql";exit();

      $db = DB::getInstance();
      $evt = $db->query($sql);
      
      if($evt->count()>0){
  
        $data  = '<h5 class="text-center">Posición '.$nomenclatura[$posicion].'</h5>';
        $data .= '<table class="table" id="tabla-eventos">';
        $data .= '<thead>';
        $data .= '<tr>
          <th class="center-text">N&deg;</th>
          <th class="center-text">Fecha</th>
          <th class="center-text">Presión</th>
          <th class="center-text">Temperatura</th>
          <th class="center-text">Rec. Operador</th>
          <th class="center-text">Rec. UmanWeb / Automático</th>
          <th class="center-text">Usuario</th>
          <th class="center-text">Comentario</th>
        </tr>';
        $data .= '</thead>';
        $data .= '<tbody style="height: 300px; overflow-y: auto;">';
        $i = 1;
        foreach($evt->results() as $r){
          $color = '';
          // $alarma[8] 		= "Timeout";
          // $alarma[16] 	= "Bateria baja";
          // $alarma[32] 	= "Temperatura";
          // $alarma[64] 	= "Presi&oacute;n baja";
          // $alarma[128] 	= "Presi&oacute;n alta"; 
          if($tipo == 64) $color = 'style="background-color: yellow;"';
          if($tipo == 128) $color = 'style="background-color: orange;"';
          if($tipo == 32) $color = 'style="background-color: red; color: white;"';
          $data .= '<tr '.$color.'>';
          $data .= '<td >'.$i++.'</td>';
          $data .= '<td >'.(new DateTime($r->ALARMAFECHA))->format('d/m/Y H:i:s').'</td>';
          $data .= '<td >'.Core::tpConvert($r->EVENTOPRESION, $unidad_pres, true).'</td>';
          $data .= '<td >'.Core::tpConvert($r->EVENTOTEMPERATURA, $unidad_temp, true).'</td>';
          $data .= '<td >'.($r->ALARMAFECHARECONOCE == '0000-00-00 00:00:00' ? ' - ' : (new DateTime($r->ALARMAFECHARECONOCE))->format('d/m/Y H:i:s')).'</td>';
          $data .= '<td >'.($r->ALARMAFECHARECONOCEUMANWEB == '0000-00-00 00:00:00' ? ' - ' : (new DateTime($r->ALARMAFECHARECONOCEUMANWEB))->format('d/m/Y H:i:s')).'</td>';
          $data .= '<td >'.$r->USUARIO.'</td>';
          $data .= '<td >'.utf8_encode($r->COMENTARIOS).'</td>';
          $data .= '</tr>';
        }
        $data .= '</tbody>';
        $data .= '</table>';
        $data .= '<script type="text/javascript">
          $("#tabla-eventos").DataTable({
            "dom": "rtip",   
            "searching": false,
            "ordering": true,
            "language": {
              "url": "assets/datatables-1.10.15/lang/Spanish.json"
            }
          });
        </script>';
      }
    }
  }

  if($data == ''){
    $data  = '<h5 class="text-center">Posición '.$nomenclatura[$posicion].'</h5>';
    $data .= '<table class="table">';
    $data .= '<thead>';
    $data .= '<tr><th>N&deg;</th><th>Fecha</th><th>Presión</th><th>Temperatura</th></tr>';
    $data .= '</thead>';
    $data .= '<tbody>';
    $data .= '</tbody>';
    $data .= '</table>';
  }
  // header("Content-Type: text/html");
  echo $data;