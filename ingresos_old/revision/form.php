<?php

  require_once('../../conectar_db.php');

	$especialidadhtml = desplegar_opciones("especialidades", 
	"esp_id, esp_desc",'','true','ORDER BY esp_desc'); 

?>



	

		<script>

		

		realizar_busqueda = function() {

		

			var myAjax = new Ajax.Updater(

			'resultado', 

			'interconsultas/listar_interconsultas.php', 

			{

				method: 'get', 

				parameters: 'tipo=revisar_interconsultas&'+$('busqueda').serialize()

			}

			

			);

		

		}

		

		abrir_ficha = function(id, inst) {

		

			inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=revisar_inter_ficha&inter_id='+id+'&institucion='+inst,

			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

			

			inter_ficha.focus();

		

		}

		

		</script>

		

		<center>

		

		<table width=650>

		<tr><td>

		

		<div class='sub-content'>

		<div class='sub-content'>

		<img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas Pendientes</b>

		</div>

		<div class='sub-content'>

		<form name='busqueda' id='busqueda'

		onChange='

		realizar_busqueda();

		'>

		<table>

		<tr><td>Especialidad Cl&iacute;nica:

		</td><td>

		<select id='especialidad' name='especialidad'>

		<?php echo $especialidadhtml; ?>

		</select>

		</td></tr>

		</table>

		</form>

		</div>

		

		<div class='sub-content2' id='resultado' 

		style='height:300px;overflow:auto;'>

		<center>(No se ha efectuado una b&uacute;squeda...)</center>

		</div>

	

		</div>

		

		</div>

		

		

		</td>

		</table>

		

		</center>

		

		<script>

		  realizar_busqueda();

		</script>



