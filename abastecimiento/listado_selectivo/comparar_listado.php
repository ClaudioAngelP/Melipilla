<?php 

	require_once('../../conectar_db.php');

	error_reporting(E_ALL);

	$ls_id=$_POST['correlativo'];
	
	$listado=cargar_registro("SELECT ls_id,ls_fecha,bod_glosa,func_rut,func_nombre,ls_estado 
							FROM listado_selectivo 
							LEFT JOIN bodega ON bod_id=ls_bod_id
							LEFT JOIN funcionario ON func_id=ls_func_id
							WHERE ls_id=$ls_id");
							
	$detalle=cargar_registros_obj("SSELECT lsd_art_id,art_codigo,art_glosa,forma_nombre,
										lsd_stock,lsd_inventario,-(lsd_stock-lsd_inventario)AS diferencia,
										ROUND(art_val_ult*-(lsd_stock-lsd_inventario)) AS valor
										FROM listado_selectivo_detalle
										LEFT JOIN articulo ON art_id=lsd_art_id
										LEFT JOIN bodega_forma ON forma_id=art_forma
										WHERE lsd_ls_id=$ls_id");
	
?>

<center>

Servicio de Salud Vi&ntilde;a del Mar - Quillota<br />
Hospital Dr. Gustavo Fricke<br />
OFICINA INVENTARIO<br />

<h1><u>CONTROL DE EXISTENCIAS</u></h1>

<div style='width:100%;text-align:left;font-size:14px;'>
CORRELATIVO: <u><?php echo $listado['ls_id'] ?></u>
 </div>
<div style='width:100%;text-align:right;font-size:14px;'>
FECHA: <u><?php echo $listado['ls_fecha']; ?></u> BODEGA: <u><?php echo $listado['bod_glosa']; ?></u>
</div>
<br /><br />
<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Nº</td>		
		<td>C&oacute;digo</td>
		<td>Art&iacute;culo</td>
		<td>UD</td>
<?php  if($listado['ls_estado']==0){ ?>
		<td>Cant. F&iacute;sico</td>
<?php }else{

		print("<td>Stock</td>
				<td>Inventario</td>
				<td>Diferencia</td>
				<td>Valor</td>
			");

	}	?>
	</tr>
	
<?php 

	

	 for($i=0;$i<sizeof($detalle);$i++) {
   	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
      
		$art_codigo=$detalle[$i]['art_codigo'];
		$art_glosa=htmlentities($detalle[$i]['art_glosa']);
		$art_forma=$detalle[$i]['forma_nombre'];
		
		//$str.=utf8_decode($art_codigo.'|'.$art_glosa.'|'.$art_forma.'|'.$fila['stock_total'])."\r\n";
		
		
		
		print("<tr class='$clase'>
				<td>".($i+1)."</td>
				<td>$art_codigo</td>
				<td>$art_glosa</td>
				<td>$art_forma</td>");
				
				
				if($listado['ls_estado']==0){
				//ingresar inventario
					print("<td style='width:15%;text-align:center;'>_________</td>
							");
				}else{
				//resultado diferencias
					print("<td style='width:15%;text-align:center;'>".$detalle[$i]['lsd_stock']."</td>
						<td>".$detalle[$i]['lsd_inventario']."</td>
						<td>".$detalle[$i]['diferencia']."</td>
						<td>\$ ".number_formats($detalle[$i]['valor']).".-</td>
						");
				}
				print("</tr>");	
    
    }
      
    //$fname='Listado_Selectivo_---'.$corr['currval'].'.txt';
	//file_put_contents("../../abastecimiento/listado_selectivo/$fname",$str);
	//$contenido=scandir("../../abastecimiento/listado_selectivo/");
	//print_r($contenido);

?>

</table>
<br /><br />
<table style='width:100%;'>
	<tr style='border-bottom:1px solid black;'>
		<td>&nbsp;</td>
	</tr>
	<tr style='border-bottom:1px solid black;'>
		<td>&nbsp;</td>
	</tr>
	<tr style='border-bottom:1px solid black;'>
		<td>&nbsp;</td>
	</tr>
</table>
<br /><br />
<table style='width:100%;'>
	<tr>
		<td style='text-align:center;'>________________________</td>
		<td style='text-align:center;'>________________________</td>
		<td style='text-align:center;'>________________________</td>
	</tr>
	<tr>
		<td style='text-align:center;'>JEFE INVENTARIO</td>
		<td style='text-align:center;'>CONTROL DE EXISTENCIAS</td>
		<td style='text-align:center;'>ENCARGADO DE BODEGA</td>
	</tr>
</table>

<table style='width:100%;'>
	<tr>
		<td style='text-align:center;'>______________________________</td>
	</tr>
	<tr>
		<td style='text-align:center;'>JEFE DE CONTABILIDAD Y PPTO.</td>
	</tr>
</table>


</center>
