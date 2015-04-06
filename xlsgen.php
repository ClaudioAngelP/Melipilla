<?php
    require_once('conectar_db.php');
    if(isset($_GET['pedido_nro']))
    {
        $pedido_nro=$_GET['pedido_nro']*1;
        $pedido=cargar_registros_obj("
        SELECT 
        *,
        b1.bod_glosa AS bodega_origen,
        COALESCE(b2.bod_glosa, 'Abastecimiento') AS bodega_destino,
        f1.func_rut AS femisor_rut,
        f1.func_nombre AS femisor_nombre,
        f2.func_rut AS fautoriza_rut,
        f2.func_nombre AS fautoriza_nombre,
        pedidoa_fecha,
        pedido.pedido_id AS pedid  
        FROM pedido
        LEFT JOIN bodega AS b1 ON origen_bod_id=b1.bod_id
        LEFT JOIN bodega AS b2 ON destino_bod_id=b2.bod_id
        LEFT JOIN funcionario AS f1 ON pedido_func_id=f1.func_id
        LEFT JOIN pedido_autorizacion ON
        pedido_autorizacion.pedido_id=pedido.pedido_id
        LEFT JOIN funcionario AS f2 ON
        pedido_autorizacion.func_id=f2.func_id
        WHERE pedido_nro=".$pedido_nro."
        ");
        
        $detalle=cargar_registros_obj('
        SELECT * FROM pedido_detalle
        JOIN articulo ON pedido_detalle.art_id=articulo.art_id
        LEFT JOIN bodega_forma ON art_forma=forma_id
        WHERE pedido_id='.$pedido[0]['pedid'].'
        ');

        $html='
        <table border=1>
            <tr>
                <td colspan=5 style="text-align:center; font-weight:bold;">
                    Pedido Nro. <u>'.$pedido_nro.'</u>
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">Fecha de Emisi&oacute;n:</td>
                <td colspan=4>'.$pedido[0]['pedido_fecha'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;">Funcionario Emisor:</td>
                <td colspan=4>'.$pedido[0]['femisor_rut'].' '.htmlentities($pedido[0]['femisor_nombre']).'</td>
            </tr>
            <tr>
                <td style="text-align:right;">Bodega de Or&iacute;gen:</td>
                <td colspan=4>'.$pedido[0]['bodega_origen'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;">Destino:</td>
                <td colspan=4>'.$pedido[0]['bodega_destino'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;">Fecha Autorizaci&oacute;n:</td>
                <td colspan=4>'.$pedido[0]['pedidoa_fecha'].'</td>
            </tr>
            <tr>
                <td style="text-align:right;">Autorizado Por:</td>
                <td colspan=4>'.$pedido[0]['fautoriza_rut'].' '.htmlentities($pedido[0]['fautoriza_nombre']).'</td>
            </tr>
            <tr>
                <td style="text-align:right;">C&oacute;digo del Pedido:</td>
                <td colspan=4>['.$pedido_nro.']</td>
            </tr>
            <tr style="font-weight:bold;">
                <td>Glosa Art&iacute;culo</td>
                <td>Forma</td>
                <td>Cantidad</td>
                <td>Ultimo Valor ($)</td>
                <td>Subtotal ($)</td>
            </tr>
        ';
        for($i=0;$i<count($detalle);$i++)
        {
            $det=$detalle[$i];
            $html.='
            <tr>
                <td>[CodInt:'.$det['art_codigo'].'|'.number_format($det['pedidod_cant'],1,',','.').'] '.$det['art_glosa'].'</td>
                <td>'.$det['forma_nombre'].'</td>
                <td style="text-align:right;">'.number_format($det['pedidod_cant'],1,',','.').'</td>
                <td style="text-align:right;">$'.$det['art_val_ult'].'</td>
                <td style="text-align:right;">$'.($det['pedidod_cant']*$det['art_val_ult']).'</td>
            </tr>
            ';
        }
        $html.='</table>';
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"Pedido ".$pedido_nro."\".xls\";");
        print($html);
    }
    if(isset($_GET['doc_id']))
    {
        $doc_id=$_GET['doc_id'];
        
        $recepcion=pg_query($conn, "
        SELECT
        date_trunc('second',log_fecha),
        doc_tipo,
        prov_rut,
        prov_glosa,
        doc_num,
        doc_iva,
        doc_id,
        doc_descuento,
        doc_orden_id, doc_orden_desc,
        doc_observacion
        FROM documento
        JOIN proveedor ON doc_prov_id=prov_id
        JOIN logs ON log_doc_id=doc_id
        WHERE doc_id=".$doc_id."
        ");
        
        $recepcion_a = pg_fetch_row($recepcion);
        switch($recepcion_a[1])
        {
            case 0: $recepcion_a[1]='Guía de Despacho'; break;
            case 1: $recepcion_a[1]='Factura'; break;
            case 2: $recepcion_a[1]='Boleta'; break;
            case 3: $recepcion_a[1]='Pedido'; break;
            case 4: $recepcion_a[1]='Resoluci&oacute;n (Donaciones)'; break;
        }
        
        $recepciones = pg_query($conn, "
        SELECT
        log_id,
        fecha,
        func_nombre,
        bodega_id,
        bod_glosa,
        log_tipo,
        log_folio
        FROM (
                SELECT 
                log_id, 
                date_trunc('second', log_fecha) AS fecha,
                func_nombre,
                (SELECT stock_bod_id FROM stock WHERE stock_log_id=log_id LIMIT 1) AS bodega_id,
                log_tipo,
                log_folio
                FROM logs 
                JOIN funcionario ON log_func_if=func_id
                WHERE log_doc_id=".$doc_id."
        ) AS foo
        LEFT JOIN bodega ON bodega_id=bod_id
        ");
        
        $cabezera_html='';
        $detalles_html='';
        $numrec=pg_num_rows($recepciones);
        $totalgeneral=0;
        
        for($r=0;$r<$numrec;$r++)
        {
            $_recepcion=pg_fetch_row($recepciones);
            
            if($_recepcion[5]==1)
            {
                $detalle_recepcion = pg_query($conn, "
		SELECT
		art_codigo,
		art_glosa,
		stock_cant,
		stock_subtotal,
		forma_nombre
		FROM 
		stock
		JOIN articulo ON stock_art_id=art_id
		LEFT JOIN bodega_forma ON art_forma=forma_id
		WHERE stock_log_id=".$_recepcion[0]."");
                
                $cabezera_html.='
                <table style="font-size: 11px;">
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Folio Recep.:</td>
                        <td colspan=0 style="font-size:14px;text-align: left;"><b>'.$_recepcion[6].'</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Fecha:</td>
                        <td colspan=0 style="text-align: left;"><b>'.$_recepcion[1].'</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Ubicaci&oacute;n:</td>
                        <td colspan=0 style="text-align: left;">'.htmlentities($_recepcion[4]).'</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Funcionario:</td>
                        <td colspan=0 style="text-align: left;">'.htmlentities($_recepcion[2]).'</td>
                    </tr>
                </table>
                ';
                
                $detalles_html='
                <br>
                <table style="font-size: 11px;" border=1>
                    <tr class="tabla_header" style="font-weight: bold;">
                        <td>Codigo Int.</td>
                        <td>Glosa</td>
                        <td>Cantidad</td>
                        <td>Forma/Unidad</td>
                        <td>P. Unit.</td>
                        <td>Subtotal</td>
                    </tr>
                ';
                
                $sumatoria=0;
                    
                for($i=0;$i<pg_num_rows($detalle_recepcion);$i++)
                {
                    $detalle_a = pg_fetch_row($detalle_recepcion);
                    ($i%2==0) ? $clase='tabla_fila':$clase='tabla_fila2';
                    $detalles_html.="
                        <tr class='".$clase."'>
                            <td style='text-align: right;'>".$detalle_a[0]."</td>
                            <td>".htmlentities($detalle_a[1])."</td>
                            <td style='text-align: right;'>".number_formats($detalle_a[2])."</td>
                            <td style='text-align: left;'>".htmlentities($detalle_a[4])."</td>
                            <td style='text-align: right;'>$".number_format(($detalle_a[3]/$detalle_a[2]),2,',', '.').".-</td>
                            <td style='text-align: right;'>$".number_formats($detalle_a[3]).".-</td>
                        </tr>
                        ";
                        $sumatoria+=$detalle_a[3];
                    }
                    
                    if($recepcion_a[7]>0 AND $numrec==1)
                    {
                        ($i%2==0) ? $clase='tabla_fila'   :  $clase='tabla_fila2';
                        $detalles_html.='
                        <tr class="'.$clase.'" style="color:red;">
                            <td style="text-align: right;">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align:right;">1</td>
                            <td style="text-align: right;">&nbsp;</td>
                            <td style="text-align: right;">$'.number_formats($recepcion_a[7]).'.-</td>
                        </tr>
                        ';
                        $sumatoria-=$recepcion_a[7];
                    }
                    
                    $neto=round($sumatoria);
                    $totalgeneral+=$sumatoria;
                    $total=round($sumatoria*$recepcion_a[5]); // Multiplica por el IVA Asociado.
                    $iva=$total-$neto;
                    
                    //SI ES DE LA CENABAST
                    if((trim($recepcion_a[9]) == 'PROGRAMA MINISTERIALES' || trim($recepcion_a[9]) == 'INTERM' || trim($recepcion_a[9]) == 'HEPATITI C') && (($iva - intval($iva)) > 0))
                        $iva++;
                    
                    // Necesario para bug en impresión de Mozilla Firefox
                    // inserta un page-break despues de las recepciones pares que no son
                    // la ultima. Ej. Imprime max 2 recepciones por hoja y luego se pasa a la
                    // sig.. 
                    if(($r==1 and $r<pg_num_rows($recepciones)-1) or ($r==3 and $r<pg_num_rows($recepciones)-1) or ($r==5 and $r<pg_num_rows($recepciones)-1) or ($r==7 and $r<pg_num_rows($recepciones)-1))
                    {
                        $p_break='style="page-break-after: always;"';
                    }
                    else
                    {
                        $p_break='';
                    }
                    
                    if($numrec==1)
                        $totaltitulo='Total General';
                    else
                        $totaltitulo='Subtotal';
                    
                    $detalles_html.='
                    <tr class="tabla_header">
                        <td colspan=4 rowspan=3 style="text-align:center;vertical-align: central;"><b>'.$totaltitulo.'</b></td>
                        <td><b>Neto:<b></td>
                        <td style="text-align:right;"><b>$'.number_formats($neto).'.-</b></td>
                    </tr>
                    <tr class="tabla_header">
                        <td><b>I.V.A.:</b></td>
                        <td style="text-align:right;"><b>$'.number_formats($iva).'.-</b></td>
                    </tr>
                    <tr class="tabla_header">
                        <td><b>Total:</b></td>
                        <td style="text-align:right;"><b>$'.number_formats($total).'.-</b></td>
                    </tr>
                </table>
                ';  
            }
            else
            {
                $detalle_recepcion = pg_query($conn, "
                SELECT
                serv_glosa,
                serv_cant,
                serv_subtotal,
                serv_item,
                item_glosa
                FROM 
                servicios
                JOIN item_presupuestario ON serv_item=item_codigo
                WHERE serv_log_id=".$_recepcion[0]."
                ");
                
                $_centro=cargar_registros_obj("SELECT * FROM cargo_centro_costo WHERE log_id=".$_recepcion[0]."");
                $_gasto=cargar_registros_obj("SELECT * FROM cargo_gasto_externo WHERE log_id=".$_recepcion[0]."");
                
                if($_centro)
                {
                    $centro_desc=cargar_registros_obj("SELECT * FROM centro_costo WHERE centro_ruta='".$_centro[0]['centro_ruta']."'");
                    $centros='
                    <tr>
                        <td style="text-align: right; font-weight: bold;">Centro de Costo:</td>
                        <td colspan=3 style="font-weight:bold;">'.htmlentities($centro_desc[0]['centro_nombre']).'</td>
                    </tr>
                    ';
                }
                else
                {
                    $gasto_desc=cargar_registros_obj("SELECT * FROM gasto_externo WHERE gastoext_id=".$_gasto[0]['gastoext_id']."");
                    $centros='
                    <tr>
                        <td style="text-align: right; font-weight: bold;">Gasto Subdistribuido:</td>
                        <td colspan=3 style="font-weight:bold;">'.$gasto_desc[0]['gastoext_nombre'].'</td>
                    </tr>
                    ';
                }
                
                $detalles_html.='
                <table style="font-size: 11px;">
                    <tr>
                        <td style="text-align: right; font-weight: bold;">Fecha:</td>
                        <td colspan=3><b>'.$_recepcion[1].'</b></td>
                    </tr>
                    '.$centros.'
                    <tr>
                        <td style="text-align: right; font-weight: bold;">Funcionario:</td>
                        <td colspan=3>'.htmlentities($_recepcion[2]).'</td>
                    </tr>
                    <tr class="tabla_header" style="font-weight: bold;">
                        <td style="width:40%;">Glosa</td>
                        <td>Item Presupuestario</td>
                        <td>Cantidad</td>
                        <td>Subtotal</td>
                    </tr>
                ';
                
                $sumatoria=0;
                
                for($i=0;$i<pg_num_rows($detalle_recepcion);$i++)
                {
                    $detalle_a = pg_fetch_row($detalle_recepcion);
                    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                    $detalles_html.="
                    <tr class='".$clase."'>
                        <td>".$detalle_a[0]."</td>
                        <td>".$detalle_a[3]." - ".$detalle_a[4]."</td>
                        <td style='text-align: right;'>".number_formats($detalle_a[1])."</td>
                        <td style='text-align: right;'>$".number_formats($detalle_a[2]).".-</td>
                    </tr>
                    ";
                    $sumatoria+=$detalle_a[2];
                }
                ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                
                $detalles_html.="
                <tr class='".$clase."'>
                    <td colspan='3' style='text-align: right;'>Descuento Neto:</td>
                    <td style='text-align: right;'>$".$recepcion_a[7].".-</td>
                </tr>
                ";
                
                $sumatoria-=$recepcion_a[7];
                $neto=$sumatoria;
                $totalgeneral+=$sumatoria;
                $total=$sumatoria*$recepcion_a[5]; // Multiplica por el IVA Asociado.
                $iva=$total-$neto;
                // Necesario para bug en impresión de Mozilla Firefox
                // inserta un page-break despues de las recepciones pares que no son
                // la ultima. Ej. Imprime max 2 recepciones por hoja y luego se pasa a la
                // sig.. 
                
                if(($r==1 and $r<pg_num_rows($recepciones)-1) or ($r==3 and $r<pg_num_rows($recepciones)-1) or ($r==5 and $r<pg_num_rows($recepciones)-1) or ($r==7 and $r<pg_num_rows($recepciones)-1))
                {
                    $p_break='style="page-break-after: always;"';
                }
                else
                {
                    $p_break='';
                }
                
                if($numrec==1)
                    $totaltitulo='Total General'; 
                else
                    $totaltitulo='Subtotal';
                
                $detalles_html.='
                <tr class="tabla_header">
                    <td colspan=2 rowspan=3 style="text-align:center;vertical-align: central;"><b>'.$totaltitulo.'</b></td>
                    <td><b>Neto:</b></td>
                    <td style="text-align:right;"><b>$'.number_formats($neto).'.-</b></td>
                </tr>
                <tr class="tabla_header">
                    <td><b>I.V.A.:</b></td>
                    <td style="text-align:right;"><b>$'.number_formats($iva).'.-</b></td>
                </tr>
                <tr class="tabla_header">
                    <td><b>Total:</b></td>
                    <td style="text-align:right;"><b>$'.number_formats($total).'.-</b></td>
                </tr>
                </table>
                ';  
            }
        }

        $orden=$recepcion_a[8];
        $ordenes_html='';
        if($orden!='0')
        {
            $ordenes_html='<tr>
            <td style="text-align:right;">&Oacute;rden de Compra:</td>
            <td>';
            $ordenes=cargar_registros_obj("SELECT orden_numero, orden_id, date_trunc('second', orden_fecha) AS orden_fecha FROM orden_compra WHERE orden_numero='".($recepcion_a[9])."' ORDER BY orden_fecha", true);
            if($ordenes)
            {
                for($i=0;$i<count($ordenes);$i++)
                {
                    $ordenes_html.='<a href="#" class="texto_tooltip" onClick="abrir_orden(\''.$ordenes[$i]['orden_id'].'\');">'.$ordenes[$i]['orden_numero'].'</a>
                    <span style="font-size:10px;">['.$ordenes[$i]['orden_fecha'].']</span><br>';
                }
            }
            else
            {
                $ordenes_html.=$recepcion_a[9];    
            }
            $ordenes_html.='</td></tr>';
        }
        else
        {
            if($recepcion_a[9]=='')
                $ordenes_html='<tr>
                    <td style="text-align:right;">&Oacute;rdenes de Compra:</td>
                    <td><em>No hay ordenes de compra asociadas.</em></td>
                </tr>';	
            else
                $ordenes_html='<tr>
                <td style="text-align:right;">&Oacute;rdenes de Compra:</td>
                <td>'.$recepcion_a[9].'</td>
                </tr>';
        }
        
        $series = cargar_registros_obj(
        "
        SELECT art_codigo, art_glosa, forma_nombre, stock_serie, stock_vence
        FROM documento
        JOIN logs ON doc_id=log_doc_id
        JOIN stock ON stock_log_id=log_id
        JOIN articulo ON stock_art_id=art_id
        LEFT JOIN bodega_forma ON art_forma=forma_id
        JOIN stock_refserie USING (stock_id)
        WHERE doc_id=$doc_id
        ORDER BY art_codigo 
        "
        );
        
        $partidas = cargar_registros_obj(
        "
        SELECT art_codigo, art_glosa, forma_nombre, stock_partida, stock_vence
        FROM documento
        JOIN logs ON doc_id=log_doc_id
        JOIN stock ON stock_log_id=log_id
        JOIN articulo ON stock_art_id=art_id
        LEFT JOIN bodega_forma ON art_forma=forma_id
        JOIN stock_refpartida USING (stock_id)
        WHERE doc_id=$doc_id
        ORDER BY art_codigo 
        "
        );
        
        if($series)
        {
            $series_html='
            <table width=100% style="font-size: 11px;">
                <tr class="tabla_header" style="font-weight: bold;">
                    <td>Codigo Int.</td>
                    <td>Descripci&oacute;n</td>
                    <td>Forma</td>
                    <td>Fecha de Venc.</td>
                    <td>Nro. de Serie</td>
                </tr>
            ';
            for($i=0;$i<count($series);$i++)
            {
                $reg=$series[$i];
                ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                $series_html.='<tr class="'.$clase.'">';
                    $series_html.='<td style="text-align:right;">'.$reg['art_codigo'].'</td>';
                    $series_html.='<td>'.htmlentities($reg['art_glosa']).'</td>';
                    $series_html.='<td>'.htmlentities($reg['forma_nombre']).'</td>';
                    $series_html.='<td style="text-align:center;">'.$reg['stock_vence'].'</td>';
                    $series_html.='<td>'.$reg['stock_serie'].'</td>';
                $series_html.='</tr>';
            }
            $series_html.='</table>';
        }
        else
            $series_html='';
        
        if($partidas)
        {
            $partidas_html='
            <table width=100% style="font-size: 11px;">
                <tr class="tabla_header" style="font-weight: bold;">
                    <td>Codigo Int.</td>
                    <td>Descripci&oacute;n</td>
                    <td>Forma</td>
                    <td>Fecha de Venc.</td>
                    <td>Nro. de Partida</td>
                </tr>
            ';
            for($i=0;$i<count($partidas);$i++)
            {
                $reg=$partidas[$i];
                ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                $partidas_html.='<tr class="'.$clase.'">';
                    $partidas_html.='<td style="text-align:right;">'.$reg['art_codigo'].'</td>';
                    $partidas_html.='<td>'.htmlentities($reg['art_glosa']).'</td>';
                    $partidas_html.='<td>'.htmlentities($reg['forma_nombre']).'</td>';
                    $partidas_html.='<td style="text-align:center;">'.$reg['stock_vence'].'</td>';
                    $partidas_html.='<td>'.$reg['stock_partida'].'</td>';
                $partidas_html.='</tr>';
            }
            $partidas_html.='</table>';
        }
        else
            $partidas_html='';
  
        $mods=cargar_registros_obj("
        SELECT dm.*, f1.func_nombre AS func_nombre1, f2.func_nombre AS func_nombre2 
        FROM documento_modificaciones AS dm
        LEFT JOIN funcionario AS f1 ON f1.func_id=func_id1
        LEFT JOIN funcionario AS f2 ON f2.func_id=func_id1
        WHERE doc_id=$doc_id
        ORDER BY docm_fecha_realiza, docm_fecha_autoriza DESC;");
        
        $detalle_orden='';
        $detalle_orden.='<table style="font-size: 12px;">';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left; width:150px;">Correlativo Int.:</td>';
            $detalle_orden.='<td style="font-size: 20px;text-align: left;"><b>'.$recepcion_a[6].'</b></td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left; width:150px;">Fecha de Recepci&oacute;n:</td>';
            $detalle_orden.='<td style="text-align: left;">'.$recepcion_a[0].'</td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left;">RUT Proveedor:</td>';
            $detalle_orden.='<td style="text-align: left;"><b>'.$recepcion_a[2].'</b></td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left;">Nombre del Proveedor:</td>';
            $detalle_orden.='<td style="text-align: left;">'.htmlentities($recepcion_a[3]).'</td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left;">Tipo de Documento:</td>';
            $detalle_orden.='<td style="text-align: left;"><b>'.htmlentities($recepcion_a[1]).'</b></td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left;">N&uacute;mero:</td>';
            $detalle_orden.='<td style="text-align: left;"><b>'.htmlentities($recepcion_a[4]).'</b></td>';
        $detalle_orden.='</tr>';
            $detalle_orden.=$ordenes_html;
        $detalle_orden.='<tr>';
            $detalle_orden.='<td style="text-align: left;">Observaciones:</td>';
            $detalle_orden.='<td style="text-align: left;">'.htmlentities($recepcion_a[10]).'</td>';
        $detalle_orden.='</tr>';
        $detalle_orden.='</table>';
        
        
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"Pedido ".$pedido_nro."\".xls\";");
        
        print($detalle_orden);
        print("<br>");
        print($cabezera_html);
        print("<br>");
        print($detalles_html);
        
        
        
    }

?>