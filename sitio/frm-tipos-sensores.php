<?php
  require 'autoload.php';

  $acc = new Acceso();
  $db  = DB::getInstance();

  $sql = "SELECT * FROM uman_tiposensor";
  $res = $db->query($sql);  

  $TITULO    = $module_label; //'Gestión de Tipos de Sensores';
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
        <button type="button" class="btn btn-primary" id="btnCrearTipoSensor" data-toggle="modal" data-target="#crearTipoSensor">
          <i class="fa fa-plus" aria-hidden="true"></i> Crear
        </button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table id="tabla-sensores" class="table table-hover">
      <thead>
        <tr>
          <th></th>
          <th>Nomenclatura</th>
          <th>Nombre</th>
          <th>Temperatura</th>
          <th>Presión</th>
          <th>Humedad</th>
          <th>Combustible</th>
          <th>Gas</th>
          <th>Descripción</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<?php
  echo Core::createModal(
    'crearTipoSensor', 
    [
      'includeContent'=>true, 
      'title'=>'Crear nuevo tipo de sensor',
      'saveButton'=>'<button type="button" class="btn btn-primary" id="GuardarTipoSensor">Guardar</button>',
      'content'=>'
        <form name="frmTipoSensor" id="frmTipoSensor">  
          <input type="hidden" name="ac">
          <input type="hidden" name="id">

          <div class="row">
            <div class="col-sm-4 obligatorio" style="text-align: right; font-weight: 600">
              <b>Nomenclatura: </b>
            </div>
            <div class="col-sm-8">
              <input type="text" name="tipo" class="form-control" data-error="Debe ingresar la nomenclatura que servirá para identificar al sensor. Este valor debe ser único.">
            </div>
          </div>

          <div class="row">
            <div class="col-sm-4 obligatorio" style="text-align: right; font-weight: 600">
              <b>Nombre: </b>
            </div>
            <div class="col-sm-8">
              <input type="text" name="nombre" class="form-control" data-error="Debe ingresar el nombre de sensor, este debe ser único e irrepetible.">
            </div>
          </div>

          <div class="row">
            <div class="col-sm-4 obligatorio" style="text-align: right; font-weight: 600">
              <b>Mediciones: </b>
            </div>
            <div class="col-sm-8">
              <select name="medicion[]" class="form-control selectpicker" multiple data-error="Debe seleccionar al menos un tipo de medición que el sensor realizará.">
                <option value="temperatura">Temperatura</option>
                <option value="presion">Presión</option>
                <option value="humedad">Humedad</option>
                <option value="combustible">Combustible</option>
                <option value="gas">Gas</option>
              </select>
            </div>
          </div>
          
          <div class="row">
            <div class="col-sm-4 obligatorio" style="text-align: right; font-weight: 600">
              <b>Descripción: </b>
            </div>
            <div class="col-sm-8">
              <input type="textarea" name="descripcion" class="form-control" data-error="Debe ingresar una breve descripción del sensor, esto ayudará a comprender su propósito.">
            </div>
          </div>

          <div class="row">
            <div class="col-sm-4 obligatorio" style="text-align: right; font-weight: 600">
              <b>Imagen: </b>
            </div>
            <div class="col-sm-8">
              <input type="file" name="imagen" class="form-control" data-error="Debe seleccionar la imagen que se mostrará en toda la aplicación, esta imagen debe ser representativa del sensor." accept="image/gif, image/jpeg, image/png" onchange="readURL(this);">
              <img src="" style="float: center !important; width: auto !important;" class="icono-x36" id="thumb_imagen">
            </div>
          </div>
        </form>
      '
    ]
  );
?>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  $(function(){
    tabla = $("#tabla-sensores").DataTable({
      dom: 'Brtip',
      // searching: true,
      responsive: true,
      ajax: {
        url: 'ajax/tipo-sensor/tipo-sensor.php?r='+Math.random()*100,
        type: 'POST',
      },
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
      columns:[
        { data: 'btn' },
        { data: 'nomenclatura' },
        { data: 'nombre' },
        { data: 'temperatura' },
        { data: 'presion' },
        { data: 'humedad' },
        { data: 'combustible' },
        { data: 'gas' },
        { data: 'descripcion' }
      ],
      columnDefs: [
        { "orderable": false, "targets": 0 },
        { "orderable": false, "targets": 1 },
        { "orderable": false, "targets": 2 },
        { "orderable": false, "targets": 3 },
        { "orderable": false, "targets": 4 },
        { "orderable": false, "targets": 5 },
        { "orderable": false, "targets": 6 },
        { "orderable": false, "targets": 7 },
        { "orderable": false, "targets": 8 },
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

    $("#crearTipoSensor")
    .on("show.bs.modal", function(e){
      if($(e.relatedTarget).attr("id") == 'btnCrearTipoSensor') $("input[name=ac]").val('crear');
      else $("input[name=ac]").val('modificar');
    })
    .on("hidden.bs.modal", function(e){
      $("input").val('');
      $("[name=medicion]").selectpicker('val','');
      $("input[name=ac]").val('crear');
    });

    $("#GuardarTipoSensor").on("click", function(){
      var error  = '';
      if($("input[name=tipo]").val() == '')                   error += '<li>'+$("input[name=tipo]").data("error")+'</li>';
      if($("input[name=nombre]").val() == '')                 error += '<li>'+$("input[name=nombre]").data("error")+'</li>';
      if($("input[name=descripcion]").val() == '')            error += '<li>'+$("input[name=descripcion]").data("error")+'</li>';
      if($(".selectpicker").selectpicker('val') == null) error += '<li>'+$(".selectpicker").data("error")+'</li>';      
      if($("input[name=ac]").val() == 'crear'){
        if($("input[name=imagen]").val() == '')                 error += '<li>'+$("input[name=imagen]").data("error")+'</li>';
      }

      var f = $("input[name=imagen]")[0].files[0];
      var sa = $("#frmTipoSensor").serializeArray();
      if($("input[name=ac]").val() == 'crear'){
        if(!f.type.match("image.*")){ 
           error += '<li>El archivo debe ser un archivo de imagen válido.</li>';
        }
      }
      if(error == ''){
        var formData = new FormData();
        if($("input[name=ac]").val() == 'crear') formData.append('imagen', f, f.name);
        $.each(sa, function(i,o){
          formData.append(o.name, o.value);
        });

        console.log(formData);

        // Set up the request.
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax/tipo-sensor/crud.php', true);
        xhr.onload = function () {
          if (xhr.status === 200) {
            tabla.ajax.reload();
            var j = JSON.parse(xhr.response);
            swal(j);
            // console.log(xhr);
          } else {
            try{
              var j = JSON.parse(xhr.response);
              swal(j);
            }catch(Exception){

            }
            console.log(xhr);
          }
          $("#crearTipoSensor").modal("hide");
        };

        xhr.send(formData);
      }
      else{
        swal({
          title: 'Error en ingreso de datos.',
          text: '<ul><h4>No se puede proceder porque se han detectado los siguientes errores: </h4>'+error+'</ul>',
          type: 'error',
          html: true
        })
      }
    });
  });

  function filtro(o){
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }
  function eliminar(o){
    var id = $(o).data("id");
    $.post('ajax/tipo-sensor/crud.php', {ac: 'eliminar', id: id}, function(json){
      tabla.ajax.reload();
    });      
  }
  function modificar(o){
    var id = $(o).data("id");
    $.post('ajax/tipo-sensor/crud.php', {ac: 'obtener', id: id}, function(json){
      if(json.type == 'success'){
        $("#crearTipoSensor").modal("show");
        let medicion = [];
        if(json.data.mide_temperatura == 1) medicion.push('temperatura');
        if(json.data.mide_presion == 1) medicion.push('presion');
        if(json.data.mide_humedad == 1) medicion.push('humedad');
        if(json.data.mide_combustible == 1) medicion.push('combustible');
        if(json.data.mide_gas == 1) medicion.push('gas');
        $("input[name=ac]").val('modificar');
        $("input[name=id]").val(json.data.id);
        $("input[name=tipo]").val(json.data.tipo);
        $("input[name=nombre]").val(json.data.nombre);
        $(".selectpicker").selectpicker('val',medicion);
        $("input[name=descripcion]").val(json.data.descripcion);
        $("#thumb_imagen").attr("src", json.data.imagen);
      }
      else{
        swal(json);
      }
    });
  }
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#thumb_imagen')
          .attr('src', e.target.result)
          .height(36);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }
</script>