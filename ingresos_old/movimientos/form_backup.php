<?php 

	require_once('../../conectar_db.php');

	$clases=cargar_registros_obj("SELECT DISTINCT tsep_clase, tsep_estructura FROM tipo_sepultura LEFT JOIN inventario ON tsep_clase=sep_clase ORDER BY tsep_clase");
	$codigos=cargar_registros_obj("SELECT DISTINCT sep_codigo, sep_clase FROM inventario");

	for($i=0;$i<sizeof($clases);$i++) {
		$clase[$i][0]=htmlentities($clases[$i]['tsep_clase']);
		$clase[$i][1]=htmlentities($clases[$i]['tsep_estructura']);
	}

	for($i=0;$i<sizeof($codigos);$i++) {
		$codigo[$i][0]=htmlentities($codigos[$i]['sep_codigo']);
		$codigo[$i][1]=htmlentities($codigos[$i]['sep_clase']);
	}


?>

<script>

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

	var html='<select id="sel_clases" name="sel_clases" onClick="select_codigos();">';
	
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

	if(val!=-1) {
	
		for(var i=0;i<clases.length;i++) {
			if(clases[i][0]==val) {
				$('tsep_estructura').value=clases[i][1].unescapeHTML();
				break;
			}
		}
		
	}
	
	var html='<select id="sel_codigos" name="sel_codigos" onChange="listar_sepulturas(); if(this.value==\'-1\') $(\'sel_codigon\').style.display=\'\'; else $(\'sel_codigon\').style.display=\'none\';">';
	
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

cargar_sep=function() {

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

	if($('listado').style.display=='none') {

		if($('sel_codigos').value*1!=-1)
			$('sepultura').value=$('sel_clases').value+'|'+$('sel_codigos').value+'|'+$('numero').value;
		else {
			$('sel_codigon').value=trim($('sel_codigon').value);
			$('sepultura').value=$('sel_clases').value+'|'+$('sel_codigon').value+'|'+$('numero').value;
		}

		realizar_carga_sep();						
	
	} else {

		$('selsep').style.display='';
		$('listado').style.display='none';
		$('sel_clases').disabled=false;
		$('sel_codigos').disabled=false;
		$('sel_codigon').disabled=false;
		$('numero').disabled=false;
		$('cargar_sepultura').value='Accesar Sepultura...';
	
	}

}

realizar_carga_sep = function() {

		var myAjax=new Ajax.Updater(
			'listado',
			'ingresos/movimientos/datos_sepultura.php',
			{
				method:'post',
				parameters:$('filtro').serialize(),
				evalScripts: true,
				onComplete:function() {
					try {
						$('selsep').style.display='none';
						$('listado').style.display='';
						$('sel_clases').disabled=true;
						$('sel_codigos').disabled=true;
						$('sel_codigon').disabled=true;
						$('numero').disabled=true;
						$('cargar_sepultura').value='Volver Atr&aacute;s...'.unescapeHTML();
						$('listado').scrollTop=0;
					} catch(err) {
						alert(err);
					}
				}		
			}
				
		);

}


//////////////////////////////////////////////////////////////////

var bloquear=0;

cargar_boletin = function() {

	var myAjax=new Ajax.Request(
		'ingresos/info_boletin.php', {
			method:'get',
			parameters:$('bolnum').serialize(),
			onComplete: function(resp) {
				r=resp.responseText.evalJSON(true);
				
				if(r) {
					
					try {

						$('bolmon').value=r.bolmon;
						$('fecha1').value=r.bolfec;
						$('bolobs').value=r.bolobs;
						$('crecod').value=r.crecod;
						$('cretot').value=(r.cretot*1)+(r.crepie*1);
						
						if(r.clirut != null && r.clidv != null ) {
							$('clirut').value=r.clirut+'-'+r.clidv;
							validar_rut();
						}
					
					} catch(err) {
						alert(err);
					}
					
				}
				
			}
		}	
	);

}

validar_rut = function() {

	if(bloquear) return;

	bloquear=1;

	var myAjax=new Ajax.Request('ingresos/info_cliente.php',
	{
		method:'get',
		parameters: $('clirut').serialize(),
		onComplete: function(resp) {
			d=resp.responseText.evalJSON(true);
			
			if(d) {

				$('clipat').value=d['clipat'].unescapeHTML();
				$('climat').value=d['climat'].unescapeHTML();
				$('clinom').value=d['clinom'].unescapeHTML();
				$('clidir').value=d['clidir'].unescapeHTML();
				$('comcod').value=d['comcod'].unescapeHTML();
				$('comdes').value=d['comdes'].unescapeHTML();
				$('clifon').value=d['clifon'].unescapeHTML();
				$('clicel').value=d['clicel'].unescapeHTML();
				$('climail').value=d['climail'].unescapeHTML();
				$('cliobs').value=d['cliobs'].unescapeHTML();
				
			} else {

				/*
				$('clipat').value='';
				$('climat').value='';
				$('clinom').value='';
				$('clidir').value='';
				$('comcod').value='';
				$('comdes').value='';
				$('clifon').value='';
				$('clicel').value='';
				$('climail').value='';
				$('cliobs').value='';
				*/
							
			}
			
			$('clipat').focus();

			bloquear=0;

		}	
	});

}


guardar=function() {

	/*
	for(var i=0;i<uso.length;i++) {
		if(!validacion_fecha($('r_'+i+'_fecha_sep'))) {
			alert('Ha ingresado una fecha de sepultaci&oacute;n inv&aacute;lida.'.unescapeHTML());
			return;
		}
	}*/

	var myAjax=new Ajax.Request('ingresos/movimientos/sql_sepulturas.php',{
		method:'post', parameters: $('filtro').serialize()+'&'+
										serializar_objetos('listado_uso'),
		onComplete: function(resp) {
		
			if(resp.responseText=='') {
				alert('Registro guardado exitosamente.');
				//volver();
			} else {
				alert(resp.responseText);
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

guardar_uso=function() {

	for(var i=0;i<uso.length;i++) {
		uso[i].us_id=$('r_'+i+'_us_id').value;
		uso[i].crecod=$('r_'+i+'_crecod').value;
		uso[i].bolnum=$('r_'+i+'_bolnum').value;
		uso[i].us_fecha_sep=$('r_'+i+'_fecha_sep').value;
		uso[i].us_rut=$('r_'+i+'_rut').value;
		uso[i].us_nombre=$('r_'+i+'_nombre').value;
		uso[i].us_referencias=$('r_'+i+'_referencias').value;
		uso[i].us_vence=$('r_'+i+'_vence').value;
		uso[i].us_ubicacion=$('r_'+i+'_ubicacion').value;
		uso[i].us_estado=$('r_'+i+'_estado').value;
	}

}

agregar=function() {
		
	if(!uso) uso=[];

	var n=uso.length;
	
	guardar_uso();

	uso[n]=new Object();
	uso[n].us_id=0; 
	uso[n].crecod=0; 
	uso[n].bolnum=''; 
	uso[n].us_fecha_sep=''; 
	uso[n].us_rut=''; 
	uso[n].us_nombre='';
	uso[n].us_referencias='';
	uso[n].us_vence='';
	uso[n].us_ubicacion='';
	uso[n].us_estado=0;

	redibujar();
	
}

eliminar=function(v, n) {

	if(v==3) {
		conf=confirm("&iquest;Desea eliminar el registro?".unescapeHTML());
		if(!conf) {
			$('r_'+n+'_estado').value=uso[n].us_estado; 
			return;
		}
		guardar_uso();	
		uso=uso.without(uso[n]);
		redibujar();
	}
	
}	


redibujar=function() {

	var html='<table cellpadding=0 cellspacing=0 style="width:100%;font-size:11px;"><tr class="tabla_header">';
	
	html+='<td>Bolet&iacute;n</td>';	
	html+='<td>Fecha Sep.</td>';	
	html+='<td>R.U.T.</td>';	
	html+='<td style="width:25%;">Nombre</td>';	
	html+='<td>Referencias</td></td>';	
	html+='<td>Vencimiento</td></td>';	
	html+='<td>Ubicaci&oacute;n</td>';	
	html+='<td>Estado</td>';	
	html+='<td>Accion</td>';	
	
	html+='</tr>';
	
	for(var i=0;i<uso.length;i++) {
	
		var u=uso[i];
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
		
		html+='<tr onMouseOver="this.className=\'mouse_over\'" ';
		html+='onMouseOut="this.className=\''+clase+'\'">';
		
		html+='<input type="hidden" id="r_'+i+'_us_id" name="r_'+i+'_us_id" style="text-align:center;" value="'+u.us_id+'" />';
		html+='<input type="hidden" id="r_'+i+'_crecod" name="r_'+i+'_crecod" style="text-align:center;" value="'+u.crecod+'" />';
						
		if(u.us_id==0) {
		
			html+='<td><input type="text" id="r_'+i+'_bolnum" name="r_'+i+'_bolnum" style="text-align:center;" value="'+u.bolnum+'" /></td>';				
			html+='<td><input type="text" id="r_'+i+'_fecha_sep" name="r_'+i+'_fecha_sep" style="text-align:center;" value="'+u.us_fecha_sep+'" onBlur="validacion_fecha(this);" /></td>';				
			html+='<td><input type="text" id="r_'+i+'_rut" name="r_'+i+'_rut" style="text-align:right;" value="'+u.us_rut+'" onKeyUp="validacion_rut(this);" /></td>';				
			html+='<td><input type="text" id="r_'+i+'_nombre" name="r_'+i+'_nombre" style="text-align:left;" value="'+u.us_nombre+'" /></td>';				
			html+='<td><input type="text" id="r_'+i+'_referencias" name="r_'+i+'_referencias" style="text-align:left;" value="'+u.us_referencias+'" /></td>';				
			html+='<td><input type="text" id="r_'+i+'_vence" name="r_'+i+'_vence" style="text-align:center;" value="'+u.us_vence+'" onBlur="validacion_fecha(this);" /></td>';				
			html+='<td><select id="r_'+i+'_ubicacion" name="r_'+i+'_ubicacion" style="text-align:left;">'+tselect(u.us_ubicacion)+'</select></td>';				
			html+='<td><select id="r_'+i+'_estado" name="r_'+i+'_estado" style="text-align:left;" onChange="eliminar(this.value,'+i+');">'+eselect(u.us_estado)+'</select></td>';
			html+='<td><center><img src="iconos/delete.png" onClick="eliminar(3,'+i+');"/></center></td>';

		} else {

			html+='<td><input type="text" id="r_'+i+'_bolnum" name="r_'+i+'_bolnum" style="text-align:center;" value="'+u.bolnum+'" DISABLED /></td>';				
			html+='<td><input type="text" id="r_'+i+'_fecha_sep" name="r_'+i+'_fecha_sep" style="text-align:center;" value="'+u.us_fecha_sep+'" onBlur="validacion_fecha(this);" DISABLED /></td>';				
			html+='<td><input type="text" id="r_'+i+'_rut" name="r_'+i+'_rut" style="text-align:right;" value="'+u.us_rut+'" onKeyUp="validacion_rut(this);" DISABLED /></td>';				
			html+='<td><input type="text" id="r_'+i+'_nombre" name="r_'+i+'_nombre" style="text-align:left;" value="'+u.us_nombre+'" DISABLED /></td>';				
			html+='<td><input type="text" id="r_'+i+'_referencias" name="r_'+i+'_referencias" style="text-align:left;" value="'+u.us_referencias+'" DISABLED /></td>';				
			html+='<td><input type="text" id="r_'+i+'_vence" name="r_'+i+'_vence" style="text-align:center;" value="'+u.us_vence+'" onBlur="validacion_fecha(this);" DISABLED /></td>';				
			html+='<td><select id="r_'+i+'_ubicacion" name="r_'+i+'_ubicacion" style="text-align:left;" DISABLED>'+tselect(u.us_ubicacion)+'</select></td>';				
			html+='<td><select id="r_'+i+'_estado" name="r_'+i+'_estado" style="text-align:left;" onChange="eliminar(this.value,'+i+');" DISABLED>'+eselect(u.us_estado)+'</select></td>';
			if(u.crecod==0)		
				html+='<td><center><img src="iconos/arrow_refresh.png" onClick="mover('+i+');"/></center></td>';
			else
				html+='<td><center><img src="iconos/lock.png" onClick="alert(\'Bloqueado esperando Cr&eacute;dito #'+u.crecod+'\'.unescapeHTML() );"/></center></td>';
			
		}				
		
		html+='</tr>';

	}	
	
	html+='</table>';
	
	$('listado_uso').innerHTML=html;

	for(var i=0;i<uso.length;i++) {
		validacion_fecha($('r_'+i+'_fecha_sep'));
		validacion_fecha($('r_'+i+'_vence'));
		validacion_rut($('r_'+i+'_rut'));
	}

}

mover=function(v) {

	var params=	$('sepultura').serialize()+'&us_id='+$('r_'+v+'_us_id').value*1;
	
   l=(screen.availWidth/2)-375;
   t=(screen.availHeight/2)-230;
        
   win = window.open('ingresos/movimientos/movimientos.php?'+params, 
                    '_ver_sepulturas',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=750, height=460');
                    
   win.focus();

}

</script>


<center>

<form id='filtro' name='filtro' onSubmit='return false;'>
<input type='hidden' id='tsep_estructura' name='tsep_estructura'>
<input type='hidden' id='sepultura' name='sepultura' value=''>

<div class="sub-content" style='width:750px;'>


<div class="sub-content">
<img src='iconos/map_magnify.png'>
<b>Movimiento/Traslado de Sepulturas</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>
Sepultura:
</td>
<td>
<span id='select_clases' style='width:150px;'></span>
<span id='select_codigos' style='width:50px;'></span>
</td>
</tr>
<tr>
<td style='text-align:right;'>N&uacute;mero:</td>
<td><input type='text' style='text-align:center;' 
id='numero' name='numero' size=5>
<input type='button' value='Accesar Sepultura...' 
id='cargar_sepultura' onClick='cargar_sep();'>
</td>
</tr>
</table>
</div>

<div class='sub-content' style='height:350px;' id='selsep'>
<center>
<br /><br/>
<br /><br/>
<b>Seleccione una Sepultura</b>
</center>
</div>

<div class='sub-content2' id='listado' 
style='height:350px;overflow:auto;display:none;'>

</div>

</div>

</form>

<script> select_clases(); </script>
