<?php
  require 'autoload.php';

  $acc = new Acceso(true);
  
  // aqui se puede llamar directamente a la base de datos por cada codigo de equipo que se consulte
  $equipo = $_POST['equipo'];
  // date_default_timezone_set('america/santiago');

  @session_start();
  $sess_id = session_id();

  $db = DB::getInstance();
  #region obtener parámetros
   $p = new General();
   $GLOBALS['alarma_ambar'] = $p->getParamValue('alarma_ambar');
   $GLOBALS['mostrar_fecha_evento'] = $p->getParamValue('mostrar_fecha_evento');
   $GLOBALS['unidad_temperatura'] = $p->getParamValue('unidad_temperatura');
   $GLOBALS['unidad_presion'] = $p->getParamValue('unidad_presion');
   $nomenclatura = $p->getNomenclaturas();
   // print_r($GLOBALS);
  #end region

  $ee = new EstadoEquipo();
  $color = $ee->estatusPosiciones($equipo,'green');
  $colores = $ee->estatusPosiciones($equipo);
  $datos_eventos = $ee->datosEventos();
  $GLOBALS['datos_eventos'] = $datos_eventos;
?>
<script> 
 function guia() {  
    var i;
    for ( i = 1 ; i <= n ; i++ ) {
      valor = document.getElementById("t" + i).value;
      console.log(i);
      if(valor!= ''){
        if (valor.length != 4 || /^\s+$/.test(valor)) {
          alert('[ERROR] El código Sensor ' + valor + ' debe tener 4 cifras');    
          return false
        }
      }
    }
    console.log('155 guiaDespacho');  
    var datosneum="Internos:  ";
    document.getElementById('datoscambia').innerHTML = datosneum;
  }
</script>

<?php
  $db = new Equipo();
  $equipo = $db->listar_full($equipo);
  $equipo = ($equipo)?$equipo[0]:NULL;

  if($equipo!=NULL){
    $id_camion      = $equipo->ID_CAMION;
    $numneumaticos  = $equipo->NEUMATICOS;
    $neumaticos     = explode(',',$equipo->NEUMATICOS);
    $id_cajauman    = $equipo->ID_CAJAUMAN;
    $tipo           = $equipo->TIPO_EQUIPO;

    $n = explode(',',$numneumaticos);
    $numneumaticos = 0;
    foreach($n as $neum){ if($neum==1) $numneumaticos++; }
    $db = DB::getInstance();
    $sql = "SELECT * FROM uman_neumatico_camion WHERE ID_EQUIPO='$equipo->ID_CAMION'";
    $datos = $db->query($sql)->results();
    //  echo $numneumaticos.'##'.count($datos);
    if(count($datos)<=0){ echo '<h3>Equipo no tiene neumáticos asignados</h3>';exit(); }
    else
    {
      foreach($datos as $dnc){
        $neum = new Neumatico();
        $neum = $neum->get_full($dnc->ID_NEUMATICO);
        if(count($neum)==1){
          $neum = $neum[0];

          $plantilla = $db->query("SELECT PIF FROM uman_plantilla WHERE ID_PLANTILLA='$neum->ID_PLANTILLA'")->results();
          if(count($plantilla)>0){ $pif = $plantilla[0]->PIF; }
          else $pif = 1;
        
          $temp_k = $datos_eventos[$dnc->ID_POSICION]->eventotemperatura + 273.15;
          $ratio  = $temp_k / ( 18 + 273.15 );

          $sensor[$dnc->ID_POSICION]['COD']           = $neum->S_CODESENSOR;
          $sensor[$dnc->ID_POSICION]['TIPO']          = $neum->S_TIPO;
          $sensor[$dnc->ID_POSICION]['MARCA']         = $neum->MARCA;
          $sensor[$dnc->ID_POSICION]['NUMIDENTI']     = $neum->NUMIDENTI;
          $sensor[$dnc->ID_POSICION]['NUMEROFUEGO']   = $neum->NUMEROFUEGO;
          $sensor[$dnc->ID_POSICION]['MODELO']        = $neum->MODELO;
          $sensor[$dnc->ID_POSICION]['COMPUESTO']     = $neum->COMPUESTO;
          $sensor[$dnc->ID_POSICION]['REC']           = round( $pif * $ratio );
        }//end if(count($neum)==1)
      }//end foreach($datos as $dnc)
    }

    $ruedas = array_fill(0, 16, "");

    // $j = 1;
    // for ( $i = 1 ; $i <= 16 ; $i++ ) {
    //   if ( $neumaticos[$i-1] == "1" ) {
    //     $ruedas[$i] = Core::dibuja_caja($j, $sensor[$j]['TIPO'], $datos_eventos[$j]['eventotemperatura'], $datos_eventos[$j]['eventopresion'], $recomendada[$j], $color[$j], $datos_eventos[$j][0]);
    //     $j++;
    //   }
    // }

    $obj_eqp = new Equipo();
    $arr_this = $obj_eqp->listar_this($equipo->ID_CAMION);

    if(count($arr_this) > 0)
    {
      foreach ($arr_this as $rd) 
      {
        $rueda_real[$rd->ID_NEUMATICO]['NC']          = $rd->ID;            //12 - ID ROW (Relacion Neumatico Camion)
        $rueda_real[$rd->ID_NEUMATICO]['ID']          = $rd->ID_NEUMATICO;  //17
        $rueda_real[$rd->ID_NEUMATICO]['CODE']        = $rd->NUMIDENTI;     //S0L003798
        $rueda_real[$rd->ID_NEUMATICO]['NUMEROFUEGO'] = $rd->NUMEROFUEGO;
        $rueda_real[$rd->ID_NEUMATICO]['BRAND']       = $rd->MARCA;         //Bridgestone
        $rueda_real[$rd->ID_NEUMATICO]['MODEL']       = $rd->MODELO;        //42/90 R57
        $rueda_real[$rd->ID_NEUMATICO]['COMPO']       = $rd->COMPUESTO;
        $rueda_real[$rd->ID_NEUMATICO]['TEMP']        = $rd->TEMPMAX;       //80
        $rueda_real[$rd->ID_NEUMATICO]['MAX']         = $rd->PRESMAX;       //135
        $rueda_real[$rd->ID_NEUMATICO]['MIN']         = $rd->PRESMIN;       //103
        $rueda_real[$rd->ID_NEUMATICO]['TPL']         = $rd->ID_PLANTILLA;  //28
        $rueda_real[$rd->ID_NEUMATICO]['SENSOR']      = $rd->CODSENSOR;  //28
        $rueda_real[$rd->ID_NEUMATICO]['ID_SENSOR']   = $rd->ID_SENSOR;
        $rueda_real[$rd->ID_NEUMATICO]['POSICION']    = $rd->ID_POSICION;
        $rueda_real[$rd->ID_NEUMATICO]['TIPO_SENSOR'] = $rd->TIPO_SENSOR;

        $rueda[$rd->ID_POSICION] = $rd->ID_NEUMATICO;
      }
    }
?>
<div class="row">
  <div class="<?php Core::absolute_col_x(12) ?>">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <img class="modal-title-i icono-x48 <?php echo $equipo->CLASS_IMG ?> pull-left" alt="">
        <div class="modal-title-1">EQUIPO <?php echo $equipo->NUMCAMION; ?></div>
        <div class="modal-title-2">Faena <?php print $faena = $_SESSION[$sess_id]['faena']; ?></div>
      </div>
      <div class="panel-body">
        <div class="row">
          <!-- DETALLE DE CAJA -->
          <div class="<?php Core::col(3,3,12,12) ?> ">
            <?php
              $caja = $db->query("SELECT * FROM uman_cajauman WHERE ID_CAJAUMAN='$equipo->ID_CAJAUMAN'");
              // echo '#####'.$caja->count();
              if($caja->count()==1){
                $caja = $caja->results()[0];
                $sim = $db->query("SELECT * FROM uman_sim WHERE ID='$caja->ID_SIM'");
                $sim = ($sim->count()>0) ? $sim->results()[0]: NULL;

                $caja = array(
                'id_caja'=>$caja->CODIGOCAJA,
                'ip'=>$caja->IPUMAN,
                'transmision'=>$caja->TRANSMISION,
                'sim'=>$sim->TELEFONO,
                'compania'=>$sim->COMPANIA,
                );
              }
              else $caja = array('id_caja'=>'','ip'=>'','transmision'=>'','sim'=>'','compania'=>'');
              // print_r($caja);
            ?>
            <div class="panel panel-default">
              <div class="panel-heading"><h4>Detalles de Conexión</h4></div>
              <div class="panel-body detalle-conexion">
                <div class="input-group">
                  <div class="text-right">ID CAJA : </div>
                  <div class="text-left"> <?=$caja['id_caja']?></div>
                </div>

                <div class="input-group">
                  <div class="text-right">IP :</div>
                  <div class="text-left"> <?=$caja['ip']?></div>
                </div>

                <div class="input-group">
                  <div class="text-right">Transmisión : </div>
                  <div class="text-left"> <?=$caja['transmision']?></div>
                </div>

                <div class="input-group">
                  <div class="text-right">SIM : </div>
                  <div class="text-left"> <?=$caja['sim']?></div>
                </div>

                <div class="input-group">
                  <div class="text-right">Compañía : </div>
                  <div class="text-left"> <?=$caja['compania']?></div>
                </div>
              </div>
            </div>
          </div>

          <!-- DETALLE EQUIPO GRANDE -->
          <div class="<?= Core::col(8,8,12,12) ?> <?= Core::offset(1,1,1) ?> hidden-xs" id="esquema_equipo_grande">
            <?php
              $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
              $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
              $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
              $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

              $numneumaticos = $eje1 + $eje2 + $eje3 + $eje4;

              $attr = array();
              if($eje1==2){
                $attr['neum'][1] = 'left: calc( 50% - 108px) !important;';
                $attr['neum'][2] = 'left: calc( 50% + 61px) !important;';
              }else{
                $attr['neum'][1] = 'left: calc( 50% - 134px) !important;';
                $attr['neum'][2] = 'left: calc( 50% - 87px) !important;';
                $attr['neum'][3] = 'left: calc( 50% + 44px) !important;';
                $attr['neum'][4] = 'left: calc( 50% + 90px) !important;';
              }            
              if($eje2==2){
                $attr['neum'][$eje1+1] = 'left: calc( 50% - 108px) !important;';
                $attr['neum'][$eje1+2] = 'left: calc( 50% + 61px) !important;';
              }else{
                $attr['neum'][$eje1+1] = 'left: calc( 50% - 134px) !important;';
                $attr['neum'][$eje1+2] = 'left: calc( 50% - 87px) !important;';
                $attr['neum'][$eje1+3] = 'left: calc( 50% + 44px) !important;';
                $attr['neum'][$eje1+4] = 'left: calc( 50% + 90px) !important;';
              }

              $attr['container']['style'] = 'min-width: 193px; max-width:193px; position: relative;';
              $attr['showPopover'] = false;
              $attr['neum-drag-n-drop'] = false;
              $attr['showNomenclatura'] = true;
              $attr['nomenclatura']['class'] = 'top';

              $colores    = array_fill(1,6,array('color'=>'none'));
              $ruedas     = array_fill(1,6,'');
              if($rueda_real)
              {
                foreach($rueda_real as $neu){
                  $placement = 'top';
                  $style = '';
                  if($eje1 == 2)
                  {
                    if($neu['POSICION'] == 1){ $placement = 'left'; $style = ' width: 165px; left: -166px; top: -20px;'; }
                    if($neu['POSICION'] == 2){ $placement = 'right'; $style = ' width: 165px; left: 45px; top: -20px;'; }
                  }
                  else
                  {
                    if($neu['POSICION'] == 1){ $placement = 'left'; $style = ' width: 165px; left: -166px;'; }
                    if($neu['POSICION'] == 2){ $placement = 'bottom'; $style = ' width: 165px; left: -166px;'; }
                    if($neu['POSICION'] == 3){ $placement = 'bottom'; $style = ' width: 165px; left: -166px;'; }
                    if($neu['POSICION'] == 4){ $placement = 'right'; $style = ' width: 165px; left: 45px;'; }
                  }

                  if($eje2 == 2)
                  {
                    if($neu['POSICION'] == $eje1+1){ $placement = 'left'; $style = ' width: 165px; left: -166px;'; }
                    if($neu['POSICION'] == $eje1+2){ $placement = 'right'; $style = ' width: 165px; left: -166px;'; }
                  }
                  else
                  {
                    if($neu['POSICION'] == $eje1+1){ $placement = 'left'; $style = ' width: 165px; left: -166px; top: -40px;'; }
                    if($neu['POSICION'] == $eje1+2){ $placement = 'bottom'; $style = ' width: 165px; left: -76px; top: 110px;'; }
                    if($neu['POSICION'] == $eje1+3){ $placement = 'bottom'; $style = ' width: 165px; left: -44px; top: 110px;'; }
                    if($neu['POSICION'] == $eje1+4){ $placement = 'right'; $style = ' width: 165px; left: 45px; top: -40px;'; }
                  }
                  
                  $n = array('id'=>$neu['ID'], 'num'=>$neu['CODE'], 'marca'=>$neu['BRAND'], 'pos'=>$neu['POSICION'], 
                    'compuesto'=>$neu['COMPO'], 'numfuego'=>$neu['NUMEROFUEGO'],
                    'tooltip'=>array('placement'=>$placement, 'type'=>'box', 'style'=>$style),
                    'showBrand'=>false);
                  $s = array('id'=>$neu['ID_SENSOR'], 'num'=>$neu['SENSOR'], 'tipo'=>$neu['TIPO_SENSOR'], 'style'=>'top:25px;');
                  $ruedas[$neu['POSICION']] = Core::neumatico_arrastrable($n,$s,'usado');
                }
              }

              echo Core::dibuja_esquema_equipo_DND($neumaticos, $colores, $ruedas, $attr);            
              // echo Core::dibuja_posiciones($numneumaticos,$attr2);
            ?>
          </div>

          <?php
            $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
            $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
            $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
            $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

            $numneumaticos = $eje1 + $eje2 + $eje3 + $eje4;
            $inc = (($numneumaticos+3)*152);
          ?>

          <!-- DETALLE EQUIPO CHICO -->
          <div class="<?php Core::col(12) ?> visible-xs" style="min-height: <?php echo intval($inc) ?>px;">
            <?php
              $attr = array();
              $inc = intval($inc / $numneumaticos);

              $colores    = array_fill(1,6,array('color'=>'none'));
              $ruedas     = array_fill(1,6,'');
              if($rueda_real)
              {
                $top = 0;
                $class = Core::absolute_col_x(12);
                foreach($rueda_real as $neu){
                  $placement = 'bottom';
                  $style = "position: relative !important;";
                  
                  //$neu['POSICION']
                  $data_po = '<div style="font-size: 80%; margin: -12px -1px 3px -1px;">
                    <div style="margin: -10px auto 0 0;"><h4 style="background-color: #3b3b3b; color:oldlace; border-radius: 6px 6px 0 0;">
                      <div class="posicion"><div>'.$nomenclatura[$neu['POSICION']].'</div></div>                    
                      <center>NEUMÁTICO</center></h4>
                    </div>
                    <div class="bg-primary" style="padding: 5px 10px 0 10px; margin: -10px auto 0 0;">      
                      <span>N&deg; SERIE: '.$neu['CODE'].'</span><br />
                      <span>N&deg; FUEGO: '.$neu['NUMEROFUEGO'].'</span><br />
                      <span>MARCA: '.$neu['BRAND'].'</span><br />
                      <span>COMPUESTO: '.$neu['COMPO'].'</span>
                    </div>
                    <div style="margin: -10px auto 0 0;"><h4 style="background-color: #3b3b3b; color:oldlace"><center>SENSOR</center></h4></div>
                    <div style="padding: 5px 10px 0 10px; margin: -10px auto 0 0;">
                      <span>CÓDIGO: '.$neu['SENSOR'].'</span><br />
                      <span>TIPO: '.$neu['TIPO_SENSOR'].'</span>
                    </div>
                    </div>';
                
                  echo Core::crearPopover('bottom', $data_po, $class, $style, 'show');
                  $top += $inc;
                }
              }
            ?>
          </div>
        </div>
      </div>      
    </div>
  </div>
</div>
<?php } ?>