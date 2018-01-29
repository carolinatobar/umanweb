<link rel="stylesheet" href="assets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="assets/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="assets/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="assets/jqwidgets/jqxslider.js"></script>
<script type="text/javascript">
 $(document).ready(function () {
 
 var counter = 0;
 
 $('#bloque_ayuda').css('background-color', '#cccccc');
 $('#bloque_ayuda_vertical').css('background-color', '#cccccc');
 $('#bloque_fondo').css('background-color', '#696969');
 
 
 $('#bloque_fondo').hide();
 
 $('#Nivel_temperatura').jqxSlider({ min: 0, max: 120, ticksFrequency: 5, value: 0, step: 1});
 $('#Nivel_inflado_frio').jqxSlider({ min: 0, max: 150, ticksFrequency: 5, value: 0, step: 1});
 $('#Nivel_inflado_caliente').jqxSlider({ min: 0, max: 160, ticksFrequency: 5, value: 0, step: 1});
 
 var tema='fresh';
 
 $('#Nivel_temperatura').jqxSlider({ mode: 'default',disabled:true,theme:tema,showTicks: false });
 $('#Nivel_inflado_frio').jqxSlider({ mode: 'default',disabled:true,theme:tema,showTicks: false });
 $('#Nivel_inflado_caliente').jqxSlider({ mode: 'default',disabled:true,theme:tema,showTicks: false });
   
 $('#val_temperatura').hide();
 $('#val_pres_frio').hide();
 $('#val_pres_caliente').hide();
  
 
 
 //$('#Nivel_temperatura').jqxSlider({ showTicks: false }); 
 
 $('#Nivel_temperatura').on('change', function (event) {
  var valor=setColor(1);
  calcula_pif(valor,'temperatura');
 });
 $('#Nivel_inflado_frio').on('change', function (event) {
  var valor=setColor(2);
  calcula_pif(valor,'presion_en_frio');
 
 });
 $('#Nivel_inflado_caliente').on('change', function (event) {
  var valor=setColor(3);
  calcula_pif(valor,'presion_en_caliente');
 
 });
 
 
 setColor(4);
 
 
 $('#img_termometro').click(function(){
  
  $('#bloque_fondo').show();
  $('#bloque_fondo').css('top', '170px');
  
  //$('#marco_pif').css('color', '#1E90FF');
  $('#val_temperatura').show();
  $('#val_pres_frio').show();
  $('#val_pres_caliente').show(); 
    
  //   $('#txt_temp').css('color', '#0000FF');
  //   $('#txt_pif').css('color', '#0000FF');
  //   $('#txt_pic').css('color', '#0000FF');
    
  $('#Nivel_temperatura').jqxSlider({ disabled:true,showTicks: false });
  $('#Nivel_inflado_frio').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });
  $('#Nivel_inflado_caliente').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });

  //$('#Nivel_inflado_frio').jqxSlider({ ticksPosition: 'bottom' }); 
  $('#Bloque_temp').css('background-color', '#74b0f4');
  $('#Bloque_pres_frio').css('background-color', '#0000FF');
  $('#Bloque_pres_caliente').css('background-color', '#0000FF');
  
 });
 
 $('#img_pif').click(function(){
  $('#bloque_fondo').show();
  $('#bloque_fondo').css('top', '218px');
  
  $('#val_temperatura').show();
  $('#val_pres_frio').show();
  $('#val_pres_caliente').show();
  
  //   $('#txt_pif').css('color', '#0000FF');
  //   $('#txt_temp').css('color', '#0000FF');
  //   $('#txt_pic').css('color', '#0000FF');
  
  
  $('#Nivel_temperatura').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });
  $('#Nivel_inflado_frio').jqxSlider({ disabled:true,showTicks: false });
  $('#Nivel_inflado_caliente').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });
  
  
  $('#Bloque_pres_frio').css('background-color', '#74b0f4');
  $('#Bloque_pres_caliente').css('background-color', '#0000FF');
  $('#Bloque_temp').css('background-color', '#0000FF');
  
 });

 $('#img_pic').click(function(){
  
  $('#bloque_fondo').show(); 
  $('#bloque_fondo').css('top', '268px');
  
  $('#val_temperatura').show();
  $('#val_pres_frio').show();
  $('#val_pres_caliente').show();
    
//   $('#txt_pic').css('color', '#0000FF');
//   $('#txt_pif').css('color', '#0000FF');
//   $('#txt_temp').css('color', '#0000FF');
  
  
  $('#Nivel_temperatura').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });
  $('#Nivel_inflado_frio').jqxSlider({ disabled:false,showTicks: true,ticksPosition:'bottom' });
  $('#Nivel_inflado_caliente').jqxSlider({ disabled:true,showTicks: false });
  
  $('#Bloque_pres_caliente').css('background-color', '#74b0f4');
  $('#Bloque_pres_frio').css('background-color', '#0000FF');
  $('#Bloque_temp').css('background-color', '#0000FF');
 });
 });
 
 function calcula_pif(valor,tipo_actual){
 console.log('funcion calcula_pif');

 //Buscar valor de presiones: 
 valor_final_temp = Math.round($('#Nivel_temperatura').jqxSlider('value')).toString(10);
 valor_final_pres_frio=Math.round($('#Nivel_inflado_frio').jqxSlider('value')).toString(10);
 valor_final_pres_caliente=Math.round($('#Nivel_inflado_caliente').jqxSlider('value')).toString(10);
 
 var disable_temperatura = $('#Nivel_temperatura').jqxSlider('disabled');
 var disable_pif = $('#Nivel_inflado_frio').jqxSlider('disabled');
 var disable_pic = $('#Nivel_inflado_caliente').jqxSlider('disabled');
  
 //La barra que estoy moviendo
 if(tipo_actual=='temperatura'){  
  console.log('tipo_actual==temperatura');
  valor=parseFloat(valor)+273.15;     
  if(disable_pif){ 
    //Aplicar formula para encontrar la presion en frio
    pif=(valor_final_pres_caliente*291.5)/valor;  console.log('pif:'+pif);
    var pif_round=Math.round(pif);         console.log('pif_round:'+pif_round);
    $('#Nivel_inflado_frio').jqxSlider('setValue', pif_round);
  }
  if(disable_pic){ 
    //Aplicar formula para encontrar la presion en caliente
    pic=(valor_final_pres_frio*valor)/291.5;
    
    console.log('273: valor_final_pres_frio:'+valor_final_pres_frio);
    console.log('274: valor:'+valor);
     
    var pic_round=Math.round(pic);         console.log('pic_round:'+pic_round);
    $('#Nivel_inflado_caliente').jqxSlider('setValue', pic_round);
  }
 }
 
 //La barra que estoy moviendo
 if(tipo_actual=='presion_en_frio'){
  console.log('tipo_actual==presion_en_frio');
  if(disable_temperatura){
  //Aplicar formula para encontrar la temperatura
  temp=(valor_final_pres_caliente*291.5)/valor;  console.log('temp:'+temp); 
  temp=Math.round(temp-273.15);    console.log('temp:'+temp);
  $('#Nivel_temperatura').jqxSlider('setValue', temp);
  }
  if(disable_pic){
  //Trabajar con temperatura en kelvin
  temp_k=parseFloat(valor_final_temp)+273.15;
  //Aplicar formula para encontrar la presion en caliente
  pic=(valor*temp_k)/291.5;
  var pic_round=Math.round(pic);         console.log('170 pic_round:'+pic_round);
  $('#Nivel_inflado_caliente').jqxSlider('setValue', pic_round);
  }
 }
 
 //La barra que estoy moviendo 
 if(tipo_actual=='presion_en_caliente'){
  console.log('tipo_actual==presion_en_caliente');
  if(disable_temperatura){
  //Aplicar formula para encontrar la temperatura
  temp=(valor*291.5)/valor_final_pres_frio;   console.log('190 temp:'+temp); 
  temp=Math.round(temp-273.15);    console.log('191 temp:'+temp);
  $('#Nivel_temperatura').jqxSlider('setValue', temp);
  }
  if(disable_pif){
  //Trabajar con temperatura en kelvin
  temp_k=parseFloat(valor_final_temp)+273.15;
  
  //Aplicar formula para encontrar la presion en frio
  pif=(valor*291.5)/temp_k;     console.log('199 pif:'+pif);
  var pif_round=Math.round(pif);         console.log('200 pif_round:'+pif_round);
  $('#Nivel_inflado_frio').jqxSlider('setValue', pif_round);
  } 
 } 
 }

 function setColor(tipo) {
   //***************************solo p�ra el color de fondo**********************************
   var red = fixHex(Math.round($('#Nivel_temperatura').jqxSlider('value')).toString(16)),
   green = fixHex(Math.round($('#Nivel_inflado_frio').jqxSlider('value')).toString(16)),
   blue = fixHex(Math.round($('#Nivel_inflado_caliente').jqxSlider('value')).toString(16));

   var valor_final_temp = Math.round($('#Nivel_temperatura').jqxSlider('value')).toString(10),
   valor_final_pres_frio = Math.round($('#Nivel_inflado_frio').jqxSlider('value')).toString(10);
   valor_final_pres_caliente = Math.round($('#Nivel_inflado_caliente').jqxSlider('value')).toString(10);

   if(isNaN(valor_final_temp)) valor_final_temp = 0;
   if(isNaN(valor_final_pres_frio)) valor_final_pres_frio = 0;
   if(isNaN(valor_final_pres_caliente)) valor_final_pres_caliente = 0;
     
   $('#val_temperatura').html(valor_final_temp+'<span class="small"> °C</span>');
   $('#val_pres_frio').html(valor_final_pres_frio+'<span class="small"> PSI</span>');
   $('#val_pres_caliente').html(valor_final_pres_caliente+'<span class="small"> PSI</span>');
  
   //**Color del valor en el texto      
   //    var color = getTextElement({ r: parseInt(red, 16), g: parseInt(green, 16), b: parseInt(blue, 16) });
   var color = '#000';
 
   $('#val_temperatura').css('color', color);
   $('#val_pres_frio').css('color', color);
   $('#val_pres_caliente').css('color', color);
    
   if(tipo==1){ return valor_final_temp; } 
   if(tipo==2){ return valor_final_pres_frio; }
   if(tipo==3){ return valor_final_pres_caliente; }
 }

 function fixHex(hex) {
  return (hex.length < 2) ? '0' + hex : hex;
 }
 
 function getTextElement(color) {
  var nThreshold = 105;
  var bgDelta = (color.r * 0.299) + (color.g * 0.587) + (color.b * 0.114);
  var foreColor = (255 - bgDelta < nThreshold) ? 'Black' : 'White';
  return foreColor;
 }
</script>
<style>
  .temperatura:hover, .pif:hover, .pic:hover {
    background: rgba(144,202,249,0.5);
  }
  .temperatura, .pif, .pic, .separador {
    background: rgba(250,250,250 ,1);
  }
  img{
    margin-top: 5px;
    cursor: pointer;
  }
</style>

<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-12 col-lg-offset-3 col-md-offset-3">
    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12" id="ayuda"><h2>Seleccione variable a determinar</h2></div>
    </div>

    <div class="row separador"><div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div></div>

    <div class="row temperatura">
      <div class="col-lg-3 col-md-3 col-sm-3" id="img_termometro">
        <img src="assets/img/termometro.png" class="center-block" width="35" height="35" />
      </div>
      <div class="col-lg-7 col-md-7 col-sm-7">
        <span id="txt_temp" style="font-style: italic;">Temperatura actual</span>
        <div id='Nivel_temperatura'></div>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2">
        <h2><span id="val_temperatura"></span></h2>
      </div>
    </div>

    <div class="row separador"><div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div></div>

    <div class="row pif">
      <div class="col-lg-3 col-md-3 col-sm-3" id="img_pif">
        <img src="assets/img/pif2.png" class="center-block" width="35" height="35" />
      </div>
      <div class="col-lg-7 col-md-7 col-sm-7">
        <span id="txt_pif" style="font-style: italic;">Presion de inflado en frio</span>
        <div id='Nivel_inflado_frio'></div>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2">
        <h2><span id="val_pres_frio"></span></h2>
      </div>
    </div>

    <div class="row separador"><div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div></div>

    <div class="row pic">
      <div class="col-lg-3 col-md-3 col-sm-3" id="img_pic">
        <img src="assets/img/pic2.png" class="center-block" width="35" height="35" />
      </div>
      <div class="col-lg-7 col-md-7 col-sm-7">
        <span id="txt_pic" style="font-style: italic;">Presión de inflado en caliente</span>
        <div id='Nivel_inflado_caliente'></div>
      </div>
      <div class="col-lg-2 col-md-2 col-sm-2">
        <h2><span id="val_pres_caliente"></span></h2>
      </div>
    </div>

    <div class="row separador"><div class="col-lg-12 col-md-12 col-sm-12">&nbsp;</div></div>

  </div>
</div>