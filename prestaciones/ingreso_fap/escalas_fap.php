<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_GET['fap_id']*1;

		$fap=cargar_registro("
			SELECT *, 
			fap_fecha::date AS fap_fecha,
			date_trunc('seconds',fap_fecha)::time AS fap_hora,
		    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias, fap_protocolo,
			COALESCE((
			SELECT hosp_id FROM hospitalizacion WHERE hosp_pac_id=pac_id AND hosp_fecha_egr IS NULL ORDER BY hosp_id DESC LIMIT 1 
			)::text,'(No encontrado...)') AS cta_cte
			FROM fap_pabellon
			LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id
			JOIN pacientes USING (pac_id)
			LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
			LEFT JOIN diagnosticos ON diag_cod=fap_diag_cod 
			LEFT JOIN clasifica_camas AS cc1 ON fap_pabellon.centro_ruta=cc1.tcama_id::text
			WHERE fap_id=$fap_id
		", true);

		$edad='';
      
		if($fap['edad_anios']*1>1) $edad.=$fap['edad_anios'].' a ';
		elseif($fap['edad_anios']*1==1) $edad.=$fap['edad_anios'].' a ';

		if($fap['edad_meses']*1>1) $edad.=$fap['edad_meses'].' m ';	
		elseif($fap['edad_meses']*1==1) $edad.=$fap['edad_meses'].' m ';

		if($fap['edad_dias']*1>1) $edad.=$fap['edad_dias'].' d';
		elseif($fap['edad_dias']*1==1) $edad.=$fap['edad_dias'].' d';		
		
		$pr=cargar_registros_obj("SELECT *, (SELECT glosa FROM codigos_prestacion_recaudacion WHERE codigo=fappr_codigo LIMIT 1) AS glosa FROM fap_prestacion WHERE fap_id=$fap_id", true);

?>

<html>
<title>Escalas Protocolo Quir&uacute;rgico</title>

<?php cabecera_popup('../..'); ?>

<script>

function cargar_series() {

	var myAjax=new Ajax.Updater(
		'series',
		'cargar_series.php',
		{method:'post',parameters:'fap_id=<?php echo $fap_id; ?>'}
	);

}

function cargar_grafico() {

	var myAjax=new Ajax.Updater(
		'grafico',
		'graficar_fap.php',
		{method:'post',parameters:'fap_id=<?php echo $fap_id; ?>'}
	);

}

function cargar_registro() {

	var myAjax=new Ajax.Updater(
		'registro_enfermeria',
		'reg_enfermeria_fap.php',
		{method:'post',parameters:'fap_id=<?php echo $fap_id; ?>'}
	);

}

function guardar_registro() {

	if(trim($('faprr_descripcion').value)=='') return;

	var myAjax=new Ajax.Updater(
		'registro_enfermeria',
		'reg_enfermeria_fap.php',
		{method:'post',parameters:'fap_id=<?php echo $fap_id; ?>&'+$('faprr_hora').serialize()+'&'+$('faprr_descripcion').serialize()}
	);

}

function agregar_serie() {

        var myAjax=new Ajax.Updater(
                'series',
                'cargar_series.php',
                {method:'post',parameters:'fap_id=<?php echo $fap_id; ?>'+$('datos').serialize()}
        );

}

function guardar_serie(fs_id) {

	for(var s=0;s<$('num_series').value;s++) {
	
		var sid=$('serie_'+s).value*1;
		
		$('editor_serie_'+s).value='';
	
		for(var i=0;i<10;i++) {
			
				if(trim($('horas_'+i).value)!='') {
			
				var hora=trim($('horas_'+i).value);
			
				var valor=trim($('valores_'+s+'_'+i).value);
					
				if(valor!='') {
					
					$('editor_serie_'+s).value=$('editor_serie_'+s).value+hora+' '+valor+"\n";
					
				}
				
				}
		
		}
	
	}

	var myAjax=new Ajax.Request(
		'sql_series.php',
		{method:'post',parameters:$('datos').serialize()+'&fs_id='+fs_id,onComplete:function(){cargar_series();cargar_grafico();}}

	);

}

function borrar_serie(i) {

	if(!confirm(('&iquest;Seguro desea eliminar la serie "'+$('nombre_serie_'+i).value+'"?').unescapeHTML())) return;

        var myAjax=new Ajax.Updater(
                'series',
                'cargar_series.php',
                {method:'post',parameters:'fap_id=<?php echo $fap_id; ?>&fs_id='+($('serie_'+i).value*1),onComplete:function(){cargar_grafico();}}
        );

}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/chart_line.png' />
<b>Escalas Protocolo Quir&uacute;rgico</b>
</div>

<form id='datos' name='datos' 
method='post' onSubmit='return false;'>

<input type='hidden' id='fap_id' name='fap_id' value='<?php if($fap) echo $fap['fap_id']; else echo '-1'; ?>' />

<input type='hidden' id='prev_id' name='prev_id' value='<?php if($fap) echo $fap['prev_id']; else echo '-1'; ?>' />
<input type='hidden' id='ciud_id' name='ciud_id' value='<?php if($fap) echo $fap['ciud_id']; else echo '-1'; ?>' />


<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td class='tabla_fila2'  style='text-align:right;'>Ficha Cl&iacute;nica:</td>
<td class='tabla_fila' style='font-weight:bold;width:25%;font-size:16px;' id='pac_ficha'>
<?php if($fap) echo $fap['pac_ficha']; else echo ''; ?>
</td>
<td class='tabla_fila2' style='text-align:right;'>
Nro. Folio:
</td><td class='tabla_fila' style='font-weight:bold;font-size:16px;'>
<?php if($fap) echo $fap['fap_fnumero']; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>R.U.N.:</td>
<td class='tabla_fila' style='font-weight:bold;font-size:16px;' id='pac_rut'>
<?php if($fap) echo formato_rut($fap['pac_rut']); else echo ''; ?>
</td>
<td class='tabla_fila2' style='text-align:right;'>
Cuenta Corriente:
</td><td class='tabla_fila' style='font-weight:bold;font-size:16px;'>
<?php if($fap) echo ($fap['cta_cte']); else echo ''; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
<td class='tabla_fila' colspan=3 style='font-size:12px;font-weight:bold;' id='pac_nombre'>
<?php if($fap) echo trim($fap['pac_appat'].' '.$fap['pac_apmat'].' '.$fap['pac_nombres']); else echo ''; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
<td class='tabla_fila' id='pac_fc_nac'>
<?php if($fap) echo trim($fap['pac_fc_nac']); else echo ''; ?>
</td>
<td class='tabla_fila2' colspan=2 style='text-align:center;' id='pac_edad'>
Edad:<b><?php if($fap) echo $edad; else echo '(n/a)'; ?></b>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Previsi&oacute;n:</td>
<td class='tabla_fila' id='prev_desc'>
<?php if($fap) echo trim($fap['prev_desc']); else echo ''; ?>
</td>
<td class='tabla_fila2'  style='text-align:right;'>N&uacute;m. Pabell&oacute;n:</td>
<td class='tabla_fila'>
<?php echo $fap['fapp_desc']; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha/Hora:</td>
<td class='tabla_fila' id='fecha_hora' style='text-align:center;font-weight:bold;'>
<?php if($fap) echo $fap['fap_fecha']; else echo date('d/m/Y'); ?> 
<?php if($fap) echo $fap['fap_hora']; else echo date('H:i'); ?>
</td>
<td class='tabla_fila2'  style='text-align:right;'>Servicio de Or&iacute;gen:</td>
<td class='tabla_fila'>
<input type='hidden' id='centro_ruta' name='centro_ruta' value='<?php if($fap) echo trim($fap['centro_ruta']); else echo ''; ?>' />
<?php echo trim($fap['tcama_tipo']); ?>
</td>
</tr>


<tr>
<td class='tabla_fila2'  style='text-align:right;'>Diagn&oacute;stico Pre.:</td>
<td class='tabla_fila' colspan=3>
<?php if($fap) echo $fap['fap_diag_cod'].' '.$fap['fap_diagnostico']; ?></td>
</tr>

<?php if($pr) {



print("<tr><td colspan=4><table style='width:100%;'><tr class='tabla_header'><td>#</td><td>C&oacute;digo</td><td>Descripci&oacute;n</td></tr>");

for($i=0;$i<sizeof($pr);$i++) {

print("<tr><td style='text-align:right;font-size:20px;'>".($i+1)."</td><td style='text-align:center;font-weight:bold;'>".$pr[$i]['fappr_codigo']."</td>");
print("<td>".$pr[$i]['glosa']."</td></tr>");


}

print("</table></td></tr>");


}


?>
<tr><td colspan=4>



<table style='width:100%;height:300px;' cellpadding=0 cellspacing=0>
<td id='grafico' style='background-color:white;text-align:center;width:60%;'></td><td style='background-color:white;font-size:11px;' valign='top'>
<b><u>Indicaciones</u></b><br/>
<pre><?php echo $fap['fap_indicaciones']; ?></pre>
<b><u>Indicaciones Anestesia</u></b><br/>
<pre><?php echo $fap['fap_indicaciones_anestesia']; ?></pre>

<div id='registro_enfermeria' style='height:120px;overflow:auto;'>

</div>

</td></tr><tr><td colspan=2 style='width:50%;background-color:white;' id='series' valign='top'>


</td></tr></table>



</td></tr>

</table>
</div>
</form>


</body>
</html>

<script>

	cargar_series(); cargar_grafico(); cargar_registro();

</script>
