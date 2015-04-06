<?php 

	require_once('../../conectar_db.php');

	$lista_id=$_GET['lista_id']*1;

	$lista=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=".$lista_id, true);


	$listas_destino=cargar_registros_obj("
		SELECT lista_id , lista_nombre FROM lista_dinamica ORDER BY lista_id
	", true);
	
	if(!$lista['lista_id_destino']) 
		$marcadas=array();
	else{
		$marcadas=str_replace('{','',str_replace('}','',$lista['lista_id_destino']));
	}

?>

<html>

<title><?php if($lista_id!=0) echo 'Editar'; else echo 'Crear nueva';?> Lista Din&aacute;mica</title>

<?php cabecera_popup('../..'); ?>

<script>

<?php if(!$lista['lista_id_destino']) { ?>
	marcadas=<?php echo json_encode($marcadas); ?>;
<?php }else{ ?>
	marcadas=<?php echo json_encode($marcadas); ?>.split(',');
<?php } ?>

listas=<?php echo json_encode($listas_destino); ?>;

listar_destinos=function() {
	
	var html='<table style="width:100%;">';
	//html+='<tr class="tabla_header"><td colspan=2>Listas de Destino</td></tr>';
	html+='<tr class="tabla_header"><td style="width:10%;">Marcar</td>';
	html+='<td>Lista Din&aacute;mica de Destino</td>';
	/*html+='<td style="width:10%;">Marcar</td>';
	html+='<td>Listas de Destino</td>';*/
	html+='</tr>';		
	
	var check='';	
	
	for(var i=0;i<listas.length;i++) {
	
		////console.log(listas[i].lista_id)
		
		if(marcadas){
		
			for(var m=0;m<marcadas.length;m++){
				if(listas[i].lista_id*1==marcadas[m]*1) {
					console.log(listas[i].lista_id+' - '+listas[i].lista_nombre+' - [SI]') 
					check='CHECKED';
					break;
				}else{
				 //console.log(listas[i].lista_id+' - '+listas[i].lista_nombre+' - [NO]')
				 	check='';
				}	
						
			}	
		}
	
		
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';	
		
		html+='<tr class="'+clase+'">';
		html+='<td><center><input type="checkbox" id="lista_id_'+listas[i].lista_id+'" name="lista_id_'+listas[i].lista_id+'" value="['+listas[i].lista_id+']"'+check+' /></center></td>';
		html+='<td style="font-size:10px;">'+listas[i].lista_nombre+'</td>';							
		/*if(i+1<=listas.length){
				i++;
			html+='<td><center><input type="checkbox" id="lista_id_'+listas[i].lista_id+'" name="lista_id_'+listas[i].lista_id+'" '+check+' /></center></td>';
			html+='<td style="font-size:10px;">'+listas[i].lista_nombre+'</td>';
		}*/
		html+='</tr>';
		
	}	
	
	html+='</table>';

	$('listas_destino').innerHTML=html;
	
}

guardar_destino=function() {

	var marcadas_tmp=[];
		for(i=0;i<listas.length;i++){
			if($('lista_id_'+listas[i].lista_id).checked){
				marcadas_tmp.push(listas[i].lista_id);
				//alert(listas[i].lista_id);
			}			
		}
		marcadas=marcadas_tmp;
}

guardar_lista=function() {

	guardar_destino();

	var params='&destino='+encodeURIComponent(marcadas.toJSON());

	var myAjax=new Ajax.Request(
		'sql_editar.php',
		{
			method:'post',
			parameters:$('datos').serialize()+params,
			onComplete:function() {
				alert('Cambios guardados exitosamente.');
				window.close();	
				
			}	
		}	
	);
	
}

valida_email = function(email){ 
var formato=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/;
	if(email==''){
		return;	
	}else if (formato.test(email)){
   //alert('La dirección de email '+email+' es correcta.');
   //console.log('si '+email);
  } else {
   alert((email+' No es una direcci&oacute;n de correo v&aacute;lida.').unescapeHTML());
   //console.log('no '+email);   
   $('paciente_mail').focus();
  }

}

</script>

<body>

<form id='datos' name='datos' onSubmit='return false;'>

<input type='hidden' id='lista_id' name='lista_id' value='<?php echo $lista_id; ?>' />

<div class='sub-content'>
<img src='../../iconos/layout_edit.png'>
<b><?php if($lista_id!=0) echo 'Editar'; else echo 'Crear nueva'; ?> Lista Din&aacute;mica</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Nombre de la Lista:</td>
<td class='tabla_fila' colspan=4>
<input type='text' id='lista_nombre' name='lista_nombre' size=50 value="<?php echo $lista['lista_nombre']; ?>" />
</td></tr>
<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Campos Tabla:</td>
<td class='tabla_fila' colspan=4>
<textarea cols=60 rows=3 id='campos_tabla' name='campos_tabla'><?php echo $lista['lista_campos_tabla']; ?></textarea>
</td></tr>
<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Campos Formulario:</td>
<td class='tabla_fila' colspan=4>
<textarea cols=60 rows=3 id='campos_formulario' name='campos_formulario'><?php echo $lista['lista_campos_formulario']; ?></textarea>
</td></tr>
<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Reporte:</td>
<td class='tabla_fila' colspan=4>
<textarea cols=60 rows=3 id='lista_reporte' name='lista_reporte'><?php echo $lista['lista_reporte']; ?></textarea>
</td></tr>
<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Condiciones:</td>
<td class='tabla_fila' colspan=4>
<textarea cols=60 rows=3 id='lista_condiciones' name='lista_condiciones'><?php echo $lista['lista_condiciones']; ?></textarea>
</td></tr><tr>
<td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2' width=25%; rowspan=2>Alertas:</td>
<td class='tabla_fila'><table width:100%;>
<td style='text-align:left;'><i>Roja: </i> 
<input type='text' id='aler_amarilla' name='aler_amarilla' value="<?php echo $lista['lista_plazo_alerta_amarilla'];?>" size=5> (D&iacute;as de Plazo)
</td><td style='border-left: 3px ridge black;'>&nbsp;
<i>Amarilla: </i><input type='text' id='aler_roja' name='aler_roja' value="<?php echo $lista['lista_plazo_alerta_roja'];?>" size=5> (D&iacute;as de Plazo)</td> 
</tr><tr>
<td class='tabla_fila' colspan=2><i>Correo Electr&oacute;nico: </i>
<input type='text' id='correo_alerta' name='correo_alerta' onBlur='valida_email(this.value);' value="<?php echo $lista['lista_correo_alerta'];?>"></td>
</tr>
</table>
</td>
</table>
</center>
</div>

<div class='sub-content3' id='listas_destino' name='listas_destino' rows=7>

</div>

<br />

<center>
<input type='button' value=' - Guardar Lista Din&aacute;mica - ' 
onClick='guardar_lista();' />
</center>

</form>

</body>

</html>

<script>

	listar_destinos();
	
</script>
