<?php 
    require_once('../../conectar_db.php');
    $conveniod_id=$_GET['conveniod_id']*1;
    $d=cargar_registros_obj("
    SELECT * FROM convenio_detalle 
    LEFT JOIN articulo USING (art_id)
    WHERE conveniod_id=$conveniod_id
    ORDER BY art_glosa;
    ", true);
    $convenio_id=$d[0]['convenio_id']*1;
    $c=cargar_registro("SELECT * FROM convenio JOIN proveedor USING (prov_id) WHERE convenio_id=$convenio_id", true);
?>
<script>
    visualizar_documento=function(doc_id) {
        top=Math.round(screen.height/2)-225;
        left=Math.round(screen.width/2)-350;
        win = window.open('../../visualizar.php?doc_id='+doc_id,
        'win_documento', 'toolbar=no, location=no, directories=no, status=no, '+
	'menubar=no, scrollbars=yes, resizable=yes, width=700, height=450, '+
	'top='+top+', left='+left);
        win.focus();
    }
</script>
<html>
    <title>Visualizar Convenio</title>
    <?php cabecera_popup('../..'); ?>
    <body class='fuente_por_defecto popup_background'>
        <center>
            <h2><?php echo $c['convenio_nombre']; ?></h2>
            <br />
            Proveedor: <?php echo '<b>'.$c['prov_rut'].'</b> '.$c['prov_glosa']; ?>
            <br />
            <h3>Monto Total: $<?php echo number_format($c['convenio_monto']*1,0,',','.'); ?>.-</h3>
            <h3>Art&iacute;culo: <?php echo $d[0]['art_codigo'].' <b>'.$d[0]['art_glosa'].'</b>'; ?></h3>
            <h3>Categoria: <?php echo '<b>'.$c['convenio_categoria'].'</b>'; ?></h3>
            <?php 
                if($c['convenio_tipo_licitacion'] == 1) echo '<h3>Tipo Convenio: <b>Apoyo Cl&iacute;nico</b></h3>';
                if($c['convenio_tipo_licitacion'] == 2) echo '<h3>Tipo Convenio: <b>Recursos F&iacute;sicos</b></h3>';
                if($c['convenio_tipo_licitacion'] == 3) echo '<h3>Tipo Convenio: <b>Prestaci&oacute;n de Servicios Cl&iacute;nicos</b></h3>';
            ?>
        </center>
        <table style='width:100%;font-size:12px;'>
            <tr class='tabla_header'>
                <td>#</td>
		<td>Fecha</td>
		<td>Documento</td>
		<td>N&uacute;mero</td>
		<td>&Oacute;rden</td>
		<td>Cantidad</td>
		<td>PU Conv. ($)</td>
		<td>PU Recep. ($)</td>
		<td>Subtotal ($)</td>
		<td>Ver</td>
            </tr>
	<?php
	$total=0;
        $total_descuento=0;
        if($c['convenio_categoria']!='servicios') {
            $d=cargar_registros_obj("
            SELECT * FROM convenio
            JOIN convenio_detalle USING (convenio_id)
            JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
            JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
            JOIN logs ON log_tipo=1 AND log_doc_id=doc_id
            JOIN stock ON stock_log_id=log_id AND stock_art_id=art_id
            WHERE conveniod_id=$conveniod_id AND orden_fecha BETWEEN convenio_fecha_inicio AND convenio_fecha_final
            ORDER BY log_fecha ASC
            ");
        }
        else {
                
            $d=cargar_registros_obj("
            SELECT *,serv_cant as stock_cant,serv_subtotal as stock_subtotal FROM convenio 
            JOIN convenio_detalle USING (convenio_id) 
            JOIN orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id 
            JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
            JOIN logs ON log_tipo=50 AND log_doc_id=doc_id 
            JOIN servicios ON serv_log_id=log_id AND serv_art_id=art_id 
            WHERE conveniod_id=$conveniod_id AND orden_fecha 
            BETWEEN convenio_fecha_inicio AND convenio_fecha_final ORDER BY log_fecha ASC 
            ");
        }
	if($d){
            
            $doc_id_ant="";
            $doc_iva=1;
            for($i=0;$i<sizeof($d);$i++) {
                
                $clase=$i%2==0?'tabla_fila':'tabla_fila2';
		
                if($d[$i]['stock_cant']*1>0)
                    $unit=($d[$i]['stock_subtotal']*1)/$d[$i]['stock_cant'];
		else
                    $unit=0;
			
		$total+=$d[$i]['stock_subtotal']*1;
			
		switch($d[$i]['doc_tipo']) {
                    case 0: $tipo='Boleta'; break;
                    case 1: $tipo='Factura'; break;
                    case 2: $tipo='Gu&iacute;a de Despacho'; break;
                    case 3: $tipo='Pedido'; break;
		}
		
		if($unit==0) {
                    $color='blue';
		} elseif($unit==$d[$i]['conveniod_punit']) {
                    $color='green';
		} else {
                    $color='red';
		}

				
		print("
                <tr class='$clase'>
                    <td>".($i+1)."</td>
                    <td style='text-align:center;'>".substr($d[$i]['log_fecha'],0,19)."</td>
                    <td style='font-weight:bold;'>".$tipo."</td>
                    <td style='text-align:center;font-weight:bold;'>".$d[$i]['doc_num']."</td>
                    <td style='text-align:center;font-weight:bold;'>".$d[$i]['doc_orden_desc']."</td>
                    <td style='text-align:right;'>".number_format($d[$i]['stock_cant']*1,0,',','.')."</td>
                    <td style='text-align:right;'>$".number_format($d[$i]['conveniod_punit']*1,0,',','.').".-</td>
                    <td style='text-align:right;font-weight:bold;color:$color'>$".number_format($unit,0,',','.').".-</td>
                    <td style='text-align:right;font-weight:bold;'>$".number_format($d[$i]['stock_subtotal']*1,0,',','.').".-</td>
                    <td>
                    <center>
                        <img src='../../iconos/magnifier.png' onClick='visualizar_documento(".$d[$i]['doc_id'].");' style='cursor:pointer;' />
                    </center>
                    </td>
                </tr>
                ");
                if($doc_id_ant!=$d[$i]['doc_id']){
                    $total_descuento+=$d[$i]['doc_descuento']*1;
                    $doc_id_ant=$d[$i]['doc_id'];
                }
                $doc_iva=$d[$i]['doc_iva']*1;
            }
        }
	print("
        <tr class='tabla_header'>
	<td colspan=8 style='text-align:right;'>Total Descuentos Neto:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format($total_descuento,0,',','.').".-</td>
	<td>&nbsp;</td>
	</tr>
	<tr class='tabla_header'>
	<td colspan=8 style='text-align:right;'>Subtotal Neto:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format(($total-$total_descuento),0,',','.').".-</td>
	<td>&nbsp;</td>
	</tr>
        <tr class='tabla_header'>
	<td colspan=8 style='text-align:right;'>I.V.A.:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format(((($total-$total_descuento)*$doc_iva)-($total-$total_descuento)),0,',','.').".-</td>
	<td>&nbsp;</td>
	</tr>
	<tr class='tabla_header'>
	<td colspan=8 style='text-align:right;'>Total:</td>
	<td style='text-align:right;font-weight:bold;'>$".number_format(($total-$total_descuento)*$doc_iva,0,',','.').".-</td>
	<td>&nbsp;</td>
	</tr>");
?>	
</table>
<br /><br />
<center>
<a href='ver_convenio.php?convenio_id=<?php echo $convenio_id; ?>'>Volver Atr&aacute;s...</a>

</center>

</body>
</html>
