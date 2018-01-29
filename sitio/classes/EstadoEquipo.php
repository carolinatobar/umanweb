<?php

class EstadoEquipo{
 private $datos_eventos = array();

 function estatusPosiciones($idequipo,$defaultClass='black',&$isTimeout=false) {
  // date_default_timezone_set("Chile/Continental");
  $colores = array_fill(0, 16, array('color'=>'none', 'dato'=>'', 'fecha'=>''));
  $db = DB::getInstance();

  $gen = new General();
  $timeout = $gen->getParamvalue('timeout');

  #region obtener posiciones establecidas
   $sql = "SELECT ID_POSICION, up.PRE_ALARMA, un.ID_NEUMATICO, up.TEMPMAX, up.PRESMAX, up.PRESMIN    
    FROM uman_neumatico_camion unc INNER JOIN uman_neumaticos un ON unc.ID_NEUMATICO=un.ID_NEUMATICO INNER JOIN uman_plantilla up ON up.ID_PLANTILLA=un.ID_PLANTILLA  
    WHERE ID_EQUIPO=$idequipo 
    ORDER BY ID_POSICION ASC";
    // echo $sql;
   $pos = $db->query($sql);

   $neum_seteado   = array();
   $neum_prealarma = array();
   $umbrales       = array();
   if($pos->count()>0){
      foreach($pos->results() as $p){ 
        $neum_seteado[$p->ID_POSICION]   = 1; 
        $neum_prealarma[$p->ID_POSICION] = $p->PRE_ALARMA;
        $umbrales[$p->ID_POSICION]       = array(
          'tempmax'=>$p->TEMPMAX,
          'presmax'=>$p->PRESMAX,
          'presmin'=>$p->PRESMIN,
        );
      }
   }
   // print_r($neum_seteado);
  #end region

  $sql = "SELECT 
   posicion, UNIX_TIMESTAMP(fecha_evento) as fecha_evento, eventopresion, eventotemperatura, eventobateria, 
   tempmax, presmax, presmin, DATE_FORMAT(fecha_evento,'%d/%m/%Y %H:%i') as fecha_evento2 
   FROM uman_ultimoevento 
   WHERE numequipo=$idequipo  
   ORDER BY posicion ASC";
  //  echo $sql;
  $pos = $db->query($sql);
  // print_r($pos->results());
  $isTimeoutSum = 0;
  foreach( $pos->results() as $posx ){
   $this->datos_eventos[$posx->posicion] = array(
      'eventopresion'=>$posx->eventopresion,
      'eventotemperatura'=>$posx->eventotemperatura,
      'eventobateria'=>$posx->eventobateria,
      'tempmax'=>$umbrales[$posx->posicion]['tempmax'],
      'presmax'=>$umbrales[$posx->posicion]['presmax'],
      'presmin'=>$umbrales[$posx->posicion]['presmin'],
      'fecha_evento'=>$posx->fecha_evento,
      'fecha_evento2'=>$posx->fecha_evento2
   );
   $hora_actual 	 = time()-$timeout*60;
   
   $ultimo_evento  = $posx->fecha_evento;

   $evento_presion = $posx->eventopresion;
   $evento_temp 	 = $posx->eventotemperatura;
   $u_tempmax 		 = $posx->tempmax;
   $u_pres_max 		 = $posx->presmax;
   $u_pres_min 		 = $posx->presmin;

   $lrm            =  '';
   $fecha          = date("d/m/Y H:i:s",$ultimo_evento);
  //  echo date("d/m/Y H:i:s",$hora_actual).'/////'.date("d/m/Y H:i:s",$ultimo_evento).'<br/>';
   $nto = false; //not timeout
    if( $hora_actual <= $ultimo_evento ) {
      $val = $defaultClass;
      
      if( $GLOBALS['pre_alarma'] ) {
        $rango = $neum_prealarma[$posx->posicion];
        if( ($evento_temp >= $rango) && ($evento_temp < $u_tempmax) ) { 
          $val = "lilac"; $lrm = "MÁXIMO: $u_tempmax &deg;C</br>ACTUAL: $evento_temp &deg;C"; 
        }
      }
      //Verificar que el último evento no se encuentre fuera de umbrales.
      if( $evento_presion >= $u_pres_max ) { 
        $val = "orange"; $lrm = "MÁXIMO: $u_pres_max PSI<br/>ACTUAL: $evento_presion PSI"; 
      }
      if( $evento_presion <= $u_pres_min ) { 
        $val = "yellow"; $lrm = "MÍNIMO: $u_pres_min PSI<br/>ACTUAL: $evento_presion PSI"; 
      }
      
      if( $evento_temp >= $u_tempmax ) { 
        $val = "red"; $lrm = "MÁXIMO: $u_tempmax &deg;C<br/>ACTUAL: $evento_temp &deg;C"; 
      }
      $fecha = '';
      $nto = true;
    } 
    else { $val = "gray"; $isTimeoutSum++; }

   // echo $posx[0].'=>'.$val.'<br/>';
   if (isset($neum_seteado[$posx->posicion])) {
     $colores[$posx->posicion] = array('color'=>$val, 'dato'=>$lrm, 'fecha'=>$fecha);
   }

   $fecha_evento[$posx->posicion]	 = $ultimo_evento;
  }

  $isTimeout = ($isTimeoutSum == $pos->count());
  
   // print_r($colores);
  return $colores;
 }

 public function datosEventos(){
   return $this->datos_eventos;
 }
}