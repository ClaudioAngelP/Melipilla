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
			)::text,'(No encontrado...)') AS cta_cte,
                        fap_fc_ingreso_recu::date as fap_fc_ingreso_recu
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
		
	
?>

<html>
<title>Informaci&oacute;n Recuperaci&oacute;n</title>

<?php cabecera_popup('../..'); ?>
<script>
validar_hora=function(obj) {
	 
		var val=trim(obj.value);
		var hr=val;
	
		if(val=='') {
			
			obj.style.background='';
			return true;
				
		}	
		
			if(!val.match(/^[0-9]{0,2}:*[0-9]{2}:*[0-9]{0,2}$/)) {
					obj.style.background='red';
					return false;														
			}		
		
			if(val.search(/\:/)==-1) {

				if(val.length==3){
					hr=0+val.charAt(0)+':'+val.charAt(1)+val.charAt(2);
				}else if(val.length==4) {
					hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3);
				} else if(val.length==6) { 
					hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3)+':'+val.charAt(4)+val.charAt(5);
				} else {
					obj.style.background='red';
					return false;										
				}	

			} 
			
		var chk = hr.split(':');
		
		if( (chk.length==2 || chk.length==3) 
				&& chk[0]*1>=0 && chk[0]*1<24 ) {
					
			for(var i=0;i<chk.length;i++) {
				if(chk[i].length!=2 || chk[i]*1<0 || (i>0 && chk[i]*1>=60)) {
					obj.style.background='red';
					return false;	
				}							
			}
					
			obj.style.background='';
			obj.value=hr;
			
			return true;
				
		} else {
			
			obj.style.background='red';
			return false;
							
		}							 
	 	
	 }
	 
	 guardar = function(){
	 
		if($('fecha1').value==''){
			alert('Debe registrar la fecha de Ingreso');
			return;
		}
		
		if($('fap_pab_hora9').value==''){
			alert('Debe registrar la hora de Ingreso');
			return;
		}
		
		params=$('datos').serialize();
		
		var myAjax=new Ajax.Request(
                 'sql_registra_recuperacion.php',
			{
				method:'post',
				parameters: params,
                onComplete:function() {
					alert("Se ha guardado la informaci&oacute;n".unescapeHTML());
					
				}
           });

	 
	 }
</script>

<body class='fuente_por_defecto popup_background'>
	
	<div class='sub-content'><img src='../../iconos/layout_edit.png' />
		<b>Informaci&oacute;n de Ingreso a Recuperaci&oacute;n</b>
	</div>
	
	<form id='datos' name='datos'>
		
		<input type='hidden' name='fap_id' id='fap_id' value='<?php echo $fap_id; ?>'>
		<div class='sub-content'>

			<table style='width:100%;'>
				<tr>
					<td class='tabla_fila2'  style='text-align:right;width:25%;'>Ficha Cl&iacute;nica:</td>
					<td class='tabla_fila' style='font-weight:bold;font-size:16px;' id='pac_ficha'>
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
					<td class='tabla_fila' id='prev_desc' colspan=3>
					<?php if($fap) echo trim($fap['prev_desc']); else echo ''; ?>
					</td>
				</tr>				
			</table>
		</div>
		
		<div class='sub-content' style='text-align:center;'>
			
			<td class="tabla_header"><b>Datos de Recuperaci&oacute;n</b></td>

		</div>
		
		<div class='sub-content' style='text-align:center;'>
			
			<table style='width:100%;'>
				
				<tr>
					<td class='tabla_fila2' style='text-align:right;'>Fecha Ingreso Recuperaci&oacute;n:</td>
					<td class='tabla_fila' style='text-align:left;'><input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo $fap['fap_fc_ingreso_recu']; ?>'>
											<img src='../../iconos/date_magnify.png' id='fecha1_boton'></td>
					<td class='tabla_fila2' style='text-align:right;'>Hora:</td>
					<td class='tabla_fila' style='text-align:left;'><input type='text' id='fap_pab_hora9' name='fap_pab_hora9' value='<?php echo $fap['fap_pab_hora9']; ?>' size=10  style='text-align:center;' onBlur='validar_hora(this);' /></td>
				</tr>
					
				<tr>
					<td class='tabla_fila2' style='text-align:right;'>Fecha Egreso Recuperaci&oacute;n:</td>
					<td class='tabla_fila' style='text-align:left;'><input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo $fap['fap_fc_egreso_recu']; ?>'>
											<img src='../../iconos/date_magnify.png' id='fecha2_boton'></td>
					<td class='tabla_fila2' style='text-align:right;'>Hora</td>
					<td class='tabla_fila' style='text-align:left;'><input type='text' id='fap_pab_hora10' name='fap_pab_hora10' value='<?php echo $fap['fap_pab_hora10']; ?>' size=10  style='text-align:center;' onBlur='validar_hora(this);' /></td>
				</tr>
				
				<tr>
					<td class='tabla_fila2' style='text-align:right;'>Observaci&oacute;n:</td>
					<td class='tabla_fila' style='text-align:left;' colspan=3><textarea id="rec_observacion" style="width:100%;height:100px;font-size:12px;" name="rec_observacion"><?php echo $fap['fap_observacion_recu']; ?></textarea></td>
				</tr>
				
				<tr>
					<td class='tabla_fila2' style='text-align:center;' colspan=4><input type='button' name='btn_guardar' value='Guardar' onclick='guardar();'></td>
				</tr>
			
			</table>

		</div>
				
	</form>
</body>
</html>
<script>
 Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'

    });
    
 Calendar.setup({
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'

    });
</script>
