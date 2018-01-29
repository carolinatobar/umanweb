<?php
  require 'autoload.php';

  $acc       = new Acceso($_SESSION, session_id());

  $db        = DB2::getInstance();

  $sql       = "SELECT * FROM uman_perfil;";
  $res       = $db->query($sql);
  $perfiles  = $res->results();

  $TITULO    = $module_label; //'Administrar Usuarios';
  $SUBTITULO = '';
?>
<style>
  .Usuario-Habilitado, .Usuario-Deshabilitado{
    color: black;
    border-radius: 4px;
    padding: 2px 4px;
    height: 24px;
    margin: auto;
  }
  .Usuario-Habilitado{
    background-color: rgba(118,255,3,.2);
    border: 1px solid rgba(118,255,3,.6);
  }
  .Usuario-Deshabilitado{
    background-color: rgba(229,20,0,.2);
    border: 1px solid rgba(229,20,0,.6);
  }
  .edit, .delete{
    width: 24px;
    height: 24px;
  }
  .radio{
    width: 80px;
  }
  .modal-body > table td{
    text-align: right !important;
  }
  h5{
    font-size: 1.25rem !important;
    margin: 0px auto 0px auto;
    font-family: inherit;
    font-weight: 500 !important;
    line-height: 1.1 !important;
    color: inherit;
    display: block;
    -webkit-margin-before: 1.67em;
    -webkit-margin-after: 1.67em;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
  }
  .close{
    float: right;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: .5;
    margin-top: -1.5rem !important;
  }
  .bootstrap-select.btn-group.dropdown-toggle.filter-option{
    word-wrap: break-word;
    word-break: break-all;
    max-width: 100%;
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
        <button class="btn btn-primary btn-xs new" data-toggle="modal" data-target="#formUsuario" title="Crear nuevo usuario">
          <i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Crear
        </button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table class='table table-condensed table-striped table-hover' id="tabla-usuarios">
      <thead>
        <tr>
          <th style="width:64px">&nbsp;</th>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Perfil</th>
          <th>Faena</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>&nbsp;</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="1" oninput="filtro(this);" placeholder="Usuario"</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="2" oninput="filtro(this);" placeholder="Nombre"</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="3" oninput="filtro(this);" placeholder="Email"</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="4" oninput="filtro(this);" placeholder="Perfil"</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="5" oninput="filtro(this);" placeholder="Faena"</th>
          <th><input type="text" class="foot-filter form-control" style="width:100%" data-index="6" oninput="filtro(this);" placeholder="Estado"</th>
        </tr>
      </tfoot>
      <tbody>
      </tbody>
    </table>
  </div>
</div>



<!-- VENTANA MODAL -->
<div class="modal fade" id="formUsuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="width: 90%">
    <div class="modal-content">
      <div class="bg-primary modal-header">
        <h5 class="modal-title">Nuevo usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frmUsuario">
          <input type='hidden' name='modo' id="modo" value='nuevo-usuario'>
          <input type='hidden' name='id' id="id" value="-1">
          <table class="table table-frm" cellspacing='0' width='100%'>        
            <tr>
              <td><span style="color: red">* </span>Nombre</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td><input type='text' class='form-control' name='nombre' data-required="true" data-mensaje="Debe ingresar el nombre completo del usuario."></td>
            </tr>
            <tr>
              <td><span style="color: red">* </span>Usuario</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td><input type='text' class='form-control' name='usuario' data-required="true" data-mensaje="Debe ingresar el nombre de usuario."></td>
            </tr>
            <tr>
              <td>Email</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td><input type='text' class='form-control' name='email' data-required="false" data-mensaje="Debe ingresar una dirección de correo electrónico."></td>
            </tr>
            <tr>
              <td><span style="color: red">* </span>Contrase&ntilde;a</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td><input type='password' class='form-control' name='password' data-required="true" data-mensaje="Debe ingresar una contraseña."></td>
            </tr>
            <tr>
              <td><span style="color: red">* </span>Repetir contrase&ntilde;a</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td><input type='password' class='form-control' name='password_2' data-required="true" data-mensaje="Debe ingresar nuevamente la contraseña ingresada anteriormente."></td>
            </tr>
            <tr>
              <td><span style="color: red">* </span>Perfil</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td style="text-align: left">
                <select name="perfil" class="form-control selectpicker" data-required="true" data-mensaje="Debe seleccionar un perfil para el usuario." multiple data-actions-box="true" data-width="100%">
                  <?php
                    if($perfilactivo->nombre == 'Root')
                      echo '<option value="'.$perfilactivo->id.'" >'.utf8_encode($perfilactivo->nombre).'</option>';

                    foreach($perfiles as $px){
                      if($px->nombre != 'Root')
                        echo '<option value="'.$px->id.'" >'.utf8_encode($px->nombre).'</option>';
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><span style="color: red">* </span>Faena</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td>
                <select name="faena" class="form-control selectpicker" data-required="true" data-mensaje="Debe seleccionar la faena a la que tendrá acceso." multiple data-actions-box="true" data-width="100%">
                  <?php echo $comboFaenas ?>
                </div>
              </td>
            </tr>
            <tr>
              <td>Estado</td>
              <td>&nbsp;:&nbsp;&nbsp;</td>
              <td>
                <label>
                  <input type="radio" name="estado" value="1" id="habilitado" />
                  Habilitado
                </label>
                <br/>
                <label>
                  <input type="radio" name="estado" value="0" id="deshabilitado" />
                  Deshabilitado
                </label>
              </td>
            </tr>
          </table>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="guardar-usuario">Guardar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var tabla;
  function modificar(o){
    $("#id").val($(o).data("usuario"));
    $("#formUsuario").modal('show');
  }
  function eliminar(o){
    var id = $(o).data("usuario");
    swal({
      title: '¿Está seguro de continuar?',
      text: 'A continuación se eliminará el usuario seleccionado.',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Si, Continuar",
      cancelButtonText: "No, Cancelar",
      closeOnConfirm: false,
      closeOnCancel: true,
      inputValue:id
    },
    function(isConfirm){
      if (isConfirm) {
        $.post('ajax/crud-usuarios.php', {id: this.inputValue, modo: 'eliminar-usuario'}, function(data){
          swal(data);
          if(data.type=='success'){
            $.post('ajax/crud-usuarios.php',{modo: 'obtener-tabla'},function(data){
              if(data!=''){
                // $("#tabla-usuarios tbody").html(data);
                tabla.ajax.reload();
              }
            });
          }
        });
      } 
    });
  }
  function showPop(o){
    var content = $(o).data("content"); 
    var title   = $(o).data("title");
    var sign    = $(o).text().trim();
    if(sign == '+'){
      $(o).popover({
        content: content,
        title: title,
        trigger: 'focus',
        html: true,
      }).
      on("hide.bs.popover", function(evt){
        $(evt.currentTarget).text('+');
      });
      $(o).text('-');
    }
    else{
      $(o).text('+');
    }
    $(o).popover('toggle');
  }
  function filtro(o){
    tabla.columns($(o).data('index')).search( o.value ).draw();
  }
  $(function(){
    var w = $(".btn-group.bootstrap-select.show-tick.form-control").css("width");

    tabla = $("#tabla-usuarios").DataTable({
      "dom": 'lrtip',   
      "searching": true,
      "ordering": true,
      "columnDefs": [
        { "orderable": false, "targets": 0 },
      ],
      "ajax": {
        "url": "ajax/crud-usuarios.php?<?=rand(1024,24500)?>",
        "type": "POST",
        "data": {modo: "obtener-tabla"}
      },
      "columns": [
        { "data": "btn" },
        { "data": "usuario" },
        { "data": "nombre" },
        { "data": "correo" },
        { "data": "perfil" },
        { "data": "faena" },
        { "data": "estado" }
      ],
      "paging": true,
      "info": false,
      "language": {
        "url": "assets/datatables-1.10.15/lang/Spanish.json",
        "loadingRecords": '<div class="loader show"></div>'
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

    $("#guardar-usuario").click(function(){
      var params = {};
      var errors = '';

      $("#frmUsuario input").each(function(i,o){
        params[o.name] = o.value;
        if($(o).data("required")==true && o.value==''){
          if(o.name=='password_2'){
            if(o.value!='' && params.password=='') errors += '<li>'+$(o).data("mensaje")+'</li>';
          } 
          else errors += '<li>'+$(o).data("mensaje")+'</li>';
        } 
      });

      $("#frmUsuario select").each(function(i,o){
        params[o.name] = $(o).selectpicker('val');
        if($(o).data("required")==true && o.value=='') errors += '<li>'+$(o).data("mensaje")+'</li>';
      });

      params.estado = $("#frmUsuario input:checked").val();

      if(params.password!=params.password_2 && params.password!=''){
        errors += '<li>Las contraseñas ingresadas no coinciden, asegúrese de que ambas sean idénticas.</li>';
      }

      params.id = $("#id").val();
      console.log(params);
      // console.log(errors);

      if(errors!=''){
        swal({
          title:'Campos incompletos',
          text: '<h3>No se podrá continuar debido a los siguientes errores:</h3> <br/><ul>'+errors+'</ul>',
          html: true,
          type: 'warning'
        });
      }
      else{
        $.post('ajax/crud-usuarios.php', params, function(data){
          swal(data);
          if(data.type=='success'){
            $("#formUsuario").modal('hide');
            $.post('ajax/crud-usuarios.php',{modo: 'obtener-tabla'},function(data){
              if(data!=''){
                // $("#tabla-usuarios tbody").html(data);
                tabla.ajax.reload();
              }
            });
          }
        });
      }
    });

    $("#formUsuario").on("show.bs.modal", function(e){
      if(e.relatedTarget){
        $("#formUsuario h5.modal-title").html('Nuevo Usuario');
        $("#guardar-usuario").html('Crear Usuario');
        $("#modo").val('nuevo-usuario');
        $("#frmUsuario input[name=password]").data("required", true);
        $("#frmUsuario input[name=password_2]").data("required", true);
      }
      else{
        $("#formUsuario h5.modal-title").html('Modificar Usuario');
        $("#guardar-usuario").html('Guardar');
        $("#modo").val('editar-usuario');

        $("#frmUsuario input").attr("readonly",true).prop("readonly",true);
        $("#frmUsuario select").prop("disabled",true);
        $("#frmUsuario select").selectpicker('refresh');
        $("#frmUsuario input:radio").attr("disabled","disabled").prop("disabled","disabled");
        $("#frmUsuario input[name=password]").data("required", false);
        $("#frmUsuario input[name=password_2]").data("required", false);
        //Cargar datos
        var id = $("#id").val();

        $.post('ajax/crud-usuarios.php', {id: id, modo: 'obtener-usuario'}, function(data){
          if(data.type=='success'){
            $("#frmUsuario input").removeAttr("readonly",false).removeProp("readonly");
            $("#frmUsuario select").prop("disabled",false);
            $("#frmUsuario select").selectpicker('refresh');
            $("#frmUsuario input:radio").removeAttr("disabled").removeProp("disabled");

            $("input[name=nombre]").val(data.data.nombre);
            $("input[name=usuario]").val(data.data.usuario);
            $("input[name=email]").val(data.data.email);
            $("select[name=perfil]").selectpicker('val', data.data.perfil);
            $("select[name=faena]").selectpicker('val', data.data.faena);
            $("input[type=radio]").attr("checked",false).prop("checked",false);
            if(data.data.activo == 1) $("#habilitado").attr("checked",true).prop("checked",true);
            else $("#deshabilitado").attr("checked",true).prop("checked",true);
          }
          else{
            swal(data);
          }
        });
      }
      // console.log(e);
    });

    $(".selectpicker").on("changed.bs.select", function(){
      $(".btn-group.bootstrap-select.show-tick.form-control").css("width", w);
    });
  });
</script>