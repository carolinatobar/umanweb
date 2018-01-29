<?php
  require 'autoload.php';

  $acc  = new Acceso($_SESSION, session_id());
  $gen  = new General();
  $db   = DB::getInstance();

  $zoom = $gen->getParamValue('zoom-2d',15);

  $dat  = $db->query("SELECT x, y FROM uman_gps ORDER BY FECHA DESC LIMIT 1");
  $dat  = $dat->results()[0];
  $LAT  = $dat->x;
  $LNG  = $dat->y;

  $TITULO    = $module_label; //'Geocercas';
  $SUBTITULO = '';

  $sql = "SELECT * FROM uman_geocerca";
  $geocercas = $db->query($sql);

  $js_gc = '';

  foreach($geocercas->results() as $k => $geo){
    $sql = "SELECT * FROM uman_poligono WHERE id_geocerca={$geo->id}";
    $poli = $db->query($sql);
    if($poli->count() > 0){
      $path = '';
      foreach($poli->results() as $p){
        $path .= '{lat: '.$p->latitud.', lng: '.$p->longitud.'},';
      }
      $js_gc .= "new google.maps.Polygon({paths: [{$path}],strokeColor: '{$geo->color}',strokeOpacity: 0.8,strokeWeight: 3,fillColor: '{$geo->color}',fillOpacity: 0.35, draggable: false,
      geodesic: true, editable: false, otherData:{ idx:{$k}, nombre:'{$geo->nombre}', icono: '{$geo->icono}'} }),";
    }
  }
?>
<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>
  #map-canvas { 
    /*width: 100%;*/
    /* height: 600px;  */
    position: relative;
    width: 100%;
    height: 720px;
  }
  /* Optional: Makes the sample page fill the window. */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
  .toolbox{
    z-index: 9999;
    position: absolute;
    left: 0px;
    top: 360px;
    width: 48px;
    height: 128px;
  }
  .toolbox>button{
    width: 48px;
    height: 36px;
  }
  #colorPicker{
    padding: 5px;
    border-radius: 3px;
    z-index: 9999;
    background-color: #dcdcdc;
    left: 50px;
    top: 0px;
    position: absolute;
  }
  .btn span.glyphicon {    			
	  opacity: 0;				
  }
  .btn.active span.glyphicon {
    opacity: 1;				
  }
  .nombre-geocerca{
    font-size: x-large;
    font-weight: bold;
    text-align: center;
  }
  .fake-check{
    width: 40px;
    height: 34px;
    /* background-color:#E0E0E0; */
    margin-left:-3px;
    border-radius: 3px;
    display: none;
  }
  .fake-check span.glyphicon{
    opacity: 1;
    color: #37474F;
  }
  th{
    text-align: center !important;
  }
</style>
<style type="text/css">
  *html .yui-picker-bg {
	background-image: none;
	filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='http://yui.yahooapis.com/2.5.2/build/colorpicker/assets/picker_mask.png', sizingMethod='scale');
  }
  #container { position: relative; padding: 6px; background-color: #eeeeee; width: 380px; height:210px; font-size: 1.2em}
  .yui-picker-bg{-moz-outline:none;outline:0px none;position:absolute;top:4px;left:4px;height:182px;width:182px;background-color:#F00;background-image:url(http://www.rapidtables.com/web/color/picker_mask.png);}
  .gm-style-iw > div{
    width: 100%;
  }
</style>
<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<!-- HERE API -->
<link rel="stylesheet" type="text/css" href="https://js.cit.api.here.com/v3/3.0/mapsjs-ui.css" />
<script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-core.js"></script>
<script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-service.js"></script>
<script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-ui.js"></script>
<script type="text/javascript" src="https://js.cit.api.here.com/v3/3.0/mapsjs-mapevents.js"></script>

<link rel="stylesheet" type="text/css" href="assets/jqwidgets/styles/jqx.base.css"/>
<script type="text/javascript" src="assets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="assets/jqwidgets/jqxcolorpicker.js"></script>

<!-- GOOGLE API -->
<!-- <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBis-Q9HufjfnPOjezA3LYymhmycbP7Ahw&libraries=geometry"></script> -->
<!-- <script src="graficos/gps-2d/maplabel/maplabel-compiled.js"></script> -->

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
        <!-- <button type="button" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Botón</button> -->
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->

  <div id="contenido">
    <div id="map-canvas"></div>
  </div>
</div>

<div class="modal fade pg-show-modal" id="modalCrearGeocerca" tabindex="-1" role="dialog" aria-hidden="true" data-pg-collapsed data-backdrop="false"> 
    <div class="modal-dialog"> 
        <div class="modal-content"> 
            <div class="modal-header bg-primary"> 
                <h4 class="modal-title">Creación de Geocerca</h4> 
            </div>             
            <div class="modal-body">
                <form id="form-geocerca" enctype="multipart/form-data">
                    <div class="form-group" data-pg-collapsed> 
                        <label class="control-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre de la Geocerca">
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6" data-pg-collapsed> 
                            <label class="control-label" for="icono">Icono</label>
                            <!-- <input type="file" class="form-control" name="icono" id="icono" accept="image/*"> -->
                        </div>
                        <div class="form-group col-md-6" data-pg-collapsed> 
                            <label class="control-label" for="color">Color</label>
                            <input type="color" class="form-control" name="color" id="color" value="#000000">
                        </div>
                    </div>                     
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Vigencia</h4>
                        </div>
                        <hr />
                        <div class="table-responsive">
                          <table class="table-striped table-hover">
                              <thead>
                                  <tr>
                                      <th>&nbsp;</th>
                                      <th>Lunes</th>
                                      <th>Martes</th>
                                      <th>Miércoles</th>
                                      <th>Jueves</th>
                                      <th>Viernes</th>
                                      <th>Sábado</th>
                                      <th>Domingo</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php
                                  $dias = ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'];
                                  $hora = "00:00";

                                  for($i=0; $i<=23; $i++){
                                    $hora = date("H:00",mktime($i,0,0,0,0,0));
                                  ?>
                                  <tr>
                                    <td><?=$hora?></td>
                                    <?php
                                      foreach($dias as $dia){
                                    ?>
                                    <td>
                                      <div class="checkboxes">
                                        <span class="pull-left"></span>
                                        <div class="btn-group" data-toggle="buttons">
                                          <label class="btn btn-info active">
                                            <input type="checkbox" autocomplete="off" name="<?=$dia?>[]" value="<?=$hora?>" data-hora="<?=$hora?>" data-dia="<?=$dia?>" checked>
                                            <span class="glyphicon glyphicon-ok"></span>
                                          </label>
                                        </div>
                                      </div>
                                    </td>
                                    <?php
                                      }
                                    ?>
                                  </tr>
                                  <?php
                                  }
                                  ?>
                              </tbody>
                          </table>
                      </div>
                    </div>
                </form>                 
            </div>             
            <div class="modal-footer"> 
                <button type="button" class="btn btn-default" id="btn-cerrar">Cerrar</button>                 
                <button type="button" class="btn btn-primary" id="btn-guardar">Guardar</button>                 
            </div>             
        </div>         
    </div>     
</div>

<script type="text/javascript">
  var color = '#000000';
  var drawingManager;
  var editingPolygon;
  var map;
  var infoWindow;
  $(function(){

    map = new google.maps.Map(document.getElementById('map-canvas'), {
      center: {lat: <?=$LAT?>, lng: <?=$LNG?>},
      zoom: 13, 
      mapTypeId: 'hybrid'
    });

    drawingManager= new google.maps.drawing.DrawingManager({
      drawingControl: false,
      markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
      polygonOptions: {
        draggable: false,
        editable: false,
        zIndex: 2
      }
    });
    drawingManager.setMap(map);

    var toolbox = document.createElement('div');
    toolbox.id='toolbox';
    toolbox.index=1;

    $(toolbox)
      .append($('<div class="btn-group" data-toggle="buttons">'))
      .append($('<button class="btn btn-sm btn-default" onclick="mover();"><i class="fa fa-hand-rock-o fa-2x" aria-hidden="true"></i></button>'))
      .append($('<button class="btn btn-sm btn-default" onclick="crear_poligono();"><i class="fa fa-paint-brush fa-2x" aria-hidden="true"></i></button>'))
      .append($('<button class="btn btn-sm btn-default" onclick="crear_marcador();"><i class="fa fa-map-marker fa-2x" aria-hidden="true"></i></button>'))
      // .append($('<button class="btn btn-sm btn-default" onclick="borrar_objeto();"><i class="fa fa-eraser fa-2x" aria-hidden="true"></i></button>'))
      .append($('<button id="btn_open_cp" class="btn btn-sm btn-default" onclick="seleccionar_color(this);"><i class="fa fa-square fa-2x" aria-hidden="true"></i></button>'))
      .append($('<div id="colorPicker" onclick="setColor();" style="display: none">'))
      .addClass("toolbox");

    map.controls[google.maps.ControlPosition.LEFT_CENTER].push(toolbox);

    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
      if(event.type == 'polygon'){
        editingPolygon = event.overlay;
        console.log(editingPolygon);

        var vertices = event.overlay.getPath();
        // poligono = [];
        $("#form-geocerca").find('input[type=hidden]').remove();

        for (var i =0; i < vertices.getLength(); i++) {
          var xy = vertices.getAt(i);
          // poligono.push({latitud: xy.lat(), longitud: xy.lng()});
          $("#form-geocerca").append(
            $('<input type="hidden" name="poligono[]" value="'+xy.lat()+';'+xy.lng()+'">')
          );
        }
        
        $("#modalCrearGeocerca").modal('show');
      }
    });

    let geocercas = [<?=($js_gc)?>];

    for(i=0; i<geocercas.length; i++){
      geocercas[i].setMap(map);
      geocercas[i].addListener('click', showArrays); 
    }

    infoWindow = new google.maps.InfoWindow;

    $("#btn-cerrar").on("click", function(){
      swal({
        title:'¿Desea descartar la geocerca?',
        text: 'Si presiona "Aceptar", a continuación se eliminará el polígono creado.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: true
      },function(isConfirm){ 
        if(isConfirm){
          if(typeof editingPolygon != 'undefined')
            editingPolygon.setMap(null);

          $("#modalCrearGeocerca").modal('hide');
        }
      });      
    });

    $("#btn-guardar").on("click", function(){
      let params = $("#form-geocerca").serializeArray();
      params.push({name: 'acc', value: 'crear'});

      $.post('ajax/geocercas/crud.php', params, function(data){
        if(data){
          if(data.status == 200){
            swal('Geocerca creada', data.mensaje, 'success');
          }
          else{
            swal('Error', data.mensaje, 'error');
          }
        }
      })
    });

  });

  function CustomControl(controlDiv, map) {

    // Set CSS for the control border
    var controlUI = document.createElement('div');
    controlUI.style.backgroundColor = '#ffffff';
    controlUI.style.borderStyle = 'none';
    controlUI.style.borderWidth = '1px';
    controlUI.style.borderColor = '#ccc';
    controlUI.style.height = '23.96px';
    controlUI.style.width = '23.96px';
    controlUI.style.marginTop = '5px';
    controlUI.style.marginLeft = '-6px';
    controlUI.style.paddingTop = '1px';
    controlUI.style.cursor = 'pointer';
    controlUI.style.textAlign = 'center';
    controlUI.title = controlDiv.title;
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior
    var controlText = document.createElement('div');
    controlText.style.fontFamily = 'Arial,sans-serif';
    controlText.style.fontSize = '16px';
    controlText.style.paddingLeft = '4px';
    controlText.style.paddingRight = '4px';
    controlText.style.marginTop = '0px';
    controlText.innerHTML = controlDiv.icon;
    controlUI.appendChild(controlText);

    // Setup the click event listeners
    google.maps.event.addDomListener(controlUI, 'click', function () {
        alert('Custom control clicked');
    });
  }

  function showArrays(event) {
    // Since this polygon has only one path, we can call getPath() to return the
    // MVCArray of LatLngs.
    var vertices = this.getPath();
    let od = this.otherData;
    console.log(this.otherData);
    editingPolygon = this;

    var contentString = '<div class="nombre-geocerca">'+od.nombre+'</div><br/>';
    contentString += '<div class="btn-group" role="group" aria-label="...">';
    contentString += '<button class="btn btn-sm btn-danger" onclick="eliminar();"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;&nbsp;Eliminar</button>';
    contentString += '<button class="btn btn-sm btn-primary" onclick="cambiar_color();"><i class="fa fa-square" aria-hidden="true"></i>&nbsp;&nbsp;Color</button>';
    contentString += '<button class="btn btn-sm btn-primary" onclick="habilitar_mover();"><i class="fa fa-arrows" aria-hidden="true"></i>&nbsp;&nbsp;Mover</button>';
    contentString += '<button class="btn btn-sm btn-primary" onclick="habilitar_modificar();"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>&nbsp;&nbsp;Forma</button>';
    contentString += '<button id="actualizar_geocerca" class="btn btn-sm btn-success actualizar_geocerca" onclick="actualizar_geocerca();" disabled><i class="fa fa-save" aria-hidden="true"></i>&nbsp;&nbsp;Guardar</button>';
    contentString += '</div>';

    if(od.icono != ''){
      contentString += '<img src="'+od.icono+'" class="icono-marcadorx48" />';
    }

    // Iterate over the vertices.
    for (var i =0; i < vertices.getLength(); i++) {
      var xy = vertices.getAt(i);
      contentString += '<br>' + 'Coordenadas ' + i + ':<br>' + xy.lat() + ',' +
          xy.lng();
    }

    // Replace the info window's content and position.
    infoWindow.setContent(contentString);
    infoWindow.setPosition(event.latLng);

    infoWindow.open(map);
  }

  function mover(){
    drawingManager.setDrawingMode(null);
  }

  function crear_poligono(){
    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
  }

  function crear_marcador(){
    drawingManager.setDrawingMode(google.maps.drawing.OverlayType.MARKER);
  }

  function seleccionar_color(o){
    let display = $("#colorPicker").css("display");

    if(display == 'none'){
      $("#colorPicker").show();
      $("#colorPicker").jqxColorPicker({ width: 300, height: 300 });
    }
    else{
      $("#colorPicker").hide();      
    }
  }

  function setColor(){
    var color = $("#colorPicker").jqxColorPicker('getColor');
    let btn = $("#btn_open_cp");
    var hex = color.hex;
    var rgb = color.r + "," + color.g + "," + color.b;
    btn.find("i").css("color", "#"+hex);
    $("#color").val("#"+hex);
    drawingManager.setOptions({ 
      polygonOptions: { 
        fillColor: '#'+hex, 
        strokeColor: '#'+hex,
        draggable: true,
        editable: true,
        zIndex: 2
      }
    });
  }

  /* cómo hacerlo ??? */
  function eliminar(){
    if(editingPolygon) editingPolygon.setMap(null);
    infoWindow.setMap(null);
  }

  function cambiar_color(){
    if(editingPolygon) editingPolygon.setOptions({fillColor: '#FFFFFF'});
    infoWindow.setMap(null);    
  }

  function habilitar_mover(){
    if(editingPolygon) editingPolygon.setDraggable(true);
    infoWindow.setMap(null);
  }

  function habilitar_modificar(){
    if(editingPolygon) editingPolygon.setEditable(true);
    infoWindow.setMap(null);
  }

  function actualizar_geocerca(){
    //Guardar todos los cambios realizados a dicha geocerca.
  }
</script>