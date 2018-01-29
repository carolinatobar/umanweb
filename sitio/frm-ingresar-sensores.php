<?php
  require 'autoload.php';

  $acc = new Acceso();

  $TITULO    = $module_label; //'Ingresar Sensores';
  $SUBTITULO = '';
// 15_01_2018 CT - Se agrega instancia de BD
$db = DB::getInstance();
?>
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>

  #panel-guia-despacho{
    position: absolute;
    top: 15px;
    display: none;
    width: calc( 100% - 30px );
    height: calc( 100% - 30px );
    z-index: 10;
  }
  #panel-guia-despacho > div.panel-body{
    padding: 10px;
    font-family: monospace;
    overflow-y: auto;
    overflow-x: none;
    min-height: 190px;
  }
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
    <div class="<?=Core::col(10,10)?>"></div>
    <div class="<?=Core::col(2,2,12,12)?>">
      <div class="frm-group">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevoSensor" data-modo="agregar"><i class="fa fa-plus" aria-hidden="true"></i> Agregar</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table class="table table-hover" id="tabla-sensores">
      <thead>
        <tr>
          <th>&nbsp;</th>
          <th>Código</th>
          <th>Tipo</th>
          <th>Neumático</th>
          <th>Equipo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>&nbsp;</th>
          <th><input type="text" class="foot-filter" data-index="1" placeholder="&#xf0b0; Código" oninput="filtro(this)"></th>
          <th><input type="text" class="foot-filter" data-index="2" placeholder="&#xf0b0; Tipo" oninput="filtro(this)"></th>
          <th><input type="text" class="foot-filter" data-index="3" placeholder="&#xf0b0; Neumático" oninput="filtro(this)"></th>
          <th><input type="text" class="foot-filter" data-index="4" placeholder="&#xf0b0; Equipo" oninput="filtro(this)"></th>
          <th><input type="text" class="foot-filter" data-index="5" placeholder="&#xf0b0; Estado" oninput="filtro(this)"></th>
        </tr>
      </tfoot>
      <tbody></tbody>
    </table>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="nuevoSensor">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nuevo Sensor</h4>
      </div>
      <div class="modal-body" style="min-height: 250px;">
        <div class="row">
          <div class="<?=Core::col(8)?>">
            <form class="form" id="frm-adicionar">
              <input type="hidden" name="modo" id="modo" value="agregar">
              <div class="row">
                <div class="<?=Core::col(6)?>">
                  <div class="<?=Core::col(12)?>">Código</div>
                  <div class="<?=Core::col(12)?>">
		  <!-- 15_01_2018 CT - Se agrega un largo mínimo de 1 en el código -->
                    <input type="text" class="form-control" placeholder='<?php print $texto_sitio["Codigo Sensor"]; ?>' name="c[]" maxlength="4" minlength="1" onKeyPress="return permite(event, 'num_car')" onfocus="contar();" onkeyup = "this.value=this.value.toUpperCase()">
                  </div>    
                </div>

                <div class="<?=Core::col(6)?>">
                  <div class="input-group">
                    <div class="<?=Core::col(12)?>">Tipo</div>
                    <div class="<?=Core::col(12)?>">
                      <select class="selectpicker" name="t[]" data-width="100%" onfocus="contar();">
                        <option value="Interno">Interno</option>
                        <option value="Externo">Externo</option>
                      </select>
                    </div>
                  </div>    
                </div>
              </div>

            </form>
          </div>

          <div class="<?=Core::col(4)?>">
            <button class="btn btn-primary" id="adicionar" style="width:100%"><i class="fa fa-plus" aria-hidden="true"></i> Añadir</button>
            <button class="btn btn-info" id="guia-despacho" style="width:100%"><i class="fa fa-plus" aria-hidden="true"></i> Guía de Despacho</button>
          </div>
        </div>
        
        <!-- GUÍA DE DESPACHO -->
        <div class="panel panel-default" id="panel-guia-despacho">
          <div class="panel-heading">
            <h3 class="panel-title">Guía de Despacho</h3>
            <a href="#!" class="close pull-right close-panel" style="margin-top: -20px;"><span aria-hidden="true">&times;</span></a>
          </div>
          <div class="panel-body">
            <small>
              <label>Internos: <span id="csi"></span></label><br/>
              <label>Externos: <span id="cse"></span></label><br/>
              <!-- <label>Canister: <span id="csc"></span></label><br/> -->
              <label>Número de sensores internos: <span id="si"></span></label><br/>
              <label>Número de sensores externos: <span id="se"></span></label><br/>
              <!-- <label>Número de sensores canister: <span id="sc"></span></label><br/> -->
            </small>
          </div>
        </div>
        
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="modificarSensor">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modificar Sensor</h4>
      </div>
      <div class="modal-body">
        <form class="form" id="frm-modificar">
          
          <input type="hidden" name="modo" id="modo" value="editar">
          <input type="hidden" name="id" id="id" value="">
          <div class="row" style="padding-left: 15px; padding-right: 15px;">

            <div class="<?=Core::col(4)?>">
              <div class="<?=Core::col(12)?>">Código</div>
	      <!-- 15_01_2018 CT - Permite editar el código del sensor -->
              <div class="<?=Core::col(12)?>">
                  <input type="text" id="codigo" class="form-control" placeholder='<?php print $texto_sitio["Codigo Sensor"]; ?>' name="codigo" maxlength="4" onKeyPress="return permite(event, 'num_car')" onfocus="contar();" onkeyup = "this.value=this.value.toUpperCase()">
                  <!--<span id="codigo" class="form-control"></span>-->
              </div>
            </div>

            <div class="<?=Core::col(4)?>">
              <div class="input-group">
                <div class="<?=Core::col(12)?>">Tipo</div>
                <div class="<?=Core::col(12)?>">
                  <select class="selectpicker" name="tipo" id="tipo" data-width="100%">
                    <option value="Interno">Interno</option>
                    <option value="Externo">Externo</option>
                    <!-- <option value="Canister">Canister</option> -->
                  </select>
                </div>
              </div>    
            </div>

            <div class="<?=Core::col(4)?>">
              <div class="input-group">
                <div class="<?=Core::col(12)?>">Estado</div>
                <div class="<?=Core::col(12)?>">
                  <select class="selectpicker" name="estado" id="estado" data-width="100%">
                    <option value="DISPONIBLE">Disponible</option>
                    <option value="BAJA">Baja</option>
		    <!-- 15_01_2018 CT - Se agrega opcion USO -->
                    <option value="USO" class="row hidden">USO</option>
                  </select>
                </div>
              </div>
            </div>

          </div>

          <br/>

          <div class="row hidden" id="razon_baja" style="padding-left: 15px; padding-right: 15px;">
            <div class="<?=Core::col(6)?> <?=Core::offset(3)?>">
              <div class="input-group">
                <div class="<?=Core::col(12)?>">Motivo de Baja</div>
                <div class="<?=Core::col(12)?>">
                  <select class="selectpicker" id="razon" name="razon" data-width="100%">
                    <option value="1">Fin de vida útil</option>
                    <option value="2">Falla</option>
                    <option value="3">Desprendimiento</option>
                    <option value="4">Sin información</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar2">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  var ix = 2;
  $(function(){
    tabla = $("#tabla-sensores").DataTable({
      dom: 'Brtip',
      searching: true,
      ajax:{
        url: 'ajax/ingresar-sensores/sensores.php',
        type: 'POST',
      },
      columns:[
        { data: 'btn' },
        { data: 'codigo' },
        { data: 'tipo' },
        { data: 'neumatico' },
        { data: 'equipo' },
        { data: 'estado' },
      ],
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "<?=$TITULO?> - <?=date("d-m-Y")?>",
            customize: function(xlsx) {
              var sheet = xlsx.xl.worksheets['sheet1.xml'];
              var downrows = 3;
              var clRow = $('row', sheet);
              var msg;
              //update Row
              clRow.each(function() {
                var attr = $(this).attr('r');
                var ind = parseInt(attr);
                ind = ind + downrows;
                $(this).attr("r", ind);
              });

              // Update  row > c
              var total_rows = 0;
              $('row c ', sheet).each(function() {
                var attr = $(this).attr('r');
                var pre = attr.substring(0, 1);
                var ind = parseInt(attr.substring(1, attr.length));
                ind = ind + downrows;
                $(this).attr("r", pre + ind);
                total_rows = ind;
              });

              function Addrow(index, data) {

                msg = '<row r="' + index + '">';
                for (var i = 0; i < data.length; i++) {
                  var key = data[i].k;
                  var value = data[i].v;
                  var formula = data[i].f;
                  if(formula==undefined){
                    msg += '<c t="inlineStr" r="' + key + index + '">';
                    msg += '<is>';
                    msg += '<t>' + value + '</t>';
                    msg += '</is>';
                    msg += '</c>';
                  }
                  else{
                    msg += '<c r="' + key + index + '" s="3" t="str">';
                    msg += '<f>'+formula+'</f>';
                    msg += '<v></v>';
                    msg += '</c>';
                  }
                }
                msg += '</row>';
                return msg;
              }

              //título
              var ti = Addrow(1, [{ k: 'A', v: '<?=$TITULO?> - <?=date("d-m-Y")?>' }]);
              var r1 = Addrow(2, [{ k: 'A', v: ' Fecha    : ' }, { k: 'B', v: '<?=date("d-m-Y")?>' }]);
              <?php
                //                 1   2   3   4   5   6   7   8   9  10  11  12  13  14  15  16  17  18  19  20  21  22  23  24  25  26  27   28   29   30   31
                $cols = array(' ','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE');
                $pos = 1;
                $c = '';
                $merge = '';
                $cells = '';
                $heads = '';
                for($i=4; $i<($max_pos*3)+4; $i+=3){
                  $c .= "{ k: '{$cols[$i]}', v: '{$nomenclatura[$pos]}' },";
                  $merge .= "<mergeCell ref=\"{$cols[$i]}4:{$cols[$i+2]}4\" />";
                  $cells .= "'c[r={$cols[$i]}4]',";
                  $pos++;
                }
                for($i=4; $i<($max_pos*3)+4; $i++){
                  $heads .= "'c[r={$cols[$i]}5]',";
                }
                $merge .= "<mergeCell ref=\"A1:{$cols[$i]}1\" />";
                $cells .= "'c[r=A1]'";
              ?>
              var r2 = Addrow(4, [<?=$c?>]);

              var cells = [<?=$cells?>];
              var heads = [<?=$heads?>];

              var merged = '<mergeCell ref="A'+(total_rows+1)+':B'+(total_rows+1)+'" />';
              var up = ''; va = ''; r3 = ''; r4 = ''; r5 = ''; r6 = '';
              // var up = Addrow(total_rows+1,[{ k: 'A', v: 'Universo Proyecto' }, { k: 'C', f: 'SUM(C6:C'+total_rows+')' }]);
              // total_rows += 2;
              // var va = Addrow(total_rows++, [{ k: 'A', v: '' }]);
              
              // var r3 = Addrow(total_rows, [{ k: 'A', v: 'TABLA TIPO SENSORES' }]);
              // merged += '<mergeCell ref="A'+(total_rows)+':D'+(total_rows)+'" />';

              // var r4 = Addrow(total_rows+1, [{ k: 'A', v: 'Interno' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor interno de presión y temperatura' }]);
              // merged += '<mergeCell ref="C'+(total_rows+1)+':D'+(total_rows+1)+'" />';
              
              // var r5 = Addrow(total_rows+2, [{ k: 'A', v: 'Externo' }, { k: 'B', v: 'TMS 24' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);
              // merged += '<mergeCell ref="C'+(total_rows+2)+':D'+(total_rows+2)+'" />';
              
              // var r6 = AddRow(total_rows, [{ k: 'A', v: 'Canister' }, { k: 'B', v: 'TMS 2' }, { k: 'C', v: 'Sensor externo de presión y temperatura' }]);


              sheet.childNodes[0].childNodes[0].childNodes[0].outerHTML = '<col min="1" max="1" width="9" customWidth="1"/>';
              sheet.childNodes[0].childNodes[1].innerHTML = ti + r1 + r2 + sheet.childNodes[0].childNodes[1].innerHTML + up + va + r3 + r4 + r5;
              sheet.childNodes[0].childNodes[2].innerHTML = '<?=$merge?>'+merged;
              var rows = sheet.childNodes[0].childNodes[1].getElementsByTagName("row");

              
              $("c[r=A4] t", sheet).text('');

              $.each(cells, function(i,o){ $(o, sheet).attr('s', '51'); });
              // $.each(heads, function(i,o){ $(o, sheet).attr('s', '251'); });

              console.log(rows);
            },
          }, 
          {
            extend: 'print',
            text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
            key:{
              key: 'p',
              altKey: true
            },
            className: 'btn btn-info',
            title: "<?=$TITULO?> - <?=date("d-m-Y")?>",
          }
        ]
      },
      autoWidth: false,
      paging: true,
      info: false,
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    })
    .on("init.dt", function(){
      var wt = parseInt($("#contenido").css("width"));
      var wb = 330;
      var x = wt - wb ;
      var top = 25;
      var left = 3;
      if(wt <= 768){
        wb = 120;
        x = wt - wb;
        if(wt <= 490){
          top = -50;
          left = -1;
        }
      }
      $("div.dt-buttons.btn-group")
        .css("margin", "auto "+x+"px auto "+left+"px")
        .css("width",wb+"px");
    });

    $("#nuevoSensor")
    .on("hidden.bs.modal", function(evt){      
      $("#frm-adicionar").children().remove();
      $("#adicionar").click();
    })
    .on("show.bs.modal", function(evt){
      if($(evt.relatedTarget).data("modo"))
        $("#modo").val($(evt.relatedTarget).data("modo"));
      $("#si").text('0');
      $("#se").text('0');
      $("#sc").text('0');
      $("#csi").text('');
      $("#cse").text('');
      $("#csc").text('');
    });

    $("#modificarSensor")
    .on("hidden.bs.modal", function(evt){

    })
    .on("show.bs.modal", function(evt){
      
    });

    $("#adicionar").on("click", function(){
      $("#frm-adicionar").append(
        $('<input type="hidden" name="modo" id="modo" value="agregar">'));
      $("#frm-adicionar").append(
        $('<div class="row">' +
        '  <div class="<?=Core::col(6)?>">' +
        '    <div class="<?=Core::col(12)?>">Código</div>' +
        '    <div class="<?=Core::col(12)?>">' +
        '      <input type="text" class="form-control" placeholder=\'<?php print $texto_sitio["Codigo Sensor"]; ?>\' name="c[]" maxlength="4" onKeyPress="return permite(event, \'num_car\')" onfocus="contar();" onkeyup = "this.value=this.value.toUpperCase()">' +
        '    </div>' +   
        '  </div>' +

        '  <div class="<?=Core::col(6)?>">' +
        '    <div class="input-group">' +
        '      <div class="<?=Core::col(12)?>">Tipo</div>' +
        '      <div class="<?=Core::col(12)?>">' +
        '        <select class="selectpicker" name="t[]" data-width="100%">' +
        '          <option value="Interno">Interno</option>' +
        '          <option value="Externo">Externo</option>' +
        // '          <option value="Canister">Canister</option>' +
        '        </select>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>'));
      $("select").selectpicker();
      ix++;
    });

    $("#panel-guia-despacho a.close").on("click", function(){
      $("#panel-guia-despacho").hide('fast');
    });

    $("#guia-despacho").on("click", function(){
      contar();
      $("#panel-guia-despacho").show('fast');
    });

    //Guardar nuevo sensor
    $("#guardar").on("click", function(){
      var params = $("#frm-adicionar").serializeArray();
      $.post('ajax/ingresar-sensores/crud.php', params, function(json){
        $("#nuevoSensor").modal('hide');
        swal(json);
        tabla.ajax.reload();
      });
    });

    //Guardar modificación de sensor
    $("#guardar2").on("click", function(){
      var params = $("#frm-modificar").serializeArray();
      // 17_01_2018 CT - Se agrega el valor estado 
      params.push({name: "estado", value: $("#estado").selectpicker('val')});

      $.post('ajax/ingresar-sensores/crud.php', params, function(json){//console.log(json);
        $("#modificarSensor").modal('hide');
        swal(json);
        tabla.ajax.reload();
      });
    });

    $("#estado").on("changed.bs.select", function(e){      
      if($(this).selectpicker('val') == 'BAJA'){
        $("#razon_baja").removeClass("hidden");
      }
      else{
       $("#razon_baja").addClass("hidden"); 
      }
    });
  });

  function contar(){
    var sel = $("#frm-adicionar").find("select");
    var interno = '';
    var externo = '';
    // var canister = '';
    var ci = 0; ce = 0; cc = 0;

    $("#frm-adicionar > div.row").each(function(i,o){
      var codigo = $(o).find("input");
      var tipo   = $(o).find(".selectpicker");

      tipo = tipo.selectpicker('val');

      if(codigo.length != 0){
        if(tipo == 'Interno'){
          if(codigo.val() != ''){
            interno += codigo.val() + ';';
            ci++;
          }
        }
        if(tipo == 'Externo'){
          if(codigo.val() != ''){
            externo += codigo.val() + ';';
            ce++;
          }
        }
        // if(tipo == 'Canister'){
        //   if(codigo.val() != ''){
        //     canister += codigo.val() + ';';
        //     cc++;
        //   }
        // }
      }
    });
    
    $("#si").text(ci);
    $("#se").text(ce);
    $("#sc").text(cc);
    $("#csi").text(interno);
    $("#cse").text(externo);
    // $("#csc").text(canister);
  }

  function filtro(o){
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }

  function permite(elEvento, permitidos) {
    
    // Variables que definen los caracteres permitidos
    var numeros = "0123456789";
    var caracteres = " abcdefABCDEF";
    var numeros_caracteres = numeros + caracteres;
    var teclas_especiales = [8, 37, 39, 46];
    // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha 
   
    // Seleccionar los caracteres a partir del parámetro de la función
    switch(permitidos) {
        case 'num_car':
        permitidos = numeros_caracteres;
        break;
    }
   
    // Obtener la tecla pulsada 
    var evento = elEvento || window.event;
    var codigoCaracter = evento.charCode || evento.keyCode;
    var caracter = String.fromCharCode(codigoCaracter);
   
    // Comprobar si la tecla pulsada es alguna de las teclas especiales
    // (teclas de borrado y flechas horizontales)
    var tecla_especial = false;
    for(var i in teclas_especiales) {
      if(codigoCaracter == teclas_especiales[i]) {
        tecla_especial = true;
        break;
      }
    }
   
    // Comprobar si la tecla pulsada se encuentra en los caracteres permitidos
    // o si es una tecla especial
    return permitidos.indexOf(caracter) != -1 || tecla_especial;
  }

  function modificar(id){
    $.post("ajax/ingresar-sensores/crud.php", {modo: 'obtener', id: id}, function(json){
      if(json.type == 'success'){
      	  // 17_01_2018 CT - Se valida el estado USO para poder editar el sensor
          if(json.data) {
              $("#id").val(json.data.id);
              $("#codigo").val(json.data.codigo);
              $("#tipo").selectpicker('val', json.data.tipo);
              $("#estado").selectpicker('val', json.data.estado);
              $("#razon").selectpicker('val', json.data.baja);

              if($("#estado").selectpicker('val') == 'USO'){
                  $('#estado').attr('disabled', 'disabled');
              }

              if($("#estado").selectpicker('val') == 'DISPONIBLE'){
                  $('#estado').removeAttr('disabled');
              }

              if($("#estado").selectpicker('val') == 'BAJA'){
                  $('#estado').removeAttr('disabled');
                  $("#razon_baja").removeClass("hidden");
              }
              else{
                  $("#razon_baja").addClass("hidden");
              }

              tabla.ajax.reload();
          }else{
              swal(json);
          }
      }
    });
    $("#modificarSensor").modal('show');
  }

  function eliminar(id){
    // $("#modo").val('eliminar');
    var params = {id: id, modo: 'eliminar'};
// 17_01_2018 CT - Se modifica eliminar sensor para confirmar antes de eliminar
      $.post("ajax/ingresar-sensores/crud.php", {modo: 'obtener', id: id}, function(json){
          if(json.type == 'success'){
              if(json.data) {
                  if(json.data.estado == 'USO'){
                      $.post('ajax/ingresar-sensores/crud.php', params, function(json){
                          $("#nuevoSensor").modal('hide');
                          swal(json);
                      });
                  }else{
                      swal({
                              title: "¿Está seguro(a) de aplicar las modificaciones?",
                              text: "Si decide continuar, se aplicarán todos los cambios realizados.",
                              type: "warning",
                              showCancelButton: true,
                              confirmButtonColor: "#DD6B55",
                              confirmButtonText: "Si, continuar",
                              cancelButtonText: "No, cancelar",
                              closeOnConfirm: true,
                              closeOnCancel: true
                          },
                          function(isConfirm){
                              if (isConfirm) {
                                  $.post('ajax/ingresar-sensores/crud.php', params, function(json){
                                      $("#nuevoSensor").modal('hide');
                                      if(json.type == 'success'){tabla.ajax.reload()};
                                  });
                              }
                          });
                  }
              }else{
                  swal(json);
              }
          }
      });


  }
</script>