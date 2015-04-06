<?php 

	require_once('../../conectar_db.php');

	$servhtml = desplegar_opciones_sql('SELECT * FROM centro_costo
	WHERE centro_hosp ORDER BY centro_nombre'); 

?>

<script>

listar_hosp=function() {

	var myAjax=new Ajax.Updater(
		'listado',
		'prestaciones/informe_hospitalizacion/listado_hosp.php',
		{
			method:'post',
			parameters: $('filtro').serialize()
		}	
	);

}


listar_ccs=function() 
{
	$('filtro').submit();
}

</script>

<center>
	<div class='sub-content' style='width:750px;'>
		<div class='sub-content'>
			<img src='iconos/table.png'>
				<b>Registro de Ingresos/Egresos Hospitalarios</b>
		</div>
		<div class='sub-content'>
			<form id='filtro' name='filtro' method="POST"
			action="prestaciones/informe_hospitalizacion/listado_csv.php"			
			onSubmit='return false;'>
				<table style='width:100%;'>
					<tr>
						<td style='text-align: right;'>Fecha Inicio:</td>
  						<td>
  							<input type='text' name='fecha1' id='fecha1' size=10
  							style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
  							<img src='iconos/date_magnify.png' id='fecha1_boton'>
  						</td>
  					</tr>
  					<tr>
  						<td style='text-align: right;'>Fecha Final:</td>
  						<td><input type='text' name='fecha2' id='fecha2' size=10
  						style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
  						<img src='iconos/date_magnify.png' id='fecha2_boton'>
  						</td>
  					</tr>
					<tr>
						<td style='text-align:right;'>Servicio Egreso:</td>
						<td>
							<select id='centro_ruta' name='centro_ruta'>
								<option value=-1 SELECTED>(Todos los Servicios...)</option>
								<?php echo $servhtml; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<center>
								<input type='button' onClick='listar_hosp();' 
								value='-- Actualizar Listado... --'>
							</center>
						</td>
						<td>
							<center>
								<input type='button' onClick='listar_ccs();' 
								value='-- Obtener Archivo CSV... --'>
							</center>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class='sub-content2' style='height:300px;overflow:auto;' id='listado'>
		</div>

	</div>
</center>

  <script>
  
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });

  
  </script>
  
