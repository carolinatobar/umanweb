<?php
  require 'autoload.php';

  $acc = new Acceso();
  $neu = new Neumatico();

  $db = DB::getInstance();

  $marcas     = $neu->obtenerMarcas();
  $modelos    = $neu->obtenerModelos();
  $dimensiones= $neu->obtenerDimensiones();
  $compuestos = $neu->obtenerCompuestos();

  $TITULO    = $module_label; //'Plantillas';
  $SUBTITULO = '';
?>

<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>
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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalPlantilla" data-modo="nueva" style="float:right">
          <i class="fa fa-plus" aria-hidden="true"></i> Agregar</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table class="table table-hover" id="tabla-plantillas">
      <thead>
        <th></th>
        <th>ID</th>
        <th><?php print $texto_sitio["Eje"]; ?></th>
        <th><?php print $texto_sitio["Marca"]; ?></th>
        <th><?php print $texto_sitio["Dimension"]; ?></th>
        <th><?php print $texto_sitio["Compuesto"]; ?></th>
        <th><?php print $texto_sitio["Sensor"]; ?></th>
        <th><?php print $texto_sitio["Temp. Max."]; ?></th>
        <th><?php print $texto_sitio["Presion Min."]; ?></th>
        <th><?php print $texto_sitio["Presion Max."]; ?></th>
        <th><?php print $texto_sitio["PIF"]; ?></th>
      </thead>
      <tbody>        
      </tbody>
    </table>
  </div>
</div>


<div class="modal fade" tabindex="-1" role="dialog" id="modalPlantilla">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
        <form id="frmPlantilla">
          <input type="hidden" name="id" id="id" value="" />
          <input type="hidden" name="ac" id="ac" value="" />
          <!-- EJE -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Eje  </div>
            <div class="col-sm-6">
              <select name="eje" id="eje" class="form-control selectpicker">
                <!--option value="0">Todas</option-->
                <option value="1" selected="selected">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
              </select>
            </div>
          </div>
          
          <div class="row"><div clss="col"></div></div>

          <!-- MARCA -->
          <div class="row">
            <div class="col-sm-6" style="font-weight: 600; text-align: left">Marca  </div>
            <div class="col-sm-6">
              <select name="marca" class="form-control selectpicker" id="marca" data-live-search="true">
                <!--option value="">Todas</option-->
                <?php
                  foreach($marcas as $marca) {
                    echo "<option value=\"{$marca->nombre}\">{$marca->nombre}</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>
          
          <!-- DIMENSIÓN -->
          <div class="row">
            <div class="col-sm-6" style="font-weight: 600; text-align: left">Dimensión  </div>
            <div class="col-sm-6">
              <select name="dimension" class="form-control selectpicker" id="dimension" data-live-search="true">
                <!--option value="">Todas</option-->
                <?php
                  foreach($dimensiones as $dimension){
                    echo "<option value=\"{$dimension->dimension}\">{$dimension->dimension}</option>";
                  }
                ?>
              </select>
            </div>
          </div>
          
          <div class="row"><div clss="col"></div></div>

          <!-- MODELO -->
          <div class="row">
            <div class="col-sm-6" style="font-weight: 600; text-align: left">Modelo  </div>
            <div class="col-sm-6">
              <select name="modelo" id="modelo" class="form-control selectpicker" data-live-search="true">
                <option value="">Todos</option>
                <?php
                  foreach($modelos as $modelo){
                    echo "<option value=\"{$modelo->nombre}\">{$modelo->nombre}</option>";
                  }
                ?>
              </select>
            </div>
          </div>
      
          <div class="row"><div clss="col"></div></div>

          <!-- COMPUESTO -->
          <div class="row">
            <div class="col-sm-6" style="font-weight: 600; text-align: left">Compuesto  </div>
            <div class="col-sm-6">
              <select name="compuesto" id="compuesto" class="form-control selectpicker" data-live-search="true">
                <option value="">Todos</option>
                <?php
                  foreach($compuestos as $compuesto){
                    echo "<option value=\"{$compuesto->nombre}\">{$compuesto->nombre}</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>

          <!-- SENSOR -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Sensor</div>
            <div class="col-sm-6">
              <select name="sensor" id="sensor" class="form-control">
                <option>Interno</option>
                <option>Externo</option>
              </select>
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>

          <!-- TEMPERATURA MÁXIMA -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Temperatura Máx.  </div>
            <div class="col-sm-6">  
              <input type="text" class="form-control" name="tempmax" id="tempmax" placeholder="Temperatura Máx.">
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>

          <!-- VALOR PRE-ALARMA -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Pre-Alarma  </div>
            <div class="col-sm-6">  
              <input type="text" class="form-control" name="pre_alarma" id="pre_alarma" placeholder="Valor Pre-Alarma">
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>

          <!-- PRESIÓN MÍNIMA -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Presión Mín.  </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" name="presmin" id="presmin" placeholder="Presión Mín.">
            </div>
          </div>

          <div class="row"><div clss="col"></div></div>
          
          <!-- PRESIÓN MÁXIMA -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">Presión Máx.  </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" name="presmax" id="presmax" placeholder="Presión Máx.">
            </div>
          </div>
      
          <div class="row"><div clss="col"></div></div>

          <!-- PIF -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style="font-weight: 600; text-align: left">PIF</div>
            <div class="col-sm-6">
              <input type="text" class="form-control" name="pif" id="pif" placeholder="PIF">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;

  $(function(){
    tabla = $("#tabla-plantillas").DataTable({
      dom: 'Brtip',
      // searching: true,
      ajax:{
        url: 'ajax/plantillas/plantillas.php',
      },
      columns:[
        { data: "btn" },
        { data: "id" },
        { data: "eje" },
        { data: "marca" },
        { data: "dimension" },
        { data: "compuesto" },
        { data: "sensor" },
        { data: "tmax" },
        { data: "pmin" },
        { data: "pmax" },
        { data: "pif" },
      ],
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de Plantillas - <?=date("d-m-Y")?>",
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
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de Plantillas - <?=date("d-m-Y")?>' }]);
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
            title: "Tabla de Plantillas - <?=date("d-m-Y")?>",
          }
        ]
      },
      columnDefs: [
        { "orderable": false, "targets": 0 },
        { "orderable": true,  "targets": 1 },
        { "orderable": true, "targets": 3 },
        { "orderable": true, "targets": 4 },
        { "orderable": true, "targets": 5 },
        { "orderable": true, "targets": 6 },
        { "orderable": true, "targets": 7 },
        { "orderable": true, "targets": 8 },
        { "orderable": true, "targets": 9 },
        { "orderable": true, "targets": 10 },
      ],
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

    // $("select").selectpicker();
  });

  $(function(){
    $("#modalPlantilla").on("show.bs.modal", function(e){
      var rt = $(e.relatedTarget);
      console.log(e);
      console.log(rt);

      $("#id").val('');
      $("#ac").val('');
      $("#marca").selectpicker('val','');
      $("#modelo").val('');
      $("#compuesto").val('');
      $("#dimension").selectpicker('val','');
      $("#tempmax").val('');
      $("#presmax").val('');
      $("#presmin").val('');
      $("#eje").selectpicker('val','');
      $("#sensor").selectpicker('val','');
      $("#pre_alarma").val('');
      $("#pif").val('');

      $("#marca").prop('disabled', false);
      $("#modelo").prop('disabled', false);
      $("#compuesto").prop('disabled', false);
      $("#dimension").prop('disabled', false);
      $("#eje").prop('disabled', false);
      $("#sensor").prop('disabled', false);
      $("#eje").selectpicker('refresh');
      $("#sensor").selectpicker('refresh');
      $("#dimension").selectpicker('refresh');
      $("#compuesto").selectpicker('refresh');
      $("#modelo").selectpicker('refresh');
      $("#marca").selectpicker('refresh');

      if(rt.data("modo") == "nueva"){
        $("#modalPlantilla h4.modal-title").text('Nueva Plantilla');
        $("#ac").val('nueva');
      }
      else if(e.relatedTarget == undefined){
        var modo = $("#modalPlantilla").data("modo");
        $("#modalPlantilla").removeAttr("data-modo");
        $("#ac").val(modo);

        if(modo == 'editar'){
          $("#modalPlantilla h4.modal-title").text('Modificar Plantilla');
          var id = $("#modalPlantilla").data("id");
          $.post('ajax/plantillas/crud.php', {id: id, ac: 'obtener'}, function(json){
            if(json.type == 'success'){
              $("#id").val(json.data.id);
              $("#marca").selectpicker('val',json.data.marca);
              $("#modelo").val(json.data.modelo);
              $("#compuesto").val(json.data.compuesto);
              $("#dimension").selectpicker('val',json.data.dimension);
              $("#tempmax").val(json.data.tmax);
              $("#presmax").val(json.data.pmax);
              $("#presmin").val(json.data.pmin);
              $("#eje").selectpicker('val',json.data.eje);
              $("#sensor").selectpicker('val',json.data.sensor);
              if(json.data.prealarma == 0) json.data.prealarma = Math.round(json.data.tmax * 0.8);
              $("#pre_alarma").val(json.data.prealarma);
              $("#pif").val(json.data.pif);
              if(json.data.used==1){
                $("#marca").prop('disabled', true);
                $("#modelo").prop('disabled', true);
                $("#compuesto").prop('disabled', true);
                $("#dimension").prop('disabled', true);
                $("#eje").prop('disabled', true);
                $("#sensor").prop('disabled', true);
                $("#eje").selectpicker('refresh');
                $("#sensor").selectpicker('refresh');
                $("#dimension").selectpicker('refresh');
                $("#compuesto").selectpicker('refresh');
                $("#modelo").selectpicker('refresh');
                $("#marca").selectpicker('refresh');
              }
            }
            else swal(json);
            console.log(json);
          });
        }
      }
    });

    $("#btn-guardar").on("click", function(){
      var params = $("#frmPlantilla").serializeArray();
      var presmin = 0; presmax = 0; error = ''; tempmax = 0; prealarma = 0;
      $.each(params, function(i,o){
        if(o.name == 'presmin'){
          presmin = parseInt(o.value);
          if(isNaN(presmin)) error += '<li>Debe ingresar un valor numérico para la presión mínima.</li>';
        }
        if(o.name == 'presmax'){
          presmax = parseInt(o.value);
          if(isNaN(presmax)) error += '<li>Debe ingresar un valor numérico para la presión máxima.</li>';
        }
        if(o.name == 'sensor' && o.value == '') error += '<li>Debe asignar el tipo de sensor.</li>';
        if(o.name == 'tempmax'){
          tempmax = parseInt(o.value);
          if(isNaN(parseInt(o.value))) error += '<li>Debe ingresar un valor numérico para la temperatura máxima.</li>';
        }
        if(o.name == 'pre_alarma'){
          prealarma = parseInt(o.value);
          if(isNaN(parseInt(o.value))) error += '<li>Debe ingresar un valor numérico para la pre alarma.</li>';
        }
        if(o.name == 'pif'){
          if(isNaN(parseInt(o.value))) error += '<li>Debe ingresar un valor numérico para la PIF.</li>';
        }
        if(o.name == 'eje' && o.value == '') error += '<li>Debe seleccionar el eje.</li>'+o.disabled;
      });
      if(presmin >= presmax) error += '<li>La presión mínima debe ser menor que la presión máxima.</li>';
      if(presmin<0) error += '<li>La presión mínima no puede ser inferior a 0 (cero).</li>';
      if(presmax<0) error += '<li>La presión máxima no puede ser inferior a 0 (cero).</li>';
      if(prealarma >= tempmax) error += '<li>El valor de la pre alarma no puede ser igual o superior al valor de la temperatura máxima.</li>';
      if(error == ''){
        $.post('ajax/plantillas/crud.php', params, function(json){
          // console.log(json);
          swal(json);
          tabla.ajax.reload();
          $("#modalPlantilla").modal('hide');
        });
      }
      else{
        swal({
          title: 'Nueva plantilla',
          text: '<ul>No se ha podido completar el ingreso porque se han detectado los siguientes errores:</ul>'+error,
          type: 'error',
          html: true,
        })
      }
      // console.log(params);
    });
  });

  function editar(id){
    if($("#modalPlantilla").data("id")) $("#modalPlantilla").data("id", id);
    else $("#modalPlantilla").attr("data-id", id);
    $("#modalPlantilla").attr("data-modo","editar");
    $("#modalPlantilla").modal("show");
  }
  function eliminar(id){
    swal({
      title: 'Eliminar registro',
      text: '¿Está seguro(a) de que desea eliminar el registro?',
      type: 'warning',
      closeOnConfirm: true,
      confirmButtonText: 'Si, Eliminar',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
    }, function(isConfirm){
      if(isConfirm){
        $.post('ajax/plantillas/crud.php',{id: id, ac: 'eliminar'})
        .success(function(data){
          tabla.ajax.reload();
          if(data['type']=='error'){
            swal({
              title: 'eliminar registro',
              text: '<ul>No se ha podido eliminar el registro porque se han detectado los siguientes errores:</ul>'+data['text'],
              type: 'error',
              html: true,
            })
          }
          console.log('success: ');
          console.log(data);
        })
        .error(function(data){
          
          console.log('fail: ');
          console.log(data);
        })
        .fail(function(data){
          console.log('fail: ');
          console.log(data);
        });
        
      }
    });
  }
  function permite(elEvento, permitidos) {

    // Variables que definen los caracteres permitidos
    var numeros = "0123456789";
    var caracteres = " abcdefghijklmn�opqrstuvwxyzABCDEFGHIJQLMN�OPQRSTUVWXYZ";
    var numeros_caracteres = numeros + caracteres;
    var teclas_especiales = [];
    // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha

    // Seleccionar los caracteres a partir del par�metro de la funci�n
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
</script>