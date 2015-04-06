<?php 

	require_once('../../conectar_db.php');
	
?>

<html>

<title>Definir Descuentos</title>

<?php cabecera_popup('../..'); ?>

<script>

var descuentos='<?php echo utf8_decode($_GET['bolnums']); ?>';

agregar_descuento = function() {

	var tipo=$('tipo').value;
	
	if(tipo=='d') {

		if(trim($('nombre').value)=='') {
			alert('Debe ingresar motivo del descuento.');
			$('nombre').focus();
		}

		descuentos+='|d/'+$('nombre').value+'/'+($('nro').value*1);
		
		redibujar_tabla();

	} else {

		var myAjax=new Ajax.Request(
			'info_documento.php',{
				method:'post', 
				parameters: $('tipo').serialize()+'&'+$('nro').serialize(),
				onComplete: function(resp) {

					try {
					var r=resp.responseText.evalJSON(true);

					if(!r) {
					
						if(tipo=='c') {						
							alert('Documento no encontrado.');
							$('monto').style.display='none';
							$('monto').value='';
						} else {
							if( $('monto').value*1==0 ) {							
								alert('Bolet&iacute;n no existe en el sistema. Ingrese monto del bolet&iacute;n.'.unescapeHTML());
								$('nmonto2').style.display='';
								$('monto').style.display='';
								$('monto').value='';
								$('monto').focus();
							} else {
								descuentos+='|bn/'+($(nro).value*1)+'/'+($('monto').value*1);
								$('nmonto2').style.display='none';
								$('monto').style.display='none';
								$('monto').value='';
								redibujar_tabla();
								$('nro').select();									
								$('nro').focus();
							}
						}

					} else {

						$('monto').style.display='none';
						$('monto').value='';
							
						if(tipo=='b') {
						
							if(r.bolnumx!=null) {
								alert( ('Documento fu&eacute; previamente anulado en bolet&iacute;n #'+r.bolnumx+' .').unescapeHTML() );
								return;
							}
							
							if(r.crecod*1!=0) {
								alert( ('El bolet&iacute;n es parte del cr&eacute;dito #'+(r.crecod*1)+'. Solamente se puede anular el cr&eacute;dito completo, no el bolet&iacute;n solo.').unescapeHTML() );
								return;
							}
							
							descuentos+='|b/'+r.bolnum+'/'+r.bolmon;
						} else
							descuentos+='|c/'+r.crecod+'/'+r.pagado;
							
						redibujar_tabla();

					}
					
					} catch(err) {
						alert(err);
					}
					
				}
			}		
		);

	}
}

var totald=0;

function eliminar(n) {

	var l=descuentos.split('|');
	
	descuentos='';	
	
	for(var i=1;i<l.length;i++) {
		if(i!=n) {
			descuentos+='|'+l[i];
		}
	}
	
	redibujar_tabla();

}

function redibujar_tabla() {

	var l=descuentos.split('|');

	var html='<table style="width:100%;"><tr class="tabla_header"><td>Documento</td><td>N&uacute;mero</td><td>Monto</td><td>Eliminar</td></tr>';

	totald=0;
	
	for(var i=1;i<l.length;i++) {
	
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
		
		reg=l[i].split('/');
		html+='<tr class="'+clase+'"><td>';

		if(reg[0]=='b') html+='Bolet&iacute;n';
		if(reg[0]=='bn') html+='Bolet&iacute;n Nuevo';
		if(reg[0]=='c') html+='Cr&eacute;dito';
		if(reg[0]=='d') html+='Descuento';
		
		if(reg[0]!='d') 
			html+='</td><td style="text-align:center;">'+number_format(reg[1],0,',','.')+'</td>';
		else
			html+='</td><td style="text-align:center;"><i>'+reg[1]+'</i></td>';

		html+='<td style="text-align:right;font-weight:bold;">$'+number_format(reg[2],0,',','.')+'.-</td>';
		html+='<td><center><img src="../../iconos/delete.png" onClick="eliminar('+i+');"></center></td>';		
		html+='</td></tr>';
		
		totald+=reg[2]*1;
		
	}
	
	html+='</table>';
	
	$('listado').innerHTML=html;
	
	$('total').innerHTML='$ '+number_format(totald,0,',','.')+'.-';
}

function aceptar() {

	window.opener.$('bolnums').value=descuentos;
	window.opener.$('total_descuento').value=totald;
	fn=window.opener.calcular_cuota.bind(window.opener);
	fn();
	window.close();

}

</script>

<body class='fuente_por_defecto popup_background'>


<div class='sub-content'>
<img src='../../iconos/coins.png'>
<b>Definici&oacute;n de Descuentos y Cambios de Producto</b>
</div>

<div class='sub-content'>
<table style='width:100%;font-size:11px;'>
<tr><td style='text-align:right;'>Tipo:</td><td>

<table cellpadding=0 cellspacing=0><tr>
<td>
<select id='tipo' name='tipo' onChange="

	if(this.value=='d') {
		$('nombre').value='';
		$('ndesc').style.display='';
		$('nmonto').style.display='';
		$('nnombre').style.display='';
	} else {
		$('ndesc').style.display='none';	
		$('nmonto').style.display='none';
		$('nnombre').style.display='none';	
	}
	
	$('nro').value='';

">
<option value='b'>Bolet&iacute;n</option>
<option value='c'>Cr&eacute;dito</option>
<option value='d'>Descuento</option>
</select>

</td>
<td id='ndesc'  style='display:none;'>
Desc.:
</td><td id='nnombre'  style='display:none;'>
<input type='text' id='nombre' name='nombre' size=20>
</td><td id='nmonto'  style='display:none;'>
Monto.:
</td><td id='nnro'>
<input type='text' id='nro' name='nro' size=10 style='text-align:center;'>
</td><td id='nmonto2'  style='display:none;'>
Monto.:
</td><td>
<input type='text' id='monto' name='monto' size=10 style='display:none;text-align:right;'>
</td><td>
<input type='button' value='Agregar...' onClick='agregar_descuento();'>
</td></tr></table>


</td></tr>
</table>
</div>

<div class='sub-content2' id='listado' style='height:290px;'>

</div>

<div style='font-size:24px;text-align:center;'>
Total Descuentos:<br />
<span id='total'>$0.-</span></div>
<center>
<input type='button' id='acepta' onClick='aceptar();' 
value='- Aceptar Informaci&oacute;n de Descuento -'>
</center>
</body>

</html>

<script> redibujar_tabla(); </script>