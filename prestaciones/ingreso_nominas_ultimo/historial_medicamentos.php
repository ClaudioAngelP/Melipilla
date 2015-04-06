<?php
    require_once('../../conectar_db.php');
    $paciente = $_GET['paciente'];
?>
<div class='sub-content3' style='overflow:auto;'>
    <table width='100%;'>
    <?php
    $consulta="SELECT *,CASE WHEN fecha_fin>current_timestamp THEN false ELSE recetad_terminada END AS terminada FROM(
    SELECT recetad_id,receta_numero,recetad_dias, recetad_horas, recetad_cant,
    (((recetad_dias*24)/recetad_horas)*recetad_cant) AS total,COALESCE((-SUM(stock_cant)),0)AS despachado,
    art_glosa, art_codigo, COALESCE(art_unidad_adm,forma_nombre)AS forma_nombre,receta_fecha_emision::date,
    ((receta_fecha_emision)+(recetad_dias||' days')::interval)::date AS fecha_fin,receta_vigente,recetad_terminada
    FROM receta
    LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
    LEFT JOIN logs ON log_recetad_id=recetad_id
    LEFT JOIN stock ON stock_log_id=log_id
    LEFT JOIN articulo ON recetad_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE receta_paciente_id=$paciente
    group by recetad_id,receta_numero,recetad_id,recetad_dias,recetad_horas,recetad_cant,
    art_glosa,art_codigo,forma_nombre,receta_fecha_emision,receta_vigente,recetad_terminada,art_unidad_adm
    ORDER BY receta_fecha_emision DESC)AS foo ORDER BY terminada, receta_fecha_emision DESC;";
    
    $rec=cargar_registros_obj($consulta, true);
    if(!$rec)
    {
        print("<tr class='tabla_header' style='font-weight: bold;'><center>Sin Historial Disponible...</center></tr>");
    }
    else
    {
        print('
        <tr class="tabla_header" style="font-weight: bold;">
            <td colspan=7>Detalle de Medicamentos Recetados</td>
        </tr>
        <tr class="tabla_header" style="font-weight: bold;">
            <td>Medicamento</td>
            <td>Dosis</td>
            <td>Ver</td>
        </tr>
        ');
        for($j=0;$j<sizeof($rec);$j++)
        {
            ($j%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            if($rec[$j]['terminada']=='f')
            {
                if($rec[$j]['recetad_terminada']=='f')
                {
                    $color='#81F781';
                }
                else
                {
                    $color='#FA5858';
                }
            }
            else
            {
                $color='#FA5858';
            }
            print('<tr class='.$clase.' >
	    <td style="background-color:'.$color.'">'.$rec[$j]['art_glosa'].'</td>
            ');
            if($rec[$j]['recetad_horas']*1<=24)
            {
                $div_h=1;
                $txt_horas='horas';
            }
            else
            {
                if(($rec[$j]['recetad_horas'])%24==0)
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
            if($rec[$j]['recetad_dias']*1<=30)
            {
                $div_d=1;
                $txt_dias='d&iacute;a(s).';
            }
            else
            {
                if(($rec[$j]['recetad_dias'])%30==0)
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
            print('
            <td style="text-align:center;background-color:'.$color.'"><i>'.$rec[$j]['recetad_cant'].' '.strtoupper($rec[$j]['forma_nombre']).' cada '.($rec[$j]['recetad_horas']/$div_h).' '.$txt_horas.' durante '.($rec[$j]['recetad_dias']/$div_d).' '.$txt_dias.'</i></td>
            <td style="text-align: center;" style="backgroun-color:#FFFFFF;">
            <b><i><img src="../../iconos/zoom.png" style="cursor:pointer;" onClick="ver_vigencia('.$rec[$j]["recetad_id"].');"></i></b>
            </td>
            </tr>');
            //onClick="visualizar_receta('.$rec[$j]["receta_id"].');"  
        } 	
    }		
    ?>
    </table>
</div>
