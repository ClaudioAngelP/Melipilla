<?php

  require_once("../../conectar_db.php");
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
		$ac=cargar_registro("SELECT *, (ac_fecha_apertura::date<CURRENT_DATE) AS abierta FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");
		
	
	?>
	
	<script>

<?php 

	if($ac) {
		if(substr($ac['ac_fecha_apertura'],0,10)!=date('d/m/Y')) {
			print("
			alert('ERROR GRAVE: \\n\\nDEBE REALIZAR CIERRE DE CAJA DE DIA ANTERIOR PENDIENTE. NO SE PUEDE RECAUDAR.'); 
			cambiar_pagina('ingresos/cierre_caja/form.php');
			</script>
			");
			
			exit();
		}else{
		?>
		$('ac_id').value='<?php echo($ac['ac_id']); ?>';
		$('func_id').value='<?php echo($func_id); ?>';
		<?php 
	}
	}

?>
	
	bloquear_boton=true;
	bloquear_ingreso=false;
	
	prestaciones=[];
 
  
	
		
		verifica_tabla = function() {
			
			
			if($('btn_apertura_caja')!=undefined) {
				alert("Debe realizar su APERTURA DE CAJA para poder recaudar.");
				return;
			}
	
			mon_monto = document.getElementById('mon_dev_total');
			
			
			
			if(trim(mon_monto.value)=='' || mon_monto.value<1 ) {
				alert('El campo Monto est&aacute; vac&iacute;o o es cero.'.unescapeHTML());
				mon_monto.select();
				return;
			}	
			
			if((($('bolmon').innerHTML*1)-mon_monto.value)<0)
			{
				alert('El campo Monto ingresado es mayor al recaudado.'.unescapeHTML());
				mon_monto.select();
				return;
			}
			
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
				var myAjax = new Ajax.Request(
				'ingresos/devolucion_recaudacion/sql.php', 
				{
					
					
					method: 'post',  
 					parameters: $('bolnum').serialize()+'&'+$('func_id').serialize()+'&'+$('mon_dev_total').serialize()+'&'+$('ac_id').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON()),

 					onComplete: function(resp) {
					
						//	alert('Edici&oacute;n de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
						

						
						
						bloquear_ingreso=false;
						bloquear_boton=false;
						
						d=resp.responseText.evalJSON(true);
						imprimir_boletin(d[1]);
						cambiar_pagina('ingresos/devolucion_recaudacion/form.php');
						
					}
				}		
				);
				
					//alert('Ingreso de C&oacute;digo de Recaudacion fallido, ya existe un codigo con esta modalidad.'.unescapeHTML());
			
			
			
		
		}
	
	imprimir_boletin=function(dev_id) {
		var valor=false;
	window.open('ingresos/devolucion_recaudacion/imprimir_dev_boletin.php?dev_id='+dev_id+'&nombre='+valor,'_blank');
}

	limpiar=function() {
		$('bolnum').value ='';
$('pac_id').value ='';	
$('clirut').value ='';	
$('cliid').value ='';
$('ac_id').value ='';	
$('func_id').value ='';
		$('nboletin').value ='';
		$('mon_dev_total').value ='';
		$('bolmon').innerHTML ='';
			$('bolmon_or').innerHTML ='';
		
	}
	
	

validar_bolnum = function(){
	
	
	var myAjax=new Ajax.Request('ingresos/devolucion_recaudacion/devolucion_boletin.php',
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
					$('cliid').value=d[0]['id_sidra']*1;
					$('bolnum').value=$('nboletin').value;
					
					$('bolmon_or').innerHTML=d[0]['bolmon'];
					$('bolmon').innerHTML=d[0]['bolmon']*1-d[1]['monto_total']*1;
					

					prestaciones=d[2];//prestaciones del documento
					console.log(prestaciones);
					if(prestaciones==false)
					{
						alert('El Boletin ingresado no tiene prestaciones disponibles para generar la devoluci&oacute;n.'.unescapeHTML());
					cambiar_pagina('ingresos/devolucion_recaudacion/form.php');
					}
						
					redibujar_tabla();
					dato_paciente($('cliid').value);
					
				}else{
				
					
					alert('El Boletin ingresado no puede ser Devuelto.');
					cambiar_pagina('ingresos/devolucion_recaudacion/form.php');
					
				}
				
									
			} else {
			
				$('pac_id').value='';
					$('clirut').value='';
					$('cliid').value='';
				$('bolnum').value='';
				$('nboletin').value='';

				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('bolmon').innerHTML='';
				$('bolmon_or').innerHTML='';
			
			
							
			}

			} catch(err) {
				alert(err);
			} 


		}	
	});
	
}


apertura_caja=function() {

	var myAjax=new Ajax.Request(
		'creditos/ingreso/sql_apertura.php',
		{
			method:'post',
			onComplete:function(r) {
				$('btn_apertura').innerHTML='';
				cambiar_pagina('ingresos/devolucion_recaudacion/form.php');
			}
		}
	);

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
				$('cliid').value='';
				$('clirut').value='';
				
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('clinom').innerHTML='';
				

				
				alert('Paciente no encontrado.');
							
			}

		
			
			} catch(err) {
				
				alert(err);
			} 


		}	
	});

}


redibujar_tabla=function() {

				
		
			var html='<table style="width:100%;font-size:10px;"><tr class="tabla_header">';
			html+='<td style="width:12%;">Fecha</td>';
			html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
			html+='<td>Descripci&oacute;n</td>';
			html+='<td style="width:3%;">Cant.</td>';
			html+='<td style="width:10%;">Valor</td>';
			html+='<td style="width:10%;">Copago</td>';
			html+='<td>Cobro</td>';
			html+='<td colspan=2 style="width:10%;" >Monto a Devolver</td>';
			html+='</tr>';
			
			var suma=0;
			
			var ver_prop=false;			
	//	console.log(p);
	$('mon_dev_total').value=0;
			for(var i=0;i<prestaciones.length;i++) {
				
				p=prestaciones[i];
				if(prestaciones[i].mon_dev == null){
					
				prestaciones[i].mon_dev=""+p.bdet_valor*p.bdet_cantidad+"";
				prestaciones[i]=p;
				}
				
				
				/*if(codigos[p.codigo]!=undefined) 
					ttipo=codigos[p.codigo].tipo; 
				else 
					ttipo=$('modalidad').value;*/
					
				//ttipo=p.modalidad;
				
				clase=(i%2==0)?'tabla_fila':'tabla_fila2';
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				//html+='<td style="text-align:center;font-size:10px;">'+ttipo+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+p.bdet_fecha.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">'+p.bdet_codigo+'</td>';
				html+='<td style="font-size:10px;">'+p.bdet_prod_nombre+'</td>';
				html+='<td style="text-align:right;">'+p.bdet_cantidad+'</td>';
				html+='<td style="text-align:right;">$'+number_format(p.bdet_valor_total*p.bdet_cantidad,0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(p.bdet_valor*p.bdet_cantidad,0,',','.')+'</td>';
				html+='<input type="hidden" id="bdet_'+i+'" name="bdet'+i+'" ';
				html+='value="'+p.bdet_id+'">';
				
				//html+='<td style="text-align:center;"><img src="iconos/delete.png" ';
				//html+=' style="cursor:pointer;" onClick="quitar_prod('+i+');"/></td>';
				
				html+='<td style="text-align:center;white-space:nowrap;">';
				
								
				
				html+='</select><img src="iconos/delete.png" style="cursor:pointer;width:12px;height:12px;" onClick="quitar_prod('+i+');" />'
				
				html+='<td><input type="text" name="mon_dev'+i+'" id="mon_dev'+i+'" value="'+(p.mon_dev)+'"  onKeyUp=" cambiar_icono('+i+');"></td>';
				html+='<td></select><img src="iconos/tick.png" style="cursor:pointer;width:12px;height:12px;" onClick="guardar_valor('+i+');" id="icon'+i+'" /></td>'

				html+='</td></tr>';				
				if(prestaciones[i].mon_dev == null){
				$('mon_dev_total').value=$('mon_dev_total').value*1+(p.bdet_valor*p.bdet_cantidad)*1;	
				
				}else{
					$('mon_dev_total').value=$('mon_dev_total').value*1+(prestaciones[i].mon_dev)*1;
					
				}
				
				/*if(p.cobro=='S') 
					suma+=p.valor;*/

			}			

			
			
			html+='</table>';		
		
			$('lista_productos').innerHTML=html;
						
		
		}
		
		cambiar_icono=function(ic){
			
			 $('icon'+ic).src='iconos/cross.png'; 

		}
		quitar_prod=function(n) {
			prestaciones=prestaciones.without(prestaciones[n]);
			redibujar_tabla();
		}
		guardar_valor=function(n) {
			prestaciones[n].mon_dev=$('mon_dev'+n).value;
			redibujar_tabla();
		}
		
	</script>
	
	<center>
	<table >
	
	<div class='sub-content' style='width: 720px;' >
	
	
		
	<tr>
		<td>
	
				<div class='sub-content' >
                	<div class='sub-content'>
                    	<img src='iconos/page_gear.png'>
                    	<b>Devoluci&oacute;n de Recaudaci&oacute;n: </b>
	                    <?php 
						if(!$ac) {
						?>
						<input type='button' id='btn_apertura_caja' name='btn_apertura_caja' style='font-size:16px;color:red;border:2px solid yellow;' value='APERTURA CAJA' onClick='apertura_caja();' />
						<?php } else { ?>
						<?php 
						if($ac['abierta']=='t') print('<span style="color:red;font-weight:bold;">');
						echo 'APERTURA CAJA: '.substr($ac['ac_fecha_apertura'],0,16); 
						if($ac['abierta']=='t') print('</span>');
						?>
						<?php } ?>

                	</div>	
  				</div>
  				
  				
  		
		
	
	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Datos de la Devoluci&oacute;n</span></b>
	
			<div class='sub-content2' >
			<left>
				<table>
					<input type='hidden' name='bolnum' id='bolnum'>
					<input type='hidden' name='pac_id' id='pac_id'>
					<input type='hidden' name='clirut' id='clirut'>
					<input type='hidden' name='cliid' id='cliid'>
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
			 				 <input type='text' id='nboletin' name='nboletin'
			 				 style='text-align:center;' onKeyUp=' if(event.which==13 ) validar_bolnum();' value='' size=10 />
							
			 			</td>
			 		</tr>
 					<tr>
			 			<td style='text-align: right;' class='form_titulo'>Monto disponible del Boletin:</td><td colspan=3 class='form_campo'>
			 	
							<span id='bolmon' name='bolmon' style='font-weight:bold;'></span>
							<span  style='font-weight:bold;'> de (</span>
							<span id='bolmon_or' name='bolmon_or' style='font-weight:bold;'></span>
							<span  style='font-weight:bold;'> )</span>
							<img src='iconos/money.png'>
			 			</td>
			 		</tr>
 			</table>
 			</left>
 		</div>
 	</div>
 	
 	
 	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Prestaciones de la  Devoluci&oacute;n</span></b>
		
		<div class='sub-content2' >
	 		<table>
	 		<td id='btn_apertura'>
	
		
			</td>
	
	
	
			<form id='contenido' name='contenido'>
			<div class='sub-content2' style='height:160px;overflow:auto;' id='lista_productos'>
		
			</div>
			</form>
			</tr>
			
			<br>
	
	
			</table>
		</div>
	
	</div>
	
	
	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Datos de la Devoluci&oacute;n</span></b>
	
		<div class='sub-content2' >
 			<table>
 			<td id='btn_apertura'>

		
			<td style='text-align: right;' class='form_titulo'>Monto a devolver:</td><td colspan=3 class='form_campo'>
			<input type='text' name='mon_dev_total' id='mon_dev_total' size=20 readonly></td>
		
	
	
	
			</td>
	
	
			<br>
		
			<div class='boton' id='guardar_boton' style='display: none;'>
			<table>
				<tr>
					<td>
					<img src='iconos/accept.png'>
					</td>
					
					<td>
					<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Ingresar Devoluci&oacute;n...</span></a>
					</td>
				</tr>
			</table>
			</div>
	
	
	
	
			</table>
		</div>
	
	</div>
	
  </td>
 
  
  <br>
	

</div >
</table>
</center>	
  
