<?php
	require_once("conectar_db.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
        <title><?php echo $sghnombre; ?></title>
        <meta http-equiv=content-type content="text/html; charset=windows-1250"/>
        <!-- estilos: aplicación... -->
        <link href="css/interface.css" type="text/css" rel="stylesheet"/>
        <link href="css/autocomplete.css" type="text/css" rel="stylesheet"/>
        <!-- estilos: ventanas... -->
        <link href="css/windows/default.css" rel="stylesheet" type="text/css"/>
        <link href="css/windows/alphacube.css" rel="stylesheet" type="text/css"/>
	 <!--- javascript: Jquery... -->
        <script type="text/javascript" src="js/jquery-1.10.2.js"></script>
        <script type="text/javascript">
            var $j=jQuery.noConflict();
        </script>
        <!--- javascript: ajax framework... -->
        <script src="js/prototype.js" type="text/javascript"></script>
        <!--- javascript: prototype autocomplete... -->
        <script src="js/autocomplete.js" type="text/javascript"></script>
        <script src="js/dyntable.js" type="text/javascript"></script>
        <!--- javascript: manejador de ventana... -->
        <script src="js/window.js" type="text/javascript"></script>
        <!--- javascript: funciones comúnes... -->
        <script src="js/common.js.php" type="text/javascript"></script>
        <!-- estilos: menú -->
        <link href="css/office_xp/office_xp.css" type="text/css" rel="stylesheet"/>
        <!-- javascript: librerías del menú... -->
        <script src="js/jsdo/jsdomenu.js" type="text/javascript"></script>
        <script src="js/jsdo/jsdomenubar.js" type="text/javascript"></script>
        <!-- estilos: calendario -->
        <link rel="stylesheet" type="text/css" media="all" href="css/calendar/calendar-blue.css" title="win2k-cold-1"/>
        <!-- javascript: librerías de calendario... -->
        <script type="text/javascript" src="js/calendar.js"></script>
        <script type="text/javascript" src="js/lang/calendar-es.js"></script>
        <script type="text/javascript" src="js/calendar-setup.js"></script>
        <script language="javascript">
                
		<!--
		// Timeout de Bloqueo para forzar ingreso de contraseńa
		// despues de un tiempo de inactividad.
                
		var _lock_timer;
		var __light_logger;
		__set_timeout_timer = function() {
			clearTimeout(_lock_timer);
			if(typeof(__light_logger)=='undefined' || __light_logger==null)
                            _lock_timer = setTimeout( bloquear_ventana , 3600000 ); // milisegundos por defecto 900000 [ms] (15 minutos)
				// por defecto 1800000 [ms] (15 minutos)
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
		-->
		</script>
	</head>
	<body>
    <div id="header">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td style="width: 50px;"><img src="imagenes/logo.png" style="margin: 4px;"></td>
            <td>
                <strong><?php echo $sghnombre; ?></strong><br>
                <span style="font-size: 24px;"><?php echo $sghinstitucion;?></span>
            </td>
            <td style="width:300px;">
                <table style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;" cellspacing="0">
                <tr>
                    <td><img src="iconos/vcard.png"></td>
                    <td><?php print ($_SESSION['sgh_usuario']); ?></td>
                </tr>
                <tr>
                    <td><img src="iconos/award_star_silver_3.png"/></td>
                    <td><i><?php print ($_SESSION['sgh_cargo']); ?></i></td>
                </tr>
                
                </table>
            </td>
          <td style="width:90px;">
            <div style="text-align: center; font-size: 10px;">            	<img src="iconos/comments.png" style="width:30px;height:30px;cursor:pointer;" onClick="chat();" /><br />
                <strong>Mensajes (<span id='chat_mensajes'>
			<?php 
						$id=$_SESSION['sgh_usuario_id']*1;
						$m=cargar_registro("SELECT count(*) AS msgs FROM chat WHERE func_id2=$id AND chat_estado=0;");
						echo $m['msgs']*1;
					?></span>)</strong></div>
            </td>
        </tr>
      </table>
      <?php require('cssmenu/cssmenu.php'); ?>
      </div>
      <div id="contenido"></div>
      <div id="carga" style="display: none;">
      	<div style="text-align: center; position:absolute; top:50%; width: 100%; height:70px; margin-top:-35px; overflow: hidden !important;">
      		<img src='imagenes/ajax-loader2.gif'><br/><strong>Cargando...</strong>
        </div>
      </div>
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
	</body>
</html>
