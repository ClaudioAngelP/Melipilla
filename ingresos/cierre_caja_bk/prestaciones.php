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
		SELECT * FROM boletin_detalle
		JOIN boletines USING (bolnum)
		WHERE bolfec::date >= '$fecha1' AND 
		bolfec::date <= '$fecha2' AND $func_w
		ORDER BY bdet_codigo
	", true);


	$grupos=Array();
	$subgrupos=Array();
	$pr=Array();
	
	for($i=0;$i<sizeof($l);$i++) {
		$grupo=substr($l[$i]['bdet_codigo'],0,2);
		$subgrupo=substr($l[$i]['bdet_codigo'],0,4);
		$presta=$l[$i]['bdet_codigo'];
		
		
		if(strlen($presta)==7 AND !strstr($presta,'CRS') AND !strstr($presta,'DP')) {

			if(!isset($grupos[$grupo])) {
				$grupos[$grupo]=Array();
				$grupos[$grupo]['cantidad']=0;
				$grupos[$grupo]['valor']=0;
				$grupos[$grupo]['copago']=0;
			}
		
			$grupos[$grupo]['cantidad']+=$l[$i]['bdet_cantidad']*1;
			$grupos[$grupo]['valor']+=$l[$i]['bdet_valor_total']*$l[$i]['bdet_cantidad'];
			$grupos[$grupo]['copago']+=$l[$i]['bdet_valor']*$l[$i]['bdet_cantidad'];

			if(!isset($subgrupos[$subgrupo])) {
				$subgrupos[$subgrupo]=Array();
				$subgrupos[$subgrupo]['cantidad']=0;
				$subgrupos[$subgrupo]['valor']=0;
				$subgrupos[$subgrupo]['copago']=0;
			}
			
			$subgrupos[$subgrupo]['cantidad']+=$l[$i]['bdet_cantidad']*1;
			$subgrupos[$subgrupo]['valor']+=$l[$i]['bdet_valor_total']*$l[$i]['bdet_cantidad'];
			$subgrupos[$subgrupo]['copago']+=$l[$i]['bdet_valor']*$l[$i]['bdet_cantidad'];
		
		}

		if(!isset($pr[$presta])) {
			$pr[$presta]=Array();
			$pr[$presta]['cantidad']=0;
			$pr[$presta]['valor']=0;
			$pr[$presta]['copago']=0;
		}
		
		$pr[$presta]['cantidad']+=$l[$i]['bdet_cantidad']*1;
		$pr[$presta]['valor']+=$l[$i]['bdet_valor_total']*$l[$i]['bdet_cantidad'];
		$pr[$presta]['copago']+=$l[$i]['bdet_valor']*$l[$i]['bdet_cantidad'];
		
	}


	
?>


<table style='width:100%;'>
<tr class='tabla_header'>
<td>C&oacute;digo</td>
<td>Cantidad</td>
<td>Precio $</td>
<td>Copago $</td>
</tr>

<?php 

	$c=0;

	foreach($grupos AS $grupo => $detalle) {
		$clase=$c++%2==0?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='text-align:center;font-size:24px;'>$grupo</td>
		<td style='text-align:right;'>".numero($detalle['cantidad'])."</td>
		<td style='text-align:right;'>".dinero($detalle['valor'])."</td>
		<td style='text-align:right;'>".dinero($detalle['copago'])."</td>
		</tr>
		");
	}

	foreach($subgrupos AS $subgrupo => $detalle) {
		$clase=$c++%2==0?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='text-align:center;font-size:20px;'>$subgrupo</td>
		<td style='text-align:right;'>".numero($detalle['cantidad'])."</td>
		<td style='text-align:right;'>".dinero($detalle['valor'])."</td>
		<td style='text-align:right;'>".dinero($detalle['copago'])."</td>
		</tr>
		");
	}

	foreach($pr AS $codigo => $detalle) {
		$clase=$c++%2==0?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='text-align:center;font-size:16px;'>$codigo</td>
		<td style='text-align:right;'>".numero($detalle['cantidad'])."</td>
		<td style='text-align:right;'>".dinero($detalle['valor'])."</td>
		<td style='text-align:right;'>".dinero($detalle['copago'])."</td>
		</tr>
		");
	}

?>

</table>
