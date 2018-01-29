<?php
session_start();
$sess_id = session_id();
session_destroy();
unset($_SESSION[$sess_id]['user']);
unset($_SESSION[$sess_id]['pass']);
unset($_SESSION[$sess_id]['perfil']);
unset($_SESSION[$sess_id]['perfilactivo']);
header("Location: index.php");
?>