<?php
    require_once('../../conectar_db.php');
    $paciente = $_GET['paciente'];
    //º$bodega_id = $_GET['bodega_id'];
    $div = $_GET['div'];
?>
<script>
</script>
<div class='sub-content3' style='overflow:auto;'>
    <table width='100%;'>
    <?php
    $rec=cargar_registros_obj("
    SELECT receta_id,
    receta_cronica,
    receta_numero,
    recetad_dias, recetad_horas, recetad_cant,
    (((recetad_dias*24)/recetad_horas)*recetad_cant) AS total,
    COALESCE((-SUM(stock_cant)),0)AS despachado,
    receta_bod_id, art_glosa, art_codigo, forma_nombre
    FROM receta
    LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
    LEFT JOIN logs ON log_recetad_id=recetad_id
    LEFT JOIN stock ON stock_log_id=log_id
    LEFT JOIN articulo ON recetad_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE receta_paciente_id=$paciente
    group by receta_id,receta_cronica,receta_numero,recetad_dias,recetad_horas,recetad_cant,receta_bod_id,
    art_glosa,art_codigo,forma_nombre,receta_fecha_emision
    ORDER BY receta_fecha_emision DESC;", true);
			
    $obs=cargar_registros_obj("SELECT date(nom_fecha) ||' '||nomd_hora as fecha,nomd_hora,doc_nombres||' '||doc_paterno||' '||doc_materno as doc,
    nomd_observaciones,nomd_id,esp_desc,
    nom_fecha,
    0 as numero,
    nomd_diag as diag,
    'N' as tipo
    FROM nomina
    LEFT JOIN nomina_detalle USING(nom_id)
    LEFT JOIN doctores on doc_id=nom_doc_id
    LEFT JOIN especialidades on nom_esp_id=esp_id
    WHERE pac_id=$paciente AND nomd_observaciones!=''
    UNION ALL
SELECT 
date_trunc('Second',receta_fecha_emision) || '' as fecha,
'00:00:00',
doc_nombres||' '||doc_paterno||' '||doc_materno as doc,
receta_comentarios,
receta_id,
centro_nombre,
date_trunc('Second',receta_fecha_emision),
receta_numero as numero,
(COALESCE(receta_diag_cod,'S/A')|| '|' || receta_diag_cod) as diag,
'R' as tipo
from receta
left join centro_costo on centro_ruta=receta_centro_ruta
left join doctores on doc_id=receta_doc_id
where receta_paciente_id=$paciente AND receta_comentarios!=''
ORDER BY nom_fecha desc ,nomd_hora DESC

");
    
    
    if($div=='rec')
    {
        if(!$rec)
        {
            print("<tr class='tabla_header' style='font-weight: bold;'><center>Sin Historial Disponible...</center></tr>");
        }
        else
        {
            print('<tr class="tabla_header" style="font-weight: bold;">
     	 		<td colspan=7>Detalle de Medicamentos Recetados</td></tr>
  		    	<tr class="tabla_header" style="font-weight: bold;">
  	      		<td>Medicamento</td>
	        	<td>Dosis</td>
	        	<td>Ver</td>
	        	</tr>
		');
			
			for($j=0;$j<sizeof($rec);$j++) {
	
	    			($j%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	    			
	    			if($rec[$j]['total']>$rec[$j]['despachado']){
	    				$color='#81F781';
	    			}else{
	    				$color='#FA5858';
	    			}
	    			
	    			print('<tr class='.$clase.' >
	    				<td style="background-color:'.$color.'">'.$rec[$j]['art_glosa'].'</td>
	    			');
	    			
	    			 if($rec[$j]['recetad_horas']*1<=24) {
        					$div_h=1;
							$txt_horas='horas';
       				 }else{
        				if(($rec[$j]['recetad_horas'])%24==0) {
        					$div_h=24;
							$txt_horas='d&iacute;a(s)';
		        		}else{
        					$div_h=1;
							$txt_horas='horas';
						}
					}
				
					if($rec[$j]['recetad_dias']*1<=30) {
        				$div_d=1;
						$txt_dias='d&iacute;a(s).';
        			}else{
        				if(($rec[$j]['recetad_dias'])%30==0) {
        					$div_d=30;
							$txt_dias='mes(es).';
        				}else{
        					$div_d=1;
							$txt_dias='d&iacute;a(s).';
					}
			}
						
					
			 print('
			      	<td style="text-align:center;background-color:'.$color.'"><i>'.$rec[$j]['recetad_cant'].' '.$rec[$j]['forma_nombre'].' cada '.($rec[$j]['recetad_horas']/$div_h).' '.$txt_horas.' durante '.($rec[$j]['recetad_dias']/$div_d).' '.$txt_dias.'</i></td>
			      	<td style="text-align: center;" style="backgroun-color:#FFFFFF;">
      				<b><i><img src="../../iconos/zoom.png" style="cursor:pointer;" onClick="visualizar_receta('.$rec[$j]["receta_id"].');"></i></b>
      				</td>
			      	</tr>
			      ');  
			} 	
		}
    }
    else if($div=='obs')
    {
        if(isset($_GET['nomd_id']))
        {
            $nomd_id =$_GET['nomd_id']*1;
        }
        else
            $nomd_id=false;
            
        if(!$obs)
        {
            print("<tr class='tabla_header' style='font-weight: bold;'><center>Sin Historial Disponible...</center></tr>");
        }
        else
        {
            print('
            <tr class="tabla_header" style="font-weight: bold;">
                <td colspan=5>Observaciones</td>
            </tr>
            <tr class="tabla_header" style="font-weight: bold;">
                <td>Fecha</td>
	        <td>Profesional</td>
                <td>Observaci&oacute;n</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            ');
            for($k=0;$k<sizeof($obs);$k++)
            {
                ($k%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                //if($obs[$k]['nomd_id']==$nomd_id)
                if($obs[$k]['tipo']=="R")
                {
                    $num_receta='<b>[Nro Receta: '.$obs[$k]['numero'].']</b>';
                    print('<tr class='.$clase.' style="background-color: #98FB98;" >');
                }
                else
                {
                    print('<tr class='.$clase.' style="background-color: #52F3FF;">');
                    $num_receta='<b>[Nomina: '.$obs[$k]['nomd_id'].']</b>';
                    
                }
                if($obs[$k]['diag']=="")
                    $str_diag="";
                else
                    $str_diag="<br><span class='texto_tooltip'<b>Diag : [ ".htmlentities($obs[$k]['diag'])." ]</b></span>";
                
                print('<td style="width:10%;font-weight:bold;";>'.$obs[$k]['fecha'].'</td>');
                if($obs[$k]['doc']=="")
                    $doc="No Asignado";
                else
                    $doc=$obs[$k]['doc'];
                
                    print('<td style="width:30%;font-size:10px;">'.htmlentities($doc).'<br><b>('.htmlentities($obs[$k]['esp_desc']).')</b></td>');
                print('
                    <td>'.htmlentities($obs[$k]['nomd_observaciones']).'</br></br>'.$num_receta.''.$str_diag.'</td>
                    <td>
                        <center>
                ');
                if($obs[$k]['nomd_id']==$nomd_id)
                    print('<img src="../../iconos/magnifier.png" style="cursor: pointer;" onClick="mostrar_observacion(1,'.$obs[$k]['nomd_id'].')" alt="" title="">');
                else
                    print('<img src="../../iconos/magnifier.png" style="cursor: pointer;" onClick="mostrar_observacion(0,'.$obs[$k]['nomd_id'].')" alt="" title="">');
                print('</center></td>');
                if($obs[$k]['tipo']=="R")
                {
                    print('<td><center>&nbsp;</center></td>');
                }
                else {
                    print('<td><center><img src="../../iconos/report.png" style="cursor: pointer;" onClick="abrir_detalle_nomina('.$obs[$k]['nomd_id'].')" alt="Abrir Detalle de Atenci&oacute;n" title="Abrir Detalle de Atenci&oacute;n"></center></td>');
                }
                print('</tr>');
            }
	}
    }
?> 
</table>
</div>