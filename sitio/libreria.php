<?php
    require 'autoload.php';

    $acc = new Acceso();

    $db = DB::getInstance();
?>
<style>
  #divi {  
    position: absolute;
    left: 1100px;
    top: 20px;
    font-size: 12px; 
    opacity: 0;
    }
    .trclass:hover { 
        background: #7199be !important;
        color: white;
    }
    .trclass:hover td { background: transparent;
        color: white; 
    }
</style>
<script>
  function show(id) {
    document.getElementById(id).style.visibility = "visible";
  }
  function hide(id) {
    document.getElementById(id).style.visibility = "hidden";
  }

$(document).ready(function(){
    $("#button").click(function(){
        $("#divi").animate({
            top: '140px',
            left: '1100px',
            opacity: '1',
            height: '530px',
            width: '340px'
        }, "slow");
    });
});
$(document).ready(function(){
    $("#button2").click(function(){
        $("#divi").animate({
            top: '20px',
            left: '1100px',
            opacity: '0',
            height: '10px',
            width: '10px'
        });
    });
});
</script> 

<div class="cc-divider"><?php print $texto_sitio["Libreria"]; ?></div>
<?php


print "<p>";
print "<table cellspacing='0' class='table table-hover' width='900'>";
print "<thead>";
print "<td width='10'>";
print "<td>";
print "<b><center>".$texto_sitio["Documento"]."</b>";
print "</td>";
print "<td>";
print "<center><b>".$texto_sitio["Nombre"]."</b>";
print "</td>";
print "<td>";
print "<center><b>".$texto_sitio["Descripcion"]."</b></center>";
print "</td>";
print "</thead>";

$data = $db->query("SELECT * FROM documentos ORDER BY id desc")  or die(mysql_error());

// print_r($data->results());
$cuenta=0;

foreach($data->results() as $info) {

    $perfilesh = explode(",",$info->perfiles);

// 18_01_2018 CT - Se comenta validacion de perfilamiento 
    //if ( in_array($perfilactivo, $perfilesh) || $perfilactivo == "su" ) {
    if ( in_array($perfilactivo->id, $perfilesh) || $perfilactivo->id == 1) {
        $count = 0;
       // if ( $perfilactivo->id == 1 ) {
         //   print "\n<tr  style='cursor:pointer;' height='40' onclick=\"modaleditar('".$info->id."','".$info->nombre."','".$info->documento."','".$info->archivo."','".$info->descripcion."','".$monitoreo."','".$monitoreo_bailac."','".$config_sensores."','".$faena."','".$tecnicos."')\">\n";
        //} else {
            print "<tr style='cursor:pointer;' height='40' onclick=\"window.open('uploads/documentos/".$info->archivo."')\">";
        //}
        
        print "<td width='10' style='text-align: center'><span class='glyphicon glyphicon-file' aria-hidden='true'></span>";
        print "<td><center>";
        print utf8_encode($info->documento);
        print "</td>";
        print "<td>";
        print "<center>".utf8_encode($info->nombre)."</center>";
        print "</td>";
        print "<td>";
        print "<center>".utf8_encode($info->descripcion)."</center>";
        print "</td>";
        print "</tr>";    
    }
}

?>
</table>


<?php
    if ( $perfilactivo->nombre == "administrativos" ) {
   // if ( $perfilactivo->id == 1 ) {

?>

<center>


<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#cargasho">
  + <?php print $texto_sitio["Documento"]; ?>
</button>




<div class="modal fade" tabindex="-1" role="dialog" id="cargasho">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title"><?php print $texto_sitio["Nuevo documento"]; ?></h4>
</div>
<div class="modal-body">
<form action="uploaddocumento.php" method="POST" enctype="multipart/form-data">
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Documento"]; ?>
</div>
<div class="col-sm-9">
<input type="text" placeholder="<?php print $texto_sitio["Documento"]; ?>" name="documento" class="form-control">
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Nombre"]; ?>
</div>
<div class="col-sm-9">
<input type="text" placeholder="<?php print $texto_sitio["Nombre"]; ?>" name="nombre" class="form-control">
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Descripcion"]; ?>
</div>
<div class="col-sm-9">
<textarea placeholder="<?php print $texto_sitio["Descripcion"]; ?>" name="descripcion" class="form-control"></textarea>
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
Perfiles
</div>
<div class="col-sm-9" style="text-align: left">
<input type="checkbox" name="monitoreo"> <?php print $texto_sitio["Monitoreo"]; ?>
<br>
<input type="checkbox" name="monitoreo-bailac"> <?php print $texto_sitio["Monitoreo BAILAC"]; ?>
<br>
<input type="checkbox" name="config-sensores"> <?php print $texto_sitio["Configuracion Sensores"]; ?>
<br>
<input type="checkbox" name="faena"> <?php print $texto_sitio["Creacion Faena"]; ?>
<br>
<input type="checkbox" name="tecnicos"> <?php print $texto_sitio["Soporte Tecnico"]; ?>
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Archivo"]; ?>
</div>
<div class="col-sm-9">
<input type="file" name="file" class="form-control">
</div>
</div>

</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal"><?php print $texto_sitio["Cerrar"]; ?></button>
&nbsp;&nbsp;&nbsp;
<input type="submit" class="btn btn-primary" value="<?php print $texto_sitio["Guardar"]; ?>">


</form>
</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    
function modaleditar(id,nombre,documento,archivo,descripcion,monitoreo,monitoreo_bailac,config_sensores,faena,tecnicos) {

  document.getElementById("id").value                            = id;
  document.getElementById("documento_editar").value              = documento;
  document.getElementById("nombre_editar").value                 = nombre;
  document.getElementById("descripcion_editar").value            = descripcion;
  document.getElementById("monitoreo_editar").checked            = monitoreo;
  document.getElementById("monitoreo-bailac_editar").checked     = monitoreo_bailac;
  document.getElementById("config-sensores_editar").checked      = config_sensores;
  document.getElementById("faena_editar").checked                = faena;
  document.getElementById("tecnicos_editar").checked             = tecnicos;

  document.getElementById("borrar").href = "borrar.php?modo=libreria&id=" + id;
  document.getElementById("descargar").href = "uploads/documentos/" + archivo;
  $('#cargasho2').modal('toggle');
}


</script>

<div class="modal fade" tabindex="-1" role="dialog" id="cargasho2">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title"><?php print $texto_sitio["Editar documento"]; ?></h4>
</div>
<div class="modal-body">
<!-- <form action="uploaddocumento.php" method="POST" enctype="multipart/form-data"> -->
<form class="form" id="frm-adicionar">
<input type="hidden" name="id" value="" id="id">
    <input type="hidden" name="editar" value="editar" id="editar">
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Documento"]; ?>
</div>
<div class="col-sm-9">
<input type="text" placeholder="<?php print $texto_sitio["Documento"]; ?>" name="documento" class="form-control" id="documento_editar">
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Nombre"]; ?>
</div>
<div class="col-sm-9">
<input type="text" placeholder="<?php print $texto_sitio["Nombre"]; ?>" name="nombre" class="form-control" id="nombre_editar">
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Descripcion"]; ?>
</div>
<div class="col-sm-9">
<textarea placeholder="<?php print $texto_sitio["Descripcion"]; ?>" name="descripcion" class="form-control" id="descripcion_editar"></textarea>
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div>
<div class="row">
<div class="col-sm-3">
Perfiles
</div>
<div class="col-sm-9" style="text-align: left">
<input type="checkbox" name="monitoreo" id="monitoreo_editar"> <?php print $texto_sitio["Monitoreo"]; ?>
<br>
<input type="checkbox" name="monitoreo-bailac" id="monitoreo-bailac_editar"> <?php print $texto_sitio["Monitoreo BAILAC"]; ?>
<br>
<input type="checkbox" name="config-sensores" id="config-sensores_editar"> <?php print $texto_sitio["Configuracion Sensores"]; ?>
<br>
<input type="checkbox" name="faena" id="faena_editar"> <?php print $texto_sitio["Creacion Faena"]; ?>
<br>
<input type="checkbox" name="tecnicos" id="tecnicos_editar"> <?php print $texto_sitio["Soporte Tecnico"]; ?>
</div>
</div>
<div class="row">
    <div class="col-sm-3">&nbsp;</div>
</div><!--
<div class="row">
<div class="col-sm-3">
<?php print $texto_sitio["Archivo"]; ?>
</div>
<div class="col-sm-9">
<input type="file" name="file" class="form-control">
</div>
</div>-->

</div>
<div class="modal-footer">
<a href="" id="borrar" class="btn btn-danger" style="float: left"><?php print $texto_sitio['Borrar']; ?></a>
<a href="" id="descargar" class="btn btn-lg btn-success" target="_blank"><?php print $texto_sitio['Descargar']; ?></a>
&nbsp;&nbsp;&nbsp;
<button type="button" class="btn btn-default" data-dismiss="modal"><?php print $texto_sitio["Cerrar"]; ?></button>
<button type="button" class="btn btn-primary" id="guardar"><?php print $texto_sitio["Guardar"]; ?></button>
<!--<input type="submit" class="btn btn-primary" value="<?php //print $texto_sitio["Guardar"]; ?>">-->

</form>
</div>
</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
}
?>

    <script>
        //Guardar nuevo sensor
        $("#guardar").on("click", function(){
            var params = $("#frm-adicionar").serializeArray();
            console.log(params);
            $.post('ajax/libreria/crud.php', params, function(json){
                 if(json.type = 'success'){
                     swal(json);
                     console.log(json);
                    location.reload();
                     $("#cargasho2").modal('hide');
                }
                 //tabla.ajax.reload();
                //location.reload();
             });
        });
    </script>
