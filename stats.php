<?php 

	require_once('config.php');
	require_once('conectores/sigh.php');
	
	
	function graficar($vig, $ale, $ven, $total, $borde=0)  {
		
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
		
			$html="<table style='margin:1px;width:175px;height:10px;display:inline;$border' cellpadding=0 cellspacing=0>
						<tr>";
						
			$html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
			
			$html.="</tr></table>";
			
			return $html;
		
		}
			
		$html="<table style='margin:1px;width:175px;height:10px;display:inline;$border' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#218aec;'>&nbsp;</td>";
		if($ale>0) $html.="<td style='width:$ale%;background-color:#f6f90b;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff1717;'>&nbsp;</td>";
		if($resto>0) $html.="<td style='width:$resto%;background-color:white;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}
	
	$data=pg_query("
		SELECT 
		date_trunc('hours',logi_fecha) AS fecha, 
		count(*) AS total, 
		avg(logi_tiempo_proceso) AS prom,
		min(logi_tiempo_proceso) AS mini,
		max(logi_tiempo_proceso) AS maxi 
		FROM logs_integraciones 
		GROUP BY date_trunc('hours',logi_fecha)
		ORDER BY date_trunc('hours',logi_fecha) DESC;
	");

?>

<html>
<title>Monitoreo Integraci&oacute;n GIS - Trakcare</title>

<body style='font-family:Arial;background-color:skyblue;'>
<br>
<center>
<br><br>

<h2>Estad&iacute;sticas Mensajer&iacute;a Integraci&oacute;n<br /><u>GIS-Trakcare</u></h2>

<table style='width:600px;font-size:12px;' cellpadding=0 cellspacing=0>
	
<?php 

	$i=0;
	$max_maxi=70;
	$max_total=4000;
	$fecha='';

	while($r=pg_fetch_assoc($data)) {
		
		$fec=substr($r['fecha'],0,10);
		
		if($fec!=$fecha) {
			print("

			<tr style='background-color:#000000;color:#66ff66;text-align:center;font-weight:bold;'>
			<td colspan=6 style='font-size:20px;'>$fec</td>
			</tr>
			<tr style='background-color:#000000;color:#66ff66;text-align:center;font-weight:bold;'>
				<td>Fecha/Hora</td>
				<td>Total de Mensajes</td>
				<td>msec Promedio</td>
				<td>msec M&iacute;nimo</td>
				<td>msec M&aacute;ximo</td>
				<td style='width:200px;'>&nbsp;</td>
			</tr>
			
			");
			$fecha=$fec;
		}
		
		$color=(($i++)%2)==0?'dddddd':'eeeeee';
		
		print("<tr style='height:25px;background-color:#$color'>
		<td rowspan=2 style='text-align:center;font-weight:bold;font-size:14px;'>".substr($r['fecha'],0,16)."</td>
		<td rowspan=2 style='text-align:right;font-weight:bold;font-size:16px;color:#218aec'>".$r['total']."</td>
		<td rowspan=2 style='text-align:right;font-weight:bold;font-size:16px;color:red;'>".number_format($r['prom'],2,',','.')."</td>
		<td rowspan=2 style='text-align:right;'>".$r['mini']."</td>
		<td rowspan=2 style='text-align:right;'>".$r['maxi']."</td>
		<td style='text-align:left;background-color:white;padding-right:20px;'>
		".graficar($r['total'],0,0,$max_total,0)."
		</td></tr>
		<tr style='height:25px;background-color:#$color'>
		<td style='text-align:left;background-color:white;padding-right:20px;'>
		".graficar(0,0,$r['prom'],$max_maxi,0)."</td>
		</tr>");
		
	}

?>


</table>

</center>
</body>

</html>
