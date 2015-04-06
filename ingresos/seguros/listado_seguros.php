<?php 

	require_once("../../conectar_db.php");
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);

	$tipo=pg_escape_string($_POST['tipo_seguros']*1);
	$compania=pg_escape_string($_POST['compania']);
	$estado=pg_escape_string($_POST['estado']*1);
	
	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}
	
	function select_estados($val) {
	
		$estados=Array('Ingresado','En Proceso','Rechazado','Cobrado','Anulado');
		
		$html="";
		
		for($i=0;$i<sizeof($estados);$i++) {
			if($val==$i) $sel='SELECTED'; else $sel='';
			$html.="<option value='$i' $sel>".htmlentities($estados[$i])."</option>";
		}
		
		return $html;
	
	}

	
	if(isset($_POST['xls']) AND $_POST['xls']*1==1) {
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"listado_seguros.xls\";");
		$xls=1; 
	} else 
		$xls=0;
	
	function dinero($num) {
		GLOBAL $xls;
		if(!$xls) return ('$ '.number_format($num,0,',','.').'.-');
		else			return floor($num*1);
	}	
	
	function numero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.'));
		else			return floor($num*1);
	}	
	
	
	$l=cargar_registros_obj("
		SELECT *  
		FROM seguros 
		JOIN tipos_seguro ON seg_tipo=ts_id
		LEFT JOIN boletines USING (bolnum)
		LEFT JOIN pacientes USING (pac_id)
		WHERE fecha_ingreso::date >= '$fecha1' AND fecha_ingreso::date <= '$fecha2'	
	");
	
?>

<table style='width:100%;font-size:12px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Tipo Seguro</td>
<td>Compa&ntilde;&iacute;a</td>
<td>Fecha</td>
<td>Nro. Parte/P&oacute;liza</td>
<td>RUT</td>
<td>Nombre</td>
<td>Serie/Patente</td>
<td>Comprobante</td>
<td>RUT Paciente</td>
<td>Nombre Paciente</td>
<td>Estado</td>
<td>Adjuntos</td>
</tr>

<?php 

	$total=0;

	for($i=0;$i<sizeof($l);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
		
		$estados=select_estados($l[$i]['estado']*1);
	
		print("
		<tr class='$clase'>
		<td style='text-align:center;'>".($l[$i]['ts_nombre'])."</td>
		<td style='text-align:left;'>".($l[$i]['compania'])."</td>
		<td style='text-align:left;'>".($l[$i]['fecha'])."</td>
		<td style='text-align:left;'>".($l[$i]['poliza'])."</td>
		<td style='text-align:right;'>".($l[$i]['rut'])."</td>
		<td style='text-align:left;'>".($l[$i]['nombre'])."</td>
		<td style='text-align:left;'>".($l[$i]['serie'])."</td>
		<td style='text-align:center;'>".vboletin($l[$i]['bolnum'], $xls)."</td>
		<td style='text-align:right;font-weight:bold;'>".($l[$i]['pac_rut'])."</td>
		<td style='text-align:left;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
		<td style='text-align:left;'>
		<select id='' name=''>
		".$estados."
		</select>
		</td>
		<td style='text-align:left;white-space:nowrap;'>
		<center>
		<img src='iconos/folder.png' style='cursor:pointer;' onClick='alert(\"!\");' />(0)
		</center>
		</td>
		</tr>		
		");
	
		$total+=$l[$i]['monto']*1;
		
	
	}

?>

</table>

