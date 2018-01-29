<!DOCTYPE HTML>
<html lang="en">
<head>
 <title id='Description'>Chart with Range Column Series</title>
 <link rel="stylesheet" href="assets/jqwidgets/styles/jqx.base.css" type="text/css" />
 <script type="text/javascript" src="assets/jqwidgets/scripts/jquery-1.11.1.min.js"></script>
 <script type="text/javascript" src="assets/jqwidgets/jqxcore.js"></script>
 <script type="text/javascript" src="assets/jqwidgets/jqxdata.js"></script>
 <script type="text/javascript" src="assets/jqwidgets/jqxdraw.js"></script>
 <script type="text/javascript" src="assets/jqwidgets/jqxchart.core.js"></script>
<style>
 .redLabel { fill: #FF0000; color: #FF0000; font-size: 11px; font-family: Verdana; }
 .greenLabel { fill: #89A54E; color: #89A54E; font-size: 11px; font-family: Verdana; }
 .jqx-chart-axis-text,
 .jqx-chart-label-text, 
 .jqx-chart-tooltip-text, 
 .jqx-chart-legend-text{ fill: #333333; color: #333333; font-size: 8px; font-family: Arial; }
 .jqx-chart-axis-description { fill: #555555; color: #555555; font-size: 11px; font-family: Verdana; }
 .jqx-chart-title-text { fill: #111111; color: #111111; font-size: 14px; font-weight: bold; font-family: Verdana; }
 .jqx-chart-title-description { fill: #111111; color: #111111; font-size: 12px; font-weight: normal; font-family: Verdana; }
</style>    
    
<script type="text/javascript">
 var data;
 
 $(document).ready(function () {
            
  function redondeo(numero, decimales){
   var flotante = parseFloat(numero);
   var resultado = Math.round(flotante*Math.pow(10,decimales))/Math.pow(10,decimales);
   return resultado;
  }	
			
  fnLabelsClass = function (value, itemIndex, serie, group) { return 'greenLabel'; }
            
  fnLabelsBorderColor = function (value, itemIndex, serie, group) { return '#89A54E'; }
						
  $.ajax({ url: "ajax/ajax_pif_data.php", dataType: "json" }).done(function(data){
        if(data[data.length-1].presion == 'fecha'){
              var fecha = data[data.length-1];
              // $("#titulo").html('DESDE <strong>'+fecha.min+'</strong>  HASTA <strong>'+fecha.max+'</strong>');
              var nuevoArr = data.filter(function(o){
                  return o.presion !== 'fecha';
              });

              data = nuevoArr;
        }
   var toolTipCustomFormatFn = function (value, itemIndex, serie, group, categoryValue, categoryAxis) {
    var dataItem = data[itemIndex];
    var porc_del_total=154;            
    var cant_neum=dataItem.max;            
    var porc_del_total=(cant_neum*100)/154;			              
    var num_red=redondeo(porc_del_total, 2);
    return '<DIV style="text-align:left"><b>Desviación PIF: ' +
          categoryValue + '</b><br />Cantidad de neumáticos: ' +
          dataItem.max + '<br />Peso sobre el total de neumáticos:'+num_red+'%</DIV>';        
   };
        
   // prepare jqxChart settings
   var settings = {
    title: "Histograma de presiones",
    description: "",
    enableAnimations: true,
    showLegend: true,
    padding: { left: 5, top: 5, right: 5, bottom: 5 },
    titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
    enableCrosshairs: true,
    source: data,
    colorScheme: 'scheme05',
    xAxis:{ dataField: 'presion', unitInterval: 1, gridLines: { step: 1 } },
    valueAxis:{
     minValue: 0,
     maxValue: 100,
     unitInterval: 5,   
     alternatingBackgroundColor: '#E5E5E5',
     alternatingBackgroundColor2: '#F5F5F5',
     alternatingBackgroundOpacity: 0.5,   
     title: { text: 'Cantidad de neumáticos' },
     labels: { horizontalAlignment: 'right', formatSettings: { sufix: '' } }
    },
    seriesGroups:[
     {
      type: 'rangecolumn',
      columnsGapPercent: 50,
      toolTipFormatFunction: toolTipCustomFormatFn,
      series: [{ dataFieldTo: 'max', displayText: 'Desviación sobre valor PIF', dataFieldFrom: 'min', opacity: 1 }]
     },
     {
      type: 'spline',
      toolTipFormatFunction: toolTipCustomFormatFn,
      series: [{ dataField: 'avg', displayText: 'Porcentaje sobre el total de neumáticos', opacity: 1, lineWidth: 2, 
       labels:{ visible: true, 'class': fnLabelsClass, backgroundColor: 'white', padding: {left: 5, right: 5, top: 1, bottom: 1}, borderColor: fnLabelsBorderColor, backgroundOpacity: 0.7, borderOpacity: 0.7 }             
      }]
     }
    ]
   };
   // setup the chart
   $('#chartContainer').jqxChart(settings);
  });			
 });
</script>
</head>
<!-- <h4 id="titulo">DESDE <strong>-</strong>  HASTA <strong>-</strong></h4> -->
<center>
 <div id='chartContainer' style="width:90%; height:500px;"></div>
</center>
<script languaje="text/javascript">
 var inter = 0;
 function tbl(){
  var tabla = $("#tblChart");
  if(tabla.length>0){
   if(tabla.css("width")!="100%"){
    $("#tblChart").css("width","100%").css("align","center");
    $("div.chartContainer").css("width","100%");
    clearInterval(inter);
   }
  }
 }

 inter = setInterval(tbl, 500);
</script>

</html>