<?php 

	require_once('../../conectar_db.php');
	
	$aut=cargar_registros_obj("
		SELECT * FROM autorizacion_farmacos ORDER BY autf_nombre;
	");

?>

<script>

validacion_fecha2=function(obj) {
	if($(obj).value=='') {
		$('obj').value='';
		$('obj').style.background='';
	} else {
		validacion_fecha(obj);
	}
}

listar_pacientes=function() {
	
	var myAjax=new Ajax.Updater(
		'lista_pacientes',
		'ficha_clinica/autorizar_farmacos/listar_pacientes.php',
		{
			method:'post',
			parameters:$('autf_id').serialize()
		}
	);
	
}

guardar_paciente=function() {
	
	var myAjax=new Ajax.Request(
		'ficha_clinica/autorizar_farmacos/sql.php',
		{
			method:'post',
			parameters:$('autorizar').serialize(),
			onComplete:function(r) {
				listar_pacientes();
			}
		}
	);
	
}

eliminar_paciente=function(pac_id) {
	
	var conf=confirm('&iquest;Est&aacute; seguro que desea eliminar al paciente del listado? - NO HAY OPCIONES PARA DESHACER.'.unescapeHTML());
	
	if(!conf) return;
	
	var myAjax=new Ajax.Request(
		'ficha_clinica/autorizar_farmacos/sql_eliminar.php',
		{
			method:'post',
			parameters:$('autf_id').serialize()+'&pac_id='+pac_id,
			onComplete:function(r) {
				listar_pacientes();
			}
		}
	);
	
}

</script>

<form id='autorizar' name='autorizar' onSubmit='return false;'>

<input type='hidden' id='pac_id' name='pac_id' value='' />

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'><img src='iconos/pill.png' /> Autorizaci&oacute;n de F&aacute;rmacos</div>

<div class='sub-content'>
<table>
	<tr>
		<td>Tipo:</td>
		<td>
		<select id='autf_id' name='autf_id' onChange='listar_pacientes();'>
		<?php 
			
			for($i=0;$i<sizeof($aut);$i++) {
				
				print("<option value='".$aut[$i]['autf_id']."'>".htmlentities($aut[$i]['autf_nombre'])."</option>");
				
			}
			
		?>
		</select>
		</td>
	</tr>
</table>
</div>

<div class='sub-content'>
<table style='width:100%;'>
	
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
		<td colspan=3>
	
		<input type='text' size=45 id='pac_rut' name='pac_rut' value='' />
	
		
		</td>
	</tr>
	
	<tr>
	<td class='tabla_fila2'  style='text-align:right;'>Ficha Cl&iacute;nica:</td>
	<td class='tabla_fila' style='text-align:left;font-weight:bold;' id='pac_ficha' colspan=3>
	</td>
	</tr>
	
	<tr>
	<td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
	<td class='tabla_fila' colspan=3 style='font-weight:bold;' id='pac_nombre'>
	</td>
	</tr>

	<tr>
	<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
	<td class='tabla_fila' id='pac_fc_nac'>
	</td>
	<td class='tabla_fila2' colspan=2 style='text-align:center;width:40%;' id='pac_edad'>
	Edad:<b>?</b>
	</td>
	</tr>
	
	<tr>
		<td class='tabla_fila2'  style='text-align:right;'>Fecha Inicio-T&eacute;rmino:</td>
		<td class='tabla_fila' colspan=3>
		<input type='text' id='finicio' name='finicio' style='text-align:center;' size=10 value='<?php echo date('d/m/Y'); ?>' onBlur='validacion_fecha(this);' />
		<input type='text' id='ffinal' name='ffinal' style='text-align:center;' size=10 value='' onBlur='validacion_fecha2(this);' />
		</td>
	</tr>
	
</table>

<center>
<input type='button' id='guarda' name='guarda' onClick='guardar_paciente();' value='-- Guardar Autorizaci&oacute;n de F&aacute;rmacos... --' />
</center>

</div>

<div class='sub-content2' id='lista_pacientes' style='height:300px;overflow:auto;'>



</div>

</div>
</center>

</form>

<script>

listar_pacientes();

    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('pac_nombre').innerHTML=d[2];
		$('pac_id').value=d[4];
		$('pac_ficha').innerHTML=d[3];
		$('pac_fc_nac').innerHTML=d[7];
		$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    
    	
    }

    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      'autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);

		ingreso_diagnosticos=function(datos_diag) {
      	
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('nomd_diag_cod').value=cie10;
      	$('nomd_diagnostico').value=datos_diag[2].unescapeHTML();
      	
      }
      
      validacion_fecha($('finicio'));
      validacion_fecha2($('ffinal'));


</script>
