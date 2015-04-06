<?php
    require_once('../conectar_db.php');
    $pac_id=$_POST['pac_id']*1;
    $frec_id=$_POST['frec_id']*1;
    $filtro=$_POST['filtro_presta']*1;
    $filtro_conv=$_POST['convenios_hosp']*1;
    $modalidad=$_POST['modalidad'];
    $pagare=$_POST['pagare']*1;
    $prev_id=$_POST['prev_id']*1;
    if($pagare==3 || $pagare==4 || $pagare==5 || $pagare==6 || $pagare==7){
        $modalidad='mle';
    }
    if($modalidad=='mle'||$filtro_conv==3 || $filtro_conv==4 || $filtro_conv==5 || $filtro_conv==6 || $filtro_conv==7 )
    {
        $farma='farmas';
    }else{
        $farma='farmacia';
    }
    $mod_w="tipo IN ('$modalidad', 'hsm', '$farma')";
    if(($pac_id*1)==4231510){
        $fecha_limite='2014-05-25';
    }else{
        $fecha_limite='2015-01-14';
    }
        
    if($filtro==0) {
        $fecha_w="nom_fecha::date BETWEEN '".$fecha_limite."' and CURRENT_DATE::date";
	$fecha2_w="log_fecha::date BETWEEN '".$fecha_limite."' and CURRENT_DATE::date";
    } else if($filtro==1) {
        $fecha_w="nom_fecha::date>=CURRENT_DATE";
	$fecha2_w="log_fecha::date>=CURRENT_DATE";
    } else if($filtro==3) {
        $fecha_w="nom_fecha::date>=CURRENT_DATE AND codigo IN ('2702007','2702008','2702019','2702020','2702021')";
	$fecha2_w="false";
    } else {
        $fecha_w="nom_fecha::date>= '".$fecha_limite."'";
	$fecha2_w="log_fecha::date>='".$fecha_limite."'";
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
    //$prev_id=$prev_id;
    $campo='codigos_prestacion.precio';
    $campo2='codigos_prestacion.precio';
    if($modalidad=='mai') {
        if ($prev_id*1>=1 AND $prev_id*1<=4 AND $pac['edad_anios']*1>=60) $campo='0';
        else if($prev_id*1==1) $campo='codigos_prestacion.copago_a';
        else if($prev_id*1==2) $campo='codigos_prestacion.copago_b';
        else if($prev_id*1==3) $campo='codigos_prestacion.copago_c';
        else if($prev_id*1==4) $campo='codigos_prestacion.copago_d';
	$campo2='codigos_prestacion.precio';
    } else {
        if ($prev_id*1>=1 AND $prev_id*1<=4) { $campo='codigos_prestacion.transferen'; $campo2='codigos_prestacion.transferen'; }
        else if($prev_id*1==5) { $campo='codigos_prestacion.transferen';  $campo2='codigos_prestacion.transferen'; }
        else if($prev_id*1==6) { $campo='codigos_prestacion.transferen';  $campo2='codigos_prestacion.transferen';}
    }
    if($prev_id*1==5) {
        $cmod="'mle'";
    } else {
        $cmod="(CASE WHEN nomd_tipo_atencion IN ('CN046', 'PC046', 'PC052', 'PC053') THEN 'mle' ELSE 'mai' END)";
    }
    $consulta="SELECT * FROM (
        SELECT 
	nomina_detalle.nomd_id AS presta_id, nomd_tipo_atencion as tipo_atencion,
	(CASE WHEN nomdp_codigo ILIKE '%-_' THEN split_part(nomdp_codigo,'-',1) ELSE nomdp_codigo END) AS codigo,
	codigos_prestacion.glosa AS glosa,
	$campo2 AS precio,COALESCE($campo,0) AS copago, 'P' AS ptipo, (nom_fecha::date + nomd_hora) AS fecha,
	'S' AS cobro, codigos_prestacion.tipo AS tipo, 1 as cantidad, $cmod AS modalidad, doctores.doc_rut
	FROM nomina_detalle
	JOIN nomina USING (nom_id)
	JOIN doctores ON nom_doc_id=doc_id
	JOIN nomina_detalle_prestaciones as ndp ON ndp.nomd_id=nomina_detalle.nomd_id
	LEFT JOIN codigos_prestacion ON (nomdp_codigo=codigo OR ((CASE WHEN nomdp_codigo ILIKE '%-_' THEN split_part(nomdp_codigo,'-',1)=codigo ELSE false END) AND nomdp_codigo IN ('2701013-C'))) AND $mod_w
	WHERE pac_id=$pac_id AND $fecha_w AND nomdp_codigo IS NOT NULL
	AND nomd_pago=0 AND nomd_diag_cod NOT IN ('X', 'T', 'NSP')  and   (nomd_codigo_no_atiende ='' or nomd_codigo_no_atiende is null)
	UNION
	SELECT 
        rp_stock_id AS presta_id, 'AT' as tipo_atencion,
	art_codigo,
	glosa,
	codigos_prestacion.precio AS precio, COALESCE($campo,0) AS copago, 'R' AS ptipo, log_fecha AS fecha,
	'S' AS cobro, codigos_prestacion.tipo AS tipo, (stock_cant*-1) AS cantidad, '$modalidad' AS modalidad, '' AS doc_rut
	FROM receta
	join recetas_detalle on recetad_receta_id=receta_id
	left join articulo on recetad_art_id=art_id
	LEFT JOIN codigos_prestacion ON codigos_prestacion.codigo=art_codigo AND tipo='$farma'
	LEFT JOIN logs on recetad_id=log_recetad_id
	left join stock on stock_log_id=log_id
	left join recetas_pago on rp_stock_id=stock_id
	WHERE receta_paciente_id=$pac_id AND $fecha2_w AND rp_desp_pago=0
    ) AS foo ORDER BY fecha;";
    
    
    //print($consulta);
    $p=cargar_registros_obj($consulta, true);
    //nom_fecha::date AS fecha_realizacion,
    //nomd_hora AS hora_realizacion,
    // CODIGOS NORMALES SE INDEXA POR MODALIDAD Y CODIGO
    $codigos=cargar_registros_obj("
    SELECT codigo, glosa, $campo2 AS precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, $campo AS copago, pago_fijo,transferen
    FROM codigos_prestacion 
    ORDER BY codigo;
    ", true);
    $cods=Array();
    for($i=0;$i<sizeof($codigos);$i++) {
        $cods[($codigos[$i]['tipo'].''.$codigos[$i]['codigo'])]=$codigos[$i];
    }
    // CODIGOS EN CONVENIO SE INDEXA POR RUT MODALIDAD Y CODIGO
    $codigos_conv=cargar_registros_obj("
    SELECT codigo, glosa, $campo2 AS precio, copago_a, copago_b, copago_c, copago_d, pab, tipo, canasta, $campo AS copago, pago_fijo, convenios,transferen
    FROM codigos_prestacion_convenio AS codigos_prestacion
    ORDER BY codigo;
    ", true);	
	
    $cods_conv=Array();
    for($i=0;$i<sizeof($codigos_conv);$i++) {
        $cods_conv[$codigos_conv[$i]['convenios']][($codigos_conv[$i]['tipo'].''.$codigos_conv[$i]['codigo'])]=$codigos_conv[$i];
    }
    $p2=Array();
    
    function info_codigo($codigo, $rut) {
        GLOBAL $cods, $cods_conv, $modalidad,$farma;
        if($rut!='' AND isset($cods_conv[$rut][$modalidad.''.$codigo]) AND ($tatencion=='PC052' || $tatencion=='PC053')) {
            $codigo_ret=$cods_conv[$rut][$modalidad.''.$codigo];
	} else {
            if(isset($cods[$modalidad.''.$codigo])) {
                $codigo_ret= $cods[$modalidad.''.$codigo];
            } else {
                if(isset($cods['hsm'.$codigo])) {
                    $codigo_ret= $cods['hsm'.$codigo];
                } else {
                    if(isset($cods[$farma.''.$codigo])) {
                        $codigo_ret= $cods[$farma.''.$codigo];
                    } else {
                        $codigo_ret= false;
                    }
		}
            }			
	}
	return $codigo_ret;
    }
	
    function info_codigo2($codigo, $modalidad, $rut, $tatencion) {
        GLOBAL $cods, $cods_conv,$farma;
        if($rut!='' AND isset($cods_conv[$rut][$modalidad.''.$codigo]) AND ($tatencion=='PC052' || $tatencion=='PC053')) {
            $codigo_ret=$cods_conv[$rut][$modalidad.''.$codigo];
	} else {
            if(isset($cods[$modalidad.''.$codigo])) {
                $codigo_ret= $cods[$modalidad.''.$codigo];
            } else {
                if(isset($cods['hsm'.$codigo])) {
                    $codigo_ret= $cods['hsm'.$codigo];
		} else {
                    if(isset($cods[$farma.''.$codigo])) {
                        $codigo_ret= $cods[$farma.''.$codigo];
                    } else {
                        $codigo_ret= false;
                    }
		}
            }			
	}
	return $codigo_ret;
    }
	
    function calcular_precio($codigo, $modalidad, $rut, $tatencion) {
        GLOBAL $pac, $prev_id, $frec_id, $pagare,$filtro_conv;
	$cod=info_codigo2($codigo, $modalidad, $rut, $tatencion);
	if($filtro_conv!=3 && $filtro_conv!=4 && $filtro_conv!=5 && $filtro_conv!=6 && $filtro_conv!=7 && $filtro_conv!=8 && $filtro_conv!=9) {
            if(!$cod) return Array(null,null);
                //if(cod==undefined){ alert('Arancel no encontrado!');return [null,null];}
        }
        if($cod) {
            if($cod['pago_fijo']=='t') {
                return Array($cod['precio'], $cod['precio']);
            }
	} 
	if($filtro_conv==3 || $filtro_conv==4 || $filtro_conv==5 || $filtro_conv==6 || $filtro_conv==7 || $filtro_conv==8 || $filtro_conv==9) {
            if($filtro_conv==3 )//capedena copago d
            {
                $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) { $valor=$cod2['copago_d']; $total=$cod2['copago_d']; } else { return Array(nul,null);}
            }
            if($filtro_conv==8 || $filtro_conv==9)//prais
            {
                $cod2=info_codigo2($codigo, 'mai', $rut, $tatencion); if($cod2) { $valor=$cod2['copago_a']; $total=$cod2['precio']; } else { return Array(nul,null);}
            }
            if($filtro_conv==4 || $filtro_conv==5 || $filtro_conv==6 || $filtro_conv==7)//dipreca copago c
            {
                $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) { $valor=$cod2['copago_c']; $total=$cod2['copago_c']; } else { return Array(nul,null);}
            }
            return Array($total, $valor);
	} else if($modalidad=='mai') {
            $total=$cod['precio'];
            if($frec_id>0) {
                if($prev_id>=1 AND $prev_id<=4) $valor=$cod['copago_b'];
                elseif($prev_id==5) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) $valor=$cod2['precio']; else $valor=$cod['precio']; }
		elseif($prev_id==6) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) $valor=$cod2['copago_b']; else $valor=$cod['precio']; }
		else $valor=$cod['copago_d'];
            } else {
                if($prev_id==6)
                    $valor=$cod['precio'];
		elseif($prev_id==5) { $cod2=info_codigo2($codigo, 'mle', $rut, $tatencion); if($cod2) { $total=$cod2['copago_b']; $valor=$cod2['copago_b']; } else $valor=$cod['precio']; }
		elseif($prev_id>=1 AND $prev_id<=4 && $pac['edad_anios']>=60) $valor=0;
		elseif($prev_id==1) $valor=$cod['copago_a'];
		elseif($prev_id==2) $valor=$cod['copago_b'];
		elseif($prev_id==3) $valor=$cod['copago_c'];
		elseif($prev_id==4) $valor=$cod['copago_d'];
		else $valor=$cod['precio'];
            }
            return Array($total, $valor);
        } else {
            if($frec_id>0) {
                $cod2=info_codigo2($codigo, 'mai', $rut, $tatencion); if($cod2) { $total=$cod2['precio']; $valor=$cod2['precio']; } else { $total=$cod['precio']; $valor=$cod['precio']; }
            } else {
                if($prev_id>=1 AND $prev_id<=4) {
                    $total=$cod['transferen'];
                    $valor=$cod['transferen'];
                } else if($prev_id==5) { 
                    $total=$cod['transferen']; 
                    $valor=$cod['transferen']; 
                } else { 
                    $total=$cod['transferen']; 
                    $valor=$cod['transferen']; 
                }
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
                if(!$c)
                    continue;
		
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
        } else
            $p2=$p;
        //print_r($p2);
        //die();
	echo json_encode($p2);
?>