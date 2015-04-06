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
		SELECT cp.codigo, cp.glosa, precio, 0 AS copago_a, copago_b, copago_c, copago_d, pab, tipo
		FROM codigos_prestacion AS cp
		ORDER BY codigo;
	", true);
	
	$cods=Array();
	
	for($i=0;$i<sizeof($codigos);$i++) {
		$cods[$codigos[$i]['codigo']]=$codigos[$i];
	}
	
	$codigos2=cargar_registros_obj("
		SELECT dp_id, dp_valor FROM codigos_derecho_pabellon ORDER BY dp_id;
	", true);
	
	$derpabs=Array();
	
	for($i=0;$i<sizeof($codigos2);$i++) {
		$derpabs[($codigos2[$i]['dp_id']*1)-1]=$codigos2[$i]['dp_valor']*1;
	}
	

	
?>

<script>

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

	var myAjax=new Ajax.Request('ingresos/datos_paciente.php',
	{
		method:'post',
		parameters: 'str='+encodeURIComponent($('clirut').value),
		onComplete: function(resp) {
			
			try {
			
			d=resp.responseText.evalJSON(true);
			
			if(d) {
			
				$('pac_id').value=d['pac_id']*1;
				$('clipat').innerHTML=d['pac_appat'];
				$('climat').innerHTML=d['pac_apmat'];
				$('clinom').innerHTML=d['pac_nombres'];
				//$('clidir').value=d['pac_direccion'].unescapeHTML();
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value=d['pac_fc_nac'].unescapeHTML();

				//$('prev_id').value=d['prev_id']*1;
				//$('prevision').value=d['prev_desc'].unescapeHTML();
				
				//if($('prevision').value=='') {
				actualizar_prevision();
				//}
				$('prevision').disabled=true;
				
				if(d['pac_rut']!='')
					$('btn_sigges').disabled=false;
				else
					$('btn_sigges').disabled=true;
					
				cargar_prestaciones();
				
			} else {
			
				$('pac_id').value='';
				$('clipat').innerHTML='';
				$('climat').innerHTML='';
				$('clinom').innerHTML='';
				//$('clidir').value='';
				//$('comcod').value='';
				//$('comdes').value='';
				//$('clifon').value='';
				//$('clifnac').value='';
				$('clirut').focus();
				
				$('prevision').value='';
				$('prevision').disabled=true;
				
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
			method:'post',parameters:'rut='+encodeURIComponent($('clirut').value),
			onComplete:function(r) {
			
				var d=r.responseText.evalJSON(true);
				
				$('cargar_fonasa').hide();
			
				if(d) {
			
					$('prev_id').value=d['prev_id']*1;
					$('prevision').value=d['prev_desc'].unescapeHTML();
				
				} else {
				
					$('prev_id').value='';
					$('prevision').value='ERROR';
				
				}
				
			}
		}
	);

}

cargar_prestaciones=function() {
	
	var myAjax=new Ajax.Request(
		'ingresos/prestaciones.php',
		{
			method:'post',
			parameters:$('pac_id').serialize()+'&'+$('filtro_presta').serialize(),
			onComplete:function(resp) {
				
				try {
					
					prestaciones=resp.responseText.evalJSON(true);
				
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
		pie=0;

	} else {

		$('pie').disabled=false;

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
	window.open('ingresos/imprimir_boletin.php?bolnum='+bolnum,'_blank');
}

ingresar_credito=function() {

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

	if( proval > 0 && $('pie').value*1==0 || (proval - $('pie').value*1) < 0 ) {
		alert( "El monto a cancelar no corresponde." );
		return;
	}

	if(derpab>0) {

				if(derpab<10) _derpab='0000'+derpab;
				else _derpab='000'+derpab;
				
				var num=prestaciones.length;
				
				prestaciones[num]=new Object();
				prestaciones[num].presta_id=0;
				prestaciones[num].fecha="<?php echo date('d/m/Y H:i:s'); ?>";
				prestaciones[num].codigo='DP'+_derpab;
				prestaciones[num].glosa='DERECHO A PABELL&Oacute;N - '+derpab;
				prestaciones[num].precio=Math.round(derpabs[derpab-1]);
				prestaciones[num].copago=Math.round(derpabs[derpab-1]*factor_pab);
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


</script>



<center>

<div class='sub-content' style='width:95%;'>

<form id='credito' name='credito' onSubmit='return false;'>

<input type='hidden' id='pac_id' name='pac_id' value='' />
<input type='hidden' id='prev_id' name='prev_id' value='' />

<div class='sub-content'>
<table style='width:100%;font-size:12px;'><tr><td>
<img src='iconos/script.png'></td><td>
<b>Recaudaci&oacute;n de Prestaciones</b>
</td>

<td style='text-align:right;width:230px;display:none;'>
Nro. de Comprobante:
</td>

<td style='display:none;'>
<input type='text' id='nbolnum' name='nbolnum' 
style='text-align:center;' value='' size=10 />
</td>

<td style='text-align:right;'>
Fecha:
</td>

<td>
<input type='text' id='nbolfec' name='nbolfec' style='text-align:center;' 
value='<?php echo date('d/m/Y'); ?>' onKeyUp='validacion_fecha(this);' size=10 />
</td>

</tr></table>
</div>

<div class='sub-content'>

<table style='width:100%;' cellpadding=1 cellspacing=0>
<tr>
<td style='width:20px;'><center><img src='iconos/user.png' /></center></td>
<td style='text-align:right;width:10%;'>R.U.N./Ficha:</td>
<td style='font-weight:bold;'>
<input type='text' id='clirut' name='clirut' style='font-size:14px;text-align:center;'
onKeyUp='validacion_rut(this); if(event.which==13 && validacion_rut(this)) validar_rut();' size=12 />
</td>
<td><input type='button' id='btn_sigges' name='btn_sigges' value='[SIGGES]' onClick='ver_sigges();' DISABLED /></td>
<td style='font-size:13px;font-weight:bold;'>
<span id='clipat' name='clipat'></span>
<span id='climat' name='climat'></span>
<span id='clinom' name='clinom'></span>
</td>
<td style='text-align:right;width:5%;'>Previsi&oacute;n:</td>
<td style='font-weight:bold;width:10%;'>
<input type='text' id='prevision' name='prevision' style='font-size:16px;color:blue;font-weight:bold;' size=20>
<img src='imagenes/ajax-loader1.gif' style='display:none;' id='cargar_fonasa' />
</td>
<td style='text-align:right;width:5%;'>Prestaciones:</td>
<td style='width:10%;'>
<select id='filtro_presta' name='filtro_presta' onChange='cargar_prestaciones();' style='font-size:12px;'>
<option value='0'>Solo Hoy</option>
<option value='1'>Hoy en Adelante</option>
<option value='2'>Todas</option>
</select>
</td>

</tr>


</table>

</div>


<table style='width:100%;'>
<tr id='listado_prods'><td colspan=2>
<div class='sub-content2' style='height:160px;overflow:auto;' id='lista_productos'>

</div>

<div class='sub-content' style='padding:0px;'>
<table style='width:100%;font-size:10px;' cellpadding=1 cellspacing=0>
<tr>
<td><center><img src='iconos/add.png'></center></td>
<td style='text-align:right;'>Prestaciones:</td>
<td>
<input type='hidden' id='cod_presta2' name='cod_presta2' value='' />
<input type='hidden' id='valor_presta' name='valor_presta' value='' />
<input type='text' id='cod_presta' name='cod_presta' style='text-align:left;font-size:11px;' size=15 />
</td>
<td style='width:40%;'>
<input type='text' id='desc_presta' name='desc_presta' READONLY
style='text-align:left;width:100%;font-size:11px;' onClick='if(this.value!="") alert("C&Oacute;DIGO FONASA: ".unescapeHTML()+$("cod_presta2").value+"\n\nGLOSA:\n"+this.value);' />
</td>
<td>Cantidad:</td>
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

<tr id='datos_venta'><td valign='top'>

<div class='sub-content'>
<img src='iconos/money.png'>
<b>Total a Pagar</b>
</div>

<div class='sub-content'>


<table style='width:100%;' cellpadding=0 cellspacing=0><tr><td style='width:50%;'>

<center>
<b><u>Cuotas de Prestaciones Odontol&oacute;gicas</u></b><br/><br/>

<table style='width:100%;' cellpadding=1 cellspacing=0>

<tr id='cuotas1' style=''>
<td style='text-align:right;'>Nro. de Cuotas:</td>
<td><input type='text' id='cuonro' name='cuonro' 
onKeyUp='calcular_cuota();' size=5 value="3">
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





</td><td style='width:50%;'>


<table style='width:100%;' cellpadding=1 cellspacing=0>

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

<tr>
<td style='text-align:right;'>Seguros:</td>
<td colspan=4>
<input type='button' style='font-size:11px;' id='reg_seguros' name='reg_seguros' value='Registro de Seguros (0)' onClick='registro_seguros();'></td>
</tr>


</table>


</td></tr></table>


<center>
<input type='button' id='ingresa' name='ingresa'
onClick='ingresar_credito();'
value='Ingresar Recaudaci&oacute;n ... >>'>
</center>

</div>

</td></tr>

</table>

</form>

</div>
</center>

<script> 

		prestaciones=[];
		derpab=0; factor_pab=1;

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
				$('cuonro').value='1';
				$('cuonro').readonly=true;
			} else {
				$('pie').value='';
				$('pie').readonly=true;
				$('btn_forma_pago').disabled=false;			
				$('tr_pie').show();
				$('cuonro').readonly=false;
			}
			
			calcular_totales();
			calcular_cuota();
		
		
		}
					
		calcular_totales=function() {

			var suma_total=0;
			var suma=0;

			derpab=0;
			
			var prev_id=$('prev_id').value*1;
		
			if(prev_id==12) factor_pab=0;
			else if(prev_id==10) factor_pab=0;
			else if(prev_id==11) factor_pab=0.1;
			else if(prev_id==15) factor_pab=0.2;
			else factor_pab=1;
			
			for(var i=0;i<prestaciones.length;i++) {
				if(prestaciones[i].cobro!='S') continue;
				var cod=codigos[prestaciones[i].codigo];
				if(cod==undefined) continue;
				if(cod.pab!='' && cod.pab!='00') {
					//alert('DEBE CANCELAR DERECHO A PABELLON ['+cod.pab+']');
					if((cod.pab*1)>derpab)
						derpab=(cod.pab*1);
				}
				suma_total+=Math.round(prestaciones[i].precio);
				suma+=Math.round(prestaciones[i].copago);
			}
			
			if(derpab>0) {
				suma_total+=derpabs[derpab-1];
				suma+=Math.round(derpabs[derpab-1]*factor_pab);
			}

			$('valor_total').innerHTML='$ '+number_format(suma_total,0,',','.')+'.-';
			$('valor_productos').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
			$('proval').value=suma;
			//$('total_prods').innerHTML='$ '+number_format(suma,0,',','.')+'.-';
		
		}

		
		redibujar_tabla=function() {

			calcular_totales();
			calcular_cuota();		
		
			var html='<table style="width:100%;font-size:10px;"><tr class="tabla_header">';
			html+='<td style="width:5%;">Modalidad</td><td style="width:15%;">Fecha</td>';
			html+='<td style="width:10%;">C&oacute;digo FONASA</td>';
			html+='<td>Descripci&oacute;n</td>';
			html+='<td style="width:10%;">Valor</td>';
			html+='<td style="width:10%;">Copago</td>';
			html+='<td>Cobro</td>';
			html+='</tr>';
			
			var suma=0;
			
			var ver_prop=false;			
			
			for(var i=0;i<prestaciones.length;i++) {
				p=prestaciones[i];
				if(codigos[p.codigo]!=undefined) ttipo=codigos[p.codigo].tipo; else ttipo='';
				
				clase=(i%2==0)?'tabla_fila':'tabla_fila2';
				html+='<tr class="'+clase+'" style="height:15px;"';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+clase+'\';"> ';
				html+='<td style="text-align:center;font-size:10px;">'+ttipo+'</td>';
				html+='<td style="text-align:center;font-size:10px;">'+p.fecha.substr(0,16)+'</td>';
				html+='<td style="text-align:center;font-weight:bold;">'+p.codigo+'</td>';
				html+='<td style="font-size:10px;">'+p.glosa+'</td>';
				html+='<td style="text-align:right;">$'+number_format(p.precio,0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(p.copago,0,',','.')+'</td>';
				
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
				html+='<td style="text-align:center;font-size:10px;">INSTITUCIONAL</td>';
				html+='<td style="text-align:center;font-size:10px;"><?php echo date('d/m/Y H:i'); ?></td>';
				html+='<td style="text-align:center;font-weight:bold;">DP'+_derpab+'</td>';
				html+='<td style="font-size:10px;color:gray;">DERECHO A PABELL&Oacute;N - '+derpab+'</td>';
				html+='<td style="text-align:right;">$'+number_format(derpabs[derpab-1],0,',','.')+'</td>';
				html+='<td style="text-align:right;font-weight:bold;">$'+number_format(Math.round(derpabs[derpab-1]*factor_pab),0,',','.')+'</td>';
				html+='<td>&nbsp;</td></tr>';				
				
			}
			
			html+='</table>';		
		
			$('lista_productos').innerHTML=html;
						
		
		}

		guardar_cobro=function(n) {
		
			prestaciones[n].cobro=$('cobro_'+n).value;

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

        var params='tipo=prestacion&'+$('cod_presta').serialize();

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
	  $('valor_presta').value=presta[3].unescapeHTML();
	  $('cant_presta').value='1';
      $('cant_presta').select();
      $('cant_presta').focus();

    }
	  
	  
	agregar_prestacion = function() {

		var num=prestaciones.length;
		
		var valores=$('valor_presta').value.split('|');
		var prev_id=$('prev_id').value*1;
		
		if(prev_id==12) valor=0;
		else if(prev_id==10) valor=valores[1]*1;
		else if(prev_id==11) valor=valores[2]*1;
		else if(prev_id==15) valor=valores[3]*1;
		else valor=valores[0]*1;

		//alert(prev_id+'= '+valor);
		
		prestaciones[num]=new Object();
		prestaciones[num].fecha='<?php echo date('d/m/Y H:i:s'); ?>';
		prestaciones[num].codigo=$('cod_presta2').value;
		prestaciones[num].glosa=($('desc_presta').value);
		prestaciones[num].precio=valores[0]*1;
		prestaciones[num].copago=valor;
		prestaciones[num].presta_id=0;
		prestaciones[num].cobro='S';

		redibujar_tabla();

	}
	  
	  
	  
	  
    autocompletar_prestaciones = new AutoComplete(
      'cod_presta', 
      'autocompletar_sql.php',
      lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 2, seleccionar_prestacion);
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	validacion_rut($('clirut'));
	//validacion_fecha($('clifnac'));

</script>
