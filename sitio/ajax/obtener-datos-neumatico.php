<?php
 require '../autoload.php';
 
 $acc = new Acceso(true);

 $id_neumatico = isset($_POST['id_neumatico']) ? $_POST['id_neumatico'] : null;

 if($id_neumatico != null && is_numeric($id_neumatico))
 {
   $n = new Neumatico();
   $n = $n->get_full($id_neumatico);

   header("Content-type: text/json");
   echo json_encode($n);
 }