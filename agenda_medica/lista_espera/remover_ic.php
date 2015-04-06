<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_GET['inter_id'])) {
		$inter_id=$_GET['inter_id']*1;
		
		$i=cargar_registro("
		SELECT *, i1.inst_nombre AS institucion_solicitante,
		inter_fecha_salida::date AS inter_fecha_salida 
		FROM interconsulta
		JOIN pacientes ON inter_pac_id=pac_id
		LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id		LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id		LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id		LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id		LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id		LEFT JOIN profesionales_externos ON prof_id=inter_prof_id    	LEFT JOIN especialidades ON inter_especialidad=esp_id		LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id		LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id		LEFT JOIN diagnosticos ON inter_diag_cod=diag_cod		LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id		LEFT JOIN interconsulta_estado ON inter_estado=ice_id				WHERE inter_id=$inter_id
		", true);

	} else { 
		$oa_id=$_GET['oa_id']*1;

		$i=cargar_registro("
		SELECT *, 
		oa_folio AS inter_folio,
		oa_fecha::date AS inter_fecha,		
		i1.inst_nombre AS institucion_solicitante, 
		oa_fecha_salida::date AS inter_fecha_salida,
		oa_motivo_salida AS inter_motivo_salida
		FROM orden_atencion
		JOIN pacientes ON oa_pac_id=pac_id
		LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id		LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id		LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id		LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id		LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id		LEFT JOIN profesionales_externos ON prof_id=oa_prof_id    	LEFT JOIN especialidades ON oa_especialidad=esp_id		LEFT JOIN instituciones AS i1 ON oa_inst_id=inst_id		LEFT JOIN instituciones AS i2 ON oa_inst_id2=i2.inst_id		LEFT JOIN diagnosticos ON oa_diag_cod=diag_cod		LEFT JOIN interconsulta_estado ON oa_estado=ice_id				WHERE oa_id=$oa_id
		", true);

	}	

?>

<html>
<title>Remover <?php if(isset($inter_id)) echo 'I.C.'; else echo 'O.A.';?> de Lista de Espera</title>

<?php cabecera_popup('../..'); ?>

<script>

	guardar=function() {
		
		if(!validacion_fecha($('inter_fecha_salida'))) {
			alert( "Debe ingresar fecha de salida v&aacute;lida.".unescapeHTML() );
			return;
		}
		
		if($('inter_salida').value==-1) {
			alert("Debe seleccionar motivo de salida.");
			return;
		}
		
		var myAjax=new Ajax.Request(
			'sql_remover.php',
			{
				parameters:'<?php if(isset($inter_id)) echo 'inter_id='.$inter_id; else echo 'oa_id='.$oa_id ?>&'+$('inter_salida').serialize()+'&'+$('inter_fecha_salida').serialize(),
				onComplete:function() {
					var fn=window.opener.listado.bind(window.opener);
					fn();
					window.close();	
				}	
			}		
		);
					
	}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>

<div class='sub-content'>
<img src='../../iconos/delete.png'>
<b>Remover <?php if(isset($inter_id)) echo 'I.C.'; else echo 'O.A.';?> de Lista de Espera</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>
<tr><td style='text-align:right;width:30%;'>
Nro. de Folio:
</td><td style='font-weight:bold;font-size:16px;'>
<?php echo ($i['inter_folio']*1>0)?$i['inter_folio']:'(n/a)'; ?>
</td>
</tr>

<tr><td style='text-align:right;width:30%;'>
Inst. Solicitante:
</td><td style='font-weight:bold;'>
<?php echo $i['institucion_solicitante']; ?>
</td>
</tr>

<tr><td style='text-align:right;width:30%;'>
Fecha de Ingreso:
</td><td style='font-weight:bold;'>
<?php echo $i['inter_fecha']; ?>
</td>
</tr>

<tr><td style='text-align:right;width:30%;'>
R.U.T. Paciente:
</td><td style='font-weight:bold;'>
<?php echo $i['pac_rut']; ?>
</td>
</tr>

<tr><td style='text-align:right;width:30%;'>
Nombre Completo:
</td><td style=''>
<?php echo trim($i['pac_nombres'].' '.$i['pac_appat'].' '.$i['pac_apmat']); ?>
</td>
</tr>

</table>

</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr><td style='text-align:right;'>
Motivo Salida:
</td><td>
<input type='text' id='inter_fecha_salida' name='inter_fecha_salida' size=10 style='text-align:center;'
onKeyUp='validacion_fecha(this);' value='<?php if($i['inter_fecha_salida']!='') echo $i['inter_fecha_salida']; else echo date('d/m/Y'); ?>' />
</td></tr>

<tr><td style='text-align:right;'>
Motivo Salida:
</td><td>
<select id='inter_salida' name='inter_salida'>
<option value='-1'>(Seleccione Causal de Salida...)</option>
<?php 
	echo desplegar_opciones_sql("
		SELECT icc_id, icc_desc FROM interconsulta_cierre
		ORDER BY icc_id	
	", $i['inter_motivo_salida']*1);
?>
</select>
</td></tr>

<tr>
<td colspan=2>
<center>
<input type='button' id='guarda' name='guarda' 
value='Guardar Registro...' onClick='guardar();' />
</center>
</td>
</tr>
</table>
</div>

</div>

</body>

</html>

<script>

	validacion_fecha($('inter_fecha_salida'));

</script>
