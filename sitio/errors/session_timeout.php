<?php
	require '../autoload.php';

	Render::make('error_fullpage', array(
        'title'=>'Su sesión se ha cerrado automáticamente',
        'content'=>'<center>Por su seguridad la sesión ha sido cerrada automáticamente.<br/><br/>'.
        '<a href="'.$GLOBALS['LOGIN'].'" target="_self" class="btn btn-info">Ir al login</a></center>',
        'footer'=>''
      ));

?>