<?php
    require_once('../../conectar_db.php');
    $paciente = $_GET['paciente'];
    $bodega_id = $_GET['bodega_id'];
?>
<div class='sub-content3' style='overflow:auto;'>
    <table width='100%;'>
    <?php
    $rec=cargar_registros_obj("
    SELECT receta_id,
    COALESCE(log_fecha, receta_fecha_emision)::date AS fecha,
    receta_cronica,
    receta_numero,
    recetad_dias, recetad_horas, recetad_cant,
    ceil((((recetad_dias*24)/recetad_horas)*recetad_cant)/COALESCE(art_unidad_cantidad, 1)) AS total,
    (-stock_cant) AS despachado,
    receta_bod_id, art_glosa, art_codigo, 
    COALESCE(art_unidad_adm, forma_nombre) AS fnombre,
    recetad_indicaciones,
    receta_vigente,
    date_trunc('Second',receta_fecha_cierre) as receta_fecha_cierre,
    receta_motivo_termino,
    receta_nomd_id,
    receta_hosp_id
    FROM receta
    LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
    LEFT JOIN logs ON log_recetad_id=recetad_id
    LEFT JOIN stock ON stock_log_id=log_id
    JOIN articulo ON recetad_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE receta_paciente_id=$paciente
    ORDER BY COALESCE(log_fecha, receta_fecha_emision) DESC;", true);
 
    print('<tr class="tabla_header" style="font-weight: bold;">');
        print('<td colspan=9>Detalle de Medicamentos Recetados</td>');
    print('</tr>');
    print('<tr class="tabla_header" style="font-weight: bold;">');
        print('<td>Nro Receta</td>');
        print('<td>Fecha Despacho</td>');
        print('<td>Codigo Int.</td>');
        print('<td>Glosa</td>');
        print('<td>Dosis</td>');
        print('<td>Cant. Prescrip.</td>');
        print('<td>Cant. Desp.</td>');
        print('<td>Vigente</td>');
        print('<td>Fecha Cierre</td>');
    print('</tr>');
    for($j=0;$j<sizeof($rec);$j++)
    {
        ($j%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
        
        if(($rec[$j]['receta_nomd_id']*1)!=0 and $rec[$j]['receta_nomd_id']!="")
                $color="background-color: #52F3FF;";
            else
                if(($rec[$j]['receta_hosp_id']*1)!=0 and $rec[$j]['receta_hosp_id']!="")
                    $color="background-color: #98FB98;";
                else
                    $color="";
        
        print('<tr class='.$clase.'>');
            print('<td style="text-align: center;'.$color.'"><b>'.$rec[$j]['receta_numero'].'</b></td>');
            print('<td style="text-align: center;"><b>'.$rec[$j]['fecha'].'</b></td>');
            print('<td style="text-align: right;"><b>'.$rec[$j]['art_codigo'].'</b></td>');
            print('<td>'.$rec[$j]['art_glosa'].'</td>');
            if($rec[$j]['recetad_indicaciones']!='')
                $indicaciones='<br />'.$rec[$j]['recetad_indicaciones'];
            else
                $indicaciones='';
            print('<td style="text-align:center;">');
                print('<i><b>'.number_format(($rec[$j]['recetad_cant']*1),2,',','.').' 
                '.$rec[$j]['fnombre'].'</b> cada '.$rec[$j]['recetad_horas'].'
                horas durante '.$rec[$j]['recetad_dias'].' d&iacute;a(s).
                </i><font color="red">'.$indicaciones.'</font>');
            print('</td>');
            print('<td style="text-align:right;"><b>'.number_format($rec[$j]['total']*1,1,',','.').'</b></td>');
            print('<td style="text-align:right;"><b>'.number_format($rec[$j]['despachado']*1,1,',','.').'</b></td>');
            if($rec[$j]['receta_vigente']!="f")
                print('<td style="text-align:center;"><img src="iconos/tick.png" style="cursor:pointer;"></td>');
            else
                print('<td style="text-align:center;">&nbsp;</td>');
            if($rec[$j]['receta_fecha_cierre']!="" and $rec[$j]['receta_fecha_cierre']!=null)
                print('<td style="text-align:center;"><b>'.$rec[$j]['receta_fecha_cierre'].'</b></td>');
            else
                print('<td style="text-align:center;"><b></b></td>');
            
        print('</tr>');  
    } 
?>
    </table>
</div>