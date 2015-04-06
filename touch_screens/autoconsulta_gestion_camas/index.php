<?php 

	require_once('../../config.php');
	require_once('../../conectores/sigh.php');

?>

<html>

<title>Autoconsulta Gesti&oacute;n de Camas</title>

    <!--- javascript: ajax framework... -->
    <SCRIPT src="../../js/prototype.js" type="text/javascript"></SCRIPT>

    <!--- javascript: prototype autocomplete... -->
    <SCRIPT src="../../js/autocomplete.js" type="text/javascript"></SCRIPT>


<style>

html, body {
  height: 100%;
  width: 100%;
  padding: 0;
  margin: 0;
  font-family: Arial, Helvetica,sans-serif;
}

#full-screen-background-image {
  z-index: -999;
  min-height: 100%;
  min-width: 1024px;
  width: 100%;
  height: auto;
  position: fixed;
  top: 0;
  left: 0;
}

#wrapper {
  position: relative;
  width: 800px;
  min-height: 400px;
  margin: 100px auto;
  color: #333;
}

.boton_grande {
	display: block;
	margin:20px;
	padding:20px;
	color:#000000;
	background-color:rgba(200,200,200,0.4);
	border:1px solid yellowgreen;
	font-weight:bold;
}

.boton_grande:hover {
	display: block;
	margin:20px;
	padding:20px;
	color:#FFFFFF;
	background-color:rgba(0,0,0,0.8);
	border:1px solid yellowgreen;
}


.header_tabla td {
	color:#FFFFFF;
	background-color:rgba(0,0,0,0.8);
	border:1px solid yellowgreen;
	font-weight:bold;
	text-align:center;
}

.fila_tabla td {
	color:#000000;
	background-color:rgba(250,250,250,0.7);
}

.fila_tabla2 td {
	color:#000000;
	background-color:rgba(250,250,250,0.5);
}

</style>

<script>

function listado(tipo, id) {
	
	$('contenido').scrollTop=0;
	
	$('contenido').innerHTML='<br /><br /><br /><br /><br /><br />Espere un momento...';

	$('menu_principal').hide();
	$('contenido').show();

	var myAjax=new Ajax.Updater(
		'contenido',
		'listado.php',
		{ method:'post', parameters: 'tipo='+tipo+'&id='+id,
		onComplete: function(r) {
		
				setTimeout("$('contenido').scrollTop=0;", 150);
		
			} 
		}
	);
	
}

function volver() {
	$('menu_principal').show();
	$('contenido').hide();
}

</script>


<body style='font-size:18px;'>

<center>

<img alt="full screen background image" src="fondo1.jpg" id="full-screen-background-image">

<input type='button' value='-- Volver al Inicio... --' style='font-size:35px;padding:10px;margin:15px;' onClick='volver();' />

<div style='width:80%;height:80%;overflow:auto;border:1px solid skyblue;display:none;background-color:rgba(50,50,220,0.8);' id='contenido'>


</div>

<div style='width:80%;height:80%;overflow:auto;border:1px solid skyblue;background-color:rgba(50,50,220,0.8);' id='menu_principal' >

<center>
<a class='boton_grande' href='#' onClick='listado(0,0);'>Listado por Unidad/Servicio</a>
<a class='boton_grande' href='#' onClick='listado(1,0);'>B&uacute;squeda por M&eacute;dico Tratante</a>
<!--- <a class='boton_grande' href='#' onClick='listado(2);'>B&uacute;squeda por Paciente</a> -->
</center>

</div>

</center>


</body>
</html>
