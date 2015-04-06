<?php 

	require_once('../../conectar_db.php');

	$dias=$_POST['dias']*1;
	$signo=$_POST['signo'];
	$comp=$_POST['comp'];
	$orden=$_POST['orden']*1;
	$func=$_POST['funcionarios']*1;
	
	if(isset($_POST['xls'])) {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: filename=\"listado_morosos.xls\";");
    $xls=1;
	} else {
	 $xls=0;
	}
	
	if($orden==0) {
		$qorden="ORDER BY deuda DESC";
	} else {
		$qorden="ORDER BY ultimo_pago";
	}

	if($func!=-1) {
		$func_w="COALESCE(func_id, 1)=$func";
	} else {
		$func_w='true';
	}
	
	$fecha_w="cuofec::date $comp (current_date $signo $dias)";	
	
	$lista=cargar_registros_obj("
	SELECT DISTINCT foo.*, pacientes.* FROM (
		
		SELECT 
		*,
		(SELECT SUM(cuopag::integer) FROM cuotas WHERE crecod=creditos.crecod) 
		AS pagado,
		(SELECT SUM( (cuomon::bigint) - COALESCE(cuopag::bigint, 0) ) FROM cuotas 
			WHERE crecod=creditos.crecod
			AND $fecha_w
		) AS deuda,
		(SELECT MAX(cuofecpag) FROM cuotas
			WHERE crecod=creditos.crecod)::date AS ultimo_pago,
		(SELECT MIN(bolnum) FROM boletines
		   WHERE boletines.crecod=creditos.crecod) AS bolnum_crea
		FROM creditos WHERE cretip='N'
	) AS foo
	 
	JOIN pacientes USING (pac_id)
	LEFT JOIN boletines ON bolnum_crea=bolnum
	LEFT JOIN comunas USING (ciud_id)
	WHERE deuda > 0 AND $func_w	
	$qorden
	");

?>

<table style='width:100%;'>
<tr class='tabla_header' <?php if($xls) echo 'bgcolor="#dddddd;"'; ?> >
<td>#</td>
<td>R.U.T.</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<?php 
	if($xls) {
		print("
		<td>Direcci&oacute;n</td>		
		<td>Comuna</td>		
		<td>Tel&eacute;fono</td>		
		");
	}
?>
<td>Total Cr&eacute;dito</td>
<td>&Uacute;ltimo Pago</td>
<td>Morosidad</td>
<?php 
	if($xls) {
		print("
		<td>Saldo Pendiente</td>		
		");
	}
?>
</tr>

<?php 

	$total=0;

	for($i=0;$i<sizeof($lista);$i++) {
	
		($i%2==0)?$clase='tabla_fila':$clase='tabla_fila2';	
	
		echo "<tr class='$clase' ";
		
		if(!$xls)		
		echo "style='height:30px;cursor:pointer;'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'
			onClick='abrir_cliente(".$lista[$i]['clirut'].");'";
			
		echo " ><td style='text-align:center;'>".($i+1)."</td>			
			<td style='text-align:right;font-weight:bold;' align='right'>
			".$lista[$i]['clirut']."-".$lista[$i]['clidv']."</td>		
			<td style='font-weight:bold;'>".htmlentities($lista[$i]['clipat'])."</td>		
			<td>".htmlentities($lista[$i]['climat'])."</td>		
			<td style='font-weight:bold;'>".htmlentities($lista[$i]['clinom'])."</td>";
			

		if($xls) {
			echo "<td align='left'>".htmlentities($lista[$i]['clidir'])."</td>";
			echo "<td align='left'>".htmlentities($lista[$i]['comdes'])."</td>";
			echo "<td align='left'>".htmlentities($lista[$i]['clifon'])."</td>";
		}
					
		echo "<td style='text-align:right;' align='right'>";
		
		if(!$xls)
			echo "$".number_format($lista[$i]['cretot'],0,',','.').".-";
		else
			echo $lista[$i]['cretot']*1;
			
		echo "</td>		
			<td style='text-align:center;' align='center'>".($lista[$i]['ultimo_pago'])."</td>			
			<td style='text-align:right;' align='right'>";
		if(!$xls)
			echo "$".number_format($lista[$i]['deuda'],0,',','.').".-";
		else
			echo $lista[$i]['deuda']*1;

		echo "</td>";

		if($xls)
			echo '<td align="right">'.(($lista[$i]['crepie']*1)+($lista[$i]['cretot']*1)-$lista[$i]['pagado']).'</td>';		

			
		echo "</tr>";
		
		$total+=$lista[$i]['deuda'];	
	
	}
	
	if(!$xls)
		echo '<tr class="tabla_header"><td colspan=7 style="text-align:right;">Total:</td>
		<td style="text-align:right;">$'.number_format($total,0,',','.').'.-</td></tr>';
	else 
		echo '<tr class="tabla_header"><td colspan=10 style="text-align:right;">Total:</td>
		<td style="text-align:right;">'.($total).'.-</td></tr>';

?>

</table>