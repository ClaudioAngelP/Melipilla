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
	
			
			
			
			
			
			
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
				var myAjax = new Ajax.Request(
				'ingresos/devolucion_recaudacion/ejecutar_sql.php', 
				{
					
					
					method: 'post',  
 					parameters: $('func_id').serialize()+'&'+$('ac_id').serialize()+'&'+$('dev_id').serialize(),

 					onComplete: function(resp) {
					
						//	alert('Edici&oacute;n de C&oacute;digo de Recaudacion realizado exitosamente.'.unescapeHTML());
						

						
						
						bloquear_ingreso=false;
						bloquear_boton=false;
						
						d=resp.responseText.evalJSON(true);
						imprimir_boletin(d[1]);
						cambiar_pagina('ingresos/devolucion_recaudacion/form_ejecuta.php');
						
					}
				}		
				);
				
					//alert('Ingreso de C&oacute;digo de Recaudacion fallido, ya existe un codigo con esta modalidad.'.unescapeHTML());
			
			
			
		
		}
	
	imprimir_boletin=function(dev_id) {
		var valor=true;
	window.open('ingresos/devolucion_recaudacion/imprimir_dev_boletin.php?dev_id='+dev_id +'&nombre='+valor,'_blank');
}

	limpiar=function() {
		$('dev_id').value ='';
		
		$('ac_id').value ='';	
		$('func_id').value ='';
		$('nDev_id').value ='';
		$('mon_dev_total').value ='';
		
		
	}
	
	

validar_id = function(){
	
	
	var myAjax=new Ajax.Request('ingresos/devolucion_recaudacion/ejecutar_boletin.php',
	{
		method:'post',
		parameters: $('nDev_id').serialize(),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
				
					
					$('mon_dev_total').value=d[1]['monto_total']*-1;
					

					prestaciones=d[1];//prestaciones del documento
					if(prestaciones==false)
					{
						alert('El Boletin ingresado no esta aprobado para devoluci&oacute;n.'.unescapeHTML());
						cambiar_pagina('ingresos/devolucion_recaudacion/form_ejecuta.php');
					}else{
						$('dev_id').value=prestaciones['devol_id'];
					}
						
					
				
				
									
			} else {
			
				$('pac_id').value='';
					$('clirut').value='';
					$('cliid').value='';
				$('dev_id').value='';
				$('nDev_id').value='';

				
			
			
							
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
                    	<b>Ejecutar Devoluci&oacute;n de Recaudaci&oacute;n: </b>
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
					<input type='hidden' name='dev_id' id='dev_id'>
					
					<input type='hidden' name='ac_id' id='ac_id'>
					<input type='hidden' name='func_id' id='func_id'>
					
				
					
					<tr>
						<td style='text-align: right;' class='form_titulo'>Numero de Devoluci&oacute;n:</td><td colspan=3 class='form_campo'>
			 				 <input type='text' id='nDev_id' name='nDev_id'
			 				 style='text-align:center;' onKeyUp=' if(event.which==13 ) validar_id();' value='' size=10 />
							
			 			</td>
			 		</tr>
 					
 			</table>
 			</left>
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
					<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Ejecutar Devoluci&oacute;n...</span></a>
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
  
