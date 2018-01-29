<?php
  require 'autoload.php';

  $acc = new Acceso();
  $neu = new Neumatico();

  $db = DB::getInstance();

  $sql        = "SELECT * FROM uman_plantilla ORDER BY MARCA ASC;";
  $plantillas = $db->query($sql);
  $plantillas = $plantillas->results();

  $marcas     = $neu->obtenerMarcas();
  $modelos    = $neu->obtenerModelos();
  $dimensiones= $neu->obtenerDimensiones();
  $compuestos = $neu->obtenerCompuestos();

  $TITULO     = $module_label; //'Neumáticos';
  $SUBTITULO  = '';
?>
<!-- Typeahead -->
<script type="text/javascript" src="assets/typeahead/typeahead.bundle.min.js"></script> 

<style>
  <?php include_once("assets/css/detalle-equipo.css") ?>
  <?php include_once("assets/css/funky-radio.css") ?>
  .neum-ref{
    width: 100%;
    height: 350px;
    margin: auto;
    background-size: contain !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
  }
  .tt-menu {
    width: inherit;
    margin: 12px 0;
    padding: 8px 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 8px;
      -moz-border-radius: 8px;
            border-radius: 8px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
      -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
  }

  .tt-suggestion {
    padding: 3px 20px;
    font-size: 18px;
    line-height: 24px;
  }

  .tt-suggestion:hover {
    cursor: pointer;
    color: #fff;
    background-color: #0097cf;
  }

  .tt-suggestion.tt-cursor {
    color: #fff;
    background-color: #0097cf;

  }

  .tt-suggestion p {
    margin: 0;
  }

  .gist {
    font-size: 14px;
  }
  .umbrales{
    font-size: 90%;
    font-weight: bold;
  }
  .eje-marca{
    margin: -3px auto -4px auto;
    padding: 0;
  }
  .eje-marca::after{
    content: "\A";
    white-space: pre;
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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevoNeumatico"><i class="fa fa-plus" aria-hidden="true"></i> Agregar</button>
      </div>
    </div>
  </div>

  <!-- CONTENIDO -->
  <div id="contenido">
    <table class="table table-hover" id="tabla-datos">
      <thead>
        <th></th>
        <th>N&deg; Serie</th>
        <th>N&deg; Fuego</th>
        <th>Marca</th>
        <th>Modelo</th>
        <th>Estado</th>
      </thead>
      <tfoot>
        <td></td>
        <td><input class="foot-filter" style="width:100%" placeholder="ID" data-index="1" /></td>
        <td><input class="foot-filter" style="width:100%" placeholder="NÚM. FUEGO" data-index="2" /></td>
        <td><input class="foot-filter" style="width:100%" placeholder="MARCA" data-index="3" /></td>
        <td><input class="foot-filter" style="width:100%" placeholder="MODELO" data-index="4" /></td>
        <td><input class="foot-filter" style="width:100%" placeholder="ESTADO" data-index="5" /></td>
      </tfoot>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade modal-lg" tabindex="-1" role="dialog" id="editarNeumatico">
  <div class="modal-dialog" role="document" style="min-width:70%">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ver / Modificar neumático</h4>
      </div>
      <div class="modal-body">
        <div class="loader show center-block"></div>
        <form action="ajax/neumaticos/crud.php" method="POST" id="form2">
          <input type="hidden" id="id_db" name="id">
          <input type="hidden" name="modo" value="editar">
          
          <div class="row">

            <!-- IMAGEN REFERENCIAL NEUMÁTICO -->
            <div class="<?php Core::col(5) ?>">
              <div class="neum-ref edt"></div>
            </div>

            <!-- FORMULARIO -->
            <div class="<?php Core::col(7) ?>">
	    <!-- 17_01_2018 CT - Se comenta ID del neumático  -->
             <!-- <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>ID Neum&aacute;tico:</b>
                </div>
                <div class="col-sm-6">
                  <strong><span id="ID_editar" class="form-control"></span></strong>
                </div>
              </div>
              -->

              <div class="row small">&nbsp;</div>
              
              <!-- Número de Fuego -->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>N&deg; Fuego:</b>
                </div>
                <div class="col-sm-6">
		<!-- 17_01_2018 CT - Se elimina validación numérica  -->
                  <!--<input type="text" class="form-control" placeholder="Número de Fuego" name="numero_fuego" id="numero_fuego_editar" onkeypress="return permite(event, 'num');">-->
                  <input type="text" class="form-control" placeholder="Número de Fuego" name="numero_fuego" id="numero_fuego_editar">
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <!-- Num Identi / Núm de Serie-->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>N&deg; Serie:</b>
                </div>
                <div class="col-sm-6">
                  <input type="text" class="form-control" placeholder="Número de Serie" name="t1" id="numidenti_editar" onkeypress="return permite(event, 'num_car');">
                </div>
              </div>

              <div class="row small">&nbsp;</div>
              
              <!-- Marca -->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>Marca:</b>
                </div>
                <div class="col-sm-6">
                  <select name="marca" class="form-control selectpicker" id="marca_editar" data-live-search="true">
                    <!-- 17_01_2018 CT - Se elimina opción "todas" en marca  -->
		    <!--<option value="">Todas</option>-->
                    <?php
                      foreach($marcas as $marca) {
                        echo('<option value="'.$marca->nombre.'">'.$marca->nombre.'</option>');
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <!-- Modelo -->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>Modelo:</b>
                </div>
                <div class="col-sm-6">
                  <select name="modelo" id="modelo_editar" class="form-control selectpicker" data-live-search="true">
                    <option value="">Todos</option>
                    <?php
                      foreach($modelos as $modelo){
                        echo "<option value=\"{$modelo->nombre}\">{$modelo->nombre}</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <!-- Dimensión -->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>Dimension:</b>
                </div>
                <div class="col-sm-6">
                  <select name="dimension" class="form-control selectpicker" id="dimension_editar" data-live-search="true">
                    <option value="">Todas</option>
                    <?php
                      foreach($dimensiones as $dimension){
                        echo "<option value=\"{$dimension->dimension}\">{$dimension->dimension}</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <!-- Compuesto -->
              <div class="row">
                <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
                  <b>Compuesto:</b>
                </div>
                <div class="col-sm-6">
                  <select name="compuesto" id="compuesto_editar" class="form-control selectpicker" data-live-search="true">
                    <option></option>
                    <?php
                      foreach($compuestos as $compuesto){
                        echo "<option value=\"{$compuesto->nombre}\">{$compuesto->nombre}</option>";
                      }
                    ?>
                  </select>
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <!-- Plantilla -->
              <div class="row">
                <div class="col-sm-6" style='text-align: left; font-weight: 600'>
                  <b>Plantilla:</b>
                </div>
                <div class="col-sm-6">
                  <div id="plantilla_editar">
                  </div>
                </div>
              </div>

              <div class="row small">&nbsp;</div>

              <div class="row">
                <div class="col-sm-6" style='text-align: left; font-weight: 600'>
                  <b>Sensor:</b>
                </div>
                <div class="col-sm-6">
                  <span id="sensor_editar" class="form-control"></span>
                </div>
              </div>
            
            </div>

          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary guardar-neumatico" data-form="form2" data-dismiss="modal">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="nuevoNeumatico">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">+ Nuevo neum&aacute;tico</h4>
      </div>
      <div class="modal-body">
        <form action="ajax/neumaticos/crud.php" method="POST" id="form1">
          <input type="hidden" name="modo" value="crear">
          
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>N&deg; Serie:</b>
            </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" placeholder="Número de Serie" name="t1" onkeyup="this.value=this.value.toUpperCase()">
            </div>
          </div>

          <div class="row small">&nbsp;</div>

          <!-- Número de fuego -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>N&deg; Fuego:</b>
            </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" placeholder="Número de Fuego" name="numero_fuego" oninput="">
            </div>
          </div>

          <div class="row small">&nbsp;</div>
          
          <!-- Marca -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>Marca:</b>
            </div>
            <div class="col-sm-6">
              <select name="marca" class="form-control selectpicker" data-live-search="true">
                <option></option>
                <?php
                  foreach($marcas as $marca) {
                    echo('<option value="'.$marca->nombre.'">'.$marca->nombre.'</option>');
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row small">&nbsp;</div>

          <!-- Modelo -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>Modelo:</b>
            </div>
            <div class="col-sm-6">
              <select name="modelo" class="form-control selectpicker" data-live-search="true">
                <option></option>
                <?php
                  foreach($modelos as $modelo){
                    echo "<option value=\"{$modelo->nombre}\">{$modelo->nombre}</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row small">&nbsp;</div>

          <!-- Dimensión -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>Dimensión:</b>
            </div>
            <div class="col-sm-6">
              <select name="dimension" class="form-control selectpicker" data-live-search="true">
                <option></option>
                <?php
                  foreach($dimensiones as $dimension){
                    echo "<option value=\"{$dimension->dimension}\">{$dimension->dimension}</option>";
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row small">&nbsp;</div>

          <!-- Compuesto -->
          <div class="row">
            <div class="col-sm-6 obligatorio" style='text-align: left; font-weight: 600'>
              <b>Compuesto:</b>
            </div>
            <div class="col-sm-6">
              <select name="compuesto" class="form-control selectpicker" data-live-search="true">
                <option></option>
                <?php
                  foreach($compuestos as $compuesto){
                    echo "<option value=\"{$compuesto->nombre}\">{$compuesto->nombre}</option>";
                  }
                ?>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary guardar-neumatico" data-form="form1" data-dismiss="modal">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
	var $rows = $('#table tr');
  var table = undefined;

  var modelos; 

  function asignarImagenNeumatico(url, obj){
    if(url!=''){
      $.ajax({
        url: url,
        type: 'HEAD',
        error: function(){
          obj.css("background","url(assets/img/neumaticos/acme.png)");
        },
        success: function(){
          obj.css("background","url("+url+")");
        }
      });
    }
    else obj.css("background","url(assets/img/neumaticos/acme.png)");
  }

  $(function(){
    
    table = $("#tabla-datos").DataTable({
      dom: 'rtip',
      responsive: true,
      ajax: 'ajax/neumaticos/neumaticos.php',
      searching: true,
      order: [0, 'asc'],
      pagingType: "full_numbers",
      autoWidth: false,
      paging: true,
      info: false,
      columns:[
        { data: "btn" },
        { data: "numidenti" },
        { data: "numfuego" },
        { data: "marca" },
        { data: "modelo" },
        { data: "estado" }
      ],
      language: {
        url: "assets/datatables-1.10.15/lang/Spanish.json",
        loadingRecords: '<div class="loader show"></div>'
      }
    });

    
    $("#modelo_editar").bind("typeahead:change, typeahead:autocomplete, typeahead:select, change", function(ev, suggestion){
      var marca = $("#marca_editar").val();
      var modelo = suggestion; 
      if(modelo==undefined) modelo = $('#modelo_editar').typeahead('val');
      var url = '';
      if(marca != '' && modelo != '') url = ('assets/img/neumaticos/' +marca + '-' + modelo + '.png').toLowerCase();      
      asignarImagenNeumatico(url, $("div.neum-ref.edt"));
    });

    $(".foot-filter").on( 'keyup change', function () {
        table.columns($(this).data('index')).search( this.value ).draw();
    });

    $("#editarNeumatico").on("show.bs.modal", function(event){      
      var loader = $(this).find("div.loader");
      loader.removeClass("hidden").addClass("show");
      var id_neumatico = $(event.relatedTarget).data("id");
      var id_plantilla = $(event.relatedTarget).data("plantilla");
      if(!isNaN(id_neumatico)){
        $.post('ajax/neumaticos/crud.php', {id_neumatico: id_neumatico, modo: 'obtener'}, function(json){
          loader.removeClass("show").addClass("hidden");
          if(json){
            json = json[0];
            $("#id_db").val(json.ID_NEUMATICO);
            $("#ID_editar").html(json.ID_NEUMATICO);
            $("#numero_fuego_editar").val(json.NUMEROFUEGO);
            $("#numidenti_editar").val(json.NUMIDENTI);
            var url = '';
            if(json.MARCA != '' && json.MODELO != '') url = ('assets/img/neumaticos/' + json.MARCA + '-' + json.MODELO + '.png').toLowerCase();
            asignarImagenNeumatico(url, $("div.neum-ref.edt"));
            $("#modelo_editar").selectpicker('val',json.MODELO);
            $("#dimension_editar").selectpicker('val', json.DIMENSION);
            $("#compuesto_editar").selectpicker('val',json.COMPUESTO);
            $("#marca_editar").selectpicker('val', json.MARCA);
            if(json.P_EJE == null) $("#plantilla_editar").html('Sin plantilla');
            else{
              $("#plantilla_editar").html(
                '<div class="option small">'+
                  '<div class="eje-marca">'+
                    'Eje: '+json.P_EJE+' '+
                    (json.P_MARCA!='null'?'':'| Marca: '+json.P_MARCA)+' '+
                    (json.P_DIMENSION!='null'?'':'| Dim.:'+json.P_DIMENSION)+' '+
                  '</div>'+
                  '<div class="umbrales">'+
                    'T&deg; Máx: '+json.P_TEMPMAX+' '+
                    '| P&deg; Mín: '+json.P_PRESMIN+' '+
                    '| P&deg; Máx: '+json.P_PRESMAX+
                  '</div>'+
                '</div>');
            }
            
            if(json.S_ID_SENSOR){
              var media = '<div class="media"><div class="media-left">'+
                '<img src="assets/img/sensor_'+json.S_TIPO.toLowerCase()+'.png" class="thumbnail center-block" style="max-height:45px" /></div>'+
                '<div class="media-body">'+
                '<h4 class="media-heading">Número : '+(json.S_CODESENSOR ? json.S_CODESENSOR : '')+'</h4>'+
                '<h5 class="media-heading">Tipo : '+(json.S_TIPO ? json.S_TIPO : '')+'</h5>'+
                '</div></div>';
              $("#sensor_editar").html(media);
              $("#sensor_editar").removeClass("form-control");
            }
            else{
              $("#sensor_editar").html('SIN ASIGNAR');
              $("#sensor_editar").addClass("form-control");
            }
          }
          else{

          }
        });
      }
    });

    $("button.guardar-neumatico").click(function(){
      var frm = $(this).data("form");
      var mdl = $(this).data("modal");
      // var npt = $("#"+frm).find("input, select");
      // var params = {compuesto:"", dimension:"", id:"", marca:"", modelo:"", modo:"", numero_fuego:"", t1:""};
      var params = $("#"+frm).serializeArray();

      // params.push({name: 'marca', value: $("select[name='marca']").selectpicker('val')});
      // params.push({name: 'dimension', value: $("select[name='dimension']").selectpicker('val')});
     //17_01_2018 CT - Se elimina impresión de parámetros ya que arroja errores al guardar
     // console.log(params);

      var url = $("#"+frm).attr("action");

      $.post(url, params, function(json){
        if(json){
          if(json.type == 'success'){
            table.ajax.reload();
          }
          json.title = "<?=$module_label?>";
          swal(json);
        }
      });

      console.log(params);
    });

    $("#nuevoNeumatico").on("show.bs.modal", function(evt){
      $("#form1 input[type=text]").val('');
      $("#form1 select").selectpicker('val', '');
    });
  });
// 17_01_2018 CT - Se repara confirmación al borrar un neumático, ya que no sale de la lista si no se recarga la pagina web 
  function eliminar(id){
    swal({
      title: "¿Está seguro/a?",
      text: "Si continúa, se eliminará el neumático seleccionado.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "Si, borrar",
      cancelButtonText: "Cancelar",
      closeOnConfirm: true
    },
     function(isConfirm){
            if (isConfirm) {
                $.post("ajax/neumaticos/crud.php", {id: id, modo: 'eliminar'}, function (json) {
                    if (json) {
                        if (json.type == "success") {
                            // table.ajax.url('ajax/obtener-neumaticos.php').load();
                           // swal({title: 'Datos almacenados', text: json.response, type: 'success', html: true,});
                            //table.ajax.reload(null, true);
                            table.ajax.reload();
                        }else{
                            swal(json);
                        }
                    }
                });
            }
    });
  }

  var n=1; 
  function add() {
  	
    n++;  
    if(n<7){
    	
    pepe = document.getElementById('tabla');  
    fila = document.createElement('tr');    
    fila.id='contenedor'+n;  
    celda = document.createElement('td');
    celda.setAttribute("align","center"); 
    fila.appendChild(celda); 
    
    code=document.createElement('input'); 
    code.type='text'; 
    code.name='t'+n; 
    code.id='t'+n;   
    code.size='8';
    code.className='form-control';
    code.placeholder='ID Neumatico';
    code.onkeypress = function() {return permite(event,'num_car')};    
    code.onkeyup = function() {this.value=this.value.toUpperCase()};    
    
    celda.appendChild(code); 
     
    celda = document.createElement('td'); 
    celda.setAttribute("align","center"); 
    fila.appendChild(celda);       
    
    cant=document.createElement('select');
    cant.name='s'+n; 
    cant.className='form-control';
    cant.id='s'+n;   

    cant.options[0] = new Option('Bridgestone');
    cant.options[1] = new Option('Michelin'); 
    cant.options[2] = new Option('Yokohama'); 
    cant.options[3] = new Option('Titan');
    cant.options[4] = new Option('GoodYear');
    cant.options[5] = new Option('Firestone');
    cant.options[6] = new Option('Resource');
    cant.options[7] = new Option('Superhawk');
    cant.options[8] = new Option('Torch');
    cant.options[9] = new Option('Magna Tyres');
    cant.options[9] = new Option('BELSCHINA');
    
    celda.appendChild(cant);
    
    celda2 = document.createElement('td'); 
    celda2.setAttribute("align","center"); 
    fila.appendChild(celda2);  
     
    cant2=document.createElement('input');
    //<input type="button" value="Adicionar" onclick="add()" id="b1" />
    cant2.id='b'+n;
    cant2.name='b'+n;
    cant2.type='button';
    cant2.className='btn btn-success';
    cant2.value='Adicionar';
    cant2.onclick = function() {add()};
    
    var a=n-1;
    var a="b"+a;
    
    //alert('109');
        
    document.getElementById(a).style.visibility = "hidden";
    //celda.appendChild(cant); //ok interno-canister
    celda2.appendChild(cant2); 
     
    pepe.appendChild(fila); 
    }
    
    if(n==7){
    	n=6;
    }
  } 

  function permite(elEvento, permitidos) {
  	
    // Variables que definen los caracteres permitidos
    var numeros = "0123456789";
    var caracteres = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var numeros_caracteres = numeros + caracteres;
    var teclas_especiales = [8, 37, 39, 46];
    // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha 
   
    // Seleccionar los caracteres a partir del parámetro de la función
    switch(permitidos) {
      case 'num_car':
        permitidos = numeros_caracteres; break;
      case 'num':
        permitidos = numeros; break;
      case 'car':
        permitidos = caracteres; break;
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

  function validacion() {	
  	
    console.log('154 validacion');	

    var i;
    var submit=true;
    valor = document.getElementById("t1").value; 
    

    if (valor.length == '' || /^\s+$/.test(valor)) {
      // Si no se cumple la condicion...
      submit=false;
    }

    for ( i = 2 ; i <= n ; i++ ) {
      valor = document.getElementById("t" + i).value;
      console.log(i);
      if(valor==''){
          // Si no se cumple la condicion...
          alert('[ERROR] Debe ingresar algo');    
          submit=false;
          break;
      } 
    }
    
    if ( submit ) {
      form2.submit();
    }
  }


  function modaleditar( id , numero_fuego , marca, modelo , dimension , compuesto , id_db , estado ) {  

    $("[data-toggle='popover']").popover('hide');
      document.getElementById("id_db").value                  = id_db;
      document.getElementById("ID_editar").value              = id;
      document.getElementById("marca_editar").value           = marca;
      document.getElementById("numero_fuego_editar").value    = numero_fuego;
      document.getElementById("modelo_editar").value          = modelo;
      document.getElementById("dimension_editar").value       = dimension;
      document.getElementById("compuesto_editar").value       = compuesto;

      if ( estado == "DISPONIBLE" ) {
        document.getElementById("borrar").href                  = "borrar.php?modo=neumaticos&id=" + id_db;
        document.getElementById("borrar").style.visibility      = "visible";
      } else {
        document.getElementById("borrar").style.visibility      = "hidden";
      }
      

      $('#cargasho').modal('toggle');
  }

</script>