<?php
  require 'autoload.php';

  $acc = new Acceso();

  $db = DB2::getInstance();

  $sql = sprintf("SELECT * FROM uman_usuarios WHERE USUARIO='%s';", $_SESSION[session_id()]['user']);
  $usuario = $db->query($sql);

  if($usuario->count() <= 0){
    echo 'Location: '.$GLOBALS['ERRORS'].'?error='.md5('usuario_no_encontrado');exit();
    header('Location: '.$GLOBALS['ERRORS'].'?error='.md5('usuario_no_encontrado'));
  }
  
  $usuario = $usuario->results()[0];  
  
?>
<style>
  .row.data div:nth-child(2){
    font-weight: 800;
  }
  @media (max-width: 450px){
    .row div:nth-child(1){
      text-align: left !important;
    }
    .row div:nth-child(2){
      margin-left: 5px;
      margin-right: 5px;
    }
  }
</style>
<div class="container">
  <div class="cc-divider">Mi Cuenta</div>
  
  <div class="row" style="background: white;">
    <form name="frm-cuenta" id="frm-cuenta">
      <input type="hidden" name="ui" value="<?=$usuario->ID_USUARIO?>">
      <!-- DATOS DE USUARIO -->
      <div class="<?=Core::col(6)?>">
        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>
      
        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Usuario : </div>
          <div class="<?=Core::col(7,7,7,12)?>"><?=$usuario->USUARIO?></div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>

        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Nombre : </div>
          <div class="<?=Core::col(7,7,7,12)?>"><?=$usuario->NOMBRE?></div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>

        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Perfil(es) : </div>
          <div class="<?=Core::col(7,7,7,12)?>">
            <ul>
              <?php
                foreach ($perfiles as $p) {
                  echo '<li>'.utf8_encode($p->nombre).'</li>';
                }
              ?>
            </ul>
          </div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>

        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Idioma : </div>
          <div class="<?=Core::col(7,7,7,12)?>">
            <select name="idioma" class="form-control">      
              <option value="es" <?=($_SESSION[session_id()]['lang']=='es'?'selected':'')?>>Español</option>
              <option value="en" <?=($_SESSION[session_id()]['lang']=='en'?'selected':'')?>>Inglés</option>
              <option value="po" <?=($_SESSION[session_id()]['lang']=='po'?'selected':'')?>>Portugués</option>
              <option value="de" <?=($_SESSION[session_id()]['lang']=='de'?'selected':'')?>>Alemán</option>
            </select>
          </div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>
        
        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Correo Electrónico : </div>
          <div class="<?=Core::col(7,7,7,12)?>">
            <input type="email" class="form-control" name="email" id="email" aria-describedby="emailHelp" placeholder="Ingrese su correo" value="<?=$usuario->CORREO?>">
          </div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>
        
        <div class="row data">
          <div class="<?=Core::col(5,5,5,12)?> text-right">Faena(s) : </div>
          <div class="<?=Core::col(7,7,7,12)?>">
            <ul>
              <?php
                foreach($faenas as $f){
                  echo('<li>'.utf8_encode($f->nombre_faena).'</li>');
                }
              ?>
            </ul>
          </div>
        </div>

        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>
      </div>

      <!-- CAMBIAR CONTRASEÑA -->
      <div class="<?=Core::col(6)?>">
        <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>

        <div class="row data">
          <div class="<?=Core::col(12)?> text-center">Cambiar Contraseña </div>
          <div class="<?=Core::col(8,8,12,12)?> <?=Core::offset(3,3)?>">
            <div id="editar-password">
              <small>Ingrese su contraseña actual.</small>
              <input type="password" class="form-control" name="pass" id="pass" aria-describedby="passHelp" placeholder="Ingrese su contraseña actual">
              <small>A continuación ingrese su nueva contraseña.</small>
              <input type="password" class="form-control" name="pass1" id="pass1" aria-describedby="passHelp" placeholder="Ingrese su nueva contraseña">
              <input type="password" class="form-control" name="pass2" id="pass2" aria-describedby="passHelp" placeholder="Repita su contraseña">
            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- BOTÓN GUARDAR -->
    <div class="<?=Core::col(12)?>">
      <div class="row">
        <div class="<?=Core::col(12)?>">
            <button class="btn btn-sm btn-primary guardar center-block">
              <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
            </button>
        </div>
      </div>

      <div class="row"><div class="<?=Core::col(12)?>">&nbsp;</div></div>
    </div>

  </div>
</div>

<script type="text/javascript">
  $(function(){
    $("button.guardar").click(function(){
      var params = $("#frm-cuenta").serializeArray();
      params.push({name:'modo', value:'actualizar-usuario'});
      var error = '';

      console.log(params[4].name + '//' + params[5].name + '//' + params[4].value + '//' + params[5].value);
      if(params[4].name == 'pass1' && params[5].name == 'pass2')
        if(params[4].value != params[5].value)
          error = '<li>Las contraseñas ingresadas no coinciden.</li>';

      if(params[2].name == 'email' && params[2].value !='' && !ValidateEmail(params[2].value)) 
        error += '<li>El correo ingresado no es válido.</li>';

      if(error!=''){
        swal({
          title: 'Error de validación',
          text: 'Por favor verifique la información ingresada: <ul>'+error+'</ul>',
          html: true,
          type: 'error',
        });
      }
      else{
        

        $.post('ajax/crud-usuarios.php', params, 
        function(data){
          swal(data);
          if(data.relogin){
            swal({
              title: "Su contraseña ha cambiado.",
              text: "Ud. ha cambiado su contraseña, por lo que deberá iniciar sesión nuevamente.",
              timer: 5000,
              showConfirmButton: false
            });
            window.location = '../cerrarsesion.php';
          }
        });
      }
    });
  });

  function ValidateEmail(mail){  
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)){  
      return (true)  
    }  
    return (false)  
  } 
</script>