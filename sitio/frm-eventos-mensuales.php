<?php
  require 'autoload.php';

  $acc = new Acceso();

  $m = intval(date("n"));

  $gen         = new General();
  $img_equipo  = $gen->getImagenesEquipo();

  $TITULO    = $module_label; //'Eventos Mensuales';
  $SUBTITULO = '';
?>
<style>
 <?php include_once("assets/css/detalle-equipo.css") ?>
 <?php include_once("assets/css/uman/tabla.css") ?>
   .col-xs-12{
    margin: 0;
    padding: 0;
  }
  .modal-dialog{
    width: 60%;
  }
  .tabla-datos td{
    text-align: center;
  }
  .tabla-datos th{
    text-align: center;
    font-weight: 800;
    font-size: 16px;
  }
  .btn span.glyphicon {
	  opacity: 0;				
  }
  .btn.active span.glyphicon {
    opacity: 1;				
  }
  .chart {
      min-width: 320px;
      max-width: 98%;
      height: 100%;
      margin: 0 auto;
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
  .tab-content{
    margin-top: -5px;
    background: white;
    border: thin solid #ddd;
    padding: 2px;
    border-radius: 4px;
  }
  #contenedor-datos{
    margin: -20px -10px auto -10px
  }
  @media (max-width: 425px){
    #contenedor-datos{
      margin: auto -20px;
      padding: 0;
    }
    .tabla-datos{
      margin: 0;
    }
    .modal-dialog{
      width: 98%;
    }
    .modal-body{
      -ms-transform: scale(0.9, 0.9); /* IE 9 */
      -webkit-transform: scale(0.9, 0.9); /* Safari */
      transform: scale(0.9, 0.9);
    }
    .modal-body table.tabla-resumen{
      margin: 0 0 0 -25px;
    }
    .tabla-datos td span{
      font-size: 14px; 
      font-weight: 800;
      text-align: center;
    }
  }
</style>

<!-- ESTILO TABLAS -->
<link rel="stylesheet" href="assets/css/uman/tabla.css">
<!-- ESTILO BASE ESTRUCTURA -->
<link rel="stylesheet" href="assets/css/uman/base.css">

<script src="assets/js/moment.js"></script>

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">
  <!-- TÍTULO DE PÁGINA -->
  <div class="cc-divider">
    <span class="titulo-pagina"><?=$TITULO?></span>
    <span class="subtitulo-pagina"><?=$SUBTITULO?></span>
  </div>
  <!-- MENÚ DE PÁGINA -->
  <div class="filtro-contenido">
    <div class="<?=Core::col(4,4)?>"></div>
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Año&nbsp;</label>
        <select class="selectpicker" id="year">
          <?php for($i=date('Y');$i>=2016;$i--){ ?>
          <option><?= $i ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    
    <div class="<?=Core::col(3,3,12,12)?>">
      <div class="frm-group">
        <label>Mes&nbsp;</label>
        <select class="selectpicker" id="month">
          <option value="1" <?=($m==1)?'selected':''?>>Enero</option>
          <option value="2" <?=($m==2)?'selected':''?>>Febrero</option>
          <option value="3" <?=($m==3)?'selected':''?>>Marzo</option>
          <option value="4" <?=($m==4)?'selected':''?>>Abril</option>
          <option value="5" <?=($m==5)?'selected':''?>>Mayo</option>
          <option value="6" <?=($m==6)?'selected':''?>>Junio</option>
          <option value="7" <?=($m==7)?'selected':''?>>Julio</option>
          <option value="8" <?=($m==8)?'selected':''?>>Agosto</option>
          <option value="9" <?=($m==9)?'selected':''?>>Septiembre</option>
          <option value="10" <?=($m==10)?'selected':''?>>Octubre</option>
          <option value="11" <?=($m==11)?'selected':''?>>Noviembre</option>
          <option value="12" <?=($m==12)?'selected':''?>>Diciembre</option>
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
    <div id="loader" class="loader center-block" style="display:none"></div>
    <div id="contenedor-datos">
  </div>
</div>


<script type="text/javascript">
  var meses = {1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril', 5: 'Mayo', 6: 'Junio',
  7: 'Julio', 8: 'Agosto', 9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'};
  var table = [];
  var img_eq = <?=json_encode($img_equipo)?>;
  var option = [];
  var styleEl;

  function addCSSRule(sheet, selector, rules, index) {
    if("insertRule" in sheet) {
      sheet.insertRule(selector + "{" + rules + "}", index);
    }
    else if("addRule" in sheet) {
      sheet.addRule(selector, rules, index);
    }
  }

  $(function(){

    $("#btn-ver").click(function(){
      var y = $("#year").val();
      var m = $("#month").val();

      $("#contenedor-datos").html('');      

      $.post('data-eventos-mensuales.php', {y: y, m: m}, function(json){
        table = [];
        var column = [];
        var filename = 'Eventos Mensuales - '+meses[m]+' '+y;
        if(json.type=='success'){
          //remove responsive class
            var tabla = $('<table class="table table-hover display tabla-datos">');
            var tbody = '';
            var thead;
            var tfoot;
          $.each(json.cant_dias, function(mes, dias){
            var hr1 = '<th colspan="'+(dias+2)+'"><h5>'+meses[mes]+' '+y+'</h5></th>';
            var hr2 = '<th id="head'+mes+'">Equipo</th><th>Posición</th>';
            thead = $('<thead>').append('<tr>'+hr1+'</tr>');
            tfoot = '<tfoot><tr>'+
                '<th><input type="text" class="foot-filter" data-tabla="'+(mes-1)+'" data-index="0" oninput="filtro(this);" placeholder="Equipo" style="width:100%" /></th>'+
                '<th><input type="text" class="foot-filter" data-tabla="'+(mes-1)+'" data-index="1" oninput="filtro(this);" placeholder="Posición" style="width:100%" /></th>';
            column.push({orderable: true});
            column.push({orderable: true});
            for(i=1; i<=dias; i++){
              hr2 += '<th>'+(i<10 ? '0'+ i : i)+'</th>';
              column.push({orderable: false});
              tfoot += '<th>&nbsp;</th>';
            }
            tfoot += '</tr></tfoot>';
            thead.append('<tr>'+hr2+'</tr>');

            $.each(json.equipos, function(eq,nom){
              $.each(json.posiciones[eq], function(j, pos){
                var img = '';
                if(img_eq[eq]){
                  if(img_eq[eq].DIV) img = img_eq[eq].DIV;
                }
                tbody += '<tr><td>'+img+' '+nom+'</td><td>'+pos.trim()+'</td>';
                
                for(dia=1; dia<=dias; dia++){
                  var total = 0;
                  if(json[mes]){
                    if(json[mes][dia]){
                      if(json[mes][dia][eq]){
                        if(json[mes][dia][eq][pos])
                          total = parseInt(json[mes][dia][eq][pos].total);
                      }
                    }
                  }

                  tbody += '<td><span class="'+(total==0?'cero':'')+'">'+total+'</span></td>';
                }

                tbody += '</tr>';
              });
            });
            tbody = '<tbody>'+tbody+'</tbody>';
          });

          thead.appendTo(tabla);
          $(tbody).appendTo(tabla);
          $(tfoot).appendTo(tabla);
          tabla.appendTo($("#contenedor-datos"));

          dt = tabla.DataTable({
            dom: 'Brtip',
            retrieve: true,
            searching: true,
            order: [0, 'asc'],
            // responsive: true,
            buttons: {
              buttons:[
                {
                  extend: 'excelHtml5',
                  text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Descargar</span>',
                  className: 'btn btn-info',
                  filename: filename,
                  title: filename
                }, 
                {
                  extend: 'print', 
                  text: '<i class="fa fa-print" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Imprimir</span>',
                  key:{
                    key: 'p',
                    altKey: true
                  },
                  className: 'btn btn-info',
                  title: filename
                },
                {
                  text: '<i class="fa fa-eye-slash" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Ocultar ceros</span>',
                  className: 'btn btn-info ocultar-ceros',
                  action: function(e, dt, node, config){
                    console.log(e);
                    if(node.text()==' Ocultar ceros'){
                      addCSSRule(document.styleSheets[0], ".cero", "display: none", 0);
                      node.html('<i class="fa fa-eye" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Mostrar ceros</span>');
                    }
                    else{
                      document.styleSheets[0].deleteRule(0);
                      node.html('<i class="fa fa-eye-slash" aria-hidden="true"></i><span class="hidden-xs hidden-sm"> Ocultar ceros</span>');
                    }
                  }
                }
              ]
            },
            columns: column,
            pagingType: "full_numbers",
            language: {
              url: "assets/datatables-1.10.15/lang/Spanish.json",
              loadingRecords: '<div class="loader show"></div>'
            }
          })
          .on("init.dt", function(){
            var wt = parseInt($("#contenedor-datos").css("width"));
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
              .css("margin", top+"px "+x+"px -45px "+left+"px")
              .css("width",wb+"px");
          });
        }
      });
    });
    /*
    $("#btn-ver2").click(function(){
      var y = $("#fecha").val();
      option = [];
      $.post('data-eventos-mensuales.php', {y: y}, function(json){
        $("#contenedor-datos").html('');
        table = [];
        var columns = [];
        var filename = [];
        if(json.type=='success'){
          var tab = $('<ul class="nav nav-tabs" role="tablist">');
          var tab_content = $('<div class="tab-content">');
          tab.appendTo($("#contenedor-datos"));
          tab_content.appendTo($("#contenedor-datos"));
      
          $.each(json.cant_dias, function(mes, dias){
            var column = [];
            filename.push('Eventos Mensuales - '+meses[mes]+' '+y);
            var active = (mes == <?=$m?>) ? 'active' : '';
            tab.append('<li role="presentation" class="'+active+'"><a href="#'+meses[mes]+'" aria-controls="'+meses[mes]+'" role="tab" data-tabla="'+(mes-1)+'" data-toggle="tab" onclick="fixElements(this);">'+meses[mes]+'</a></li>');
            var tabpanel = $('<div role="tabpanel" class="tab-pane fade in '+active+'" id="'+meses[mes]+'">');
            var hr1 = '<th colspan="'+(dias+2)+'"><h5>'+meses[mes]+'</h5></th>';
            var hr2 = '<th id="head'+mes+'">Equipo</th><th>Posición</th>';
            var tabla = $('<table class="table table-hover display responsive tabla-datos">');
            var thead = $('<thead>').append('<tr>'+hr1+'</tr>');
            var tbody = '';
            var tfoot = '<tfoot><tr>'+
                '<th><input type="text" class="foot-filter" data-tabla="'+(mes-1)+'" data-index="0" oninput="filtro(this);" placeholder="Equipo" style="width:100%" /></th>'+
                '<th><input type="text" class="foot-filter" data-tabla="'+(mes-1)+'" data-index="1" oninput="filtro(this);" placeholder="Posición" style="width:100%" /></th>';
            column.push({orderable: true});
            column.push({orderable: true});
            for(i=1; i<=dias; i++){
              hr2 += '<th>'+(i<10 ? '0'+ i : i)+'</th>';
              column.push({orderable: false});
              tfoot += '<th>&nbsp;</th>';
            }
            tfoot += '</tr></tfoot>';
            thead.append('<tr>'+hr2+'</tr>');

            $.each(json.equipos, function(eq,nom){
              $.each(json.posiciones[eq], function(j, pos){
                var img = '';
                if(img_eq[eq]){
                  if(img_eq[eq].DIV) img = img_eq[eq].DIV;
                }
                tbody += '<tr><td>'+img+' '+nom+'</td><td>'+pos.trim()+'</td>';
                
                for(dia=1; dia<=dias; dia++){
                  var total = 0;
                  if(json[mes]){
                    if(json[mes][dia]){
                      if(json[mes][dia][eq]){
                        if(json[mes][dia][eq][pos])
                          total = parseInt(json[mes][dia][eq][pos].total);
                      }
                    }
                  }

                  tbody += '<td>'+total+'</td>';
                }

                tbody += '</tr>';
              });
            });

            tbody = '<tbody>'+tbody+'</tbody>';

            thead.appendTo(tabla);
            $(tbody).appendTo(tabla);
            $(tfoot).appendTo(tabla);
            tabla.appendTo(tabpanel);
            tabpanel.appendTo(tab_content);

            table.push(tabla);
            columns.push(column);            
          });

          for(i=0; i<table.length; i++){
            option.push({
              dom: 'Brtip',
              retrieve: true,
              searching: true,
              order: [0, 'asc'],
              responsive: false,
              buttons: {
                buttons:[
                  {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Descargar',
                    className: 'btn btn-info',
                    filename: filename[i],
                    title: filename[i]
                  }, 
                  {
                    extend: 'print',
                    text: '<i class="fa fa-print" aria-hidden="true"></i> Imprimir',
                    key:{
                      key: 'p',
                      altKey: true
                    },
                    className: 'btn btn-info'
                  }
                ]
              },
              columns: columns[i],
              pagingType: "full_numbers",
              language: {
                url: "assets/datatables-1.10.15/lang/Spanish.json",
                loadingRecords: '<div class="loader show"></div>'
              }
            });
            table[i] = table[i].DataTable(option[i])
            .on("init.dt", function(){
              var wt = $("div.tab-pane.fade.in.active").css("width");
              var x = parseInt(wt) - 200 ;
              
              $("div.dt-buttons.btn-group").each(function(i,o){
                $(o).css("margin", "10px "+x+"px -45px auto");
              });
              $("td").css("font-size", "75%");
              $("th").css("font-size", "80%");
            });
          }
        }
      });
    });
    */
  });

  function filtro(o){
    // var i = $(o).data("tabla");
    dt.columns($(o).data('index')).search( o.value ).draw();
  }

  // function cargarMes(o){
  //   var m = $(o).data("mes");
  //   $.post('data-eventos-mensuales.php', {y: y, m: m}, function(json){
  //   });
  // }

  // function fixElements(o){
  //   // var cls = o.parentNode.className;
  //   var idx = $(o).data("tabla");

  //   var wt = $("div.tab-pane.fade.in.active").css("width");
  //   var thistable = $("div.tab-pane.fade.in.active").find("table");
  //   thistable.css("width", wt);
  //   new $.fn.dataTable.Responsive( table[idx] );
  //   table[idx].responsive.rebuild();
  //   table[idx].responsive.recalc();
  // }
</script>