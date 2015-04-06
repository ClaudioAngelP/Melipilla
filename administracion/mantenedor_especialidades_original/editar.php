<?php 

	require_once('../../conectar_db.php');

	$esp_id=$_GET['esp_id']*1;

	$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_id, true);
	$pro=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$esp_id, true);

	$presta=cargar_registros_obj("
		SELECT *,
		(SELECT COUNT(*) FROM nomina_detalle_prestaciones 
		WHERE nomina_detalle_prestaciones.pc_id=procedimiento_codigo.pc_id) AS cnt 
		FROM procedimiento_codigo
		WHERE esp_id=$esp_id
	", true);
	
	if(!$presta) $presta=array();

?>

<html>

<title><?php if($esp_id!=0) echo 'Editar'; else echo 'Crear nueva';?> Especialidad/Unidad</title>

<?php cabecera_popup('../..'); ?>

<script>

presta=<?php echo json_encode($presta); ?>;

agregar_codigo=function() {

	guardar_tabla();	

	var num=presta.length;
	
	presta[num]=new Object();
	presta[num].pc_id=0;
	presta[num].pc_codigo=$('codigo').value;
	presta[num].pc_desc=$('desc').value;
	presta[num].cnt=0;

	listar_prestaciones();
	
}

guardar_tabla=function() {

	for(var i=0;i<presta.length;i++) {
		var pc_id=presta[i].pc_id;
		if(pc_id!=0) {
			presta[i].pc_codigo=$('pc_codigo_'+pc_id).value;
			presta[i].pc_desc=$('pc_desc_'+pc_id).value;	
		} else {
			presta[i].pc_codigo=$('pc_codigo_0_'+i).value;
			presta[i].pc_desc=$('pc_desc_0_'+i).value;				
		}	
	}
	
}

eliminar=function(num) {

	guardar_tabla();
	presta=presta.without(presta[num]);
	listar_prestaciones();
	
}

listar_prestaciones=function() {
	
	var html='<table style="width:100%;"><tr class="tabla_header">';
	html+='<td style="width:150px;">C&oacute;digo FONASA</td>';
	html+='<td>Descripci&oacute;n</td>';
	html+='<td>Regs.</td>';
	html+='<td>Eliminar</td>';
	html+='</tr>';		
	
	for(var i=0;i<presta.length;i++) {
		
		var clase=(i%2==0)?'tabla_fila':'tabla_fila2';				
		
		html+='<tr class="'+clase+'">';
		if(presta[i].pc_id!=0) {
			html+='<td><input type="text" style="width:100%;text-align:center;" id="pc_codigo_'+presta[i].pc_id+'" name="pc_codigo_'+presta[i].pc_id+'" value="'+presta[i].pc_codigo+'" /></td>';
			html+='<td><input type="text" style="width:100%;"id="pc_desc_'+presta[i].pc_id+'" name="pc_desc_'+presta[i].pc_id+'" value="'+presta[i].pc_desc+'" /></td>';
		} else {
			html+='<td><input type="text" style="width:100%;text-align:center;" id="pc_codigo_0_'+i+'" name="pc_codigo_0_'+i+'" value="'+presta[i].pc_codigo+'" /></td>';
			html+='<td><input type="text" style="width:100%;"id="pc_desc_0_'+i+'" name="pc_desc_0_'+i+'" value="'+presta[i].pc_desc+'" /></td>';			
		}
		html+='<td><center>'+presta[i].cnt+'</center></td>';
		
		if(presta[i].cnt==0)
			html+='<td><center><img src="../../iconos/delete.png" style="cursor:pointer;" onClick="eliminar('+i+');" /></center></td>';
		else
			html+='<td>&nbsp;</td>';
					
		html+='</tr>';
		
	}	

	var clase=(i%2==0)?'tabla_fila':'tabla_fila2';		

	html+='<tr class="'+clase+'"><td style="width:100px;">';
	html+='<input type="text" id="codigo" name="codigo" style="width:100%;text-align:center;" />';
	html+='</td><td>';
	html+='<input type="text" id="desc" name="desc" style="width:100%;" />';
	html+='</td><td>&nbsp;</td><td>';
	html+='<center><img src="../../iconos/add.png"  style="cursor:pointer;" onClick="agregar_codigo();" /></center>';
	html+='</td></tr>';
	
	html+='</table>';

	$('prestaciones').innerHTML=html;
	
}

guardar_esp=function() {

	guardar_tabla();

	var params='&presta='+encodeURIComponent(presta.toJSON());

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

</script>

<body>

<form id='datos' name='datos' onSubmit='return false;'>

<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />

<div class='sub-content'>
<img src='../../iconos/layout_edit.png'>
<b><?php if($esp_id!=0) echo 'Editar'; else echo 'Crear nueva'; ?> Especialidad/Unidad</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Unidad:</td>
<td class='tabla_fila'>
<input type='text' id='esp_desc' name='esp_desc' size=30 value='<?php echo $esp['esp_desc']; ?>' />
</td></tr>
<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Cod. Interno:</td>
<td class='tabla_fila'>
<input type='text' id='esp_codigo_int' name='esp_codigo_int' size=10 value='<?php echo $esp['esp_codigo_int']; ?>' />
</td></tr>


<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Genera Proc./Ex&aacute;menes:</td>
<td class='tabla_fila'>
<input type='checkbox' id='proce' name='proce' <?php if($pro) echo 'CHECKED'; ?> />
</td></tr>

<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Genera Informe Cl&iacute;nico:</td>
<td class='tabla_fila'>
<input type='checkbox' id='informe' name='informe' 
<?php if($pro['esp_informe']=='t') echo 'CHECKED'; ?> />
</td></tr>

<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Asocia Orden de Atenci&oacute;n:</td>
<td class='tabla_fila'>
<input type='checkbox' id='orden' name='orden' 
<?php if($pro['esp_orden_atencion']=='t') echo 'CHECKED'; ?> />
</td></tr>


<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Equipos Asociados:</td>
<td class='tabla_fila'>
<textarea cols=60 rows=5 id='equipos' name='equipos'><?php echo $pro['esp_equipos']; ?></textarea>
</td></tr>


<tr><td style='text-align:right;' 
style='text-align:right;' class='tabla_fila2'>Campos Din&aacute;micos:</td>
<td class='tabla_fila'>
<textarea cols=60 rows=5 id='campos' name='campos'><?php echo $pro['esp_campos']; ?></textarea>
</td></tr>

</table>
</div>

<div class='sub-content2' id='prestaciones' name='prestaciones'>

</div>

<br />

<center>
<input type='button' value=' - Guardar Especialidad - ' 
onClick='guardar_esp();' />
</center>

</form>

</body>

</html>

<script>

	listar_prestaciones();

</script>