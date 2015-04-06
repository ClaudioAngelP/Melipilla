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
    
    if(isset($_POST['filtro_cond'])) {
    
		$cond=cargar_registros_obj("SELECT * FROM lista_dinamica_condiciones;");
		
		$conds='';
		
		for($i=0;$i<sizeof($cond);$i++) {
		
			if(isset($_POST['chk_cnd_'.$cond[$i]['id_condicion']])) {
				$conds.="'".$cond[$i]['id_condicion']."',";
			}
			
		}
		
		$conds=trim($conds,',');
    
		if($conds!='')
			$cond_w='(monr_clase IN ('.$conds.'))';		// CONDICIONES SELECCIONADAS...
		else
			$cond_w='true';	// SI NO SELECCIONA NINGUNA LAS TOMA TODAS...
    
	} else {
		
		$cond_w='true';
		
	}




    if(isset($_POST['filtro_cual'])) {
    
		$chks=$_POST['filtro_cual']*1;
    
		for($i=0;$i<=$chks;$i++) {
		
			if(isset($_POST['chk_cual_'.$i])) {
				$cuales.="'".pg_escape_string(utf8_decode($_POST['chk_cual_'.$i]))."',";
			}
			
		}
		
		$cuales=trim($cuales,',');
    
		if($cuales!='')
			$cual_w='(trim(monr_subcondicion) IN ('.$cuales.'))';		// CONDICIONES SELECCIONADAS...
		else
			$cual_w='true';	// SI NO SELECCIONA NINGUNA LAS TOMA TODAS...
    
	} else {
		
		$cual_w='true';
		
	}





    
    if(isset($_POST['filtro_cond']) AND $_POST['filtro_cond']=='3') {
    
		$pat=cargar_registros_obj("SELECT * FROM patologias_sigges_traductor;");
		
		$pats='';
		
		for($i=0;$i<sizeof($pat);$i++) {
		
			if(isset($_POST['chk_pst_'.$pat[$i]['pst_id']])) {
				
				// Existen multiples IDS por cada x problema x garantia...
				// TODO: ¿¿¿Deben unificarse los ID de x problema x garantia???...
				$tmp=cargar_registros_obj("SELECT * FROM patologias_sigges_traductor 
						WHERE pst_patologia_interna='".$pat[$i]['pst_patologia_interna']."' AND 
							  pst_garantia_interna='".$pat[$i]['pst_garantia_interna']."'");
							  
				for($k=0;$k<sizeof($tmp);$k++)
					$pats.=$tmp[$k]['pst_id'].',';
					
			}
			
		}
		
		$pats=trim($pats,',');
		
		if($pats!='')
			$filtropat_w='(pst_id IN ('.$pats.'))';		// SI HAY PATOLOGIAS TOMA LAS CHEQUEADAS...
		else
			$filtropat_w='true';	// SI NO HAY CHEQUEADAS LAS TOMA TODAS...
    
	} else {
		
		$filtropat_w='true';
		
	}
	
	$tipo_gar=$_POST['tipo_gar']*1;
	
	if($tipo_gar==0) {
		$tipo_w='true';
	} else if($tipo_gar==1) {
		$tipo_w='dias>0';
	} else {
		$tipo_w='dias<=0';
	}

	$fecha_lim=pg_escape_string($_POST['fecha_limite']);

	if($fecha_lim=='') {
		$fecha_w='true';
	} else {
		$fecha_w="mon_fecha_limite<='$fecha_lim'";
	}

	$directorio=isset($_POST['directorio']);

	if(!$directorio) {
		$dir_w="lb.codigo_bandeja IS NOT NULL";
	} else {
		$dir_w="lb.codigo_bandeja IS NULL";
		//$dir_w="true";
	}
	
	if($_POST['fecha_dif']!='') {
		$dif_w='dias3<='.($fecha_dif*1);
	} else {
		$dif_w='true';
	}
	
	
	/*
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
	*/

	//$vdetalle=isset($_POST['ver_detalle']);
	//$vresumen=isset($_POST['ver_resumen']);
	
	$vdetalle=true;
	$vresumen=true;
	
	if(isset($_POST['xls']) AND $_POST['xls']*1==1) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"LISTADO_MONITOREO---".date('d-m-Y').".xls\";");
		$xls=true;
    } else $xls=false;

	$lista_id=pg_escape_string(utf8_decode($_POST['lista_id']));

	$lista_html='';
	
	$bandeja_cual=false;
	$bandeja_dividir=0;
	$divisor_w='true';
	$lista_orden=0;

	if($lista_id!='-1' AND $lista_id!='-2') {

		$li=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$lista_id'");	
		
		$bandeja_cual=($li['lista_mostrar_cual']=='t');
		$bandeja_dividir=($li['lista_dividir']*1);
		$lista_orden=$li['lista_orden']*1;
		
		if($bandeja_dividir>1) {
			
			$bandeja_divisor=$_POST['divisor']*1;
			
			if($bandeja_divisor>0) {
				$divisor_w='monr_dividir='.($bandeja_divisor-1);
			} else
				$divisor_w='true';

			$cond_w='true';
			$filtropat_w='true';
				
		} else {
			$divisor_w='true';
		}
		
		
		$destinos=pg_query("
			SELECT * FROM (
				SELECT DISTINCT id_condicion_n FROM (
					SELECT DISTINCT id_condicion_n 
					FROM lista_dinamica_proceso 
					WHERE codigo_bandeja='$lista_id'
				) AS foo2
			) AS foo
			LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
			WHERE NOT nombre_condicion = ''
			ORDER BY id_condicion_n;
		");
		
		$lista_html='';
		
		while($dest=pg_fetch_assoc($destinos)) {

			$lista_html.='<option value="'.$dest['codigo_bandeja_n'].'" style="color:green;">'.htmlentities($dest['nombre_condicion']).' &gt;&gt;</option>';		
				
		} 

		$destinos=pg_query("
			SELECT * FROM (
				SELECT DISTINCT codigo_bandeja_n FROM (
					SELECT DISTINCT codigo_bandeja_n 
					FROM lista_dinamica_proceso 
					WHERE codigo_bandeja='$lista_id'
				) AS foo2
			) AS foo
			LEFT JOIN lista_dinamica_bandejas AS ldc ON ldc.codigo_bandeja=foo.codigo_bandeja_n
			WHERE NOT nombre_bandeja = ''
			ORDER BY nombre_bandeja;
		");
		
		while($dest=pg_fetch_assoc($destinos)) {

			$lista_html.='<option value="'.$dest['codigo_bandeja_n'].'" style="color:blue;">'.htmlentities($dest['nombre_bandeja']).' &gt;&gt;</option>';	
				
		} 

		$lista_w="monr_subclase='$lista_id'";

	} else {
		
		$lista_w='true';
		
	}

	/*
	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
					(CURRENT_DATE-in_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
					COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna
					FROM lista_dinamica_instancia 
					JOIN lista_dinamica_caso USING (caso_id)
					JOIN lista_dinamica_bandejas USING (codigo_bandeja)
					JOIN lista_dinamica_condiciones USING (id_condicion)
					LEFT JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN monitoreo_ges_registro USING (monr_id)
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					JOIN pacientes USING (pac_id)
					WHERE $lista_w AND $cond_w AND in_estado=0 AND $pat_w AND $filtrogar_w) AS foo 
					ORDER BY dias DESC;");
	*/
	
	$agrupar=$_POST['agrupar']*1;
	
	if($agrupar==1) {	// Agrupa patologias
		$ordenar_pat="_pst_patologia_interna,";
	} else if($agrupar==2) {	// Agrupa cuales
		$ordenar_pat="_monr_subcondicion,";
	} else {	// No agrupa...
		$ordenar_pat='';
	}
	
	if($lista_orden==0)
		$orden='ORDER BY '.$ordenar_pat.' dias3 ASC, dias DESC';
	else if($lista_orden==1)
		$orden='ORDER BY '.$ordenar_pat.' monr_fecha_evento ASC, dias DESC';
	
	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
					(CURRENT_DATE-monr_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					(mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS _pst_patologia_interna,
					COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna,
					monitoreo_ges_registro.monr_id AS id, monitoreo_ges_registro.mon_id AS id2,
					(CASE 
					WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2 
					WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
						  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1 
					ELSE 0 END) AS estado,
					trim(monr_subcondicion) AS monr_subcondicion,
					trim(upper(monr_subcondicion)) AS _monr_subcondicion,
					(SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut LIMIT 1) AS pac_id
					FROM monitoreo_ges_registro 
					JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN lista_dinamica_bandejas AS lb ON COALESCE(monr_subclase,'')=lb.codigo_bandeja
					LEFT JOIN lista_dinamica_condiciones AS lc2 ON COALESCE(monr_clase,'0')::bigint=lc2.id_condicion
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					WHERE NOT mon_estado AND monr_estado=0 AND $lista_w AND $divisor_w AND $cond_w AND $cual_w AND $filtropat_w AND $dir_w) AS foo 
					LEFT JOIN pacientes USING (pac_id)
					WHERE $tipo_w AND $fecha_w AND $dif_w
					$orden;");

    $totales=array();
    $totales_detalle=array();

    $condiciones=array();
    $cuales=array();
    $divisiones=array();
    
    if($lista)
    for($i=0;$i<sizeof($lista);$i++) {
		
		$div=$lista[$i]['monr_dividir']*1;
		
		if(!isset($divisiones[$div])) {
			$divisiones[$div]=0;
		}

		$divisiones[$div]++;
	
		if(!isset($condiciones[$lista[$i]['nombre_condicion']])) {
			
			$condiciones[$lista[$i]['nombre_condicion']]['id_condicion']=$lista[$i]['monr_clase'];
			$condiciones[$lista[$i]['nombre_condicion']]['vig']=0;
			$condiciones[$lista[$i]['nombre_condicion']]['ven']=0;
			
		}

		if(!isset($cuales[$lista[$i]['monr_subcondicion']])) {
			$cuales[$lista[$i]['monr_subcondicion']]['vig']=0;
			$cuales[$lista[$i]['monr_subcondicion']]['ven']=0;
		}
		
		if($lista[$i]['estado']*1<=1) {
			$condiciones[$lista[$i]['nombre_condicion']]['vig']++;
			$cuales[$lista[$i]['monr_subcondicion']]['vig']++;
		} else {
			$condiciones[$lista[$i]['nombre_condicion']]['ven']++;
			$cuales[$lista[$i]['monr_subcondicion']]['ven']++;
		}


		if(!isset($totales[$lista[$i]['pst_patologia_interna']])) {
			
			$totales[$lista[$i]['pst_patologia_interna']]['vig']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['prox']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['ven']=0;
			
		}

		if(!isset($totales_detalle[$lista[$i]['pst_patologia_interna']]
						  [$lista[$i]['pst_garantia_interna']])) {
							  
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['pst_id']=$lista[$i]['pst_id'];
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']=0;
			
		}
	
		if($lista[$i]['estado']*1==0) {
			$totales[$lista[$i]['pst_patologia_interna']]['vig']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']++;
		} elseif($lista[$i]['estado']*1==1) {
			$totales[$lista[$i]['pst_patologia_interna']]['prox']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']++;
		} else {
			$totales[$lista[$i]['pst_patologia_interna']]['ven']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']++;
		}
		
	}
	
	// Ordena los arrays de totales alfabeticamente...
	
	array_multisort(array_keys($condiciones), $condiciones);
	array_multisort(array_keys($cuales), $cuales);
	array_multisort(array_keys($totales), $totales);

?>

<input type='hidden' id='filtro_cond' name='filtro_cond' value='3' />




<?php 

if($bandeja_dividir>0) {

?>

<input type='hidden' id='divisor' name='divisor' value='<?php echo $bandeja_divisor; ?>' />

<div class='sub-content'>

<center>
<table>
	<tr>
		<td style='font-weight:bold;font-size:18px;'>Divisiones:</td>
		<td><input type='button' 
		style='font-size:24px;font-ẃeight:bold;'
		id='cajon_t' name='cajon_t' 
		value='[VER TODAS]' onClick='$("divisor").value="0"; cargar_lista();' /></td>
<?php 

	for($i=0;$i<$bandeja_dividir;$i++) {
		
		if($bandeja_divisor==($i+1))
			$estilo='font-size:24px;font-ẃeight:bold;';
		else
			$estilo='font-size:24px;';
		
		print("<td><input type='button' style='$estilo'
			id='cajon_$i' name='cajon_$i' 
			value='".($i+1)."' onClick='$(\"divisor\").value=\"".($i+1)."\"; cargar_lista();' /></td>
			<td style='text-align:center;font-weight:bold;font-size:18px;width:30px;'>(".$divisiones[$i].")</td>");
		
	}
	
?>
	<td><input type='text' id='redividir' name='redividir' value='<?php echo $bandeja_dividir; ?>' size=5 />
	<input type='button' id='reparte' name='reparte' value='-- [Repartir] --' onClick='redividir_bandeja();' /></td>
	</tr>
</table>

</div>
</center>

<?php } ?>


















<?php if(!$bandeja_cual) { ?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td style='width:20px;'>&nbsp;</td>
		<td>Clasificaci&oacute;n</td>
		<td>Vigentes</td>
		<td>Vencidos</td>
		<td>Subtotal</td>
	</tr>

	
<?php
	
	$i=0;
	
	foreach($condiciones AS $key => $val) {
		
		$clase=($i++)%2==0?'tabla_fila':'tabla_fila2';
		
		if($key=='')
			$key='<i>(Sin Clasificar...)</i>';
		
		$id=$val['id_condicion'];
		
		print("
		<tr class='$clase'>
		<td><center><input type='checkbox' id='chk_cnd_".$id."' name='chk_cnd_".$id."' CHECKED></center></td>
		<td style='width:40%;'>".htmlentities($key)."</td>
		<td style='text-align:right;font-weight:bold;color:green;'>".number_format($val['vig'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;color:#ff4444;'>".number_format($val['ven'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($val['vig']*1+$val['ven']*1,0,',','.')."</td>
		</tr>
		");
		
		
	}

?>

</table>



<table style='width:100%;font-size:12px;' cellpadding=0>

<tr class='tabla_header'>
<?php if(!$xls) { ?> <td style='width:20px;'><input type='checkbox' id='todas' name='todas' onClick='seltodas();' CHECKED /></td> <?php } ?>
<td colspan=2>Patolog&iacute;a</td>
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
		$tpat=htmlentities($key);	

		if($tpat=='')
			$tpat="<i>(No especificada...)</i>";	

		$total=$val['vig']+$val['prox']+$val['ven'];
		
		$v1=$val['vig']*100/$total;
		$v2=$val['ven']*100/$total;
		$v3=$val['prox']*100/$total;

			
		print("<tr class='$class'>");
		
		if(!$xls)
		/*print("
			<td style='font-weight:bold;font-size:12px;'>
			<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='$(\"pat\").value=\"$pat\".unescapeHTML(); $(\"filtrogar\").value=\"\"; cargar_listado();' /></center>
			</td>
		");*/
		
		
		
		
		// ARREGLAR, SELECCIONA O QUITA LAS GARANTIAS DE LA PATOLOGIA...
		
		print("
			<td style='font-weight:bold;font-size:12px;'>
			<center><input type='checkbox' class='sel_patologia' id='chk_pst_0' name='chk_pst_0' CHECKED /></center>
			</td>
		");
		
		
		
		
		
		print("
		<td style='font-weight:bold;font-size:12px;' colspan=2>$tpat</td>
		<td style='text-align:right;color:blue;font-size:12px;'>".($val['vig']+$val['prox'])."</td>
		<td style='text-align:right;color:blue;font-size:12px;'><i>".number_format($v1+$v3,1,',','.')."%</i></td>
		<td style='text-align:right;color:red;font-size:12px;'>".$val['ven']."</td>
		<td style='text-align:right;color:red;font-size:12px;'><i>".number_format($v2,1,',','.')."%</i></td>
		<td style='text-align:right;color:black;font-size:14px;'><b>".$total."</b></td>
		");
		
		
		if(!$xls) print("<td style='width:250px;'>".graficar($v1, $v3, $v2)."</td>");
		
		
		print("</tr>");

		array_multisort(array_keys($totales_detalle[$key]), $totales_detalle[$key]);
		
		foreach($totales_detalle[$key] AS $key2 => $val2) {

			$gar=htmlentities($key2);
			$tgar=htmlentities($key2);

			if($tgar=='')
				$tgar="<i>(No especificada...)</i>";	

			$total=$val2['vig']+$val2['ven']+$val2['prox'];
			
			$v1=$val2['vig']*100/$total;
			$v2=$val2['ven']*100/$total;
			$v3=$val2['prox']*100/$total;

			print("<tr class='$class'>");

			if(!$xls)
			/*print("
				<td style='font-weight:bold;font-size:16px;'>
				<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='$(\"pat\").value=\"$pat\".unescapeHTML(); $(\"filtrogar\").value=\"$gar\".unescapeHTML(); cargar_listado();' /></center>
				</td>
			");*/
			print("
				<td>&nbsp;</td>
				<td style='font-weight:bold;font-size:16px;width:20px;'>
				<center><input type='checkbox' class='sel_garantia' id='chk_pst_".$val2['pst_id']."' name='chk_pst_".$val2['pst_id']."' CHECKED /></center>
				</td>
			");

			print("
			<td style='font-size:12px;padding-left:20px;'><span style='cursor:pointer;'><i>$tgar</i></span></td>
			<td style='text-align:right;color:blue;font-size:12px;'>".($val2['vig']+$val2['prox'])."</td>
			<td style='text-align:right;color:blue;font-size:12px;'><i>".number_format($v1+$v3,1,',','.')."%</i></td>
			<td style='text-align:right;color:red;font-size:12px;'>".$val2['ven']."</td>
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
	
} else {

?>


<input type='hidden' id='filtro_cual' name='filtro_cual' value='<?php echo sizeof($cuales);  ?>' />

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td style='width:20px;text-align:center;'><input type='checkbox' id='cual_todas' name='cual_todas' onClick='cual_seltodas();' CHECKED /></td>
		<td>Descripci&oacute;n</td>
		<td>Vigentes</td>
		<td>Vencidos</td>
		<td>Subtotal</td>
	</tr>

	
<?php
	
	$i=0;
	
	foreach($cuales AS $key => $val) {
		
		$clase=($i++)%2==0?'tabla_fila':'tabla_fila2';
		
		if($key=='') {
			$value='';
			$key='<i>(Sin Clasificar...)</i>';
		} else {
			$key=htmlentities($key);
			$value=$key;
		}
		
		print("
		<tr class='$clase'>
		<td><center><input type='checkbox' class='sel_cual' id='chk_cual_".$i."' name='chk_cual_".$i."' value='".$value."' CHECKED /></center></td>
		<td style='width:40%;'>".($key)."</td>
		<td style='text-align:right;font-weight:bold;color:green;'>".number_format($val['vig'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;color:#ff4444;'>".number_format($val['ven'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($val['vig']*1+$val['ven']*1,0,',','.')."</td>
		</tr>
		");
		
		
	}

?>

</table>


<?php }   ?>













<table style='width:100%;font-size:11px;' cellspacing=0>

	<tr class='tabla_header'>
		<!--<td>ID #</td>
		<td style='width:30px;'>D&iacute;as Espera</td>-->
		<td>RUT</td>
		
		<?php if($xls) { ?><td>Ficha</td> <?php } ?>
		
		
		<td style='width:35%;'>Nombre Completo</td>
		<?php if(!$xls) { ?> 
		
		<td>Fecha L&iacute;mite</td>
		<td>Fecha Evento</td>
		<td>Dif.</td>
		<td style='width:200px;'>Estado</td> 
		
		<?php } else { ?>
		
		<td>Patolog&iacute;a</td>
		<td>Garant&iacute;a</td>
		<td>Fecha Inicio</td>
		<td>Fecha L&iacute;mite</td>
		
		<?php } ?>
		
<?php 

	$campos=array();

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

	$selmul=array();
	$selmul_dest=array();

	$tmp_pat='';

	if($lista)
	for($k=0;$k<sizeof($lista);$k++) {
		
		$r=$lista[$k];
		
		$clase=($k%2==0)?'tabla_fila':'tabla_fila2';
		
		$caso_id=$r['caso_id'];
				
		if($agrupar==1) {
			
			if($tmp_pat!=$r['pst_patologia_interna'] OR $k==0) {
				$colspan=7+sizeof($campos);
				print("<tr><td colspan=$colspan style='font-size:14px;background-color:purple;color:#ffffff;font-weight:bold;'><i>".htmlentities($r['pst_patologia_interna'])."</i></td></tr>");
				$tmp_pat=$r['pst_patologia_interna'];
			}
			
		} else if($agrupar==2) {
			
			if($r['_monr_subcondicion']=='') 
				$r['_monr_subcondicion']='<i>(Sin Cual...)</i>';
			else
				$r['_monr_subcondicion']=htmlentities($r['_monr_subcondicion']);

			if($tmp_pat!=$r['_monr_subcondicion'] OR $k==0) {
				$colspan=7+sizeof($campos);
				print("<tr><td colspan=$colspan style='font-size:14px;background-color:yellowgreen;color:#000000;font-weight:bold;'><i>".($r['_monr_subcondicion'])."</i></td></tr>");
				$tmp_pat=$r['_monr_subcondicion'];
			}
		}

		
		
		$nombre=trim(strtoupper($r['mon_nombre']));
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td style='text-align:right;'>".$r['mon_rut']."</td>
		");
		
		if($xls) print("<td style='text-align:center;'>".$r['pac_ficha']."</td>");
		
		print("
		<td>".htmlentities($nombre)."</td>
		");
		
		if($r['dias']*1>0) $color='red'; else $color='green';
		
		if($r['dias3']*1<0) 
			$color2='red'; 
		else if($r['dias3']*1>=0 AND $r['dias3']*1<=4) 
			$color2='green'; 
		else
			$color2='purple';
		
		$selmul[]=$r['id']*1;
		
		if(!$xls) {
		print("
		<td style='text-align:center;font-weight:bold;color:$color;'>".htmlentities($r['mon_fecha_limite'])."</td>
		<td style='text-align:center;font-weight:bold;color:$color;'>".htmlentities($r['monr_fecha_evento'])."</td>
		<td style='text-align:center;font-weight:bold;color:$color2;'>".htmlentities($r['dias3'])."</td>
		<td>
		<select id='sel_".$r['id']."' name='sel_".$r['id']."' 
		style='font-size:11px;width:100%;'>
		<option value=''>(".htmlentities($r['nombre_condicion']).")</option>
		");
		
		//if($lista_html!='')
		
			//print("$lista_html");
			
		//else {

			//			WHERE id_condicion='".$r['monr_clase']."'
			
			/*$destinos=pg_query("
				SELECT * FROM (
					SELECT DISTINCT id_condicion_n FROM (
						SELECT DISTINCT id_condicion_n, codigo_bandeja_n
						FROM lista_dinamica_proceso 
						WHERE codigo_bandeja='".$r['monr_subclase']."' OR id_condicion='".$r['monr_clase']."'
					) AS foo2
				) AS foo
				LEFT JOIN lista_dinamica_bandejas AS ldb ON ldb.codigo_bandeja=foo.codigo_bandeja_n
				LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
				WHERE NOT nombre_condicion = ''
				ORDER BY id_condicion_n;
			");
		
			while($dest=pg_fetch_assoc($destinos)) {

				print('<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:green;">'.htmlentities($dest['nombre_bandeja']).' &gt; '.htmlentities($dest['nombre_condicion']).' &gt;&gt;</option>');		
				
			} */

					//	WHERE id_condicion='".$r['monr_clase']."'
			
			if($r['monr_subclase']!='')
				$destinos=pg_query("
					SELECT * FROM (
						SELECT DISTINCT codigo_bandeja_n, id_condicion_n FROM (
							SELECT DISTINCT codigo_bandeja_n, id_condicion_n
							FROM lista_dinamica_proceso 
							WHERE codigo_bandeja='".$r['monr_subclase']."' AND (id_condicion=0 OR id_condicion=".$r['monr_clase'].")
						) AS foo2
					) AS foo
					LEFT JOIN lista_dinamica_bandejas AS ldb ON ldb.codigo_bandeja=foo.codigo_bandeja_n
					LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
					ORDER BY id_condicion_n;
				");
			else
				$destinos=pg_query("
					SELECT * FROM (
						SELECT DISTINCT codigo_bandeja AS codigo_bandeja_n, id_condicion_n FROM (
							SELECT DISTINCT codigo_bandeja, 0 AS id_condicion_n
							FROM lista_dinamica_proceso 
							WHERE id_condicion='".$r['monr_clase']."'
						) AS foo2
					) AS foo
					LEFT JOIN lista_dinamica_bandejas AS ldb ON ldb.codigo_bandeja=foo.codigo_bandeja_n
					LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
					ORDER BY id_condicion_n;
				");
			
			while($dest=pg_fetch_assoc($destinos)) {

				if($dest['id_condicion_n']=='0' AND $dest['codigo_bandeja_n']=='') continue;
				
				print('<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">['.htmlentities($dest['nombre_condicion']).'] &gt; '.htmlentities($dest['nombre_bandeja']).' &gt;&gt;</option>');	
				$selmul_dest[]='<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">['.htmlentities($dest['nombre_condicion']).'] &gt; '.htmlentities($dest['nombre_bandeja']).' &gt;&gt;</option>';
					
			} 
			
			if($directorio) {

				$dest['id_condicion_n']=0;
				$dest['codigo_bandeja_n']='G';
				$dest['nombre_bandeja']='Bandeja Monitor GES por Patolog&iacute;a';
				
				print('<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">[] &gt; '.($dest['nombre_bandeja']).' &gt;&gt;</option>');	
				$selmul_dest[]='<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">[] &gt; '.($dest['nombre_bandeja']).' &gt;&gt;</option>';

				$dest['id_condicion_n']=0;
				$dest['codigo_bandeja_n']='O';
				$dest['nombre_bandeja']='Bandeja Coordinador GES';

				print('<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">[] &gt; '.($dest['nombre_bandeja']).' &gt;&gt;</option>');	
				$selmul_dest[]='<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">[] &gt; '.($dest['nombre_bandeja']).' &gt;&gt;</option>';
				
			}

			
		//}
		
		print("
		</select>
		</td>
		");
		
		
		} else
		print("
		<td style='text-align:left;'>".htmlentities($r['mon_patologia'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_garantia'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_fecha_inicio'])."</td>
		<td style='text-align:left;'>".htmlentities($r['mon_fecha_limite'])."</td>
		");

		$id=$r['monr_id']*1;
		
		if($lista_id!='-1' AND $lista_id!='-2') {

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
					
					print("<td><center>");
					
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
										
					} elseif($tipo==2) {

						print("<input type='text' size=7 style='text-align:center;' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='$vact' />");
										
					} elseif($tipo==3) {

                        print("<input type='text' class='ld_fechas' style='text-align:center;' size=10 onBlur='validacion_fecha(this);' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='".$valores[$i]."' />");

                    } elseif($tipo==5) {
					
						$opts=explode('//', $cmp[2]);
									
						if(isset($valores[$i])) 
							$vact=$valores[$i];
						else 
							$vact='';

						print("<select style='width:100px;' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."'>");
						
						for($j=0;$j<sizeof($opts);$j++) {
							
							$opts[$j]=trim($opts[$j]);
							
							if($vact==$opts[$j]) $sel='SELECTED'; else $sel='';
							
							print("<option value='".htmlentities($opts[$j])."' $sel>".htmlentities($opts[$j])."</option>");	
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
						
						print("<input type='text' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' size=10 value='$vact' />");
										
					}	
					
					print("</center></td>");	
					
				}	
			
			}
			
		} 

		if(_cax(50))
			$monitoreo="<img src='iconos/book_open.png' style='cursor:pointer;' onClick='abrir_monitoreo(".$r['id2'].");' />";
		else
			$monitoreo="";

		// impresión de documentos...				
		// <img src='iconos/printer.png' style='cursor:pointer;' onClick='imprimir_lista(".$r['in_id'].");' />


		if(!$xls) {
			print("
				<td style='width:75px;'>
				<center>
				<img src='iconos/pencil_go.png' style='cursor:pointer;' onClick='reg_instancia(".$r['monr_id'].");' />
				<img src='iconos/magnifier.png' style='cursor:pointer;' onClick='ver_caso(".$r['mon_id'].");' />
				$monitoreo
				</center>
				</td>
			");
		}
		
		print("</tr>");
				
	}
	
	$tmp=array_unique($selmul_dest);
	$selmul_dest=array();
	
	foreach($tmp AS $key => $val) {
		$selmul_dest[]=$val;
	}

?>	
	
</table>

<script>

	$('cant_total').innerHTML='<?php if($lista) echo sizeof($lista); else echo '0'; ?>';
	
	<?php if($lista) { ?>
	
	selmul=<?php echo json_encode($selmul); ?>;
	selmul_dest=<?php echo json_encode($selmul_dest); ?>;
	
	$('selmul').show();
	
	<?php } else { ?>

	$('selmul').show();

	<?php } ?>
	
</script>
