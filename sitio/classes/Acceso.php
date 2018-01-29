<?php 
  // require __DIR__.'/../errors/error_codes.php';
  include __DIR__.'/../errors/error_codes.php';

  class Acceso{

    private $permitido = false;
    private $mensaje = '';
    private $codigo  = '';

    private $excepciones = array(
      '/ajax/', '/data-', '/cambiar-', 'monitor.php',
    );

    function __construct($autoStartSession=false, $showErrors=true){      
      //Obtiene el módulo actual al que se está intentando acceder
      $modulo = isset($_GET['s']) ? $_GET['s'] : NULL;
      // echo $modulo;
      $_self = $_SERVER['SCRIPT_NAME'];

      if($autoStartSession) @session_start();
      //Si no existe nada en la variable global $_SESSION, inmediatamente arroja un error de acceso
      if(!isset($_SESSION[session_id()])) header('Location: '.$GLOBALS['ERRORS'].'?error='.md5('acceso_denegado'));

      $ses = $_SESSION[session_id()];

      //Si el archivo accesado está dentro de la excepciones no comprueba si tiene acceso como módulo
      $comprobar_modulo = TRUE;
      foreach($this->excepciones as $e){
        if(stripos($_self, $e) !== FALSE) $comprobar_modulo = FALSE;
      }
      if($comprobar_modulo && $modulo != NULL){
        if(DEBUG === true) echo '<small>'.$modulo.'</small>';
        //Si el perfil no tiene acceso al módulo actual, envía un mensaje indicándolo
        //y no permite continuar con la carga de dicho módulo
        $tiene_acceso_modulo = (new Usuario())->obtenerAccesoPerfil($ses['id'], $modulo, $ses['perfilactivo']->id);
        // var_dump($tiene_acceso_modulo);
        if( $tiene_acceso_modulo === FALSE ){
          $this->permitido = false;
          $this->codigo = 'e_'.md5('perfil_permiso_insuficiente');
          $this->mensaje = $_self.'<br/>'.$GLOBALS['_ERROR']['e_'.md5('perfil_permiso_insuficiente')]->text;
          if($showErrors) die($this->mensaje);
          return 0;
        }
      }

      $this->permitido = false;
      if(is_array($ses)){
        if(array_key_exists('user',$ses) && array_key_exists('pass',$ses) && array_key_exists('faena',$ses) && 
          array_key_exists('csrf_token',$ses)){

          $db = DB2::getInstance();

          $sql = sprintf("SELECT * FROM uman_usuarios WHERE ID_USUARIO=%d;", $ses['id']);

          $res = $db->query($sql);
          $res = $res->results();

          if(count($res) == 1){
            $res = $res[0];
            if($ses['pass'] == $res->PASS && $res->ACTIVO==1){
              $faenas = (new Usuario())->obtenerFaenas($ses['id']);
              $this->permitido = false;
              foreach($faenas as $f){
                if($ses['faena'] == $f->nombre_db) $this->permitido = true;
              }
            }
            else{
              if($res->ACTIVO!=1)
                header('Location: '.$GLOBALS['ERRORS'].'/?error='.md5('usuario_inactivo'));
              else
                header('Location: '.$GLOBALS['ERRORS'].'/?error='.md5('0'));
            }
          }
          else{
            header('Location: '.$GLOBALS['ERRORS'].'/?error='.md5('0'));
          }
        }
        else{
          $this->codigo = 'e_'.md5('acceso_denegado');
          $this->mensaje = $GLOBALS['_ERROR']['e_'.md5('acceso_denegado')]->text;
          if($showErrors) die($this->mensaje);
        }
      }
      else{
        $this->codigo = 'e_'.md5('acceso_denegado');
        $this->mensaje = $GLOBALS['_ERROR']['e_'.md5('acceso_denegado')]->text;
        if($showErrors) die($this->mensaje);
      }
    }

    public function Permitido(){
      return $this->permitido;
    }

    public function MensajeError(){
      return $this->mensaje;
    }

    public function CodigoError(){
      return $this->codigo;
    }
  
  }//End Class