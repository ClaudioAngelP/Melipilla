<?php 

	require_once('../conectar_db.php');

	$bancos=explode("\n",trim(file_get_contents('bancos.list')));
	
	$bcos=array();
	
	for($i=0;$i<sizeof($bancos);$i++) {
		if(trim($bancos[$i])=='') continue;
		$bcos[]=htmlentities($bancos[$i]);
	}
	
	sort($bcos);

	$fp=cargar_registros_obj("SELECT * FROM tipo_formas_pago ORDER BY fpago_id;");

	$f=array();

	for($i=0;$i<sizeof($fp);$i++) {
		$f[$i]=array($fp[$i]['fpago_id']*1,htmlentities($fp[$i]['fpago_nombre']) );
	}

	$total=$_GET['total']*1;
	
?>

<html>
<title>Establecer Forma de Pago</title>

<?php cabecera_popup('..'); ?>

<style>

.cheq {
	font-size:11px;
	width:100%;
}

</style>

<script>


	function validacion_rut(obj) {

		obj.value=trim(obj.value);

		if( !comprobar_rut(obj.value) ) {
			obj.style.background='red';
			return false;
		} else {
			obj.style.background='yellowgreen';
			return true;	
		}

	}

	pago=new Object();
	
	lista_bancos=<?php echo json_encode($bcos); ?>;	
	
	function init() {
	
		p=window.opener.pago;
		
		if(p=='') {
		
			$('efectivo').value=<?php echo $total; ?>;
			$('nro_cheques').value=0;
			pago=new Object();
			pago.total=<?php echo $total; ?>;
			pago.efectivo=<?php echo $total; ?>;
			pago.cheques=[];
			pago.nro_cheques=0;
			pago.total_cheques=0;
			pago.nro_otras=0;
			pago.otras=[];
			pago.total_otras=0;

		} else {

			pago=p;
			$('efectivo').value=pago.efectivo;
			$('nro_cheques').value=pago.nro_cheques;
			$('nro_otras').value=pago.nro_otras;
			calcular_totales(0);		

		}

		$('efectivo').select(); $('efectivo').focus();
		
	}

	
	function valin(v) {
		
		try {
			return $(v).value;
		} catch(err) {
			return '';
		}	
	
	}	
	
	/*
	tipo_otras=[[0,'Dep&oacute;sito Bancario'],
					[1,'Tarjeta Cr&eacute;dito'],
					[2,'Tarjeta D&eacute;bito']];	
	*/
	
	tipo_otras=<?php echo json_encode($f); ?>;
	
	function combo_tipo(vsel) {
		
		var html='';	
	
		for(var i=0;i<tipo_otras.length;i++) {
			var sel=(tipo_otras[i][0]==vsel)?'SELECTED':'';
			html+='<option value="'+tipo_otras[i][0]+'" '+sel+'>'+tipo_otras[i][1]+'</option>';
		}	
		
		return html;
	
	}
	
	function combo_bancos(vsel) {
		
		var html=''; var sel='';
		
		for(i=0;i<lista_bancos.length;i++) {
			
			if(lista_bancos[i]==vsel)
				sel='SELECTED'; else sel='';

			html+='<option value="'+lista_bancos[i]+'" '+sel+'>'+lista_bancos[i]+'</option>';	

		}
		
		return html;
		
	}
		
	function calcular_totales(refresh) {
	
		var nro_cheques=$('nro_cheques').value*1;
		var nro_otras=$('nro_otras').value*1;
	
		pago.nro_cheques=nro_cheques;
		pago.nro_otras=nro_otras;			
	
		var htmlcheques='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
		htmlcheques+='<td>Banco</td>';
		htmlcheques+='<td>R.U.T.</td>';
		htmlcheques+='<td style="width:30%;">Nombre</td>';
		htmlcheques+='<td>Fecha</td>';
		htmlcheques+='<td>Serie</td>';
		htmlcheques+='<td>Monto</td>';
		htmlcheques+='</tr>';	
	
		for(var i=0;i<nro_cheques;i++) {
		
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';		

			if(refresh) {
				pago.cheques[i]=new Object();
				pago.cheques[i].banco=valin('banco_chq_'+i);
				pago.cheques[i].rut=valin('rut_chq_'+i);
				pago.cheques[i].nombre=valin('nombre_chq_'+i);
				pago.cheques[i].fecha=valin('fecha_chq_'+i);
				pago.cheques[i].serie=valin('serie_chq_'+i);
				pago.cheques[i].monto=valin('monto_chq_'+i);
			}
			//pago.cheques[i].rut=$('rut_chq_'+i).value;
					
			c=pago.cheques[i];
		
			htmlcheques+='<tr class="'+clase+'">';
			htmlcheques+='<td>';
			htmlcheques+='<select id="banco_chq_'+i+'" style="text-align:left;" ';
			htmlcheques+='name="banco_chq_'+i+'" class="cheq">';
			htmlcheques+=combo_bancos(c.banco);
			htmlcheques+='</select></td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="rut_chq_'+i+'" style="text-align:right;" ';
			htmlcheques+='name="rut_chq_'+i+'" class="cheq" value="'+c.rut+'" onKeyUp="validacion_rut(this);">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="nombre_chq_'+i+'" ';
			htmlcheques+='name="nombre_chq_'+i+'" class="cheq" value="'+c.nombre+'">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="fecha_chq_'+i+'" style="text-align:center;" onBlur="validacion_fecha(this);" ';
			htmlcheques+='name="fecha_chq_'+i+'" class="cheq" value="'+c.fecha+'">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="serie_chq_'+i+'" style="text-align:center;" ';
			htmlcheques+='name="serie_chq_'+i+'" class="cheq" value="'+c.serie+'">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="monto_chq_'+i+'" style="text-align:right;" ';
			htmlcheques+='name="monto_chq_'+i+'" class="cheq"  value="'+c.monto+'" ';
			htmlcheques+='onKeyUp="actualizar_cheques();">';
			htmlcheques+='</td>';
			htmlcheques+='</tr>';		
		
		}
		
		htmlcheques+='</table>';
		
		$('lista_cheque').innerHTML=htmlcheques;

		for(var i=0;i<nro_cheques;i++) {
			validacion_rut($('rut_chq_'+i));
			validacion_fecha($('fecha_chq_'+i));
		}
		
		var htmlcheques='<table style="width:100%;font-size:11px;"><tr class="tabla_header">';
		htmlcheques+='<td>Tipo</td>';
		htmlcheques+='<td>N&uacute;mero</td>';
		htmlcheques+='<td>Fecha</td>';
		htmlcheques+='<td>Monto</td>';
		htmlcheques+='</tr>';	
	
		for(var i=0;i<nro_otras;i++) {
		
			var clase=(i%2==0)?'tabla_fila':'tabla_fila2';		

			if(refresh) {
				pago.otras[i]=new Object();
				pago.otras[i].tipo=valin('tipo_otr_'+i);
				pago.otras[i].numero=valin('numero_otr_'+i);
				pago.otras[i].fecha=valin('fecha_otr_'+i);
				pago.otras[i].monto=valin('monto_otr_'+i);
			}
					
			c=pago.otras[i];
		
			htmlcheques+='<tr class="'+clase+'">';
			htmlcheques+='<td>';
			htmlcheques+='<select id="tipo_otr_'+i+'" style="text-align:left;" ';
			htmlcheques+='name="tipo_otr_'+i+'" class="cheq">';
			htmlcheques+=combo_tipo(c.tipo);
			htmlcheques+='</select></td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="numero_otr_'+i+'" style="text-align:right;" ';
			htmlcheques+='name="numero_otr_'+i+'" class="cheq" value="'+c.numero+'">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="fecha_otr_'+i+'" style="text-align:center;" onBlur="validacion_fecha(this);" ';
			htmlcheques+='name="fecha_otr_'+i+'" class="cheq" value="'+c.fecha+'">';
			htmlcheques+='</td>';
			htmlcheques+='<td>';
			htmlcheques+='<input type="text" id="monto_otr_'+i+'" style="text-align:right;" ';
			htmlcheques+='name="monto_otr_'+i+'" class="cheq"  value="'+c.monto+'" ';
			htmlcheques+='onKeyUp="actualizar_otras();">';
			htmlcheques+='</td>';
			htmlcheques+='</tr>';		
		
		}
		
		htmlcheques+='</table>';
		
		$('lista_otras').innerHTML=htmlcheques;

		for(var i=0;i<nro_otras;i++) {
			validacion_fecha($('fecha_otr_'+i));			
		}

		actualizar_cheques();
		actualizar_otras();
	
	}

	function actualizar_cheques() {
		
		var nro_cheques=$('nro_cheques').value*1;
		
		var ctotal=0;		
		
		for(var i=0;i<nro_cheques;i++) {
			pago.cheques[i].banco=valin('banco_chq_'+i);
			pago.cheques[i].rut=valin('rut_chq_'+i);
			pago.cheques[i].nombre=valin('nombre_chq_'+i);
			pago.cheques[i].fecha=valin('fecha_chq_'+i);
			pago.cheques[i].serie=valin('serie_chq_'+i);
			pago.cheques[i].monto=valin('monto_chq_'+i);		
			ctotal+=valin('monto_chq_'+i)*1;
		}	

		pago.total_cheques=ctotal;

		$('total_cheque').innerHTML='$'+number_format(ctotal,0,',','.')+'.-';
		
		chequear_totales();
	
	}

	function actualizar_otras() {
		
		var nro_otras=$('nro_otras').value*1;
		
		var ctotal=0;		
		
		for(var i=0;i<nro_otras;i++) {
			pago.otras[i].tipo=valin('tipo_otr_'+i);
			pago.otras[i].numero=valin('numero_otr_'+i);
			pago.otras[i].fecha=valin('fecha_otr_'+i);
			pago.otras[i].monto=valin('monto_otr_'+i);		
			ctotal+=valin('monto_otr_'+i)*1;
		}	

		pago.total_otras=ctotal;

		$('total_otras').innerHTML='$'+number_format(ctotal,0,',','.')+'.-';
		
		chequear_totales();
	
	}


	function chequear_totales() {

		var pagando=(pago.efectivo+pago.total_cheques+pago.total_otras);
		var dif=pago.total-pagando;

		if(dif!=0) {
			var signo=(dif>=0)?'-':'+';
			$('chequeo').innerHTML='<table cellpadding=0 cellspacing=0><tr><td><img src="../iconos/cancel.png" /></td><td>No coinciden los totales.</td><td>(Diferencia '+signo+'$'+number_format(Math.abs(dif),0,',','.')+'.-)</td></tr></table>';
			$('confirmar').disabled=true;
		} else {
			$('chequeo').innerHTML='<img src="../iconos/accept.png" />';
			$('confirmar').disabled=false;		
		}

	}
	
	function confirmar_pago() {
	
		actualizar_cheques();
		actualizar_otras();
		
		var nro_cheques=$('nro_cheques').value*1;		
		
		for(var i=0;i<nro_cheques;i++) {
			
			if(!validacion_rut($('rut_chq_'+i))) {
				alert("El RUT de alg&uacute;n(os) cheque(s) no es v&aacute;lido.".unescapeHTML());
				return;
			}
			
			if(!validacion_fecha($('fecha_chq_'+i)) ) {
				alert("La fecha de alg&uacute;n(os) cheque(s) no es v&aacute;lido.".unescapeHTML());
				return;			
			}			
			
			if($('banco_chq_'+i).value=='' ||
				$('nombre_chq_'+i).value=='' ||
				$('serie_chq_'+i).value=='' ||
				$('monto_chq_'+i).value*1==0) {
				alert("Los dato(s) de algun(os) cheque(s) est&aacute; vac&iacute;os.".unescapeHTML());
				return;
			}			
			
		}		
		
		window.opener.pago=pago;
		window.opener.$('pago_efectivo').value=pago.efectivo;
		var ic=''; var io='';

		for(i=0;i<nro_cheques;i++) {
			c=pago.cheques[i];
			ic+=c.rut+'|'+c.nombre+'|'+c.fecha+'|'+c.serie+'|'+c.monto+'|'+c.banco+'[|]';
		}

		for(i=0;i<pago.otras.length;i++) {
			c=pago.otras[i];
			io+=c.tipo+'|'+c.numero+'|'+c.fecha+'|'+c.monto+'[|]';
		}

		window.opener.$('pago_cheques').value=ic;
		window.opener.$('pago_otras').value=io;
		
		window.close();	
	
	}

</script>

<body class='popup_background fuente_por_defecto'
onLoad='init();'>

<div class='sub-content'>
<img src='../iconos/money.png' />
<b>Establecer Forma de Pago</b>
</div>

<div class='sub-content'>

<form id='forma_pago' name='forma_pago' 
onChange='calcular_totales();' onSubmit='return false;'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:100px;'>Total:</td>
<td style='font-weight:bold;text-align:right;color:green;font-size:16px;width:150px;'>
$<?php echo number_format($total,0,',','.'); ?>.-
</td><td id='chequeo'></td>
</tr>

<tr>
<td style='text-align:right;'>Efectivo:</td>
<td>
<input type='text' id='efectivo' name='efectivo' style='width:100%;text-align:right;'
onKeyUp='pago.efectivo=this.value*1; chequear_totales();' 
value='0'>
</td>
</tr>

</table>

</form>

</div>

<div class='sub-content'>
<table style='width:100%;'><tr><td>
<img src='../iconos/vcard_edit.png' />
</td><td style='width:40%;'>
<b>Pago con Cheques</b>
</td><td>Cantidad:</td><td>
<input type='text' id='nro_cheques' 
name='nro_cheques' size=5 onKeyUp='calcular_totales(1);'
style='text-align:center;'
value='0'>
</td><td>Total:</td>
<td id='total_cheque' 
style='width:100px;text-align:right;font-size:16px;font-weight:bold;color:green;'>$0.-</td>
</tr></table>
</div>

<div class='sub-content2' style='height:110px;overflow:auto;' id='lista_cheque'>

</div>

<div class='sub-content'>
<table style='width:100%;'><tr><td>
<img src='../iconos/creditcards.png' />
</td><td style='width:40%;'>
<b>Otras Formas de Pago</b>
</td><td>Cantidad:</td><td>
<input type='text' id='nro_otras'
name='nro_otras' size=5 onKeyUp='calcular_totales(true);'
style='text-align:center;'
value='0'>
</td><td>Total:</td>
<td id='total_otras' 
style='width:100px;text-align:right;font-size:16px;font-weight:bold;color:green;'>$0.-</td>
</tr></table>
</div>
<div class='sub-content2' style='height:110px;overflow:auto;' id='lista_otras'>

</div>

<center>
<input type='button' onClick='confirmar_pago();' id='confirmar' 
value='--- Confirmar Forma de Pago... ---'>
</center>

</body>
</html>





