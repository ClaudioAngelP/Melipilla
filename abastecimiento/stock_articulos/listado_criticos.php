<?php
    require_once("../../conectar_db.php");
    $cadena = str_replace('*', '%', pg_escape_string($_GET['buscar']));
    $bodega = $_GET['bodega']*1;
    $item = pg_escape_string($_GET['item']);
    // $orden = $_GET['orden'];
    $cadena=str_replace('*', '%', $cadena);
    if($cadena!='')
    {
        $codigo_w="AND (art_codigo || ' ' || art_glosa || ' ' || art_nombre ILIKE '$cadena')";
    }
    else
    {
        $codigo_w="";
    }

    if($item!=-1)
    {
        $item_w="AND art_item='".$item."'";
    }
    else
    {
        $item_w='';
    }

    $listado = pg_query($conn, "
    SELECT
		*, ss.art_id AS real_art_id,
        critico_pedido,
  		critico_critico,
        critico_gasto,
        ((
        SELECT SUM(-stock_cant) FROM stock
        JOIN logs ON log_fecha>(CURRENT_DATE-('3 months'::interval)) AND stock_log_id=log_id AND log_tipo IN (2,9,15,17,18)
        WHERE stock_art_id=ss.art_id AND stock_bod_id=$bodega AND stock_cant<0
        )/3) AS consumo3

		FROM
        (
			SELECT
			art_id,
			art_codigo,
			art_glosa,
			forma_nombre

			FROM
			articulo
			JOIN articulo_bodega ON artb_art_id=art_id AND artb_bod_id=$bodega
			LEFT JOIN bodega_forma
			ON art_forma=forma_id
		   	WHERE art_activado
          	$codigo_w $item_w
			GROUP BY
			art_id, art_codigo, art_glosa, forma_nombre

		) AS ss

		LEFT JOIN stock_critico
		ON critico_art_id=art_id AND critico_bod_id=$bodega

		ORDER BY art_codigo
		");

		print("<center>
		<table style='width:100%;'>
    <tr class='tabla_header'><td><b>C&oacute;digo Int.</b></td><td width=35%%><b>Glosa</b></td>
		<td><b>Forma</b></td>
        <td><b>Gasto Promedio &Uacute;ltimos 3m</b></td>
		<td><b>Gasto Mens.</b></td>
		<td><b>Stock Pedido</b></td>
        <td><b>Stock Cr&iacute;tico</b></td>
        <td width=7%%>&nbsp;</td></tr>
		");

        for($i=0;$i<pg_num_rows($listado);$i++)
        {
            $fila = pg_fetch_assoc($listado);
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            if(/*($fila[4]*1)==0 AND */($fila['critico_pedido']*1==0 AND $fila['critico_critico']*1==0))
            {
                $color1 = "#DDDDDD";
		$color2 = "#DDDDDD";
		//$icono = '';
                $icono='<img id="error_'.$fila['real_art_id'].'" src="iconos/exclamation.png">';
            }
            else
            {
                if(($fila['critico_pedido']*1)==0)
                {
                    $color1="yellow";
                    $icono='<img id="error_'.$fila['real_art_id'].'" src="iconos/exclamation.png">';
                    
                }
		else
                {
                    $color1='inherit';
                    $icono='<img id="error_'.$fila['real_art_id'].'" src="iconos/tick.png">';
                    
                }
                if(($fila['critico_critico']*1)==0)
                {
                    $color2="yellow";
                    $icono='<img id="error_'.$fila['real_art_id'].'" src="iconos/exclamation.png">';
                    
                }
		else
                {
                    $color2='inherit';
                    $icono='<img id="error_'.$fila['real_art_id'].'" src="iconos/tick.png">';
                    
                }
            }

			print("
			<tr id='fila_".$fila['real_art_id']."' class='".$clase."'
      style='font-size:9px;'>
			<td style='text-align:right;'><b><i>".$fila['art_codigo']."</i></b></td>
		       <td><b>".htmlentities($fila['art_glosa'])."</b></td>
			<td>".htmlentities($fila['forma_nombre'])."</td>
			<td style='text-align:right;font-weight:bold;'>".number_format($fila['consumo3'],2,',','.')."</td>
			<td><center>
			<input type='text' id='gasto_".$fila['real_art_id']."' name='gasto_".$fila['real_art_id']."'
			size=7 value='".$fila['critico_gasto']."' style='text-align: right; background-color: ".$color1."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila['real_art_id'].");'
			onFocus='$(\"fila_".$fila['real_art_id']."\").className=\"mouse_over\";
			info_articulo(".$fila['real_art_id'].");'
			onBlur='$(\"fila_".$fila['real_art_id']."\").className=\"".$clase."\";'
			>
			</center></td>
			<td><center>
			<input type='text' id='pedido_".$fila['real_art_id']."' name='pedido_".$fila['real_art_id']."'
			size=7 value='".$fila['critico_pedido']."'
      style='text-align: right; background-color: ".$color2."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila['real_art_id'].");'
			onFocus='$(\"fila_".$fila['real_art_id']."\").className=\"mouse_over\";
      info_articulo(".$fila['real_art_id'].");'
			onBlur='$(\"fila_".$fila['real_art_id']."\").className=\"".$clase."\";'>
			</center></td>
			<td><center>
			<input type='text' id='critico_".$fila['real_art_id']."' name='critico_".$fila['real_art_id']."'
			size=7 value='".$fila['critico_critico']."'
      style='text-align: right; background-color: ".$color2."; font-size:11px;'
			onKeyUp='comprobar_item(".$fila['real_art_id'].");'
			onFocus='$(\"fila_".$fila['real_art_id']."\").className=\"mouse_over\";
      info_articulo(".$fila['real_art_id'].");'
			onBlur='$(\"fila_".$fila['real_art_id']."\").className=\"".$clase."\";'>
			</center></td>
			<td id='operacion_".$fila['real_art_id']."'><center>
			$icono
			<img src='iconos/accept.png' id='aceptar_".$fila['real_art_id']."' 
			onClick='guardar_articulo(".$fila['real_art_id'].");'
			style='display:none;cursor:pointer;'>
			</center></td>
			</tr>
			");

		}

		print("</table></center>");
?>
