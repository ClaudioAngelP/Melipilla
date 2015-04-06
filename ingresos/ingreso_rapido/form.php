<?php 

	require_once('../../conectar_db.php');

	$clases=cargar_registros_obj("SELECT DISTINCT tsep_clase, tsep_estructura, tsep_vigencia FROM tipo_sepultura LEFT JOIN inventario ON tsep_clase=sep_clase ORDER BY tsep_clase");
	$codigos=cargar_registros_obj("SELECT DISTINCT sep_codigo, sep_clase FROM inventario");

	for($i=0;$i<sizeof($clases);$i++) {
		$clase[$i][0]=htmlentities($clases[$i]['tsep_clase']);
		$clase[$i][1]=htmlentities($clases[$i]['tsep_estructura']);
		$clase[$i][2]=($clases[$i]['tsep_vigencia'])*1;
	}

	for($i=0;$i<sizeof($codigos);$i++) {
		$codigo[$i][0]=htmlentities($codigos[$i]['sep_codigo']);
		$codigo[$i][1]=htmlentities($codigos[$i]['sep_clase']);
	}


?>

<script>

var suma_anios=true;

listar_sepulturas=function() {

	var myAjax=new Ajax.Updater(
	'listado','administracion/distribucion/listado_sepulturas.php',{
		method:'post',evalScripts:true,parameters: $('filtro').serialize()	
	}	
	);

}

clases=<?php echo json_encode($clase); ?>;
codigos=<?php echo json_encode($codigo); ?>;

select_clases=function() {

	var html='<select id="sel_clases" name="sel_clases" onChange="select_codigos(); select_ubicaciones();">';
	
	for(var i=-1;i<clases.length;i++) {
		var sel='';
		if(i==-1)
			html+='<option value="-1" '+sel+'>(Seleccione Clase...)</option>';
		else 
			html+='<option value="'+clases[i][0]+'" '+sel+'>'+clases[i][0]+'</option>';
	}

	html+='</select>';

	$('select_clases').innerHTML=html;
	
	select_codigos();
	

}

select_codigos=function() {

	var val=$('sel_clases').value;

	if(val!='-1')
		for(var i=0;i<clases.length;i++) {		
			if(clases[i][0].unescapeHTML()==val) {

				if(clases[i][2]*1!=0) {

					if(clases[i][2]*1>0) {					
						$('anios').value=clases[i][2];
						suma_anios=true;
					} else {
						$('anios').value=(-clases[i][2])+' m';
						suma_anios=false;
					}			
							
				} else {

					$('anios').value='';					

				}	

				break;	

			}
		}

	if(val!='-1') 
		for(var i=0;i<clases.length;i++) {
			if(clases[i][0].unescapeHTML()==val) {
				$('tsep_estructura').value=clases[i][1].unescapeHTML();
				break;
			}
		}
		
	
	var html='<select id="sel_codigos" name="sel_codigos" onChange="select_ubicaciones(); if(this.value==\'-1\') $(\'sel_codigon\').style.display=\'\'; else $(\'sel_codigon\').style.display=\'none\';">';
	
	var estilo='display:none;';	
	
	for(var i=-2;i<codigos.length;i++) {
		var sel='';
		if(i==-2)
			html+='<option value="-2" '+sel+'>(Seleccione C&oacute;digo...)</option>';
		else if(i==-1 && val!=-1) {
			html+='<option value="-1" '+sel+'>(C&oacute;digo Nuevo...)</option>';
			//estilo='';
		} else if(i>=0) { 
			if(codigos[i][1]!=val) continue;
			html+='<option value="'+codigos[i][0]+'" '+sel+'>'+codigos[i][0]+'</option>';
		}
	}

	html+='</select>';

	html+='&nbsp;<input type="text" id="sel_codigon" name="sel_codigon" size=10 value="" style="'+estilo+'">';

	$('select_codigos').innerHTML=html;

	//listar_sepulturas();

}

select_ubicaciones=function() {

	if($('sepulturas')==null || $('otrasep').checked) {

		if($('sel_clases').value*1==-1) {
			$('select_ubicaciones').innerHTML='<select id="sel_ubicaciones" name="sel_ubicaciones"><option value=-1>(Seleccione la Clase...)</option></select>';
			return;	
		}
	
		if($('sel_codigos').value=='-2') {
			$('select_ubicaciones').innerHTML='<select id="sel_ubicaciones" name="sel_ubicaciones"><option value=-1>(Seleccione el C&oacute;digo...)</option></select>';
			return;
		}
	
		if($('sel_codigos').value=='-1' && trim($('sel_codigon').value)=='') {
			$('select_ubicaciones').innerHTML='<select id="sel_ubicaciones" name="sel_ubicaciones"><option value=-1>(Seleccione el C&oacute;digo...)</option></select>';
			return;
		}
	
		if($('numero').value*1==0) {
			$('select_ubicaciones').innerHTML='<select id="sel_ubicaciones" name="sel_ubicaciones"><option value=-1>(Seleccione la Numeraci&oacute;n...)</option></select>';
			return;
		}
	
	} 	
	
	$('select_ubicaciones').innerHTML='<input type="button" style="font-size:11px;" onclick="chequear_ubicaciones();" value="Chequear Ubicaciones Disponibles...">';
	
}

chequear_ubicaciones=function() {

	var params='clase='+encodeURIComponent($('sel_clases').value)+'&';
		
	if($('sel_codigos').value!='-1')	
		params+='codigo='+encodeURIComponent($('sel_codigos').value)+'&';
	else
		params+='codigo='+encodeURIComponent($('sel_codigon').value)+'&';
			
	params+='numero='+encodeURIComponent($('numero').value)+'&';	
	params+='letra='+encodeURIComponent($('letra').value);
	
	
	$('select_ubicaciones').innerHTML='<img src="imagenes/ajax-loader1.gif"> Espere un momento...';	
	
	var myAjax=new Ajax.Updater(
		'select_ubicaciones',
		'ingresos/ubicaciones_sepultura.php',
		{
			method: 'post',
			parameters: params+'&'+$('estado').serialize() 	
		}	
	);
	
}	



guardar_registro=function() {

	if(	$('sel_clases').value*1==-1 || 
			$('sel_codigos').value*1==-2 ||
			$('numero').value*1==0) {
		alert( 'La ubicaci&oacute;n ingresada no es v&aacute;lida.'.unescapeHTML() );
		return;	
	}

	if($('sel_codigos').value=='-1' && trim($('sel_codigon').value)=='') {
		alert( "Debe especificar el c&oacute;digo de la sepultura.".unescapeHTML() );
		return;
	}
	
	if($('sel_ubicaciones')==null || $('sel_ubicaciones').value*1==-1) {
		alert( "Debe seleccionar una ubicaci&oacute;n v&aacute;lida dentro de la sepultura.".unescapeHTML() );
		return;		
	}

	if(!validacion_fecha($('fechasep'))) {
		alert( 'Debe ingresar la fecha de sepultaci&oacute;n.'.unescapeHTML() );
		return;
	}
	
	if(trim($('nombre').value)=='') {
		alert( 'Debe ingresar el nombre del sepultado.'.unescapeHTML() );
		return;		
	}

	var myAjax=new Ajax.Request('ingresos/ingreso_rapido/sql_ingreso.php',{
		method:'post', parameters: $('filtro').serialize(),
		onComplete: function(resp) {
		
			if(resp.responseText=='') {
				alert('Registro guardado exitosamente.');
				limpiar_formulario();
				//volver();
			} else {
				alert(resp.responseText);
			}

		}	
	});

}

limpiar_formulario=function() {

	//select_clases();
	//$('sel_codigos').value=-2;
	$('numero').value='';
	$('letra').value='';
		 

	$('bolnum').value='';
	$('rut').value='';
	$('nombre').value='';
	$('referencias').value='';
	$('fechasep').value='';
	$('vence').value='';
	$('estado').value=0;

	$('bolnum2').value='';
	$('crecod').value=0;
	$('datos_boletin').innerHTML='(Sin Bloqueos Asociados...)';

	validacion_rut($('rut'));
	validacion_fecha($('fechasep'));
	validacion_fecha($('vence'));
	
	$('numero').focus(); 

	
}

cargar_boletin=function() {

	$('bolnum2').value=$('bolnum2').value*1;
	
	if($('bolnum2').value=='0') {
		$('bolnum2').value='';
		$('datos_boletin').innerHTML='(Sin Bloqueos Asociados...)';
		$('crecod').value=0;
		return;
	}

	$('guardar_reg').disabled=true;

	var myAjax=new Ajax.Request(
	'ingresos/info_boletin.php',
	{
		method: 'get',
		parameters: 'bolnum='+($('bolnum2').value*1),
		onComplete: function(resp) {
			
			$('guardar_reg').disabled=false;			
			
			var r=resp.responseText.evalJSON(true);
			
			if(!r) {
				$('datos_boletin').innerHTML='No hay registros del bolet&iacute;n en el sistema.';
				$('crecod').value=0;
				return;	
			}			
			
			if(r.crecod!=0) {
				$('datos_boletin').innerHTML='Asociado a Cr&eacute;dito <b>#'+r.crecod+'</b> <img src="iconos/magnifier.png" style="width:12px;height:12px;cursor:pointer;" onClick="abrir_credito('+r.crecod+');" />';
				$('crecod').value=r.crecod;	
			} else {
				$('datos_boletin').innerHTML='Bolet&iacite;n sin cr&eacute;dito asociado.';
				$('crecod').value=0;
			}
		}	

	});
	
}

var lista_estados='Entero,Reducido,Anfora,Eliminar...';

eselect=function(v) {

	var html='';
	var estados=lista_estados.split(',');
	
	for(var i=0;i<estados.length;i++)
		html+='<option value="'+i+'" '+(i==v?'SELECTED':'')+'>'+estados[i]+'</option>';

	return html;

}

tselect=function(v) {

	var html='';
	var estados=lista_sepulturas.split(',');
	
	for(var i=0;i<estados.length;i++) {
		var sel=trim(estados[i].unescapeHTML())==v.unescapeHTML()?'SELECTED':'';
		html+='<option value="'+trim(estados[i])+'" '+(sel)+'>'+trim(estados[i])+'</option>';
	}

	return html;

}

sumar_anios=function() {

	var t=$('anios').value;

	if(suma_anios) {
		var a=$('anios').value*1;
	} else {
		var a=$('anios').value.split(' ');
		a=a[0]*1;
	}
	
	if(a==0) {
		$('vence').value='';
		validacion_fecha($('vence'));
		return;
	}
	
	var fec=$('fechasep').value.split('/');
	
	if(suma_anios) {
		$('vence').value=fec[0]+'/'+fec[1]+'/'+((fec[2]*1)+a);
	} else {
		
		fec[1]=(fec[1]*1)+a;
		
		if(fec[1]>12) {
			fec[1]-=12; fec[2]++;	
		}
		
		$('vence').value=fec[0]+'/'+(fec[1]<10?'0':'')+fec[1]+'/'+((fec[2]*1));
		
	}

	validacion_fecha($('vence'));
	
}


</script>


<center>

<form id='filtro' name='filtro' onSubmit='return false;'>
<input type='hidden' id='tsep_estructura' name='tsep_estructura'>
<input type='hidden' id='sepultura' name='sepultura' value=''>
<input type='hidden' id='crecod' name='crecod' value='0'>

<div class="sub-content" style='width:750px;'>


<div class="sub-content">
<img src='iconos/map_go.png'>
<b>Ingreso R&aacute;pido de Registro de Sepulturas</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr>
<td style='text-align:right;'>
Sepultura:
</td>
<td>
<table cellpadding=0 cellspacing=0><tr><td>
<span id='select_clases' style='width:150px;'></span>
</td>
<td style='text-align:right;'>&nbsp;&nbsp;A&ntilde;os de Vigencia:</td>
<td>
	<input type='text' id='anios' name='anios' DISABLED 
	style='text-align:center;' value='' size=3 />
</td>
</tr></table>

</td>
</tr>

<tr>
<td style='text-align:right;'>
C&oacute;digo:
</td>
<td>
<span id='select_codigos' style='width:50px;'></span>
</td>
</tr>



<tr>
<td style='text-align:right;'>N&uacute;mero:</td>
<td><input type='text' style='text-align:center;' 
id='numero' name='numero' size=5 onBlur='select_ubicaciones();'><input type='text' style='text-align:center;' 
id='letra' name='letra' size=5 onBlur='select_ubicaciones();'>
</td>
</tr>

<tr>
<td style='text-align:right'>Bolet&iacute;n:</td>
<td><input type='text' id='bolnum' name='bolnum' style='text-align:center;' size=10></td>
</tr>

<tr>
<td style='text-align:right'>Fecha de Sep.:</td>
<td>

	<input type='text' id='fechasep' name='fechasep' size=10
 	onBlur='if(validacion_fecha(this)) sumar_anios();' style='text-align:center;' />

</td>
</tr>

<tr>
<td style='text-align:right'>R.U.T.:</td>
<td><input type='text' id='rut' name='rut' size=10
onKeyUp='validacion_rut(this);' style='text-align:center;' ></td>
</tr>

<tr>
<td style='text-align:right'>Nombre Completo:</td>
<td><input type='text' id='nombre' name='nombre' size=25></td>
</tr>

<tr>
<td style='text-align:right'>Referencias:</td>
<td><input type='text' id='referencias' name='referencias' size=35></td>
</tr>

<tr>
<td style='text-align:right'>Vencimiento:</td>
<td><input type='text' id='vence' name='vence' size=10
onBlur='validacion_fecha(this);' style='text-align:center;' ></td>
</tr>

<tr>
<td style='text-align:right'>Bolet&iacute;n Bloqueo:</td>
<td>

<table cellpadding=0 cellspacing=0><tr><td>
<input type='text' id='bolnum2' name='bolnum2' size=10
style='text-align:center;' onBlur='cargar_boletin();' 
onKeyPress='if(event.which==13) cargar_boletin();' />
</td><td>&nbsp;&nbsp;</td><td id='datos_boletin'>
(Sin Bloqueos Asociados...)
</td></tr></table>

</td>
</tr>


<tr>
<td style='text-align:right'>Estado:</td>
<td><select id='estado' name='estado' onChange='select_ubicaciones();'>
<option value='0' SELECTED>Entero</option>
<option value='1'>Reducido</option>
<option value='2'>Anfora</option>
</select></td>
</tr>


<tr style=''>
<td style='text-align:right;width:25%;'>Ubicaci&oacute;n:</td>
<td id='select_ubicaciones'>

</td>
</tr>	


<tr><td colspan=2 style='text-align:center;'>
<br /><br />
<input type='button' id='guardar_reg' name='guardar_reg' 
value='-- Guardar Registro... --' onClick='guardar_registro();' />
<input type='button' id='limpiar_form' name='limpiar_form' 
value='-- Limpiar Formulario... --' onClick='limpiar_formulario();' />
</td></tr>

</table>
</div>


</div>

</form>

<script> 

	select_clases(); 

	validacion_rut($('rut'));
	validacion_fecha($('fechasep'));
	validacion_fecha($('vence'));
	
	$('sel_clases').focus(); 
	
</script>
