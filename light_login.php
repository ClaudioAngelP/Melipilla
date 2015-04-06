<?php
session_start();
$usuario = $_GET['usuario'];
session_destroy();

?>

<script>
  
  submit_light_login = function() {
  
    if(trim($('___pass').value)=='') {
      alert('No ha ingresado clave alguna.');
      return;
    }
  
    var myAjax = new Ajax.Request(
    'conectar_db.php',
    {
      method: 'post',
      parameters: 'prueba_interna&usuario='+encodeURIComponent($('___usuario').value)+'&pass='+encodeURIComponent($('___pass').value),
      onComplete: function(respuesta) {
        if(respuesta.responseText=='') {
          __light_logger.close();
          __light_logger=null;
          
          __set_timeout_timer();
        } else {
          alert('Clave Incorrecta.');
          return;
        }
      }
    });
    
  }

</script>

<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%">
  <TBODY>
  <TR>
    <TD id='contenedor'>
      <center>
      
      <table style='width:270px;'><tr><td>
      
      <div class='sub-content'>
      <table cellpadding=0 cellspacing=0><tr><td width=30>
      <center><img src='iconos/error.png'></center>
      </td><td style='font-size: 12px;'>
      Su sesi&oacute;n a estado <b>m&aacute;s de 15 minutos
      inactiva</b>; por su seguridad, debe reingresar su clave para validarse nuevamente en el sistema.</td></tr></table>
      </div>
      
      <div class='sub-content'>
      
      <form id='__light_login_form' name='__light_login_form' 
      onSubmit='return false;'>
      
      <table style='width:250px;'>
      
      <tr class='tabla_header'>
      <td colspan=3>
      <img src='iconos/key.png'>
      <b>Credenciales de Acceso al Sistema</b></td></tr>
      <tr>
      <td width=20><img src='iconos/vcard.png'></td>
      <td>RUT:</td>
     
      <td>
      <input type='text' id='___usuario' name='___usuario' value='<?php echo $usuario; ?>' DISABLED>
      </td>
      </tr>
      <tr>
      <td width=20><img src='iconos/textfield_key.png'></td>
      <td>Clave:</td>
      <td>
      <input type='password' id='___pass' name='___pass'>
      </td>
      </tr>
      <tr>
      <td colspan=3>
      <center>
      <input type='submit' value='[ Ingresar ]' onClick='submit_light_login();'>
      </center>
      </td>
      </tr>
      
      </table>
      
      </form>
      
      </div>
      
      </td></tr></table>
      
    </TD>
  </TR>
  </TBODY>
</TABLE>

<script>

$("___pass").focus();

</script>

