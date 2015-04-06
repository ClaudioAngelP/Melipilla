<?php 

	require_once('../../conectar_db.php');
	
	$convenio_id=$_GET['convenio_id']*1;
		
		
	$q = cargar_registros_obj("SELECT * FROM convenio_multa WHERE convm_convenio_id=$convenio_id ORDER BY convm_numero DESC");
	
	$convenio_licitacion = cargar_registro("SELECT convenio_licitacion FROM convenio WHERE convenio_id=$convenio_id");
	
?>
<script>
	
agregar_multa = function(id_convenio,nro_lic,tipo,multa_id){
    
    tipo=tipo*1;
    multa_id=multa_id*1;
    l=(screen.availWidth/2)-250;
    t=(screen.availHeight/2)-200;
    new_win = window.open('form_multas.php?'+'licitacion='+id_convenio+'&licitacion_nro='+nro_lic+'&tipo='+tipo+'&multa_id='+multa_id, 'win_multas',
                      'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                      'resizable=no, width=470, height=480');
   
    new_win.focus();
}


eliminar_multa = function(multa_id){
	
	confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar esta multa?'.unescapeHTML());

     if(confirma){

       var myAjax2 = new Ajax.Request(
		'elimina_multa.php',
		{
			method: 'post',
            parameters: 'multa_id='+(multa_id*1),
            onComplete: function(respuesta) {
                
				if(respuesta) {
					alert('Multa eliminada!');
					listado_multas();
              } else {
					alert('ERROR:\n\n'+respuesta.responseText);
              }
            }
        });
     }
	
}


listado_multas=function() {
	
		var params=$('convenio_id').serialize();		
	
		var myAjax=new Ajax.Updater(
			'listado_multas',
			'listado_multas.php',
			{  method:'post', parameters:params 	}	
			
		);
	
}

aparescan_adjuntos=function(i){
	
	if($('td_adjunto_'+i).style.display == 'none'){
		$('td_adjunto_'+i).style.display = 'block'
	}else{
		$('td_adjunto_'+i).style.display = 'none';
	}	
}
</script>

<html>
<title>Visualizar Multas</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>
<div class='sub-content'>
<img src='../../iconos/book.png'> <b>Convenio - Multas</b>
</div>
<div class='sub-content'>
	<center>
		<table>
			<tr><td>Licitaci&oacute;n:</td>
				<td><input type='hidden' name='convenio_id' id='convenio_id' value='<?php echo $convenio_id; ?>'>
					<b><?php echo $convenio_licitacion['convenio_licitacion']; ?></b></td></tr>
		</table>
	</center>
<div class='sub-content2' style='height:300px; overflow:auto;' id='listado_multas' name='listado_multas'>
<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>N&uacute;mero</td>
		<td>Estado</td>
		<td>Descripci&oacute;n</td>
		<td>Concepto</td>
		<td>Fecha Notificaci&oacute;n</td>
		<td>Fecha Descargos</td>
		<td>Fecha Resoluci&oacute;n</td>
		<td>N&uacute;m. Res.</td>
		<td>N&uacute;m. Exp.</td>
		<td>Monto (UTM)</td>
		<td>Adjunto</td>
		<?php if(_cax(48)){ ?><td colspan=2>Acci&oacute;n</td><?php } ?>
	</tr>

<?php 

	if($q)
	for($i=0;$i<sizeof($q);$i++) {
			
		switch($q[$i]['convm_estado']*1){
			case 1:
				$estado='En Tr&aacute;mite';
			break;
			case 2:
				$estado='Aplicada';
			break;
			case 3:
				$estado='Rebajada';
			break;
			case 4:
				$estado='Revocada';
			break;
		}
		
		switch($q[$i]['convm_concepto']*1){
			case 1:
				$concepto='Leve';
			break;
			case 2:
				$concepto='Grave';
			break;
		}
		
		list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['convm_adjunto']);
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
		print("<tr style='height:20px;' class='$clase'
					onMouseOver='this.className=\"mouse_over\";'
					onMouseOut='this.className=\"$clase\";'>");
					
		print("
				<td style='text-align:center;'>".$q[$i]['convm_numero']."</td>
				<td style='text-align:center;'>$estado</td>
				<td style='text-align:left;'>".$q[$i]['convm_descripcion']."</td>
				<td style='text-align:center;'>$concepto</td>
				<td style='text-align:center;'>".$q[$i]['convm_fecha_notificacion']."</td>
				<td style='text-align:center;'>".$q[$i]['convm_fecha_descargo']."</td>
				<td style='text-align:center;'>".$q[$i]['convm_resolucion']."</td>
				<td style='text-align:center;'>".$q[$i]['convm_numero_res']."</td>
				<td style='text-align:center;'>".$q[$i]['convm_nro_expediente']."</td>
				<td style='text-align:center;'>".number_format($q[$i]['convm_monto'],1,',','.')."</td>");
		
		
		
		$m = cargar_registro("SELECT * FROM multa_adjuntos WHERE multa_id=".$q[$i]['covnm_id']." LIMIT 1;");

		if($m){
			//print("<td style='text-align:center; cursor: pointer;' onClick='window.open(\"descargar_adjunto_multa.php?adjunto_id=".$q[$i]['covnm_id']."\", \"_self\");'><img src='../../iconos/application_put.png'><b><u>".$nombre."</u></b></td>");
			print("<td onclick='aparescan_adjuntos(".$i.")'><b>Con Adjuntos</b></td>");
		}else{
			print("<td><b>Sin Adjuntos</b></td>");
		}	
		
		if(_cax(48)){
			print("<td><center><img src='../../iconos/script_edit.png' style='cursor: pointer;'  onClick=agregar_multa('','',1,".$q[$i]['covnm_id'].");></center></td>
					<td><center><img src='../../iconos/delete.png' style='cursor: pointer;'  onClick='eliminar_multa(".$q[$i]['covnm_id'].")'></center></td>");
			
		}
		
		$adj =cargar_registros_obj("SELECT * FROM multa_adjuntos WHERE multa_id=".$q[$i]['covnm_id']);
		
			print("</tr>");
			print("<tr>");
			print("<td colspan=8>");
			print("<div id='td_adjunto_".$i."' style='display:none;'>");
		
		if($adj)
			for($j=0;$j<sizeof($adj);$j++) {
					
				list($nombre,$tipo,$peso,$md5)=explode('|',$adj[$j]['mad_adjunto']);
				print("<span  onClick='window.open(\"descargar_adjunto_multa.php?adjunto_id=".$adj[$j]['mad_id']."\", \"_self\");'>");
				print("<img src='../../iconos/application_put.png'>");
				print strtoupper("<b><u>".$nombre."</u></b>");
				print("</span><BR/>");
			}
		print("<div>");
		print("</td>");
		print("</tr>");
	} ?>
	
	</table></div>
<br /><br />
<center>
<a href='ver_convenio.php?convenio_id=<?php echo $convenio_id; ?>'>Volver Atr&aacute;s...</a>

</center>
</div>
</body>
</html>

