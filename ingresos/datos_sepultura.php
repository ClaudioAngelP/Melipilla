<?php 

	require_once('../conectar_db.php');
	
	if(isset($_GET['prod_sel'])) {
	
		$r=explode('|',utf8_decode($_GET['prod_sel']));

		$nclase=$r[0];
		$ncodigo=$r[1];
		$numero=$r[2];
		$letra=$r[3];
		$fecha_sep=$r[4];

		$rut_sep=$r[5];
		$nombre_sep=$r[6];
		$referencias=$r[7];
		$ubicacion=$r[8];

		$estado=$r[9];
		
	} else $r=false;

	$l=cargar_registros_obj("
		SELECT * FROM inventario 
		WHERE sep_clase='$clase'
		ORDER BY sep_codigo, sep_ninicio
	");

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

<html>
<title>Ingresar Datos de Uso de Sepulturas</title>

<?php cabecera_popup('..'); ?>

<script>

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
	
	select_ubicaciones(val);
	
	var html='<select id="sel_codigos" name="sel_codigos" onChange="if(this.value==\'-1\') $(\'sel_codigon\').style.display=\'\'; else $(\'sel_codigon\').style.display=\'none\';">';
	
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

select_ubicaciones=function(id) {

	if(id==-1) {
		return;	
	}
		
	for(var i=0;i<clases.length;i++) {
		if(clases[i][0]==id) {
			id=i; break;
		}
	}

	var u=clases[id][1].split(',');

	var html='<select id="sel_ubicaciones" name="sel_ubicaciones">';

	for(var i=0;i<u.length;i++) {
		html+='<option value="'+u[i]+'">'+u[i]+'</option>';
	}

	html+='</select>';

	$('select_ubicaciones').innerHTML=html;

}


function aceptar() {

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

	if(!validacion_fecha($('fecha_sep'))) {
		alert( "Debe ingresar una fecha de sepultaci&oacute;n v&aacute;lida.".unescapeHTML() );
		return;	
	}

	var clase=$('sel_clases').value;
	
	var codigo=$('sel_codigos').value;
	
	if(codigo=='-1') {
		$('sel_codigon').value=trim($('sel_codigon').value);
		codigo=$('sel_codigon').value;	
	}
	
	var numero=$('numero').value;
	var letra=$('letra').value;
	var fecha_sep=$('fecha_sep').value;

	var rut_sep=$('rut_sep').value;
	var nombre_sep=$('nombre_sep').value;
	var referencias=$('referencias').value;
	var ubicacion=$('sel_ubicaciones').value;
	var estado=$('estado').value;

	window.opener.productos[indice].prod_sel=clase+'|'+codigo+'|'+numero+'|'+letra+'|'+fecha_sep+'|'+rut_sep+'|'+nombre_sep+'|'+referencias+'|'+ubicacion+'|'+estado;
	fn=window.opener.redibujar_tabla.bind(window.opener);
	fn(); window.close();
	
}

</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../iconos/map_add.png'>
<b>Informaci&oacute;n de Uso de Sepulturas</b>
</div>

<div class='sub-content'>

	<table style='width:100%;' cellpadding=3>
	<tr>
	<td style='text-align:right;width:35%;'>Tipo de Sepultura:</td>
	<td id='select_clases'>
	</td>
	</tr>	
	<tr>
	<td style='text-align:right;width:30%;'>C&oacute;digo:</td>
	<td id='select_codigos'>
	</td>
	<tr>
	<td style='text-align:right;width:30%;'>Numeraci&oacute;n:</td>
	<td>
	<input type='text' size=10 id='numero' name='numero' value='<?php if($r) echo $numero; else echo ''; ?>'>	
	<input type='text' size=5 id='letra' name='letra' value='<?php if($r) echo $letra; else echo ''; ?>'>	
	</td>
	</tr>	
	
	<tr>
	<td style='text-align:right;width:30%;'>Fecha Sepultaci&oacute;n:</td>
	<td>
	<input type='text' size=10 style='text-align:center;'
	onBlur='validacion_fecha(this);' 
	id='fecha_sep' name='fecha_sep' value='<?php if($r) echo $fecha_sep; else echo ''; ?>'>
	<img src='../iconos/calendar.png' id='fecha_sep_boton'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:30%;'>R.U.T.:</td>
	<td>
	<input type='text' size=10 id='rut_sep' name='rut_sep' 
	value='<?php if($r) echo $rut_sep; else echo ''; ?>'
	onKeyUp='validacion_rut(this);'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:30%;'>Nombre Completo:</td>
	<td>
	<input type='text' id='nombre_sep' name='nombre_sep' value='<?php if($r) echo $nombre_sep; else echo ''; ?>'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:30%;'>Referencias:</td>
	<td>
	<input type='text' size=20 style='text-align:left;' 
	id='referencias' name='referencias' value='<?php if($r) echo $referencias; else echo ''; ?>'>
	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:30%;'>Ubicaci&oacute;n:</td>
	<td id='select_ubicaciones'>

	</td>
	</tr>	

	<tr>
	<td style='text-align:right;width:30%;'>Estado:</td>
	<td>
	<select id='estado' name='estado' value='<?php if($r) echo $estado; else echo '0'; ?>'>
	<option value=0>Entero</option>
	<option value=1>Reducido</option>
	<option value=2>Anfora</option>
	</select>
	</td>
	</tr>	
	
	<tr><td colspan=2>
	<center>
	<input type='button' id='ingresar' name='ingresar' 
	onClick='aceptar();' value='-- Ingresar Datos... --'>
	</center>	
	</td></tr>	
	
	</table>
	
</div>

</body>
</html>

<script> 

    Calendar.setup({
        inputField     :    'fecha_sep',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_sep_boton'
    });
    
	select_clases(); 

<?php if($r) { ?>

	$('sel_clases').value='<?php echo $nclase; ?>';
	select_codigos();
	$('sel_codigos').value='<?php echo $ncodigo; ?>';
	$('sel_codigon').value='<?php echo $ncodigo; ?>';
	$('sel_ubicaciones').value='<?php echo $ubicacion; ?>';
	
	$('sel_codigos').value='<?php echo $ncodigo; ?>';
	
	if($('sel_codigos').value!='<?php echo $ncodigo; ?>') {
		$('sel_codigos').value=-1;
		$('sel_codigon').style.display='';	
	}

<?php } ?>

	$('sel_clases').focus();
	
	validacion_fecha($('fecha_sep')); 
	validacion_rut($('rut_sep')); 

</script>