<?php

  require_once('config.php');

  session_start();
  session_unset();
  session_destroy();
  
  $fsess=$sghsessionpath.''.$_SERVER['REMOTE_ADDR'].'.session';
  
  /*if(file_exists($fsess)) {
    unlink($fsess);
  }*/
    
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML style='width:100%;height:100%;'>
<HEAD>
<TITLE>
Sistema de Gestión y Control Hospitalaria
</TITLE>
<META http-equiv=content-type content="text/html; charset=windows-1250">

<!-- estilos: aplicación... -->
<LINK href="css/interface.css" type='text/css' rel='stylesheet'>

<!-- estilos: ventanas... -->
<LINK href="css/windows/default.css" rel="stylesheet" type="text/css" >
<LINK href="css/windows/alphacube.css" rel="stylesheet" type="text/css" >


<!--- javascript: ajax framework... -->
<SCRIPT src="js/prototype.js" type="text/javascript"></SCRIPT>

<!--- javascript: manejador de ventana... -->
<SCRIPT src="js/window.js" type="text/javascript"></script>

</HEAD>

<BODY leftMargin=0 topMargin=0 rightMargin=0 style='width:100%;height:100%;font-family: Arial, Liberation Sans, sans-serif;'
onLoad='$("usuario").focus();'>


<DIV class='interfacediv'>
<TABLE cellSpacing=0 cellPadding=0 width="100%">
  <TBODY>
  <TR>
    <TD id='contenedor' background='white'>
      <center>
      
      
      <table style='width:270px;'>
      <tr>
      <td><img src='imagenes/login_background.jpg' style='padding:30px;' /></td>
      </tr>
      <tr><td>
      
      
      <center>
      
      
	  <table>
		  <tr>
			  <td> 
      <div class='sub-content'>
      
      <form action='interface.php' method='post'>
      
      <table style='width:250px;'>
      
      <tr class='tabla_header'>
      <td colspan=3>
      <img src='iconos/key.png'>
      <b>Credenciales de Acceso al Sistema</b></td></tr>
      <tr>
      <td width=20><img src='iconos/vcard.png'></td>
      <td>RUT:</td>
     
      <td>
      <input type='text' id='usuario' name='usuario'>
      </td>
      </tr>
      <tr>
      <td width=20><img src='iconos/textfield_key.png'></td>
      <td>Clave:</td>
      <td>
      <input type='password' id='pass' name='pass'>
      </td>
      </tr>
      <tr>
      <td colspan=3>
      <center>
      <input type='submit' value='[ Ingresar ]'>
      </center>
      </td>
      </tr>
      
      </table>
      
      </form>
      
      </div>
      
      
      </td>
		  </tr>
	  </table>
	  
	  </center>
      
      </td></tr></table>
      
    </TD>
  </TR>
  </TBODY>
</TABLE>
</DIV>


</BODY>

</HTML>

