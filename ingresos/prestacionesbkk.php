<?php 

	require_once('../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;
	$frec_id=$_POST['frec_id']*1;
	$filtro=$_POST['filtro_presta']*1;
	$modalidad=$_POST['modalidad'];
	
	
	$mod_w="tipo IN ('$modalidad', 'crs', 'Farmacia')";
	
	if($filtro==0) {
		$fecha_w="nom_fecha::date<=CURRENT_DATE AND   nom_fecha::date>='2014-02-01'";
		$fecha2_w="fecha_despacho::date<=CURRENT_DATE AND   fecha_despacho::date>='2014-02-01'";
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
	
	$p=cargar_registros_obj("

		SELECT * FROM (
	
			SELECT 
			nomd_id AS presta_id, 
			(CASE WHEN nomd_codigo_presta ILIKE '%-_' THEN split_part(nomd_codigo_presta,'-',1) ELSE nomd_codigo_presta END) AS codigo,
			codigos_prestacion.glosa AS glosa,
			$campo2 AS precio,COALESCE($campo,0) AS copago, 'P' AS ptipo, (nom_fecha::date + nomd_hora) AS fecha,
			'S' AS cobro, codigos_prestacion.tipo AS tipo, 1 as cantidad, (CASE WHEN nomd_tipo_atencion IN ('CN046', 'PC046') THEN 'mle' ELSE 'mai' END) AS modalidad
			FROM nomina_detalle
			JOIN nomina USING (nom_id)
			JOIN doctores ON nom_doc_id=doc_id
			LEFT JOIN codigos_prestacion ON (nomd_codigo_presta=codigo OR ((CASE WHEN nomd_codigo_presta ILIKE '%-_' THEN split_part(nomd_codigo_presta,'-',1)=codigo ELSE false END) AND nomd_codigo_presta IN ('2701013-C'))) AND $mod_w
			WHERE pac_id=$pac_id AND $fecha_w AND nomd_codigo_presta IS NOT NULL
			AND nomd_pago=0 AND nomd_diag_cod NOT IN ('X', 'T', 'NSP')
			
			UNION
			
			SELECT 
			id AS presta_id,
			sga_recetas.codigo,
			glosa,
			codigos_prestacion.precio AS precio, COALESCE($campo,0) AS copago, 'R' AS ptipo, fecha_retiro AS fecha,
			'S' AS cobro, codigos_prestacion.tipo AS tipo, cantidad_solicitada AS cantidad, '$modalidad' AS modalidad
			FROM sga_recetas
			LEFT JOIN codigos_prestacion ON codigos_prestacion.codigo=sga_recetas.codigo AND tipo='Farmacia'
			LEFT JOIN sga_recetas_pago USING (id)
			WHERE id_paciente=$pac_id AND $fecha2_w AND desp_pago IS NULL
		
		) AS foo ORDER BY fecha;
		
	", true);
	
	
	//nom_fecha::date AS fecha_realizacion,
	//nomd_hora AS hora_realizacion,

	$codigos=cargar_registros_obj("
		SELECT codigo, glosa, $campo2 AS precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, $campo AS copago, pago_fijo
		FROM codigos_prestacion 
		ORDER BY codigo;
	", true);
	
	$cods=Array();
	
	for($i=0;$i<sizeof($codigos);$i++) {
		$cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
	}

	$p2=Array();
	
	function info_codigo($codigo) {
	
		GLOBAL $cods, $modalidad;
		
		if(isset($cods[$modalidad.''.$codigo]))
			return $cods[$modalidad.''.$codigo];
		else if(isset($cods['crs'.$codigo]))
			return $cods['crs'.$codigo];
		else if(isset($cods['Farmacia'.$codigo]))
			return $cods['Farmacia'.$codigo];
		else
			return false;
		
	}
	
	function info_codigo2($codigo, $modalidad) {
	
		GLOBAL $cods;
		
		if(isset($cods[$modalidad.''.$codigo]))
			return $cods[$modalidad.''.$codigo];
		else if(isset($cods['crs'.$codigo]))
			return $cods['crs'.$codigo];
		else if(isset($cods['Farmacia'.$codigo]))
			return $cods['Farmacia'.$codigo];
		else
			return false;
		
	}
	
	function calcular_precio($codigo, $modalidad) {
	
		GLOBAL $pac, $prev_id, $frec_id;
	
		$cod=info_codigo2($codigo, $modalidad);
		if(!$cod) return Array(0,0);
		
		if($cod['pago_fijo']=='t')
			return Array($cod['precio'], $cod['precio']);
			
		if($modalidad=='mai') {

			$total=$cod['precio'];
		
			if($frec_id>0) {
			
				if($prev_id>=10 AND $prev_id<=15) $valor=$cod['copago_b'];
				elseif($prev_id==5) $valor=$cod['precio']*1;
				elseif($prev_id==6) { $cod2=info_codigo2($codigo, 'mle'); if($cod2) $valor=$cod2['copago_b']; else $valor=$cod['precio']; }
				else $valor=$cod['precio'];
			
			} else {
			
				if($prev_id==6) $valor=$cod['precio'];
				elseif($prev_id==5) { $cod2=info_codigo2($codigo, 'mle'); if($cod2) { $total=$cod2['copago_b']; $valor=$cod2['copago_b']; } else $valor=$cod['precio']; }
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
			
				$cod2=info_codigo2($codigo, 'mai'); if($cod2) { $total=$cod2['precio']; $valor=$cod2['precio']; } else { $total=$cod['precio']; $valor=$cod['precio']; }
			
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
	
		$_codigo=info_codigo($p[$i]['codigo']);

		list($p[$i]['precio'], $p[$i]['copago'])=calcular_precio($p[$i]['codigo'], $p[$i]['modalidad']);
		
		$p2[]=$p[$i];
		
		if($_codigo['canasta']!='') {
		
			if(strstr($_codigo['canasta'],'x')) {
				list($codigo, $cant)=explode('x',$_codigo['canasta']);
			} else {
				$codigo=$_codigo['canasta'];
				$cant='1';
			}

			$c=info_codigo($codigo); 
			if(!$c) continue;
			
			$num=sizeof($p2);
			
			$p2[$num]=Array();
			$p2[$num]['presta_id']='0';
			$p2[$num]['codigo']=$c['codigo'];
			$p2[$num]['glosa']=$c['glosa'];
			list($p2[$num]['precio'],$p2[$num]['copago'])=calcular_precio($c['codigo'], $p[$i]['modalidad']);
			
			$p2[$num]['cantidad']=$cant;
			$p2[$num]['tipo']=$c['tipo'];
			$p2[$num]['modalidad']=$p[$i]['modalidad'];
			$p2[$num]['ptipo']='P';
			$p2[$num]['fecha']=$p[$i]['fecha'];
			$p2[$num]['cobro']='S';
			
			
			
		}
		
	} else $p2=$p;
	
	echo json_encode($p2);

?>
