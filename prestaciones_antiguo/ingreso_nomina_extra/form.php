<?php 

	require_once('../../conectar_db.php');


?>

<script>

	dnomina='';

	abrir_nomina=function(nom_id) {

		var params='nom_folio='+nom_id;
		
      var myAjax = new Ajax.Updater(      'listado_nominas',      'prestaciones/ingreso_nomina_extra/abrir_nomina.php',      {
      	        method:'post',        parameters:params,
        evalScripts:true,
        onComplete: function(resp) {

				if(resp.responseText=='') {
					alert('N&oacute;mina no encontrada.'.unescapeHTML());
					return;	
				}

				$('datos_nomina').style.display='';
       	
				$('listado_nominas').scrollTop=0;
				
				$('paciente').disabled=false;
				
				$('paciente').select();
				$('paciente').focus();
					
        }
              });		
	}
	
	calcular_totales=function() {
	
		var num_nuevo=0;
		var num_control=0;	
		var num_extra=0;
	
		for(var i=0;i<dnomina.length;i++) {
		
			r=dnomina[i];		
		
			var val=$('nomd_tipo_'+r.nomd_id).value;
			
			if(val=='N')
				num_nuevo++;
			else
				num_control++;
				
			var val=$('nomd_extra_'+r.nomd_id).value;
			
			if(val=='S')
				num_extra++;
				
		}
		
		if(dnomina.length>0)
			var factor=100/dnomina.length;
		else
			var factor=0;
		
		var html='<table style="width:100%;"><tr class="tabla_header"><td colspan=3>Indicadores de la N&oacute;mina</td></tr>';
		html+='<tr class="tabla_fila"><td style="text-align:right;">Pac. Nuevos:</td><td style="font-weight:bold;text-align:center;">'+num_nuevo+'</td><td style="text-align:center;">'+number_format(num_nuevo*factor,2,',','.')+' %</td></tr>';	
		html+='<tr class="tabla_fila2"><td style="text-align:right;">Pac. Control:</td><td style="font-weight:bold;text-align:center;">'+num_control+'</td><td style="text-align:center;">'+number_format(num_control*factor,2,',','.')+' %</td></tr>';	
		html+='<tr class="tabla_fila"><td style="text-align:right;">Cant. Extras:</td><td style="font-weight:bold;text-align:center;">'+num_extra+'</td><td style="text-align:center;">'+number_format(num_extra*factor,2,',','.')+' %</td></tr>';	
		
		html+='</table>';
		
		$('indicadores').innerHTML=html;		
		
	}
	
	buscar_paciente=function() {
	
		var myAjax=new Ajax.Request(
			'registro.php',
			{
				method:'get',
				parameters:'tipo=paciente&paciente_tipo_id=3&paciente_rut='+encodeURIComponent($('paciente').value*1),
				onComplete:function(resp) {

					if(resp.responseText=='') {
						alert('Paciente no encontrado.');
						return;	
					}

					var d=resp.responseText.evalJSON(true);
					
					for(var i=0;i<dnomina.length;i++) {
						
						if(d[0]*1==dnomina[i].pac_id*1) {
							alert('Paciente ya est&aacute; registrado en la n&oacute;mina actual.'.unescapeHTML());
							$('paciente').select();
							$('paciente').focus();
							return;	
						}		

					}

					var myAjax=new Ajax.Request(
						'prestaciones/ingreso_nomina_extra/sql.php',
						{
							method:'post',
							parameters:$('nom_id').serialize()+'&pac_id='+d[0],
							onComplete:function(resp2) {
								
								abrir_nomina($("folio_nomina").value*1);								
								
							}	
						}					
					);					
						
				}						
			}		
		);	
		
	}
	
	eliminar=function(nomd_id) {

		var myAjax=new Ajax.Request(
			'prestaciones/ingreso_nomina_extra/sql_eliminar.php',
			{
				method:'post',
				parameters:'nomd_id='+nomd_id,
				onComplete:function() {
					abrir_nomina($("folio_nomina").value*1);														
				}	
			}		
		);
		
	}


</script>

<center>
<div class='sub-content' style='width:750px;'>

<div class='sub-content'>
<img src='iconos/user_add.png'>
<b>Ingreso de Pacientes Extra</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='width:100px;text-align:right;'>Nro. N&oacute;mina:</td>
<td>
<input type='text' 
id='folio_nomina' name='folio_nomina' size=10 style='text-align:center;'
onKeyUp='if(event.which==13) abrir_nomina($("folio_nomina").value*1);'>
</td>
</tr>
</table>
</div>

<div class='sub-content' id='datos_nomina' style='display:none;'>
<table style='width:100%;'>



<tr>

<td style='width:100px;text-align:right;'>Fecha:</td>
<td id='fecha_nomina' style='font-size:14px;font-weight:bold;'></td>

<td rowspan=4 id='indicadores' style='width:40%;'>

</td>

</tr>

<tr>
<td style='width:100px;text-align:right;'>M&eacute;dico:</td>
<td id='medico_nomina'></td>
</tr>

<tr>
<td style='width:100px;text-align:right;'>Especialidad:</td>
<td id='esp_nomina'></td>
</tr>

</table>
</div>

<div class='sub-content2' style='height:220px;overflow:auto;'id='listado_nominas'>
</div>
<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='width:20px;'><img src='iconos/add.png' /></td>
<td style='width:100px;text-align:right;'>Agregar Paciente:</td>
<td>
<input type='text' id='paciente' name='paciente' 
style='text-align:center;' size=10
onKeyUp='if(event.which==13) buscar_paciente();' DISABLED />
</td></tr>
</table>
</div>

</div>
</center>