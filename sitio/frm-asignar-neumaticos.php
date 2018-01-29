<?php
 require 'autoload.php';

 // session_start();
 $acceso = new Acceso($_SESSION, session_id());
 if(!$acceso->Permitido()) exit();

 $gen = new General();
 $img_equipo   = $gen->getImagenesEquipo();

 $f = new Flota();
 $f = $f->listar();
 $flota = array();
 foreach($f as $fx){ $flota[$fx->NUMFLOTAS] = $fx->NOMBRE; }

 $e = new Equipo();
 $option = array(); 
 $equipos = $e->listar();
 foreach($equipos as $e){
  $option[$e->NUMFLOTA][] = '<option 
    data-tokens="'.$e->NUMCAMION.'" 
    data-content="'.str_replace('"','\'',$img_equipo[$e->ID_CAMION]['DIV']).'&nbsp;&nbsp;&nbsp;'.$e->NUMCAMION.'" 
    value="'.$e->ID_CAMION.'">'.$e->NUMCAMION.'</option>';
 }

 $TITULO    = $module_label; //'Asignar neumáticos';
 $SUBTITULO = '';
?>
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>
  <?php include_once("assets/css/drag-n-drop.css") ?>
</style>
<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">
  <!-- TÍTULO DE PÁGINA -->
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <!-- MENÚ DE PÁGINA -->
  <div class="filtro-contenido">
    <div class="<?=Core::col(7,7,null,null)?>"></div>
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Equipos&nbsp;</label>
        <select id="equipo" name="equipo" data-style="btn-default" class="selectpicker" data-live-search="true">
        <?php
          foreach($flota as $i => $f){
          echo('<optgroup label="'.$f.'">');
          foreach($option[$i] as $o){ echo($o); }
          echo('</optgroup>');
          }
        ?>
        </select>
      </div>
    </div>
    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" id="btn-ver">Ver</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <div id="contenedor-datos" class="container-fluid"></div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modalFechaRetiro" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger">
        <h4 class="modal-title">Fecha de Retiro</h4>
      </div>
      <div class="modal-body">
        Por favor ingrese la fecha real del retiro del neumático<br/>
        <input type="text" class="rangepicker form-control" name="fechaRetiro" id="fechaRetiro" value="<?=date("d/m/Y H:i")?>">
      </div>
      <div class="modal-footer">
       <!-- 10_01_2018 CT - Al retirar neumáticos no se puede cancelar el proceso en tabla fecha de retiro -->
	<!-- Se agrega botón para cancelar acción-->
        <button type="button" class="btn btn-info" id="cancelar">Cancelar</button>
        <button type="button" class="btn btn-primary" id="asignarFechaRetiro">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modalFechaInstalacion" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h4 class="modal-title">Fecha de Instalación</h4>
      </div>
      <div class="modal-body">
        Por favor ingrese la fecha real de la instalación del neumático<br/>
        <input type="text" class="rangepicker form-control" name="fechaInstalacion" id="fechaInstalacion" value="<?=date("d/m/Y H:i")?>">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="asignarFechaInstalacion">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  var acciones = {rem:[], put:[]};
  var originales = [];
  $(document).ready(function(){
    $("#btn-ver").on('click', function(){
      var equipo = $("#equipo").selectpicker('val');
      $.post('data-asignar-neumaticos.php',{equipo:equipo}, function(data){
        $("#contenedor-datos").html(data);
        acciones = {rem:[], put:[]};
      });
    });

    $('.rangepicker').daterangepicker({
      singleDatePicker: true,
      showDropdowns: true,
      autoApply: true,
      startDate: '<?=date("d/m/Y H:i")?>',
      timePicker: true,
      timePicker24Hour: true,
      maxDate: new Date,
      locale: {
        "format": "DD/MM/YYYY H:mm",
        "separator": " - ",
        "applyLabel": "Aplicar",
        "cancelLabel": "Cancelar",
        "fromLabel": "Desde",
        "toLabel": "Hasta",
        "customRangeLabel": "Custom",
        "weekLabel": "S",
        "daysOfWeek": [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
        "monthNames": [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        "firstDay": 1
      }
    });

    $("#asignarFechaRetiro").on("click", function(){
      let idneumatico = $("#modalFechaRetiro").attr("data-idneumatico");
      for(i=0; i<acciones.rem.length; i++){
        if(acciones.rem[i].id==idneumatico){
          acciones.rem[i].fecha = moment($("#fechaRetiro").val(), "DD/MM/YYYY H:mm").format("YYYY-MM-DD H:mm");
          $("#modalFechaRetiro").modal("hide");
        }
      }
    });
// 17_01_2018 CT - Al retirar neumáticos no se puede cancelar el proceso en tabla fecha de retiro
// Se agrega acción del botón cancelar		
      $("#cancelar").on("click", function(){
          var equipo = $("#equipo").selectpicker('val');
          $.post('data-asignar-neumaticos.php',{equipo:equipo}, function(data){
              $("#contenedor-datos").html(data);
              acciones = {rem:[], put:[]};
              $("#modalFechaRetiro").modal("hide");
          });
      });
    
    
    $("#asignarFechaInstalacion").on("click", function(){
      let idneumatico = $("#modalFechaInstalacion").attr("data-idneumatico");
      for(i=0; i<acciones.put.length; i++){
        if(acciones.put[i].id==idneumatico){
          acciones.put[i].fecha = moment($("#fechaInstalacion").val(), "DD/MM/YYYY H:mm").format("YYYY-MM-DD H:mm");
          $("#modalFechaInstalacion").modal("hide");
        }
      }
    });
  });

  var dragSrcNeum = null;

  function allowDrop(ev){ ev.preventDefault(); }

  function drag(ev) {
    // this.style.opacity = '0.4';
    dragSrcNeum = $("#"+ev.target.id);
    
    ev.dataTransfer.effectAllowed = 'move';
    ev.dataTransfer.setData("text", ev.target.id);
  }

  function dragEnd(ev){
    
  }

  function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    if(ev.currentTarget.id == 'contenedor-neumaticos'){ /* DESDE ESQUEMA A CONTENEDOR */
      //almacenar contanedor de procedencia por si es necesario devolverlo
      dragSrcNeum.removeClass('usado').addClass('libre');
      dragSrcNeum.find('header').removeClass('hidden');
      dragSrcNeum.find('footer').removeClass('hidden');
      dragSrcNeum.find('sensor').removeClass('hidden');
      ev.target.appendChild(document.getElementById(data));
      //Liberar neumático
      var neumatico = dragSrcNeum[0].id.replace('neum-','');
      var quitar = false;
      $.each(originales[0], function(i,o){
        if(o == neumatico){ quitar = true; }
      });
      if(quitar){
        acciones.rem.push({id: neumatico, fecha: ''});
        dragSrcNeum.data('posicion','');
        //Mostrar popup
        $("#modalFechaRetiro").attr("data-idneumatico", neumatico);
        $("#modalFechaRetiro").modal("show");        
      } 
    }
    else{ /* DESDE CONTENEDOR A ESQUEMA */          
      if(ev.currentTarget.children.length==1){
        if(ev.currentTarget.children[0].className.indexOf('lblpos')==0){          
          var pos = ev.target.id;
          pos = pos.replace('pos','');
          dragSrcNeum.data('posicion',pos);

          var equipo    = $("#hid_truck_id").val();
          var posicion  = dragSrcNeum.data('posicion');
          var neumatico = dragSrcNeum[0].id.replace('neum-','');
          var eje       = $(ev.target).data('eje');
          
          $.post('ajax/plantillas/crud.php', {pos:posicion, neu:neumatico, ac:'obtener-plantilla'}, function(json){
            if(json){
              if(json.type == 'success'){
                if(json.data){
                  if(json.data.id){
                    dragSrcNeum.removeClass('libre').addClass('usado');
                    ev.target.appendChild(document.getElementById(data));
                    if(acciones.put.length > 0){
                      var existe = false;
                      for(i=0; i<acciones.put.length; i++){
                        if(acciones.put[i].posicion == posicion){
                          acciones.put[i] = {posicion: posicion, id: neumatico, eje: eje, fecha: ''};
                          existe = true;
                        }
                      }

                      if(!existe){
                        acciones.put.push({posicion: posicion, id: neumatico, eje: eje, fecha: ''});
                        $("#modalFechaInstalacion").attr("data-idneumatico", neumatico);
                        $("#modalFechaInstalacion").modal("show"); 
                      }
                    }
                    else{
                      acciones.put.push({posicion: posicion, id: neumatico, eje: eje, fecha: ''});
                      $("#modalFechaInstalacion").attr("data-idneumatico", neumatico);
                      $("#modalFechaInstalacion").modal("show"); 
                    }
                  }
                }
              }
              else{
                swal(json);
              }
            }
          });
        }
        else{
          if(ev.currentTarget.id.indexOf("pos")==-1) $("#"+data).removeClass('usado').addClass('libre');
          swal('Error','Primero debe quitar el neumático de la posición y luego colocar el que desee.','warning');
        }
      }
      else{
        if(ev.currentTarget.id.indexOf("pos")==-1) $("#"+data).removeClass('usado').addClass('libre');
        swal('Error','Primero debe quitar el neumático de la posición y luego colocar el que desee.','warning');
      }
    }
  }
</script>
