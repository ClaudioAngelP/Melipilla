<?php 

	require_once('../conectar_db.php');

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;white-space:nowrap;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}
	
	function vdevolucion($dev_id,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;white-space:nowrap;text-decoration:underline;font-weight:bold;color:blue;' onClick='imprimir_boletin($dev_id);'>
							".number_format($dev_id,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($dev_id*1);
			}
	
	}

	function dinero($num) {
		GLOBAL $xls;
		if(!$xls) return ('$'.number_format($num,0,',','.').'.-');
		else			return floor($num*1);
	}	
	
	function numero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.'));
		else			return floor($num*1);
	}	



	$pac_id=$_POST['pac_id']*1;


	$l=cargar_registros_obj("
		SELECT *, bolfec::date AS bolfec, (
			SELECT SUM(monto) FROM cheques
			WHERE cheques.bolnum=boletines.bolnum		
		) AS cheques, 
		array(
			SELECT (
			SELECT COALESCE(SUM(monto),0) AS monto FROM forma_pago
			WHERE forma_pago.bolnum=boletines.bolnum
			AND forma_pago.tipo=fpago_id
			) FROM tipo_formas_pago ORDER BY fpago_id
		) AS formas_pago,
		(SELECT count(*) FROM seguros WHERE seguros.bolnum=boletines.bolnum) AS nro_seguros,
				(SELECT SUM(monto_total) FROM devolucion_boletines WHERE devolucion_boletines.bolnum=boletines.bolnum) AS monto_total
		
		FROM boletines 
		LEFT JOIN apertura_cajas ON bolfec BETWEEN ac_fecha_apertura AND COALESCE(ac_fecha_cierre, CURRENT_TIMESTAMP) AND apertura_cajas.func_id=boletines.func_id
		WHERE pac_id=$pac_id
		ORDER BY bolnum DESC	
	");
	
?>

<table style='width:100%;'>
<tr class='tabla_header'><td><b><u>Documentos Emitidos</u></b></td></tr>
</table>

<table style='width:100%;font-size:12px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Fecha</td>
<td>CC</td>
<td>Nro. Doc.</td>
<td>Efectivo</td>
<td>Devoluciones</td>
<td>Cheques</td>

<?php 
	$fp=cargar_registros_obj("SELECT * FROM tipo_formas_pago ORDER BY fpago_id");

	$tpag=array();
	
	for($i=0;$i<sizeof($fp);$i++) {
		echo '<td>'.htmlentities($fp[$i]['fpago_nombre']).'</td>';
		$tpag[$i]=0;
	}
?>

<td>Seguros</td>
<td>Sub Total</td>
</tr>

<?php 

	$efectivo=0;$cheques=0;$total=0;$seguros=0;$devoluciones=0;

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$fpag=explode( ',', substr($l[$i]['formas_pago'],1,-1) );
		
		$ofp=0;
		for($k=0;$k<sizeof($fp);$k++) $ofp+=$fpag[$k]*1;
			
		$befectivo=$l[$i]['bolmon']-$l[$i]['cheques']-$ofp;	
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
		
		if($l[$i]['anulacion']=='')
			$color='';
		else
			$color='red;text-decoration:line-through';
	
		print("
			<tr class='$clase' style='color:$color'>
			<td style='text-align:center;'>".$l[$i]['bolfec']."</td>
			<td style='text-align:center;'>".$l[$i]['ac_id']."</td>
			<td style='text-align:center;'>".vboletin($l[$i]['bolnum'], $xls)."</td>
			<td style='text-align:right;'>".dinero($befectivo,0,',','.')."</td>
			<td style='text-align:right;'>".dinero($l[$i]['monto_total'],0,',','.')."</td>
			<td style='text-align:right;'>".dinero($l[$i]['cheques'])."</td>
		");

		for($k=0;$k<sizeof($fp);$k++) {
			print("<td style='text-align:right;'>".dinero($fpag[$k])."</td>");
			if($l[$i]['anulacion']=='')
				$tpag[$k]+=$fpag[$k]*1;
		}
		
		print("
			<td style='text-align:center;'>".($l[$i]['nro_seguros'])."</td>
			<td style='text-align:right;font-weight:bold;'>".dinero($l[$i]['bolmon'])."</td>
			</tr>		
		");
	
		if($l[$i]['anulacion']=='') {
		
			$seguros+=$l[$i]['nro_seguros'];
			$efectivo+=$befectivo;
			$devoluciones+=$l[$i]['monto_total']*1;
			$cheques+=$l[$i]['cheques']*1;
			$total+=$l[$i]['bolmon']*1+$l[$i]['monto_total']*1;
		
		}
		
	
	}

	print("
	<tr class='tabla_header' style='font-weight:bold;'>
	<td style='text-align:right;' colspan=3>Totales:</td>
	<td style='text-align:right;'>".dinero($efectivo)."</td>	
	<td style='text-align:right;'>".dinero($devoluciones)."</td>	
	<td style='text-align:right;'>".dinero($cheques)."</td>	
	");

	for($i=0;$i<sizeof($fp);$i++)
		print("<td style='text-align:right;'>".dinero($tpag[$i])."</td>");


	print("
		<td style='text-align:center;'>".($seguros)."</td>	
		<td style='text-align:right;'>".dinero($total)."</td>	
	</tr>	
	
	");

?>

</table>

<?php

	
	$creditos=cargar_registros_obj("
		SELECT *, crefec::date AS crefec, creditos.crecod AS _crecod FROM creditos 
		LEFT JOIN boletines ON boletines.crecod=creditos.crecod AND pagare AND bolmon=0
		WHERE creditos.pac_id=$pac_id ORDER BY creditos.crefec DESC	
	");
	
	//print_r($creditos);
	
	//print_r($cuotas);

	print("<table style='width:100%;'><tr class='tabla_header'>
	<td>Fecha Ingreso</td>	
	<td>Total</td>	
	<td>Pie</td>	
	<td>Saldo Cr&eacute;dito</td>	
	<td>Fecha Pago</td>	
	<td>Fecha Bol.</td>	
	<td>Atraso (D&iacute;as)</td>	
	<td>Bolet&iacute;n</td>	
	<td>Abono</td>	
	<td>&nbsp;</td>	
	</tr>");

	if($creditos)
	for($i=0;$i<sizeof($creditos);$i++) {
	
		($i%2==0)?$clase='tabla_fila':$clase='tabla_fila2';	

		if($creditos[$i]['cretip']=='N') {

			$_chk=cargar_registro("SELECT SUM(cuopag::bigint) AS pago FROM cuotas 
											WHERE crecod=".$creditos[$i]['_crecod']);
			
			if( $_chk['pago'] >= ($creditos[$i]['cretot']+$creditos[$i]['crepie']) ) {
				pg_query("UPDATE creditos SET cretip='CP' 
								WHERE crecod=".$creditos[$i]['_crecod']);	
				$creditos[$i]['cretip']='CP';					
			}
		
		}
		
		$q="
			SELECT 
			*,
			cuofec::date as cuofec,
			cuofecpag::date as cuofecpag,
			(cuofecpag::date-cuofec::date) as atraso,
			(now()::date-cuofec::date) AS atraso2,
			COALESCE(bolfec::date,cuofec::date) AS bolfec 
			FROM cuotas 
			LEFT JOIN boletines USING (bolnum)
			WHERE cuotas.crecod=".$creditos[$i]['_crecod']."
			ORDER BY cuotas.cuofec, cuotas.cuofecpag	
		";
		
		$cuotas=cargar_registros_obj($q);
		
		$rspan='rowspan='.(sizeof($cuotas)+3);

		switch($creditos[$i]['cretip']) {
			case 'CD': $cestado='CERRADO x DESCUENTO'; break;
			case 'CP': $cestado='CERRADO Y CANCELADO'; break;
			case 'A': $cestado='ANULADO'; break;
			default: $cestado='VIGENTE'; break;
		}
		
		if($creditos[$i]['anulacion']!='') {
			$cestado='ANULADO';
		}

		print("<tr class='$clase'>
		<td style='text-align:center;' $rspan>

		$cestado<br /><br />

		<input type='button' onClick='abrir_credito(".$creditos[$i]['_crecod'].");' 
		value='".$creditos[$i]['_crecod']."'>
		<br /><br />
		".$creditos[$i]['crefec']."
		</td>
		<td style='text-align:right;font-weight:bold;' $rspan>
		$".number_format($creditos[$i]['cretot']+$creditos[$i]['crepie'],0,',','.').".-</td>
		<td style='text-align:right;' $rspan>
		$".number_format($creditos[$i]['crepie'],0,',','.').".-</td>
		<td style='text-align:right' $rspan>
		$".number_format($creditos[$i]['cretot'],0,',','.').".-</td>
		");
		
		$total=0; $deuda=0; $saldo=$creditos[$i]['cretot']+$creditos[$i]['crepie'];		
		
		for($j=0;$j<sizeof($cuotas);$j++) {
		
			if($j>0) echo '<tr class="'.$clase.'">';
			
			if($cuotas[$j]['cuofecpag']!='') {			

			echo '<td style="text-align:center;">'.$cuotas[$j]['cuofec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['bolfec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['atraso'].'</td>
					<td style="text-align:center;">
					'.vboletin($cuotas[$j]['bolnum']).'</td>
					<td style="text-align:right;color:green;">
					$'.number_format($cuotas[$j]['cuopag']*1,0,',','.').'.-</td>
					<td style="text-align:center;">
					<img src="iconos/tick.png"> 
					</td>';

			$total+=$cuotas[$j]['cuopag']*1;
			$saldo-=$cuotas[$j]['cuopag']*1;

			} else {

			$f=explode('/',$cuotas[$j]['cuofec']);
			$fp=@mktime(0,0,0,$f[1],$f[0],$f[2]);
			$fn=mktime(0,0,0);
			
			if($fp<=$fn) {
				$color='red';
				$deuda+=$cuotas[$j]['cuomon']*1; $icono='cross';
			} else {
				$color='blue'; $icono='clock';
			}

			echo '<td style="text-align:center;">'.$cuotas[$j]['cuofec'].'</td>
					<td style="text-align:center;">(n/a)</td>
					<td style="text-align:center;">'.$cuotas[$j]['atraso2'].'</td>
					<td style="text-align:center;">
					(n/a)</td>
					<td style="text-align:right;color:'.$color.'">
					$'.number_format($cuotas[$j]['cuomon'],0,',','.').'.-</td>
					<td style="text-align:center;">
					<img src="iconos/'.$icono.'.png">					
					</td>';
			
			}
					
			echo	'</tr>';		
		
					
		}	
		
		echo '<tr class="'.$clase.'" 
				style="font-weight:bold;">
				<td style="text-align:center;" colspan=4>Total Pagado</td>
				<td style="text-align:right;">
				$'.number_format($total,0,',','.').'.-</td><td>&nbsp;</td></tr>
				<tr class="'.$clase.'" 
				style="font-weight:bold;">
				<td style="text-align:center;" colspan=4>Morosidad</td>
				<td style="text-align:right;color:red;">
				$'.number_format($deuda,0,',','.').'.-</td><td>&nbsp;</td></tr>
				<tr class="'.$clase.'" 
				style="font-weight:bold;">
				<td style="text-align:center;" colspan=4>Saldo Pendiente</td>
				<td style="text-align:right;color:blue;">
				$'.number_format($saldo,0,',','.').'.-</td><td>&nbsp;</td></tr>';
	
	}
	
	echo '</table>';

?>
