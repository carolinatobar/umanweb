<?php
  require 'autoload.php';
  
  // session_start();
  $acc = new Acceso(true);

  $nombrefaena = $_SESSION[session_id()]['faena'];  

  $gen = new General();
  $nomenclaturas = $gen->getNomenclaturas();
  $verNeumaticoPor = $gen->getParamValue('verneumaticosegun');

  $get_eqp = (isset($_POST['equipo']))?$_POST['equipo']:null;
  if(!is_numeric($get_eqp)){
    print('Valor no permitido: '.$get_eqp);
    exit();
  }

  $obj_neu  = new Neumatico();
  $arr_neu  = $obj_neu->listar_con_sensor_y_disponibles();

  $obj_eqp  = new Equipo();
  $dat_eqp  = $obj_eqp->listar_full($get_eqp); //data completa del equipo en consulta
  // print_r($dat_eqp);
  $nombre_equipo  = $dat_eqp[0]->NUMCAMION;
  $ruedas_equipo  = $dat_eqp[0]->NUMNEUMATICOS;
  $img_camion     = $dat_eqp[0]->CLASS_IMG;
  $img_esquema    = $dat_eqp[0]->IMG_ESQUEMA;

  // print_r($dat_eqp[0]);

  $act_eqp  = $obj_eqp->actualizarEstadoUmanBlue($get_eqp);

  // $ruedas_equipo  = '';
  $rueda  = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0);
  $rueda_name = array();

  if($get_eqp)
  {
    $arr_this = $obj_eqp->listar_this($get_eqp);
    // $nombre_equipo  = $arr_this[0]->NUMCAMION;
    // $ruedas_equipo  = $arr_this[0]->NUMNEUMATICOS;

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
        $rueda_real[$rd->ID_NEUMATICO]['COMPUESTO']   = $rd->COMPUESTO;
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
  } 
  else 
  {
    $arr_this = array();
  }

  // print_r($rueda_real);
?>

<input type="hidden" id="hid_truck_id" placeholder="Equipo" value="<?=$get_eqp;?>">
<input type="hidden" id="hid_wheel_pos" placeholder="Posicion">
<div class="panel panel-primary center-block">
  <div class="panel-heading">
    <div class="modal-title-i icono-x48 <?php echo $img_camion ?> pull-left"></div>
    <div class="modal-title-1">EQUIPO <strong><?php echo $nombre_equipo ?></strong></div>
    <div class="modal-title-2">Faena <?php print $nombrefaena ?></div>
  </div>
  <div class="panel-body-aside">
    <div class="row" style="padding: 30px 0px 20px 0px;">
      
      <!-- ESQUEMA -->
      <div class="<?php Core::col(5,5,12,12) ?>" style="max-height: 361px; max-width:480px; min-width: 480px;">
        <?php
          $neumaticos = explode(',',$dat_eqp[0]->NEUMATICOS);
          $eje1 = $neumaticos[0] + $neumaticos[1] + $neumaticos[2] + $neumaticos[3];
          $eje2 = $neumaticos[4] + $neumaticos[5] + $neumaticos[6] + $neumaticos[7];
          $eje3 = $neumaticos[8] + $neumaticos[9] + $neumaticos[10] + $neumaticos[11];
          $eje4 = $neumaticos[12] + $neumaticos[13] + $neumaticos[14] + $neumaticos[15];

          $attr = array();
          if($eje1==2){
            $attr['neum'][1] = 'left: 132px !important;';
            $attr['neum'][2] = 'left: 299px !important;';
          }else{
            $attr['neum'][1] = 'left: 105px !important;';
            $attr['neum'][2] = 'left: 152px !important;';
            $attr['neum'][3] = 'left: 281px !important;';
            $attr['neum'][4] = 'left: 328px !important;';
          }

          if($eje2==2){
            $attr['neum'][$eje1+1] = 'left: 132px !important;';
            $attr['neum'][$eje1+2] = 'left: 299px !important;';
          }else{
            $attr['neum'][$eje1+1] = 'left: 105px !important;';
            $attr['neum'][$eje1+2] = 'left: 152px !important;';
            $attr['neum'][$eje1+3] = 'left: 281px !important;';
            $attr['neum'][$eje1+4] = 'left: 328px !important;';
          }
          $attr['container']['style'] = 'min-width: 480px; max-width:480px;' ;
          $attr['showPopover'] = false;
          $attr['neum-drag-n-drop'] = true;
          $attr['showNomenclatura'] = true;
          $attr['nomenclatura']['class'] = 'top';
          // print_r($neumaticos);
          $colores    = array_fill(1,6,array('color'=>'none'));
          $ruedas     = array_fill(1,6,'');
          // print_r($rueda_real);
          $originales = array();
          $tooltip = array('type'=>'tooltip', 'placement'=>'bottom');
          if($rueda_real)
          {
            foreach($rueda_real as $neu){
              $n = array('id'=>$neu['ID'], 'num'=>$neu['CODE'], 'marca'=>$neu['BRAND'], 'pos'=>$neu['POSICION'], 
                'compuesto'=>$neu['COMPUESTO'], 'numfuego'=>$neu['NUMEROFUEGO'], 'style'=>'margin-top: 0px !important;',
                );
              $s = array('id'=>$neu['ID_SENSOR'], 'num'=>$neu['SENSOR'], 'tipo'=>$neu['TIPO_SENSOR']);
              $ruedas[$neu['POSICION']] = Core::neumatico_arrastrable($n,$s,'usado');
              $originales[] = $neu['ID'];
            }
          }
          
          echo Core::dibuja_esquema_equipo_DND($neumaticos, $colores, $ruedas, $attr);
        ?>            
      </div>

      <!-- SEPARADOR -->
      <div class="<?php Core::col(12) ?> hidden-lg hidden-md"><p>&nbsp;<br/></p></div>
      
      <!-- CONTENEDOR NEUMÁTICOS DISPONIBLES -->
      <div class="<?php Core::col(6,6,12,12) ?>">
        <div class="row">
          <div class="col-xs-10 col-sm-10 text-left">
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon3">Filtrar <span class='hidden-xs'>Neumaticos</span></span>
              <input type="text" class="form-control" id="tire-filter" aria-describedby="basic-addon3">
            </div>
          </div>
          
          <div id="contenedor-neumaticos" class="<?php Core::col(11) ?>" 
          ondrop="drop(event)" ondragover="allowDrop(event)"  
          style="height: 361px; background-color:rgba(227,242,253,0.5); overflow-y:auto">
            <?php
              if(isset($arr_neu) && count($arr_neu) > 0){
                foreach ($arr_neu as $neu) {
                  $n = array('id'=>$neu->ID_NEUMATICO, 'num'=>($verNeumaticoPor=='fuego'?$neu->NUMEROFUEGO:$neu->NUMIDENTI), 'marca'=>$neu->MARCA, 'path'=>'.');
                  $s = array('id'=>$neu->ID_SENSOR, 'num'=>$neu->CODSENSOR, 'tipo'=>$neu->TIPO);
                  Core::neumatico_arrastrable($n,$s,'libre CS', true);
                }
              }
            ?>
          </div>
        </div>
      </div>
    
      <!-- BOTÓN GUARDAR -->
      <div class="<?php Core::col(12) ?>">
        <button class="btn btn-primary center-block" id="guardar_neumaticos">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Modal Ver Neumático -->

<div id="modal-view-wheel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <input type="hidden" id="hid_wheel_del" value="">

      <div class="modal-header">
        Control Neumático
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid" style="margin-bottom: 10px;">
          <div class="row">
            <div class="col-xs-3" style="height: 50px;">
              <img src="assets/img/tire.png" alt="" width="40">
            </div>
            <div class="col-xs-9" style="height: 50px;"><img id='img-tire-brand' src="" style="width: 100%;"></div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-condensed">
            <tr class='white'>
              <td>CODIGO</td>
              <td><span id="td-tire-code"></span></td>
            </tr>
            <tr class='white'>
              <td>MARCA</td>
              <td><span id="td-tire-brand"></span></td>
            </tr>
            <tr class='white'>
              <td>MODELO</td>
              <td><span id="td-tire-model"></span></td>
            </tr>
            <tr class='white'>
              <td>TEMP. MAX.</td>
              <td><span id="td-tire-temp"></span></td>
            </tr>
            <tr class='white'>
              <td>PRES. MAX.</td>
              <td><span id="td-tire-max"></span></td>
            </tr>
            <tr class='white'>
              <td>PRES. MIN.</td>
              <td><span id="td-tire-min"></span></td>
            </tr>
            <tr class='white'>
              <td>SENSOR</td>
              <td><span id="td-tire-sensor"></span></td>
            </tr>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button id="btn_quitar_rueda" type="button" class="btn btn-danger btn-sm pull-left">Retirar Neumático</button>
        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  
  $(document).ready(function(){
    originales = [<?= json_encode($originales) ?>];
    var nomenclaturas = <?= json_encode($nomenclaturas) ?>;
    
    $("#guardar_neumaticos").click(function(){
      console.log(acciones);
      var txt_success = '', txt_errors = '';

      if(acciones.put.length > 0 || acciones.rem.length > 0){
        acciones.equipo = <?= $get_eqp ?>;
        swal({
          title: "¿Está seguro(a) de aplicar las modificaciones?",
          text: "Si decide continuar, se aplicarán todos los cambios realizados.",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Si, continuar",
          cancelButtonText: "No, cancelar",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
          if (isConfirm) {
            $.post('ajax/asignar-retirar-neumatico.php',acciones, function(data){
              console.log(data);
              swal(data);
              acciones = {equipo:'', rem: [], put: []};
            });
          }
        });        
      }
      else{
        swal('No hay cambios que guardar', 'Primero realice las operaciones necesarias (cambiar, quitar o asignar neumáticos) y luego presione guardar.','warning');
      }
    });
   
    // Modal - Filtro Neumaticos
    var list = $('#contenedor-neumaticos > div.neumatico');

    $('#tire-filter').keyup(function() {
      var this_val  = $.trim($(this).val()).toLowerCase();
        list.show().filter(function() {
            var text = $(this).attr("n-string").replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(this_val);
        }).hide();
    });
}); //end ready
</script>
