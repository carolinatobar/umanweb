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

  $sql = '';
  $data = '';

  if($fecha != NULL && $equipo != NULL && $posicion != NULL){
    if(stripos($fecha,' - ') !== false && is_numeric($equipo) && is_numeric($posicion)){
      $fecha = explode(' - ', $fecha);
      $sql = "SELECT DISTINCTROW EVENTOPRESION, EVENTOTEMPERATURA, EVENTOFECHA, TEMPMAX, PRESMAX, PRESMIN FROM uman_eventos
        WHERE UNIX_TIMESTAMP(EVENTOFECHA) BETWEEN UNIX_TIMESTAMP(STR_TO_DATE('$fecha[0]', '%d/%m/%Y %H:%i:%s')) AND UNIX_TIMESTAMP(STR_TO_DATE('$fecha[1]', '%d/%m/%Y %H:%i:%s')) 
          AND EVENTONUMCAMION = $equipo AND EVENTOPOSICION = $posicion 
        ORDER BY EVENTOFECHA ASC";
        // echo "QUERY: $sql";exit();

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
        </tr>';
        $data .= '</thead>';
        $data .= '<tbody style="height: 300px; overflow-y: auto;">';
        $i = 1;
        foreach($evt->results() as $r){
          $color = '';
          if($r->EVENTOPRESION <= $r->PRESMIN) $color = 'style="background-color: yellow;"';
          if($r->EVENTOPRESION >= $r->PRESMAX) $color = 'style="background-color: orange;"';
          if($r->EVENTOTEMPERATURA >= $r->TEMPMAX) $color = 'style="background-color: red; color: white;"';
          $data .= '<tr '.$color.'>';
          $data .= '<td >'.$i++.'</td>';
          $data .= '<td >'.(new DateTime($r->EVENTOFECHA))->format('d/m/Y H:i:s').'</td>';
          $data .= '<td >'.Core::tpConvert($r->EVENTOPRESION, $unidad_pres, true).'</td>';
          $data .= '<td >'.Core::tpConvert($r->EVENTOTEMPERATURA, $unidad_temp, true).'</td>';
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