<?php

//error_reporting(0);


$umbox=$_GET['umbox'];

$cont=$_GET['cont'];
$sigui=$_GET['sigui'];
$retro=$_GET['retro'];

if($sigui){
	$cont++;
}

if($retro){
	$cont--;
}

if(!$cont){
	$cont=0;
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><style type="text/css">
<!--
body {
	background-image: url(imagenes/fondo1.jpg);
	background-repeat: repeat;
	background-color: #000000;
}
.style1 {color: #dbfdff; font-family: "Myriad Pro"; }
.style2 {color: #FFFF00;}
.style3 {color: #dbfdff; font-family: "Myriad Pro"; font-size: 14px }
.style4 {color: #000000; font-size: 16px }
.style5 {color: #00FF00; font-family: "Myriad Pro"; }
-->
</style>
<link rel="stylesheet" href="windowfiles/dhtmlwindow.css" type="text/css" />

<script type="text/javascript" src="windowfiles/dhtmlwindow.js">
</script>

<link rel="stylesheet" href="modalfiles/modal.css" type="text/css" />
<script type="text/javascript" src="modalfiles/modal.js"></script>
<script type="text/JavaScript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>


<script>
function permiteip(elEvento, permitidos) {
  // Variables que definen los caracteres permitidos
  var numeros = "0123456789";
  var caracteres = ".";
  var numeros_caracteres = numeros + caracteres;
  var teclas_especiales = [8, 37, 39, 46];
  // 8 = BackSpace, 46 = Supr, 37 = flecha izquierda, 39 = flecha derecha
 
 
  // Seleccionar los caracteres a partir del parámetro de la función
  switch(permitidos) {
      case 'num_car':
      permitidos = numeros_caracteres;
      break;
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
</script>

<script>
function validacion1() {
	
	//alert('Validacion1');
	
	
	//valor de la ip
	valor_ip = document.getElementById("textfield").value;
	
	//alert('124');
	
	valor_puerto = document.getElementById("textfield2").value;
	valor_caja = document.getElementById("textfield3").value;
	valor_timeout = document.getElementById("textfield4").value;
	valor_bateria = document.getElementById("textfield5").value;
	
	//alert('128');
	
	
  if (valor_ip == null || valor_ip.length == 0 || /^\s+$/.test(valor_ip)) {
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "IP Asignada" no debe estar vacio');
    return false;
  }
  
  if ( !(/^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$/ .test(valor_ip)) ) 
  {	
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "IP Asignada" no corresponde');
    return false;
  }
  
  if (valor_puerto == null || valor_puerto.length == 0 || /^\s+$/.test(valor_puerto)) {
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "Puerto" no debe estar vacio');
    return false;
  }
  if ( !(/^\d{2,5}$/ .test(valor_puerto)) ) 
  {	
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "Puerto" no corresponde');
    return false;
  }
  
  if (valor_caja == null || valor_caja.length == 0 || /^\s+$/.test(valor_caja)) {
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "Código caja" no debe estar vacio');
    return false;
  }
  
  if (valor_timeout == null || valor_timeout.length == 0 || /^\s+$/.test(valor_timeout)) {
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "Time Out" no debe estar vacio');
    return false;
  }
  
  if (valor_bateria == null || valor_bateria.length == 0 || /^\s+$/.test(valor_bateria)) {
    // Si no se cumple la condicion...
    alert('[ERROR] El campo "Umbral Batería" no debe estar vacio');
    return false;
  }
  
  
  
   
   // Si el script ha llegado a este punto, todas las condiciones
  // se han cumplido, por lo que se devuelve el valor true
  f.submit();
}
</script>



</head>
<script> 
function ventanaSecundaria1 (URL){ 
   window.open(URL,"ventana1","width=800,height=200,scrollbars=no,top=200,left=200")      
} 
</script> 

<script> 
function ventanaSecundaria2 (URL){ 
   window.open(URL,"ventana2","width=300,height=110,scrollbars=no,top=300,left=500")   
} 
</script> 

<script> 
function ventanaSecundaria3 (URL){ 
   window.open(URL,"ventana1","width=750,height=170,scrollbars=no,top=200,left=260")   
} 
</script> 
<body onload="MM_preloadImages('imagenes/ingresar2.png','imagenes/cancelar2.png','imagenes/aceptar2.png')">


 <script type="text/javascript">

function ageprompt(id){
	agewindow=dhtmlmodal.open('agebox', 'div',id, 'Borrar UmanBox','width=250px,height=150px,left=350px,top=50px,resize=0,scrolling=0')
}

//Function to run when buttons within modal window is clicked on. Directly embedded inside hidden DIV, bypassing "onclose" event:
function ventana(botton){
	if (botton=="no")
		alert("Alerta de Presión")
	agewindow.hide()
}

</script>

 <script type="text/javascript">
function ageprompt1(id){
	agewindow=dhtmlmodal.open('agebox', 'div',id, 'Ingresar UmanBox','width=820px,height=170px,left=130px,top=50px,resize=0,scrolling=0')
}

//Function to run when buttons within modal window is clicked on. Directly embedded inside hidden DIV, bypassing "onclose" event:
function ventana(botton){
	if (botton=="no")
		alert("Alerta de Presión")
	agewindow.hide()
}
</script>

 <script type="text/javascript">
function ageprompt2(id){
	agewindow=dhtmlmodal.open('agebox', 'div',id, 'Editar UmanBox','width=820px,height=170px,left=130px,top=50px,resize=0,scrolling=0')
}

//Function to run when buttons within modal window is clicked on. Directly embedded inside hidden DIV, bypassing "onclose" event:
function ventana(botton){
	if (botton=="no")
		alert("Alerta de Presión")
	agewindow.hide()
}
</script>

 <script type="text/javascript">

function ageprompt3(id){
	agewindow=dhtmlmodal.open('agebox', 'div',id, 'Historial UmanBox','width=660px,height=250px,left=220px,top=50px,resize=0,scrolling=1')
}

//Function to run when buttons within modal window is clicked on. Directly embedded inside hidden DIV, bypassing "onclose" event:
function ventana(botton){
	if (botton=="no")
		alert("Alerta de Presión")
	agewindow.hide()
}

</script>

  <table width="203" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><img src="imagenes/umanboxinicio.png" width="335" height="47" /></td>
      <td><a href="mantencion_umanbox.php?umbox=estado" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image18','','imagenes/sensoresestado2.png',1)"><img src="imagenes/sensoresestado1.png" name="Image18" id="Image18" width="120" height="41" border="0" /></a></td>
      <td><a href="mantencion_umanbox.php?umbox=historial" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image19','','imagenes/sensoreshistorial2.png',1)"><img src="imagenes/sensoreshistorial1.png" name="Image19" id="Image19" width="116" height="41" border="0" /></a></td>
      <td><img src="imagenes/sensoresresto.png" width="227" height="41" /></td>
    </tr>
  </table>

<?php if($umbox=="estado"){?>
<form id="form1" name="form1" method="post" action="mantencion_umanbox.php"> 
<table align="left">
<td width="849" colspan="10" bgcolor="#8F9A9C" >
<table width="849" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="844" height="65" align="center" valign="top" background="imagenes/bordearriba.png"><table width="867" border="0" cellspacing="0" cellpadding="0">
      <tr>
      
        <td width="853" rowspan="2"><div align="center">
          <table width="847" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="33"><span class="style1"><img src="imagenes/fotoizq.png" width="29" height="32" /></span></td>
              <td width="781"><div align="center"><span class="style1"> Estado de UmanBox</span></div></td>
            </tr>
          </table>
          </div></td>
        <td width="16">&nbsp;</td>
      </tr>
      <tr>
        <td width="16">&nbsp;</td>
      </tr>
      <tr>
        <td><table width="850" border="1" align="left" cellpadding="2" cellspacing="0">
          <tr>
            <td width="112" ><div align="center"><span class="style1">Código caja</span></div></td>
			<td width="65" ><div align="center"><span class="style1">Equipo</span></div></td>
            <td width="107" ><div align="center"><span class="style1">IP asignada</span></div></td>
            <td width="62" ><div align="center"><span class="style1">Puerto</span></div></td>
            <td width="80" ><div align="center"><span class="style1">Time out</span></div></td>
            <td width="66" ><div align="center"><span class="style1">Bateria</span></div></td>
            <td width="47" ><div align="center"><span class="style1">Leds</span></div></td>
            <td width="59" ><div align="center"><span class="style1">Buzzer</span></div></td>
            <td width="56" ><div align="center"><span class="style1">Editar</span></div></td>
            <td width="53" ><div align="center"><span class="style1">Borrar</span></div></td>
          </tr>
        </table></td>
        <td>&nbsp;</td>
      </tr>
    </table>
    
    </td>
  </tr>
</table>
      
      <div style="height: 350px; width: 867px; font-size: 12px; overflow: auto;">
    <table  width="850" border="1" cellspacing="0" cellpadding="2">
<?php 
$a=1;
$cota=mysql_query("SELECT COUNT(ID_CAJAUMAN) FROM uman_cajauman");
$da=mysql_fetch_array($cota);
$cantneu= $da[0];
$cont1=$cont;
$cont1=$cont1*50;

$consulta=mysql_query("select * from uman_cajauman where ESTADO='1' order by CODIGOCAJA ASC limit $cont1,50");
while ($datos=mysql_fetch_array($consulta)){

$id= $datos[0];
$ip= $datos[1];
$puerto= $datos[2];	
$cod_caja= $datos[3];
$time_out= $datos[4];
$ubateria= $datos[7];
$leds=$datos[5];
$sirena= $datos[6];
$estadoub= $datos[9];

$equipo= '&nbsp;';
//$posicion= '&nbsp;';

$buscaequipo= mysql_query("select * from uman_camion ");
while ($datosequipo=mysql_fetch_array($buscaequipo)){

$cajauman=$datosequipo[3];
	
if ($id== $cajauman){ $equipo=$datosequipo[1];}	
}
$id2=$id;
$id2=$id2."100";
if($a%2==0){

echo "<tr><td width='112' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$cod_caja</span></div></td>";
echo "<td width='65' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$equipo</span></div></td>";
echo "<td width='107' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$ip</span></div></td>";
echo "<td width='62' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$puerto</span></div></td>";
echo "<td width='80' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$time_out</span></div></td>";
echo "<td width='66' bgcolor='#A8AEAA'><div align='center'><span class='style4'>$ubateria</span></div></td>";
?>
<td width="47" bgcolor='#A8AEAA'><div align='center'><?php if($leds==0){?><img src='imagenes/ledapagado.png' style='cursor: hand' width='23' height='22'/><?php }else{?><img src='imagenes/ledencendido.png' style='cursor: hand' width='23' height='22' /><?php }?></div></td>
<td width="59" bgcolor='#A8AEAA'><div align='center'><?php if($sirena==0){?><img src='imagenes/sirenaapagada.png' width='23' height='23'/><?php }else{?><img src='imagenes/sirenaencendida.png'style='cursor: hand' width='23' height='23' /><?php }?></div></td>

<td width="56" bgcolor='#A8AEAA'><div align='center'><img src='imagenes/editar.gif' style='cursor: hand' width='22' height='23' onclick="ageprompt2(<?php echo $id2; ?>); return false"/></div></td>
<td width="53" bgcolor='#A8AEAA'><div align='center'><img src='imagenes/baja.png' style='cursor: hand' width='22' height='23' onclick="ageprompt(<?php echo $id; ?>); return false"/></div></td>
<?php
	}else{
echo "<tr><td width='112' bgcolor='#676B68'><div align='center'><span class='style4'>$cod_caja</span></div></td>";
echo "<td width='65' bgcolor='#676B68'><div align='center'><span class='style4'>$equipo</span></div></td>";
echo "<td width='107' bgcolor='#676B68'><div align='center'><span class='style4'>$ip</span></div></td>";
echo "<td width='62' bgcolor='#676B68'><div align='center'><span class='style4'>$puerto</span></div></td>";
echo "<td width='80' bgcolor='#676B68'><div align='center'><span class='style4'>$time_out</span></div></td>";
echo "<td width='66' bgcolor='#676B68'><div align='center'><span class='style4'>$ubateria</span></div></td>";
?>
<td width="47" bgcolor='#676B68'><div align='center'><?php if($leds==0){?><img src='imagenes/ledapagado.png' style='cursor: hand' style='cursor: hand' width='23' height='22' /><?php }else{?><img src='imagenes/ledencendido.png' style='cursor: hand' width='23' height='22' /><?php }?></div></td>
<td width="59" bgcolor='#676B68'><div align='center'><?php if($sirena==0){?><img src='imagenes/sirenaapagada.png' style='cursor: hand' width='23' height='23'/><?php }else{?><img src='imagenes/sirenaencendida.png' style='cursor: hand' width='23' height='23' /><?php }?></div></td>

<td width="56" bgcolor='#676B68'><div align='center'><img src='imagenes/editar.gif' style='cursor: hand' width='22' height='23' onclick="ageprompt2(<?php echo $id2; ?>); return false" /></div></td>
<td width="53" bgcolor='#676B68'><div align='center'><img src='imagenes/baja.png' style='cursor: hand' width='22' height='23' onclick="ageprompt(<?php echo $id; ?>); return false"/></div></td>

<?php
		
	}
	$a++;
}
?>
</table>
</div>
<table width="867" height="30" border="0" align="center" cellpadding="0" cellspacing="0" background="imagenes/bordeabajo.png">
  <tr>
    <td width="219" height="23" ><div align="center">
      <label></label>
      <?php
	  if($cont>=1){
	  ?>
      <div align="right"><a href="mantencion_umanbox.php?umbox=estado&retro=1&cont=<?php echo $cont; ?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Imae3','','imagenes/anterior1.png',1)"><img src="imagenes/anterior.png" name="Imae3" id="Imae3" border="0" id="Image3" /></a></div>
      <?php
	  }
	  ?>
    </div>
	</td>
    <td width="219"><div align="center"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Ie7','','imagenes/ingresar2.png',1)"><img src="imagenes/ingresar1.png" name="Ie7"  id="Ie7" width="71" height="22" border="0" align="middle" onclick="ageprompt1(0); return false" /></a></div></td>
    <td width="219"><div align="left">
	<?php
	if($cantneu>50 && $a>50){
	?>
	<a href="mantencion_umanbox.php?umbox=estado&sigui=1&cont=<?php echo $cont; ?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','imagenes/siguiente1.png',1)"><img src="imagenes/siguiente.png" name="Image4" id="Image4" border="0" id="Image4" /></a>
	<?php
	}
	?>	
	</div></td>
  </tr>
</table>

  </td>
</table>  
</form>





<?php
$consulta2=mysql_query("select * from uman_cajauman where ESTADO='1' order by ID_CAJAUMAN ASC");
while ($datos2=mysql_fetch_array($consulta2)){

$id2= $datos2[0];
$id2=$id2."100";

?>
<div id="<?php echo $id2; ?>" style="display:none;">

<div style="background: #a8afab; height: 100%; padding: 5px">
<?php 
$id2 = substr($id2, 0, -3);

$consulta=mysql_query("select * from uman_cajauman where ID_CAJAUMAN='$id2'");
while ($datos=mysql_fetch_array($consulta)){

$id2= $datos[0];
$ip= $datos[1];
$puerto= $datos[2];	
$codcaja= $datos[3];
$timeout= $datos[4];
$led= $datos[5];
$sirena= $datos[6];
$umbral= $datos[7];

$consulta1=mysql_query("select NUMCAMION from uman_camion where ID_CAJAUMAN='$id2'");
$datos1=mysql_fetch_array($consulta1);
$equipo=$datos1[0];
?>
<form name="fo<?php echo $id2; ?>" method="post" action=""    >
  <label></label><label></label>
  <table width="801" height="151" border="0" align="center" cellpadding="0" cellspacing="0" background="imagenes/fondoingresar.png">
    <tr>
      <td colspan="7"><div align="center"><strong><span class="style1">Editar</span></strong></div></td>
    </tr>
    <tr>
      <td><div align="center"><span class="style1">Equipo</span></div></td>
      <td><div align="center"><span class="style1">IP Asignada</span></div></td>
      <td><div align="center"><span class="style1">Puerto</span></div></td>
      <td><div align="center"><span class="style1">C&oacute;digo caja</span></div></td>
      <td><div align="center"><span class="style1">Time out</span></div></td>
      <td><div align="center"><span class="style1">Leds</span></div></td>
      <td><div align="center"><span class="style1">Buzzer</span></div></td>
      <td><div align="center"><span class="style1">Umbral</span></div></td>
    </tr>
    <tr>
      <td><div align="center">
        <select name="selecte" id="selecte">
        <option></option>
          <?php
	    $consulta1=mysql_query("select NUMCAMION from uman_camion ORDER BY ORDEN ASC");
		while ($datos1=mysql_fetch_array($consulta1)){
	  ?>
          <option <?php if($equipo==$datos1[0]){?> selected="selected"<?php } ?>><?php echo $datos1[0]; ?></option>
          <?php } ?>
        </select>
      </div></td>
      <td><div align="center">
        <input name="textfielde" id="textfielde" type="text" size="20" maxlength="15"  onkeypress="return permiteip(event, 'num_car')"  value="<?php echo $ip; ?>" />
      </div></td>
      <td><div align="center">
        <input name="textfield2e" id="textfield2e" type="text" size="10" maxlength="5" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $puerto; ?>" />
      </div></td>
      <td><div align="center">
        <input name="textfield3e" id="textfield3e" type="text" size="7" maxlength="20" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $codcaja; ?>" />
      </div></td>
      <td><div align="center">
        <input name="textfield4e" name="textfield4e" type="text" size="5" maxlength="2" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $timeout; ?>" />
      </div></td>
      <td><div align="center">
        <select name="select2e" id="select2e">
          <option <?php if($led==1){?> selected="selected"<?php } ?>>Encendida</option>
          <option <?php if($led==0){?> selected="selected"<?php } ?>>Apagada</option>
        </select>
      </div></td>
      <td><div align="center">
        <select name="select3e" id="select3e">
          <option <?php if($sirena==1){?> selected="selected"<?php } ?>>Encendida</option>
          <option <?php if($sirena==0){?> selected="selected"<?php } ?>>Apagada</option>
        </select>
      </div></td>
      <td>
        <div align="center">
          <input name="textfield42e" id="textfield42e" type="text" size="5" maxlength="3" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $umbral; ?>" />
        </div></td>
    </tr>
    <tr>
      <td colspan="7"><div align="center">
        <table width="125" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><div align="right"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2<?php echo $id2; ?>','','imagenes/aceptar2.png',1)"><img src="imagenes/aceptar1.png" name="Image2<?php echo $id2; ?>" id="Image2<?php echo $id2; ?>" width="71" height="22" border="0" onclick="fo<?php echo $id2; ?>.submit()" /></a></div></td>
            <td>&nbsp;</td>
            <td><div align="left"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1<?php echo $id2; ?>','','imagenes/cancelar2.png',1)"><img src="imagenes/cancelar1.png" name="Image1<?php echo $id2; ?>" id="Image1<?php echo $id2; ?>" width="71" height="22" border="0" onclick="ventana('yes')" /></a></div></td>            
          </tr>
        </table>
      </div></td>
    </tr>
  </table>    
    <input type="hidden" name="nide" id="nide"  value="<?php echo "$id2";  ?>" />
    <input type="hidden" name="equipoe" id="equipoe"   value="<?php echo "$equipo";  ?>" />
</p>
</form>
</div>
</div>
<?php } }?>












<div id="0" style="display:none;">

<div style="background: #a8afab; height: 100%; padding: 5px">

<form name="f" method="post" action=""  onsubmit="return validacion1()"   >
  <label></label><label></label>
  <table width="800" height="151" border="0" align="center" cellpadding="0" cellspacing="0" background="imagenes/fondoingresar.png">
    <tr>
      <td colspan="8"><div align="center"><strong><span class="style1">Ingresar UmanBox</span></strong></div></td>
    </tr>
    <tr>
      <td width="108"><div align="center"><span class="style1">Equipo</span></div></td>
      <td width="135"><div align="center"><span class="style1">IP Asignada</span></div></td>
      <td width="67"><div align="center"><span class="style1">Puerto</span></div></td>
      <td width="92"><div align="center"><span class="style1">C&oacute;digo caja</span></div></td>
      <td width="69"><div align="center"><span class="style1">Time out</span></div></td>
      <td width="107"><div align="center"><span class="style1">Leds</span></div></td>
      <td width="102"><div align="center"><span class="style1">Buzzer</span></div></td>
      <td width="122"><div align="center"><span class="style1">Umbral bater&iacute;a</span></div></td>
    </tr>
    <tr>
      <td><div align="center">
        <select name="select" id="select">
        <option selected="selected"></option>
          <?php
	    $consulta1=mysql_query("select NUMCAMION from uman_camion ORDER BY ORDEN ASC");
		while ($datos1=mysql_fetch_array($consulta1)){
	  ?>
          <option><?php echo $datos1[0]; ?></option>
          <?php } ?>
        </select>
      </div></td>
      <td><div align="center">
        <input name="textfield" id="textfield" type="text" size="15" onkeypress="return permiteip(event, 'num_car')"  maxlength="15" />
      </div></td>
      <td><div align="center">
        <input name="textfield2" id="textfield2" value="10001" type="text" size="10" maxlength="5" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" />
      </div></td>
      <td><div align="center">
        <input name="textfield3" id="textfield3" type="text" size="7" maxlength="20" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" />
      </div></td>
      <td><div align="center">
        <input name="textfield4" id="textfield4" value="60" type="text" size="5" maxlength="2" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" />
      </div></td>
      <td><div align="center">
        <select name="select2">
          <option>Encendida</option>
          <option>Apagada</option>
        </select>
      </div></td>
      <td><div align="center">
        <select name="select3">
          <option>Encendida</option>
          <option>Apagada</option>
        </select>
      </div></td>
      <td><div align="center">
        <input name="textfield5" id="textfield5" value="80" type="text" size="5" maxlength="3" onkeypress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" />
      </div></td>
    </tr>
    <tr>
      <td colspan="8"><div align="center">
        <table width="125" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image98','','imagenes/aceptar2.png',1)"><img src="imagenes/aceptar1.png" name="Image98" width="71" height="22" border="0" id="Image2" onclick="validacion1()" /></a></td>
            <td>&nbsp;</td>
            <td><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image90','','imagenes/cancelar2.png',1)"><img src="imagenes/cancelar1.png" name="Image90" width="71" height="22" border="0" id="Image1" onclick="ventana('yes')" /></a></td>            
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
</form>
</div>
</div>



<?php
$consulta=mysql_query("select * from uman_cajauman where ESTADO='1' order by ID_CAJAUMAN ASC");
while ($datos=mysql_fetch_array($consulta)){

$id= $datos[0];

?>
<div id="<?php echo $id; ?>" style="display:none;">

<div style="background: #a8afab; height: 100%; padding: 5px">

<form name="form1<?php echo $id; ?>" method="post" action="">
  <label>
  <div align="center">
    <table width="230" height="140" border="0" cellpadding="0" cellspacing="0" background="imagenes/fondodebajauman.png">
      <tr>
        <td colspan="3" height="35"><div align="center"><strong><span class="style1">UmanBox</span></strong></div></td>
      </tr>
      <tr>
        <td colspan="3"><img src="imagenes/separador3.png" width="220" height="3" /></td>
      </tr>
      <tr>
        <td colspan="3"><div align="center">
			<table width="203" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="26"><label>
                <input name="radiobutton" type="radio" value="borrar" checked="checked" />
              </label></td>
              <td><span class="style1">Borrar</span></td>
              
            </tr>
          </table>
        </div></td>
      </tr>
  
      <tr>
        <td><div align="right"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('I3<?php echo $id; ?>','','imagenes/aceptar2.png',1)"><img src="imagenes/aceptar1.png" name="I3<?php echo $id; ?>" width="71" height="22" border="0" id="Image3" onclick="form1<?php echo $id; ?>.submit()" /></a></div></td>
        <td>&nbsp;</td>
        <td><div align="left"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('I2<?php echo $id; ?>','','imagenes/cancelar2.png',1)"><img src="imagenes/cancelar1.png" name="I2<?php echo $id; ?>" width="71" height="22" border="0" id="Image2" onclick="ventana('yes')" /></a></div></td>        
      </tr>
    </table>
  </div>
  </label>
  <label>
  <input type="hidden" name="nid"   value="<?php echo "$id";  ?>" />
  </label>
</form>
</div>
</div>
<?php } ?>


</body>
</html>

<?php
$nid= $_POST['nid'];
$estadoradiobutton= $_POST['radiobutton'];

if($nid){
$fechaini=date('Y-m-d H:i:s');
$seleccionid= mysql_query("select ID_CAMION from uman_camion where ID_CAJAUMAN='$nid'");
$id=mysql_fetch_array($seleccionid);
$idc=$id[0];
$selonid= mysql_query("select CODIGOCAJA from uman_cajauman where ID_CAJAUMAN='$nid'");
$id2=mysql_fetch_array($selonid);
$codigoc=$id2[0];

if($estadoradiobutton=="borrar"){
mysql_query("INSERT INTO uman_reg_cajauman(ID_CAJAUMAN,ID_CAMION,LOG,DESDE,HASTA,NUMSERIE) VALUES('$nid','$idc','umanbox eliminada','','$fechaini','$codigoc')");
mysql_query("UPDATE  uman_camion SET ID_CAJAUMAN='0' WHERE ID_CAJAUMAN='$nid'");
mysql_query("DELETE FROM uman_cajauman WHERE ID_CAJAUMAN='$nid'");
mysql_query("DELETE FROM uman_reconocealarma WHERE ID_CAJAUMAN='$nid'");
echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=$PHP_SELF'>";
}else{
mysql_query("INSERT INTO uman_reg_cajauman(ID_CAJAUMAN,ID_CAMION,LOG,DESDE,HASTA,NUMSERIE) VALUES('$nid','$idc','umanbox de baja','','$fechaini','$codigoc')");
mysql_query("UPDATE  uman_camion SET ID_CAJAUMAN='0' WHERE ID_CAJAUMAN='$nid'");
mysql_query("UPDATE  uman_cajauman SET ESTADO='0' WHERE ID_CAJAUMAN='$nid'");
echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=$PHP_SELF'>";
}
}
?>





<?php
$equipo= $_POST['select'];
$ip= $_POST['textfield'];
$puerto= $_POST['textfield2'];
$codcaja= $_POST['textfield3'];
$timeout= $_POST['textfield4'];
$led= $_POST['select2'];
$sirena= $_POST['select3'];
$umbral= $_POST['textfield5'];
$fechaini=date('Y-m-d H:i:s');

$validacion ="([1-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([1-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-4]))$";

if($led=="Encendida"){$led=1;}else{$led=0;}
if($sirena=="Encendida"){$sirena=1;}else{$sirena=0;}

if($ip){
if($timeout<=60){
if ((ereg($validacion,$ip))&&($puerto<=65000)&&($puerto!="")) {
	
mysql_query("INSERT INTO uman_cajauman(IPUMAN,PUERTO,CODIGOCAJA,TIMEOUTSENSOR,LEDS,BUZZER,UMBRALBATERIA,ESTADO) VALUES('$ip','$puerto','$codcaja','$timeout','$led','$sirena','$umbral','1')");
//mysql_query("INSERT INTO uman_reconocealarma(ID_CAJAUMAN,IPUMANBOX,VALOR) VALUES('$id','$ip','0')");
$seleccionidcaja= mysql_query("select ID_CAJAUMAN from uman_cajauman where CODIGOCAJA='$codcaja'");
$idcaja=mysql_fetch_array($seleccionidcaja);
$idumb1=$idcaja[0];
mysql_query("INSERT INTO uman_reg_cajauman(ID_CAJAUMAN,ID_CAMION,LOG,DESDE,HASTA,NUMSERIE) VALUES('$idumb1','$idcamion','umanbox creada','$fechaini','','$codcaja')");

if($equipo){
$seleccionidcaja= mysql_query("select ID_CAJAUMAN,NUMCAMION from uman_camion where NUMCAMION='$equipo'");
$idcaja=mysql_fetch_array($seleccionidcaja);
$idcajaub=$idcaja[0];
$numcamion2=$idcaja[1];

if($idcajaub){
	?>
<form name="form_hidden1" method="POST" action="umanbox_mensaje.php">
<input type="hidden" name="id" value="<?php echo $idumb1;?>" /> 
<input type="hidden" name="equipo" value="<?php echo $numcamion2;?>" />
<input type="hidden" name="equipoini" value="<?php echo $equipo?>" />
<input type="hidden" name="ip" value="<?php echo $ip;?>" />
</form>

<script type="text/javascript">
var flag = confirm("Equipo <?php echo $numcamion2;?> ya posee UmanBox ¿Desea reemplazarlo?")
if (flag)
{
document.form_hidden1.submit();
}	
</script>

<?php
}else{
mysql_query("UPDATE uman_camion SET ID_CAJAUMAN='$idumb1' WHERE NUMCAMION='$equipo'");
$fechaini=date('Y-m-d H:i:s');	
$seleccionid= mysql_query("select ID_CAMION from uman_camion where NUMCAMION='$equipo'");
$idcam=mysql_fetch_array($seleccionid);
$idcamion=$idcam[0];
mysql_query("INSERT INTO uman_reg_cajauman(ID_CAJAUMAN,ID_CAMION,LOG,DESDE,HASTA,NUMSERIE) VALUES('$idumb1','$idcamion','umanbox ingresada','$fechaini','','$codcaja')");
}
}

//Para la actualizacion automática
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','1')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','2')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','3')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','4')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','5')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','6')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','7')  ");
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','8')  ");

mysql_query("INSERT INTO uman_actualizacion (IPUMANBOX,TIPO) VALUES('$ip','10')  ");

echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=$PHP_SELF'>";
}else {
	?>	
		<script type="text/javascript">
	alert("IP Incorrecta")	
</script>
<?php
	}
	}else{
		?>	
		<script type="text/javascript">
	alert("Time Out debe ser menor o igual a 60 minutos")	
</script>
<?php	
	}
	
	}
?>



<?php
$id= $_POST['nide'];
$equipoinicial= $_POST['equipoe'];
$equipo= $_POST['selecte'];
$ip= $_POST['textfielde'];
$puerto= $_POST['textfield2e'];
$codcaja= $_POST['textfield3e'];
$timeout= $_POST['textfield4e'];
$led= $_POST['select2e'];
$sirena= $_POST['select3e'];
$umbral= $_POST['textfield42e'];

$validacion ="([1-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([0-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-5]))\.";
$validacion .="([1-9]|[1-9][0-9]|1([0-9][0-9])|2([0-4][0-9]|5[0-4]))$";

if($led=="Encendida"){$led=1;}else{$led=0;}
if($sirena=="Encendida"){$sirena=1;}else{$sirena=0;}

if($ip){
if($timeout<=60){
if ((ereg($validacion,$ip))&&($puerto<=65000)&&($puerto!="")) {
	
//Buscar solo los cambios

$buscarepetido=mysql_query("select * from uman_cajauman where ID_CAJAUMAN='$id'");
$repetido= mysql_fetch_array($buscarepetido);

$ipbd=$repetido[1];
$busca_timeout=$repetido[4];
$busca_leds=$repetido[5];
$busca_buzzer=$repetido[6];
$busca_umbralbateria=$repetido[7];

if ($busca_timeout!=$timeout){
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','4')  ");
}
if ($busca_leds!=$led){
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','6')  ");
}
if ($busca_buzzer!=$sirena){
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','7')  ");
}
if ($busca_umbralbateria!=$umbral){
mysql_query("INSERT INTO uman_actualizaciones (ID,IPUMAN,TIPO) VALUES('','$ip','5')  ");
}

if($ipbd != $ip){
mysql_query("INSERT INTO uman_actualizacion (IPUMANBOX,TIPO) VALUES('$ip','10')  ");
}
	
mysql_query("UPDATE uman_cajauman SET IPUMAN='$ip',PUERTO='$puerto',CODIGOCAJA='$codcaja',TIMEOUTSENSOR='$timeout',LEDS='$led',BUZZER='$sirena',UMBRALBATERIA='$umbral' where ID_CAJAUMAN='$id'");

mysql_query("UPDATE uman_reconocealarma SET IPUMANBOX='$ip' where ID_CAJAUMAN='$id'");


if($equipoinicial!=$equipo){
$consulta1=mysql_query("SELECT `ID_CAJAUMAN` FROM `uman_camion` WHERE `NUMCAMION`='$equipo'");
$datos1=mysql_fetch_array($consulta1);
$idcajauman=$datos1[0];	

if($idcajauman!=0)
	{
	?>
<form name="form_hidden" method="POST" action="umanbox_mensaje.php">
<input type="hidden" name="id" value="<?php echo $id;?>" /> 
<input type="hidden" name="equipo" value="<?php echo $equipo;?>" />
<input type="hidden" name="equipoini" value="<?php echo $equipoinicial;?>" />
<input type="hidden" name="ip" value="<?php echo $ip;?>" />
</form>

<script type="text/javascript">
var flag = confirm("Equipo <?php echo $equipo;?> ya posee UmanBox ¿Desea reemplazarlo?")
if (flag)
{
document.form_hidden.submit();
}	
</script>
<?php
}	
else{
$fechaini=date('Y-m-d H:i:s');	
$seleccionid= mysql_query("select ID_CAMION from uman_camion where NUMCAMION='$equipo'");
$idcam=mysql_fetch_array($seleccionid);
$idcamion=$idcam[0];
$selonid= mysql_query("select CODIGOCAJA from uman_cajauman where ID_CAJAUMAN='$id'");
$id2=mysql_fetch_array($selonid);
$codigoc=$id2[0];
mysql_query("INSERT INTO uman_reg_cajauman(ID_CAJAUMAN,ID_CAMION,LOG,DESDE,HASTA,NUMSERIE) VALUES('$id','$idcamion','umanbox ingresada','$fechaini','','$codigoc')");	
mysql_query("UPDATE  uman_camion SET ID_CAJAUMAN='0' WHERE NUMCAMION='$equipoinicial'");
mysql_query("UPDATE  uman_camion SET ID_CAJAUMAN='$id' WHERE NUMCAMION='$equipo'");
mysql_query("INSERT INTO uman_actualizacion (IPUMANBOX,TIPO) VALUES('$ip','10')  ");
}
}

echo "<META HTTP-EQUIV='refresh' CONTENT='0; URL=$PHP_SELF'>";

}else {
	?>	
		<script type="text/javascript">
	alert("IP Incorrecta")	
</script>
<?php
	}
	}else{
		?>	
		<script type="text/javascript">
	alert("Time Out debe ser menor o igual a 60 minutos")	
</script>
<?php	
	}
	}
	
	}
	
	
if($umbox=="historial"){
	?>
	&nbsp;
  <table align="left">
<td width="630" colspan="7" bgcolor="#8F9A9C" >
  <table width="630" border="0" cellspacing="0" cellpadding="0">
  <tr>
  <td width="625" height="35" align="center" valign="top" background="imagenes/bordearriba1.png"><table width="646" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="853" rowspan="2"><table width="634" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="33"><span class="style1"><img src="imagenes/fotoizq.png" width="29" height="32" /></span></td>
            <td width="540"><div align="center"><span class="style1"> Historial UmanBox </span></div></td>
          </tr>
        </table></td>
        <td width="16">&nbsp;</td>
      </tr>
      <tr>
        <td width="16">&nbsp;</td>
      </tr>
    </table>
    
    </td>
  </tr>
</table>
      
	  <div STYLE="height: 350px; width: 647px; font-size: 12px; overflow: auto;">
    <table width="630" border="1" cellspacing="0" cellpadding="2" >
<?php 
$b=1;
$c=1;
$consulta=mysql_query("SELECT DISTINCT ID_CAJAUMAN,NUMSERIE  FROM uman_reg_cajauman order by NUMSERIE ASC");
while ($datos=mysql_fetch_array($consulta)){

$id= $datos[0];
$codigo=$datos[1];

$id3=$id."1000";


if($c==1){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==2){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==3){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==4){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==5){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==6){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
	}

if($c==7){
?>
<td align="center" width='80' bgcolor='#A8AEAA'><div align='center'>
<table width="80" height='25' align="center" border="0" cellpadding="0" cellspacing="0" background="imagenes/sensoresfondo.png">
      <tr>
        <td align="center"><div align="center"><span class="style1"><a onclick="ageprompt3(<?php echo $id3; ?>); return false"><?php echo $codigo; ?></a></span></div></td>
      </tr>
    </table>
    </div></td>
    <?php
    }
?>

<?php

	
	if($c==7){
	echo "<tr></tr>";
	$c=0;	
	}
	
	$c++;
}

while($c<=7){
echo "<td>&nbsp;</td>";	
$c++;
}

?>
</table>
</div>
 
<table width="647" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="23" background="imagenes/bordeabajo1.png"><div align="center">
    </div></td>
  </tr>
</table>
  </td>
</table> 
	<?php
}
?>

<p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p><img src="imagenes/separador.png" width="796" height="3"></p>
  <table width="871" border="0">
      <tr>
        <td width="865" rowspan="4"><img align="right" src="imagenes/logoabajo.png"></td>
      </tr>
</table>




<?php

$consulta=mysql_query("SELECT DISTINCT ID_CAJAUMAN,NUMSERIE FROM uman_reg_cajauman");
while ($datos=mysql_fetch_array($consulta)){

$id= $datos[0];
$codigo=$datos[1];

$id3=$id."1000";

?>
<div id="<?php echo $id3; ?>" style="display:none;">

<div style="background: #434C4B; height: 100%; padding: 5px">

<form name="formulario<?php echo $id3; ?>" method="post" action="">
  <label>
  <div align="center">
    <table width="620" height="80" border="0" cellpadding="0" cellspacing="0" >
      <tr>
        <td colspan="3" height="35"><div align="center"><strong><span class="style1">Historial UmanBox:<span class="style5"> <?php echo $codigo; ?></span></span></strong></div></td>
      </tr>
      <tr>
        <td colspan="3"><img src="imagenes/separador3.png" width="620" height="3" /></td>
      </tr> 
       <tr>
        <td colspan="3"><p>&nbsp;</p></td>
      </tr>
	      <td><table width="620" border="1" cellspacing="0" cellpadding="2">
 <?php
$id3 = substr($id3, 0, -4);
$a=1;
$consulta1=mysql_query("SELECT NUMSERIE,ID_CAMION,LOG,DESDE,HASTA FROM uman_reg_cajauman where ID_CAJAUMAN='$id3'");
while ($datos1=mysql_fetch_array($consulta1)){

$codcaja= $datos1[0];
$idcamion= $datos1[1];
$log= $datos1[2];
$desde= $datos1[3];
$hasta= $datos1[4];

list( $fecha,$hora ) = split( '[ ]', $desde);

list( $año, $mes,$dia ) = split( '[-]', $fecha);
$desde=$dia."-".$mes."-".$año." ".$hora;

list( $fecha1,$hora1 ) = split( '[ ]', $hasta);

list( $año1, $mes1,$dia1 ) = split( '[-]', $fecha1);
$hasta=$dia1."-".$mes1."-".$año1." ".$hora1;

if($a%2==0){
?>	
<tr>
<td bgcolor="#A8AEAA" width="135" >
<?php
if($log=="umanbox ingresada" || $log=="umanbox creada"){
	echo "$desde"; 
}

if($log=="umanbox eliminada" || $log=="umanbox libre" || $log=="umanbox de baja"){
	echo "$hasta"; 
}
?>
</td>
<td bgcolor="#A8AEAA">	
<?php

if($log=="umanbox creada"){
	echo "Umanbox creada."; 
}

if($log=="umanbox ingresada"){
$c1=mysql_query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION='$idcamion'");
$d1=mysql_fetch_array($c1);
$numcamion= $d1[0];	

	echo "Se instala la UmanBox N° $codcaja en el equipo N° $numcamion."; 
}

if($log=="umanbox libre"){
$c1=mysql_query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION='$idcamion'");
$d1=mysql_fetch_array($c1);
$numcamion= $d1[0];	

	echo "Se extrae la UmanBox N° $codcaja del equipo N° $numcamion."; 
}

if($log=="umanbox eliminada"){
	echo "Umanbox eliminada."; 
}

if($log=="umanbox de baja"){
	echo "Umanbox de baja."; 
}

?>
</td>
</tr>
	
<?php
}else{
?>	
<tr>
<td bgcolor="#676B68" width="135" >
<?php
if($log=="umanbox ingresada" || $log=="umanbox creada"){
	echo "$desde"; 
}

if($log=="umanbox eliminada" || $log=="umanbox libre" || $log=="umanbox de baja"){
	echo "$hasta"; 
}
?>
</td>
<td bgcolor="#676B68">	
<?php

if($log=="umanbox creada"){
	echo "Umanbox creada."; 
}

if($log=="umanbox ingresada"){
$c1=mysql_query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION='$idcamion'");
$d1=mysql_fetch_array($c1);
$numcamion= $d1[0];	

	echo "Se instala la UmanBox N° $codcaja en el equipo N° $numcamion."; 
}

if($log=="umanbox libre"){
$c1=mysql_query("SELECT NUMCAMION FROM uman_camion WHERE ID_CAMION='$idcamion'");
$d1=mysql_fetch_array($c1);
$numcamion= $d1[0];	

	echo "Se extrae la UmanBox N° $codcaja del equipo N° $numcamion."; 
}

if($log=="umanbox eliminada"){
	echo "Umanbox eliminada."; 
}

if($log=="umanbox de baja"){
	echo "Umanbox de baja."; 
}
?>
</td>
</tr>
	
<?php	
}
$a++;
}
?>		
    </table>
	<p>&nbsp;</p>
	</td>     
      <tr>
        <td><div align="center"><a href="#" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('botonn<?php echo $id3; ?>','','imagenes/cerrar2.png',1)"><img src="imagenes/cerrar1.png" name="botonn<?php echo $id3; ?>" width="71" height="22" border="0" id="Image2" onclick="ventana('yes')" /></a></div></td>
      </tr>
    </table>
  </div>
  </label>
  <label>  
  </label>
</form>
</div>
</div>
<?php 
}
?>


