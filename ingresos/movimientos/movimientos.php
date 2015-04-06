<?php 

	require_once('../../conectar_db.php');
	
	$s=explode('|',utf8_decode($_GET['sepultura']));

	$nclase=$s[0];
	$ncodigo=$s[1];
	$numero=$s[2];
	$letra=$s[3];

	$sepultura=$nclase.' &gt; '.$ncodigo.' &gt; '.$numero.''.$letra;

	$b=false; $r=true;

	$id=$_GET['us_id']*1;
	
	$u=cargar_registro("SELECT * FROM uso_sepultura WHERE us_id=$id");

	$fecha_sep=htmlentities($u['us_fecha_sep']);
	$rut_sep=htmlentities($u['us_rut']);
	$nombre_sep=htmlentities($u['us_nombre']);
	$referencias=htmlentities($u['us_referencias']);
	$ubicacion=htmlentities($u['us_ubicacion']);
	
	$estado=$u['us_estado']*1;
		
	/*
	$l=cargar_registros_obj("
		SELECT * FROM inventario 
		WHERE sep_clase='$clase'
		ORDER BY sep_codigo, sep_ninicio
	");
	*/

	$clases=cargar_registros_obj("SELECT DISTINCT tsep_clase, tsep_estructura FROM tipo_sepultura ORDER BY tsep_clase");
	$codigos=cargar_registros_obj("SELECT DISTINCT sep_codigo, sep_clase FROM inventario");

	for($i=0;$i<sizeof($clases);$i++) {
		$clase[$i][0]=htmlentities($clases[$i]['tsep_clase']);
		$clase[$i][1]=htmlentities($clases[$i]['tsep_estructura']);
	}

	for($i=0;$i<sizeof($codigos);$i++) {
		$codigo[$i][0]=htmlentities($codigos[$i]['sep_codigo']);
		$codigo[$i][1]=htmlentities($codigos[$i]['sep_clase']);
	}

	if($u['bolnum2']*1==0) 
		$sepulturashtml='';
	else
		$sepulturashtml=desplegar_opciones_sql("
			SELECT ps_id,
			ps_clase || ' > ' || ps_codigo || ' > ' || ps_numero || ps_letra
			|| ' (Bol. #' || bolnum || ')'
			FROM propiedad_sepultura 
			WHERE bolnum=".$u['bolnum2']."	
		");

?>

<html>
<title>Traslado de Usuarios de Sepulturas (<?php echo $u['bolnum2']; ?>)</title>

<?php cabecera_popup('../..'); ?>

<script>

var bloquear=0;

cargar_boletin = function() {

	var myAjax=new Ajax.Request(
		'../../ingresos/info_boletin.php', {
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
											
					} catch(err) {
						alert(err);
					}
					
				}
				
			}
		}	
	);

}


clases=<?php echo json_encode($clase); ?>;
codigos=<?php echo json_encode($codigo); ?>;

select_clases=function() {

	var html='<select id="sel_clases" name="sel_clases" onChange="select_codigos();">';
	
	for(var i=-1;i<clases.length;i++) {
		var sel='';
		if(i==-1)
			html+='<option value="-1" '+sel+'>(Seleccione la Clase...)</option>';
		else 
			html+='<option value="'+clases[i][0]+'" '+sel+'>'+clases[i][0]+'</option>';
	}

	html+='</select>';

	$('select_clases').innerHTML=html;
	
	select_codigos();
	

}

select_codigos=function() {

	var val=$('sel_clases').value;
	
	select_ubicaciones();
	
	var html='<select id="sel_codigos" name="sel_codigos" onChange="select_ubicaciones(); if(this.value==\'-1\') $(\'sel_codigon\').style.display=\'\'; else $(\'sel_codigon\').style.display=\'none\';">';
	
	var estilo='display:none';	
	
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

function chequear_ubicaciones() {

	if($('sepulturas')==null || $('otrasep').checked) {

		var params='clase='+encodeURIComponent($('sel_clases').value)+'&';
		
		if($('sel_codigos').value!='-1')	
			params+='codigo='+encodeURIComponent($('sel_codigos').value)+'&';
		else
			params+='codigo='+encodeURIComponent($('sel_codigon').value)+'&';
			
		params+='numero='+encodeURIComponent($('numero').value)+'&';	
		params+='letra='+encodeURIComponent($('letra').value);
	
	} else {
	
		params='ps_id='+($('sepulturas').value*1);
		
	}
	
	$('select_ubicaciones').innerHTML='<img src="../../imagenes/ajax-loader1.gif"> Espere un momento...';	
	
	var myAjax=new Ajax.Updater(
		'select_ubicaciones',
		'../ubicaciones_sepultura.php',
		{
			method: 'post',
			parameters: params+'&'+$('estado').serialize() 	
		}	
	);
	
}	


function aceptar() {

	if($('bolnum').value*1==0) {
			alert( "Debe asociar un bolet&iacute;n al movimiento.".unescapeHTML() );
			return;	
	}

	if($('traslado').value*1==1) {

		if($('sepulturas')==null || $('otrasep').checked) {

			if($('sel_clases').value=='-1') {
				alert("Debe seleccionar la clase de la sepultura.");
				return;
			}
		
			if($('sel_codigos').value=='-2') {
				alert( "Debe seleccionar el c&oacute;digo de la sepultura.".unescapeHTML() );
				return;
			}
		
			if($('sel_codigos').value=='-1' && trim($('sel_codigon').value)=='') {
				alert( "Debe especificar el c&oacute;digo de la sepultura.".unescapeHTML() );
				return;
			}
		
			if($('numero').value*1==0) {
				alert( "Debe ingresar numeraci&oacute;n de la sepultura.".unescapeHTML() );
				return;
			}
		
		}
		
		if($('sel_ubicaciones')==null || $('sel_ubicaciones').value*1==-1) {
			alert( "Debe seleccionar una ubicaci&oacute;n v&aacute;lida dentro de la sepultura.".unescapeHTML() );
			return;		
		}

	
	}

	if(!validacion_fecha($('fecha_sep'))) {
		alert( "Debe ingresar una fecha de sepultaci&oacute;n v&aacute;lida.".unescapeHTML() );
		return;	
	}

	var myAjax=new Ajax.Request(
		'sql_movimientos.php',
		{
			method:'post',
			parameters:$('registro').serialize(),
			onComplete:function(resp) {
				if(resp.responseText=='') {
				
					alert('Movimiento realizado exitosamente.');

					fn=window.opener.realizar_carga_sep.bind(window.opener);
					fn();
						
					window.close();
					
				} else {
				
					alert(resp.responseText);
					
				}
			}		
		});
	
}

</script>

<body class='popup_background fuente_por_defecto'>

<form id='registro' name='registro' onsubmit='return false;'>

<input type='hidden' id='us_id' name='us_id' value='<?php echo $id; ?>'>

<div class='sub-content'>
<img src='../../iconos/map_go.png'>
<b>Traslado de Usuarios de Sepulturas</b>
</div>

<div class='sub-content'>
<img src='../../iconos/coins.png'>
<b>Datos Comerciales</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:25%;'>Bolet&iacute;n:</td>
<td><input type='text' id='bolnum' name='bolnum'
onBlur='cargar_boletin();' onKeyUp='if(event.which==13) cargar_boletin();' 
size=10 <?php if($s) echo "value='".$s['bolnum']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:25%;'>Fecha Bolet&iacute;n:</td>
<td>

<input type='text' name='fecha1' id='fecha1' size=10 onBlur='validacion_fecha(this);'
  style='text-align: center;' value='<?php if($b) echo $b['bolfec']; else echo date("d/m/Y"); ?>'>
  <img src='../../iconos/date_magnify.png' id='fecha1_boton'>
  
</td>
</tr>


<tr>
<td style='text-align:right;width:25%;'>Monto $:</td>
<td><input type='text' id='bolmon' name='bolmon' 
size=10 <?php if($b) echo "value='".$b['bolmon']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:25%;'>Observaciones:</td>
<td><input type='text' id='bolobs' name='bolobs' 
size=10 <?php if($b) echo "value='".$b['bolobs']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:25%;'>Cod. Cr&eacute;dito:</td>
<td><input type='text' id='crecod' name='crecod' 
size=10 <?php if($b) echo "value='".$b['crecod']."'"; ?> ></td>
</tr>

<tr>
<td style='text-align:right;width:25%;'>Total Cr&eacute;dito $:</td>
<td><input type='text' id='cretot' name='cretot' 
size=10 <?php if($cr) echo "value='".($cr['crepie']+$cr['cretot'])."'"; ?> ></td>
</tr>

</table>

</div>

<div class='sub-content'>
<img src='../../iconos/user_go.png'>
<b>Detalle del Movimiento</b>
</div>


<div class='sub-content'>

	<table style='width:100%;' cellpadding=3>
	<tr>
	<td style='text-align:right;'>Lugar de Or&iacute;gen:</td>
	<td style='font-size:16px;'><?php echo $sepultura; ?></td>	
	</tr>
	<tr><td style='text-align:right;'>Traslado de Ubicaci&oacute;n:</td>
	<td><select id='traslado' name='traslado'
	onChange='if(this.value*1==1) {
		<?php if(trim($sepulturashtml)=='') { ?>
		$("ubica1").style.display="";
		$("ubica2").style.display="";
		$("ubica3").style.display="";
		$("ubica4").style.display="";
		<?php } ?>
		<?php if(trim($sepulturashtml)!='') echo '$("sepulturas_bloq").style.display="";'; ?>	
	} else {
		$("ubica1").style.display="none";
		$("ubica2").style.display="none";
		$("ubica3").style.display="none";
		$("ubica4").style.display="none";	
		<?php if(trim($sepulturashtml)!='') echo '$("sepulturas_bloq").style.display="none";'; ?>	
	}'>

		<option value='0' <?php if(trim($sepulturashtml)=='') echo 'SELECTED'; ?>>Sin Traslado de Sepultura</option>
		<option value='1' <?php if(trim($sepulturashtml)!='') echo 'SELECTED'; ?>>Traslado de Sepultura</option>
		<!---- <option value='2'>Traslado a Fosa</option> --->
	
	</select>	
	</td>	
	</tr>
	
	<tr id='sepulturas_bloq' <?php if(trim($sepulturashtml)=='') echo "style='display:none;'"; ?>><td style='text-align:right;'>
	Trasladar a:
	</td><td>
	<select id='sepulturas' name='sepulturas'>
	<?php echo $sepulturashtml; ?>
	</select>
	<input type='checkbox' id='otrasep' name='otrasep'
	onClick='
	select_ubicaciones();
	if(this.checked) {
		$("ubica1").style.display="";
		$("ubica2").style.display="";
		$("ubica3").style.display="";
		$("ubica4").style.display="";
		$("sepulturas").disabled=true;	
	} else {
		$("ubica1").style.display="none";
		$("ubica2").style.display="none";
		$("ubica3").style.display="none";
		//$("ubica4").style.display="none";			
		$("sepulturas").disabled=false;	
	}	
	'> Otra Sepultura
	</td></tr>	
		
	<tr id='ubica1' style='display:none;'>
	<td style='text-align:right;width:25%;'>Tipo de Sepultura:</td>
	<td id='select_clases'>
	</td>
	</tr>	
	<tr id='ubica2' style='display:none;'>
	<td style='text-align:right;width:25%;'>C&oacute;digo:</td>
	<td id='select_codigos'>
	</td>
	<tr id='ubica3' style='display:none;'>
	<td style='text-align:right;width:25%;'>Numeraci&oacute;n:</td>
	<td>
	<input type='text' size=10 id='numero' name='numero' value='' onBlur='select_ubicaciones();'>	
	<input type='text' size=5 id='letra' name='letra' value='' onBlur='select_ubicaciones();'>	
	</td>
	</tr>	
	
	<tr>
	<td style='text-align:right;width:25%;'>Fecha Sepultaci&oacute;n:</td>
	<td>
	<input type='text' size=10 style='text-align:center;'
	onBlur='validacion_fecha(this);'
	id='fecha_sep' name='fecha_sep' value='<?php if($r) echo $fecha_sep; else echo ''; ?>'>
	<img src='../../iconos/calendar.png' id='fecha_sep_boton'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:25%;'>R.U.T.:</td>
	<td>
	<input type='text' size=10 id='rut_sep' name='rut_sep' DISABLED 
	value='<?php if($r) echo $rut_sep; else echo ''; ?>'
	onKeyUp='validacion_rut(this);'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:25%;'>Nombre Completo:</td>
	<td>
	<input type='text' id='nombre_sep' name='nombre_sep' 
	DISABLED value='<?php if($r) echo $nombre_sep; else echo ''; ?>'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:25%;'>Referencias:</td>
	<td>
	<input type='text' size=20 style='text-align:left;' DISABLED 
	id='referencias' name='referencias' value='<?php if($r) echo $referencias; else echo ''; ?>'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:25%;'>Estado Anterior:</td>
	<td style='font-size:16px;font-weight:bold;'>
	<?php 
		switch($estado) {
			case 0: echo 'Entero'; break;
			case 1: echo 'Reducido'; break;
			case 2: echo 'Anfora'; break;
		}
	?>
	</td>
	</tr>	


	<tr>
	<td style='text-align:right;width:25%;'>Estado Actual:</td>
	<td>
	<select id='estado' name='estado' onChange='select_ubicaciones();' 
	value='<?php if($r) echo $estado; else echo '0'; ?>'>
	<option value=0>Entero</option>
	<option value=1>Reducido</option>
	<option value=2>Anfora</option>
	</select>
	</td>
	</tr>	


	<tr>
	<td style='text-align:right;width:25%;'>Ubicaci&oacute;n Anterior:</td>
	<td style='font-size:16px;font-weight:bold;'>
	<?php echo $ubicacion; ?>
	</td>
	</tr>	

	<tr id='ubica4' style='<?php if(trim($sepulturashtml)=='') echo 'display:none;'; ?>'>
	<td style='text-align:right;width:25%;'>Ubicaci&oacute;n Actual:</td>
	<td id='select_ubicaciones'>

	</td>
	</tr>	

	
	<tr><td colspan=2>
	<center>
	<input type='button' id='ingresar' name='ingresar' 
	onClick='aceptar();' value='-- Realizar Movimiento... --'>
	</center>	
	</td></tr>	
	
	</table>
	
</div>

</form>

</body>
</html>

<script> 

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });

    Calendar.setup({
        inputField     :    'fecha_sep',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_sep_boton'
    });
    
	select_clases(); 

	$('bolnum').focus();
	
	validacion_fecha($('fecha1')); 
	validacion_fecha($('fecha_sep')); 
	validacion_rut($('rut_sep')); 

</script>