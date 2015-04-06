<?php
    require_once('../../conectar_db.php');
    if(!isset($_GET['submit']))
    {
  
    }
    else
    {
        // El script puede ejecutarse por mas de 30 segundos.
        set_time_limit(0);
        $tmp_inicio = microtime(true);
        $fecha1=pg_escape_string($_GET['fecha1']);
        $fecha2=pg_escape_string($_GET['fecha2']);
        $bod_id=(pg_escape_string($_GET['bod_id'])*1);
        $option_view=(pg_escape_string($_GET['option_view'])*1);
        $form_centro=(pg_escape_string($_GET['form_centro']));
        $string_bodega="";
        if($form_centro!="")
        {
            $string_centro=" and centro_ruta like '.$form_centro%'";
            $string_centro2=" and cargo_centro_costo.centro_ruta like '$form_centro%'";
            $string_grupo_centro="centro_ruta ";
        }
        else
        {
            $string_centro="";
            $string_centro2="";
            $string_grupo_centro=" (case when log_tipo!=17 then obtener_centro_costo(centro_ruta) else centro_ruta end) AS centro_ruta ";
        }
        if($bod_id!=-1)
        {
            $string_bodega="AND stock_bod_id=".$bod_id."";
            $string_servicios="";
        }
        else
        {
            $string_servicios="
            UNION ALL
            
            SELECT * FROM
            (
                SELECT
                COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta) 
                AS centro_ruta, 
                serv_item AS item_codigo, 
                (CASE WHEN gastoext_id IS NULL THEN serv_subtotal
                ELSE (serv_subtotal*gastoextd_valor)/gastoext_valortotal
                END) AS gasto,
                centro_winsig,
                log_tipo
                FROM servicios
                JOIN logs ON serv_log_id=log_id 
                LEFT JOIN cargo_centro_costo USING (log_id)
                LEFT JOIN cargo_gasto_externo USING (log_id)
                LEFT JOIN gasto_externo USING (gastoext_id)
                LEFT JOIN gastoext_detalle ON gastoext_id=gastoextd_gastoext_id
                LEFT JOIN centro_costo on COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta)=centro_costo.centro_ruta
                WHERE log_tipo=50 AND 
                log_fecha BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 23:59:59'
            ) AS foo 
            WHERE gasto>0 $string_centro";
            
        }
        
            
            
        if(isset($_GET['xls']))
        {
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: filename=\"Informe General de Gastos.XLS\";");
            $xlsborder="border=1";
            $clasetabla="";
        }
        else
        {
            $xlsborder="border=1";
            $clasetabla="class='tabla_header'";
            cabecera_popup('../..');
        ?>
            <style>
                
            </style>
            <script>
                ver_detalle=function(item,centro)
                {
                    params='submit&fecha1_detalle='+encodeURIComponent($('fecha1_detalle').value)
                    +'&fecha2_detalle='+encodeURIComponent($('fecha2_detalle').value)
                    +'&bod_id_detalle='+encodeURIComponent($('bod_id_detalle').value)
                    +'&form_centro_detalle='+encodeURIComponent($('form_centro_detalle').value)
                    +'&option_view_detalle='+encodeURIComponent($('option_view_detalle').value)
                    +'&item_detalle='+encodeURIComponent(item)
                    +'&centro_detalle='+encodeURIComponent(centro);
                    top=Math.round(screen.height/2)-200;
                    left=Math.round(screen.width/2)-300;
                    new_win = 
                    window.open('detalle_gastos.php'+'?'+params, 'win_informe_detalle', 
                    'toolbar=no, location=no, directories=no, status=no, fullscreen=yes '+
                    'menubar=no, scrollbars=yes, resizable=no, width='+ (screen.availWidth - 10).toString()+', height='+(screen.availHeight - 122).toString()+', '+
                    'top='+top+', left='+left);
                    new_win.focus();
                }
            </script>
            <title >Informe General de Gastos por Centro de Costo</title>
            <body class='fuente_por_defecto popup_background'>
                <input type="hidden" id="fecha1_detalle" name="fecha1_detalle" value="<?php echo $fecha1;?>">
                <input type="hidden" id="fecha2_detalle" name="fecha2_detalle" value="<?php echo $fecha2;?>">
                <input type="hidden" id="bod_id_detalle" name="bod_id_detalle" value="<?php echo $bod_id;?>">
                <input type="hidden" id="option_view_detalle" name="option_view_detalle" value="<?php echo $option_view;?>">
                <input type="hidden" id="form_centro_detalle" name="form_centro_detalle" value="<?php echo $form_centro;?>">
        
        <?php
        }
        // Crea tabla temporal con:
        // -- gastos de recetas
        // -- gastos fuera de bodega
        // -- despachos a servicios
        // -- gastos por hoja de cargo
        $query="
        CREATE TEMP TABLE gasto_centros AS 
        SELECT $string_grupo_centro, item_codigo, gasto,centro_winsig FROM (
        SELECT 
        receta_centro_ruta AS centro_ruta, 
        art_item AS item_codigo, 
        (-(stock_cant) * art_val_ult) AS gasto,
        centro_winsig,
        log_tipo
        FROM stock
        JOIN articulo ON stock_art_id=art_id
	JOIN logs ON stock_log_id=log_id
	JOIN recetas_detalle ON log_recetad_id=recetad_id
        JOIN receta ON recetad_receta_id=receta_id
        LEFT JOIN centro_costo on receta_centro_ruta=centro_costo.centro_ruta
        WHERE
	log_fecha BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 23:59:59'
        $string_bodega
        $string_centro
            
        UNION ALL
        
        SELECT 
        cargo_centro_costo.centro_ruta AS centro_ruta, 
        articulo.art_item AS item_codigo, 
        (-(stock.stock_cant) * articulo.art_val_ult) AS gasto,
        centro_winsig,
        log_tipo
        FROM stock
        JOIN articulo ON stock_art_id=art_id
        JOIN logs ON stock_log_id=log_id 
        JOIN cargo_centro_costo USING (log_id)
        LEFT JOIN centro_costo on cargo_centro_costo.centro_ruta=centro_costo.centro_ruta
        WHERE log_tipo=15 AND 
        log_fecha BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 23:59:59'
        $string_bodega
        $string_centro2
            

        UNION ALL
        
        SELECT 
        bod_glosa AS centro_ruta, 
        articulo.art_item AS item_codigo, 
        (-(stock.stock_cant) * articulo.art_val_ult) AS gasto,
        bod_glosa as centro_winsig,
        log_tipo
        FROM stock
        JOIN articulo ON stock_art_id=art_id
        JOIN logs ON stock_log_id=log_id 
        JOIN bodega on bod_id=stock_bod_id
        WHERE log_tipo=17 AND 
        log_fecha BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 23:59:59'
        $string_bodega
        $string_centro2
            
        $string_servicios
        )
        AS foo2;
        ";
        
        //print($query);
        //die();
        pg_query($conn, $query);
        
        if($option_view==1)
        {
            $centros = pg_query($conn, "SELECT DISTINCT centro_ruta, centro_nombre FROM gasto_centros LEFT JOIN centro_costo USING (centro_ruta);");
        }
        else
        {
            $centros = pg_query($conn, "select Distinct gasto_centros.centro_winsig,gasto_centros.centro_winsig from gasto_centros");
        }
        
        $items = pg_query($conn, "SELECT DISTINCT item_codigo, item_glosa FROM gasto_centros JOIN item_presupuestario USING (item_codigo);");
  
        // Cabecera del Informe
  
        print("<table $xlsborder style='font-size:11px;'>");
            print("<tr ".$clasetabla." style='text-align: center; font-weight:bold;'>");
            //print("<tr style='text-align: center; font-weight:bold;'>");
                print("<td style='width:300px;'>&nbsp;</td>");
                for($i=0;$i<pg_num_rows($centros);$i++)
                {
                    $centro_arr = pg_fetch_row($centros, $i);
                    if($centro_arr[0]==null)
                    {
                        print("<td style='width:300px;'>Centro No Definido</td>");
                    }
                    else
                    {
                        if($option_view==1) {
                            if(strstr($centro_arr[0],'.'))
                                print("<td style='width:300px;'>".$centro_arr[1]."</td>");
                            else
                                print("<td style='width:300px;background-color: #7B68EE;'>".$centro_arr[0]."</td>");
                        }
                        else {
                            $reg_bodega=cargar_registro("SELECT * FROM bodega WHERE bod_glosa='".$centro_arr[0]."'");
                            if($reg_bodega){
                                print("<td style='width:300px;background-color: #7B68EE;'>".$centro_arr[0]."</td>");
                            }
                            else
                                print("<td style='width:300px;'>".$centro_arr[1]."</td>");
                        }
                            
                    }
                }
                print("<td style='text-align: center; font-weight:bold;'>TOTALES ITEM</td>");
            print("</tr>");
            
            // Filas del Informe; una por cada item presupuestario.
            for($a=0;$a<pg_num_rows($items);$a++)
            {
                $item_arr = pg_fetch_row($items, $a);
                $total_item=0;
                print("<tr>");
                    print("<td ".$clasetabla.">".$item_arr[1]."</td>");
                    for($i=0;$i<pg_num_rows($centros);$i++)
                    {
                        $centro_arr = pg_fetch_row($centros, $i);
                        if($option_view==1)
                        {
                            $string_comp="centro_ruta='".pg_escape_string($centro_arr[0])."'";
                        }
                        else
                        {
                            if($centro_arr[0]==null)
                            {
                                $string_comp="centro_winsig is null";
                            }
                            else
                            {
                                $string_comp="centro_winsig='".pg_escape_string($centro_arr[0])."'";
                            }
                        }
                        
                        $dato_q = pg_query($conn, "
                        SELECT SUM(gasto) FROM ( 
                        SELECT gasto FROM gasto_centros WHERE $string_comp AND item_codigo='".pg_escape_string($item_arr[0])."'
                        ) AS foo");
                        $dato = pg_fetch_row($dato_q);
                        print("<td style='text-align: right;'>");
                        if(($dato[0]*1)!=0){
                            $item=pg_escape_string($item_arr[0]);
                            $centro=pg_escape_string($centro_arr[0]);
                            if($option_view==1)
                                print("<span ondblclick='ver_detalle(\"$item\", \"$centro\");' class='texto_tooltip'>".number_formats($dato[0])."</span>");
                            else
                                print("".number_formats($dato[0])."");
                                
                        }
                        else
                            print("".number_formats($dato[0])."");
                        
                        print("</td>");
                        $total_item=($total_item*1)+($dato[0]*1);
                    }
                    print("<td ".$clasetabla." style='text-align: right;text-align: right;border-style: solid; border-width: 1px 1px 2px 2px; margin: 1px; padding: 0;font-weight: bold;'>");
                        print("".number_formats($total_item)."");
                    print("</td>");
                    
                print("</tr>");
            }
                print("<tr>");
                    print("<td ".$clasetabla." style='text-align: center; font-weight:bold;'>TOTALES CENTROS</td>");
                    for($i=0;$i<pg_num_rows($centros);$i++)
                    {
                        $sum_centros=0;
                        $centro_arr = pg_fetch_row($centros, $i);
                        if($option_view==1)
                        {
                            $string_comp="centro_ruta='".pg_escape_string($centro_arr[0])."'";
                        }
                        else
                        {
                            if($centro_arr[0]==null)
                            {
                                $string_comp="centro_winsig is null";
                            }
                            else
                            {
                                $string_comp="centro_winsig='".pg_escape_string($centro_arr[0])."'";
                            }
                        }
                        for($a=0;$a<pg_num_rows($items);$a++)
                        {
                            $item_arr = pg_fetch_row($items, $a);
                            $dato_q = pg_query($conn, "
                            SELECT SUM(gasto) FROM (
                            SELECT gasto FROM gasto_centros
                            WHERE 
                            $string_comp
                            AND 
                            item_codigo='".pg_escape_string($item_arr[0])."'
                            ) AS foo
                            ");
                            $dato = pg_fetch_row($dato_q);
                            $sum_centros=($sum_centros*1)+($dato[0]*1);
                        }
                        print("<td ".$clasetabla." style='text-align: right;border-style: solid; border-width: 1px 1px 2px 2px; margin: 1px; padding: 0;font-weight: bold;'>");
                            print("".number_formats($sum_centros)."");
                        print("</td>");
                    }
                    
                    /*
                    for($a=0;$a<pg_num_rows($items);$a++)
                    {
                        $item_arr = pg_fetch_row($items, $a);
                        for($i=0;$i<pg_num_rows($centros);$i++)
                        {
                            $sum_centros=0;
                            $centro_arr = pg_fetch_row($centros, $i);
                            if($option_view==1)
                            {
                                $string_comp="centro_ruta='".pg_escape_string($centro_arr[0])."'";
                            }
                            else
                            {
                                if($centro_arr[0]==null)
                                {
                                    $string_comp="centro_winsig is null";
                                }
                                else
                                {
                                    $string_comp="centro_winsig='".pg_escape_string($centro_arr[0])."'";
                                }
                            }
                            $dato_q = pg_query($conn, "
                            SELECT SUM(gasto) FROM (
                            SELECT gasto FROM gasto_centros
                            WHERE 
                            $string_comp
                            AND 
                            item_codigo='".pg_escape_string($item_arr[0])."'
                            ) AS foo
                            ");
                            $dato = pg_fetch_row($dato_q);
                        }
                    }
                    */
                    
                print("</tr>");
            print("</table>");
            $tmp_final = microtime(true);
            $tmp = $tmp_final-$tmp_inicio;
            print("<center>Obtenido en [".$tmp."] msecs.</center>");
        }
    if(!isset($_GET['xls']))
    {
    ?>
    </body>
    <?php
    }
?>