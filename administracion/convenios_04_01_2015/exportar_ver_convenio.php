<?php

	require_once('../../conectar_db.php');
	$convenio_id=$_GET['convenio_id']*1;
	header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"convenio_$convenio_id_".date('d-m-Y his')."\".xls\";");
	
	pg_query("

	UPDATE convenio_detalle AS c1 SET 
	
	conveniod_monto_utilizado=(
			SELECT SUM(stock_subtotal) FROM convenio AS c2
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id 
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c2.convenio_id=c1.convenio_id
	), 
	
	conveniod_cantidad_recepcionada=(
			SELECT SUM(stock_cant) FROM convenio AS c4
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c4.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c4.convenio_id=c1.convenio_id
	), 
	
	conveniod_monto_comprometido=(
			SELECT SUM(ordetalle_subtotal) FROM convenio AS c3
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c3.convenio_id=c1.convenio_id and orden_estado!=3 and orden_estado_portal!='OC Removida'
	),
	conveniod_cant_com=(
	SELECT SUM(ordetalle_cant) FROM convenio AS c5
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c5.convenio_id AND c1.art_id=convenio_detalle.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c5.convenio_id=c1.convenio_id  and orden_estado!=3 and orden_estado_portal!='OC Removida'
			and orden_estado_portal!='OC Requerida para Cancelación'
			)	
	
	
	WHERE convenio_id=$convenio_id;
	
	");
	
	
	
	$c=cargar_registro("
		SELECT * FROM convenio 
		JOIN proveedor USING (prov_id)
		LEFT JOIN funcionario USING (func_id)
		WHERE convenio_id=$convenio_id
	", true);
	
	$d=cargar_registros_obj("
		SELECT * FROM convenio_detalle 
		JOIN articulo USING (art_id)
		WHERE convenio_id=$convenio_id
		ORDER BY art_glosa;
	", true);
	
	if(isset($_GET['xls'])) {
	    header("Content-type: application/vnd.ms-excel");
      	header("Content-Disposition: filename=\"HistorialPedidos--.XLS\";");
  	}

?>

<html>
<title>Visualizar Convenio</title>

<?php cabecera_popup('../..'); ?>


<script>

function ver_detalle(conveniod_id)  {
	
	window.open('ver_convenio_detalle.php?conveniod_id='+conveniod_id,
    'win_talonarios');

	
}


</script>

<body class='fuente_por_defecto popup_background'>

<?php
if(!isset($_GET['xls'])){
print("<center>");
}?>
<h1><?php echo $c['convenio_licitacion']; ?></h1>
<h2><?php echo $c['convenio_nombre']; ?></h2>
<h3>Proveedor: <?php echo '<b>'.$c['prov_rut'].'</b> '.$c['prov_glosa']; ?><br />
Monto Total: $<?php echo number_format($c['convenio_monto']*1,0,',','.'); ?>.-</h3>

<table style='width:100%;font-size:11px;'>

<tr>

<td style='text-align:right;' class='tabla_fila2'>ID Licitaci&oacute;n:</td>

<td colspan=3>

<?php echo $c['convenio_licitacion']; ?>

</td>

</tr>


<tr>

<td style='text-align:right;' class='tabla_fila2'>Nombre Convenio:</td>

<td colspan=3>

<?php echo $c['convenio_nombre']; ?>

</td>

</tr>

<tr>

<td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Bases:</td>

<td>

<?php //echo $c['convenio_nro_res_aprueba']; ?>

</td>

<td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

<td>

<?php echo $c['convenio_fecha_aprueba']; ?>

</td>

</tr>


<tr>

<td style='text-align:right;' class='tabla_fila2'>Nro. Res. Adjudica:</td>

<td>

<?php echo $c['convenio_nro_res_adjudica']; ?>

</td>

<td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

<td>

<?php echo $c['convenio_fecha_adjudica']; ?>

</td>

</tr>

<?php if($c['convenio_aprueba']=='contrato'){ 
	}elseif($c['convenio_aprueba']=='prorroga'){ ?>
<tr>

<td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Prorroga:</td>

<td>

<?php echo $c['convenio_nrores_prorroga']; ?>

</td>

<td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

<td>

<?php echo $c['convenio_fecha_resprorroga']; ?>

</td>

</tr>
<?php }elseif($c['convenio_aprueba']=='aumento'){ ?>

<tr  id='td_res_aumento' name='td_res_aumento' style='display:none;'>

<td style='text-align:right;' class='tabla_fila2'>Num. Res. Aprueba Aumento:</td>

<td>

<?php echo $c['convenio_aumento_aprueba']; ?>

</td>

<td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

<td>

<?php echo $c['convenio_aumento_fecha']; ?>

</tr>
<tr>
</td>

<td style='text-align:right;' class='tabla_fila2'>Monto Aumento:</td>

<td colspan='3'>

<?php echo $c['convenio_aumento_fecha']; ?>

</td>
</tr>

<?php } ?>


<tr id='td_res_apruebac' name='td_res_apruebac' style='display:none;'>

<td style='text-align:right;' class='tabla_fila2'>Nro. Res. Aprueba Contrato:</td>

<td>

<?php echo $c['convenio_nro_res_contrato']; ?>

</td>

<td style='text-align:right;' class='tabla_fila2'>Fecha:</td>

<td>

<?php echo $c['convenio_fecha_resolucion']; ?>

</td>

</tr>


<tr><td style='text-align:right;' class='tabla_fila2'>
Proveedor:
</td><td colspan=3>

<b><?php echo $c['prov_rut']; ?></b>

<?php echo $c['prov_glosa']; ?>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Administrador Contrato:
</td><td colspan=3>

<b><?php echo $c['func_rut']; ?></b>

<?php echo $c['func_nombre']; ?>

</td></tr>


<tr><td style='text-align:right;' class='tabla_fila2'>
e-mail(s) Contacto:
</td><td colspan=3>

<?php echo $c['convenio_mails']; ?>


</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Monto $:
</td><td>

<?php echo $c['convenio_monto']; ?>


</td><td style='text-align:right;' class='tabla_fila2'>
Plazo de Entrega (D&iacute;as):
</td><td>

<?php echo $c['convenio_plazo']; ?>

</td></tr>



<tr><td style='text-align:right;' class='tabla_fila2'>
Fecha Inicio:
</td><td>

<?php echo $c['convenio_fecha_inicio']; ?>

</td><td style='text-align:right;' class='tabla_fila2'>
Fecha T&eacute;rmino:
</td><td>

<?php echo $c['convenio_fecha_final']; ?>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
N&uacute;mero Boleta Garant&iacute;a:
</td><td>

<?php echo $c['convenio_nro_boleta']; ?>


</td><td style='text-align:right;' class='tabla_fila2'>
Fecha Venc. Boleta Garant&iacute;a:
</td><td>

<?php echo $c['convenio_fecha_boleta']; ?>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Banco Boleta Garant&iacute;a:
</td><td>

<?php echo $c['convenio_banco_boleta']; ?>

</td><td style='text-align:right;' class='tabla_fila2'>
Monto Boleta $:
</td><td>

<?php echo $c['convenio_monto_boleta']; ?>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Multa (Descripci&oacute;n):
</td><td colspan=3>

<?php echo $c['convenio_multa']; ?>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Comentarios:
</td><td colspan=3>

<?php echo $c['convenio_comentarios']; ?>

</td></tr>
<?php
if(!isset($_GET['xls'])){
    	
?>
<tr><td style='text-align:right;'>Adjuntos:</td>
	<table>
       <?php echo $adj_con; ?>
    </table>
</tr>
<? } ?>
</table><br>
<table style='width:100%;font-size:12px;'>
	<tr class='tabla_header'>
		<td>C&oacute;digo</td>
		<td style='width:50%;'>Art&iacute;culo</td>
		<td>Comprometido ($)</td>
		<td>Unidades Estimadas</td>	
		<td>Unidades Comprometidas</td>
		<td>Unidades Recepcionadas</td>
		<td>PU Conv.($)</td>
		<td>PU Recep.($)</td>
		<td>Devengado ($)</td>
		<td>&nbsp;</td>
	</tr>
	
<?php 

	$mc=0; $md=0;

	if($d)
	for($i=0;$i<sizeof($d);$i++) {
		
		$clase=$i%2==0?'tabla_fila':'tabla_fila2';

		if($d[$i]['conveniod_cantidad_recepcionada']!=0)
			$unit=$d[$i]['conveniod_monto_utilizado']/$d[$i]['conveniod_cantidad_recepcionada'];
		else
		$unit=0;
		
		if($d[$i]['conveniod_cant_com']!=0){
			if($d[$i]['conveniod_cant']!=0)
			{
				$unidUtil=$d[$i]['conveniod_cant_com']/$d[$i]['conveniod_cant'];
			}else{
				$unidUtil=0;
			}		
		}else{
			$unidUtil=0;
		}
			
		$mc+=$d[$i]['conveniod_monto_comprometido']*1.19;
		$md+=$d[$i]['conveniod_monto_utilizado']*1.19;
		
		if($unit==0) {
			$color='blue';
		} elseif($unit==$d[$i]['conveniod_punit']) {
			$color='green';
		} else {
			$color='red';
		}
	
		if($unidUtil==0) {
			$colorU='blue';
		} elseif($unidUtil==1) {
			$colorU='green';
		} else {
			$colorU='red';
		}
	
		print("
			<tr class='$clase'>
			<td style='text-align:right;'>".$d[$i]['art_codigo']."</td>
			<td>".$d[$i]['art_glosa']."</td>
			<td style='text-align:right;'>$".number_format($d[$i]['conveniod_monto_comprometido']*1,0,',','.').".-</td>
			<td style='text-align:right;'>".number_format($d[$i]['conveniod_cant']*1,0,',','.')."</td>
			<td style='text-align:right;color:$colorU'>".number_format($d[$i]['conveniod_cant_com']*1,0,',','.')."</td>
			
			<td style='text-align:right;'>".number_format($d[$i]['conveniod_cantidad_recepcionada']*1,0,',','.')."</td>
			
			<td style='text-align:right;'>$".number_format($d[$i]['conveniod_punit']*1,0,',','.').".-</td>
			<td style='text-align:right;font-weight:bold;color:$color'>$".number_format($unit,0,',','.').".-</td>
			<td style='text-align:right;'>$".number_format($d[$i]['conveniod_monto_utilizado']*1,0,',','.').".-</td>
			<td><center>
			<img src='../../iconos/magnifier.png' onClick='ver_detalle(".$d[$i]['conveniod_id'].");' style='cursor:pointer;' />
			</center></td>
			</tr>
		");
		
	}
	
	$pc=$mc*100/$c['convenio_monto']*1;
	$pd=$md*100/$c['convenio_monto']*1;
	
	print("
	<tr class='tabla_header'>
	<td colspan=2 style='text-align:right;'>Total Comprometido:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format($mc,0,',','.').".-</td>
	<td colspan=2 style='text-align:right;'>Total Devengado:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format($md,0,',','.').".-</td>
		<td>&nbsp;</td>
	</tr>
	<tr class='tabla_header'>
	<td colspan=2 style='text-align:right;'>Comprometido:</td>
	<td style='text-align:right;font-weight:bold;'>".number_format($pc,2,',','.')."%</td>
	<td colspan=2 style='text-align:right;'>Devengado:</td>
	<td style='text-align:right;font-weight:bold;'>".number_format($pd,2,',','.')."%</td>
		<td>&nbsp;</td>
	</tr>
	");

?>	
	
	
</table>
