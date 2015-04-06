<?php 

	require_once("../../conectar_db.php");
	
	function graficar_totales($pendiente, $atrasado, $pagado) {
		
		$total=$pendiente+$atrasado+$pagado;
		
		if($total>0) {
			$a_pendiente=$pendiente*360/$total;
			$a_atrasado=$atrasado*360/$total;
			$a_pagado=$pagado*360/$total;
		} else {
			$svg='<svg width="80px" height="80px">';
			$svg.="<circle cx='40' cy='40' r='35' style='stroke:black; stroke-width: 2; fill:gray;' />";
			$svg.='</svg>';
			
			return $svg;
		}
		
		$svg='';
	
		$svg.='<svg width="80px" height="80px">';
		
		$angulo=0;
		
		if($a_pendiente>0) {
			
			$x1=40+(cos(deg2rad($angulo))*35);
			$y1=40+(sin(deg2rad($angulo))*35);
			
			$x2=40+(cos(deg2rad($angulo+$a_pendiente))*35);
			$y2=40+(sin(deg2rad($angulo+$a_pendiente))*35);
			
			if($a_pendiente>180) $la='1'; else $la='0';
			
			if($a_pendiente!=360)
				$svg.="<path d='M40,40 L$x1,$y1 A 35,35 0 $la,1 $x2,$y2 z' style='stroke:black; stroke-width: 2; fill:blue;' />";
			else
				$svg.="<circle cx='40' cy='40' r='35' style='stroke:black; stroke-width: 2; fill:blue;' />";
			
			
			
		
			$angulo+=$a_pendiente;
			
		}

		if($a_atrasado>0) {
			
			$x1=40+(cos(deg2rad($angulo))*35);
			$y1=40+(sin(deg2rad($angulo))*35);
			
			$x2=40+(cos(deg2rad($angulo+$a_atrasado))*35);
			$y2=40+(sin(deg2rad($angulo+$a_atrasado))*35);
			
			if($a_atrasado>180) $la='1'; else $la='0';
			
			if($a_atrasado<360) {
				$svg.="<path d='M40,40 L$x1,$y1 A 35,35 0 $la,1 $x2,$y2 z' style='stroke:black; stroke-width: 2; fill:red;' />";
			} else {
				$svg.="<circle cx='40' cy='40' r='35' style='stroke:black; stroke-width: 2; fill:red;' />";
			}
			
			
			$angulo+=$a_atrasado;
			
		}

		if($a_pagado>0) {
			
			$x1=40+(cos(deg2rad($angulo))*35);
			$y1=40+(sin(deg2rad($angulo))*35);
			
			$x2=40+(cos(deg2rad($angulo+$a_pagado))*35);
			$y2=40+(sin(deg2rad($angulo+$a_pagado))*35);
			
			if($a_pagado>180) $la='1'; else $la='0';
			
			if($a_pagado!=360)
				$svg.="<path d='M40,40 L$x1,$y1 A 35,35 0 $la,1 $x2,$y2 z' style='stroke:black; stroke-width: 2; fill:green;' />";
			else
				$svg.="<circle cx='40' cy='40' r='35' style='stroke:black; stroke-width: 2; fill:green;' />";
			
			$angulo+=$a_pagado;
			
		}
		
		$svg.='</svg>';
		
		return $svg;
			
	}
	
	$tipo=$_GET['tipo']*1;
	$prov_id=$_POST['prov_id']*1;
	
	if($tipo==1)
		$filtro='docp_autorizado IS NULL';
	else if($tipo==2)
		$filtro='docp_autorizado=2';
	else
		$filtro='true';
		
	if($prov_id==0) {
		$prov_w='true';
	} else {
		$prov_w='doc_prov_id='.$prov_id;
		$prov=cargar_registro("SELECT * FROM proveedor WHERE prov_id=$prov_id;", true);
	}
	
	
	$d=cargar_registros_obj("
	SELECT *, (total_det+total_ser) AS total_oc FROM (
	SELECT documento.doc_id AS real_doc_id, *, 
	(SELECT SUM(stock_subtotal) AS total FROM logs JOIN stock ON stock_log_id=log_id WHERE log_doc_id=doc_id)*doc_iva AS subtotal,
	COALESCE(docp_fecha, (doc_fecha_recepcion::date+'30 days'::interval))::date AS fecha_pago,
	COALESCE(docp_fecha, (doc_fecha_recepcion::date+'30 days'::interval))::date-CURRENT_DATE AS dias,
	COALESCE(docp_nro_factura, doc_num) AS num_doc,
	(SELECT SUM(ordetalle_subtotal) FROM orden_detalle WHERE ordetalle_orden_id=orden_id)*orden_iva AS total_det,
	(SELECT SUM(orserv_subtotal) FROM orden_servicios WHERE orserv_orden_id=orden_id)*orden_iva AS total_ser
	FROM documento
	LEFT JOIN proveedor ON doc_prov_id=prov_id
	LEFT JOIN documento_pagos USING (doc_id)
	LEFT JOIN orden_compra ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
	WHERE $filtro AND $prov_w
	ORDER BY doc_fecha_recepcion ASC
	) AS foo;
	");
	
	//doc_tipo=1 AND 
	
	ob_start();

?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>O.C.</td>
		<td>Fecha Recep.</td>
		<td>Doc.</td>
		<td>N&uacute;mero</td>
		<td>Monto O.C.</td>
		<td>Monto Recep.</td>
		<td>Autorizar</td>
		<td>Dias</td>
		<td>Estado</td>
	</tr>
	
<?php 

	$frmpago=array('(Pendiente...)','Autorizado','Rechazado');
	
	$total_pendiente=0;
	$total_atrasado=0;
	$total_pagado=0;

	if($d)
	for($i=0;$i<sizeof($d);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
				
		if($d[$i]['doc_tipo']=='1' OR $d[$i]['docp_nro_factura']*1>0) {
			
			$tipo_doc='FACTURA';

			$ing_factura=" <img src='iconos/magnifier.png' onClick='abrir_recep(".$d[$i]['real_doc_id'].");' style='cursor:pointer;width:12px;height:12px;' />";
			$color2='blue';
			
		} else {
		
			if($d[$i]['doc_tipo']=='0') $tipo_doc='GUIA';
			if($d[$i]['doc_tipo']=='2') $tipo_doc='BOLETA';
			if($d[$i]['doc_tipo']=='3') $tipo_doc='OTRO';
		
			$ing_factura="<img src='iconos/magnifier.png' onClick='abrir_recepcion(".$d[$i]['real_doc_id'].");' style='cursor:pointer;width:12px;height:12px;' /> <img src='iconos/pencil.png' style='cursor:pointer;width:12px;height:12px;' onClick='asociar_factura(".$d[$i]['real_doc_id'].");' />";
			$color2='red';
			
		}
		
		$htmlselect='';
		
		for($j=0;$j<sizeof($frmpago);$j++) {
			
			if($d[$i]['docp_autorizado']*1==$j) $sel='SELECTED'; else $sel='';
			
			$htmlselect.="<option value='$j' $sel >".$frmpago[$j].'</option>';
			
		}
				
		switch($d[$i]['docp_tipo_pago']*1) {
			
			case 0:
				if($d[$i]['dias']*1>3) {
					$icono='clock.png';
					$total_pendiente+=$d[$i]['subtotal']*1;
					
				} else if($d[$i]['dias']*1>=0) {
					$icono='error.png';
					$total_pendiente+=$d[$i]['subtotal']*1;
					
				} else {
					$icono='stop.png';
					$total_atrasado+=$d[$i]['subtotal']*1;
					
				}
				break;
			default:
				$icono='tick.png';
				$total_pagado+=$d[$i]['subtotal']*1;
			
		}
		
		if($d[$i]['orden_id']*1!=0) {
			$ver_orden=$d[$i]['orden_numero']." <img src='iconos/magnifier.png' style='cursor:pointer;width:12px;height:12px;' onClick='abrir_orden(".$d[$i]['orden_id'].");' />";
			$color='green';
		} else {
			$ver_orden="<i>(Sin Asignar...)</i> <img src='iconos/pencil.png' style='cursor:pointer;width:12px;height:12px;' onClick='asociar_oc(".$d[$i]['real_doc_id'].");' />";	
			$color='red';
		}
		
		$total_oc=round(($d[$i]['total_det']*1)+($d[$i]['total_ser']*1));
		$total_doc=round($d[$i]['subtotal']*1);
		
		if($total_oc==$total_doc) {
			$color3='green';
		} else if($total_oc>$total_doc) {
			$color3='gray';
		} else {
			$color3='red';
		}
		
		print("
		<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;color:$color;'>$ver_orden</td>
		<td style='text-align:center;'>".$d[$i]['doc_fecha_recepcion']."</td>
		<td style='text-align:center;color:$color2;'>".$tipo_doc." $ing_factura</td>
		<td style='text-align:center;font-weight:bold;'>".$d[$i]['num_doc']."</td>
		<td style='text-align:right;font-weight:bold;'>$ ".number_format($total_oc,0,',','.').".-</td>
		<td style='text-align:right;font-weight:bold;color:$color3;'>$ ".number_format($total_doc,0,',','.').".-</td>
		<td style='text-align:center;font-weight:bold;'><center>
		<select id='fpago_".$d[$i]['real_doc_id']."' name='fpago_".$d[$i]['real_doc_id']."' style='width:200px;' onChange='ver_guardar(".$d[$i]['real_doc_id'].");'>
		$htmlselect
		</select></center>
		</td>
		<td style='text-align:center;font-weight:bold;'>".$d[$i]['dias']."</td>
		<td><center><img src='iconos/disk.png' id='guardar_".$d[$i]['real_doc_id']."' style='cursor:pointer;display:none;' onClick='guardar_aut(".$d[$i]['real_doc_id'].");' /> <img src='iconos/$icono' id='icono_".$d[$i]['real_doc_id']."' /> </center></td>
		</tr>
		");
		
	}

?>	
	
</table>

<?php 

	$html=ob_get_contents();
	ob_end_clean();

?>

<center>

<?php if($prov) { ?>

<table style='width:50%;font-size:12px;'>
<tr class='tabla_header'><td colspan=2>Datos del Proveedor</td></tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;width:25%;'>RUT:</td>
		<td class='tabla_fila' style='font-size:14px;font-weight:bold;'><?php echo $prov['prov_rut']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Nombre:</td>
		<td class='tabla_fila' style='font-size:12px;font-weight:bold;'><?php echo $prov['prov_glosa']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Direcci&oacute;n:</td>
		<td class='tabla_fila'><?php echo $prov['prov_direccion'],', '.$prov['prov_ciudad']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Tel&eacute;fono:</td>
		<td class='tabla_fila'><?php echo $prov['prov_fono']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>e-mail:</td>
		<td class='tabla_fila'><?php echo $prov['prov_mail']; ?></td>
	</tr>
</table>

<?php } ?>

<table>
	<tr>
		<td style='text-align:right;'> 

<?php echo graficar_totales($total_pendiente, $total_atrasado, $total_pagado); ?>


		</td>
		<td> 



<table style='width:450px;'>

<?php if($total_pendiente>0) { ?>
	<tr>
		<td style='text-align:right;font-size:16px;' class='tabla_header'>Total Pendiente:</td>
		<td style='text-align:right;font-size:24px;color:black;' class='tabla_fila'>$ <?php echo number_format($total_pendiente,0,',','.'); ?>.-</td>
	</tr>
<?php }

if($total_atrasado>0) { ?>
	<tr>
		<td style='text-align:right;font-size:16px;' class='tabla_header'>Total Atrasado:</td>
		<td style='text-align:right;font-size:24px;color:red;' class='tabla_fila'>$ <?php echo number_format($total_atrasado,0,',','.'); ?>.-</td>
	</tr>
<?php }

if($total_pagado>0) { 
?>
	<tr>
		<td style='text-align:right;font-size:16px;' class='tabla_header'>Total Pagado:</td>
		<td style='text-align:right;font-size:24px;color:green;' class='tabla_fila'>$ <?php echo number_format($total_pagado,0,',','.'); ?>.-</td>
	</tr>
<?php } ?>
</table>





		</td>
	</tr>
</table>


<?php echo $html; ?>

</center>

<script>

	docs=<?php echo json_encode($d); ?>;

</script>
