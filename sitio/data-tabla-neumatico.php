<?php
 require 'autoload.php';
  // error_reporting(E_ALL);

  $db = DB::getInstance();
  $gn = new General();

  $nomenclatura = $gn->getNomenclaturas();
  $uPres = $gn->getParamValue('unidad_presion','psi');
  $uTemp = $gn->getParamValue('unidad_temperatura','celsius');

  // date_default_timezone_set("America/Santiago");
  $neumatico = isset($_POST['neumatico'])  && is_numeric($_POST['neumatico']) ? $_POST['neumatico'] : NULL;
  $final_json = NULL;
  if($neumatico){
    $sql   = "SELECT uh.*,uc.NUMCAMION,us.CODSENSOR FROM uman_historial uh INNER JOIN uman_camion uc ON uh.ID_CAMION=uc.ID_CAMION INNER JOIN uman_sensores us ON uh.ID_SENSOR=us.ID_SENSOR WHERE uh.ID_NEUMATICO ={$neumatico} AND (uh.ACCION LIKE  'Neumatico instalado en equipo' OR uh.ACCION LIKE  'Neumatico retirado de equipo') ORDER BY uh.FECHA ASC";
    // echo $sql;exit();
    $fetch = $db->query($sql);
    $fetch = ($fetch->count()>0) ? $fetch->results() : array();

    $titulo_grafico = "NO EXISTE INFORMACION DEL NEUMATICO SELECCIONADO";

    $tabla = array();

    for($i=0;$i<count($fetch);$i++){
      $titulo_grafico = '';
      $in = $fetch[$i];
      $i++;
      $tabla[$in->ID]= array('equipo'=>$in->NUMCAMION,'sensor'=>$in->CODSENSOR,'posicion'=>$in->ID_POSICION,'desde'=>$in->FECHA,'hasta'=>'','datos'=>0);
      
      $desde = '-';
      $hasta = '-';
      
      $sql_datos = "SELECT COUNT( * ) AS tot
        FROM uman_eventos
        WHERE EVENTONUMCAMION ={$in->ID_CAMION}
        AND EVENTOPOSICION =$in->ID_POSICION
        AND EVENTOFECHA >=  '{$in->FECHA}'";
      if($i<count($fetch)){
        $out = $fetch[$i];
        $sql_fecha = "SELECT EVENTOFECHA
          FROM uman_eventos
          WHERE EVENTONUMCAMION ={$in->ID_CAMION}
          AND EVENTOPOSICION ={$in->ID_POSICION}
          AND EVENTOFECHA <=  '{$out->FECHA}'
          AND EVENTOFECHA >=  '{$in->FECHA}'
          ORDER BY EVENTOFECHA ASC";
        $hasta = '-';
        $datos_fecha = $db->query($sql_fecha);
        if($datos_fecha->count()>0){
          $datos_fecha2 = $datos_fecha->results()[0];
          $desde = $datos_fecha2->EVENTOFECHA;
          $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
          $hasta = $datos_fecha2->EVENTOFECHA;
        }
        $tabla[$in->ID]['hasta']=$hasta;
        $sql_datos .= " AND EVENTOFECHA <= '{$out->FECHA}'";
      }else{
        $sql_fecha = "SELECT EVENTOFECHA
          FROM uman_eventos
          WHERE EVENTONUMCAMION ={$in->ID_CAMION}
          AND EVENTOPOSICION ={$in->ID_POSICION}
          AND EVENTOFECHA >=  '{$in->FECHA}'
          AND EVENTOFECHA <=  NOW()
          ORDER BY EVENTOFECHA ASC";
        
        $datos_fecha = $db->query($sql_fecha);
        if($datos_fecha->count()>0){
          $datos_fecha2 = $datos_fecha->results()[0];
          $desde = $datos_fecha2->EVENTOFECHA;
          $datos_fecha2 = $datos_fecha->results()[$datos_fecha->count()-1];
          $hasta = $datos_fecha2->EVENTOFECHA;
        }
        
      }
      if($desde!='-'){
        $myDateTimeI = DateTime::createFromFormat('Y-m-d H:i:s', $desde);
        $desde = $myDateTimeI->format('d/m/Y H:i:s');
      }
      if($hasta!='-'){
        $myDateTimeI = DateTime::createFromFormat('Y-m-d H:i:s', $hasta);
        $hasta = $myDateTimeI->format('d/m/Y H:i:s');
      }
      $tabla[$in->ID]['desde']=$desde;
      $tabla[$in->ID]['hasta']=$hasta;
      $datos = $db->query($sql_datos);
      $datos = $datos->results()[0];
      $tabla[$in->ID]['datos']=$datos->tot;
    }
    
    
    $final_json  = json_encode($tabla);
  }

  if($final_json != NULL){
    if($titulo!='') echo '<br/>';
 }
?>
<!-- TABLA RESUMEN -->
<div class="<?php Core::col(12) ?>" style="overflow-x: auto">
<?php
  if(count($tabla)>0)
  {
    echo '<table class="table table-responsive tabla-resumen" style="background:#f8f8f8">';
    echo '<thead>';
    echo '<th>Equipo</th><th>Posición</th><th>N&deg; Sensor</th><th>Desde</th><th>Hasta</th><th>Cantidad de Datos</th>';
    echo '</thead>';
    echo '<tbody>';
    foreach($tabla as $pos => $t)
    {
      echo '<tr>';
      echo '<td>'.(isset($t['equipo']) ? $t['equipo'] : '').'</td>';
      echo '<td>'.$nomenclatura[$t['posicion']].'</td>';
      echo '<td>'.(isset($t['sensor']) ? $t['sensor'] : '').'</td>';
      echo '<td>'.(isset($t['desde']) ? $t['desde'] : '').'</td>';
      echo '<td>'.(isset($t['hasta']) ? $t['hasta'] : '').'</td>';
      echo '<td>'.(isset($t['datos']) ? $t['datos'] : '').'</td>';      
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
  }else{
echo('<h3>No hay datos en para el neumático seleccionado</h3>');
}
?>
</div>
