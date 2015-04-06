<?php 

	require_once('../conectar_db.php');
	
	$sel_clase=pg_escape_string(utf8_decode($_GET['clase']));

	if(isset($_GET['prod_sel'])) {
		$p_sel=explode('|',utf8_decode($_GET['prod_sel']));
		$p_clase=$p_sel[0];
		$p_codigo=$p_sel[1];
		$p_numero=$p_sel[2];
		$p_letra=$p_sel[3];
	} else $p_sel=false;

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
<title>Asociar Datos de Sepulturas</title>

<?php cabecera_popup('..'); ?>

<script>

clases=<?php echo json_encode($clase); ?>;
codigos=<?php echo json_encode($codigo); ?>;

select_clases=function() {

	var html='<select id="sel_clases" name="sel_clases" <?php if($sel_clase) echo 'DISABLED'; else echo 'onClick="select_codigos();"'; ?> >';
	
	for(var i=-1;i<clases.length;i++) {
		<?php if($sel_clase) echo 'if (i>=0 && clases[i][0]=="'.htmlentities($sel_clase).'") var sel="SELECTED"; else ';  ?>		
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
	
	var html='<select id="sel_codigos" name="sel_codigos" onChange="if(this.value==\'-1\') $(\'sel_codigon\').style.display=\'\'; else $(\'sel_codigon\').style.display=\'none\';">';
	
	var estilo='display:none;';	
	
	for(var i=-2;i<codigos.length;i++) {
		var sel='';
		if(i==-2)
			html+='<option value="-2" '+sel+'>(Seleccione C&oacute;digo...)</option>';
		else if(i==-1) {
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

	if($('sel_clases').value*1==-1 || 
		$('sel_codigos').value*1==-2 ||
		$('numero').value*1==0) {
		
		alert( 'La ubicaci&oacute;n ingresada no es v&aacute;lida.'.unescapeHTML() );
		return;
			
	}

	if($('sel_codigos').value=='-1' && trim($('sel_codigon').value)=='') {
		alert( "Debe especificar el c&oacute;digo de la sepultura.".unescapeHTML() );
		return;
	}

	if($('sel_codigos').value*1!=-1)
		$('sepultura').value=$('sel_clases').value+'|'+$('sel_codigos').value+'|'+$('numero').value+'|'+$('letra').value;
	else {
		$('sel_codigon').value=trim($('sel_codigon').value);
		$('sepultura').value=$('sel_clases').value+'|'+$('sel_codigon').value+'|'+$('numero').value+'|'+$('letra').value;
	}
	
	var myAjax=new Ajax.Request(
	'disponibilidad_sepultura.php',
	{
		method:'post',
		parameters:$('filtro').serialize(),
		onComplete:function(resp) {
			var r=resp.responseText.evalJSON(true);
			
			if(!r) {
				$('selsep').innerHTML='<br /><br /><br /><br /><b>Sepultura DISPONIBLE</b><br /><br /><input type="button" value="Seleccionar Sepultura..." onClick="asociar();" />';
			} else {
				$('selsep').innerHTML='<br /><br /><br /><br /><b>Sepultura NO DISPONIBLE</b>';			
			}
		}
	});

}


function asociar() {

	window.opener.productos[indice].prod_sel=$('sepultura').value;
	fn=window.opener.redibujar_tabla.bind(window.opener);
	fn(); window.close();
	
}

</script>

<body class='popup_background fuente_por_defecto'>

<form id='filtro' name='filtro' onSubmit='return false;'>
<input type='hidden' id='tsep_estructura' name='tsep_estructura'>
<input type='hidden' id='sepultura' name='sepultura' value=''>

<div class="sub-content">
<img src='../iconos/map_edit.png'>
<b>Seleccione la Sepultura</b>
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
id='numero' name='numero' size=5><input type='text' style='text-align:center;' 
id='letra' name='letra' size=5>
<input type='button' value='Comprobar Disponibilidad de Sepultura...' 
id='cargar_sepultura' onClick='cargar_sep();'>
</td>
</tr>
</table>
</div>

<div class='sub-content' style='height:250px;text-align:center;' id='selsep'>
<center>
<br /><br/>
<br /><br/>
<b>Seleccione una Sepultura</b>
</center>
</div>


</form>


</body>
</html>

<script> 

	select_clases(); 

<?php if($p_sel) { ?>

	$('sel_codigos').value='<?php echo $p_codigo; ?>';
	$('sel_codigon').value='<?php echo $p_codigo; ?>';
	$('numero').value='<?php echo $p_numero; ?>';	
	$('letra').value='<?php echo $p_letra; ?>';	

	if($('sel_codigos').value!='<?php echo $p_codigo; ?>') {
		$('sel_codigos').value=-1;
		$('sel_codigon').style.display='';		
	}
	
	cargar_sep();	
	
<?php } ?>
	
</script>
