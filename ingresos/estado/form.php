<?php 



    require_once('../../conectar_db.php');



		$institucionhtml = desplegar_opciones("institucion_solicita", 
		"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc'); 



?>

		<script>

		

		realizar_busqueda = function() {

		

      $('resultado').innerHTML='<br><br><br><img src="imagenes/ajax-loader3.gif"><br>Cargando'
		

			var myAjax = new Ajax.Updater(

			'resultado', 

			'interconsultas/listar_interconsultas.php', 

			{

				method: 'get', 

				parameters: 'tipo=estado_interconsultas&'+$('busqueda').serialize()

			}

			

			);

		

		}

		

		abrir_ficha = function(id) {

		

			inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,

			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

			

			inter_ficha.focus();

		

		}
		
		$('buscar').focus();

		

		</script>

		

		<center>

		

		<table width=700>

		<tr><td>

		

		<div class='sub-content'>

		<div class='sub-content'>

		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas</b>

		</div>

		<div class='sub-content'>

		<form name='busqueda' id='busqueda' onSubmit='return false;'>

		<table style='width:100%;'>

		<tr><td style='text-align: right;'>Buscar:

		</td><td>

		<input type='text' name='buscar' id='buscar' size=60 
    onKeyPress="if(event.which==13) realizar_busqueda();">

		</td></tr>

		<tr><td style='text-align: right;'>Ordenar por:

		</td><td>

		<select id='orden' name='orden'>

		<option value=4>N&uacute;mero de Folio</option>

		<option value=0>Fecha Ingreso</option>

		<option value=1>R.U.T./ID</option>

		<option value=2 SELECTED>Paterno - Materno - Nombre(s)</option>

		<option value=3>Especialidad</option>

		</select>

		<input type='checkbox' name='ascendente' id='ascendente' CHECKED> Ascendente

		</td></tr>

		<tr><td colspan=2>

		<center><input type='button' value='Actualizar Listado...' onClick='realizar_busqueda();'>

		</td></tr>

		</table>

		</form>

		</div>

		

		<div class='sub-content2' id='resultado' 

		style='overflow:auto;height:290px;'>

		<center>(No se ha efectuado una b&uacute;squeda...)</center>

		</div>

	

		</div>

		

		</div>

		

		

		</td>
		
		</tr>

		</table>

		

		</center>

			

