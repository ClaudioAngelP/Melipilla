<?php 

	require_once('conectar_db.php');

	
?>



<html>
<title>Mensajes</title>

<?php cabecera_popup('.'); ?>

<script>


cargar_usuarios = function() {
	
		var myAjax = new Ajax.Updater(
			'usuarios', 
			'chat_usuarios.php', 
			{
				method: 'get', 
				parameters: 'buscar='+serializar('personal_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
function enviar_mensaje() {

	if(trim($('mensaje').value)=='') return;
	
	var myAjax=new Ajax.Updater(
		'mensajes',
		'chat_mensajes.php',
		{
			method:'post',
			parameters:$('mensaje').serialize()+'&'+$('func_id').serialize(),
			onComplete:function() {
				$('mensajes').scrollTop=100000;
			}
		}
		);
		
	$('mensaje').value='';
	
}

function ver_mensajes() {
	
	if($('func_id').value*1==0) return;
	
	var myAjax=new Ajax.Updater(
		'mensajes',
		'chat_mensajes.php',
		{
			method:'post',
			parameters:$('func_id').serialize(),
			onComplete:function() {
				$('mensajes').scrollTop=100000;
			}
		}
		);
	
}

function enviar_archivo() {
	
	  top=Math.round(screen.height/2)-750;
      left=Math.round(screen.width/2)-75;

	  var sendfile =window.open('chat_archivo.php?'+$('func_id').serialize(),
	        'win_chat_file', 'toolbar=no, location=no, directories=no, status=no, '+
			'menubar=no, scrollbars=yes, resizable=no, width=600, height=150, '+
			'top='+top+', left='+left);
		
	  sendfile.focus();

	
}

</script>

<body class='fuente_por_defecto popup_background'>

<input type='hidden' id='func_id' name='func_id' value='0' />

<table style='width:100%;'>
	<tr>
		<td style='width:60%;'> 
		
		<div class='sub-content' id='func_nombre' name='func_nombre' style='font-size:24px;font-weight:bold;background-color:black;color:white;'></div>		
		<div class='sub-content2' id='mensajes' style='overflow:auto;height:340px;'>
		
		
		</div>
		
		<table style='width:100%;border:1px solid black;background-color:yellowgreen;'>
		<td><center><img src='iconos/user_comment.png' style='width:35px;height:35px;'></center></td>
		<td>		
		<input type='text' id='mensaje' name='mensaje' 
		onKeyUp='if(event.which==13) enviar_mensaje();' style='font-size:24px;width:100%;' />
		</td><td>
		<center>
		<input type='button' value='-- Enviar Archivo... --' onClick='enviar_archivo();' style='font-size:20px;' />
		</center></td></tr></table>
		
		</td>
		<td style='width:40%;'>
		
		
		<table style='width:100%;border:1px solid black;background-color:skyblue;'>
			<tr>
				<td><center><img src='iconos/magnifier.png' style='width:35px;height:35px;'></center></td>
				<td><input type='text' id='personal_filtro' name='personal_filtro' style='width:100%;'
				onKeyUp='cargar_usuarios();'></td>
			</tr>
		</table>
		
		
		<div class='sub-content' id='usuarios' style='overflow:auto;height:340px;'>
		
		<!-- Listado de usuarios -->
		
		</div>
		
		 </td>
	</tr>
</table>

</body>


</html>

<script>

	setInterval('ver_mensajes();',5000);
	setInterval('cargar_usuarios();',15000);


	Event.observe(window, "resize", function() {
		var height = window.innerHeight*1;

		$('mensajes').style.height=((height-120)+'')+'px';
		$('usuarios').style.height=((height-120)+'')+'px';
	});

    var height = window.innerHeight*1;

	$('mensajes').style.height=((height-120)+'')+'px';
	$('usuarios').style.height=((height-120)+'')+'px';


	cargar_usuarios();


</script>
