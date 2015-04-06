<?php 

	require_once('../../conectar_db.php');
	
	$rnpt_id=$_GET['rnpt_id']*1;

	$q=cargar_registro("
		SELECT * FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		LEFT JOIN funcionario ON func_id=rnpt_func_id
		WHERE rnpt_id=$rnpt_id;
	", true);
	
	
?>
<script>

	eliminar_npt = function(id){
		
	if(!confirm('Est&aacute; seguro de eliminar esta receta NPT?'.unescapeHTML())) return;
	var myAjax10 = new Ajax.Request(
    'eliminar_rnpt.php',
    {
      method: 'get',
      parameters: 'rnpt_id='+id,
      onComplete: function(r) {
      
          if(r.responseText=='OK') {
            alert('Receta Eliminada Exitosamente!');
            window.close();
          } else
            alert('ERROR:\n\n'+r.responseText);
            
      }
    }
    );	
	
	}
	
</script>

<html>
<title>Visualizar Receta NPT</title>

<?php cabecera_popup('../..'); ?>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../../iconos/database.png'>
<b>Receta de Nutrici&oacute;n Parenteral</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>
	
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>RUT:</td>
		<td class='tabla_fila' style='font-weight:bold;'><?php echo $q['pac_rut']; ?></td>
	</tr>
	
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Ficha:</td>
		<td class='tabla_fila' style='font-weight:bold;'><?php echo $q['pac_ficha']; ?></td>
	</tr>
	
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Nombre:</td>
		<td class='tabla_fila'><?php echo trim($q['pac_nombres'].' '.$q['pac_appat'].' '.$q['pac_apmat']); ?></td>
	</tr>

	<tr>
		<td style='text-align:right;' class='tabla_fila2'>M&eacute;dico:</td>
		<td class='tabla_fila'><?php echo trim('<b>'.$q['doc_rut'].'</b> '.$q['doc_nombres'].' '.$q['doc_paterno'].' '.$q['doc_materno']); ?></td>
	</tr>

	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Servicio:</td>
		<td class='tabla_fila'><?php echo trim($q['centro_nombre']); ?></td>
	</tr>

	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Peso (gr):</td>
		<td class='tabla_fila'><?php echo number_format($q['rnpt_peso_gr'],0,',','.'); ?></td>
	</tr>

	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Diagn&oacute;stico:</td>
		<td class='tabla_fila'><?php echo trim('<b>'.$q['rnpt_diag_cod']).'</b> '.$q['rnpt_diagnostico']; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Digitada por:</td>
		<td class='tabla_fila'><?php echo trim($q['func_nombre'].' '.$q['pac_paterno'].' '.$q['pac_materno']); ?></td>
	</tr>
	
</table>

</div>
<div class='sub-content2'>
<table style='width:100%;'>
	<tr class='tabla_header'>
		<td style='width:60%;'>Componente</td>
		<td>Cantidad</td>
		<td>Unidad</td>
	</tr>
	
<?php 

	$d=explode("\n",$q['rnpt_detalle']);
	
	for($i=0;$i<(sizeof($d)-1);$i++) {
		
		$c=explode('|',$d[$i]);
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
			<tr class='$clase'> 
			<td style='text-align:right;'>".$c[0]."&nbsp;:&nbsp;</td>
			<td style='text-align:right;font-weight:bold;'>".number_format($c[1],2,',','.')."</td>
			<td>".$c[2]."</td>
			</tr>
		");
		
	}

?>	
</table>
</div>
<?php if(_cax(40)){ ?>
<center>
    <div class='boton'>
		<table><tr><td>
		<img src='../../iconos/cross.png'>
		</td><td>
		<a href='#' onClick='eliminar_npt(<?php echo $rnpt_id; ?>);'>
		Eliminar Receta NPT...</a>
		</td></tr></table>
		</div>
</center>
<?php } ?>
</body>

</html>
