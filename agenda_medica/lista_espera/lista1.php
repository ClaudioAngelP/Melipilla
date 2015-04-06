<?php 

	require_once('../../conectar_db.php');
?>

<script>

listar_inter=function() {

	var myAjax=new Ajax.Updater(
		'listado',
		'agenda_medica/lista_espera/listado_inter.php',
		{
			method:'post',
			parameters: $('filtro').serialize()
		}	
	);

}

listar_xls = function() {
  	
	$('xls').value=1;
	$('filtro').submit();

}
</script>

<center>
	<div class='sub-content' style='width:750px;'>
		<div class='sub-content'>
			<img src='iconos/table.png'>
				<b>Registro de Lista de Espera Consultas e I.Q.</b>
		</div>
		<div class='sub-content'>

			<form id='filtro' name='filtro' onSubmit='return false;' method="POST" action='agenda_medica/lista_espera/listado_inter.php' >
            <input type='hidden' id='xls' name='xls' value='0' />
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
						<td colspan=2>
							<center>
								<p>Fecha menor a 2 meses							  </p>
								<p>
								  <input type='button' onClick='listar_inter();' 
								value='-- Actualizar Listado Pantalla... --'>
							  </p>
                            </center>
						</td>
						<td>
							<center>
								<p>Foto Completa (.CSV)							  </p>
								<p>
								  <input type='button' onClick='listar_xls();' 
								value='-- Obtener Archivo CSV (Excel)... --'>
							  </p>
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
