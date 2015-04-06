<?php 

	require_once('../../conectar_db.php');

	function iniciales($str) {
	$words = explode(" ", $str);
	$acronym = "";

	foreach ($words as $w) {
	  $acronym .= $w[0];
	}
	
	return $acronym;
	}
	
	$ts=cargar_registros_obj("SELECT * FROM tipos_seguro ORDER BY ts_id;");

	$s=array();

	for($i=0;$i<sizeof($ts);$i++) {
		$s[$ts[$i]['ts_id']*1]=Array(htmlentities($ts[$i]['ts_nombre']),  htmlentities(iniciales($ts[$i]['ts_nombre'])));
	}
	
	$codigos=cargar_registros_obj("
		SELECT cp.codigo, cp.glosa, precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, pago_fijo
		FROM codigos_prestacion_recaudacion AS cp
		ORDER BY codigo;
	", true);
	
	$cods=Array();
	
	for($i=0;$i<sizeof($codigos);$i++) {
		//$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
		$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
	}
	
	$codigos2=cargar_registros_obj("
		SELECT dp_id, dp_valor FROM codigos_derecho_pabellon ORDER BY dp_id;
	", true);
	
	$derpabs=Array();
	
	for($i=0;$i<sizeof($codigos2);$i++) {
		$derpabs[($codigos2[$i]['dp_id']*1)-1]=$codigos2[$i]['dp_valor']*1;
	}
	

	$func_id=$_SESSION['sgh_usuario_id']*1;
	$ac=cargar_registro("SELECT *, (ac_fecha_apertura::date<CURRENT_DATE) AS abierta FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");

	$valorizar=isset($_GET['valorizar']);
	
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
		}
	}

	

?>

derpabs=<?php echo json_encode($derpabs); ?>;
codigos=<?php echo json_encode($cods); ?>;
utm=<?php echo $utm*1; ?>;
tipos_seguro=<?php echo json_encode($s); ?>;
interes=0;

var bloquear=0;

validacion_rut=function(obj) {

	obj.value=trim(obj.value);

	if( !comprobar_rut(obj.value) ) {
		obj.style.background='red';
		return false;
	} else {
		obj.style.background='yellowgreen';
		return true;	
	}

}
//NUEVO
		verificar_rut = function() {
    
      var texto = $('clirut').value;
      
      if(texto.charAt(0)=='R') {
        $('paciente_tipo_id').value=0;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='P') {
        $('paciente_tipo_id').value=1;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='I') {
        $('paciente_tipo_id').value=2;
        $('clirut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='F') {
        $('paciente_tipo_id').value=3;
        $('clirut').value=texto.substring(1,texto.length);
      }
      
      if($('paciente_tipo_id').value==0) {
      
        if(comprobar_rut($('clirut').value)) {
      
          $('clirut').style.background='inherit';
          validar_rut();
      
        } else {
		  alert('RUT INV&Aacute;LIDO'.unescapeHTML());
          $('clirut').style.background='red';
                
        }
        
      } else if($('paciente_tipo_id').value>0) {
      
          $('clirut').style.background='yellowgreen';
          validar_rut();
          
      }
      
    }
    
    busqueda_pacientes = function(objetivo, callback_func) {

      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-250;
  
      new_win =
      window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=500, height=300, '+
        'top='+top+', left='+left);
  
      new_win.objetivo_cod = objetivo;
      new_win.onCloseFunc = callback_func;
  
      new_win.focus();

    }
    
//FIN NUEVO
validacion_fecha = function(obj) {
		
	obj.value=trim(obj.value.replace(/-/gi,'/'));
	
	var fecha_ok=obj.value;
	
	switch(fecha_ok.length) {
		case 6:

			decada=(fecha_ok.charAt(4)+''+fecha_ok.charAt(5))*1;

			if(decada>50) 
				siglo='19';
			else
				siglo='20';
				
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+siglo+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5);
			break;						
		case 8:
			fecha_ok=fecha_ok.charAt(0)+
						fecha_ok.charAt(1)+'/'+
						fecha_ok.charAt(2)+
						fecha_ok.charAt(3)+'/'+
						fecha_ok.charAt(4)+
						fecha_ok.charAt(5)+
						fecha_ok.charAt(6)+
						fecha_ok.charAt(7);
			break;
	}
	
	if( !isDate(fecha_ok) ) {
		obj.style.background='red';
		return false;
	} else {
		obj.style.background='yellowgreen';
		obj.value=fecha_ok;
		return true;	
	}
		
		
}


validar_rut = function() {

	if(bloquear) return;

	bloquear=1;
	
	$('clirut').value=$('clirut').value.toUpperCase();

	var myAjax=new Ajax.Request('ingresos/datos_paciente.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('clirut').value),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
			
				$('pac_id').value=d['pac_id']*1;
				$('pac_edad').value=d['edad_anios']*1;
				
				$('cliedad').innerHTML='('+$('pac_edad').value+'a)';
				
				$('clipat').innerHTML=d['pac_appat'];
				$('climat').innerHTML=d['pac_apmat'];
				$('clinom').innerHTML=d['pac_nombres'];
								
				$('datos_titular').value=(d['pac_rut']+'|'+d['pac_nombres']+' '+d['pac_appat']+' '+d['pac_apmat']+'|'+d['pac_direccion']+', '+d['ciud_desc']+'|'+d['pac_fono']).unescapeHTML();
				
				if($('pac_edad').value*1>=60){
					$('td_cobertura').innerHTML='Adulto Mayor';
				}else if(d['pac_prais']=='t'){
					$('td_cobertura').innerHTML='Paciente PRAIS';
				}else if(d['pac_pbs']=='t'){
					$('td_cobertura').innerHTML='Paciente PBS';
				}else{
					$('td_cobertura').innerHTML='Sin Cobertura';
				}
				
				//$('clidir').value=d['pac_direccion'].unescapeHTML();
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value=d['pac_fc_nac'].unescapeHTML();

				//$('prev_id').value=d['prev_id']*1;
				//$('prevision').value=d['prev_desc'].unescapeHTML();
				
				//if($('prevision').value=='') {
				if(d['prev_id']*1==5 || d['prev_id']*1==9 || d['prev_id']*1==10 || d['prev_id']*1==11 || d['prev_id']*1==12 || d['prev_id']*1==13 || d['prev_id']*1==14 || d['prev_id']*1==15 || d['prev_id']*1==16 || d['prev_id']*1==17 || d['prev_id']*1==18 || d['prev_id']*1==19 || d['prev_id']*1==20 || d['prev_id']*1==21 || d['prev_id']*1==22){
					$('prev_id').value=d['prev_id']*1;
					$('prevision').value=d['prev_desc'].unescapeHTML();
		
					if(d['pac_prais']=='t') {
						if(!confirm('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.\n ¿DESEA RECAUDAR?'.unescapeHTML())){
						
							//alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.'.unescapeHTML());
							cambiar_pagina('creditos/ingreso/form.php?pacrut=0&dv=0');
						}
					}
					
					if(d['pac_pbs']=='t') {
						if(!confirm('Alerta PBS:\n===================================\n\nPACIENTE ES PBS, NO DEBE RECAUDAR ESTAS PRESTACIONES.\n ¿DESEA RECAUDAR?'.unescapeHTML())){
						
							//alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.'.unescapeHTML());
							cambiar_pagina('creditos/ingreso/form.php?pacrut=0&dv=0');
						}
					}
					
					cargar_prestaciones();
					
				}else{
					actualizar_prevision();
				}
				//}
				$('prevision').readonly=true;
				
				if(d['pac_rut']!='')
					$('btn_sigges').disabled=false;
				else
					$('btn_sigges').disabled=true;
									
			} else {
			
				$('pac_id').value='';
				$('pac_edad').value='';
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('clinom').innerHTML='';
				$('datos_titular').value='';
				$('td_cobertura').innerHTML='';
				//$('clidir').value='';
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value='';
				$('clirut').focus();
				$('ver_func').hide();
				
				$('prevision').value='';
				$('prevision').readonly=true;
				
				prestaciones=[];
				
				alert('Paciente no encontrado.');
							
			}

			validacion_rut($('clirut'));
			//validacion_fecha($('clifnac'));
			
			} catch(err) {
				alert(err);
			} 

			bloquear=0;

		}	
	});

}

actualizar_prevision=function() {

	var myAjax=new Ajax.Request(
		'ingresos/certificar_paciente.php',
		{
			method:'post',parameters:'rut='+encodeURIComponent($('clirut').value)+'&tipo_rut='+encodeURIComponent($('paciente_tipo_id').value),
			onComplete:function(r) {
			
				var d=r.responseText.evalJSON(true);
				
				$('cargar_fonasa').hide();
			
				if(d) {
			
					$('prev_id').value=d['prev_id']*1;
					
					//if(d['prev_id']*1!=6)
						$('prevision').value=d['prev_desc'].unescapeHTML();
					//else
						//$('prevision').value=d['desc'].unescapeHTML();

					$('frec_id').value=d['frec_id']*1;	
						
					if(d['frec_id']*1>0) {
						$('ver_func').show();
					} else {
						$('ver_func').hide();
					}
					
					if(d['prais']!='000' && d['prais']!='' && d['prais']!=null) {
						if(!confirm('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.\n ¿DESEA  RECAUDAR?'.unescapeHTML())){
							$('td_cobertura').innerHTML='Paciente PRAIS';
							//alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.'.unescapeHTML());
							cambiar_pagina('creditos/ingreso/form.php?pacrut=0&dv=0');
						}
					}else{
						if($('pac_edad').value*1>=60){
							$('td_cobertura').innerHTML='Adulto Mayor';
						}else{
							$('td_cobertura').innerHTML='Sin Cobertura';
						}
					}
					
					if(d['pbs']=='t') {
						$('td_cobertura').innerHTML='Paciente PBS';
						if(!confirm('Alerta PBS:\n===================================\n\nPACIENTE ES PBS, NO DEBE RECAUDAR ESTAS PRESTACIONES.\n ¿DESEA RECAUDAR?'.unescapeHTML())){
						
							//alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.'.unescapeHTML());
							cambiar_pagina('creditos/ingreso/form.php?pacrut=0&dv=0');
						}
					}
					
				cargar_prestaciones();

						
				} else {
				
					$('prev_id').value='';
					$('prevision').value='ERROR';
					$('ver_func').hide();
				
				}
				
			}
		}
	);

}

cargar_prestaciones=function() {
	
	if($('filtro_presta').value*1==3) {
		$('div_cuotas').show();
	} else {
		$('div_cuotas').hide();
	}
	
	var myAjax=new Ajax.Request(
		'ingresos/prestaciones.php',
		{
			method:'post',
			parameters:$('pac_id').serialize()+'&'+$('filtro_presta').serialize()+'&'+$('modalidad').serialize()+'&'+$('frec_id').serialize(),
			onComplete:function(resp) {
				
				try {
					
					var datos=resp.responseText.evalJSON(true);
					
					if(datos) {
						prestaciones=datos;
					} else {
						prestaciones=[];
					}
					redibujar_tabla();
				
				} catch(err) {
				
					alert(err);
					
				}
				
			}
		}
	);
	
}

calcular_cuota=function() {

	$('descuento').innerHTML='$ '+number_format($('total_descuento').value*1,0,',','.')+'.-';
	
	var proval=($('proval').value*1)-($('total_descuento').value*1);
	var pie=$('pie').value*1;
	var cuonro=$('cuonro').value*1;

	if(proval<=0) {
	
		$('pie').value='';
		$('pie').disabled=true;
		$('pie2').value='';
		$('pie2').disabled=true;	
		pie=0;

	} else {

		$('pie').disabled=false;
		$('pie2').disabled=false;

	}
	
	if(pie>=proval) {
		$('ingresa').value='Ingresar Recaudaci&oacute;n ... >>'.unescapeHTML();
		//$('cuotas0').style.display='none';
		//$('cuotas1').style.display='none';
		$('cuotas2').style.display='none';
		$('cuotas3').style.display='none';
		$('cuotas4').style.display='none';
	} else {
		if($('pagare').checked) {
			$('ingresa').value='Ingresar Pagar&eacute; ... >>'.unescapeHTML();
			//$('cuotas0').style.display='none';
			$('cuotas1').style.display='none';
			$('cuotas2').style.display='none';
			$('cuotas3').style.display='none';	
			$('cuotas4').style.display='none';	
		} else {
			$('ingresa').value='Ingresar Cr&eacute;dito ... >>'.unescapeHTML();
			//$('cuotas0').style.display='';
			$('cuotas1').style.display='';
			$('cuotas2').style.display='';
			$('cuotas3').style.display='';	
			$('cuotas4').style.display='';	
		}
	}	
	
	if(proval<=0 || cuonro==0) {
	
		$('valor_cuota').innerHTML='$ 0.-';
		$('total_interes').innerHTML='0%';
		$('saldo_credito').innerHTML='$ 0.-';

		if(proval<0) {
			$('afavor').style.display='';
			$('saldo_favor').innerHTML='$ '+number_format(-proval,0,',','.')+'.-';
		} else {
			$('afavor').style.display='none';
		}

	} else {

		$('afavor').style.display='none';

		var valcredito = ( proval - pie );
		
		if($('aplicaint').checked) {
			
			var inf=interes/100;
			var fact=Math.pow(1+inf,cuonro);
			
			var valcuota=Math.ceil(valcredito*((inf*fact)/(fact-1)));
			var valinteres=valcuota*cuonro;
			var tinteres=((valinteres/valcredito)-1)*100;
			
		} else {
			
			var valcuota=Math.ceil(valcredito/cuonro);
			var valinteres=valcuota*cuonro;
			var tinteres=0;
			
		}
		
		$('total_interes').innerHTML=number_format(tinteres,1,',','.')+'%';
		
		if( valcredito > 0 )
			$('saldo_credito').innerHTML='$ '+number_format(valcredito,0,',','.')+'.-';
		else
			$('saldo_credito').innerHTML='- $ '+number_format(-valcredito,0,',','.')+'.-';
			
		$('valor_credito').innerHTML='$ '+number_format(valinteres,0,',','.')+'.-';	
		$('valor_cuota').innerHTML='$ '+number_format(valcuota,0,',','.')+'.-';	
	
	}

}

imprimir_boletin=function(bolnum) {
	alert("POR FAVOR AJUSTE LA BOLETA EN SU IMPRESORA Y PRESIONE ACEPTAR.");
	window.open('conectores/okidata/imprimir_boleta.php?bolnum='+bolnum,'_blank');
}

ingresar_credito=function() {

	if($('btn_apertura_caja')!=undefined) {
		alert("Debe realizar su APERTURA DE CAJA para poder recaudar.");
		return;
	}

	if(prestaciones.length==0) {
		alert("El paciente no tiene prestaciones pendientes por recaudar.");
		return;
	}
	
	if($('pac_id').value*1==0) {
		alert( "Debe seleccionar paciente.".unescapeHTML() );
		return;
	}	

	if($('nbolnum').value!='' && !validacion_fecha($('nbolfec'))) {
		alert( "La fecha del comprobante no es v&aacute;lida.".unescapeHTML() );
		return;
	}	

	/*if(!validacion_fecha($('clifnac'))) {
		alert( "La fecha de nacimiento no es v&aacute;lida.".unescapeHTML() );
		return;
	}*/	
	
	/*if(trim($('clirut').value)=='' ||
		trim($('clipat').value)=='' ||
		trim($('climat').value)=='' ||
		trim($('clinom').value)=='' ||
		trim($('clidir').value)=='') {
	
		alert( "Los datos del cliente est&aacute;n incompletos.".unescapeHTML() );	
		return;
	}*/

	var proval=($('proval').value*1)-($('total_descuento').value*1);

	if( (proval > 0 && ($('pie').value*1==0 && !$('pagare').checked))) {
		alert( "Debe ingresar monto a cancelar no corresponde." );
		return;
	}
	
	if( ((proval - $('pie').value*1)<0 && !$('pagare').checked) ) {
		alert( "Monto ingresado no es suficiente." );
		return;
	} 

	if( !$('pagare').checked && ((proval - $('pie').value*1)>0 && ($('cuonro').value*1==0 || $('filtro_presta').value!='3') ) ) {
		alert( "Debe ingresar numero de cuotas para el saldo." );
		return;	
	}

	if(derpab>0) {

				if(derpab<10) _derpab='0000'+derpab;
				else _derpab='000'+derpab;
				
				var num=prestaciones.length;
				
				prestaciones[num]=new Object();
				prestaciones[num].presta_id=0;
				prestaciones[num].fecha=fecpab;
				prestaciones[num].codigo='DP'+_derpab;
				prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab;
				prestaciones[num].precio=Math.round(derpabs[derpab-1]);
				prestaciones[num].copago=Math.round(derpabs[derpab-1]*factor_pab);
				prestaciones[num].cantidad=1;
				prestaciones[num].cobro="S";

	
	}
	
	params=$('credito').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON());
	
	if(seguros.item!=undefined)
		params+='&seguros='+encodeURIComponent(seguros.item.toJSON());
	else
		params+='&seguros=false';
	
	params+='&'+$('pac_id').serialize();
	
	$('ingresa').disabled=true;	
	
	var myAjax=new Ajax.Request(
	'creditos/sql_ingreso_credito.php',
	{
		method:'post',
		parameters: params,
		onComplete: function(resp) {
		
			try {		
		
				d=resp.responseText.evalJSON(true);
				imprimir_boletin(d[1]);
				cambiar_pagina('creditos/ingreso/form.php?pacrut=0&dv=0');
			
			} catch(err) {
			
				alert( 'ERROR:\n\n' + resp.responseText.unescapeHTML() );			
				$('ingresa').disabled=false;	
			
			}
		
		}	
	}	
	);


}

pago='';
seguros=new Object();

forma_pago=function() {

	var params='total='+($('pie').value*1);

	l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-250;
        
    win = window.open('ingresos/forma_pago.php?'+params, 
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=490');
                    
    win.focus();


}

registro_seguros=function() {

    var params=$('pac_id').serialize();

    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;

    win = window.open('ingresos/registro_seguros.php?'+params,
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=550');

    win.focus();


}


datos_pagare=function() {

    var params=$('pac_id').serialize()+'&'+$('datos_titular').serialize();

    l=(screen.availWidth/2)-375;
    t=(screen.availHeight/2)-275;

    win = window.open('ingresos/registro_pagare.php?'+params,
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=550');

    win.focus();


}


definir_descuentos=function() {

	var params=($('bolnums').serialize()+'&'+$('total_descuento').serialize());

	 l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-250;
        
    win = window.open('creditos/ingreso/definir_descuentos.php?'+params, 
                    '_descuentos',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=490');
                    
    win.focus();

}



ver_sigges=function() {
		
		 sigges = window.open('ficha_clinica/registro_sigges.php?rut='+encodeURIComponent($('clirut').value),
		 'sigges', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
			
		 sigges.focus();
		
}

ver_coberturas=function() {

                 sigges = window.open('ingresos/form_coberturas.php?rut='+encodeURIComponent($('clirut').value),
                 'sigges', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');

                 sigges.focus();

}

apertura_caja=function() {

	var myAjax=new Ajax.Request(
		'creditos/ingreso/sql_apertura.php',
		{
			method:'post',
			onComplete:function(r) {
				$('btn_apertura').innerHTML='';
			}
		}
	);

}

limpiar_recaudacion = function(){
	
	$('clirut').value='';
	$('pac_id').value='';
	$('pac_edad').value='';
	$('frec_id').value='';
	$('prev_id').value='';
	$('clipat').innerHTML='';
	$('climat').innerHTML='';
	$('clinom').innerHTML='';
	$('cliedad').innerHTML='';
	$('prevision').value='';
	$('td_cobertura').innerHTML='';
	$('filtro_presta').value='0';
	$('nboleta').value='';
	$('nbolnum').value='';
	$('lista_productos').innerHTML='';
	$('modalidad').value='mai';
	$('cod_presta2').value='';
	$('valor_presta').value='';
	$('cod_presta').value='';
	$('cant_presta').value='1';
	$('valor_total').innerHTML='$ 0.-'
	$('valor_productos').innerHTML='$ 0.-';
	$('pagare').checked=false;
	$('proval').value='0';
	$('descuento').innerHTML='$ 0.-';
	$('total_descuento').value='0';
	$('bolnums').value='';
	$('pie').value='';
	$('pago_efectivo').value='';
	$('pago_cheques').value='';
	$('pago_otras').value='';
	$('div_cuotas').hide();
}

guardar_cuenta=function() {

	if(derpab>0) {

                                if(derpab<10) _derpab='0000'+derpab;
                                else _derpab='000'+derpab;

                                var num=prestaciones.length;

                                prestaciones[num]=new Object();
                                prestaciones[num].presta_id=0;
                                prestaciones[num].fecha=fecpab;
                                prestaciones[num].codigo='DP'+_derpab;
                                prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab;
                                prestaciones[num].precio=Math.round(derpabs[derpab-1]);
                                prestaciones[num].copago=Math.round(derpabs[derpab-1]*factor_pab);
                                prestaciones[num].cantidad=1;
				prestaciones[num].modalidad='mle';
                                prestaciones[num].cobro="S";


        }

        params=$('credito').serialize()+'&prestaciones='+encodeURIComponent(prestaciones.toJSON());

        if(seguros.item!=undefined)
                params+='&seguros='+encodeURIComponent(seguros.item.toJSON());
        else
                params+='&seguros=false';

        params+='&'+$('pac_id').serialize()+'&'+$('hosp_id').serialize();

        $('ingresa').disabled=true;

        var myAjax=new Ajax.Request(
        'creditos/sql_guardar_cuenta.php',
        {
                method:'post',
                parameters: params,
                onComplete: function(resp) {
			alert('Cuenta guardada exitosamente');

			l=(screen.availWidth/2)-325;
			    t=(screen.availHeight/2)-250;

			 win = window.open('creditos/descargar_cuenta.php?'+$('hosp_id').serialize(),
                    		'_descuentos',
                    		'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    		'resizable=no, width=650, height=490');

    			win.focus();
		}
	});

}

abrir_cuenta=function() {

	var myAjax=new Ajax.Request(
                'ingresos/cuenta_corriente.php',
                {
                        method:'post',
                        parameters:$('hosp_id').serialize(),
                        onComplete:function(resp) {

                                try {

                                        var datos=resp.responseText.evalJSON(true);

                                        if(datos) {

						$('pac_id').value=datos[0].pac_id;

						if(datos[0].pac_rut!='') {
							$('clirut').value=datos[0].pac_rut;
							$('paciente_tipo_id').value='0';
						} else {
							$('clirut').value=datos[0].pac_ficha;
							$('paciente_tipo_id').value='3';
						}

						$('clipat').innerHTML=datos[0].pac_appat;
						$('climat').innerHTML=datos[0].pac_apmat;
						$('clinom').innerHTML=datos[0].pac_nombres;
						if(datos[0].prev_desc!=undefined) {
							$('prev_id').value=datos[0].prev_id;
							$('prevision').value=datos[0].prev_desc.unescapeHTML();
							$('modalidad').value=datos[0].bolmod;
						} else actualizar_prevision();

						$('pac_edad').value=datos[0].edad_anios*1;
		                                $('cliedad').innerHTML='('+$('pac_edad').value+'a)';

                                                prestaciones=datos[1];
                                        } else {
                                                prestaciones=[];
                                        }

                                        redibujar_tabla();

                                } catch(err) {

                                        alert(err);

                                }

                        }
                }
        );	

}

calcular_vuelto = function(){

	var vuelto = ($('pie2').value*1)-($('pie').value*1);
	if(vuelto>=0)
		$('valor_vuelto').innerHTML='$ '+number_format(vuelto,0,',','.')+'.-';
	else
		$('valor_vuelto').innerHTML='$ '+number_format(0,0,',','.')+'.-';
}	


recibir_pac = function(){
	
	 pacrut = <?php echo $_GET['pacrut'];?>;
	 pacdv = <?php echo $_GET['dv'];?>;
	 
	 if(pacrut!='0'){
	 
		 $('clirut').value=pacrut+'-'+pacdv;
		 
		 verificar_rut();
	 }
	 
}

recibir_pac();
</script>



<center>

<div class='sub-content' style='width:95%;'>

<form id='credito' name='credito' onSubmit='return false;'>

<input type='hidden' id='pac_id' name='pac_id' value='' />
<input type='hidden' id='pac_edad' name='pac_edad' value='' />
<input type='hidden' id='frec_id' name='frec_id' value='0' />
<input type='hidden' id='prev_id' name='prev_id' value='' />
<input type='hidden' id='datos_titular' name='datos_titular' value='' />

<div class='sub-content'>
<table style='width:100%;font-size:12px;'><tr><td>
<img src='iconos/script.png'></td><td>
<?php if(!$valorizar) { ?><b>Recaudaci&oacute;n de Prestaciones</b> <?php } else { ?><b>Valorizaci&oacute;n de Prestaciones</b> <?php } ?>
</td>

<td id='btn_apertura'>
<?php 
if(!$valorizar) {
if(!$ac) {
?>
<input type='button' id='btn_apertura_caja' name='btn_apertura_caja' style='font-size:16px;color:red;border:2px solid yellow;' value='APERTURA CAJA' onClick='apertura_caja();' />
<?php } else { ?>
<?php 
if($ac['abierta']=='t') print('<span style="color:red;font-weight:bold;">');
echo 'APERTURA CAJA: '.substr($ac['ac_fecha_apertura'],0,16); 
if($ac['abierta']=='t') print('</span>');
?>
<?php } }  ?>
</td>

<td style='text-align:right;'>Cuenta Cte.:</td>
<td><input type='text' id='hosp_id' name='hosp_id' onKeyUp='if(event.which==13) abrir_cuenta();' /></td>

<td style='text-align:right;width:100px;<?php if($valorizar) echo "display:none;"; ?>'>
Nro. de Boleta:
</td>
<td <?php if($valorizar) echo "style='display:none;'"; ?>>
<input type='text' id='nboleta' name='nboleta'
style='text-align:center;' value='' size=10 />
<input type='text' id='nbolnum' name='nbolnum' style='display:none;'
style='text-align:center;' value='' size=10 />
</td>

<td style='text-align:right;<?php if($valorizar) echo "display:none;"; ?>'>
Fecha:
</td>

<td <?php if($valorizar) echo "style='display:none;'"; ?>>
<input type='text' id='nbolfec' name='nbolfec' style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' onKeyUp='validacion_fecha(this);' size=10 />
</td>

</tr></table>
</div>

<div class='sub-content'>

<table style='width:100%;' cellpadding=1 cellspacing=0>
	
<tr>
<td style='width:20px;'><center><img src='iconos/user.png' /></center></td>
<td style='width:20px; font-weight: bold;'>
<select id="paciente_tipo_id" name="paciente_tipo_id"
style="font-size:10px;" >
<option value=0 SELECTED>R.U.T.</option>
<option value=3>Nro. de Ficha</option>
<option value=1>Pasaporte</option>
</select>
</td><td style='width:20px;'><input type='text' id='clirut' name='clirut' size=11
style='text-align: center;font-size:13px;' onKeyUp='
if(event.which==13) { this.value=this.value.toUpperCase();
verificar_rut(); }
' maxlength=11 value='<?php echo $pac_rut; ?>'></td>
<td>&nbsp;
<img src='iconos/calendar.png' id='coberturas_paciente' onClick='ver_coberturas();'>
<img src='iconos/zoom_in.png' id='buscar_paciente'
onClick='
busqueda_pacientes("clirut", function() { verificar_rut(); });
'
onKeyUp="fix_bar(this);"
alt='Buscar Paciente...'
title='Buscar Paciente...'>
</td>

<td><input type='button' id='btn_sigges' name='btn_sigges' value='[SIGGES]' onClick='ver_sigges();' DISABLED /></td>
<td style='font-size:13px;'>
<span id='clipat' name='clipat' style='font-weight:bold;'></span>
<span id='climat' name='climat' style='font-weight:bold;'></span>
<span id='clinom' name='clinom' style='font-weight:bold;'></span>
<span id='cliedad' name='cliedad'></span>
</td>
<td style='text-align:right;width:5%;'>Previsi&oacute;n:</td>
<td style='font-weight:bold;width:10%;'>
<input type='text' id='prevision' name='prevision' READONLY 
onClick='if(this.value!="") alert("PREVISION:\n\n"+this.value);' style='text-align:left;font-size:15px;color:blue;font-weight:bold;overflow:hidden;' size=20>
<img src='imagenes/ajax-loader1.gif' style='display:none;' id='cargar_fonasa' />
</td>
<td style="width:20px;">Coberturas Adicionales:</td><td style="font-weight:bold; width:20px;" id='td_cobertura'></td>
<td style='text-align:right;width:5%;'>Prestaciones:</td>
<td style='width:10%;'>
<select id='filtro_presta' name='filtro_presta' onChange='cargar_prestaciones();' style='font-size:12px;'>
<option value='0'>Solo Hoy</option>
<option value='1'>Hoy en Adelante</option>
<!---<option value='2'>Todas</option>--->
<option value='3'>Odontolog&iacute;a</option>
</select>
</td>

</tr>


</table>

</div>


<table style='width:100%;'>
<tr id='listado_prods'><td colspan=2>
<div class='sub-content2' style='height:<?php if($valorizar) echo '320'; else echo '160'; ?>px;overflow:auto;' id='lista_productos'>

</div>

<div class='sub-content' style='padding:0px;'>
<table style='width:100%;font-size:10px;margin:0px;' cellpadding=1 cellspacing=0>
<tr>
<td><center><img src='iconos/add.png'></center></td>
<td style='text-align:right;'>Prestaciones:</td>

<td style='text-align:right;width:100px;'>
Modalidad:
</td>
<td>
<select id='modalidad' name='modalidad'>
<option value='mai' SELECTED>INSTITUCIONAL</option>
<option value='mle'>LIBRE ELECCION</option>
</select>
</td>

<td>
<center>
<input type='text' id='pfec' name='pfec' value='<?php echo date('d/m/Y'); ?>' onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
</center>
</td>

<td>
<input type='hidden' id='cod_presta2' name='cod_presta2' value='' />
<input type='hidden' id='valor_presta' name='valor_presta' value='' />
<input type='text' id='cod_presta' name='cod_presta' style='text-align:left;font-size:11px;' size=15 />
</td>
<td style='width:40%;'>
<input type='text' id='desc_presta' name='desc_presta' READONLY
style='text-align:left;width:90%;font-size:11px;' onDblClick='if(this.value!="") alert("C&Oacute;DIGO FONASA: ".unescapeHTML()+$("cod_presta2").value+"\n\nGLOSA:\n"+this.value);' />
</td>
<td>Cant:</td>
<td>
<input type='text' id='cant_presta' name='cant_presta' value='1' style='text-align:center;' size=5 onKeyUp='if(event.which==13) agregar_prestacion();' />
</td>
<td>
<input type='button' id='agrega_presta' name='agrega_presta' value='[Agregar...]' onClick='agregar_prestacion();' />
</td>
</tr>
</table>
</div>


</td></tr>

<tr id='datos_venta' <?php if($valorizar) echo "style='display:none;'"; ?>><td valign='top'>

<div class='sub-content'>
<img src='iconos/money.png'>
<b>Total a Pagar</b>
<span id='ver_func' style='color:red;font-weight:bold;display:none;'>(CONVENIO FUNCIONARIOS)</span>
</div>

<div class='sub-content'  <?php if($valorizar) echo "style='display:none;'"; ?>>


<table style='width:100%;' cellpadding=0 cellspacing=0><tr><td style='width:50%;'>



<table style='width:100%;' cellpadding=1 cellspacing=0>
<tr>
	<td style='text-align:right;'>Cajero:</td>
	<td><b><?php echo htmlentities($_SESSION['sgh_usuario']); ?></b></td>
</tr>
<tr>
<td style='text-align:right;'>Valor Prestacion(es):</td>
<td colspan=3 id='valor_total'
style='font-size:18px;font-weight:bold;color:gray;'>$ 0.-</td>
</tr>

<tr>
<td style='text-align:right;'>Copago:</td>
<td colspan=3 id='valor_productos'
style='font-size:18px;font-weight:bold;color:blue;'>$ 0.-</td>
</tr>
<input type='hidden' id='proval' name='proval' value=0>

<tr style='display:none;'>
<td style='text-align:right;'>Descuento(s):</td>
<td colspan=2 id='descuento'
style='font-size:18px;font-weight:bold;color:red;'>$ 0.-</td>
<td>
<input type='button' style='font-size:11px;' 
onClick='definir_descuentos();' value='Definir...'>
</td>
</tr>
<input type='hidden' id='total_descuento' name='total_descuento' value=''>
<input type='hidden' id='bolnums' name='bolnums' value=''>


<tr>
<td style='text-align:right;'>Pagar&eacute;:</td>
<td colspan=2><input type='checkbox' id='pagare' name='pagare' 
onChange='calcular_pagare();'> <i>No realiza pago.</i></td>
<td>
<input type='button' id='titular_pagare' name='titular_pagare' 
style='display:none;' value='Datos del Titular...' onClick='datos_pagare();' />
</td>
</tr>

<tr id='tr_pie'>
<td style='text-align:right;'>Monto a Cancelar $:</td>
<td colspan=2><input type='text' id='pie' name='pie' 
onKeyUp='calcular_cuota();' size=10></td>
<td>
<input type='hidden' id='pago_efectivo' name='pago_efectivo' value=''>
<input type='hidden' id='pago_cheques' name='pago_cheques' value=''>
<input type='hidden' id='pago_otras' name='pago_otras' value=''>
<input type='button' style='font-size:11px;' id='btn_forma_pago' name='btn_forma_pago'
value='Forma de Pago...' onClick='forma_pago();'></td>
</tr>

<tr id='tr_pie2'>
<td style='text-align:right;'>Monto Paciente $:</td>
<td colspan=2><input type='text' id='pie2' name='pie2' 
onKeyUp='calcular_vuelto();' size=10></td>
</tr>
<tr id='tr_pie3'>
<td style='text-align:right;'>Vuelto $: </td>
<td colspan=3 id='valor_vuelto'
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>

<tr>
<td style='text-align:right;'>Seguros:</td>
<td colspan=4>
<input type='button' style='font-size:11px;' id='reg_seguros' name='reg_seguros' value='Registro de Seguros (0)' onClick='registro_seguros();'></td>
</tr>


</table>






</td><td style='width:50%;'>






<div id='div_cuotas' style='display:none;'>

<center>
<b><u>Cuotas de Prestaciones Odontol&oacute;gicas</u></b><br/><br/>

<table style='width:100%;' cellpadding=1 cellspacing=0>

<tr id='cuotas1' style=''>
<td style='text-align:right;'>Nro. de Cuotas:</td>
<td><input type='text' id='cuonro' name='cuonro' 
onKeyUp='calcular_cuota();' size=5 value="">
</td>
</tr>

<tr id='cuotas0' style='display:none;'>
<td style='text-align:right;'>Aplicar Inter&eacute;s:</td>
<td><input type='checkbox' id='aplicaint' name='aplicaint' 
onChange='calcular_cuota();' size=5>
</td>
<td>Inter&eacute;s:</td>
<td id='total_interes' style='text-align:center;font-weight:bold;'>0%</td>
</tr>

<tr id='cuotas4'>
<td style='text-align:right;'>D&iacute;a de Pago:</td>
<td>
<input type='text' id='diapago' name='diapago' 
size=5 value="<?php echo date('d'); ?>">
</td></tr>

<tr id='cuotas1'>
<td style='text-align:right;'>Saldo Cr&eacute;dito:</td>
<td id='saldo_credito' colspan=3 
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>

<tr id='cuotas2'>
<td style='text-align:right;'>Valor Cr&eacute;dito:</td>
<td id='valor_credito' colspan=3 
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>

<tr id='cuotas3'>
<td style='text-align:right;'>Valor Cuota:</td>
<td id='valor_cuota' colspan=3
style='font-size:18px;font-weight:bold;color:red;'>$ 0.-</td>
</tr>

<tr id='afavor' style='display:none;'>
<td style='text-align:right;'>Saldo a Favor:</td>
<td id='saldo_favor' colspan=3
style='font-size:18px;font-weight:bold;color:green;'>$ 0.-</td>
</tr>


</table>


</center>

</div>



</td></tr></table>

<center>
<input type='button' id='ingresa' name='ingresa'
onClick='ingresar_credito();'
value='Ingresar Recaudaci&oacute;n ... >>'>

<input type='button' id='limpiar' name='limpiar'
onClick='limpiar_recaudacion();'
value='(Limpiar Selecci&oacute;n...)'>
</center>

</div>

</td></tr>


</table>

<?php if($valorizar) { ?>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>Valor Prestacion(es):</td>
<td colspan=3 id='valor_total2'
style='font-size:22px;font-weight:bold;color:gray;'>$ 0.-</td>
</tr>

<tr>
<td style='text-align:right;'>Copago:</td>
<td colspan=3 id='valor_productos2'
style='font-size:22px;font-weight:bold;color:blue;'>$ 0.-</td>
</tr>
<tr><td colspan=2>
<center>
<input type='button' id='ingresa' name='ingresa'
onClick='guardar_cuenta();'
value='Guardar Cuenta ... >>'>

<input type='button' id='limpiar' name='limpiar'
onClick='limpiar_recaudacion();'
value='(Limpiar Selecci&oacute;n...)'>
</center>
</td></tr></table>

<?php } ?>

</form>

</div>
</center>

<script> 

		prestaciones=[];
		derpab=0; fecpab=''; factor_pab=1;

		/*seleccionar_comuna=function(d) {

			$('comdes').value=d[1].unescapeHTML();
			$('comcod').value=d[0]*1;				

		}*/
		
		calcular_pagare=function() {
		
			var pagare=$('pagare').checked;
			
			if(pagare) {
				$('pie').value='';
				$('pie').readonly=true;
				$('btn_forma_pago').disabled=true;
				$('tr_pie').hide();
				$('tr_pie2').hide();
				$('tr_pie3').hide();
				$('cuonro').value='1';
				$('cuonro').readonly=true;
				$('titular_pagare').show();
			} else {
				$('pie').value='';
				$('pie').readonly=true;
				$('btn_forma_pago').disabled=false;			
				$('tr_pie').show();
				$('tr_pie2').show();
				$('tr_pie3').show();
				$('cuonro').readonly=false;
				$('titular_pagare').hide();
			}
			
			calcular_totales();
			calcular_cuota();
		
		
		}
					
		calcular_totales=function() {

			var suma_total=0;
			var suma=0;

			derpab=0; fecpab=''; pabdental=false;
			
			var prev_id=$('prev_id').value*1;
			var pac_edad=$('pac_edad').value*1;
					
			for(var i=0;i<prestaciones.length;i++) {
				
				if(prestaciones[i].cobro!='S') continue;
				
				var cod=codigos[$('modalidad').value+''+prestaciones[i].codigo];

				if(cod==undefined) cod=codigos['Farmacia'+prestaciones[i].codigo];
				
				if(cod==undefined) continue;
				
				if(cod.pab!='' && cod.pab!='00' && prestaciones[i].modalidad!='mai') {
					//alert('DEBE CANCELAR DERECHO A PABELLON ['+cod.pab+']');
					if((cod.pab*1)>derpab) {
					
						derpab=(cod.pab*1);
						fecpab=prestaciones[i].fecha.substr(0,16);
						
						if(cod.codigo.substr(0,2)=='27' || cod.codigo=='1302022' || cod.codigo=='1302023') { // REGLA MAL HECHA!!!
							pabdental=true;
						} else {
							pabdental=false;
						}
						
						//alert(fecpab);
						
					}
				}
				
				suma_total+=Math.round(prestaciones[i].precio*prestaciones[i].cantidad);
				suma+=Math.round(prestaciones[i].copago*prestaciones[i].cantidad);
			}
			
			if(!pabdental) {
				if(prev_id==1 || pac_edad>=60) factor_pab=0;
				else if(prev_id==2) factor_pab=0;
				else if(prev_id==3) factor_pab=0.1;
				else if(prev_id==4) factor_pab=0.2;
				else factor_pab=1;
			} else {
				if(prev_id==1 || pac_edad>=60) factor_pab=0;
				else if(prev_id==2) factor_pab=0.3;
				else if(prev_id==3) factor_pab=0.5;
				else if(prev_id==4) factor_pab=0.8;
				else factor_pab=1;
			}

			
			if(derpab>0) {
				suma_total+=derpabs[derpab-1];
				suma+=Math.round(derpabs[derpab-1]*factor_pab);
			}

			$('valor_total').innerHTML='$ '+number_format(suma_total,0,',','.')+'.-';
			$('valor_productos').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			<?php if($valorizar) { ?>
			$('valor_total2').innerHTML='$ '+number_format(suma_total,0,',','.')+'.-';
                        $('valor_productos2').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			<?php } ?>
			$('proval').value=suma;
			//$('total_prods').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			$('pie').value=suma;
		}

		
		redibujar_tabla=function() {

			calcular_totales();
			calcular_cuota();		
		
			var html='<table style="width:100%;font-size:10px;"><tr class="tabla_header">';
			html+='<td style="width:5%;">Modalidad</td><td style="width:12%;">Fecha</td>';
			html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
			html+='<td>Descripci&oacute;n</td>';
			html+='<td style="width:3%;">Cant.</td>';
			html+='<td style="width:10%;">Valor</td>';
			html+='<td style="width:10%;">Copago</td>';
			html+='<td>Cobro</td>';
			html+='</tr>';
			
			var suma=0;
			
			var ver_prop=false;			
			
			for(var i=0;i<prestaciones.length;i++) {
				
				p=prestaciones[i];
				
				/*if(codigos[p.codigo]!=undefined) 
					ttipo=codigos[p.codigo].tipo; 
				else 
					ttipo=$('modalidad').value;*/
					
				ttipo=p.modalidad;
				
				clase=(i%2==0)?'tabla_fila':'tabla_fila2';
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+(ttipo=='mai'?'INST':'PART')+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+p.fecha.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">'+p.codigo+'</td>';
				html+='<td style="font-size:10px;">'+p.glosa+'</td>';
				html+='<td style="text-align:right;">'+p.cantidad+'</td>';
				html+='<td style="text-align:right;">$'+number_format(p.precio*p.cantidad,0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(p.copago*p.cantidad,0,',','.')+'</td>';
				
				//html+='<td style="text-align:center;"><img src="iconos/delete.png" ';
				//html+=' style="cursor:pointer;" onClick="quitar_prod('+i+');"/></td>';
				
				html+='<td style="text-align:center;white-space:nowrap;">';
				html+='<select id="cobro_'+i+'" name="cobro_'+i+'" onChange="guardar_cobro('+i+');" style="font-size:9px;margin:0px;padding:0px;text-align:center;">';
				
				html+='<option value="S" '+(p.cobro=='S'?'SELECTED':'')+'>SI</option>';
				html+='<option value="N" '+(p.cobro=='N'?'SELECTED':'')+'>NO</option>';
				html+='<option value="GES" '+(p.cobro=='GES'?'SELECTED':'')+'>GES</option>';
				
				if(seguros.item!=undefined && seguros.item.length>0) {
					for(var s=0;s<seguros.item.length;s++) {
						var iniciales=tipos_seguro[seguros.item[s].tipo][1];
						html+='<option value="S'+s+'" '+(p.cobro=='S'+s?'SELECTED':'')+'>SEG.'+iniciales+'</option>';
					}
				}
				
				if(p.codigo=='5001111') { html+='<option value="MOD" '+(p.cobro=='MOD'?'SELECTED':'')+'>MOD.</option>'; } 
				
				
				html+='</select><img src="iconos/delete.png" style="cursor:pointer;width:12px;height:12px;" onClick="quitar_prod('+i+');" /></td>'
				
				html+='</tr>';				

				/*if(p.cobro=='S') 
					suma+=p.valor;*/

			}			

			if(derpab>0) {
				
				clase=(i++%2==0)?'tabla_fila':'tabla_fila2';
				
				if(derpab<10) _derpab='0000'+derpab;
				else _derpab='000'+derpab;
				
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">PART</td>';
				html+='<td style="text-align:center;font-size:10px;">'+fecpab.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">DP'+_derpab+'</td>';
				html+='<td style="font-size:10px;color:gray;">DERECHO A PABELL&Oacute;N - '+derpab+'</td>';
				html+='<td style="text-align:right;">1</td>';
				html+='<td style="text-align:right;">$'+number_format(derpabs[derpab-1],0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(Math.round(derpabs[derpab-1]*factor_pab),0,',','.')+'</td>';
				html+='<td>&nbsp;</td></tr>';				
				
			}
			
			html+='</table>';		
		
			$('lista_productos').innerHTML=html;
						
		
		}

		guardar_cobro=function(n) {
		
			
			if($('cobro_'+n).value!='MOD')
				prestaciones[n].cobro=$('cobro_'+n).value;
			else {
				 pmanual = window.open('ingresos/modificar_prestacion.php?n='+n,
				 'editar_prestacion', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
					
				 pmanual.focus();
				
			}
				
			if($('cobro_'+n).value=='N') {
				prestaciones=prestaciones.without(prestaciones[n]);
				redibujar_tabla();
			}
			
			calcular_totales();
			calcular_cuota();
		
		}
		
		quitar_prod=function(n) {
			prestaciones=prestaciones.without(prestaciones[n]);
			redibujar_tabla();
		}

	  
	  
    lista_prestaciones=function() {

        if($('cod_presta').value.length<3) return false;

        var params='tipo=prestacion&'+$('cod_presta').serialize()+'&'+$('modalidad').serialize();

        /*if($('auge').checked) {
          params='tipo=prestacion_patologia&pat_id=';
          params+=getRadioVal('i
		  nfo_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
        }*/

        return {
          method: 'get',
          parameters: params
        }

    }
    
    seleccionar_prestacion = function(presta) {

      //$('codigo_prestacion').value=presta[0];
      //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
	  $('cod_presta2').value=presta[0].unescapeHTML();
	  $('desc_presta').value=presta[2].unescapeHTML();
	  //$('valor_presta').value=presta[3].unescapeHTML();
	  $('cant_presta').value='1';
      $('cant_presta').select();
      $('cant_presta').focus();

    }
	
	info_codigo=function(codigo) {

		var cod;
	
		cod=codigos[$('modalidad').value+''+codigo];
		if(cod==undefined) cod=codigos['crs'+codigo];
		if(cod==undefined) cod=codigos['Farmacia'+codigo];
		
		return cod;
	
	}

	info_codigo2=function(codigo, modalidad) {

		var cod;
	
		cod=codigos[modalidad+''+codigo];
		if(cod==undefined) cod=codigos['crs'+codigo];
		if(cod==undefined) cod=codigos['Farmacia'+codigo];
		
		return cod;
	
	}

	
	calcular_precio = function(codigo, modalidad) {

		var prev_id=$('prev_id').value*1;
		var pac_edad=$('pac_edad').value*1;
		var total=0; var valor=0;

		var cod=info_codigo2(codigo, modalidad);
		if(cod==undefined) return [0,0];
		
		if( cod.pago_fijo=='t' ) {
			
			total=cod.precio*1;
			valor=cod.precio*1;
						
		} else {
		
			if($('modalidad').value=='mai') {
		
				total=cod.precio*1;
			
				if($('frec_id').value*1>0) {
				
					if(prev_id>=1 && prev_id<=4) valor=cod.copago_b*1;
					else if(prev_id==5) { cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) valor=cod.precio*1; else valor=cod.precio*1 }
					else if(prev_id==6) { cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) valor=cod2.copago_b*1; else valor=cod.precio*1 }
					else valor=cod.precio*1;
				
				} else {
				
					if(prev_id==6) valor=cod.precio*1;
					else if(prev_id==5) {  cod2=info_codigo2(codigo, 'mle'); if(cod2!=undefined) { total=cod2.copago_b*1; valor=cod2.copago_b*1; } else { valor=cod.precio*1 }  }
					else if(prev_id>=1 && prev_id<=4 && pac_edad>=60) valor=0;
					else if(prev_id==1) valor=cod.copago_a*1;
					else if(prev_id==2) valor=cod.copago_b*1;
					else if(prev_id==3) valor=cod.copago_c*1;
					else if(prev_id==4) valor=cod.copago_d*1;
					else valor=cod.precio*1;
					
				}
			
			} else {

				if($('frec_id').value*1>0) {

					cod2=info_codigo2(codigo, 'mai'); if(cod2!=undefined) { total=cod2.precio*1; valor=cod2.precio*1; } else { total=cod.precio*1; valor=cod.precio*1; }
				
				} else {
			
					if(prev_id>=1 && prev_id<=4) { total=cod.copago_a*1; valor=cod.copago_a*1; }
					else if(prev_id==5) { total=cod.copago_b*1; valor=cod.copago_b*1; }
					else { total=cod.copago_d*1; valor=cod.copago_d*1; }
					
				}
			
			}
			
		}
		
		return [total, valor];
	
	}
	  
	agregar_prestacion = function() {

		if(!validacion_fecha($('pfec'))) {
			alert("La fecha introducida es incorrecta.");
			$('pfec').select(); $('pfec').focus();
			return;
		}

		var num=prestaciones.length;
		
		/*var valores=$('valor_presta').value.split('|');
		var prev_id=$('prev_id').value*1;
		var pac_edad=$('pac_edad').value*1;*/
		
		var tmp_valor=calcular_precio($('cod_presta2').value, $('modalidad').value);

		//alert(prev_id+'= '+valor);

		prestaciones[num]=new Object();
		prestaciones[num].fecha=$('pfec').value+' 00:00:00';
		prestaciones[num].codigo=$('cod_presta2').value;
		prestaciones[num].glosa=($('desc_presta').value);
		prestaciones[num].cantidad=($('cant_presta').value*1);
		prestaciones[num].precio=tmp_valor[0];
		prestaciones[num].copago=tmp_valor[1];
		prestaciones[num].presta_id=0;
		prestaciones[num].cobro='S';
		prestaciones[num].tipo=$('modalidad').value;
		prestaciones[num].modalidad=$('modalidad').value;
		prestaciones[num].id_padre=-1;
		
		try {
		
			//console.log($('modalidad').value+''+$('cod_presta2').value);
			
			var cod=info_codigo($('cod_presta2').value);

			if(cod!=undefined && cod.canasta!='') {
			
				//console.log(codigos[$('modalidad').value+''+$('cod_presta2').value]);
				
				_num=num;
				
				if(cod.canasta.indexOf('x')>=0) {
					var tmp=cod.canasta.split('x');
					codigo=tmp[0];
					cantidad=tmp[1];
				} else {
					codigo=cod.canasta;
					cantidad='1';
				}

				var tmp=info_codigo(codigo);
				
				if(tmp!=undefined) {
				
					var tmp_valor=calcular_precio(codigo, $('modalidad').value);
				
					num=prestaciones.length;
				
					prestaciones[num]=new Object();
					prestaciones[num].fecha='<?php echo date('d/m/Y H:i:s'); ?>';
					prestaciones[num].codigo=tmp.codigo;
					prestaciones[num].glosa=tmp.glosa;
					prestaciones[num].cantidad=cantidad;
					prestaciones[num].precio=tmp_valor[0];
					prestaciones[num].copago=tmp_valor[1];
					prestaciones[num].presta_id=0;
					prestaciones[num].cobro='S';
					prestaciones[num].tipo=$('modalidad').value;
					prestaciones[num].modalidad=$('modalidad').value;
					prestaciones[num].id_padre=_num;
					
				}

			
			}
		
		} catch(err) {
		
			//console.log(err);
		
		}

		redibujar_tabla();

		  $('cod_presta2').value='';
		  $('desc_presta').value='';
		  //$('valor_presta').value='';
		  $('cant_presta').value='1';
		  $('cod_presta').select();
		  $('cod_presta').focus();
		
		
		
		
	}
	  
	  
	  
	  
    autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      'autocompletar_sql.php',
      lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 2, seleccionar_prestacion);
	  
	  
	  
	  
	  
	validacion_rut($('clirut'));
	validacion_fecha($('pfec'));
	//validacion_fecha($('clifnac'));

</script>
