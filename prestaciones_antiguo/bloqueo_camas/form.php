<?php 

	require_once('../../conectar_db.php');

	  $ccamas = cargar_registros_obj( 
	  "SELECT * 
		FROM clasifica_camas  
		WHERE tcama_id IN ("._cav(254).")
	   ORDER BY tcama_num_ini", true);
	   
	   $ccamas2 = cargar_registros_obj( 
	  "SELECT *
		FROM tipo_camas  
	   ORDER BY cama_num_ini", true);

?>

<script>

	ccamas=<?php echo json_encode($ccamas) ?>;
	tcamas=<?php echo json_encode($ccamas2) ?>;

	validacion_fecha2=function(obj) {
		
		var o=$(obj);
		
		if(o.value=='') {
			o.style.background='';
			return true;
		} else {
			return validacion_fecha(o);
		}
		
	}
	
	listar_bloqueos=function() {
		
		$('lista_bloqueos').innerHTML='<br><br><br><br><img src="imagenes/ajax-loader3.gif" />';
		
		var myAjax=new Ajax.Updater(
			'lista_bloqueos',
			'prestaciones/bloqueo_camas/lista_bloqueos.php',
			{
				method:'post'
			}
		);
		
	}
	
	guardar_bloqueo=function() {

		if($('tcama_id')==null || $('ccama_id')==null || $('cama_id')==null) {
			alert("Debe seleccionar la cama a bloquear.");
			return;
		}
		
		if($('tcama_id').value*1==-1 || $('ccama_id').value*1==-1 || $('cama_id').value*1==-1) {
			alert("Debe seleccionar la cama a bloquear.");
			return;
		}
		
		if($('intercambio').value*1==1) {
			alert('No se puede bloquear una cama ocupada.');
			return;
		}

		
		var myAjax=new Ajax.Request(
			'prestaciones/bloqueo_camas/sql.php',
			{
				method: 'post',
				parameters: $('form_bloqueo').serialize(),
				onComplete:function(r) {
					
					if(r.responseText!='')
						alert(r.responseText);
					else
						alert("Bloqueo de cama guardado exitosamente.");
						
					listar_bloqueos();
					
				}
			}
		);
		
	}

	eliminar_bloqueo=function(bloq_id) {

		var myAjax=new Ajax.Request(
			'prestaciones/bloqueo_camas/sql_eliminar.php',
			{
				method: 'post',
				parameters: 'bloq_id='+bloq_id,
				onComplete:function(r) {
					
					if(r.responseText!='')
						alert(r.responseText);
					else
						alert("Bloqueo de cama eliminado exitosamente.");
						
					listar_bloqueos();
					
				}
			}
		);
		
	}


   select_ccamas=function(){

		var val=$('tcama_id').value;
	   
	   if(val=='-1' || val=='-2') {
		
		$("ccama").innerHTML='';
		$("cama").innerHTML='';
		$("imagen").innerHTML='';
		
		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		return;
	   
	   }
   
		var id=$('tcama_id').value.split(';');
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;
		
		html="<select id='ccama_id' name='ccama_id' onchange='select_camas();'><option value='-1'>(Seleccionar...)</option>";

		for (i=0;i<tcamas.length;i++){
		
			if(tcamas[i].cama_num_ini*1>=tcama_num_ini && tcamas[i].cama_num_ini*1<=tcama_num_fin)
				html+="<option value='"+tcamas[i].cama_id+";"+tcamas[i].cama_num_ini+";"+tcamas[i].cama_num_fin+"'>"+tcamas[i].cama_tipo+"</option>";	
	
		}
	
		html+="</select>";
		
		$("ccama").innerHTML=html;
		$("cama").innerHTML='';
		$("imagen").innerHTML='';
		   
   }
   
   select_camas=function(){

		var val=$('ccama_id').value;
	   
		if(val=='-1') {
		
		$("cama").innerHTML='';
		$("imagen").innerHTML='';

		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		return;
	   
		}
   
		var id=$('tcama_id').value.split(';');
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;
   
		var id=$('ccama_id').value.split(';');
		
		var cama_id=id[0]*1;
		var cama_num_ini=id[1]*1;
		var cama_num_fin=id[2]*1;
		
		html="<select id='cama_id' name='cama_id' onchange='verificar_cama();'><option value='-1'>(Seleccionar...)</option>";

		for (i=cama_num_ini;i<=cama_num_fin;i++){
		
			html+="<option value='"+i+"'>"+((i-tcama_num_ini)+1)+"</option>";	
	
		}
	
		html+="</select>";
		
		$("cama").innerHTML=html;
		$("imagen").innerHTML='';

	}
	
	verificar_cama = function(){

		var val=$('cama_id').value;
	   
		if(val=='-1') {
		
		$("imagen").innerHTML='';

		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		return;
	   
		}
			
		var myajax=new Ajax.Request(
			'prestaciones/asignar_camas/verificar_cama.php',
			{ 
				method:'post',
				parameters:$('cama_id').serialize(),
				onComplete:function(r){

					var dd=r.responseText.evalJSON(true);
					
					if(!dd[0]) {
						
						var d=dd[1];
						
						$("imagen").innerHTML="<img src='iconos/lock.png' style='width:18px;height:18px;' />";
							
						$('pac_cama_tr').show();
						
						var observa='<br /><font color="blue">'+d.bloq_observaciones+'</font>';
						
						$('pac_en_cama').innerHTML='<b><u>'+d.bmot_desc+'</u></b> <i>['+d.func_nombre+']</i>'+observa;
						
						$('pac_mot').innerHTML='Bloqueada:';
						
					} else {
						
						var d=dd[1];

						$('pac_mot').innerHTML='Pac. en Cama:';
					
						if(!d) {
						
							$("imagen").innerHTML="<img src='iconos/tick.png' style='width:18px;height:18px;' />";						

							$('pac_cama_tr').hide();
							
							$('intercambio').value=0;
														
						}else{
							 
							$("imagen").innerHTML="<img src='iconos/cross.png' style='width:18px;height:18px;' />";
							
							$('pac_cama_tr').show();
							$('pac_en_cama').innerHTML='<b>'+d.pac_rut+'</b> '+d.pac_appat+' '+d.pac_apmat+' '+d.pac_nombres;
							
							$('intercambio').value=1;
											
						}
					
					}			


				}			
			}		
		); 
	
	}
		
	html="<select id='tcama_id' name='tcama_id' onchange='select_ccamas();'><option value='-1'>(Seleccionar...)</option>";

	for (i=0;i<ccamas.length;i++){
		html+="<option value='"+ccamas[i].tcama_id+";"+ccamas[i].tcama_num_ini+";"+ccamas[i].tcama_num_fin+"'>"+ccamas[i].tcama_tipo+"</option>";	
	
	}
	
	html+="</select>";
	
	$("tcama").innerHTML=html;




	
	validacion_fecha($('bloq_fecha_ini'));
	validacion_fecha2($('bloq_fecha_fin'));
	
	listar_bloqueos();


</script>

<center>
<div class='sub-content' style='width:950px;'>

<form id='form_bloqueo' name='form_bloqueo' onSubmit='return false;'>

<input type='hidden' id='intercambio' name='intercambio' value='0'>

<div class='sub-content'>
<img src='iconos/stop.png' />
<b>Administraci&oacute;n de Bloqueo de Camas</b>
</div>
<div class='sub-content'>
<table style='width:100%;'>

	<tr>
    <td style='text-align:right;'>Sector / Cama:</td>
	<td colspan=3>
	<span id='tcama' name='tcama'>
	</span>
	<span id='ccama' name='ccama'>
	</span>
	<span id='cama' name='cama'>
	</span>
	<span id='imagen' name='imagen'>
	</span>
	</td>
	</tr>

	<tr id='pac_cama_tr' style='display:none;'>
    <td style='text-align:right;' id='pac_mot'>Pac. en Cama:</td>
	<td id='pac_en_cama' style='font-size:16px;color:#FF3333;'>

	</td>
	</tr>

	<tr>
		<td style='text-align:right;'>Fecha Inicio:</td>
		<td>
		<input type='text' id='bloq_fecha_ini' name='bloq_fecha_ini' style='text-align:center;'
		value='<?php echo date('d/m/Y'); ?>' onBlur='validacion_fecha(this);' />
		</td>
		<td style='text-align:right;'>Fecha T&eacute;rmino:</td>
		<td>
		<input type='text' id='bloq_fecha_fin' name='bloq_fecha_fin' style='text-align:center;' 
		value='' onBlur='validacion_fecha2(this);' />
		</td>
	</tr>

	<tr>
		<td style='text-align:right;'>Motivo Bloqueo:</td>
		<td colspan=3>
		<select id='bloq_motivo' name='bloq_motivo'>
		<?php 
			
			$m=cargar_registros_obj("SELECT * FROM bloqueo_camas_motivos");
			
			for($i=0;$i<sizeof($m);$i++) {
				
				print("<option value='".$m[$i]['bmot_id']."|".$m[$i]['bmot_detalles']."'>".htmlentities($m[$i]['bmot_desc'])."</option>");
				
			}
			
		?>
		</select>
		</td>
	</tr>

	<tr>
		<td style='text-align:right;'>Observaciones:</td>
		<td colspan=3>
		<input type='text' size=45 id='bloq_observaciones' name='bloq_observaciones' value='' />
		</td>
	</tr>
	
	<tr>
		<td style='text-align:center;' colspan=4>
		<input type='button' value='-- Guardar Bloqueo de Cama... --' onClick='guardar_bloqueo();' /> 
		</td>
	</tr>
	
</table>
</div>

</form>

<div class='sub-content2' id='lista_bloqueos' style='height:300px;overflow:auto;'>

</div>

</div>

</center>
