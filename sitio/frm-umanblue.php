<?php
  require 'autoload.php';

  $acc = new Acceso();

  $db = DB::getInstance();

  $TITULO    = $module_label; //'UMAN Blue';
  $SUBTITULO = '';

  $advanced  = ($perfilactivo->id == 5 || $perfilactivo->id == 7);

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
      <?php if($advanced){ ?>
      <div class="frm-group">
        <button type="button" class="btn btn-primary" onclick="nuevo();">
          <i class="fa fa-plus" aria-hidden="true"></i> Agregar
        </button>
      </div>
      <?php } ?>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    
    <table class="table table-hover" id="tabla-cajas">
      <thead>
        <?= ($advanced ? '<th>&nbsp;btn</th>' : '') ?>
        <th>Código Caja</th>
        <th>Equipo</th>
        <?= ($advanced ? '<th>IP Pública</th>' : '') ?>
        <?= ($advanced ? '<th>IP WiFi</th>' : '') ?>
        <?= ($advanced ? '<th>IP LAN</th>' : '') ?>
        <?= ($advanced ? '<th>Número SIM</th>' : '') ?>
        
        <?= (!$advanced ? '<th>TimeOut</th>' : '') ?>
        <?= (!$advanced ? '<th>Batería</th>' : '') ?>
        <?= (!$advanced ? '<th>Estado</th>' : '') ?>
        <?= (!$advanced ? '<th>Buzzer</th>' : '') ?>
      </thead>
        
      <tbody>      
      </tbody>
    </table>

  </div>
</div>


<?php 
  if($advanced){
?>
<div class="modal fade" tabindex="-1" role="dialog" id="modalUMANBlue">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Nueva UMAN Blue</h4>
      </div>
      <div class="modal-body">
        <form id="form-UMAN">
          
          <div class="row">
            <div class="col-sm-6 obligatorio">C&oacute;digo UMAN Blue</div>
            <div class="col-sm-6">
              <input type="hidden" name="modo" id="modo" value="">
              <input type="hidden" name="id" id="id" value="">
              <input type="hidden" name="old_eq" id="old_eq" value="">
              <input type="hidden" name="old_ub" id="old_ub" value="">
              <input type="text" class="form-control" placeholder="Codigo UMAN Blue" id="codigo" name="codigo">
            </div>
          </div>
          
          <div class="row"><div class="<?=Core::col(12)?>"></div></div>
          
          <div class="row">
            <div class="col-sm-6 obligatorio">Equipo</div>
            <div class="col-sm-6">
              <select class="form-control selectpicker" name="equipo" id="equipo">
                <option value=""></option>
              <?php
                $datoso = $db->query("SELECT * FROM uman_camion WHERE ID_CAJAUMAN='0' ORDER BY NUMCAMION");
                foreach($datoso->results() as $datax) {
                  print "<option value='{$datax->ID_CAMION}'>{$datax->NUMCAMION}</option>";
                }
              ?>
              </select>
            </div>
          </div>
          
          <div class="row"><div class="<?=Core::col(12)?>"></div></div>

          <div class="row">
            <div class="col-sm-6">IP WiFi</div>
            <div class="col-sm-6">
              <input type="text" class="form-control" placeholder="IP WiFi" name="ip_wifi" id="ip_wifi">
            </div>
          </div>

          <div class="row"><div class="<?=Core::col(12)?>"></div></div>

          <div class="row">
            <div class="col-sm-6">IP LAN</div>
            <div class="col-sm-6">
              <input type="text" class="form-control" placeholder="IP LAN" name="ip_lan" id="ip_lan">
            </div>
          </div>

          <div class="row"><div class="<?=Core::col(12)?>"></div></div>

          <div class="row">
            <div class="col-sm-6">N&uacute;mero SIM</div>
            <div class="col-sm-6">
              <select class="form-control selectpicker" name="sim" id="sim">
                <?php
                  $data = $db->query("SELECT * FROM uman_sim WHERE ESTADO='Disponible' ORDER BY COMPANIA");

                  foreach($data->results() as $datos) {
                    print "<option value='{$datos->ID}'>{$datos->TELEFONO}</opcion>\n";
                  }
                ?>
              </select>
            </div>
          </div>

        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger pull-left" id="Eliminar">Eliminar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="Guardar">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
  }
?>

<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;
  $(function(){
    tabla = $("#tabla-cajas").DataTable({
      dom: 'Brtip',
      // searching: true,
      responsive: true,
      ajax: {
        url: 'ajax/umanblue/umanblue.php',
        type: 'POST',
      },
      columns:[
        <?= ($advanced ? '{ data: "btn" },' : '') ?>
        { data: "codigo" },
        { data: "equipo" },
        <?= ($advanced ? '{ data: "ip_publica" },' : '') ?>
        <?= ($advanced ? '{ data: "ip_wifi" },' : '') ?>
        <?= ($advanced ? '{ data: "ip_lan" },' : '') ?>
        <?= ($advanced ? '{ data: "sim" },' : '') ?>
        
        <?= (!$advanced ? '{ data: "timeout" },' : '') ?>
        <?= (!$advanced ? '{ data: "bateria" },' : '') ?>
        <?= (!$advanced ? '{ data: "leds" },' : '') ?>
        <?= (!$advanced ? '{ data: "buzzer" },' : '') ?>
      ],
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "Tabla de UMAN Blue - <?=date("d-m-Y")?>",
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
              var ti = Addrow(1, [{ k: 'A', v: 'Tabla de UMAN Blue - <?=date("d-m-Y")?>' }]);
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
            title: "Tabla de UMAN Blue - <?=date("d-m-Y")?>",
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

    $("#Guardar")
    .on("click", function(){
      var params = $("#form-UMAN").serializeArray();

      $.post('ajax/umanblue/crud.php', params, function(data){
        swal(data);
        tabla.ajax.reload();
        $("#modalUMANBlue").modal('hide');
      });
    });

    $("#Eliminar")
    .on("click", function(){
      swal({
        title: 'Eliminar registro',
        text: '¿Está realmente seguro(a) de eliminar el registro seleccionado?. Una vez realizado no podrá deshacer la operación.',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmTextButton: 'Si, Eliminar',
        type: 'warning',
      }, function(isConfirm){
        if(isConfirm){
          $("#modo").val('eliminar');
          var params = $("#form-UMAN").serializeArray();

          $.post('ajax/umanblue/crud.php', params, function(data){
            swal(data);
            tabla.ajax.reload();
            $("#modalUMANBlue").modal('hide');
          });
        }
      });
    });
  });

  function editar(id,cod,cam,ip_wifi,ip_lan,sim){
    $.post('ajax/umanblue/crud.php', { modo: 'equipos-disponibles', cam: cam}, function(data){
      $("#equipo").html(data);
      $("#equipo").selectpicker('refresh');
    });
    $.post('ajax/umanblue/crud.php', { modo: 'sim-disponibles', sim: sim}, function(data){
      $("#sim").html(data);
      $("#sim").selectpicker('refresh');
    });
    $("#modalUMANBlue h4.modal-title").text('Modificar UMAN Blue');

    $("#modo").val('editar');
    $("#id").val(id);
    $("#codigo").val(cod);
    $("#old_ub").val(cod);
    $("#old_eq").val(cam);
    $("#ip_wifi").val(ip_wifi);
    $("#ip_lan").val(ip_lan);    
    $("#sim").selectpicker('val', sim);
    $("#modalUMANBlue").modal('show');
    $("#Eliminar").show();
  }

  function nuevo(){
    $.post('ajax/umanblue/crud.php', { modo: 'equipos-disponibles'}, function(data){
      $("#equipo").html(data);
      $("#equipo").selectpicker('refresh');
    });
    $("#modalUMANBlue h4.modal-title").text('Nueva UMAN Blue');
    $("#modo").val('nueva');
    $("#id").val('');
    $("#codigo").val('');
    $("#ip_wifi").val('');
    $("#ip_lan").val('');
    $("#sim").selectpicker('val', '');
    $("#modalUMANBlue").modal('show');
    $("#Eliminar").hide();
  }
</script>