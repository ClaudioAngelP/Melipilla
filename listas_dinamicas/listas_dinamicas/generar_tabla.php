<?php 

	require_once('../conectar_db.php');

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
    $filtrogar=pg_escape_string(trim(utf8_decode($_POST['filtrogar'])));
    
    $filtro=false;
    
    if($pat=="") {
		$pat_w="true";
	} else {
		$pat_w="trim(pst_patologia_interna)='".$pat."'";
		$filtro=true;
	}
    
	if($filtrogar!='') {
		$filtrogar_w="trim(pst_garantia_interna)='".$filtrogar."'";
		$filtro=true;
	} else {
		$filtrogar_w='true';
	}



	$vdetalle=isset($_POST['ver_detalle']);
	$vresumen=isset($_POST['ver_resumen']);
	
	if(isset($_POST['xls']) AND $_POST['xls']*1==1) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"LISTADO_MONITOREO---".date('d-m-Y').".xls\";");
		$xls=true;
    } else $xls=false;
	
	$lista_id=$_POST['lista_id']*1;

	$li=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=$lista_id");	
	
	$dest=cargar_registro("SELECT array_to_string(lista_id_destino,',') AS ids FROM lista_dinamica WHERE lista_id=$lista_id");
	
	if($dest['ids']!='') {

		$lavanzar=pg_query("
			SELECT lista_id, lista_nombre FROM lista_dinamica 
			WHERE lista_id IN (".$dest['ids'].");
		");
		
		$lista_html='';
		
		while($v=pg_fetch_assoc($lavanzar)) {
			$lista_html.='<option value="'.$v['lista_id'].'" style="color:green;">'.htmlentities($v['lista_nombre']).' &gt;&gt;</option>';		
		}
		

	} else
		$lista_html='';

	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
					(CURRENT_DATE-in_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					trim(pst_patologia_interna) AS pst_patologia_interna,
					trim(pst_garantia_interna) AS pst_garantia_interna
					FROM lista_dinamica_instancia 
					JOIN lista_dinamica_caso USING (caso_id)
					LEFT JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					JOIN pacientes USING (pac_id)
					WHERE lista_id=$lista_id AND in_estado=0 AND $pat_w AND $filtrogar_w AND monr_id=(SELECT monr_id 
        										FROM monitoreo_ges_registro 
        										WHERE monr_fecha=(SELECT max(monr_fecha) FROM monitoreo_ges_registro 
        										WHERE monitoreo_ges_registro.mon_id=monitoreo_ges.mon_id))) AS foo 
					ORDER BY dias DESC;");


	// RESUMEN EN XLS

	if($xls OR $vresumen) {

    $totales=array();
    $totales_detalle=array();
    
    if($lista)
    for($i=0;$i<sizeof($lista);$i++) {
	
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

?>

<table style='width:100%;font-size:14px;' cellpadding=0>

<tr class='tabla_header'>
<?php if(!$xls) { ?> <td>&nbsp;</td> <?php } ?>
<td>Patolog&iacute;a</td>
<td>Vigentes</td>
<td>%</td>
<td>Vencidas</td>
<td>%</td>
<td>Total</td>
<?php if(!$xls) { ?> <td>Gr&aacute;fico</td> <?php } ?>
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

			
		print("<tr class='$class'>");
		
		if(!$xls)
		print("
			<td style='font-weight:bold;font-size:16px;'>
			<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='$(\"pat\").value=\"$pat\".unescapeHTML(); $(\"filtrogar\").value=\"\"; cargar_listado();' /></center>
			</td>
		");
		
		print("
		<td style='font-weight:bold;font-size:16px;'>$pat</td>
		<td style='text-align:right;color:blue;font-size:16px;'>".($val['vig']+$val['prox'])."</td>
		<td style='text-align:right;color:blue;font-size:12px;'><i>".number_format($v1+$v3,1,',','.')."%</i></td>
		<td style='text-align:right;color:red;font-size:16px;'>".$val['ven']."</td>
		<td style='text-align:right;color:red;font-size:12px;'><i>".number_format($v2,1,',','.')."%</i></td>
		<td style='text-align:right;color:black;font-size:16px;'><b>".$total."</b></td>
		");
		
		
		if(!$xls) print("<td style='width:250px;'>".graficar($v1, $v3, $v2)."</td>");
		
		
		print("</tr>");

		array_multisort(array_keys($totales_detalle[$key]), $totales_detalle[$key]);
		
		foreach($totales_detalle[$key] AS $key2 => $val2) {

			$gar=htmlentities($key2);

			$total=$val2['vig']+$val2['ven']+$val2['prox'];
			
			$v1=$val2['vig']*100/$total;
			$v2=$val2['ven']*100/$total;
			$v3=$val2['prox']*100/$total;

			print("<tr class='$class'>");

			if(!$xls)
			print("
				<td style='font-weight:bold;font-size:16px;'>
				<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='$(\"pat\").value=\"$pat\".unescapeHTML(); $(\"filtrogar\").value=\"$gar\".unescapeHTML(); cargar_listado();' /></center>
				</td>
			");


			print("
			<td style='font-size:14px;padding-left:20px;'><span style='cursor:pointer;'><i>$gar</i></span></td>
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

<?php 
	
	} // FIN RESUMEN EN XLS


	if(!$vdetalle AND !$xls) exit(); // NO QUIERE VER DETALLE.. EN XLS SIEMPRE SALE DETALLE


?>

<table style='width:100%;'>

	<tr class='tabla_header'>
		<td>ID #</td>
		<td style='width:30px;'>D&iacute;as Espera</td>
		<td>RUT</td>
		
		<?php if($xls) { ?><td>Ficha</td> <?php } ?>
		
		
		<td style='width:35%;'>Nombre Completo</td>
		<?php if(!$xls) { ?> 
		
		<td>Fecha L&iacute;mite</td>
		<td style='width:200px;'>Estado</td> 
		
		<?php } else { ?>
		
		<td>Patolog&iacute;a</td>
		<td>Garant&iacute;a</td>
		<td>Fecha Inicio</td>
		<td>Fecha L&iacute;mite</td>
		
		<?php } ?>
		
<?php 

	if($li['lista_campos_tabla']!='') {
	
		$campos=explode('|', $li['lista_campos_tabla']);
		
		for($i=0;$i<sizeof($campos);$i++) {
			
			if(strstr($campos[$i],'>>>')) {
				$cmp=explode('>>>',$campos[$i]);
				$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
			} else {
				$cmp=$campos[$i]; $tipo=2;
			}
			
			print("<td>".$nombre."</td>");
			
		}

	}

?>		
		
		<?php if(!$xls) { ?> <td>&nbsp;</td> <?php } ?>
	</tr>

<?php 


	if($lista)
	for($k=0;$k<sizeof($lista);$k++) {
		
		$r=$lista[$k];
		
		$clase=($k%2==0)?'tabla_fila':'tabla_fila2';
		
		$caso_id=$r['caso_id'];
		
		$lvolver=pg_query("
			SELECT lista_id, lista_nombre FROM lista_dinamica_instancia
			JOIN lista_dinamica USING (lista_id)
			WHERE caso_id=$caso_id AND in_estado=1 ORDER BY in_fecha;
		");
		
		$volver_html='';
		
		while($v=pg_fetch_assoc($lvolver)) {
			$volver_html.='<option value="'.$v['lista_id'].'" style="color:red;">&lt;&lt; '.htmlentities($v['lista_nombre']).'</option>';		
		}
		
		if($lista_html=='') $vhtml=$volver_html;
		else $vhtml='';
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td style='text-align:right;font-weight:bold;'>".$r['caso_id']."</td>
		<td style='text-align:center;'>".$r['dias2']."</td>
		<td style='text-align:right;'>".$r['pac_rut']."</td>
		");
		
		if($xls) print("<td style='text-align:center;'>".$r['pac_ficha']."</td>");
		
		print("
		<td>".htmlentities(trim(strtoupper($r['pac_nombres']." ".$r['pac_appat']." ".$r['pac_apmat'])))."</td>
		");
		
		if(!$xls)
		print("
		<td style='text-align:center;'>".htmlentities($r['mon_fecha_limite'])."</td>
		<td><select id='sel_".$r['in_id']."' name='sel_".$r['in_id']."' style='font-size:11px;width:100%;'>
		<option value='0'>(Sin Cambios...)</option>
		$vhtml
		$lista_html
		</select></td>
		");
		else
		print("
		<td style='text-align:left;'>".htmlentities($r['mon_patologia'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_garantia'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_fecha_inicio'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_fecha_limite'])."</td>
		");

		$id=$r['in_id']*1;

		if($li['lista_campos_tabla']!='') {
		
			$campos=explode('|', $li['lista_campos_tabla']);
			$valores=explode('|', $r['in_valor_tabla']);
			
			for($i=0;$i<sizeof($campos);$i++) {
			
				if(strstr($campos[$i],'>>>')) {
					$cmp=explode('>>>',$campos[$i]);
					$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
				} else {
					$cmp=$campos[$i]; $tipo=2;
				}
				
				print("<td>");
				
				if($tipo==0) {

					if(isset($valores[$i])) 
						$vact=($valores[$i]=='true')?'CHECKED':'';
					else 
						$vact='';

					print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");	

				} elseif($tipo==1) {

					if(isset($valores[$i])) 
						$vact=($valores[$i]=='true')?'CHECKED':'';
					else 
						$vact='CHECKED';

					print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");
									
				} elseif($tipo==5) {
				
					$opts=explode('//', $cmp[2]);
								
					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';

					print("<select id='campo_".$i."_".$id."' name='campo_".$i."_".$id."'>");
					
					for($k=0;$k<sizeof($opts);$k++) {
						
						$opts[$k]=trim($opts[$k]);
						
						if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
						
						print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");	
					}			
					
					print("</select>");		
					
				} elseif($tipo==10) {

					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';
					
					print("<textarea id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' style='width:100%;height:20px;'>$vact</textarea>");
									
				} else {

					if(isset($valores[$i])) 
						$vact=$valores[$i];
					else 
						$vact='';
					
					print("<input type='text' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='$vact' />");
									
				}	
				
				print("</td>");	
				
			}	
		
		}

		if(!$xls)
		print("
		<td>
		<center>
		<img src='iconos/pencil_go.png' style='cursor:pointer;' onClick='reg_instancia(".$r['in_id'].");' />
		<img src='iconos/magnifier.png' style='cursor:pointer;' onClick='ver_caso(".$r['caso_id'].");' />
		<img src='iconos/printer.png' style='cursor:pointer;' onClick='imprimir_lista(".$r['in_id'].");' />
		</center>
		</td>
		");
		
		print("</tr>");
				
	}

?>	
	
</table>



<?php if($filtro) { ?>

<script>	$('quitar_filtro').show();    </script>

<?php } else { ?>

<script>	$('quitar_filtro').hide();    </script>

<?php } ?>
