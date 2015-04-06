<?php
    require_once('../../conectar_db.php');
    set_time_limit(0);
    ini_set("memory_limit","1500M");
    
    if(!isset($_GET['submit']))
    {
  
    }
    else
    {
        
        set_time_limit(0);
        $tmp_inicio = microtime(true);
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
            
            
        
        
        /*
        if($form_centro_detalle!="")
        {
            $string_centro=" and centro_ruta like '.$form_centro_detalle%'";
            $string_centro2=" and cargo_centro_costo.centro_ruta like '$form_centro_detalle%'";
        }
        else
        {
            $string_centro="";
            $string_centro2="";
        }
        * 
        */
        
        if($option_view_detalle==1)
        {
            //$wcentro="and receta_centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        }
        else
        {
            $wcentro="and centro_winsig ilike '%".$centro_detalle."%'";
        }
        
        $string_bodega="";
        if($bod_id_detalle!=-1)
        {
            $string_bodega="AND stock_bod_id=".$bod_id_detalle."";
            
        }
        
        
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
        
        //print($query);
        
        $detalle_recetas=cargar_registros_obj($query);
        
        $query="
        SELECT * FROM (
        SELECT 
	receta_id,receta_numero,sum((-(stock_cant) * art_val_ult)) AS gasto,
        (case when log_tipo!=17 then obtener_centro_costo(centro_ruta) else centro_ruta end) AS centro_ruta_filtro
	FROM stock
	JOIN articulo ON stock_art_id=art_id
	JOIN logs ON stock_log_id=log_id
	JOIN recetas_detalle ON log_recetad_id=recetad_id
	JOIN receta ON recetad_receta_id=receta_id
	LEFT JOIN centro_costo on receta_centro_ruta=centro_costo.centro_ruta
        LEFT JOIN item_presupuestario on item_codigo=art_item
	WHERE log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
        AND art_item='".$item_detalle."' 
        $string_bodega 
        GROUP by receta_id,log_tipo,centro_ruta
        ORDER BY receta_numero
        )as foo
        WHERE $wcentro
        ORDER BY receta_numero
        ;";
        
        $recetas=cargar_registros_obj($query);
        $total_recetas=0;
        if($recetas)
        {
            for($i=0;$i<count($recetas);$i++)
            {
                $total_recetas=$total_recetas+($recetas[$i]['gasto']*1);
            }
        }
        
        if($option_view_detalle==1)
        {
            //$wcentro="and cargo_centro_costo.centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        }
        
        
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
        
        //print($query);
        
        $detalle_pedidos=cargar_registros_obj($query);
        
        
        if($option_view_detalle==1)
        {
            //$wcentro="and cargo_centro_costo.centro_ruta ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        }
        
        
        $query="
        SELECT * FROM (
        SELECT 
	pedido_id,pedido_nro,sum((-(stock_cant) * art_val_ult)) AS gasto,
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
        AND art_item='".$item_detalle."' 
        $string_bodega 
        GROUP by pedido_id,log_tipo,cargo_centro_costo.centro_ruta
        ORDER BY pedido_nro
        )as foo
        WHERE $wcentro
        ORDER BY pedido_nro
        ";
        
        //print($query);
     
        
        $pedidos=cargar_registros_obj($query);
        $total_pedidos=0;
        if($pedidos)
        {

            for($i=0;$i<count($pedidos);$i++)
            {
                $total_pedidos=$total_pedidos+($pedidos[$i]['gasto']*1);
            }
        }
        
        if($option_view_detalle==1)
        {
            //$wcentro="and centro_ruta2 ilike '%".$centro_detalle."%'";
            $wcentro="centro_ruta_filtro='".$centro_detalle."'";
        }
        
        
        
        $query="
        SELECT * from (
            SELECT *,
            COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta) AS centro_ruta2, 
            serv_item AS item_codigo, 
            (
                CASE WHEN gastoext_id IS NULL THEN serv_subtotal 
                ELSE (serv_subtotal*gastoextd_valor)/gastoext_valortotal
                END
            ) AS gasto,
            centro_winsig,
            date_trunc('Second',log_fecha)as fecha_despacho,
            (case when log_tipo!=17 then obtener_centro_costo(COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta)) else COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta) end) AS centro_ruta_filtro
            FROM servicios
            JOIN logs ON serv_log_id=log_id 
            LEFT JOIN cargo_centro_costo USING (log_id)
            LEFT JOIN cargo_gasto_externo USING (log_id)
            LEFT JOIN gasto_externo USING (gastoext_id)
            LEFT JOIN gastoext_detalle ON gastoext_id=gastoextd_gastoext_id
            LEFT JOIN centro_costo on COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta)=centro_costo.centro_ruta
            LEFT JOIN item_presupuestario on item_codigo=serv_item
            WHERE log_tipo=50 AND 
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND serv_item='".$item_detalle."'
        )as foo
        WHERE gasto>0 
        AND $wcentro
        ORDER BY fecha_despacho,log_doc_id";
        
        //print($query);
        $detalle_servicios=cargar_registros_obj($query);
        
        $query="
        select sum(gasto)as gasto,log_doc_id,centro_ruta_filtro from (
            SELECT *,
            COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta) AS centro_ruta2, 
            serv_item AS item_codigo, 
            (
                CASE WHEN gastoext_id IS NULL THEN serv_subtotal 
                ELSE (serv_subtotal*gastoextd_valor)/gastoext_valortotal
                END
            ) AS gasto,
            centro_winsig,
            (case when log_tipo!=17 then obtener_centro_costo(COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta)) else COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta) end) AS centro_ruta_filtro
            FROM servicios
            JOIN logs ON serv_log_id=log_id 
            LEFT JOIN cargo_centro_costo USING (log_id)
            LEFT JOIN cargo_gasto_externo USING (log_id)
            LEFT JOIN gasto_externo USING (gastoext_id)
            LEFT JOIN gastoext_detalle ON gastoext_id=gastoextd_gastoext_id
            LEFT JOIN centro_costo on COALESCE(cargo_centro_costo.centro_ruta, gastoextd_centro_ruta)=centro_costo.centro_ruta
            WHERE log_tipo=50 AND 
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND serv_item='".$item_detalle."'
        )as foo
        WHERE gasto>0 
        AND $wcentro
        group by foo.log_doc_id,foo.centro_ruta_filtro
        ORDER BY log_doc_id";
        
        $servicios=cargar_registros_obj($query);
        $total_servicios=0;
        if($servicios)
        {

            for($i=0;$i<count($servicios);$i++)
            {
                $total_servicios=$total_servicios+($servicios[$i]['gasto']*1);
            }
        }
        
        if(strstr($centro_detalle,'.'))
            $mostrar_cargo=false;
        else {
            $mostrar_cargo=true;
            $query="SELECT 
            *,
            (-(stock.stock_cant) * articulo.art_val_ult)as subtotal,date_trunc('Second',log_fecha)as fecha_despacho,
            articulo.art_item AS item_codigo
            FROM stock
            JOIN articulo ON stock_art_id=art_id
            JOIN logs ON stock_log_id=log_id 
            JOIN bodega on bod_id=stock_bod_id
            JOIN cargo_hoja using (log_id)
            JOIN pacientes using (pac_id)
            JOIN item_presupuestario on item_codigo=art_item
            WHERE log_tipo=17 AND 
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND art_item='".$item_detalle."' 
            $string_bodega
            order by fecha_despacho,log_id";
            
            $detalle_cargos=cargar_registros_obj($query);
            
            
            $query="SELECT 
            (-(stock.stock_cant) * articulo.art_val_ult)as gasto,date_trunc('Second',log_fecha)as fecha_despacho,
            articulo.art_item AS item_codigo
            FROM stock
            JOIN articulo ON stock_art_id=art_id
            JOIN logs ON stock_log_id=log_id 
            JOIN bodega on bod_id=stock_bod_id
            JOIN cargo_hoja using (log_id)
            JOIN pacientes using (pac_id)
            WHERE log_tipo=17 AND 
            log_fecha BETWEEN '".$fecha1_detalle." 00:00:00' AND '".$fecha2_detalle." 23:59:59'
            AND art_item='".$item_detalle."' 
            $string_bodega
            order by fecha_despacho,log_id";
            
            $cargos=cargar_registros_obj($query);
            $total_cargos=0;
            if($cargos)
            {
                for($i=0;$i<count($cargos);$i++)
                {
                    $total_cargos=$total_cargos+($cargos[$i]['gasto']*1);
                }
            }
            
            
            
        }
        cabecera_popup('../..');
    }
?>
<style>
    .tbCss1 {background: #FFFFFF;color: #333377;font-size:11px;padding: 6px;margin: 3px;border:2px groove #C8C8F2;line-height: 1.5em;white-space: nowrap;font-family: verdana,arial,helvetica,sans-serif;}
    .tb1CaptionTop {caption-side: top;color:#191970;font-weight: bold;font-size:11px;font-family: verdana,arial,helvetica,sans-serif;}
    .tb1CaptionBottom {caption-side: bottom;color:#191970;font-weight: normal;font-size:11px;font-family: verdana,arial,helvetica,sans-serif;}
    .tbHeader1 {background: #AAAAFF;color: #FFFFFF;font-size:11px;font-weight: bold;padding: 6px;border:2px groove #C8C8F2;line-height: 1.5em;white-space: nowrap;font-family: verdana,arial,helvetica,sans-serif;}
    .tbSubHeader1 {background: #CCCCFF;color: #191970;font-size:11px;font-weight: bold;padding: 6px;border:2px groove #C8C8F2;line-height: 1.5em;white-space: nowrap;font-family: verdana,arial,helvetica,sans-serif;}
    .tdCss1 {padding: 4px;}
</style>
<script type="text/javascript">
    imprimir_detalle=function(index) {
        var fecha1_detalle=<?php echo json_encode($fecha1_detalle); ?>;
        var fecha2_detalle=<?php echo json_encode($fecha2_detalle); ?>;
        var bod_id_detalle=<?php echo $bod_id_detalle; ?>;
        var form_centro_detalle=<?php echo $form_centro_detalle; ?>;
        var option_view_detalle=<?php echo $option_view_detalle; ?>;
        
        var item=<?php echo $item_detalle; ?>;
        var centro=<?php echo json_encode($centro_detalle); ?>;
        
                
        
         var params='fecha1_detalle='+encodeURIComponent(fecha1_detalle)
        +'&fecha2_detalle='+encodeURIComponent(fecha2_detalle)
        +'&bod_id_detalle='+encodeURIComponent(bod_id_detalle)
        +'&form_centro_detalle='+encodeURIComponent(form_centro_detalle)
        +'&option_view_detalle='+encodeURIComponent(option_view_detalle)
        +'&item_detalle='+encodeURIComponent(item)
        +'&centro_detalle='+encodeURIComponent(centro)
        +'&index='+index;
        
        var ruta='reporte_gastos.php';
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_report_gastos = window.open(ruta+'?'+params,
        'win_reporte_gastos', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_report_gastos.focus();
        
    }
    
    historico_precios=function (art_id){
        var win = new Window("popup_historico",
        {
            className: "alphacube", top:40, left:0, width: 800, height: 600, 
            title: '<img src="../../iconos/page_white_link.png"> HISTORICO DE PRECIOS',
            minWidth: 500, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, draggable: true,
            closable: true, resizable: false 
        });
        win.setDestroyOnClose();
        win.setAjaxContent('historico_precios.php', 
        {
            method: 'post',
            async:false,
            dataType: 'json',
            parameters: 'art_id='+art_id
        });
        $("popup_historico").win_obj=win;
        win.showCenter();
        win.show(true);
    }
    
    confirmar_precios = function(art_id, fila) {
    
      var punit = $('valunit_art_'+fila).value*1;
      params='&art_id='+(art_id*1)+'&punit='+encodeURIComponent(punit);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('confirmar_punit.php?'+
      params,
      'win_punit', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      new_win.focus();
	}
</script>
<?php $cont=0 ?>
<center>
    <div style="width:1400px;" class="sub-content">
        <div style="font-weight:bold;" class="sub-content">
            <img src="../../iconos/coins.png">
            Informe General de Gastos Detallado
        </div>
        <div class="sub-content">
            <table>
                <tr>
                    <td style="width:80%;">
                        <table style='width:100%;'>
                            <tr>
                                <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'><b>Fecha Inicio :</b></td>
                                <td style='text-align:left;width:50px;white-space:nowrap;' valign='top'><?php echo $fecha1_detalle;?></td>
                                <td style='text-align:left;width:55px;white-space:nowrap;' valign='top' class='tabla_fila2'><b>Fecha Final :</b></td>
                                <td><?php echo $fecha2_detalle;?></td>
                            </tr>
                            <tr>
                                <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'><b>Item C&oacute;digo :</b></td>
                                <td style='text-align:left;width:50px;white-space:nowrap;' valign='top'><?php echo $item_detalle;?></td>
                                <td style='text-align:left;width:55px;white-space:nowrap;' valign='top' class='tabla_fila2'><b>Item :</b></td>
                                <td><?php echo htmlentities($item_glosa);?></td>
                            </tr>
                            <tr>
                                <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'><b> Centro costo:</b></td>
                                <td colspan="3"><?php echo $centro_nombre;?></td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <input type="button" onclick="imprimir_detalle(0)" value="Detalle General en Excel" />
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table class="tbCss1">
                                <tr class="tbHeader1">
                                    <th>&nbsp;</th>
                                    <th style='text-align:right;font-size:10px;' colspan="3">Cantidad</th>
                                    <th style='text-align:right;font-size:10px;' colspan="3">Totales $</th>
                                </tr>
                                <tr class="tbSubHeader1">
                                    <th>Total Recetas</th>
                                    <th colspan="3" style='text-align:right;font-size:12px;'><?php if($recetas){echo count($recetas);}else{ echo "0"; } ?></th>
                                    <th colspan="3" style='text-align:right;font-size:12px;width: 180px;'><?php echo number_formats($total_recetas); ?></th>
                                </tr>
                                <tr class="tbSubHeader1">
                                    <th>Total Pedidos</th>
                                    <th colspan="3" style='text-align:right;font-size:12px;'><?php if($pedidos){echo count($pedidos);}else{ echo "0"; } ?></th>
                                    <th colspan="3" style='text-align:right;font-size:12px;width: 180px;'><?php echo number_formats($total_pedidos); ?></th>
                                </tr>
                                <tr class="tbSubHeader1">
                                    <th>Total Servicios</th>
                                    <th colspan="3" style='text-align:right;font-size:12px;'><?php if($servicios){echo count($servicios);}else{ echo "0"; } ?></th>
                                    <th colspan="3" style='text-align:right;font-size:12px;width: 180px;'><?php echo number_formats($total_servicios); ?></th>
                                </tr>
                                <tr class="tbSubHeader1">
                                    <th>Total Cargo Paciente</th>
                                    <th colspan="3" style='text-align:right;font-size:12px;'><?php if($cargos){echo count($cargos);}else{ echo "0"; } ?></th>
                                    <th colspan="3" style='text-align:right;font-size:12px;width: 180px;'><?php echo number_formats($total_cargos); ?></th>
                                </tr>
                                <tr class="tbSubHeader1">
                                    <th style='text-align:center;font-size:12px;' colspan="4">Total General</th>
                                    <th style='text-align:right;font-size:12px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo number_formats((($total_recetas*1)+($total_pedidos*1)+($total_servicios*1)+($total_cargos*1))); ?></th>
                                </tr>
                        </table> 
                    </td>
                </tr>
            </table>
        </div>
        <?php if(!$mostrar_cargo){?>
        <div class="sub-content2" style="height: 320px;">
            <div class='sub-content' style='font-size:16px;background-color: #7B68EE;'>
                <table>
                    <tr>
                        <td style="color: #FFFFFF;font-size:14px;">
                            &nbsp;&nbsp;<b>(Detalle de Recetas Entregadas)</b>&nbsp;&nbsp;
                            Total Recetas: <b><font color="yellow"><?php if($recetas){echo count($recetas);}else{ echo "0"; } ?></font></b>
                            &nbsp;
                            Valor Total: <b><font color="yellow">$ <?php echo number_formats($total_recetas); ?></font></b>
                        </td>
                        <?php if(($total_recetas*1)>0) {?>
                        <td>
                            <input type="button" onclick="imprimir_detalle(1)" value="Detalle en Excel" />
                        </td>
                        <?php }?>
                    </tr>
                </table>
            </div>
            <div class="sub-content2" style="height: 250;overflow:auto;">
                <center>
                    <?php
                    if($recetas) {
                        $receta_id="";
                        for($i=0;$i<count($detalle_recetas);$i++)
                        {
                            $clase=($i%2)==0?'tabla_fila':'tabla_fila2';
                            if($detalle_recetas[$i]['receta_id']!=$receta_id){
                                if($i!=0){
                                    print("<tr class='tabla_header' >");
                                        print("<td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td>");
                                        print("<td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_receta,1, ',','.'))."</b></td>");
                                        print("<td>&nbsp;</td>");
                                    print("</tr>");
                                    print("</table></center></div>");
                                    print("<br />");
                                    $total_receta=0;
                                }
                                
                                print("<div class='sub-content'><center><table style='width:80%;'>");
                                    print("<tr class='tabla_header' style='background-color:#B0E0E6;'>");
                                        print("<td colspan=9 style='text-align:left;font-size:13px;'>N&deg; Receta: <b>".$detalle_recetas[$i]['receta_numero']."</b>&nbsp;&nbsp;&nbsp;&nbsp;Servicio/Centro: <b>".$detalle_recetas[$i]['centro_nombre']."</b></td>");
                                    print("</tr>");
                                    print("<tr class='tabla_header'>");
                                        print("<td style='width:10%;'>Fecha Despacho</td>");
                                        print("<td style='width:10%;'>C&oacute;digo</td>");
                                        print("<td style='width:30%;'>Articulo</td>");
                                        print("<td style='width:10%;'>Item C&oacute;digo</td>");
                                        print("<td style='width:10%;'>Item</td>");
                                        print("<td style='width:10%;'>Cantidad</td>");
                                        print("<td style='width:10%;'>Precio Unit.</td>");
                                        print("<td style='width:10%;'>Subtotal</td>");
                                        print("<td style='width:10%;'>&nbsp;</td>");
                                    print("</tr>");
                            }
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['fecha_despacho'])."</td>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_codigo'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_glosa'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['art_item'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_recetas[$i]['item_glosa'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_recetas[$i]['stock_cant'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
									print("$ ".htmlentities(number_format($detalle_recetas[$i]['art_val_ult'],2, ',','.'))."");
									print('<input type="hidden" id="valunit_art_'.$cont.'" name="valunit_art_'.$cont.'" style="text-align: right;" size=8 value="'.($detalle_recetas[$i]['art_val_ult']*1).'" onFocus="" onKeyUp="">');
								print("</td>");
                                print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_recetas[$i]['subtotal'],1, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
                                if(($detalle_recetas[$i]['art_val_med']*1)!=0) {
									$var=(($detalle_recetas[$i]['art_val_med']*1)-($detalle_recetas[$i]['art_val_ult']*1))*100/($detalle_recetas[$i]['art_val_med']*1);
								} else {
									$var=0;
								}
								if(abs($var)>10 or $var==0) {
									$icono_precio='error';
								} else {
									$icono_precio='magnifier';
								}
								print('<img src="../../iconos/'.$icono_precio.'.png" id="art_dif_'.$i.'" style="cursor: pointer;width:24px;height:24px;" onClick="confirmar_precios('.$detalle_recetas[$i]['art_id'].','.$cont.');">');
                                print("</td>");
                            print("</tr>");
                            $total_receta=$total_receta+($detalle_recetas[$i]['subtotal']*1);
                            $receta_id=($detalle_recetas[$i]['receta_id']*1);
                            $cont=$cont+1;
                        }
                        print("<tr class='tabla_header' >");
                            print("<td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td>");
                            print("<td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_receta,1, ',','.'))."</b></td>");
                            print("<td>&nbsp;</td>");
                        print("</tr>");
                        print("</table></center></div>");
                        print("<br />");
                        $total_receta=0;
                    }
                    else {
                        print("<table><tr><td>No se han econtrado movimientos para los valores de busqueda</td></tr></table>");
                    }
                    ?>
                </center>
            </div>
        </div>
        <div class="sub-content2" style="height: 320px;">
            <div class='sub-content' style='font-size:16px;background-color: #7B68EE;'>
                <table>
                    <tr>
                        <td style="color: #FFFFFF;font-size:14px;">
                            &nbsp;&nbsp;<b>(Detalle de Pedidos a Servicios Despachados)</b>&nbsp;&nbsp;
                            Total Pedidos: <b><font color="yellow"><?php if($pedidos){ echo count($pedidos); }else{ echo "0";} ?></font></b>
                            &nbsp;
                            Valor Total: <b><font color="yellow">$ <?php echo number_formats($total_pedidos); ?></font></b>
                        </td>
                        <?php if(($total_pedidos*1)>0) {?>
                        <td>
                            <input type="button" onclick="imprimir_detalle(2)" value="Detalle en Excel" />
                        </td>
                        <?php }?>
                    </tr>
                </table>
            </div>
            <div class="sub-content2" style="height: 250;overflow:auto;">
                <center>
                <?php 
                    if($pedidos) {
                        $pedido_id="";
                        for($i=0;$i<count($detalle_pedidos);$i++)
                        {
                            $clase=($i%2)==0?'tabla_fila':'tabla_fila2';
                            if($detalle_pedidos[$i]['log_id_pedido']!=$pedido_id){
                                if($i!=0){
                                    print("<tr class='tabla_header' ><td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td><td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_pedido,1, ',','.'))."</b></td><td>&nbsp;</td></tr>");
                                    print("</table></center></div>");
                                    print("<br />");
                                    $total_pedido=0;
                                }
                                print("<div class='sub-content'><center><table style='width:80%;'>");
                                    print("<tr class='tabla_header' style='background-color:#B0E0E6;'>");
                                        print("<td colspan=9 style='text-align:left;font-size:13px;'>N&deg; Pedido: <b>".$detalle_pedidos[$i]['pedido_nro']."</b>&nbsp;&nbsp;&nbsp;&nbsp;Servicio/Centro: <b>".$detalle_pedidos[$i]['centro_nombre']."</b></td>");
                                    print("</tr>");
                                    print("<tr class='tabla_header'>");
                                        print("<td style='width:10%;'>Fecha Despacho</td>");
                                        print("<td style='width:10%;'>C&oacute;digo</td>");
                                        print("<td style='width:30%;'>Articulo</td>");
                                        print("<td style='width:10%;'>Item C&oacute;digo</td>");
                                        print("<td style='width:10%;'>Item</td>");
                                        print("<td style='width:10%;'>Cantidad</td>");
                                        print("<td style='width:10%;'>Precio Unit.</td>");
                                        print("<td style='width:10%;'>Subtotal</td>");
                                        print("<td style='width:10%;'>&nbsp;</td>");
                                    print("</tr>");
                                    
                                
                            }
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['fecha_despacho'])."</td>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_codigo'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_glosa'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['art_item'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_pedidos[$i]['item_glosa'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_pedidos[$i]['stock_cant'])."</td>");
                                //print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_pedidos[$i]['art_val_ult'],2, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
									print("$ ".htmlentities(number_format($detalle_pedidos[$i]['art_val_ult'],2, ',','.'))."");
									print('<input type="hidden" id="valunit_art_'.$cont.'" name="valunit_art_'.$cont.'" style="text-align: right;" size=8 value="'.($detalle_pedidos[$i]['art_val_ult']*1).'" onFocus="" onKeyUp="">');
								print("</td>");
                                print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_pedidos[$i]['subtotal'],1, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
                                if(($detalle_pedidos[$i]['art_val_med']*1)!=0) {
									$var=(($detalle_pedidos[$i]['art_val_med']*1)-($detalle_pedidos[$i]['art_val_ult']*1))*100/($detalle_pedidos[$i]['art_val_med']*1);
								} else {
									$var=0;
								}
                                if(abs($var)>10 or $var==0) {
									$icono_precio='error';
								} else {
									$icono_precio='magnifier';
								}
								print('<img src="../../iconos/'.$icono_precio.'.png" id="art_dif_'.$i.'" style="cursor: pointer;width:24px;height:24px;" onClick="confirmar_precios('.$detalle_pedidos[$i]['art_id'].','.$cont.');">');
                                print("</td>");
                            print("</tr>");
                            $total_pedido=$total_pedido+($detalle_pedidos[$i]['subtotal']*1);
                            $pedido_id=($detalle_pedidos[$i]['log_id_pedido']*1);
                            $cont=$cont+1;
                        }
                        print("<tr class='tabla_header' ><td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td><td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_pedido,1, ',','.'))."</b></td><td>&nbsp;</td></tr>");
                        print("</table></center></div>");
                        print("<br />");
                        $total_pedido=0;
                    }
                    else {
                        print("<table><tr><td>No se han econtrado movimientos para los valores de busqueda</td></tr></table>");
                    }
                ?>
                </center>
            </div>
        </div>
        <div class="sub-content2" style="height: 320px;">
            <div class='sub-content' style='font-size:16px;background-color: #7B68EE;'>
                <table>
                    <tr>
                        <td style="color: #FFFFFF;font-size:14px;">
                            &nbsp;&nbsp;<b>(Detalle de Consumo inmediato / Servicios )</b>&nbsp;&nbsp;
                            Total Consumo/Servicios: <b><font color="yellow"><?php if($servicios){echo count($servicios);}else{echo "0";} ?></font></b>
                            &nbsp;
                            Valor Total: <b><font color="yellow">$ <?php echo number_formats($total_servicios); ?></font></b>
                        </td>
                        <?php if(($total_servicios*1)>0) {?>
                        <td>
                            <input type="button" onclick="imprimir_detalle(3)" value="Detalle en Excel" />
                        </td>
                        <?php }?>
                    </tr>
                </table>
            </div>
            <div class="sub-content2" style="height: 250;overflow:auto;">
                <center>
                    <?php
                    if($servicios) {
                        $total_servicio=0;
                        $doc_id="";
                        for($i=0;$i<count($detalle_servicios);$i++)
                        {
                            $clase=($i%2)==0?'tabla_fila':'tabla_fila2';
                            if($detalle_servicios[$i]['log_doc_id']!=$doc_id){
                                if($i!=0){
                                    print("<tr class='tabla_header' ><td colspan=6 style='text-align:center;font-size:13px;'><b>Total Final</b></td><td><b>$ ".htmlentities(number_format($total_servicio,1, ',','.'))."</b></td></tr>");
                                    print("</table></center></div>");
                                    print("<br />");
                                    $total_servicio=0;
                                }
                                
                                print("<div class='sub-content'><center><table style='width:80%;'>");
                                    print("<tr class='tabla_header' style='background-color:#B0E0E6;'>");
                                        print("<td colspan=7 style='text-align:left;font-size:13px;'>N&deg; Pedido: <b>".$detalle_servicios[$i]['log_doc_id']."</b>&nbsp;&nbsp;&nbsp;&nbsp;Servicio/Centro: <b>".$detalle_servicios[$i]['centro_nombre']."</b></td>");
                                    print("</tr>");
                                    print("<tr class='tabla_header'>");
                                        print("<td style='width:10%;'>Fecha Despacho</td>");
                                        print("<td style='width:30%;'>Descripci&oacute;n del Gasto</td>");
                                        print("<td style='width:10%;'>Item C&oacute;digo</td>");
                                        print("<td style='width:10%;'>Item</td>");
                                        print("<td style='width:10%;'>Cantidad</td>");
                                        print("<td style='width:10%;'>Precio Unit.</td>");
                                        print("<td style='width:10%;'>Subtotal</td>");
                                    print("</tr>");
                                    
                                
                            }
                            
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_servicios[$i]['fecha_despacho'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_servicios[$i]['serv_glosa'])."</td>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_servicios[$i]['serv_item'])."</td>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_servicios[$i]['item_glosa'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>".htmlentities($detalle_servicios[$i]['serv_cant'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format(($detalle_servicios[$i]['gasto']/$detalle_servicios[$i]['serv_cant']),2, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_servicios[$i]['gasto'],1, ',','.'))."</td>");
                                print("<td style='width:10%;'>&nbsp;</td>");
                            print("</tr>");
                            $total_servicio=$total_servicio+($detalle_servicios[$i]['gasto']*1);
                            $doc_id_id=($detalle_servicios[$i]['log_doc_pedido']*1);
                        }
                        print("<tr class='tabla_header' ><td colspan=6 style='text-align:center;font-size:13px;'><b>Total Final</b></td><td><b>$ ".htmlentities(number_format($total_servicio,1, ',','.'))."</b></td></tr>");
                        print("</table></center></div>");
                        print("<br />");
                        $total_servicio=0;
                    }
                    else
                    {
                        print("<table><tr><td>No se han econtrado movimientos para los valores de busqueda</td></tr></table>");
                    }
                    ?>
                </center>
            </div>
        </div>
        <?php } else {?>
        
        <div class="sub-content2" style="height: 320px;">
            <div class='sub-content' style='font-size:16px;background-color: #7B68EE;'>
                <table>
                    <tr>
                        <td style="color: #FFFFFF;font-size:14px;">
                            &nbsp;&nbsp;<b>(Detalle de Cargos a Pacientes)</b>&nbsp;&nbsp;
                            Total Cargo Pacientes: <b><font color="yellow"><?php if($cargos){echo count($cargos);}else{echo "0";} ?></font></b>
                            &nbsp;
                            Valor Total: <b><font color="yellow">$ <?php echo number_formats($total_cargos); ?></font></b>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="sub-content2" style="height: 250;overflow:auto;">
                <center>
                    <?php
                    if($cargos) {
                        $total_cargo=0;
                        $log_id="";
                        for($i=0;$i<count($detalle_cargos);$i++)
                        {
                            $clase=($i%2)==0?'tabla_fila':'tabla_fila2';
                            if($detalle_cargos[$i]['log_id']!=$log_id){
                                if($i!=0){
                                    print("<tr class='tabla_header' ><td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td><td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_cargo,1, ',','.'))."</b></td><td>&nbsp;</td></tr>");
                                    print("</table></center></div>");
                                    print("<br />");
                                    $total_cargo=0;
                                }
                                
                                print("<div class='sub-content'><center><table style='width:80%;'>");
                                    print("<tr class='tabla_header' style='background-color:#B0E0E6;'>");
                                        print("<td colspan=9 style='text-align:left;font-size:13px;'>N&deg; Movimiento: <b>".$detalle_cargos[$i]['log_id']."</b><!--&nbsp;&nbsp;&nbsp;&nbsp;Servicio/Centro: <b>".$detalle_cargos[$i]['centro_nombre']."</b>--></td>");
                                    print("</tr>");
                                    print("<tr class='tabla_header'>");
                                        print("<td style='width:10%;'>Fecha Despacho</td>");
                                        print("<td style='width:10%;'>C&oacute;digo</td>");
                                        print("<td style='width:30%;'>Articulo</td>");
                                        print("<td style='width:10%;'>Item C&oacute;digo</td>");
                                        print("<td style='width:10%;'>Item</td>");
                                        print("<td style='width:10%;'>Cantidad</td>");
                                        print("<td style='width:10%;'>Precio Unit.</td>");
                                        print("<td style='width:10%;'>Subtotal</td>");
                                        print("<td style='width:10%;'>&nbsp;</td>");
                                    print("</tr>");
                            }
                            
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_cargos[$i]['fecha_despacho'])."</td>");
                                print("<td style='text-align:center;font-size:10px;'>".htmlentities($detalle_cargos[$i]['art_codigo'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_cargos[$i]['art_glosa'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_cargos[$i]['art_item'])."</td>");
                                print("<td style='text-align:left;font-size:10px;'>".htmlentities($detalle_cargos[$i]['item_glosa'])."</td>");
                                print("<td style='text-align:right;font-size:10px;'>".htmlentities(-$detalle_cargos[$i]['stock_cant'])."</td>");
                                //print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_cargos[$i]['art_val_ult'],2, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
									print("$ ".htmlentities(number_format($detalle_cargos[$i]['art_val_ult'],2, ',','.'))."");
									print('<input type="hidden" id="valunit_art_'.$cont.'" name="valunit_art_'.$cont.'" style="text-align: right;" size=8 value="'.($detalle_cargos[$i]['art_val_ult']*1).'" onFocus="" onKeyUp="">');
								print("</td>");
                                print("<td style='text-align:right;font-size:10px;'>$ ".htmlentities(number_format($detalle_cargos[$i]['subtotal'],1, ',','.'))."</td>");
                                print("<td style='text-align:right;font-size:10px;'>");
                                if(($detalle_cargos[$i]['art_val_med']*1)!=0) {
									$var=(($detalle_cargos[$i]['art_val_med']*1)-($detalle_cargos[$i]['art_val_ult']*1))*100/($detalle_cargos[$i]['art_val_med']*1);
								} else {
									$var=0;
								}
                                if(abs($var)>10 or $var==0) {
									$icono_precio='error';
								} else {
									$icono_precio='magnifier';
								}
								print('<img src="../../iconos/'.$icono_precio.'.png" id="art_dif_'.$i.'" style="cursor: pointer;width:24px;height:24px;" onClick="confirmar_precios('.$detalle_cargos[$i]['art_id'].','.$cont.');">');
                                print("</td>");
                            print("</tr>");
                            $total_cargo=$total_cargo+($detalle_cargos[$i]['subtotal']*1);
                            $log_id=($detalle_cargos[$i]['log_id']*1);
                            
                            $cont=$cont+1;
                        }
                        print("<tr class='tabla_header' ><td colspan=7 style='text-align:right;font-size:13px;'><b>Total Final</b></td><td style='text-align:right;font-size:13px;'><b>$ ".htmlentities(number_format($total_cargo,1, ',','.'))."</b></td><td>&nbsp;</td></tr>");
                        print("</table></center></div>");
                        print("<br />");
                        $total_cargo=0;
                    }
                    else
                    {
                        print("<table><tr><td>No se han econtrado movimientos para los valores de busqueda</td></tr></table>");
                    }
                    ?>
                </center>
            </div>
        </div>
        <?php } ?>
    </div>
</center>
