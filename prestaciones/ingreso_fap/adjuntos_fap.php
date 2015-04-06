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
			LEFT JOIN clasifica_camas ON centro_ruta=tcama_id::text
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
<title>Archivos Adjuntos</title>

<?php cabecera_popup('../..'); ?>

<script>

buffer_datos_multimedia='';

cargar_multimedia=function() {

	var myAjax=new Ajax.Request (

	    'listado_multimedia.php',
        {
                method:'post',
                parameters: '',
                evalScripts:true,
                onComplete:function(r) {
					try {
						
					if($('lista_multimedia')==null) return;
					
					if(r.responseText!=buffer_datos_multimedia) {
						buffer_datos_multimedia=r.responseText;
						$('lista_multimedia').innerHTML=r.responseText;
					}
					
					setTimeout('cargar_multimedia();', 3000);
					
					} catch(err) {
					
					}
				}
        }
        
        );

}

ver_archivo=function(archivo) {

	top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

	window.open('../../tmp/'+archivo, '_blank', 'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

}

eliminar_archivo=function(archivo) {

 var myAjax=new Ajax.Updater (

        'lista_multimedia',
        'listado_multimedia.php',
        {
                method:'post',
                parameters: 'eliminar='+encodeURIComponent(archivo)
        }
        
        );

	
}



</script>

<body class='fuente_por_defecto popup_background' onload='cargar_multimedia();'>

<div class='sub-content'>
<img src='../../iconos/photos.png' />
<b>Archivos Adjuntos</b>
</div>

<form id='datos' name='datos' 
action='sql_generar.php' method='post' onSubmit='return false;'>

<input type='hidden' id='fap_id' name='fap_id' value='<?php if($fap) echo $fap['fap_id']; else echo '-1'; ?>' />

<input type='hidden' id='prev_id' name='prev_id' value='<?php if($fap) echo $fap['prev_id']; else echo '-1'; ?>' />
<input type='hidden' id='ciud_id' name='ciud_id' value='<?php if($fap) echo $fap['ciud_id']; else echo '-1'; ?>' />
</form>


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
<div class='sub-content' style='height:230px;overflow:auto;text-align:center;'  id='lista_multimedia'>


</script>
</td></tr>


</table>
</div>

</body>
</html>
