<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_GET['fap_id']*1;
	$ub=$_GET['ub']*1;

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

		$s=cargar_registros_obj("SELECT * FROM fap_suspension ORDER BY faps_id;", true);
	
		$suspensionhtml='';
	
		for($i=0;$i<sizeof($s);$i++) {
			$t=$s[$i]['faps_nombre'];
			$sel=(htmlentities($fap['fap_suspension'])==htmlentities($t))?'SELECTED':'';
			$style=($s[$i]['faps_titulo']=='t')?'font-weight:bold;':'';
		
			$suspensionhtml.='<option value="'.$t.'" style="'.$style.'" '.$sel.'>'.$t.'</option>';
		}
?>

<html>
<title>Checklist Intervenci&oacute;n Quir&uacute;rgica</title>

<?php cabecera_popup('../..'); ?>

<script>

function cargar_checklist() {

	if($('selector').value=='-1') {
		var reg=prompt("Nombre de checklist a agregar:");

		if(reg!='' && reg!=null && reg!=undefined) {
		var myAjax=new Ajax.Updater(
                'td_selector',
                'cargar_checklist.php',
                { method:'post',parameters:'fap_id=<?php echo $fap_id; ?>&add_reg='+encodeURIComponent(reg),onComplete:function(d){cargar_checklist();} }
                );
		}

		return;
	}

	var myAjax=new Ajax.Updater(
		'checklist',
		'cargar_checklist.php',
		{ method:'post',evalScripts:true,parameters:'fap_id=<?php echo $fap_id; ?>&'+$('selector').serialize() }
		);	

}

<?php if(_cax(211)) { ?>

function editar_reg() {

                var myAjax=new Ajax.Updater(
                'checklist',
                'cargar_checklist.php',
                { method:'post',parameters:'fap_id=<?php echo $fap_id; ?>&'+$('selector').serialize()+'&'+$('edit_reg').serialize() }
                );
}

function borrar_reg() {

		var myAjax=new Ajax.Updater(
                'td_selector',
                'cargar_checklist.php',
                { method:'post',parameters:'fap_id=<?php echo $fap_id; ?>&'+$('selector').serialize()+'&remover=1',onComplete:function(){cargar_checklist();} }
                );

}

<?php } ?>

function imprimir_checklist(fcld_id) {


      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-400;

      new_win = 
      window.open('imprimir_checklist_fap.php?fcld_id='+fcld_id,
      'win_checklist', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();


}



 function guardar_checklist() {

				/**if($('fap_suspension').value=='')
				if(!comprobar_campos()){
					return;
				}*/
				
				//if($('fap_suspension').value!=''){
				guardar_suspension();
				if($('fap_suspension').value!=''){
					return;
				}
							
                var params=$('datos').serialize();

                var myAjax=new Ajax.Request(
                        'sql_checklist.php',
                        {
                                method:'post',
                                parameters: params,
                                onComplete:function() {
                                        alert('Checklist guardado exitosamente.');
					cargar_checklist();
					window.close();
                                }
                        }
                );

        }
        
  function guardar_suspension(){
	  
	if($('fap_suspension').value!='') {
		var tmp=$('fap_suspension').value.split(' ');
		if(tmp[0].length==2) {
			alert( 'Debe seleccionar motivo de suspensi&oacute;n v&aacute;lido.'.unescapeHTML() );
			return false;	
		}
	}
	  
	var params=$('fap_id').serialize()+'&'+$('fap_suspension').serialize();

                var myAjax=new Ajax.Request(
                        'sql_guarda_suspension.php',
                        {
                                method:'post',
                                parameters: params,
                                onComplete:function() {
                                   					
									//var fn=window.opener.listar_fap.bind(window.opener);
									//fn();
									var fn = window.opener.abrir_fap(<?php echo $fap_id.','.$ub; ?>,0);
									fn();
									window.close();
                                }
                        }
                );
  } 
        
  function comprobar_campos(){
  
	var estado=true;
	
	if($('selector').value==10){
		if(!document.datos.campo_1[0].checked && !document.datos.campo_1[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Reidentificaci&oacute;n del Paciente&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_2.value==''){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe Ingresar el Nombre del Procedimiento a Realizar".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_3.value==''){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe Ingresar, &#191;Cu&aacute;nto estima la duraci&oacute;n de la cirug&iacute;a&#63; ".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_4[0].checked && !document.datos.campo_4[1].checked && !document.datos.campo_4[2].checked && !document.datos.campo_4[3].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Se comprueba lateralidad&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_5[0].checked && !document.datos.campo_5[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, P&eacute;rdida herm&aacute;tica > 500? (7ml/kg ni&ntilde;os);".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_6[0].checked && !document.datos.campo_6[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Hay alg&uacute;n paso cr&iacute;tico a considerar&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_7[0].checked && !document.datos.campo_7[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Paciente tiene riesgo de enfermedad tromboemb&oacute;lica&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_8[0].checked && !document.datos.campo_8[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Paciente tiene prevenci&oacute;n de enfermedad tromboemb&oacute;lica&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_9[0].checked && !document.datos.campo_9[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Se indica antibioprofilaxis&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_10[0].checked && !document.datos.campo_10[1].checked && !document.datos.campo_10[2].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Hay im&aacute;genes disponibles&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_12[0].checked && !document.datos.campo_12[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Existen alergias conocidas&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_12[0].checked && document.datos.campo_13.value==''){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe especificar la alergia".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_14[0].checked && !document.datos.campo_14[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, Evaluaci&oacute;n preanest&eacute;sica realizada;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_15[0].checked && !document.datos.campo_15[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Dificultad de la v&iacute;a a&eacute;rea o condiciones de riesgo de aspiraci&oacute;n&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_16[0].checked && !document.datos.campo_16[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Otra Condici&oacute;n de riesgo a considerar&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_18[0].checked && !document.datos.campo_18[1].checked && !document.datos.campo_18[2].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Indicadores de esterilizaci&oacute;n virados&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_19[0].checked && !document.datos.campo_19[1].checked && !document.datos.campo_19[2].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Electrobistur&iacute; correctamente instalado&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_20[0].checked && !document.datos.campo_20[1].checked){
			alert("En Pausa de Seguridad - Ingreso a Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Correcto funcionamiento de otros equipos necesarios&#63;".unescapeHTML());
			estado=false;
			return false;
		}
	}else if($('selector').value==9){
	
		if(document.datos.campo_1.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\n&#191;Cu&aacute;l es su nombre&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_2.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\n&#191;Cu&aacute;l es su fecha de nacimiento&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_3[0].checked && !document.datos.campo_3[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, Tiene brazalete".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_4[0].checked && !document.datos.campo_4[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, &#191;Existen alergias conocidas&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_4[0].checked && document.datos.campo_5.value==''){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe especificar la alergia".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_6[0].checked && !document.datos.campo_6[1].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo, Consentimiento Informado".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_8[0].checked && !document.datos.campo_8[1].checked && !document.datos.campo_8[2].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar el campo de, la Persona que suministra la informaci&oacute;n".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_10[0].checked && !document.datos.campo_10[1].checked && !document.datos.campo_10[2].checked){
			alert("En Pausa de Seguridad - Recepci&oacute;n. \n\nDebe seleccionar si Asistente Verifica localizaci&oacute;n Quir&uacute;rgica".unescapeHTML());
			estado=false;
			return false;
		}
	
	}else if($('selector').value==12){
		
		if(document.datos.campo_1.value==''){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nNombre del Procedimiento Realizado".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_2[0].checked && !document.datos.campo_2[1].checked && !document.datos.campo_2[2].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Est&aacute; Correcto el recuento de Compresas, Gasas, Agujas e Instrumental&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_3[0].checked && !document.datos.campo_3[1].checked && !document.datos.campo_3[2].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Muestras Biol&oacute;gicas etiquetadas Correctamente&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_4[0].checked && !document.datos.campo_4[1].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191; Solicitud de Biopsia Realizada;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_5[0].checked && !document.datos.campo_5[1].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Existi&oacute; Alg&uacute;n Problema en Relaci&oacute;n con el Material o los Equipos&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_5[0].checked && document.datos.campo_6.value==''){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe especificar (1) el problema.".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_8[0].checked && !document.datos.campo_8[1].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Ocurri&oacute; Alg&uacute;n Evento Adverso Durante la Intervenci&oacute;n&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_8[0].checked && document.datos.campo_9.value==''){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe especificar (2) el evento.".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_11[0].checked && !document.datos.campo_11[1].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;Ocurri&oacute; Alg&uacute;n Evento Adverso Durante la Intervenci&oacute;n&#63;".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(document.datos.campo_11[0].checked && document.datos.campo_12.value==''){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe especificar (3) el evento.".unescapeHTML());
			estado=false;
			return false;
		}
		
		if(!document.datos.campo_14[0].checked && !document.datos.campo_14[1].checked && !document.datos.campo_14[2].checked){
			alert("En Pausa de Seguridad - Salida de Pabell&oacute;n. \n\nDebe seleccionar el campo, &#191;D&oacute;nde Ir&aacute; Paciente en Post-Operatorio&#63;".unescapeHTML());
			estado=false;
			return false;
		}		
	}
	return estado;
  
  }
  
  comprueba_pausa = function(fap_id, fcl_id){
		
		var myAjax = new Ajax.Request(
		'comprueba_pausas.php',
		{
		  method:'post',
		  parameters: 'fap_id='+fap_id+'&ptipo=1&fcl_id='+fcl_id,
		  onComplete: function(resp) {
			   try {
			
					resultado=resp.responseText;
				
					if(resultado=='1'){
						alert('Debe completar la Pausa de Seguridad'.unescapeHTML());
						return;
					}else{
						imprimir_checklist($('fcld_id').value);
					}
				}catch(err){
					alert("ERROR: " + resp.responseText);
				}
		  }
		  });
	}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/table_edit.png' />
<b>Checklist Intervenci&oacute;n Quir&uacute;rgica</b>
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

<tr><td style='text-align:right;' class='tabla_fila2'>
Suspensi&oacute;n de FAP:
</td><td class='tabla_fila' colspan=3>
<select id='fap_suspension' name='fap_suspension' style='width:300px;'>
<option value=''><i>(No ha sido suspendido...)</i></option>
<?php echo $suspensionhtml; ?>
</select>
</td></tr>

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
<table style='width:100%;'><tr><td id='td_selector'>
<select id='selector' name='selector' style='width:100%;font-size:20px;' onChange='cargar_checklist();'>
<?php

	$cl=cargar_registros_obj("SELECT *, (SELECT count(*) FROM fap_checklist_detalle AS fcld WHERE fap_id=$fap_id AND fcld.fcl_id=fap_checklist.fcl_id) AS cnt  FROM fap_checklist WHERE fcl_nombre ilike '%pausa de seg%'ORDER BY fcl_nombre;", true);	
	if($cl)
	for($i=0;$i<sizeof($cl);$i++) {
		if($cl[$i]['cnt']*1>0) $txt='<i>(Registrado)</i>'; else $txt='<i>(Pendiente)</i>';
		print("<option value='".$cl[$i]['fcl_id']."'>".$cl[$i]['fcl_nombre']." $txt</option>");
	}

	$cl=cargar_registros_obj("SELECT *, (SELECT count(*) FROM fap_checklist_detalle AS fcld WHERE fap_id=$fap_id AND fcld.fcl_id=fap_checklist.fcl_id) AS cnt  FROM fap_checklist WHERE fcl_nombre NOT ILIKE '%pausa de s%'ORDER BY fcl_nombre;", true);
	if($cl)
	for($i=0;$i<sizeof($cl);$i++) {
		if($cl[$i]['cnt']*1>0) $txt='<i>(Registrado)</i>'; else $txt='<i>(Pendiente)</i>';
		print("<option value='".$cl[$i]['fcl_id']."'>".$cl[$i]['fcl_nombre']." $txt</option>");
	}

?>

<?php if(_cax(211)) { ?><option value='-1'>(Agregar Nuevo Checklist...)</option><?php } ?>
</select></td><td style='width:64px;'>
<?php if(_cax(211)) { ?>
<img src='../../iconos/pencil.png' onClick='$("checklist_editor").toggle();' style='cursor:pointer;' />
<img src='../../iconos/delete.png' onClick='borrar_reg();' style='cursor:pointer;' />
<?php } ?>
</td></tr></table>
<div id='checklist'>

</div>

<center><input type='button' onClick='guardar_checklist();' value='Guardar Checklist...' /></center>

</td></tr>

</table>
</div>

</form>

</body>
</html>
<script> cargar_checklist(); </script>
