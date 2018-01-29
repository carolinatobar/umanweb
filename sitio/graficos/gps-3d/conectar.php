<?php
function Conectarse() 
{ 
   // 192.168.20.100:3306
   if (!($link=mysql_connect("192.168.20.100:3306","mysql","bailac09"))) 
   { 
      echo "Error conectando a la base de datosx."; 
      exit(); 
   } 
      
      
      
   //if (!mysql_select_db("umanetgaby",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   //if (!mysql_select_db("umanet_sg17092013",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   //if (!mysql_select_db("umanet_Lc_23092015",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   //if (!mysql_select_db("umanet_lc_2016_09_13",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   //if (!mysql_select_db("umanet_andina_28102016",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   if (!mysql_select_db("umanet",$link))  //ESTA TABLA TIENE UMAN_ENCOBERTURA
   { 
      echo "Error seleccionando la base de datos x."; 
      exit(); 
   } 
   return $link; 
} 

$link=Conectarse(); 

?>
