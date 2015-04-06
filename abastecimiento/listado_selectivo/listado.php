<?php
    require_once('../../conectar_db.php');
    error_reporting(E_ALL);
    $accion=$_POST['accion'];
    $neto=0;
    $total=0;
    
    function evalua_color($valor)
    {
        $color='black';
	if($valor==0)
        {
            $color='black';
	}
        else if($valor>0)
        {
            $color='blue';
	}
        else if($valor<0)
        {
            $color='red';
	}
	return $color;
    }
    if($accion==0 || $accion==2)
    {
        //GENERAR LISTADO PARA CONTEO SELECTIVO	
	$bod_id=$_POST['bodega'];
	$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id", true);
	$bodega=$b['bod_glosa'];
	$item_pres=$_POST['item_pres'];
	$cant=$_POST['limit']*1;
        if($accion==2)
        {
            $cant='';
	}
        else
        {
            $cant=$_POST['limit']*1;
	}
	$fecha_emision=date('d/m/Y');
        
	if($item_pres==0)
            $w_item='';
	else
            $w_item="AND item_codigo='".$item_pres."'";
	
        if($cant!='')
            $limit='LIMIT '.$cant;
	else
            $limit='';	
        
        if(isset($_POST['activados']))
        {  
            $activado_q=" AND art_activado=true"; 
        }
        else
        {
            $activado_q=""; 
        } 
		
	$listado=pg_query("SELECT * FROM 
	(SELECT art_id,art_codigo,art_glosa,
	COALESCE(forma_nombre,'(No Asignado...)') AS forma_nombre,
	calcular_stock(art_id,$bod_id)AS stock_disponible,
	COALESCE((SELECT sum(stock_cant) FROM stock
	JOIN logs ON stock_log_id = logs.log_id
	LEFT JOIN pedido ON logs.log_id_pedido = pedido.pedido_id
	LEFT JOIN pedido_detalle ON
	pedido_detalle.pedido_id = pedido.pedido_id AND
	pedido_detalle.art_id = stock_art_id
	WHERE
	((NOT log_tipo=2) OR
	(log_tipo=2 AND pedidod_estado))
	AND
	stock_art_id=articulo.art_id and
	date_trunc('day', log_fecha)<=CURRENT_TIMESTAMP AND stock_bod_id=$bod_id),0)
	AS stock_total
	from articulo
	LEFT JOIN bodega_forma ON forma_id=art_forma
	LEFT JOIN item_presupuestario ON item_codigo=art_item
	WHERE art_id IN (SELECT DISTINCT stock_art_id FROM stock 
	LEFT JOIN articulo_bodega ON artb_art_id=stock_art_id AND artb_bod_id=stock_bod_id
	WHERE stock_bod_id=$bod_id)
	AND art_activado=true $w_item
	ORDER BY random() $limit)
	AS foo
	order by art_codigo");
		
	pg_query("INSERT INTO listado_selectivo VALUES (DEFAULT,CURRENT_TIMESTAMP,$bod_id,".($_SESSION['sgh_usuario_id']*1).");");
		
	$corr= cargar_registro("SELECT CURRVAL('listado_selectivo_ls_id_seq');");
	//$func=cargar_registros_obj("SELECT func_rut,func_nombre FROM funcionario WHERE func_id=".($_SESSION['sgh_usuario_id']*1).";");
	//$corr=str_replace('-','',date('Y-m-d-H-m-s'));
		
	//$str="Fecha: ".date('d/m/Y')."\r\n";
	//$str.="Bodega: ".$b['bod_glosa']."\r\n";
		
	$correlativo=$corr['currval'];
	$funcionario=cargar_registro("SELECT func_rut,func_nombre FROM funcionario WHERE func_id=".($_SESSION['sgh_usuario_id']*1)."");
	$funcionario=$funcionario['func_nombre'];
	
    }
    else if($accion==1)
    {
        //BUSCAR LISTADO GENERADO ANTERIORMENTE
	if(isset($_POST['comparar']))
            $comparar=$_POST['comparar'];
	else
            $comparar='false';
	
        $ls_id=$_POST['correlativo'];
	if($comparar=='true')
        {
            $comprueba_existencia=cargar_registros_obj("SELECT lsd_art_id, terminado FROM listado_selectivo_detalle 
            LEFT JOIN articulo ON art_id=lsd_art_id
            LEFT JOIN bodega_forma ON forma_id=art_forma
            WHERE lsd_ls_id=$ls_id");
            $falso=0;
            for($n=0;$n<sizeof($comprueba_existencia);$n++)
            {
                if($comprueba_existencia[$n]['terminado']=='f')
                {
                    $falso+=1;
                }
            }
            if($falso>0)
            {
                $actualizar=cargar_registros_obj("SELECT lsd_id,lsd_art_id,lsd_stock FROM listado_selectivo_detalle WHERE lsd_ls_id=$ls_id");
                for($n=0;$n<sizeof($actualizar);$n++)
                {
                    $cantidad=($_POST['art_'.$actualizar[$n]['lsd_art_id']])*1;
                    if($cantidad=="0")
                    {
                        $estado_act = 'true';
                    }
                    else if(empty($cantidad))
                    {
                        $estado_act = 'false';
                    }
                    else
                    {
                        $estado_act = 'true';
                    }
                    $cantidad = $cantidad*1;
                    pg_query("UPDATE listado_selectivo_detalle SET lsd_inventario=".$cantidad.", terminado=$estado_act WHERE lsd_id=".$actualizar[$n]['lsd_id'].";");
                    $array_estado[$n]=$estado_act;
                }
                $count = count($array_estado);
                $falso=0;
                for($i=0;$i<$count;$i++)
                {
                    if($array_estado[$i]=='false')
                    {
                        $falso+=1;
                    }
                }
                if($falso>0)
                {
                    pg_query("UPDATE listado_selectivo SET ls_estado=0 WHERE ls_id=$ls_id");
                }
                else
                {
                    pg_query("UPDATE listado_selectivo SET ls_estado=1 WHERE ls_id=$ls_id");
                }
            }
	}
	$listado=cargar_registro("SELECT ls_id,ls_fecha,bod_glosa,func_rut,func_nombre,ls_estado 
	FROM listado_selectivo 
	LEFT JOIN bodega ON bod_id=ls_bod_id
	LEFT JOIN funcionario ON func_id=ls_func_id
	WHERE ls_id=$ls_id");
	
        /*
        $detalle=cargar_registros_obj("SELECT lsd_art_id,art_codigo,art_glosa,forma_nombre,
	lsd_stock,lsd_inventario,-(lsd_stock-lsd_inventario)AS diferencia,
	ROUND(art_val_ult*-(lsd_stock-lsd_inventario)) AS valor
	FROM listado_selectivo_detalle
	LEFT JOIN articulo ON art_id=lsd_art_id
	LEFT JOIN bodega_forma ON forma_id=art_forma
	WHERE lsd_ls_id=$ls_id");
        * 
        */
        $detalle=cargar_registros_obj("SELECT lsd_art_id,art_codigo,art_glosa,forma_nombre,
        lsd_stock,lsd_inventario,-(lsd_stock-lsd_inventario) AS diferencia,
        ROUND(art_val_ult*(lsd_inventario)) AS valor, art_val_ult, terminado,
        ROUND(-(lsd_stock-lsd_inventario)*(art_val_ult)) AS valorizado, lsd_comentario, lsd_id
        FROM listado_selectivo_detalle
        LEFT JOIN articulo ON art_id=lsd_art_id
        LEFT JOIN bodega_forma ON forma_id=art_forma
        WHERE lsd_ls_id=$ls_id
        ORDER BY art_codigo, valor DESC");
		
	$fecha_emision=$listado['ls_fecha'];
	$correlativo=$ls_id;
	$bodega=$listado['bod_glosa'];	
	$funcionario=$listado['func_nombre'];	
    }
    else
    {
        //COMPARAR LISTADO GUARDADO CON INVENTARIO REALIZADO			
    }
    if(isset($_GET['xls']))
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Listado_Selectivo_---".$correlativo.".XLS\";");
        $strip_html=true;
    }
    else
    {
        
    }
?>
<?php
    if(!empty($listado['ls_id']) || isset($cant)>0)
    {
?>
        <center>
            <h1>
                <u>CONTROL DE EXISTENCIAS</u>
            </h1>
            <div style='width:100%;text-align:left;font-size:14px;'>
                CORRELATIVO: <u><?php echo $correlativo ?></u>
                <input type='hidden' name='corr' id='corr' value='<?php echo $correlativo ?>'>
            </div>
            <div style='width:100%;text-align:left;font-size:14px;'>
                FUNCIONARIO: <u><?php echo $funcionario; ?></u>
            </div>
            <div style='width:100%;text-align:right;font-size:14px;'>
                FECHA: <u><?php echo $fecha_emision; ?></u> BODEGA: <u><?php echo $bodega; ?></u>
            </div>
            <br /><br />
            <table style='width:100%;'>
                <tr class='tabla_header'>
                    <td>N&deg;</td>
                    <td>C&oacute;digo</td>
                    <td>Art&iacute;culo</td>
                    <td>UD</td>
                    <?php
                    if($listado['ls_estado']==0)
                    {
                    ?>
                        <td>Cant. F&iacute;sico</td>
                    <?php
                    }
                    else
                    {
                        print("<td>Stock</td>
                        <td>Inventario</td>
                        <td>Diferencia</td>
                        <td>P. Unit.</td>
                        <td>NETO</td>
                        <td>Valorizado Dif.</td>
                        <td>Observaci&oacute;n</td>
                        ");
                    }
                    ?>
                </tr>
                <?php 
                if($accion==0 || $accion==2)
                {
                    for($i=0;$i<pg_num_rows($listado);$i++)
                    {
                        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		   	$fila = pg_fetch_assoc($listado,$i);
                        $art_codigo=$fila['art_codigo'];
                        $art_glosa=htmlentities($fila['art_glosa']);
                        $art_forma=$fila['forma_nombre'];
			//$str.=utf8_decode($art_codigo.'|'.$art_glosa.'|'.$art_forma.'|'.$fila['stock_total'])."\r\n";
			pg_query("INSERT INTO listado_selectivo_detalle VALUES(DEFAULT,CURRVAL('listado_selectivo_ls_id_seq'),".$fila['art_id'].",".$fila['stock_total'].")");
			print("<tr class='$clase'>
                        <td>".($i+1)."</td>
			<td>$art_codigo</td>
			<td>$art_glosa</td>
			<td>$art_forma</td>
			<td style='width:15%;text-align:center;'>_________</td>
			</tr>");
                    }
                    //$fname='Listado_Selectivo_---'.$corr['currval'].'.txt';
                    //file_put_contents("../../abastecimiento/listado_selectivo/$fname",$str);
                    //$contenido=scandir("../../abastecimiento/listado_selectivo/");
                    //print_r($contenido);
                }
                else if($accion==1)
                {
                    $valorizadoTotal=0;
                    for($i=0;$i<sizeof($detalle);$i++)
                    {
                        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                        $art_codigo=$detalle[$i]['art_codigo'];
			$art_glosa=htmlentities($detalle[$i]['art_glosa']);
			$art_forma=$detalle[$i]['forma_nombre'];
			$comentario=htmlentities($detalle[$i]['lsd_comentario']);
			$lsd_id=$detalle[$i]['lsd_id']*1;
			//$str.=utf8_decode($art_codigo.'|'.$art_glosa.'|'.$art_forma.'|'.$fila['stock_total'])."\r\n";
			print("<tr class='$clase'>
			<td>".($i+1)."</td>
			<td>$art_codigo</td>
			<td>$art_glosa</td>
			<td>$art_forma</td>");
                        
			if($listado['ls_estado']==0)
                        {
                            if($detalle[$i]['terminado']=='t')
                            {
                                $lsd_inventario=$detalle[$i]['lsd_inventario'];
                            }
                            else
                            {
                                $lsd_inventario='';
                            }
                            //ingresar inventario
                            print("<td style='width:15%;text-align:center;'>
                            <input type='text' id='art_".$detalle[$i]['lsd_art_id']."' name='art_".$detalle[$i]['lsd_art_id']."' value='".$lsd_inventario."' size=3></td>
                            ");
			}
                        else
                        {
                            /*
                            //resultado diferencias
                            print("<td style='text-align:right;'>".$detalle[$i]['lsd_stock']."</td>
                            <td style='text-align:right;'>".$detalle[$i]['lsd_inventario']."</td>
                            <td style='text-align:right;'>".$detalle[$i]['diferencia']."</td>
                            <td style='text-align:right;'>\$".number_formats(str_replace('-','',($detalle[$i]['valor']))).".-</td>
                            <td style='text-align:right;'>\$".number_formats(str_replace('-','',($detalle[$i]['valor']*1.19))).".-</td>
                            ");
                            $neto+=($detalle[$i]['valor']);
                            $total+=($detalle[$i]['valor']*1.19);
                            */
                            
                            //resultado diferencias
                            print("<td style='text-align:right;color:black;'>".$detalle[$i]['lsd_stock']."</td>
                            <td style='text-align:right;color:black;'>".$detalle[$i]['lsd_inventario']."</td>
                            <td style='text-align:right;color:".evalua_color($detalle[$i]['diferencia']).";'>".number_formats(str_replace('-','',($detalle[$i]['diferencia'])))."</td>
                            <td style='text-align:right;color:black;'>\$".number_formats(str_replace('-','',($detalle[$i]['art_val_ult']))).".-</td>
                            <td style='text-align:right;color:black;'>\$".number_formats(str_replace('-','',($detalle[$i]['valor']))).".-</td>
                            <td style='text-align:right;color:".evalua_color($detalle[$i]['valorizado']).";'>\$".number_format(str_replace('-','',$detalle[$i]['valorizado'])).".-</td>
                            ");
                            if($comentario=='' or $comentario==null)
                            {
                                print("<td><input type='text' name='comentario_$lsd_id' id='comentario_$lsd_id' maxlength='50' onKeyUp='if(event.which==13) agregar_comentario($lsd_id, this.value);'></td>");
                            }
                            else
                            {
                                print("<td>$comentario</td>");
                            }
                            //number_formats(str_replace('-','',($detalle[$i]['valor']*1.19)))
                            //$total+=($detalle[$i]['art_val_ult']);
                            $neto+=($detalle[$i]['valor']);
                            $valorizadoTotal+=($detalle[$i]['diferencia']*$detalle[$i]['art_val_ult']);
			}
			print("</tr>");		    
		    }
                    if($listado['ls_estado']==1)
                    {
                        //$color_neto=evalua_color($total);
			$color_total=evalua_color($neto);
			$color_val_total=evalua_color($valorizadoTotal);
			print("<tr class='tabla_header'>
			<td style='text-align:right;' colspan=8><b>Total:</b></td>
			<td style='text-align:right;color:$color_total;'>\$".number_formats(str_replace('-','',($neto))).".-</td>
			<td style='text-align:right;color:$color_val_total;'>\$".number_format(str_replace('-','',($valorizadoTotal))).".-</td>
			<td></td></tr>");
                    }
                    /*
                    print("<tr class='tabla_header'>
		    <td style='text-align:right;' colspan=7><b>Total:</b></td>
		    <td style='text-align:right;'>\$".number_formats(str_replace('-','',($neto))).".-</td>
		    <td style='text-align:right;'>\$".number_formats(str_replace('-','',($total))).".-</td>
		    </tr>");
                     * 
                     */
                }
                ?>
            </table>
        </center>
<?php
    }
    else
    {
        echo "Correlativo no existe!";
    }
?>