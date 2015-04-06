<?php 

    require_once('../../conectar_db.php');
    
    function graficar($vig, $prox, $ven)  {
		
		$vig=round($vig);
		$prox=round($prox);
		$ven=round($ven);
			
		$html="<table style='width:250px;border:1px solid black;' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#4444ff;'>&nbsp;</td>";
		if($prox>0) $html.="<td style='width:$prox%;background-color:#BBBB00;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff4444;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}

    
    $pat=pg_escape_string(trim(utf8_decode($_POST['pat'])));
    $tmppat=$pat;
    $estado=$_POST['estado']*1;
    $filtro=pg_escape_string(trim($_POST['filtro2']));
    $filtrogar=pg_escape_string(trim(utf8_decode($_POST['filtrogar'])));
    $filtrocond=pg_escape_string(trim(utf8_decode($_POST['filtrocond'])));
    
    if($pat=="-1") {
		$pat_w="true";
	} else {
		$pat_w="trim(pst_patologia_interna)='".$pat."'";
	}
    
    if($estado==-2) {
		$estado_w='true';
	} elseif($estado==-1) {
		$estado_w='NOT mon_estado';
	} elseif($estado==0) {
		$estado_w='NOT mon_estado AND mon_fecha_limite>=CURRENT_DATE';
	} elseif($estado==1) {
		$estado_w='NOT mon_estado AND mon_fecha_limite<CURRENT_DATE';
	} elseif($estado==2) {
		$estado_w='mon_estado';
	} elseif($estado==3) {
		$estado_w="mon_estado AND mon_estado_sigges='Exceptuada'";
	}
	
	if($filtro!='') {
		$filtro_w="mon_rut ilike '%$filtro%' OR mon_nombre ilike '%$filtro%'";
		$estado_w='true';
	} else {
		$filtro_w='true';
	}

	if($filtrogar!='') {
		$filtrogar_w="trim(pst_garantia_interna)='".$filtrogar."'";
	} else {
		$filtrogar_w='true';
	}

	if($filtrocond!='0') {
		if($filtrocond!='')
			$filtrocond_w="nombre_condicion='".$filtrocond."'";
		else
			$filtrocond_w="nombre_condicion is null";
	} else {
		$filtrocond_w='true';
	}

	$ver=$_POST['tipo_ver']*1;


    $lista=cargar_registros_obj("
    
		SELECT * FROM (
    
        SELECT 

        *, 

    		(CURRENT_DATE)-mon_fecha_limite AS dias,
    		
    		trim(pst_patologia_interna) AS pst_patologia_interna,
    
    		trim(pst_garantia_interna) AS pst_garantia_interna,
    		
    		mg.mon_id AS real_mon_id,
        
        (monr_fecha_evento IS NOT NULL AND monr_fecha_evento<CURRENT_DATE) AS reclasificar

        FROM monitoreo_ges AS mg

        JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
        
        LEFT JOIN monitoreo_ges_registro AS mgr ON mgr.mon_id=mg.mon_id AND mgr.monr_estado=0
        LEFT JOIN lista_dinamica_condiciones ON id_condicion=COALESCE(monr_clase,'0')::integer
        LEFT JOIN lista_dinamica_bandejas ON codigo_bandeja=monr_subclase

        WHERE $estado_w AND $pat_w AND $filtrogar_w AND $filtro_w AND $filtrocond_w
        
        ) AS foo
        
        ORDER BY dias DESC

    ");

	if(!$lista) {
	
		print("<br /><br /><br />
		<img src='iconos/error.png' style='width:32px;height:32px;' />
		<br /><br />
		<font style='font-weight:bold;font-size:18px;'>NO HAY REGISTROS EN LA PATOLOGIA/ESTADO SOLICITADOS.</font>");
		
		exit();
		
	}
    
    $totales=array();
    $totales_detalle=array();
    $totales_condiciones=array();
    
    for($i=0;$i<sizeof($lista);$i++) {
		
		$key=htmlentities($lista[$i]['nombre_condicion']);
		
		if($key=='')
			$key='(Sin Clasificar...)';
		
		if(!isset($totales_condiciones[$key])) {

			$totales_condiciones[$key]['cant']=0;
			if($key!='(Sin Clasificar...)')
				$totales_condiciones[$key]['opt']=$key;
			else
				$totales_condiciones[$key]['opt']='';
			
		}

		$totales_condiciones[$key]['cant']++;
	
		if(!isset($totales[$lista[$i]['pst_patologia_interna']])) {
			
			$totales[$lista[$i]['pst_patologia_interna']]['vig']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['prox']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['ven']=0;
			
		}

		if(!isset($totales_detalle[$lista[$i]['pst_patologia_interna']]
						  [$lista[$i]['pst_garantia_interna']])) {
							  
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']=0;
			
		}
	
		if($lista[$i]['dias']*1<-30) {
			$totales[$lista[$i]['pst_patologia_interna']]['vig']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']++;
		} elseif($lista[$i]['dias']*1>=-30 AND $lista[$i]['dias']*1<=0) {
			$totales[$lista[$i]['pst_patologia_interna']]['prox']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']++;
		} else {
			$totales[$lista[$i]['pst_patologia_interna']]['ven']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']++;
		}
		
	}
	
	array_multisort(array_keys($totales), $totales);
	array_multisort(array_keys($totales_condiciones), $totales_condiciones);
	
	$options2='<select id="filtrocond" name="filtrocond"><option value="0" SELECTED>(Todas las Clasificaciones...)</option>';
	
	foreach($totales_condiciones AS $key => $val) {
		
		
		$options2.="<option value=\"".$val['opt']."\">$key (".$val['cant'].")</option>";		
		
	}
	
	$options2.='</select>';

?>


<table style='width:100%;font-size:14px;' cellpadding=0>

<tr class='tabla_header'>
<td>Patolog&iacute;a</td>
<td>Vigentes</td>
<td>%</td>
<td>Vencidas</td>
<td>%</td>
<td>Total</td>
<td>Gr&aacute;fico</td>
</tr>

<?php 

	$i=0; 
	
	$options='<select id="filtrogar" name="filtrogar"><option value="" SELECTED>(Todas las Garant&iacute;as....)</option>';

	foreach($totales AS $key => $val) {
			
		$i++;
		
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$pat=htmlentities($key);	

		$total=$val['vig']+$val['prox']+$val['ven'];
		
		$v1=$val['vig']*100/$total;
		$v2=$val['ven']*100/$total;
		$v3=$val['prox']*100/$total;

			
		print("<tr class='$class'>
		<td style='font-weight:bold;font-size:16px;'>$pat</td>
		<td style='text-align:right;color:blue;font-size:16px;'>".($val['vig']+$val['prox'])."</td>
		<td style='text-align:right;color:blue;font-size:12px;'><i>".number_format($v1+$v3,1,',','.')."%</i></td>
		<td style='text-align:right;color:red;font-size:16px;'>".$val['ven']."</td>
		<td style='text-align:right;color:red;font-size:12px;'><i>".number_format($v2,1,',','.')."%</i></td>
		<td style='text-align:right;color:black;font-size:16px;'><b>".$total."</b></td>
		<td style='width:250px;'>".graficar($v1, $v3, $v2)."</td>
		</tr>");

		array_multisort(array_keys($totales_detalle[$key]), $totales_detalle[$key]);
		
		foreach($totales_detalle[$key] AS $key2 => $val2) {

			$gar=htmlentities($key2);

			$total=$val2['vig']+$val2['ven']+$val2['prox'];
			
			$v1=$val2['vig']*100/$total;
			$v2=$val2['ven']*100/$total;
			$v3=$val2['prox']*100/$total;

			print("<tr class='$class'>
			<td style='font-size:14px;padding-left:20px;'><span style='cursor:pointer;' onClick='$(\"filtrogar\").value=\"$gar\".unescapeHTML(); listado_proceso();'><i>$gar</i></span></td>
			<td style='text-align:right;color:blue;font-size:14px;'>".($val2['vig']+$val2['prox'])."</td>
			<td style='text-align:right;color:blue;font-size:12px;'><i>".number_format($v1+$v3,1,',','.')."%</i></td>
			<td style='text-align:right;color:red;font-size:14px;'>".$val2['ven']."</td>
			<td style='text-align:right;color:red;font-size:12px;'><i>".number_format($v2,1,',','.')."%</i></td>
			<td style='text-align:right;color:black;font-size:14px;'>".$total."</td>
			<td style='width:250px;'>".graficar($v1, $v3, $v2)."</td>
			</tr>");
			
			$options.="<option value=\"$gar\">$gar</option>";
			
		}	
			
	}
	
	$options.='</select>';
	
?>

</table>

<table style='width:100%'>

<tr class='tabla_header'>

<td rowspan=2>#</td>

<td rowspan=2>RUT</td>

<td rowspan=2>Nombre Completo</td>

<td rowspan=2>Fecha L&iacute;mite</td>

<td rowspan=2>Fecha Evento</td>

<td rowspan=2><?php if(!$ver) { ?>Condici&oacute;n<?php } else { ?>Garant&iacute;a/Rama<?php } ?></td>

<td rowspan=2>Dias</td>

<td colspan=2>Monitoreo</td>

</tr>

<tr class='tabla_header'>

<td>GES</td>

<td>Estado</td>

</tr>



<?php 

    if($lista)

    for($i=0;$i<count($lista);$i++) {

        $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$dias=$lista[$i]['dias'];	

		if($dias<-30) {
			$color='blue';
		} elseif($dias>=-30 AND $dias<=0) {
			$color='#999900';
		} else {
			$color='red';
		}
		
		if($lista[$i]['mon_estado']=='t') {
			$color='black';
		}
    

      print("
        <tr class='$clase'>
        <td style='text-align:right;' class='tabla_header'>".($i+1)."</td>
        <td style='text-align:right;font-weight:bold;'>".htmlentities($lista[$i]['mon_rut'])."</td>
        <td>".htmlentities($lista[$i]['mon_nombre'])."</td>
        <td style='text-align:center;font-size:11px;font-weight:bold;'>".htmlentities($lista[$i]['mon_fecha_limite'])."</td>
      ");
	
	  if($lista[$i]['mon_estado']!='t' OR $lista[$i]['mon_fecha_sigges']==''  OR ($lista[$i]['mon_estado_sigges']=='Exceptuada' AND $lista[$i]['nombre_condicion']!=''))
		print("
			<td style='text-align:center;font-size:11px;font-weight:bold;'>".htmlentities($lista[$i]['monr_fecha_evento'])."</td>
		");
	  else
		print("
			<td style='text-align:center;font-size:11px;font-weight:bold;color:4444ff;'>".htmlentities($lista[$i]['mon_fecha_sigges'])."</td>
		");
      
      if(!$ver) {
			
			if($lista[$i]['mon_estado']!='t' OR $lista[$i]['mon_estado_sigges']=='' OR ($lista[$i]['mon_estado_sigges']=='Exceptuada' AND $lista[$i]['nombre_condicion']!='')) {
			
				if($lista[$i]['monr_subcondicion']!='') {
					$subcond='<i>('.htmlentities($lista[$i]['monr_subcondicion']).')</i>';
				} else {
					$subcond='';
				}
				
				print("<td style='text-align:left;font-size:11px;'>".htmlentities($lista[$i]['nombre_condicion'])." $subcond</td>");
			
			} else {

				if($lista[$i]['mon_causal_sigges']!='') {
					$subcond='<i>('.htmlentities($lista[$i]['mon_causal_sigges']).')</i>';
				} else {
					$subcond='';
				}
				
				print("<td style='text-align:left;font-size:11px;color:blue;'>".htmlentities($lista[$i]['mon_estado_sigges'])." $subcond</td>");
				
			}
			
		} else
		
			print("<td style='text-align:left;font-size:10px;'>".htmlentities($lista[$i]['mon_garantia'])."</td>");
		

		
        if($lista[$i]['mon_estado']!='t') {
			print("<td style='font-weight:bold;text-align:right;color:$color;'>".number_format($lista[$i]['dias'],0,',','.')."</td>");
		} else {
			print("<td style='font-weight:bold;text-align:right;background-color:#000000;color:$color;'><center>&nbsp;</center></td>");			
		}

		print("
        <td style='text-align:center;'><img src='iconos/page_edit.png' onClick='abrir_monitoreo(".$lista[$i]['real_mon_id'].");' /></td>
        ");
      
      if($lista[$i]['mon_estado']=='t') {
  			print("<td style='text-align:center;'><img src='iconos/database_save.png' /></td>");		  
	  } else if($lista[$i]['lista_remonitorear']=='t') {
  			print("<td style='text-align:center;'><img src='iconos/stop.png' /></td>");
      } else if($lista[$i]['reclasificar']=='t') {
  			print("<td style='text-align:center;'><img src='iconos/error.png' /></td>");
      } else if($lista[$i]['monr_id']*1==0) {
  			print("<td style='text-align:center;'><img src='iconos/cross.png' /></td>");
  	  } else {
  			print("<td style='text-align:center;'><img src='iconos/tick.png' /></td>");		
  	  }

      print("</tr>");

    }

?>



</table>

<script>

<?php 

	if($filtrogar=='' OR $tmppat=='-1') {

	if($tmppat!='-1') { ?>

	$('gar_td').innerHTML='<?php echo $options; ?>';
	$('gar_tr').show();	

<?php 
	}  else { 
?>

	$('gar_td').innerHTML='<select id="filtrogar" name="filtrogar"></select>';
	$('gar_tr').hide();

<?php 
	} 

	}

	if($filtrocond=='0') {
	
?>

	$('cond_td').innerHTML='<?php echo $options2; ?>';
	$('cond_tr').show();	
	
<?php 

	}

?>

</script>
