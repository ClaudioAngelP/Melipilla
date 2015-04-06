<?php 

	require_once('../conectar_db.php');
	
    function graficar2($vig, $ale, $ven, $total, $borde=0)  {
		
		switch($borde) {
			case 0:	$border='border:0px;'; break;
			case 1:	$border='border:1px solid #999999;'; break;
			case 2:	$border='border:1px solid black;'; break;
		}
		
		if(($vig*1)+($ale*1)+($ven*1)>0) {
		
			$vig=round($vig*100/$total,1);
			$ale=round($ale*100/$total,1);
			$ven=round($ven*100/$total,1);
			$resto=100-($vig+$ale+$ven);
		
		} else {
		
			$html="<table style='width:250px;$border' cellpadding=0 cellspacing=0>
						<tr>";
						
			$html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
			
			$html.="</tr></table>";
			
			return $html;
		
		}
			
		$html="<table style='width:250px;$border' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#218aec;'>&nbsp;</td>";
		if($ale>0) $html.="<td style='width:$ale%;background-color:#f6f90b;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff1717;'>&nbsp;</td>";
		if($resto>0) $html.="<td style='width:$resto%;background-color:#bbbbbb;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}
	
	//SELECT DISTINCT codigo_bandeja, nombre_bandeja FROM lista_dinamica_proceso ORDER BY codigo_bandeja;
		
		$totalvig=0;
		$totalale=0;
		$totalven=0;
		
		$r=cargar_registros_obj("
		SELECT * FROM (
		SELECT id_condicion, nombre_condicion, 
		SUM(CASE WHEN estado=0 THEN 1 ELSE 0 END) AS vigentes,
		SUM(CASE WHEN estado=1 THEN 1 ELSE 0 END) AS alerta,
		SUM(CASE WHEN estado=2 THEN 1 ELSE 0 END) AS vencidas,
		COUNT(*) AS subtotal FROM (
        SELECT 
        *, 
		(CURRENT_DATE)-mon_fecha_limite AS dias,
		trim(pst_patologia_interna) AS pst_patologia_interna,
		trim(pst_garantia_interna) AS pst_garantia_interna,
		(CASE 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
			  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1 
		ELSE 0 END) AS estado        

        FROM monitoreo_ges 
        JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
        LEFT JOIN monitoreo_ges_registro USING (mon_id)
        LEFT JOIN lista_dinamica_condiciones ON id_condicion=monr_clase::integer
        WHERE NOT mon_estado AND (monr_estado=0 OR monr_estado IS NULL)
        ) AS foo
        GROUP BY id_condicion, nombre_condicion
        ) AS foo2
        ORDER BY (vencidas+alerta+vigentes) DESC, nombre_condicion;
        
		", true);
		
		$total=($r[0]['vigentes']*1)+($r[0]['vencidas']*1);
?>

<input type='hidden' id='filtro_cond' name='filtro_cond' value='2' />

<table style='width:100%;font-size:14px;'>

<tr class='tabla_header'>
<td style='width:5%;'>Sel.</td>
<td style='width:40%;'>Condici&oacute;n</td>
<td>Vigentes</td>
<td>Alerta</td>
<td>Vencidos</td>
<td>Subtotal</td>
<td>Gr&aacute;fico</td>
</tr>

<?php 

	for($i=0;$i<sizeof($r);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($r[$i]['vencidas']==0) $color='#999999'; else $color='#ff4444';
		if($r[$i]['alerta']==0) $color2='#999999'; else $color2='#ff811b';
		
		if($r[$i]['nombre_condicion']=='')
		$r[$i]['nombre_condicion']='<i>(Sin Clasificar...)</i>';

		$totalvig+=$r[$i]['vigentes']*1;
		$totalale+=$r[$i]['alerta']*1;
		$totalven+=$r[$i]['vencidas']*1;
		
		print("
			<tr class='$clase' style='cursor:pointer;' 
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td><center>");
		
		if($r[$i]['id_condicion']*1!=0)
			print("<input type='checkbox' id='chk_cnd_".$r[$i]['id_condicion']."' name='chk_cnd_".$r[$i]['id_condicion']."' value='1' onClick='$(\"filtro_cond\").value=\"2\";' />");
		else
			print("<input type='checkbox' id='chk_cnd_".$r[$i]['id_condicion']."' name='chk_cnd_".$r[$i]['id_condicion']."' value='1' onClick='$(\"filtro_cond\").value=\"2\";' DISABLED />");
		
		print("</center></td>
			<td style='font-weight:bold;width:40%;'>".$r[$i]['nombre_condicion']."</td>
			<td style='font-weight:bold;text-align:right;color:#4b5cc3;'>".$r[$i]['vigentes']."</td>
			<td style='font-weight:bold;text-align:right;color:$color2;'>".$r[$i]['alerta']."</td>
			<td style='font-weight:bold;text-align:right;color:$color;'>".$r[$i]['vencidas']."</td>
			<td style='font-weight:bold;text-align:right;color:#000000;'>".($r[$i]['vigentes']+$r[$i]['alerta']+$r[$i]['vencidas'])."</td>
			<td><center>".graficar2($r[$i]['vigentes']*1,$r[$i]['alerta']*1,$r[$i]['vencidas']*1, $total)."</center></td>
			</tr>
		");
		
	}


	if($total_ven==0) $color='green'; else $color='#ff4444';
		
	print("
		<tr class='tabla_header'>
		<td>&nbsp;</td>
		<td style='font-weight:bold;width:40%;'>Totales</td>
		<td style='font-weight:bold;text-align:right;color:#4b5cc3;'>".$totalvig."</td>
		<td style='font-weight:bold;text-align:right;color:#ff811b;'>".$totalale."</td>
		<td style='font-weight:bold;text-align:right;color:red;'>".$totalven."</td>
		<td style='font-size:16px;'><center><b>".($totalvig+$totalale+$totalven)."</b></center></td>
		<td>&nbsp;</td>
		</tr>
	");

?>

</table>
