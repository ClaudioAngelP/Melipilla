<?php
    require_once('../../conectar_db.php');
    if(isset($_GET['receta_id']))
        $receta_id=$_GET['receta_id']*1;
    else
        $receta_id=$_POST['receta_id']*1;
    
    if(isset($_POST['stock_id']))
    {
        if(!isset($_POST['editar']))
        {
            $stock_id=$_POST['stock_id'];
            $tmp=cargar_registro("SELECT (log_fecha::date=CURRENT_DATE) AS despacho_editable FROM stock JOIN logs ON stock_log_id=log_id AND stock_id=$stock_id;");
            //if(_cax(505) OR $tmp['despacho_editable']=='t')
            if(_cax(505))
            {
                pg_query("START TRANSACTION;");
                pg_query("INSERT INTO stock_rechazado SELECT * FROM stock WHERE stock_id=$stock_id;");
                pg_query("DELETE FROM stock WHERE stock_id=$stock_id;");
                pg_query("COMMIT;");
            }
            exit();
        }
        else
        {
            $editar=$_POST['editar']*1;
            if($editar==1)
            {
                if(isset($_POST['cant']))
                {
                    $stock_id=$_POST['stock_id']*1;
                    $cant=str_replace(",",".",$_POST['cant']);
                    $cant=$cant*1;
                    $tmp=cargar_registro("SELECT (log_fecha::date=CURRENT_DATE) AS despacho_editable,stock_vence,stock_cant,stock_art_id,stock_bod_id FROM stock JOIN logs ON stock_log_id=log_id AND stock_id=$stock_id;");
                    if(_cax(505))
                    {
                        pg_query("START TRANSACTION;");
                        if(($tmp['stock_cant']*-1)<$cant)
                        {
                            $tmp_total=cargar_registro("SELECT sum(stock_cant)as total FROM stock WHERE stock_art_id=".$tmp['stock_art_id']." AND stock_vence='".$tmp['stock_vence']."' AND stock_bod_id=".$tmp['stock_bod_id'].";");
                            if($tmp_total)
                            {
                                $total_stock=(($tmp_total['total']*1)+($tmp['stock_cant']*-1));
                                if($total_stock>0)
                                {
                                    if($total_stock>=$cant)
                                    {
                                        pg_query("UPDATE stock set stock_cant=".($cant*-1)." WHERE stock_id=$stock_id;");
                                    }
                                    else
                                    {
                                        exit("1");
                                    }
                                }
                                else
                                {
                                    exit("0");
                                }
                            }
                        }
                        else
                        {
                            pg_query("UPDATE stock set stock_cant=".($cant*-1)." WHERE stock_id=$stock_id;");
                        }
                        pg_query("COMMIT;");
                        exit();
                    }
                }
            }
        }
    }
    
    $r=cargar_registro("SELECT * FROM receta JOIN pacientes ON receta_paciente_id=pac_id JOIN funcionario ON receta_func_id=func_id WHERE receta_id=$receta_id;");
    $d=cargar_registros_obj("SELECT 
    log_fecha, func_nombre, art_codigo, art_glosa, -stock_cant AS cantidad, stock_vence, stock_id,
    (log_fecha::date=CURRENT_DATE) AS despacho_editable
    FROM logs 
    LEFT JOIN funcionario ON log_func_if=func_id
    JOIN stock ON stock_log_id=log_id
    JOIN articulo ON stock_art_id=art_id
    WHERE log_recetad_id IN (SELECT recetad_id FROM recetas_detalle WHERE recetad_receta_id=$receta_id)
    ORDER BY log_fecha
    ;", true);
    $rd=cargar_registros_obj("SELECT art_codigo, art_glosa FROM recetas_detalle JOIN articulo ON recetad_art_id=art_id WHERE recetad_receta_id=$receta_id ORDER BY art_glosa", true);
    $totales=array();
    if($rd)
    {
        for($i=0;$i<sizeof($rd);$i++)
        {
            $totales[$rd[$i]['art_codigo']]=Array($rd[$i]['art_glosa'],0);
	}
    }
?>
<html>
    <title>Despachos de Receta</title>
    <?php cabecera_popup('../..'); ?>
    <script>
    
        function eliminar_despacho(stock_id)
        {
            if(!confirm("&iquest;Esta&aacute; seguro que desea eliminar este despacho? - NO HAY OPCIONES DE DESHACER.".unescapeHTML()))
                return;
        
            var myAjax=new Ajax.Request('despachos_receta.php',
            {
                method:'post',
                parameters:'receta_id=<?php echo $receta_id; ?>&stock_id='+stock_id,
                onComplete:function(r)
                {
                    window.location.reload();
                }
            });
        }
        
        function editar_despacho(stock_id)
        {
            $('td_cant_'+stock_id+'').innerHTML="<center><input type='text' style='text-align:right;' id='cant_stock_edit_"+stock_id+"' name='cant_stock_edit_"+stock_id+"' value='"+$('cant_stock_'+stock_id+'').value+"'  onkeypress='return soloNumeros(event);'  /></center>";
            var html_accion="<center>";
            html_accion+="<img src='../../iconos/disk.png' style='cursor:pointer;' onClick='guardar_stock("+stock_id+")' />";
            html_accion+="<img src='../../iconos/cross.png' style='cursor:pointer;' onClick='cancelar_stock("+stock_id+")' />";
            html_accion+="</center>";
            $('td_accion_'+stock_id+'').innerHTML=html_accion;
            
            
        }
        
        function cancelar_stock(stock_id)
        {
            $('td_cant_'+stock_id+'').innerHTML=""+$('cant_stock_'+stock_id+'').value+"";
            var html_accion="<center>";
            html_accion+="<img src='../../iconos/pencil.png' style='cursor:pointer;' onClick='editar_despacho("+stock_id+");' />";
            html_accion+="<img src='../../iconos/delete.png' style='cursor:pointer;' onClick='eliminar_despacho("+stock_id+");' />";
            html_accion+="</center>";
            $('td_accion_'+stock_id+'').innerHTML=html_accion;
        }
        
        
        function guardar_stock(stock_id)
        {
            var cant=$('cant_stock_edit_'+stock_id+'').value*1;
            if(cant<=0)
            {
                alert("Debe ingresar cantidad mayor a cero");
                return;
            }
            if(!confirm("&iquest;Esta&aacute; seguro que desea modificar este despacho? - NO HAY OPCIONES DE DESHACER.".unescapeHTML()))
                return;
        
            var myAjax=new Ajax.Request('despachos_receta.php',
            {
                method:'post',
                parameters:'receta_id=<?php echo $receta_id; ?>&stock_id='+stock_id+'&editar=1&cant='+$('cant_stock_edit_'+stock_id+'').value,
                onComplete:function(r)
                {
                    var resp=r.responseText;
                    if(resp=='0')
                    {
                        alert("NO HAY STOCK DISPONIBLE PARA CAMBIAR LA CANTIDAD INGRESADA AL DESPACHO");
                    }
                    if(resp=='1')
                    {
                        alert("NO HAY STOCK SUFICIENTE PARA CAMBIAR LA CANTIDAD INGRESADA AL DESPACHO");
                    }
                    window.location.reload();
                }
            });
            
            
        }
        
        
        soloNumeros=function(e)
        {
            //var keynum = window.event ? window.event.keyCode : e.which;
            var keynum = (document.all) ? e.keyCode : e.which;
            var keyCrt=e.ctrlKey
            if((keyCrt && keynum==99) || (keyCrt && keynum==118) || (keyCrt && keynum==120))
            {
                return true;
            }
            //alert(keynum);
            if(keynum==0 && !document.all)
                return true;
            
            if (keynum == 8 || keynum == 45 || keynum == 39 || keynum == 75 || keynum == 107 || keynum == 44)
                return true;
        
            if(keynum < 48 || keynum > 57)
            {
                return false;
            }
            return true;
        }
    </script>
    <body style=''>
        <div class='sub-content'>
            <img src='../../iconos/calendar.png'>
            <b>Despachos de Receta</b>
        </div>
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Fecha Emisi&oacute;n:</td>
                    <td><?php echo $r['receta_fecha_emision']; ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>RUN/Ficha:</td>
                    <td><?php echo $r['pac_rut'].' / '.$r['pac_ficha']; ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Nombre Paciente:</td>
                    <td><?php echo $r['pac_nombres'].' '.$r['pac_appat'].' '.$r['pac_apmat']; ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Usuario:</td>
                    <td><?php echo $r['func_nombre']; ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Comentarios:</td>
                    <td><?php echo $r['receta_comentarios']; ?></td>
                </tr>
            </table>
        </div>
        <?php ob_start(); ?>
        <div class='sub-content2'>
            <table style='width:100%;'>
                <tr class='tabla_header'>
                    <td colspan=7>Despachos Realizados a la Fecha</td>
                </tr>
                <tr class='tabla_header'>
                    <td>Fecha</td>
                    <td>Usuario</td>
                    <td>C&oacute;digo</td>
                    <td>Descripci&oacute;n</td>
                    <td>Fec.Venc.</td>
                    <td>Cant.</td>
                    <td>Eliminar</td>
                </tr>
                <?php 
                if($d)
                {
                    for($i=0;$i<sizeof($d);$i++)
                    {
                        $class=($i%2==0)?'tabla_fila':'tabla_fila2';
			if(!isset($totales[$d[$i]['art_codigo']]))
                        {
                            $totales[$d[$i]['art_codigo']]=array($d[$i]['art_glosa'], 0);
			}
                        $totales[$d[$i]['art_codigo']][1]+=$d[$i]['cantidad']*1;
                        print("
			<tr class='$class' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$class\";'>
                            <td style='text-align:center;'><input type='hidden' id='cant_stock_".$d[$i]['stock_id']."' name='cant_stock_".$d[$i]['stock_id']."' value='".number_format($d[$i]['cantidad'],2,',','.')."' style='display:none;'/>".substr($d[$i]['log_fecha'],0,16)."</td>
                            <td style='text-align:center;'>".$d[$i]['func_nombre']."</td>
                            <td style='text-align:right;font-weight:bold;'>".$d[$i]['art_codigo']."</td>
                            <td style='text-align:left;font-size:10px;'>".$d[$i]['art_glosa']."</td>
                            <td style='text-align:center;font-size:10px;'>".$d[$i]['stock_vence']."</td>
                            <td style='text-align:right;' id='td_cant_".$d[$i]['stock_id']."'>
                                ".number_format($d[$i]['cantidad'],2,',','.')."
                            </td>
                            <td id='td_accion_".$d[$i]['stock_id']."'><center>");
                            if(_cax(505))
                            {
                                print("<img src='../../iconos/pencil.png' style='cursor:pointer;' onClick='editar_despacho(".$d[$i]['stock_id'].");' />");
                                print("<img src='../../iconos/delete.png' style='cursor:pointer;' onClick='eliminar_despacho(".$d[$i]['stock_id'].");' />");
                            }
                            else
                                print("<img src='../../iconos/stop.png' />");
                            
                            print("
                            </center></td>
                        </tr>
                        ");
                    }
                }
                ?>
            </table>
        </div>
        <?php $html=ob_get_contents(); ob_end_clean(); ?>
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr class='tabla_header'>
                    <td colspan=3>Totales Generales</td>
                </tr>
                <tr class='tabla_header'>
                    <td>C&oacute;digo</td>
                    <td>Descripci&oacute;n</td>
                    <td>Total</td>
                </tr>
                <?php 
                $i=0;
                foreach($totales AS $codigo => $val)
                {
                    $i++;
                    $class=($i%2==0)?'tabla_fila':'tabla_fila2';
                    print("
                    <tr class='$class' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$class\";'>
                        <td style='text-align:right;font-weight:bold;'>".$codigo."</td>
			<td style='text-align:left;'>".$val[0]."</td>
			<td style='text-align:right;font-weight:bold;'>".number_format($val[1],2,',','.')."</td>
                    </tr>
                    ");
                }
                ?>
            </table>
        </div>
        <?php print ($html); ?>
    </body>
</html>