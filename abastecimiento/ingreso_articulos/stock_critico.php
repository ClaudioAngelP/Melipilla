<?php

		require_once("../../conectar_db.php");

    if(isset($_GET['art_id']))  $art_id = ($_GET['art_id']*1);
    else                        $art_id=0;
		// $orden = $_GET['orden'];
		
		$listado = pg_query($conn, "
		SELECT 
		bodega.bod_id,
		bod_glosa,
		calcular_stock($art_id, bodega.bod_id),
		critico_pedido,
		critico_critico,
		critico_gasto,
		artb_id
		FROM
		bodega
		LEFT JOIN stock_critico ON critico_art_id=$art_id AND critico_bod_id=bod_id
		LEFT JOIN articulo_bodega ON artb_art_id=$art_id AND artb_bod_id=bodega.bod_id
		ORDER BY bod_glosa
		
		");
		
		print("<center>
		<table width='100%'>
		<tr class='tabla_header'><td><b>Ubicaci&oacute;n</b></td>
		<td><b>Ver</b></td>
		<td><b>Stock</b></td>
		<td><b>Stock Pedido</b></td>
    <td><b>Stock Cr&iacute;tico</b></td>
    <td><b>Gasto Mensual</b></td>
    <td width=7%%>&nbsp;</td></tr>
		");
		
		for($i=0;$i<pg_num_rows($listado);$i++) {
		
			$fila = pg_fetch_row($listado);
			
			($i%2==0) ? 	$clase='tabla_fila' : $clase='tabla_fila2';
			
			if(($fila[2]*1)==0) { 
				$color1 = "#DDDDDD";
				$color2 = "#DDDDDD";
				$icono = '<img id="error_'.$fila[0].'" src="iconos/exclamation.png" style="display:none">';
				
			} else {
				if(($fila[3]*1)==0) { $color1="yellow"; $icono='<img id="error_'.$fila[0].'" src="iconos/exclamation.png">'; }
				else 				{ $color1='inherit'; $icono='<img id="error_'.$fila[0].'" src="iconos/tick.png">'; }
				if(($fila[4]*1)==0) { $color2="yellow";	$icono='<img id="error_'.$fila[0].'" src="iconos/exclamation.png">'; }
				else				{ $color2='inherit'; $icono='<img id="error_'.$fila[0].'" src="iconos/tick.png">'; }
				
			}
			
			print("
			<tr id='fila_".$fila[0]."' class='".$clase."'>
			<td><b>".htmlentities($fila[1])."</b></td>
			<td style='text-align: center;'>
			<input type='checkbox' id='ver_".$fila[0]."' name='ver_".$fila[0]."' ".(($fila[6]*1!=0)?'CHECKED':'')." />
			
			</td>
			<td style='text-align: right;'>".htmlentities($fila[2])."</td>
			<td><center>
			<input type='text' id='pedido_".$fila[0]."' name='pedido_".$fila[0]."' 
			size=6 value='".$fila[3]."' style='text-align: right; background-color: ".$color1."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila[0].");'
			onFocus='$(\"fila_".$fila[0]."\").className=\"mouse_over\";'
			onBlur='$(\"fila_".$fila[0]."\").className=\"".$clase."\";'
			>
			</center></td>
			<td><center>
			<input type='hidden' id='art_stock_".$fila[0]."' 
      name='art_stock_".$fila[0]."' value='".$fila[2]."'>
			<input type='text' id='critico_".$fila[0]."' name='critico_".$fila[0]."' 
			size=6 value='".$fila[4]."' 
      style='text-align: right; background-color: ".$color2."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila[0].");'
			onFocus='$(\"fila_".$fila[0]."\").className=\"mouse_over\";'
			onBlur='$(\"fila_".$fila[0]."\").className=\"".$clase."\";'
			></center></td>
			<td><center>
			<input type='text' id='gasto_".$fila[0]."' name='gasto_".$fila[0]."' 
			size=6 value='".$fila[5]."' 
      style='text-align: right; background-color: ".$color2."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila[0].");'
			onFocus='$(\"fila_".$fila[0]."\").className=\"mouse_over\";'
			onBlur='$(\"fila_".$fila[0]."\").className=\"".$clase."\";'
			></center></td>
			<td id='operacion_".$fila[0]."'><center>
			$icono
			<img src='iconos/accept.png' id='aceptar_".$fila[0]."' style='display: none;'>
			</center></td>
			</tr>
			");
			
		}
		
		print("</table></center>");
		
?>
