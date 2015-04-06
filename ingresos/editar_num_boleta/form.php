<?php

  require_once("../../conectar_db.php");
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
		
	
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
	
			
			
			
			
			
			
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
				var myAjax = new Ajax.Request(
				'ingresos/editar_num_boleta/sql.php', 
				{
					
					
					method: 'post',  
 					parameters: $('func_id').serialize()+'&'+$('ac_id').serialize()+'&'+$('bolnum_id').serialize()+'&'+$('boleta_numero').serialize(),

 					onComplete: function(resp) {
					
						//	alert('Edici&oacute;n de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
						

						
						
						bloquear_ingreso=false;
						bloquear_boton=false;
						
						d=resp.responseText.evalJSON(true);
						imprimir_boletin(d[1]);
						cambiar_pagina('ingresos/editar_num_boleta/form.php');
						
					}
				}		
				);
				
					//alert('Ingreso de C&oacute;digo de Recaudacion fallido, ya existe un codigo con esta modalidad.'.unescapeHTML());
			
			
			
		
		}
	
	imprimir_boletin=function(bolnum_id) {
		var valor=true;
	window.open('ingresos/imprimir_boletin.php?bolnum='+bolnum_id,'_blank');
}

	limpiar=function() {
		$('bolnum_id').value ='';
		
		$('ac_id').value ='';	
		$('func_id').value ='';
		$('nboletin').value ='';
		$('boleta_numero').value ='';
		
		
	}
	
	

validar_id = function(){
	
	
	var myAjax=new Ajax.Request('ingresos/editar_num_boleta/cargar_boletin.php',
	{
		method:'post',
		parameters: $('nboletin').serialize(),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
				if(d[0]['anulacion']==''){
					if(d[1]['ac_fecha_cierre'] == undefined)
					{
						
						
						$('bolnum_id').value=d[0]['bolnum']*1;
						$('boleta_numero').value= d[0]['numboleta']*1;	
					}else{
						alert('Caja cerrada, no se puede editar el boletín.'.unescapeHTML());
						cambiar_pagina('ingresos/editar_num_boleta/form.php');					
						}
					
					

				}else{
						alert('El Boletin ingresado está anulado.'.unescapeHTML());
						cambiar_pagina('ingresos/editar_num_boleta/form.php');
					}
						
					
				
				
									
			} else {
			
				$('pac_id').value='';
					$('clirut').value='';
					$('cliid').value='';
				$('bolnum_id').value='';
				$('nboletin').value='';
				$('boleta_numero').value ='';

				
			
			
							
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
				cambiar_pagina('ingresos/editar_num_boleta/form.php');
			}
		}
	);

}
	
	
dato_paciente = function(){
	
	var myAjax=new Ajax.Request('ingresos/datos_paciente_id.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('cliid').value),
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


	</script>
	
	<center>
	<table >
	
	<div class='sub-content' style='width: 720px;' >
	
	
		
	<tr>
		<td>
	
				<div class='sub-content' >
                	<div class='sub-content'>
                    	<img src='iconos/page_gear.png'>
                    	<b>Guardar cambio de n&uacute;mero de boleta: </b>
	                   
                	</div>	
  				</div>
  				
  				
  		
		
	
	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Datos del Bolet&iacute;n</span></b>
	
			<div class='sub-content2' >
			<left>
				<table>
					<input type='hidden' name='bolnum_id' id='bolnum_id'>
					
					<input type='hidden' name='ac_id' id='ac_id'>
					<input type='hidden' name='func_id' id='func_id'>
					
				
					
					<tr>
						<td style='text-align: right;' class='form_titulo'>N&uacute;mero de Bolet&iacute;n:</td><td colspan=3 class='form_campo'>
			 				 <input type='text' id='nboletin' name='nboletin'
			 				 style='text-align:center;' onKeyUp=' if(event.which==13 ) validar_id();' value='' size=10 />
							
			 			</td>
			 		</tr>
 					
 			</table>
 			</left>
 		</div>
 	</div>
 	
 	
 	
	
	
	<div class='sub-content'>
		<img src='iconos/script.png' id='imagen_titulo'> 
		<b><span id='titulo_formulario'>Datos del Bolet&iacute;n</span></b>
	
		<div class='sub-content2' >
 			<table>
 			<td id='btn_apertura'>

		
			<td style='text-align: right;' class='form_titulo'>N&uacute;mero de boleta:</td><td colspan=3 class='form_campo'>
			<input type='text' name='boleta_numero' id='boleta_numero' size=20 ></td>
		
	
	
	
			</td>
	
	
			<br>
		
			<div class='boton' id='guardar_boton' style='display: none;'>
			<table>
				<tr>
					<td>
					<img src='iconos/accept.png'>
					</td>
					
					<td>
					<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Guardar Boleta...</span></a>
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
  
