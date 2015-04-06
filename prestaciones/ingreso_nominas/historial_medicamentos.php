<?php
  require_once('../../conectar_db.php');
  $paciente = $_GET['paciente'];
?>
<script>


</script>
<table width='100%;'>
  <tr>
    <td>
      <select id='select_historia' name='select_historia' onChange='mostrar_historia();'>
        <option value='1'>Historial de Medicamentos</option>
        <option value='2'>Historial de Ex&aacute;menes</option>
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <div class='sub-content3' id='div_hist_medicamentos' style='overflow:auto;'>
        <?php
        $consulta="SELECT *,CASE WHEN fecha_fin>current_timestamp THEN false ELSE recetad_terminada END AS terminada FROM(
        SELECT recetad_id,receta_numero,recetad_dias, recetad_horas,
        recetad_cant,
        (
          case when art_unidad_cantidad is null then
    	     (((recetad_dias*24)/recetad_horas)*recetad_cant)
          else
    	      ceil((((recetad_dias*24)/recetad_horas)*recetad_cant)/art_unidad_cantidad)
          end
        )as total,
        COALESCE((-SUM(stock_cant)),0)AS despachado,
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
        art_glosa,art_codigo,forma_nombre,receta_fecha_emision,receta_vigente,recetad_terminada,art_unidad_adm,art_unidad_cantidad
        ORDER BY receta_fecha_emision DESC)AS foo ORDER BY terminada, recetad_id desc,receta_fecha_emision DESC";

        $rec=cargar_registros_obj($consulta, true);
        if(!$rec) {
          print('<table><tr><td>NO PRESENTA HISTORIAL DE MEDICAMENTOS</td></tr></table>');
        } else {
          print('<table width="100%;">');
            print('<tr class="tabla_header" style="font-weight: bold;">');
              print('<td colspan=7>Detalle de Medicamentos Recetados</td>');
            print('</tr>');
            print('<tr class="tabla_header" style="font-weight: bold;">');
              print('<td>Medicamento</td>');
              print('<td>Dosis</td>');
              print('<td>Total R.</td>');
              print('<td>Total D.</td>');
              print('<td>Ver</td>');
            print('</tr>');
            for($j=0;$j<sizeof($rec);$j++) {
              ($j%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
              if($rec[$j]['terminada']=='f') {
                if($rec[$j]['recetad_terminada']=='f') {
                  $color='#81F781';
                } else {
                  $color='#FA5858';
                }
              } else {
                $color='#FA5858';
              }
              print('<tr class='.$clase.' >');
                print('<td style="background-color:'.$color.';font-size:11px;">'.$rec[$j]['art_glosa'].'</td>');
                if($rec[$j]['recetad_horas']*1<=24) {
                  $div_h=1;
                  $txt_horas='horas';
                } else {
                  if(($rec[$j]['recetad_horas'])%24==0) {
                    $div_h=24;
                    $txt_horas='d&iacute;a(s)';
                  } else {
                    $div_h=1;
                    $txt_horas='horas';
                  }
                }
                if($rec[$j]['recetad_dias']*1<=30) {
                  $div_d=1;
                  $txt_dias='d&iacute;a(s).';
                } else {
                  if(($rec[$j]['recetad_dias'])%30==0) {
                    $div_d=30;
                    $txt_dias='mes(es).';
                  } else {
                    $div_d=1;
                    $txt_dias='d&iacute;a(s).';
                  }
                }
                print('<td style="text-align:left;background-color:'.$color.';font-size:11px;">');
                  print('<i>'.($rec[$j]['recetad_cant']*1).' '.strtoupper($rec[$j]['forma_nombre']).' cada '.($rec[$j]['recetad_horas']/$div_h).' '.$txt_horas.' durante '.($rec[$j]['recetad_dias']/$div_d).' '.$txt_dias.'</i>');
                print('</td>');
                print('<td style="text-align:center;background-color:'.$color.';font-size:14px;">');
                  print(''.number_format($rec[$j]['total']*1,2,',','').'');
                print('</td>');
                print('<td style="text-align:center;background-color:'.$color.';font-size:14px;">');
                  print(''.number_format($rec[$j]['despachado']*1,2,',','').'');
                print('</td>');
                print('<td style="text-align: center;" style="backgroun-color:#FFFFFF;">');
                  print('<b><i><img src="../../iconos/zoom.png" style="cursor:pointer;" onClick="ver_vigencia('.$rec[$j]["recetad_id"].');"></i></b>');
                print('</td>');
              print('</tr>');
            }
          print('</table>');
        }
        ?>
      </div>
      <div class='sub-content3' id='div_hist_examenes' style='overflow:auto;display:none;'>
        <?php
        $nomd_id =  $_GET['nomd_id'];
        $consulta="SELECT *,
        sol_examd_fecha_realizada::date as fecha_realizado ,
        sol_fecha::date fecha_solicitud
        FROM solicitud_examen_detalle
        LEFT JOIN procedimiento_codigo on sol_examd_cod_presta=pc_id
        JOIN nomina_detalle ON  sol_examd_nomd_id = nomd_id
        JOIN solicitud_examen on sol_exam_id=sol_examd_solexam_id
        WHERE pac_id = $paciente order by sol_fecha DESC";
        $reg_examenes=cargar_registros_obj($consulta, true);

        if(!$reg_examenes) {
          print('<table><tr><td>NO PRESENTA HISTORIAL DE MEDICAMENTOS</td></tr></table>');
        } else {
          print('<table width="100%;">');
            print('<tr class="tabla_header">');
              print('<td>C&oacute;digo</td>');
              print('<td>Cant.</td>');
              print('<td>Descripci&oacute;n</td>');
              print('<td>Disparos</td>');
              print('<td>Fecha Solicitud</td>');
              print('<td>Fecha Realizado</td>');
              print('<td>Informe</td>');
            print('</tr>');
            for($i=0;$i<count($reg_examenes);$i++) {
              ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
              if($reg_examenes[$i]['sol_examd_nomd_id']==$nomd_id){
                $color='#81F781';
              } else {
                $color='';
              }

              print('<tr class="'.$clase.'" style="background-color:'.$color.';">');
                print('<td style="text-align: center;">');
                  print(''.$reg_examenes[$i]['pc_codigo'].'');
                print('</td>');
                print('<td style="text-align: center;">');
                  print(''.$reg_examenes[$i]['sol_examd_cant'].'');
                print('</td>');
                print('<td style="text-align: left;">');
                  print(''.$reg_examenes[$i]['pc_desc'].'');
                print('</td>');
                print('<td style="text-align: center;">');
                  print(''.$reg_examenes[$i]['sol_examd_disparos'].'');
                print('</td>');
                print('<td style="text-align: center;">');
                  print(''.$reg_examenes[$i]['fecha_solicitud'].'');
                print('</td>');
                print('<td style="text-align: center;">');
                  print(''.$reg_examenes[$i]['fecha_realizado'].'');
                print('</td>');
                print('<td>');
                  print('<center>');
                    print("<img src='../../iconos/script_edit.png'  style='cursor:pointer;' onClick='informe_examen(".$reg_examenes[$i]['sol_examd_id'].");' />");
                  print('</center>');
                print('</td>');
              print('</tr>');
            }
          print('</table>');
        }
        ?>
      </div>
    </td>
  </tr>
</table>
