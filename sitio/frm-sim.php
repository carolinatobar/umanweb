<?php
  require 'autoload.php';

  $acc = new Acceso();

  $TITULO    = $module_label; //$texto_sitio["Tarjetas SIM"];
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
			<!-- 11_01_2018 CT - Se agrega id al botón agregar -->
				<button id="agregar" class="btn btn-primary" style="width:100%" data-toggle="modal" data-target="#modalTarjeta" onclick="nuevo()"><i class="fa fa-plus"></i> Agregar</button>
			</div>
		</div>
	</div>

  <!-- CONTENIDO -->
  <div id="contenido">
		<table class="table table-responsive" id="tabla-tarjetas">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Teléfono</th>
					<th>Compañía</th>
					<th>Estado</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<!-- NUEVA TARJETA -->
<div class="modal" id="modalTarjeta">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nuevo Teléfono</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frm-sim">
          <input type="hidden" name="id" id="id" value="">
          <input type="hidden" name="modo" id="modo" value="">
          <div class="form-group">
            <label>Compañía Móvil</label>
            <select name="compania" class='form-control selectpicker' id="compania">
              <option selected>Seleccione una opción</option>
              <option value="Entel">Entel</option>
              <option value="Movistar">Movistar</option>
              <option value="Claro">Claro</option>
              <option value="WOM">WOM</option>
            </select>
          </div>
          <div class="form-group">
            <label>Número telefónico</label>
	    <!-- 11_01_2018 CT - Se elimina texto predefinido +569... -->
            <input type="text" class="form-control"  name="telefono" id="telefono" REQUIRED>
          </div>
          <div class="form-group estado">
            <label>Estado</label>
            <select name="estado" id="estado" class="form-control selectpicker">
              <option selected>Seleccione una opción</option>
              <option value="Disponible">Disponible</option>
              <option value="Uso">En uso</option>
              <option value="Baja">Baja</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="guardar">Guardar</button>
	<!-- 11_01_2018 CT - Se agrega id al botón cerrar -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrar">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- JAVASCRIPT -->
<script type="text/javascript">
  var tabla;

  $(function(){

    tabla = $("#tabla-tarjetas")
    .DataTable({
      dom: 'Brtip',
			ajax: {
				url: 'ajax/sim/tarjetas.php',
				type: 'POST',
			},
			columns:[
				{ data: "btn" },
				{ data: "telefono" },
				{ data: "compania" },
				{ data: "estado" }
			],
      searching: true,
      responsive: true,
      buttons: {
        buttons:[
          {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
            className: 'btn btn-info',
            filename: "<?=$TITULO?> - <?=date("d-m-Y")?>"
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
      columnDefs: [
        { "orderable": false, "targets": 0 },
        { "orderable": false, "targets": 1 },
        { "orderable": false, "targets": 2 },
				{ "orderable": false, "targets": 3 },
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


    $("#guardar")
    .on("click", function(){
      var params = $("#frm-sim").serializeArray();
      console.log(params);
      $.post('ajax/sim/crud.php', params)
      .success(function (data){
        console.log(data);
        swal(data);
        tabla.ajax.reload();
        $("#modalTarjeta").modal('hide');
      })
      .fail(function (data){
        console.log(data);
        $("#modalTarjeta").modal('hide');
      });
    });
  });
</script>
<script type="text/javascript">
// 11_01_2018 CT - Se repara problema que al agregar otro SIM aparece el cuadro editar del último SIM de la lista
  function nuevo(){
      $("#modalTarjeta")
          .on("show.bs.modal", function(evt){
              $("#modalTarjeta h5.modal-title").text("Nueva SIM");
              $("#guardar").text("Crear");
              $("#id").val('');
              $("#modo").val('nueva');
              $("#telefono").val('');
              $("#compania").selectpicker('val','');
              $("div.form-group.estado").hide('fast');
          });
  }
 // 11_01_2018 CT - Se modifica funcion editar, para resolver problema que al editar un SIM aparecen los datos del primer SIM abierto
  function editar(id){
      $("#modalTarjeta").attr("data-modo","editar");
      $("#modalTarjeta")
          .on("show.bs.modal", function(evt) {
              $("#modo").val("editar");
              $("#modalTarjeta h5.modal-title").text("Modificar SIM");
              $("#guardar").text("Guardar");
              $("div.form-group.estado").show('fast');
              $(this).removeAttr("data-modo").removeAttr("data-id");
          });
    //abrir ventana modal y cargar datos para modificarlos
      $.post('ajax/sim/crud.php', {id: id, modo: 'obtener'}, function(json){
          if(json){
              if(json.type == 'success'){
                  if(json.data){
                      $("#id").val(json.data.id);
                      $("#telefono").val(json.data.telefono);
                      $("#compania").selectpicker('val',json.data.compania);
                      $("#estado").selectpicker('val', json.data.estado);
                  }
              }
              else{
                  swal(json);
              }
          }
      });
      $("#modalTarjeta").modal('show');

  }

  function eliminar(id){
    swal({
      title: 'Eliminar SIM',
      text: '¿Está seguro(a) de eliminar el registro',
      type: 'warning',
      confirmTextButton: 'Si, eliminar',
      closeOnConfirm: false,
      showCancelButton: true,
    }, function (isConfirm){
      if(isConfirm){
        //Eliminar registro
        $.post('ajax/sim/crud.php', {id: id, modo: 'eliminar'})
        .success(function (data){
          console.log(data);
            tabla.ajax.reload();
          swal(data);

        })
        .fail(function (data){
          console.log(data);
        });
      }
    });
  }
</script>