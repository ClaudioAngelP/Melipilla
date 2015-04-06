<?php 

	require_once('../conectar_db.php');
	
	$mon_id=$_GET['mon_id']*1;
	
	$modo=(isset($_GET['modo']) AND $_GET['modo']*1==1);

	$m=cargar_registro("SELECT *,
	(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias
	FROM monitoreo_ges_registro 
	JOIN monitoreo_ges USING (mon_id)
	WHERE mon_id=$mon_id", true);
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='".$m['mon_rut']."' LIMIT 1", true);
	
	$mr=cargar_registros_obj("SELECT *, date_trunc('second', monr_fecha) AS fecha FROM monitoreo_ges_registro 
						LEFT JOIN lista_dinamica_bandejas ON codigo_bandeja=monr_subclase 
						JOIN lista_dinamica_condiciones ON id_condicion=monr_clase::bigint
						LEFT JOIN funcionario ON monr_func_id=func_id 
						WHERE mon_id=$mon_id ORDER BY monr_fecha DESC;", true);
						
if(!$modo) {

?>

<html>

<title>Visualizar Caso #<?php echo $mon_id; ?></title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<input type='hidden' id='mon_id' name='mon_id' value='<?php echo $mon_id; ?>' />

<table style='width:100%;font-size:14px;'>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>R.U.T.:</td>
<td class='tabla_fila' style='font-size:14px;' colspan=3><b><?php echo $m['mon_rut']; ?></b> [<i>Ficha:</i> <b><u><?php echo $pac['pac_ficha']; ?></u></b>]
<!--- <img src='../../iconos/magnifier.png' onClick='abrir_ficha(<?php echo $pac['pac_id']*1; ?>);' style='cursor:pointer;' /> -->
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre:</td>
<td class='tabla_fila' style='font-weight:bold;' colspan=3><?php echo $m['mon_nombre']; ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Inicio:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_inicio']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha L&iacute;mite:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_limite']; ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Patolog&iacute;a:</td>
<td class='tabla_fila' colspan=3 style='font-weight:bold;'><i><?php echo $m['mon_patologia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Garant&iacute;a:</td>
<td class='tabla_fila' colspan=3 style='font-weight:bold;'><i><?php echo $m['mon_garantia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Estado Actual:</td>

<?php 
	
	if($m['dias']*1>0) { $color='red'; $estado='Vencida'; } else { $color='green'; $estado='Vigente'; }
		
	echo '<td class="tabla_fila" style="color:'.$color.';" colspan=3>Garant&iacute;a '.$estado.'</td>';
	
?>

</tr>


</table>


<div class='sub-content'>
<img src='../iconos/clock.png' />
Historial de Eventos
</div>

<div class='sub-content2'>

<?php } // END IF MODO ?>

<?php ob_start(); ?>

<table style='width:100%;font-size:12px;'>

<?php 

	$campos_especiales='';

	for($i=0;$i<sizeof($mr);$i++) {
		if($mr[$i]['monr_fecha_evento']=='')	
			$rowspan=4;
		else
			$rowspan=5;
		
		if($mr[$i]['monr_observaciones']!='') $rowspan++;
		
		if($mr[$i]['lista_campos_tabla']!='' AND $mr[$i]['monr_valor']!='') {
	
			$campos=explode('|', $mr[$i]['lista_campos_tabla']);
			
			$rowspan+=sizeof($campos);
			
		}
		
		if($mr[$i]['nombre_bandeja']!='')
			$bandeja=($mr[$i]['nombre_bandeja']);
		else
			$bandeja="<i>(Directorio GES)</i>";
		
		
		if($mr[$i]['monr_descripcion']!='') $rowspan++;
		
		switch($mr[$i]['monr_estado']*1) {
			case 0: $estado='Activo'; $color='green'; $color2='blue'; $style=''; break;
			case 1: $estado='Terminado'; $color='red'; $color2='black'; $style='text-decoration:line-through;'; break;
			case 2: $estado='Anulado'; $color='red'; $color2='black'; $style='text-decoration:line-through;'; break;
		} 

		if($mr[$i]['monr_subcondicion']!='') {
			$subcond='<i>('.trim($mr[$i]['monr_subcondicion']).')</i>';
		} else {
			$subcond='';
		}
		
		if(_cax(57))		
			$eliminar="<br/><span style='font-size:8px;cursor:pointer;' onClick='eliminar_monr(".$mr[$i]['monr_id'].");'>[Eliminar]</span>";
		else
			$eliminar='';
		
		print("
		
		<tr><td rowspan=$rowspan class='tabla_header'
		style='width:30px;text-align:center;font-weight:bold;font-size:32px;'>".(sizeof($mr)-$i).''.$eliminar."</td>
		
		        <td style='text-align:right;width:150px;' class='tabla_fila2'>Fecha Digitaci&oacute;n:</td>
                <td class='tabla_fila' style='font-weight:bold;'>".substr($mr[$i]['fecha'],0,16)."</td>
        
		</tr>
		
		<tr>
		<td style='text-align:right;width:150px;' class='tabla_fila2'>Condici&oacute;n:</td>
		<td class='tabla_fila' style='font-weight:bold;color:$color;font-size:14px;$style'>".($mr[$i]['nombre_condicion'])." $subcond [$estado]</td>
		</tr>

		<tr>
        <td style='text-align:right;' class='tabla_fila2'>Bandeja:</td>
		<td class='tabla_fila' style='font-weight:bold;color:$color2;font-size:14px;$style'>".$bandeja."</td>
		        </tr>


		");

		if($mr[$i]['monr_descripcion']!='')
		print("
	         <tr>
                <td style='text-align:right;width:150px;' class='tabla_fila2'>Fuente:</td>
                <td class='tabla_fila' style='font-size:12px;font-weight:bold;'>".$mr[$i]['monr_descripcion']."</td>
                </tr>
		");
		

		if($mr[$i]['monr_fecha_evento']!='')
		print("
	         <tr>
                <td style='text-align:right;width:150px;' class='tabla_fila2'>Fecha Evento:</td>
                <td class='tabla_fila' style='font-size:14px;font-weight:bold;'>".$mr[$i]['monr_fecha_evento']."</td>
                </tr>
		");
		
		if($mr[$i]['func_nombre']!='')
		
			print("
			
			<tr>
			<td style='text-align:right;' class='tabla_fila2'>Usuario:</td>
			<td class='tabla_fila'>".($mr[$i]['func_nombre'])."</td>
			</tr>
			
			");
			
		else
		
			print("
			
			<tr>
			<td style='text-align:right;' class='tabla_fila2'>Usuario:</td>
			<td class='tabla_fila'><i>(No ejecutado...)</i></td>
			</tr>
			
			");

		if($mr[$i]['monr_observaciones']!='')
		
			print("
			
			<tr>
			<td style='text-align:right;' class='tabla_fila2'>Observaciones:</td>
			<td class='tabla_fila'><i>".($mr[$i]['monr_observaciones'])."</i></td>
			</tr>
			
			");
			
		if($mr[$i]['lista_campos_tabla']!='' AND $mr[$i]['monr_valor']!='') {
	
		$campos=explode('|', $mr[$i]['lista_campos_tabla']);
		$valores=explode('|', $mr[$i]['monr_valor']);

		for($j=0;$j<sizeof($campos);$j++) {
			if(strstr($campos[$j],'&gt;&gt;&gt;')) {
				$cmp=explode('&gt;&gt;&gt;',$campos[$j]);
				$nombre=($cmp[0]); $tipo=$cmp[1]*1;
			} else {
				$cmp=$campos[$j]; $tipo=2;
			}
			
			if(trim($valores[$j])=='') 
				$valores[$j]='&nbsp;';
			else
				$valores[$j]=($valores[$j]);
			
			$nombre=trim($nombre,' :');
			
			print("<tr>
				<td style='text-align:right;font-size:14px;' class='tabla_fila2'>".$nombre.":</td>
				<td class='tabla_fila' style='font-size:15px;font-weight:bold;'>".$valores[$j]."</td>
			</tr>");

			$campos_especiales.="<tr>
                                <td style='width:25%;text-align:right;font-size:16px;' class='tabla_fila2'>".$nombre.":</td>
                                <td class='tabla_fila' style='font-size:18px;font-weight:bold;'>".$valores[$j]."</td>
                        </tr>";
			
		}

	}


		
	}

?>

</table>

<?php 
	$tmp=ob_get_contents(); 
	ob_end_clean(); 

	if($campos_especiales!='')
		print("<center><table style='width:80%;'><tr class='tabla_header'><td colspan=2>Campos Especiales</td></tr>$campos_especiales</table></center>");

	echo $tmp;

?>

<?php if(!$modo) { ?>

</div>

</body>


</html>

<?php } // END IF MODO ?>
