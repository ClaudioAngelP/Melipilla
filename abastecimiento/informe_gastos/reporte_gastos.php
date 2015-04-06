<?php
    require_once('../../conectar_db.php');
    set_time_limit(0);
    ini_set("memory_limit","1500M");
    $index=$_GET['index']*1;
    
    $fecha1_detalle=pg_escape_string($_GET['fecha1_detalle']);
    $fecha2_detalle=pg_escape_string($_GET['fecha2_detalle']);
    $bod_id_detalle=(pg_escape_string($_GET['bod_id_detalle'])*1);
    $form_centro_detalle=(pg_escape_string($_GET['form_centro_detalle'])*1);
    $option_view_detalle=(pg_escape_string($_GET['option_view_detalle']));
    $item_detalle=(pg_escape_string($_GET['item_detalle']));
    $centro_detalle=(pg_escape_string($_GET['centro_detalle']));
    
    
    $item_glosa="";
    $item_reg=cargar_registro("SELECT * FROM item_presupuestario where item_codigo='".$item_detalle."'");
    if($item_reg)
        $item_glosa=$item_reg['item_glosa'];

    $centro_nombre="";

    if(strstr($centro_detalle,'.')){
        $centro_reg=cargar_registro("SELECT * FROM centro_costo where centro_ruta='".$centro_detalle."'");
        if($centro_reg)
            $centro_nombre=$centro_reg['centro_nombre'];
    }
    else {
        $centro_nombre=utf8_decode($centro_detalle);
    }

    
    if($index==0) {
        if($option_view_detalle==1) {
            //$wcentro="and receta_centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        } else {
            $wcentro="and centro_winsig ilike '%".$centro_detalle."%'";
        }

        $string_bodega="";
        if($bod_id_detalle!=-1) {
            $string_bodega="AND stock_bod_id=".$bod_id_detalle."";
        }
        
        
        $query="
        SELECT *,'R' as tipo FROM (
            SELECT *,(-(stock.stock_cant) * articulo.art_val_ult)as subtotal,date_trunc('Second',log_fecha)as fecha_despacho,
            (case when log_tipo!=17 then obtener_centro_costo(centro_ruta) else centro_ruta end) AS centro_ruta_filtro
            FROM stock
            JOIN articulo ON stock_art_id=art_id
            JOIN logs ON stock_log_id=log_id
            JOIN recetas_detalle ON log_recetad_id=recetad_id
            JOIN receta ON recetad_receta_id=receta_id
            LEFT JOIN centro_costo on receta_centro_ruta=centro_costo.centro_ruta
            LEFT JOIN item_presupuestario on item_codigo=art_item
            WHERE
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND art_item='".$item_detalle."' 
            $string_bodega 
            ORDER BY fecha_despacho,receta_numero
        )as foo
        WHERE $wcentro
        ORDER BY fecha_despacho,receta_numero
        ;";
        
        //print($query);
        
        
        $detalle_recetas=cargar_registros_obj($query);
        
        $query="
        SELECT *,'P' as tipo FROM (
        SELECT *,(-(stock.stock_cant) * articulo.art_val_ult)as subtotal,date_trunc('Second',log_fecha)as fecha_despacho,
        articulo.art_item AS item_codigo, 
        (-(stock.stock_cant) * articulo.art_val_ult) AS gasto,
        centro_winsig,
        (case when log_tipo!=17 then obtener_centro_costo(cargo_centro_costo.centro_ruta) else cargo_centro_costo.centro_ruta end) AS centro_ruta_filtro
        FROM stock
        JOIN articulo ON stock_art_id=art_id
        JOIN logs ON stock_log_id=log_id 
        JOIN cargo_centro_costo USING (log_id)
        LEFT JOIN centro_costo on cargo_centro_costo.centro_ruta=centro_costo.centro_ruta
        LEFT JOIN pedido on pedido_id=log_id_pedido
        LEFT JOIN item_presupuestario on item_codigo=art_item
        WHERE log_tipo=15 AND 
        log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
        AND art_item='".$item_detalle."' $string_bodega
        ORDER BY fecha_despacho,log_id_pedido
        )as foo
        WHERE $wcentro
        ORDER BY fecha_despacho,log_id_pedido
        ";
        
        $detalle_pedidos=cargar_registros_obj($query);
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Reporte_Gastos_Recetas_".$fecha1_detalle."_".$fecha2_detalle.".xls\";");
        print("
        <table style='width:100%;'>
            <tr>
                <td style='text-align:left;width:60px;white-space:nowrap;'><b>Fecha Inicio :</b></td>
                <td style='text-align:left;width:50px;white-space:nowrap;'>$fecha1_detalle</td>
                <td style='text-align:left;width:55px;white-space:nowrap;'><b>Fecha Final :</b></td>
                <td>$fecha2_detalle</td>
            </tr>
            <tr>
                <td style='text-align:left;width:60px;white-space:nowrap;'><b>Item C&oacute;digo :</b></td>
                <td style='text-align:left;width:50px;white-space:nowrap;'>$item_detalle</td>
                <td style='text-align:left;width:55px;white-space:nowrap;'><b>Item :</b></td>
                <td>$item_glosa</td>
            </tr>
            <tr>
                <td style='text-align:left;width:60px;white-space:nowrap;'><b> Centro costo:</b></td>
                <td colspan='3'>$centro_nombre</td>
            </tr>
        </table>");
        print("<br>");
        print("<br>");
        print("<br>");
        print("<table style='width:80%;'>");
            print("<tr>");
                print("<td style='width:10%;'>Tipo de Registro</td>");
                print("<td style='width:10%;'>N&deg; Registro</td>");
                print("<td style='width:10%;'>Servicio/Centro</td>");
                print("<td style='width:10%;'>Fecha Despacho</td>");
                print("<td style='width:10%;'>C&oacute;digo</td>");
                print("<td style='width:30%;'>Articulo</td>");
                print("<td style='width:10%;'>Item C&oacute;digo</td>");
                print("<td style='width:10%;'>Item</td>");
                print("<td style='width:10%;'>Cantidad</td>");
                print("<td style='width:10%;'>Precio Unit.</td>");
                print("<td style='width:10%;'>Subtotal</td>");
            print("</tr>");
            $total_general=0;
            for($i=0;$i<count($detalle_recetas);$i++) {
                print("<tr>");
                    print("<td style='text-align:center;font-size:10px;'>RECETA</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['receta_numero'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['centro_nombre'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['fecha_despacho'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_codigo'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_glosa'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_item'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['item_glosa'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_recetas[$i]['stock_cant'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_recetas[$i]['art_val_ult'],2, ',','.'))."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_recetas[$i]['subtotal'],1, ',','.'))."</td>");
                print("</tr>");
                $total_general=$total_general+($detalle_recetas[$i]['subtotal']*1);
            }
            for($i=0;$i<count($detalle_pedidos);$i++) {
                print("<tr>");
                    print("<td style='text-align:center;font-size:10px;'>PEDIDO</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['pedido_nro'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['centro_nombre'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['fecha_despacho'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_codigo'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_glosa'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_item'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['item_glosa'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_pedidos[$i]['stock_cant'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_pedidos[$i]['art_val_ult'],2, ',','.'))."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_pedidos[$i]['subtotal'],1, ',','.'))."</td>");
                print("</tr>");
                $total_general=$total_general+($detalle_pedidos[$i]['subtotal']*1);
            }
            print("
            <tr>
                <td colspan='10' style='text-align:right;width:60px;white-space:nowrap;'><b> Total General:</b></td>
                <td>".htmlentities(number_format($total_general,1, ',','.'))."</td>
            </tr>
            ");
            print("</table>");
    }
    
    if($index==1) {
        
        if($option_view_detalle==1) {
            //$wcentro="and receta_centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        } else {
            $wcentro="and centro_winsig ilike '%".$centro_detalle."%'";
        }

        $string_bodega="";
        if($bod_id_detalle!=-1) {
            $string_bodega="AND stock_bod_id=".$bod_id_detalle."";
        }
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Reporte_Gastos_Recetas_".$fecha1_detalle."_".$fecha2_detalle.".xls\";");
        
        
        $query="
        SELECT * FROM (
            SELECT *,(-(stock.stock_cant) * articulo.art_val_ult)as subtotal,date_trunc('Second',log_fecha)as fecha_despacho,
            (case when log_tipo!=17 then obtener_centro_costo(centro_ruta) else centro_ruta end) AS centro_ruta_filtro
            FROM stock
            JOIN articulo ON stock_art_id=art_id
            JOIN logs ON stock_log_id=log_id
            JOIN recetas_detalle ON log_recetad_id=recetad_id
            JOIN receta ON recetad_receta_id=receta_id
            LEFT JOIN centro_costo on receta_centro_ruta=centro_costo.centro_ruta
            LEFT JOIN item_presupuestario on item_codigo=art_item
            WHERE
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND art_item='".$item_detalle."' 
            $string_bodega 
            ORDER BY fecha_despacho,receta_numero
        )as foo
        WHERE $wcentro
        ORDER BY fecha_despacho,receta_numero
        ;";
        
        
        
        $detalle_recetas=cargar_registros_obj($query);
        
        if($detalle_recetas) {
            
            print("
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b>Fecha Inicio :</b></td>
                    <td style='text-align:left;width:50px;white-space:nowrap;'>$fecha1_detalle</td>
                    <td style='text-align:left;width:55px;white-space:nowrap;'><b>Fecha Final :</b></td>
                    <td>$fecha2_detalle</td>
                </tr>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b>Item C&oacute;digo :</b></td>
                    <td style='text-align:left;width:50px;white-space:nowrap;'>$item_detalle</td>
                    <td style='text-align:left;width:55px;white-space:nowrap;'><b>Item :</b></td>
                    <td>$item_glosa</td>
                </tr>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b> Centro costo:</b></td>
                    <td colspan='3'>$centro_nombre</td>
                </tr>
            </table>");
            print("<br>");
            print("<br>");
            print("<br>");
            print("<table style='width:80%;'>");
                print("<tr>");
                    print("<td style='width:10%;'>N&deg; Receta</td>");
                    print("<td style='width:10%;'>Servicio/Centro</td>");
                    print("<td style='width:10%;'>Fecha Despacho</td>");
                    print("<td style='width:10%;'>C&oacute;digo</td>");
                    print("<td style='width:30%;'>Articulo</td>");
                    print("<td style='width:10%;'>Item C&oacute;digo</td>");
                    print("<td style='width:10%;'>Item</td>");
                    print("<td style='width:10%;'>Cantidad</td>");
                    print("<td style='width:10%;'>Precio Unit.</td>");
                    print("<td style='width:10%;'>Subtotal</td>");
                print("</tr>");
                $total_recetas=0;
                for($i=0;$i<count($detalle_recetas);$i++) {
                    print("<tr>");
                        print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['receta_numero'])."</td>");
                        print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['centro_nombre'])."</td>");
                        print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['fecha_despacho'])."</td>");
                        print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_codigo'])."</td>");
                        print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_glosa'])."</td>");
                        print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_item'])."</td>");
                        print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['item_glosa'])."</td>");
                        print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_recetas[$i]['stock_cant'])."</td>");
                        print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_recetas[$i]['art_val_ult'],2, ',','.'))."</td>");
                        print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_recetas[$i]['subtotal'],1, ',','.'))."</td>");
                    print("</tr>");
                    $total_recetas=$total_recetas+($detalle_recetas[$i]['subtotal']*1);
                }
                print("
                <tr>
                    <td colspan='9' style='text-align:right;width:60px;white-space:nowrap;'><b> Total:</b></td>
                    <td>".htmlentities(number_format($total_recetas,1, ',','.'))."</td>
                </tr>
                ");
                print("</table>");
            }
    }
    if($index==2) {
        
        if($option_view_detalle==1) {
            //$wcentro="and receta_centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        } else {
            $wcentro="and centro_winsig ilike '%".$centro_detalle."%'";
        }

        $string_bodega="";
        if($bod_id_detalle!=-1) {
            $string_bodega="AND stock_bod_id=".$bod_id_detalle."";
        }
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Reporte_Gastos_Recetas_".$fecha1_detalle."_".$fecha2_detalle.".xls\";");
        
        $query="
        SELECT * FROM (
        SELECT *,(-(stock.stock_cant) * articulo.art_val_ult)as subtotal,date_trunc('Second',log_fecha)as fecha_despacho,
        articulo.art_item AS item_codigo, 
        (-(stock.stock_cant) * articulo.art_val_ult) AS gasto,
        centro_winsig,
        (case when log_tipo!=17 then obtener_centro_costo(cargo_centro_costo.centro_ruta) else cargo_centro_costo.centro_ruta end) AS centro_ruta_filtro
        FROM stock
        JOIN articulo ON stock_art_id=art_id
        JOIN logs ON stock_log_id=log_id 
        JOIN cargo_centro_costo USING (log_id)
        LEFT JOIN centro_costo on cargo_centro_costo.centro_ruta=centro_costo.centro_ruta
        LEFT JOIN pedido on pedido_id=log_id_pedido
        LEFT JOIN item_presupuestario on item_codigo=art_item
        WHERE log_tipo=15 AND 
        log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
        AND art_item='".$item_detalle."' $string_bodega
        ORDER BY fecha_despacho,log_id_pedido
        )as foo
        WHERE $wcentro
        ORDER BY fecha_despacho,log_id_pedido
        ";
        
        $detalle_pedidos=cargar_registros_obj($query);
        
        if($detalle_pedidos) {
            
            print("
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b>Fecha Inicio :</b></td>
                    <td style='text-align:left;width:50px;white-space:nowrap;'>$fecha1_detalle</td>
                    <td style='text-align:left;width:55px;white-space:nowrap;'><b>Fecha Final :</b></td>
                    <td>$fecha2_detalle</td>
                </tr>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b>Item C&oacute;digo :</b></td>
                    <td style='text-align:left;width:50px;white-space:nowrap;'>$item_detalle</td>
                    <td style='text-align:left;width:55px;white-space:nowrap;'><b>Item :</b></td>
                    <td>$item_glosa</td>
                </tr>
                <tr>
                    <td style='text-align:left;width:60px;white-space:nowrap;'><b> Centro costo:</b></td>
                    <td colspan='3'>$centro_nombre</td>
                </tr>
            </table>");
            print("<br>");
            print("<br>");
            print("<br>");
            
            print("<table style='width:80%;'>");
                print("<tr>");
                    print("<td style='width:10%;'>N&deg; Pedido</td>");
                    print("<td style='width:10%;'>Servicio/Centro</td>");
                    print("<td style='width:10%;'>Fecha Despacho</td>");
                    print("<td style='width:10%;'>C&oacute;digo</td>");
                    print("<td style='width:30%;'>Articulo</td>");
                    print("<td style='width:10%;'>Item C&oacute;digo</td>");
                    print("<td style='width:10%;'>Item</td>");
                    print("<td style='width:10%;'>Cantidad</td>");
                    print("<td style='width:10%;'>Precio Unit.</td>");
                    print("<td style='width:10%;'>Subtotal</td>");
                print("</tr>");
                $total_pedidos=0;
                for($i=0;$i<count($detalle_pedidos);$i++) {
                    print("<tr>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['pedido_nro'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['centro_nombre'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['fecha_despacho'])."</td>");
                    print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_codigo'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_glosa'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_item'])."</td>");
                    print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['item_glosa'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_pedidos[$i]['stock_cant'])."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_pedidos[$i]['art_val_ult'],2, ',','.'))."</td>");
                    print("<td style='text-align:right;font-size:10px;'>".htmlentities(number_format($detalle_pedidos[$i]['subtotal'],1, ',','.'))."</td>");
                    print("</tr>");
                    $total_pedidos=$total_pedidos+($detalle_pedidos[$i]['subtotal']*1);
                }
                print("
                <tr>
                    <td colspan='9' style='text-align:right;width:60px;white-space:nowrap;'><b> Total:</b></td>
                    <td>".htmlentities(number_format($total_pedidos,1, ',','.'))."</td>
                </tr>
                ");
            print("</table>");
        }
    }
    
    
    
        
    
    
?>