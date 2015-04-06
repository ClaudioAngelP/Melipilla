<?php 
    require_once('../../conectar_db.php');
    $fap_id=$_POST['fap_id']*1;
    $hc=cargar_registros_obj("
    SELECT urghc_fecha_digitacion AS log_fecha, art_codigo, art_glosa, 
    urghc_cantidad AS cantidad, forma_nombre,
    art_val_ult AS punit, 
    (urghc_cantidad*art_val_ult) AS subtotal, urghc_id
    FROM urgencia_hoja_cargo
    JOIN articulo on art_id=urghc_art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE urghc_fap_id=$fap_id
    ORDER BY log_fecha DESC
    ", true);
    
    print("<table style='width:100%;'>
        <tr class='tabla_header'>
        <td>Fecha/Hora</td>
	<td>C&oacute;digo</td>
	<td>Art&iacute;culo</td>
	<td>Cantidad</td>
	<td>Unidad</td>
	<td>P Unit. $</td>
	<td>Subtotal $</td>
	<td>Eliminar</td>
    </tr>");
	
    if($hc)
        for($i=0;$i<sizeof($hc);$i++) {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase'>
                <td style='text-align:center;'>".substr($hc[$i]['log_fecha'],0,16)."</td>
		<td style='text-align:right;font-weight:bold;'>".$hc[$i]['art_codigo']."</td>
		<td style='text-align:left;'>".$hc[$i]['art_glosa']."</td>
		<td style='text-align:right;'>".$hc[$i]['cantidad']."</td>
		<td style='text-align:left;'>".$hc[$i]['forma_nombre']."</td>
		<td style='text-align:right;'>$".number_format($hc[$i]['punit'],0,',','.').".-</td>
		<td style='text-align:right;'>$".number_format($hc[$i]['subtotal'],0,',','.').".-</td>
		<td><center>
		");
                if($hc[$i]['urghc_id']!=0)
                    print("<img src='../../iconos/delete.png' style='cursor:pointer;' onClick='eliminar_hc(".$hc[$i]['urghc_id'].");'>");
		else
                    print("<img src='../../iconos/stop.png'>");
		
                print("</center></td>
            </tr>");
	}
        print("</table>");
?>