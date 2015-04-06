<?php
    require_once('../../conectar_db.php');
    $paciente = $_GET['paciente'];
    $bodega_id = $_GET['bodega_id'];
    $recetas = cargar_registros_obj("
        SELECT 
        receta_id,
        doc_rut,
        doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS doc_nombre,
        date_trunc('second', receta_fecha_emision) AS receta_fecha,
        receta_comentarios,
        receta_diag_cod,
        diag_desc,
        COALESCE(receta_cronica, false) AS receta_cronica,
        tipotalonario_nombre,
        receta_numero,
        receta_tipotalonario_id
        FROM receta
        LEFT JOIN doctores ON receta_doc_id=doc_id
        LEFT JOIN diagnosticos ON receta_diag_cod=diag_cod
        LEFT JOIN receta_tipo_talonario 
        ON receta_tipotalonario_id=tipotalonario_id
        WHERE receta_paciente_id=".$paciente." AND receta_vigente
        ORDER BY receta_fecha_emision DESC
        ");
        $count_recetas=0;
        $ids_recetas='';
        $num=count($recetas);
        for($i=0;$i<$num;$i++)
        {
            $ids_recetas.=$recetas[$i]['receta_id'];
            if($i<$num-1)
                $ids_recetas.=',';
        }
        // print('<b>'.$ids_recetas.'</b>');
        if($ids_recetas!='')
            $detalle = cargar_registros("
            SELECT
            recetad_id,
            art_id,
            art_codigo,
            art_glosa,
            COALESCE(clasifica_nombre,'(No Asignado...)'),
            COALESCE(forma_nombre,'(No Asignado...)'),
            ((((recetad_dias*24)/recetad_horas)*recetad_cant)/COALESCE( art_unidad_cantidad, 1 )) AS total,
            0 AS stock,
            recetad_cant,
            recetad_horas,
            recetad_dias,
            COALESCE((
                SELECT SUM(stock_cant) 
                FROM stock
                JOIN logs ON stock_log_id=log_id 
                AND log_recetad_id=recetad_id
                WHERE stock_art_id=recetad_art_id
            ),0) AS stock_entregado,
            recetad_receta_id,
            COALESCE( art_unidad_cantidad, 1 ) AS art_unidad_cantidad_adm,
            upper(COALESCE(art_unidad_adm, forma_nombre)) AS art_unidad_administracion,
            CEIL((((recetad_dias*24)/(recetad_horas))*recetad_cant)/COALESCE(art_unidad_cantidad, 1)) AS total_entregar
            FROM recetas_detalle
            JOIN articulo ON recetad_art_id=art_id
            LEFT JOIN bodega_clasificacion ON art_clasifica_id=clasifica_id
            LEFT JOIN bodega_forma ON art_forma=forma_id
            WHERE recetad_receta_id IN ($ids_recetas)
            ", false);                               
        else
            $detalle=false;
      
        $ids_art='';
        if($detalle)
            for($i=0;$i<count($detalle);$i++)
            {
                if(!isset($detx[$detalle[$i][12]]))
                    $n=0;
                else
                    $n=count($detx[$detalle[$i][12]]);
        
                $ids_art.=$detalle[$i][1];
                if($i<count($detalle)-1)
                    $ids_art.=',';
                $detx[$detalle[$i][12]][$n]=$detalle[$i];
            }
        if($ids_art!='')
            $stock = cargar_registros_obj("SELECT stock_art_id, SUM(stock_cant) AS total FROM stock_precalculado
            WHERE stock_art_id IN ($ids_art) AND stock_bod_id=$bodega_id
            GROUP BY stock_art_id");
        else
            $stock=false;
        
        if($stock)
            for($i=0;$i<count($stock);$i++)
            {
                $artx[$stock[$i]['stock_art_id']]=$stock[$i]['total'];
            }
            
        if($recetas)
            for($i=0;$i<count($recetas);$i++)
            {
                $receta = $recetas[$i];
                ($receta['receta_cronica']=='t') ? $descr_cronica='S&iacute;' : $descr_cronica='No';
                if($receta['receta_tipotalonario_id']==0)
                {
                    $receta['tipotalonario_nombre']='Receta Aguda';
                }
                $recetahtml="
                <div id='receta_".$receta['receta_id']."' name='receta_".$receta['receta_id']."'>
                    <input type='hidden' name='receta_id' id='receta_id' value='".$receta['receta_id']."'>
                    <table width=100%>
                        <tr>
                            <td style='text-align: right; width:100px;'>Fecha Emision:</td>
                            <td colspan=3><b>".$receta['receta_fecha']."</b> <i>ID: [".$receta['receta_id']."]</i></td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Tipo de Receta:</td>
                            <td><b>".htmlentities($receta['tipotalonario_nombre'])."</b></td>
                            <td style='text-align:right;'>Cr&oacute;nica:</td>
                            <td><b>".$descr_cronica."</b></td>
                        </tr>
                ";
                if($receta['receta_tipotalonario_id']!=0)
                {
                    $recetahtml.="
                        <tr> 
                            <td style='text-align: right;'>N&uacute;mero Receta:</td>
                            <td colspan=3 style='font-size: 16px;'><b>".$receta['receta_numero']."</b></td>
                        </tr>
                    ";
                }
                $recetahtml.="
                        <tr>
                            <td style='text-align: right;'>M&eacute;dico:</td>
                            <td colspan=3>
                                <b>".$receta['doc_rut']."</b> <i>".htmlentities($receta['doc_nombre'])."</i>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Diagn&oacute;stico:</td>
                            <td colspan=3>
                                <div align='text-align:justify; font-weight: bold;'>
                                    <b><i>".htmlentities($receta['receta_diag_cod'])."</i>
                                    ".htmlentities($receta['diag_desc'])."</b>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Observaciones:</td>
                            <td colspan=3>
                                <div align='text-align:justify;'>
                                    ".htmlentities($receta['receta_comentarios'])."
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class='tabla_header' style='font-weight: bold;'>
                            <td colspan=3>Detalle de Medicamentos Recetados</td>
                            <td style='text-align:right;'><i>Terminar Receta:</i></td>
                            <td>
                                <center>
                                    <img src='iconos/stop.png' style='cursor:pointer;' onClick='cerrar_receta(".$receta['receta_id'].");'>
                                </center>
                            </td>
                        </tr>
                        <tr class='tabla_header' style='font-weight: bold;'>
                            <td>Codigo Int.</td>
                            <td>Glosa</td>
                            <td>Cantidad</td>
                            <td>Stock Disp.</td>
                            <td>Entregar</td>
                        </tr>
                    ";
                $cadena='';
                $activar_boton=false;
                $detalle=$detx[$receta['receta_id']];
                for($a=0;$a<count($detalle);$a++)
                {
                    $fila = ($detalle[$a]);
                    $fila[7]=$artx[$fila[1]];
                    ($a%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
                    $fila[11]=-$fila[11];
                    $max_val=$fila[6]-$fila[11];
                    if($max_val<0)
                        $max_val=0;
                    
                    if($receta['receta_cronica']=='f')
                    {
                        $entrega=ceil(((($fila[10]*24)/($fila[9]))*$fila[8])/$fila[13]);
                    }
                    else
                    {
                        $entrega=ceil(((($fila[10]*24)/($fila[9]))*$fila[8])/$fila[13]);
                        $entrega=ceil($entrega/($fila[10]/30));
                    }
                    if($entrega>=$fila[7] AND $fila[7]>0)
                    {
                        $entrega=$fila[7];
                    }
                    elseif($fila[7]==0)
                    {
                        $entrega=0;
                    }
                    $total=ceil(((($fila[10]*24)/($fila[9]))*$fila[8])/$fila[13]);
                    $recetahtml.="
                        <tr id='art".$fila[0]."' class='$clase'>
                            <td style='text-align: right;'><b>".htmlentities($fila[2])."</b></td>
                            <td>
                                <span id='rec_".$receta['receta_id']."_art_".$fila[1]."' class='texto_tooltip' onClick='abrir_despachos(".$receta['receta_id'].");'>
                                    ".htmlentities($fila[3])."
                                </span>
                            </td>
                            <td style='text-align: right;'>".number_format($total,1,',','.')."</td>
                            <td style='text-align: right;'>".number_format($fila[7],1,',','.')."</td>
                            <td rowspan=2>
                                <center>
                    ";
                    if($fila[11]<$fila[6])
                    {
                        $recetahtml.="
                                    <input type='text' size=3 value='$entrega' id='art_cant_".$fila[0]."' name='art_cant_".$fila[0]."' style='text-align: right;' onKeyUp='this.value=(this.value*1);if(this.value==0) this.value=\"\";if(this.value>$max_val) this.value=$max_val;'>";
                        $activar_boton=true;
                    }
                    else
                    {
                        $recetahtml.="<img src='iconos/tick.png'>";
                    }
                    $recetahtml.="
                                </center>
                            </td>
                        </tr>
                        <tr class='$clase'>
                            <td style='text-align: right;'><b>Dosis:</b></td>
                    ";
                    /*if($receta['receta_cronica']=='f') {
                    $recetahtml.="
                    <td><i><b>".number_format($fila[8], 1,',','.')."</b> U.A. cada 
                    <b>".$fila[9]."</b> horas durante 
                    <b>".$fila[10]."</b> d&iacute;a(s).</i></td>";
                    } else {
                    $recetahtml.="
                    <td><i><b>".number_format($fila[8], 1,',','.')."</b> U.A. cada 
                    <b>".($fila[9]/24)."</b> d&iacute;a(s) durante 
                    <b>".($fila[10]/30)."</b> mes(es).</i></td>";
                    }*/
                    if($fila[9]*1<=24)
                    {
                        $div_h=1;
                        $txt_horas='horas';
                    }
                    else
                    {
                        if(($fila[9])%24==0)
                        {
                            $div_h=24;
                            $txt_horas='d&iacute;a(s)';
        		}
                        else
                        {
                            $div_h=1;
                            $txt_horas='horas';
                        }
                    }
                    if($fila[10]*1<=30)
                    {
                        $div_d=1;
                        $txt_dias='d&iacute;a(s).';
                    }
                    else
                    {
                        if(($fila[10])%30==0)
                        {
                            $div_d=30;
                            $txt_dias='mes(es).';
        		}
                        else
                        {
                            $div_d=1;
                            $txt_dias='d&iacute;a(s).';
                        }
                    }
                    $recetahtml.="
                            <td>
                                <i>
                                    <b>".number_format($fila[8], 1,',','.')." ".$fila[14]."</b> cada 
                                    <b>".($fila[9]/$div_h)."</b> $txt_horas durante 
                                    <b>".($fila[10]/$div_d)."</b> $txt_dias
                                </i>
                            </td>
                    ";
                    $recetahtml.="
                            <td style='text-align: right;'><b>Entregado:</b></td>
                            <td style='text-align: right;'>
                                <span class='texto_tooltip' id='entregado_".$receta['receta_id']."_".$fila[1]."' onClick='abrir_despachos(".$receta['receta_id'].");'>
                                    ".number_format($fila[11], 1, ',', '.')."
                                </span>
                            </td>
                        </tr>
                    ";
                    $art_id=$fila[1]*1;
                    $pac_id=$paciente;
                    $b=cargar_registro("
                    select *, pow((((24/recetad_horas::real)*recetad_cant::real)/unidad::real)/despacho::real,-1) AS dias from (
                    select log_fecha::date, stock_art_id, SUM(-stock_cant) AS despacho, 
                    receta_cronica, recetad_horas, recetad_dias, recetad_cant, 
                    (CURRENT_DATE-log_fecha::date) AS dias_trans, coalesce(art_unidad_cantidad,1) AS unidad
                    from receta
                    join recetas_detalle on recetad_receta_id=receta_id AND recetad_art_id=$art_id
                    join articulo on recetad_art_id=art_id
                    join logs on log_recetad_id=recetad_id AND log_fecha::date between (CURRENT_DATE-'1 month'::interval)::date and CURRENT_DATE
                    join stock on log_id=stock_log_id AND stock_art_id=recetad_art_id
                    WHERE receta_paciente_id=$pac_id
                    GROUP BY log_fecha, stock_art_id, recetad_horas, recetad_dias, recetad_cant, receta_cronica, art_unidad_cantidad
                    ORDER BY log_fecha::date DESC
                    ) as foo;
                    ");
                    if($b)
                    {
                        if($b['dias_trans']*1<=30 AND $b['dias']*1>$b['dias_trans']*1)
                            $recetahtml.='
                        <tr class="'.$clase.'">
                            <td style="text-align: right;font-weight:bold;">Atenci&oacute;n:</td>
                            <td colspan=4>
                                <img src="iconos/error.png"> El dia <b>'.$b['log_fecha'].'</b> se despach&oacute; <b>'.$b['despacho'].' '.$fila[5].'</b> para <b>'.floor($b['dias']).'</b> d&iacute;as; se acaba en <b><u>'.(floor($b['dias'])-$b['dias_trans']*1).' d&iacute;as m&aacute;s</u></b>.
                            </td>
                        </tr>';
                    }
                    $cadena.=$fila[0].'/'.$fila[1].'!';
                }
                $recetahtml.="
                <input type='hidden' name='receta_detalle_".$receta['receta_id']."' id='receta_detalle_".$receta['receta_id']."' value='".$cadena."'>
                ";
                if($activar_boton)
                    $recetahtml.="
                    <tr class='tabla_header'>
                        <td style='text-align: right;'>
                            <b><i></i></b>
                        </td>
                        <td style='text-align: right;'><b><i>Imprimir Talonario:</i></b></td>
                        <td style='text-align: center;'>
                            <b><i><img src='iconos/printer.png' style='cursor:pointer;' onClick='imprimir_talonario(".$receta['receta_id'].");'></i></b>
                        </td>
                        <td style='text-align: right;' >
                            <b><i>Entregar Medicamentos:</i></b>
                        </td>
                        <td>
                            <center><img src='iconos/accept.png' style='cursor:pointer;' onClick='entregar_receta(".$receta['receta_id'].");'></center>
                        </td>
                    </tr>
                    ";
                
                $recetahtml.="
                </table>
                </div>
                <hr>
                ";
                echo $recetahtml;
                $count_recetas++;
            }
            if($count_recetas==0)
            {
                if($_GET['tipo']=='recetario')
                    die('(No tiene Recetas Vigentes...)');
                else
                    die('(No tiene Historial de Recetas...)');
            }
?> 
