<?php

require_once('../../conectar_db.php');

$conv_id=$_REQUEST['convenio_id']*1;

$q = cargar_registros_obj("SELECT * FROM convenio_multa WHERE convm_convenio_id=$conv_id ORDER BY convm_numero DESC");

?>
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

<?php if($q)
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
			print("<td><b>Con Adjuntos</b></td>");
		}else{
			print("<td><b>Sin Adjuntos</b></td>");
		}	
		
		if(_cax(48)){
			print("<td><center><img src='../../iconos/script_edit.png' style='cursor: pointer;'  onClick=agregar_multa('','',1,".$q[$i]['covnm_id'].");></center></td>
					<td><center><img src='../../iconos/delete.png' style='cursor: pointer;'  onClick='eliminar_multa(".$q[$i]['covnm_id'].")'></center></td>");
			
		}
		print("</tr>");
	} ?>
	
</table>
