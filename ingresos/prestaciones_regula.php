<?php 

	require_once('../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;
	$bol_num=$_POST['nboletin']*1;
	$frec_id=$_POST['frec_id']*1;
	$filtro=$_POST['filtro_presta']*1;
	$modalidad=$_POST['modalidad'];
	$pagare=$_POST['pagare']*1;
	
	
	$mod_w="tipo IN ('$modalidad', 'crs', 'Farmacia')";
	
	if($filtro==0) {
		$fecha_w="nom_fecha::date>='04/02/2014' and nom_fecha::date<=CURRENT_DATE";
		$fecha2_w="fecha_despacho::date>='04/02/2014' and fecha_despacho::date<=CURRENT_DATE";
	} else if($filtro==1) {
		$fecha_w="nom_fecha::date>=CURRENT_DATE";
		$fecha2_w="fecha_despacho::date>=CURRENT_DATE";
	} else if($filtro==3) {
		$fecha_w="nom_fecha::date>=CURRENT_DATE AND codigo IN ('2702007','2702008','2702019','2702020','2702021')";
		$fecha2_w="false";
	} else {
		$fecha_w='true';
		$fecha2_w='true';
	}
	
	/*
	
	
	
		SELECT presta_id, 
		COALESCE(codigo, presta_codigo_v) AS codigo, 
		COALESCE(codigos_prestacion.glosa, presta_desc) AS glosa, 
		COALESCE(mai.precio,presta_valor) AS precio, 'R' AS tipo, presta_fecha AS fecha 
		FROM prestacion 
		LEFT JOIN codigos_prestacion ON presta_codigo_v=codigo
		LEFT JOIN mai ON ((grupo || sub_grupo || presta) = presta_codigo_v) AND corr='0000'
		WHERE pac_id=$pac_id AND presta_estado=1
		
		UNION
		
	
	
	*/
	
	$pac=cargar_registro("
		SELECT *, 
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
		FROM pacientes WHERE pac_id=$pac_id LIMIT 1
	");
	
	$prev_id=$pac['prev_id']*1;
	
	$campo='codigos_prestacion.precio';
	
	if($modalidad=='mai') {
		if ($pac['prev_id']*1>=10 AND $pac['prev_id']*1<=15 AND $pac['edad_anios']*1>=60) $campo='0';
		else if($pac['prev_id']*1==12) $campo='codigos_prestacion.copago_a';
		else if($pac['prev_id']*1==10) $campo='codigos_prestacion.copago_b';
		else if($pac['prev_id']*1==11) $campo='codigos_prestacion.copago_c';
		else if($pac['prev_id']*1==15) $campo='codigos_prestacion.copago_d';
		
		$campo2='codigos_prestacion.precio';
	} else {
		if ($pac['prev_id']*1>=10 AND $pac['prev_id']*1<=15) { $campo='codigos_prestacion.copago_a'; $campo2='codigos_prestacion.copago_a'; }
		else if($pac['prev_id']*1==5) { $campo='codigos_prestacion.copago_b';  $campo2='codigos_prestacion.copago_b'; }
		else if($pac['prev_id']*1==6) { $campo='codigos_prestacion.copago_d';  $campo2='codigos_prestacion.copago_d';}
	} 
	
	if($pac['prev_id']*1==5) {
			$cmod="'mle'";
	} else { 
		$cmod="(CASE WHEN nomd_tipo_atencion IN ('CN046', 'PC046', 'PC052', 'PC053') THEN 'mle' ELSE 'mai' END)";
	}
	
	$p=cargar_registros_obj("
 
		SELECT * FROM ( 
						
			SELECT 
			bdet_presta_id AS presta_id, 'AT' as tipo_atencion,
			bdet_codigo AS codigo,
			codigos_prestacion.glosa AS glosa,
			$campo2 AS precio,COALESCE($campo,0) AS copago, 'P' AS ptipo, bdet_fecha AS fecha,
			'S' AS cobro, codigos_prestacion.tipo AS tipo, 1 as cantidad, $cmod AS modalidad, doctores.doc_rut
			FROM boletin_detalle
			left join nomina_detalle on nomd_id= bdet_presta_id
			LEFT JOIN nomina using(nom_id)
			LEFT JOIN doctores ON nom_doc_id=doc_id
			
			JOIN codigos_prestacion ON bdet_codigo=codigo
			WHERE bolnum=$bol_num AND bdet_codigo IS NOT NULL AND codigos_prestacion.tipo='$modalidad'
			
			UNION
			
			SELECT 
			 
			id AS presta_id, 'AT' as tipo_atencion,
			sga_recetas.codigo,
			glosa,
			codigos_prestacion.precio AS precio, COALESCE($campo,0) AS copago, 'R' AS ptipo, fecha_retiro AS fecha,
			'S' AS cobro, codigos_prestacion.tipo AS tipo, cantidad_solicitada AS cantidad, '$modalidad' AS modalidad, '' AS doc_rut
			FROM sga_recetas
			LEFT JOIN codigos_prestacion ON codigos_prestacion.codigo=sga_recetas.codigo AND tipo='Farmacia'
			LEFT JOIN sga_recetas_pago USING (id)
			WHERE id_paciente=$pac_id AND  fecha_retiro   in (SELECT bdet_fecha from boletin_detalle where bolnum=$bol_num)
		 AND codigos_prestacion.codigo in (SELECT bdet_codigo from boletin_detalle where bolnum=$bol_num)
		
		) AS foo ORDER BY fecha;
		
	", true);
	
	
	//nom_fecha::date AS fecha_realizacion,
	//nomd_hora AS hora_realizacion,
	
	
	// CODIGOS NORMALES SE INDEXA POR MODALIDAD Y CODIGO

	$codigos=cargar_registros_obj("
		SELECT codigo, glosa, $campo2 AS precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, $campo AS copago, pago_fijo
		FROM codigos_prestacion 
		ORDER BY codigo;
	", true);	
	
	$cods=Array();
	
	for($i=0;$i<sizeof($codigos);$i++) {
		$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
	}

	// CODIGOS EN CONVENIO SE INDEXA POR RUT MODALIDAD Y CODIGO
	
	$codigos_conv=cargar_registros_obj("
		SELECT codigo, glosa, $campo2 AS precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, $campo AS copago, pago_fijo, convenios
		FROM codigos_prestacion_convenio AS codigos_prestacion
		ORDER BY codigo;
	", true);	
	
	$cods_conv=Array();
	
	for($i=0;$i<sizeof($codigos_conv);$i++) {
		$cods_conv[$codigos_conv[$i]['convenios']][($codigos_conv[$i]['tipo'].''.$codigos_conv[$i]['codigo'])]=$codigos_conv[$i];
	}
	
	$p2=Array();
	
	function info_codigo($codigo, $rut) {
	
		GLOBAL $cods, $cods_conv, $modalidad;
				
		if($rut!='' AND isset($cods_conv[$rut][$modalidad.''.$codigo])) {
		
			return $cods_conv[$rut][$modalidad.''.$codigo];
		
		} else {	
		
			if(isset($cods[$modalidad.''.$codigo]))
				return $cods[$modalidad.''.$codigo];
			else if(isset($cods['crs'.$codigo]))
				return $cods['crs'.$codigo];
			else if(isset($cods['Farmacia'.$codigo]))
				return $cods['Farmacia'.$codigo];
			else
				return false;
				
		}
		
	}
	
	function info_codigo2($codigo, $modalidad, $rut, $tatencion) {
	
		GLOBAL $cods, $cods_conv;

		if($rut!='' AND isset($cods_conv[$rut][$modalidad.''.$codigo]) AND ($tatencion=='PC052' || $tatencion=='PC053')) {

				return $cods_conv[$rut][$modalidad.''.$codigo];
		
		} else {
		
			if(isset($cods[$modalidad.''.$codigo]))
				return $cods[$modalidad.''.$codigo];
			else if(isset($cods['crs'.$codigo]))
				return $cods['crs'.$codigo];
			else if(isset($cods['Farmacia'.$codigo]))
				return $cods['Farmacia'.$codigo];
			else
				return false;

		}
			
	}
	
	function calcular_precio($codigo, $modalidad, $rut, $tatencion) {
	
		GLOBAL $pac, $prev_id, $frec_id, $pagare;
	
		$cod=info_codigo2($codigo, $modalidad, $rut, $tatencion);
		if(!$cod) return Array(0,0);
		
		if($cod['pago_fijo']=='t')
			return Array($cod['precio'], $cod['precio']);
			
		if($pagare==3) {
		
			$cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) { $valor=$cod2['copago_c']; $total=$cod2['copago_b']; } else { $valor=$cod['precio']; $total=$cod['precio']; }

			return Array($total, $valor);
			
		} else if($modalidad=='mai') {

			$total=$cod['precio'];
					
			if($frec_id>0) {
			
				if($prev_id>=10 AND $prev_id<=15) $valor=$cod['copago_b'];
				elseif($prev_id==5) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) $valor=$cod2['copago_b']; else $valor=$cod['precio']; }
				elseif($prev_id==6) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) $valor=$cod2['copago_b']; else $valor=$cod['precio']; }
				else $valor=$cod['copago_d'];
			
			} else {
			
				if($prev_id==6) $valor=$cod['precio'];
				elseif($prev_id==5) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) { $total=$cod2['copago_b']; $valor=$cod2['copago_b']; } else $valor=$cod['precio']; }
				elseif($prev_id>=10 AND $prev_id<=15 && $pac['edad_anios']>=60) $valor=0;
				elseif($prev_id==12) $valor=$cod['copago_a'];
				elseif($prev_id==10) $valor=$cod['copago_b'];
				elseif($prev_id==11) $valor=$cod['copago_c'];
				elseif($prev_id==15) $valor=$cod['copago_d'];
				else $valor=$cod['precio'];
			
			}
			return Array($total, $valor);
		
		} else {
		
			if($frec_id>0) {
			
				$cod2=info_codigo2($codigo, 'mai', $rut, $tatencion); if($cod2) { $total=$cod2['precio']; $valor=$cod2['precio']; } else { $total=$cod['precio']; $valor=$cod['precio']; }
			
			} else {

				if($prev_id>=10 AND $prev_id<=15) { $total=$cod['copago_a']; $valor=$cod['copago_a']; }
				else if($prev_id==5) { $total=$cod['copago_b']; $valor=$cod['copago_b']; }
				else { $total=$cod['copago_d']; $valor=$cod['copago_d']; }
			
			}

			return Array($total, $valor);
			
		}
		
	
	}


	if($p)
	for($i=0;$i<sizeof($p);$i++) {
		
	
		$_codigo=info_codigo2($p[$i]['codigo'], $p[$i]['modalidad'], $p[$i]['doc_rut'], $p[$i]['tipo_atencion']);

		list($p[$i]['precio'], $p[$i]['copago'])=calcular_precio($p[$i]['codigo'], $p[$i]['modalidad'], $p[$i]['doc_rut'], $p[$i]['tipo_atencion']);
		
		if(isset($_codigo['convenios'])) {
			$p[$i]['convenios']=$_codigo['convenios'];
		}
		
		$p2[]=$p[$i];
		
		if($_codigo['canasta']!='') {
		
			if(strstr($_codigo['canasta'],'x')) {
				list($codigo, $cant)=explode('x',$_codigo['canasta']);
			} else {
				$codigo=$_codigo['canasta'];
				$cant='1';
			}

			$c=info_codigo($codigo, $p[$i]['doc_rut']); 
			if(!$c) continue;
			
			$num=sizeof($p2);
			
			$p2[$num]=Array();
			$p2[$num]['presta_id']='0';
			$p2[$num]['codigo']=$c['codigo'];
			$p2[$num]['glosa']=$c['glosa'];
			
			list($p2[$num]['precio'],$p2[$num]['copago'])=calcular_precio($c['codigo'], $p[$i]['modalidad'], $p[$i]['doc_rut'], $p[$i]['tipo_atencion']);
			
			$p2[$num]['cantidad']=$cant;
			$p2[$num]['tipo']=$c['tipo'];
			$p2[$num]['modalidad']=$p[$i]['modalidad'];
			$p2[$num]['ptipo']='P';
			$p2[$num]['fecha']=$p[$i]['fecha'];
			$p2[$num]['cobro']='S';
			
			if(isset($c['convenios']))
				$p2[$num]['convenios']=$c['convenios'];
			
			
			
		}
		
	} else $p2=$p;
	
	echo json_encode($p2);

?>