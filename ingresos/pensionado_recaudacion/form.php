<?php

  require_once("../../conectar_db.php");
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
		$ac=cargar_registro("SELECT *, (ac_fecha_apertura::date<CURRENT_DATE) AS abierta FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");
		
	
	?>
	
	<script>
$('func_id').value='<?php echo($func_id); ?>';

	bloquear_boton=true;
	bloquear_ingreso=false;
	
	prestaciones=[];
	procedimientos=[];
 	tipo_proc=[];
  	datos='';
	tipo=0;
		
		verifica_tabla = function() {
			
			
			
	
			
				campos_data=datos.split(',');
			for(var i=0;i<campos_data.length-1;i++) {
			
			if($(campos_data[i]).value=='' ) {
				alert('Falta asociar un médico a la prestación.'.unescapeHTML());
				
				return;
			}	
			
			}
			
			
			
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
				var myAjax = new Ajax.Request(
				'ingresos/pensionado_recaudacion/sql.php', 
				{
					
					
					method: 'post',  
 					parameters: $('bolnum').serialize()+'&'+$('func_id').serialize()+'&'+$('todo').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON())+'&tipo='+tipo,

 					onComplete: function(resp) {
					
						//	alert('Edici&oacute;n de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
						

						
						
						bloquear_ingreso=false;
						bloquear_boton=false;
						
						d=resp.responseText.evalJSON(true);
						alert('Ingreso correcto.');
						//imprimir_boletin(d[1]);
						cambiar_pagina('ingresos/pensionado_recaudacion/form.php');
						
					}
				}		
				);
				
					//alert('Ingreso de C&oacute;digo de Recaudacion fallido, ya existe un codigo con esta modalidad.'.unescapeHTML());
			
			
			
		
		}
	
	imprimir_boletin=function(dev_id) {
		var valor=false;
	window.open('ingresos/pensionado_recaudacion/imprimir_pen_boletin.php?dev_id='+dev_id+'&nombre='+valor,'_blank');
}

	limpiar=function() {
		$('bolnum').value ='';
$('pac_id').value ='';	
$('clirut').value ='';	
//$('cliid').value ='';
$('ac_id').value ='';	
$('func_id').value ='';
		$('nboletin').value ='';
		$('mon_pen_total').value ='';
		$('bolmon').innerHTML ='';
			$('bolmon_or').innerHTML ='';
		
	}
	
	

validar_bolnum = function(){
	

	var myAjax=new Ajax.Request('ingresos/pensionado_recaudacion/pensionado_boletin.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('nboletin').value),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
				
				if(d[0]['anulacion']==''){
					$('pac_id').value=d[0]['pac_id']*1;
					$('clirut').value=d[0]['pac_rut'];
					//$('cliid').value=d[0]['id_sidra']*1;
					$('bolnum').value=$('nboletin').value;
					
					

					prestaciones=d[1];//prestaciones del documento
					tipo_proc=d[2];
					tipo=d[3];
					if(prestaciones==false)
					{
						alert('El Boletin ingresado no tiene prestaciones.'.unescapeHTML());
					cambiar_pagina('ingresos/pensionado_recaudacion/form.php');
					}
						
					redibujar_tabla();
					dato_paciente($('pac_id').value);
					
				}else{
				
					
					alert('El Boletin ingresado no existe o está anulado.');
					cambiar_pagina('ingresos/pensionado_recaudacion/form.php');
					
				}
				
									
			} else {
			
				$('pac_id').value='';
					$('clirut').value='';
				//	$('cliid').value='';
				$('bolnum').value='';
				$('nboletin').value='';

				$('clipat').innerHTML='';
				$('climat').innerHTML='';
			
			
							
			}

			} catch(err) {
				alert(err);
			} 


		}	
	});
	
}

	
dato_paciente = function(){
	
	var myAjax=new Ajax.Request('ingresos/datos_paciente_id.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('pac_id').value),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			
			if(d) {
				
				
								
				$('clipat').innerHTML=d['pac_appat'];
				$('climat').innerHTML=d['pac_apmat'];
				$('clinom').innerHTML=d['pac_nombres'];
								
													
			} else {
			
				
				$('pac_id').value='';
				//$('cliid').value='';
				$('clirut').value='';
				
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				

				
				alert('Paciente no encontrado.');
							
			}

		
			
			} catch(err) {
				
				alert(err);
			} 


		}	
	});

}
validar_rut= function(x,y,id){
		
			var myAjax=new Ajax.Request(
			'ingresos/pensionado_recaudacion/validar_doc.php',
			{
				method: 'post',
				parameters: 'doctor='+$('nombre_medico_'+x+'_'+y).value,
				onComplete: function(r) {
					var a = r.responseText.evalJSON(true);
					if(!a){
						$('rut_medico_'+x+'_'+y).style.background='red'; 
						
					}else{
						$('rut_medico_'+x+'_'+y).value=a[1];
						$('rut_medico_'+x+'_'+y).style.background='';
						prestaciones[x]["r"+id+"t"]=a[2];
						console.log(prestaciones[x]);
					}
					//guardar_vals(x);
				}
			}
			);
		
	}

redibujar_tabla=function() {

				
		datos='';
			var html='<table style="width:100%;font-size:10px;"><tr class="tabla_header">';
			html+='<td style="width:10%;">Fecha</td>';
			html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
			html+='<td style="width:20%;">Descripci&oacute;n</td>';
			html+='<td style="width:60%;">Seleccionar M&eacute;dico.</td>';
			html+='</tr>';
			
			html+=' <input type="text" id="campos" name="campos" >';
			
			
			var ver_prop=false;			
	
			for(var i=0;i<prestaciones.length;i++) {
				
				p=prestaciones[i];
				if(prestaciones[i].mon_pen == null){
					
				prestaciones[i].mon_pen=""+p.bdet_valor*p.bdet_cantidad+"";
				prestaciones[i]=p;
				temp_conv=prestaciones[i].convenios
				procedimientos[i]=temp_conv.split(',');
				}
				
				
				
				
				clase=(i%2==0)?'tabla_fila':'tabla_fila2';
				
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+p.bdet_fecha.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">'+p.bdet_codigo+'</td>';
				html+='<td style="font-size:10px;">'+p.bdet_prod_nombre+'</td>';
				if(procedimientos[i][0]!=undefined)
				{
					html+='<td><table style="width:100%;font-size:10px;">';
					for(var j=0;j<procedimientos[i].length;j++)
					{
				
						html+='<tr>';
						if(procedimientos[i][j]>0)
						{
						
						html+='<td style="font-size:10px;">'+tipo_proc[procedimientos[i][j]*1-1].tipo_glosa;
						html+='<input type="text" id="rut_medico_'+i+'_'+j+'" name="rut_medico_'+i+'_'+j+'" size=10 style="text-align: center;" disabled>';
		                html+=' <input type="text" id="nombre_medico_'+i+'_'+j+'" name="nombre_medico_'+i+'_'+j+'" ';
		                html+='onBlur="if(this.value!=\'\'){ validar_rut('+i+','+j+','+procedimientos[i][j]+'); }" ></td >';
		                datos+='rut_medico_'+i+'_'+j+',';
		               
		               }
		               html+='</tr>';
		             }
		             html+='</td></table>';
				}
               
				html+='<input type="hidden" id="bdet_'+i+'_'+j+'" name="bdet'+i+'" ';
				html+='value="'+p.bdet_id+'">';
				
				//html+='<td style="text-align:center;"><img src="iconos/delete.png" ';
				//html+=' style="cursor:pointer;" onClick="quitar_prod('+i+');"/></td>';
				
				html+='<td style="text-align:center;white-space:nowrap;">';

				html+='</td></tr>';				
				
				
				/*if(p.cobro=='S') 
					suma+=p.valor;*/

			}			

			
			
			html+='</table>';		
		
			$('lista_productos').innerHTML=html;
			
			$('campos').value=datos;
			
			for(var x=prestaciones.length-1;x>=0;x--){
				if(procedimientos[x][0]!=undefined)
				{
					console.log(procedimientos[x].length);
				for(var y=procedimientos[x].length-1;y>=0;y--){
				
					if(procedimientos[x][y]>0)
					{
					selecc= "seleccionar_map_"+x+"_"+y;
					window[ selecc ] = function(d) { console.log('lalala'); $('rut_medico_'+x+'_'+y).value=d[0]; };
					autocom = "autocompletar_doc_"+x+"_"+y;
					var tmp;
					eval("tmp=function() { try{ if($('nombre_medico_"+x+"_"+y+"').value.length<2) return false; return { method: 'get', parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('nombre_medico_"+x+"_"+y+"').value) } }catch(e){ console.log(e.name + ': ' + e.message); }};");
					window[ autocom ] = new AutoComplete('nombre_medico_'+x+'_'+y, 'autocompletar_sql.php', tmp, 'autocomplete', 150, 200, 250, 0, 0, selecc); 			
					}
				}
				}
			}
						
		
		}
		
		cambiar_icono=function(ic){
			
			 $('icon'+ic).src='iconos/cross.png'; 

		}
		quitar_prod=function(n) {
			prestaciones=prestaciones.without(prestaciones[n]);
			redibujar_tabla();
		}
		guardar_valor=function(n) {
			prestaciones[n].mon_pen=$('mon_pen'+n).value;
			redibujar_tabla();
		}
		
	</script>
	
	<center>
		
	<table >
	
	
		
	
		<td>
	
				
  				
  				
  		
		
	
	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Datos de la(s) Prestaciones.</span></b>
	
			<div class='sub-content2' >
			<left>
				<table>
					<input type='hidden' name='bolnum' id='bolnum'>
					<input type='hidden' name='pac_id' id='pac_id'>
					<input type='hidden' name='clirut' id='clirut'>
					<!--input type='hidden' name='cliid' id='cliid'-->
					<input type='hidden' name='ac_id' id='ac_id'>
					<input type='hidden' name='func_id' id='func_id'>
					
				
					<td style='font-size:13px;'>
						<span id='clipat' name='clipat' style='font-weight:bold;'></span>
						<span id='climat' name='climat' style='font-weight:bold;'></span>
						<span id='clinom' name='clinom' style='font-weight:bold;'></span>
						</span>
					</td>
					<tr>
						<td style='text-align: right;' class='form_titulo'>Numero de Boletin:</td><td colspan=3 class='form_campo'>
			 				 <input type='text' id='nboletin' name='nboletin' style='text-align:center;' onKeyUp=' if(event.which==13 ){ validar_bolnum();}' value='' size=10 />
							
			 			</td>
			 		</tr>
 					
 			</table>
 			</left>
 		</div>
 	</div>
 	
 	
 	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Prestaciones.</span></b>
		
		<div class='sub-content2' >
	 		<table>
	 			
			<form id='contenido' name='contenido'>
			<div class='sub-content2' style='height:200px;overflow:auto;' id='lista_productos'>
		
			</div>
			</form>
			
			
			<br>
			<div class='boton' id='guardar_boton' style='display: none;'>
			<table>
				<tr>
					<td>
					<img src='iconos/accept.png'>
					</td>
					
					<td>
					<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Asociar M&eacute;dicos...</span></a>
					</td>
				</tr>
			</table>
			</div>
	
			</table>
		</div>
	
	</div>
	
	
	
  </td>
 
  
  <br>
	


</table>

</center>	
  
