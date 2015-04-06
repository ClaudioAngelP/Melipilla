<?php 

	require_once("../../conectar_db.php");
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$funcs=$_POST['funcionarios']*1;

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}

	
	if(isset($_POST['xls'])) {
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"aperturas_cierres_caja.xls\";");
		$xls=1; 
	} else 
		$xls=0;
	
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
	
	
	if($funcs!=-1) {
		$func_w='func_id='.$funcs;
	} else {
		$func_w='true';
	}	
	
	$l=cargar_registros_obj("
		SELECT * FROM apertura_cajas
		JOIN funcionario USING (func_id)
		LEFT JOIN arqueo_cajas_detalle USING (ac_id)
		WHERE ac_fecha_apertura::date >= '$fecha1' AND 
		ac_fecha_apertura::date <= '$fecha2' AND $func_w
		ORDER BY ac_id	
	", true);
	
?>


<table style='width:100%;'>
<tr class='tabla_header'>
<?php if(!$xls) { ?><td>Sel.</td><?php } ?>
<td>Correlativo</td>
<td>R.U.N.</td>
<td>Funcionario</td>
<td>Fecha/Hora Apertura</td>
<td>Fecha/Hora Cierre</td>
<?php if(!$xls) { ?><td>Ver</td><?php } ?>
</tr>

<?php 

	if($l)
	for($i=0;$i<sizeof($l);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($l[$i]['ac_fecha_cierre']!='')
			$color='';
		else
			$color='blue';
		
		
		if(substr($l[$i]['ac_fecha_cierre'],0,10)!=substr($l[$i]['ac_fecha_apertura'],0,10) AND $l[$i]['ac_fecha_cierre']!='')
			$color='red';

		print("
			<tr class='$clase' style='color:$color'>
		");

		if(!$xls) {

			if($l[$i]['aqc_id']=='') {
                        	print("<td><center><input type='checkbox' id='ac_".$l[$i]['ac_id']."' name='ac_".$l[$i]['ac_id']."' /></center></td>");
			} else {
				print("<td><center>&nbsp;</center></td>");
			}

                }

		print("
			<td style='text-align:right;font-weight:bold;font-size:18px;'>".$l[$i]['ac_id']."</td>
			<td style='text-align:right;'>".formato_rut($l[$i]['func_rut'])."</td>
			<td style='text-align:left;'>".$l[$i]['func_nombre']."</td>
			<td style='text-align:center;font-weight:bold;'>".substr($l[$i]['ac_fecha_apertura'],0,16)."</td>
			<td style='text-align:center;font-weight:bold;'>".substr($l[$i]['ac_fecha_cierre'],0,16)."</td>
		");
			
		if(!$xls)
			print("<td><center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='informe_caja(".$l[$i]['ac_id'].");' /></center></td>");
			
		print("	
			</tr>
		");
	
	
	}

?>

</table>
