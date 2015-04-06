<?php 

	require_once('../conectar_db.php');

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}


	$pac_id=$_POST['pac_id']*1;
	
	$creditos=cargar_registros_obj("
		SELECT *, crefec::date AS crefec FROM creditos 
		WHERE pac_id=$pac_id ORDER BY creditos.crefec DESC	
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
											WHERE crecod=".$creditos[$i]['crecod']);
			
			if( $_chk['pago'] >= ($creditos[$i]['cretot']+$creditos[$i]['crepie']) ) {
				pg_query("UPDATE creditos SET cretip='CP' 
								WHERE crecod=".$creditos[$i]['crecod']);	
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
			WHERE cuotas.crecod=".$creditos[$i]['crecod']."
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

		print("<tr class='$clase'>
		<td style='text-align:center;' $rspan>

		$cestado<br /><br />

		<input type='button' onClick='abrir_credito(".$creditos[$i]['crecod'].");' 
		value='".$creditos[$i]['crecod']."'>
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
