<?php

	require_once("conectar_db.php");
	

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML style='width:100%;height:100%;'><HEAD>
<TITLE>
<?php echo htmlentities($sghnombre); ?>
</TITLE>
<META http-equiv=content-type content="text/html; charset=windows-1250">

  <!-- estilos: aplicación... -->
  <LINK href="css/interface.css" type='text/css' rel='stylesheet'>
  <LINK href="css/autocomplete.css" type='text/css' rel='stylesheet'>

  <!-- estilos: ventanas... -->
  <LINK href="css/windows/default.css" rel="stylesheet" type="text/css" >
  <LINK href="css/windows/alphacube.css" rel="stylesheet" type="text/css" >


  <!--- javascript: ajax framework... -->
  <SCRIPT src="js/prototype.js" type="text/javascript"></SCRIPT>

  <!--- javascript: prototype autocomplete... -->
  <SCRIPT src="js/autocomplete.js" type="text/javascript"></SCRIPT>
  <SCRIPT src="js/dyntable.js" type="text/javascript"></SCRIPT>
  
  <!--- javascript: manejador de ventana... -->
  <SCRIPT src="js/window.js" type="text/javascript"></script>

  <!--- javascript: funciones comúnes... -->
  <SCRIPT src="js/common.js.php" type="text/javascript"></SCRIPT>
  
  
  <!-- estilos: menú -->
  <LINK href="css/office_xp/office_xp.css" type='text/css' rel='stylesheet'>
  
  <!-- javascript: librerías del menú... -->
  <SCRIPT src="js/jsdo/jsdomenu.js" type='text/javascript'></SCRIPT>
  <SCRIPT src="js/jsdo/jsdomenubar.js" type='text/javascript'></SCRIPT>

  <SCRIPT src="js/mainmenu.js.php" type='text/javascript'></SCRIPT>

  <!-- estilos: calendario -->
  <link rel="stylesheet" type="text/css" media="all" href="css/calendar/calendar-blue.css" title="win2k-cold-1" />

  <!-- javascript: librerías de calendario... -->
  <script type="text/javascript" src="js/calendar.js"></script>
  <script type="text/javascript" src="js/lang/calendar-es.js"></script>
  <script type="text/javascript" src="js/calendar-setup.js"></script>


<script>

  // Timeout de Bloqueo para forzar ingreso de contraseńa
  // despues de un tiempo de inactividad.

  var _lock_timer;
  var __light_logger;
  
  __set_timeout_timer = function() {
    clearTimeout(_lock_timer);
    if(typeof(__light_logger)=='undefined' || __light_logger==null)
      _lock_timer = setTimeout( bloquear_ventana , 900000 ); // milisegundos
      // por defecto 900000 [ms] (15 minutos)
  }
  
  document.onmousemove = __set_timeout_timer;
  
  function chat() {
	  
	  top=Math.round(screen.height/2)-225;
      left=Math.round(screen.width/2)-375;

	  var chat=window.open('chat.php',
	        'win_chat', 'toolbar=no, location=no, directories=no, status=no, '+
			'menubar=no, scrollbars=yes, resizable=no, width=750, height=450, '+
			'top='+top+', left='+left);
		
	  chat.focus();
	  
  }
  
  var notify_closed=false;
  
  function actualizar_msgs(){
  
		var myAjax = new Ajax.Updater(
		'chat_mensajes', 
			'chat_status.php', 
			{
				method: 'get', 
				evalScripts: true,
				onComplete:function(r) {
					var mnum=r.responseText*1;
					
					if(mnum>0 && !notify_closed) {
						$('notify_body').innerHTML='<span style="cursor:pointer;" onClick="chat();cerrar_notificacion();">Usted tiene <b>'+mnum+'</b> mensajes sin leer.</span>';
						mostrar_notificacion();
					}
					
					if(mnum==0) {
						$('notify').hide();
						notify_closed=false;
					}
				}
			}
			
			);  
  
  }
  
  function mostrar_notificacion() {
		$('notify').style.left=(window.innerWidth-310)+'px';
		$('notify').show();
  }

  function cerrar_notificacion() {
	    notify_closed=true;
		$('notify').hide();
  }
  
  setInterval('actualizar_msgs();',15000);
  
</script>


</HEAD>

<BODY style='width:100%; height:100%;' onresize='
$("contenido").style.height=window.innerHeight-70;
' 
leftMargin=0 topMargin=0 rightMargin=0 
onload='initjsDOMenu(); __set_timeout_timer(); '>


<DIV id='interfacediv' class='interfacediv'>

<TABLE height="100%" cellSpacing=0 cellPadding=0 width="100%">
  <TBODY>
  <TR>
    <TD height=50>
      <DIV class='titlediv'>
      <table width='100%'><tr>
      <td style='width: 50px;'><img src='imagenes/logo.png'></td><td>
      <b><?php echo htmlentities($sghnombre); ?></b><br>
      <font size=+2><?php echo htmlentities($sghinstitucion); ?></font>
      </td><td>
      
      <div align='right'>
      <table 
      style='font-family: Arial, Helvetica, sans-serif; font-size: 12px;' 
      cellspacing=0>
      <tr><td><img src='iconos/vcard.png'></td>
      <td><?php print (htmlentities($_SESSION['sgh_usuario'])); ?></td></tr>
      
      <tr><td><img src='iconos/award_star_silver_3.png'></td>
      <td><?php print (htmlentities($_SESSION['sgh_cargo'])); ?></td></tr>
      
      </table>
      </div>

      
      </td><td rowspan='2' style='font-size:11px;width:85px;'><center>
      
      <img src='iconos/comments.png' style='width:30px;height:30px;cursor:pointer;' onClick='chat();' /><br /><b>Mensajes (<span id='chat_mensajes'><?php 

		$id=$_SESSION['sgh_usuario_id']*1;
      
		$m=cargar_registro("SELECT count(*) AS msgs FROM chat WHERE func_id2=$id AND chat_estado=0;");
		
		echo $m['msgs']*1;
      
?></span>)</b></center></td>
      <td style='width: 50px;'></td>
      </tr></table>
      </DIV>
    </TD>
  </TR>
  <TR>
    <TD height=20 style='background-color: #ffffff;'></TD></TR>
  <TR>
    <TD id='contenedor' class='contentdiv'>
      <DIV id='contenido' class='contentdiv' style='OVERFLOW: auto;'>
      
      </DIV>
      <div id='carga' style='display: none;'>
      <center>
      <img src='imagenes/ajax-loader2.gif'><br><br><b>Cargando...</b>
      </div>
    </TD>
  </TR>
  </TBODY>
</TABLE>
</DIV>

<div style='display:none;border:1px solid black;position:fixed;top:60px;left:20px;z-index:10000;width:280px;height:60px;background-color:skyblue;' id='notify'>

<table style='width:100%;height:100%;font-size:11px;'>
	<tr>
		<td> 
		<center><img src='iconos/comments.png' style='width:35px;height:35px;' /></center>
		</td>
		<td style='width:80%;' id='notify_body'></td>
		<td><center><img src='iconos/cross.png' style='cursor:pointer;' onClick='cerrar_notificacion();' /></center></td>
	</tr>
</table>

</div>

</BODY>

</HTML>
