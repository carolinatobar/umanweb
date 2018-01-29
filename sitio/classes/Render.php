<?php

class Render{
  function make_block($title, $content, $type='danger'){
    $errorMsg = '
       <div class="panel panel-'.$type.'" style="width:70%; margin:5% 15%;">
        <div class="panel-heading"><h3 class="bg-'.$type.'"><center>'.$title.'</center></h3></div>
         <div class="panel-body text-center">'.$content.'</div>
         <p>&nbsp;</p>
         <p>&nbsp;</p>
         <div class="panel-footer text-center">
          <div class="btn-group">
            <a role="button" href="?'.$_SERVER['QUERY_STRING'].'" target="_self" class="btn btn-default">Recargar página</a>
            <a role="button" href="?s=logout" target="_self" class="btn btn-default">Cerrar sesión</a>
          </div>
         </div>
        </div>
       </div>
      ';
    echo($errorMsg);
  }
}