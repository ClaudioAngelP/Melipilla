<?php 

	require_once('../conectar_db.php');

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
	
?>

<html>

<title>Definir Bloqueo de Usuarios</title>

<?php cabecera_popup('..'); ?>

<script>

var ids='<?php echo str_replace("'","\\'", utf8_decode($_GET['ids_bloqueos'])); ?>';

agregar_bloqueo = function(datos) {

	ids+='&&'+datos;

	redibujar_tabla();

}

function eliminar(n) {

	var l=ids.split('&&');
	
	ids='';	
	
	for(var i=1;i<l.length;i++) {
		if(i!=n) {
			ids+='&&'+l[i];
		}
	}
	
	redibujar_tabla();

}

function redibujar_tabla() {

	var l=ids.split('&&');

	var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>Sepultura</td><td>RUT</td><td>Nombre</td><td>Ubicaci&oacute;n</td><td>Eliminar</td></tr>';

	totald=0;
	
	for(var i=1;i<l.length;i++) {
	
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';
		
		reg=l[i].split('|');
		
		html+='<tr class="'+clase+'">';
		html+='<td style="text-align:center;">'+reg[1]+' &gt; '+reg[2]+' &gt; '+reg[3]+''+reg[4]+'</td>';
		html+='<td style="text-align:right;">'+reg[5]+'</td>';
		html+='<td style="text-align:left;">'+reg[6]+'</td>';
		html+='<td style="text-align:center;">'+reg[7]+'</td>';
		html+='<td><center><img style="cursor:pointer;" src="../iconos/delete.png" onClick="eliminar('+i+');"></center></td>';		
		html+='</td></tr>';
				
	}
	
	html+='</table>';
	
	$('listado_sel').innerHTML=html;
	
}

function aceptar() {

	window.opener.$('ids_bloqueos').value=ids;
	window.close();

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

buscar_nombre=function() {

	var myAjax=new Ajax.Updater(
		'listado',
		'busqueda_usuarios.php',
		{
			method:'post',
			parameters:$('nombre').serialize()	
		}	
	);
	
}

buscar_sepultura=function() {

	var myAjax=new Ajax.Updater(
		'listado',
		'busqueda_usuarios.php',
		{
			method:'post',
			parameters:$('sel_clases').serialize()+'&'+
							$('sel_codigos').serialize()+'&'+
							$('sel_codigon').serialize()+'&'+
							$('numero').serialize()	
		}	
	);
	
}


</script>

<body class='fuente_por_defecto popup_background'>


<div class='sub-content'>
<img src='../iconos/lock.png'>
<b>Definici&oacute;n de Bloqueo de Usuarios</b>
</div>

<div class='sub-content'>
<table style='width:100%;font-size:11px;'>
<tr><td style='text-align:right;'>Tipo de B&uacute;squeda:</td><td>

<select id='tipo' name='tipo' onChange="
	if(this.value=='0') {
		$('bsep1').style.display='';
		$('bsep2').style.display='';
		$('bsep3').style.display='';
		$('bnombre').style.display='none';
	} else {
		$('bsep1').style.display='none';
		$('bsep2').style.display='none';
		$('bsep3').style.display='none';
		$('bnombre').style.display='';
	}
">
<option value='1'>RUT/Nombre</option>
<option value='0'>Sepultura</option>
</select>
</td></tr>

<tr id='bnombre'>
<td style='text-align:right;'>RUT/Nombre:</td>
<td>
<input type='text' id='nombre' name='nombre' size=20 />
<input type='button' value='Buscar...' onClick='buscar_nombre();'>
</td></tr>

<tr id='bsep1' style='display:none;'>
<td style='text-align:right;width:25%;'>Tipo de Sepultura:</td>
<td id='select_clases'>
</td>
</tr>	
<tr id='bsep2' style='display:none;'>
<td style='text-align:right;width:25%;'>C&oacute;digo:</td>
<td id='select_codigos'>
</td>
<tr id='bsep3' style='display:none;'>
<td style='text-align:right;width:25%;'>Numeraci&oacute;n:</td>
<td>
<input type='text' size=10 id='numero' name='numero' value=''>

<input type='button' value='Buscar...' onClick='buscar_sepultura();' />	
</td>
</tr>	

</table>
</div>

<div class='sub-content2' id='listado' style='height:150px;overflow:auto;'>

</div>

<div class='sub-content'>
<img src='../iconos/lock_go.png'>
<b>Selecci&oacute;n de Bloqueo de Usuarios</b>
</div>

<div class='sub-content2' id='listado_sel' style='height:100px;'>

</div>

<center>
<input type='button' id='acepta' onClick='aceptar();' 
value='- Aceptar Definici&oacute;n de Bloqueos -'>
</center>
</body>

</html>

<script> 

	select_clases(); 

	redibujar_tabla(); 

</script>