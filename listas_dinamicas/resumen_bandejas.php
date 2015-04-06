<?php 

	require_once('../conectar_db.php');
	
    function graficar($vig, $prox, $ven, $total)  {
		
		if(($vig*1)+($prox*1)+($ven*1)>0) {
		
			$vig=round($vig*100/$total);
			$prox=round($prox*100/$total);
			$ven=round($ven*100/$total);
			$resto=100-($vig+$prox+$ven);
		
		} else {
		
			$html="<table style='width:300px;border:0px;' cellpadding=0 cellspacing=0>
						<tr>";
						
			$html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
			
			$html.="</tr></table>";
			
			return $html;
		
		}
			
		$html="<table style='width:300px;border:0px;' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#22CC22;'>&nbsp;</td>";
		if($prox>0) $html.="<td style='width:$prox%;background-color:#BBBB00;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff4444;'>&nbsp;</td>";
		if($resto>0) $html.="<td style='width:$resto%;background-color:#dddddd;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}
	
	//SELECT DISTINCT codigo_bandeja, nombre_bandeja FROM lista_dinamica_proceso ORDER BY codigo_bandeja;
		
		$r=cargar_registros_obj("
		SELECT * FROM (
		
			SELECT *, 
				(SELECT COUNT(*) FROM monitoreo_ges_registro AS mgr WHERE mgr.monr_subclase=foo.codigo_bandeja AND monr_estado=0 AND (CURRENT_DATE-monr_fecha::date)::integer BETWEEN lista_plazo_alerta_amarilla AND lista_plazo_alerta_roja) AS total_amarilla,
				(SELECT COUNT(*) FROM monitoreo_ges_registro AS mgr WHERE mgr.monr_subclase=foo.codigo_bandeja AND monr_estado=0 AND (CURRENT_DATE-monr_fecha::date)::integer>lista_plazo_alerta_roja) AS total_roja,
				(SELECT COUNT(*) FROM monitoreo_ges_registro AS mgr WHERE mgr.monr_subclase=foo.codigo_bandeja AND monr_estado=0) AS total,
				codigo_bandeja IN ('".str_replace(',',"','",_cav(49))."') AS permiso
			FROM (
				SELECT DISTINCT codigo_bandeja, nombre_bandeja, lista_plazo_alerta_amarilla, lista_plazo_alerta_roja
				FROM lista_dinamica_bandejas
			) AS foo

		) AS foo2
		ORDER BY total DESC, total_roja DESC, total_amarilla DESC, codigo_bandeja ASC;
		", true);
		
		$total=$r[0]['total']*1;
?>

<table style='width:100%;font-size:14px;'>

<tr class='tabla_header'>
<td style='width:40%;'>Bandeja</td>
<td>Sin Alerta</td>
<td>Alerta Amarilla</td>
<td>Alerta Roja</td>
<td>Subtotal</td>
<td>Gr&aacute;fico</td>
</tr>

<?php 

	$tverde=0;
	$tamarilla=0;
	$troja=0;
	$ttotal=0;

	for($i=0;$i<sizeof($r);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$r[$i]['total']*=1;
		$r[$i]['total']-=($r[$i]['total_amarilla']*1+$r[$i]['total_roja']*1);
		
		$p=($r[$i]['permiso']=='t');

		$tverde+=$r[$i]['total'];
		$tamarilla+=$r[$i]['total_amarilla'];
		$troja+=$r[$i]['total_roja'];
		$ttotal+=($r[$i]['total_roja']+$r[$i]['total_amarilla']+$r[$i]['total']);
		
		print("
			<tr class='$clase' 
		");	
		
		if($p)
		print("style='cursor:pointer;' 
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'
			onClick='$(\"lista_id\").value=\"".($r[$i]['codigo_bandeja'])."\"; cargar_lista();'");	
			
		print(">
			<td style='font-weight:".($p?'bold':'normal').";color:".($p?'black':'blue')."'>".$r[$i]['nombre_bandeja']."</td>
			<td style='font-weight:bold;text-align:right;color:green;'>".$r[$i]['total']."</td>
			<td style='font-weight:bold;text-align:right;color:#BBBB00;'>".$r[$i]['total_amarilla']."</td>
			<td style='font-weight:bold;text-align:right;color:#ff4444;'>".$r[$i]['total_roja']."</td>
			<td style='font-weight:bold;text-align:right;color:black;'>".($r[$i]['total_roja']+$r[$i]['total_amarilla']+$r[$i]['total'])."</td>
			<td><center>".graficar($r[$i]['total'],$r[$i]['total_amarilla']*1,$r[$i]['total_roja']*1, $total)."</center></td>
			</tr>
		");
		
	}

	print("<tr class='tabla_header'><td>Totales</td>
	 <td style='font-weight:bold;text-align:right;color:green;'>".$tverde."</td>
                        <td style='font-weight:bold;text-align:right;color:#BBBB00;'>".$tamarilla."</td>
                        <td style='font-weight:bold;text-align:right;color:#ff4444;'>".$troja."</td>
                        <td style='font-weight:bold;text-align:right;color:black;'>".($ttotal)."</td><td>&nbsp;</td></tr>
	");

?>

</table>
