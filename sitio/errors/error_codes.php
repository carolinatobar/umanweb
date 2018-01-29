<?php
// error_reporting(E_ALL);
$GLOBALS['_ERROR'] = new ArrayObject();
$GLOBALS['_ERROR']->setFlags(ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);

// SAMPLE
$GLOBALS['_ERROR']->offsetSet('e_'.md5('_'), new ArrayObject(
	array(
		'title'=>'',
		'text'=>'',
		'type'=>'',
		'footer'=>'',
		'html'=>false,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// ERROR DE ACCESO / LOGIN
$GLOBALS['_ERROR']->offsetSet('e_'.md5(0), new ArrayObject(
	array(
		'title'=>'Error de acceso',
	  	'text'=>'El nombre de usuario o la contraseña no corresponden, por favor, intente nuevamente.',
	  	'type'=>'error',
	  	'html'=>false,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// NINGÚN PERFIL ASIGNADO
$GLOBALS['_ERROR']->offsetSet('e_'.md5(1), new ArrayObject(
	array(
	  'title'=>'Ningún perfil asignado',
	  'text'=>'<div><center>El usuario no tiene asignado ningún perfil.</center> <br/> <small>Para mayor información contáctese con el administrador o con soporte técnico.</small></div>',
	  'type'=>'warning',
	  'html'=>true,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// ERROR FAENA SELECCIONADA
$GLOBALS['_ERROR']->offsetSet('e_'.md5(2), new ArrayObject(
	array(
	  'title'=>'Error con faena seleccionada',
	  'text'=>'<div><center>Existe un problema con la faena seleccionada.</center> <br/> <small>Para mayor información contáctese con el administrador o con soporte técnico.</small></div>',
	  'type'=>'error',
	  'html'=>true,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// ERROR ASIGNACIÓN DE FAENA
$GLOBALS['_ERROR']->offsetSet('e_'.md5(3), new ArrayObject(
	array(
	  'title'=>'Error de asignación de faenas',
	  'text'=>'<div><center>El usuario no tiene ninguna faena asignada.</center> <br/> <small>Para mayor información contáctese con el administrador o con soporte técnico.</small></div>',
	  'type'=>'error',
	  'html'=>true,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// SESSION TIMEOUT
$GLOBALS['_ERROR']->offsetSet('e_'.md5('session_timeout'), new ArrayObject(
	array(
		'title'=>'Su sesión se ha cerrado automáticamente',
	  'text'=>'Por su seguridad la sesión ha sido cerrada automáticamente.<br/><br/>',
	  'footer'=>'<a href="'.$GLOBALS['LOGIN'].'" target="_self" class="btn btn-info">Ir al login</a>',
	  'type'=>'warning',
	  'html'=>false,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// USUARIO DESACTIVADO
$GLOBALS['_ERROR']->offsetSet('e_'.md5('usuario_inactivo'), new ArrayObject(
	array(
		'title'=>'Usuario desactivado',
		'text'=>'El usuario se encuentra inactivo, para mayor información contáctese con el administrador.',
		'type'=>'warning',
		'footer'=>'',
		'html'=>false,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// INGRESO SIN CREDENCIALES
$GLOBALS['_ERROR']->offsetSet('e_'.md5('acceso_denegado'), new ArrayObject(
	array(
		'title'=>'Acceso denegado',
		'text'=>'Está intentando acceder a un recurso protegido sin las credenciales de seguridad necesarias. <br/>Debe iniciar sesión.',
		'type'=>'error',
		'footer'=>'<a href="'.$GLOBALS['LOGIN'].'" target="_self" class="btn btn-info">Ir al login</a>',
		'html'=>false,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

// ERROR USUARIO NO ENCONTRADO
$GLOBALS['_ERROR']->offsetSet('e_'.md5('usuario_no_encontrado'), new ArrayObject(
	array(
		'title'=>'No es posible obtener los datos de su cuenta',
		'text'=>'<div><center>No se ha encontrado el usuario.</center> <br/> '.
      '<small>Para mayor información contáctese con el administrador o con soporte técnico.</small></div><br/>',
		'type'=>'warning',
		'footer'=>'<a href="'.$GLOBALS['LOGIN'].'" target="_self" class="btn btn-info">Ir al login</a>',
		'html'=>true,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));

//ERROR PERMISO INSUFICIENTE PERIL
$GLOBALS['_ERROR']->offsetSet('e_'.md5('perfil_permiso_insuficiente'), new ArrayObject(
	array(
		'title'=>'Permisos insuficientes',
		'text'=>'El perfil actual no tiene acceso al módulo que está intentando accesar. <br/> Si Ud. tiene asignado más perfiles a su cuenta puede cambiar de perfil e intentar acceder nuevamente. <br/>Si cree que es un error puede contactarse con soporte técnico o el administrador del sistema.',
		'type'=>'warning',
		'footer'=>'',
		'html'=>true,
	),ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS));
