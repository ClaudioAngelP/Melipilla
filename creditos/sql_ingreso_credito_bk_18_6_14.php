<?php 

	require_once('../conectar_db.php');

	function fecha_sql($f) {
		$fp=explode('/',$f);
		if(sizeof($fp)==3 AND checkdate($fp[1]*1, $fp[0]*1,$fp[2]*1)) 
			return "'".$f."'";
		else 
			return 'null';
	}


	$pac_id=pg_escape_string($_POST['pac_id']*1);
	
	$p=cargar_registro("SELECT * FROM pacientes LEFT JOIN prevision USING (prev_id) WHERE pac_id=$pac_id;");
	$prev_id=$p['prev_id']*1;
	$prev_desc=pg_escape_string($p['prev_desc']);
	
	$efectivo=$_POST['pago_efectivo']*1;
	
	if($_POST['pago_cheques']!='')	
		$cheques=explode('[|]',substr($_POST['pago_cheques'],0,-3));
	else
		$cheques=false;	
	
	if($_POST['pago_otras']!='')	
		$otras=explode('[|]',substr($_POST['pago_otras'],0,-3));
	else
		$otras=false;	

	pg_query("START TRANSACTION;");

	$nbolnum=$_POST['nbolnum']*1;
	
	$nboleta=$_POST['nboleta']*1;
	if($nboleta==0) $nboleta='null';
	
	$nbolfec=fecha_sql($_POST['nbolfec']);

	$datos_pagare=pg_escape_string(utf8_decode($_POST['datos_titular']));

	$modalidad=pg_escape_string(utf8_decode($_POST['modalidad']));
	
	//$prodesc=pg_escape_string($_POST['prodesc']);
	$proval=floor($_POST['proval']*1);
	$total_descuento = floor($_POST['total_descuento']*1);
	$pie=floor($_POST['pie']*1);
	$cuonro=$_POST['cuonro']*1;
	
	$pagare=(isset($_POST['pagare']) AND $_POST['pagare']*1==1)?'true':'false';
	$garantia=(isset($_POST['pagare']) AND $_POST['pagare']*1==2)?'true':'false';
	
	if( ( $proval - $total_descuento ) > 0 AND 
			$pie < ( $proval - $total_descuento ) ) {	
	
	$interes = 0;
	
	if($cuonro==0) $cuonro=1;

	if(isset($_POST['aplicaint'])) {
	
		$inf = $interes/100;
		$fact = pow(1+$inf,$cuonro);	
	
		$saldo = ($proval - $total_descuento - $pie);
		$cuomon = $saldo*(($inf*$fact)/($fact-1));

		$credito = $cuomon*$cuonro; 
	
	} else {

		$inf = $interes/100;
		$fact = pow(1+$inf,$cuonro);	
	
		$saldo = ($proval - $total_descuento - $pie);
		
		/*if($cuonro>2)
			$cuomon = $saldo*(($inf*$fact)/($fact-1));
		else*/
			$cuomon = $saldo;

		$credito_interes = $cuomon*$cuonro; 

		$saldo = ($proval - $total_descuento - $pie);
		$cuomon = $saldo/$cuonro;

		$credito = $cuomon*$cuonro;
		$dif_credito=$credito_interes-$credito; 

	}
	
	pg_query("INSERT INTO creditos VALUES (
		$pac_id,
		NEXTVAL('creditos_crecod_seq'),
		$proval, $pie, ".$saldo.", ".$cuomon.",
		".$credito.", current_timestamp, current_timestamp, '$prodesc',
		$cuonro,'.1455','N' 	
	);");
	
	$crecod="CURRVAL('creditos_crecod_seq')";
	
	} else $crecod='0';
	
	if(($proval-$total_descuento)<0) {
		$saldo_favor=-($proval-$total_descuento);
		$coming="NEXTVAL('boletines_coming_seq')";
	} else {
		$saldo_favor=0;
		$coming='0';
	}	
	
	if($nbolnum!=0) {
		$bolnum=$nbolnum;
		$currval=$nbolnum;
		$bolfec=$nbolfec;
	} else {
		$bolnum="NEXTVAL('boletines_bolnum_seq')";
		$currval="CURRVAL('boletines_bolnum_seq')";
		$bolfec='current_timestamp';
	}	
	
	$chk=cargar_registro("SELECT $bolnum AS bnum;");
	$chk2=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=".$chk['bnum']);
	
	if($chk2) {
		pg_query('ROLLBACK;');
		die('BOLETÍN # '.number_format($chk['bnum'],0,',','.').' YA EXISTE ( FECHA: '.$chk2['bolfec'].' ), EL ADMINISTRADOR DEBE CORREGIR LA NUMERACIÓN ACTUAL DE LOS BOLETINES.');
	} else {
		$bolnum = ($chk['bnum']*1);
	}	
	
	pg_query("INSERT INTO boletines VALUES (
		$bolnum,
		$bolfec,
		$pie,
		'',
		1,
		0,
		$crecod, ".$_SESSION['sgh_usuario_id'].", $pac_id,
		null, $saldo_favor, $coming, $pagare, '$prev_id', '$prev_desc', 
		$nboleta, '$datos_pagare', '$modalidad', '', $garantia
	);");

	$boletin=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=$currval;");			

	/*if(!isset($_POST['aplicaint']))
		pg_query("INSERT INTO descuentos VALUES (DEFAULT, $currval, '', $dif_credito, 0, 0, 'i');");*/
	


	// Anulación de boletines en caso de ser necesario...

	if($_POST['bolnums']!='') {
	
		$d=explode('|',utf8_decode(pg_escape_string($_POST['bolnums'])));
		
		$banular='';		
		
		for($i=0;$i<sizeof($d);$i++) {
			$dr=explode('/',$d[$i]);
			if($dr[0]=='b') {
				$banular.=($dr[1]*1).',';
				pg_query("INSERT INTO descuentos VALUES (DEFAULT, $currval, '".($dr[1])."', ".($dr[2]*1).", 0, ".($dr[1]).", 'b');");
			} elseif($dr[0]=='c') {
				$bcred=cargar_registros_obj("
					SELECT * FROM boletines WHERE crecod=".($dr[1]*1)."				
				");
				for($j=0;$j<sizeof($bcred);$j++) {
					$banular.=$bcred[$j]['bolnum'].',';
					pg_query("INSERT INTO descuentos VALUES (DEFAULT, $currval, '".($dr[1])."', ".($dr[2]*1).", 0, ".($bcred[$j]['bolnum']).", 'c');");
				}
			} elseif($dr[0]=='d') {
				pg_query("INSERT INTO descuentos VALUES (DEFAULT, $currval, '".($dr[1])."', ".($dr[2]*1).", 0, 0, 'd');");
			} elseif($dr[0]=='bn') {
				pg_query("INSERT INTO boletines VALUES (".($dr[1]*1).", null, ".($dr[2]*1).", '', 0,0,0, ".$_SESSION['sgh_usuario_id'].",0,$currval);");
				pg_query("INSERT INTO descuentos VALUES (DEFAULT, $currval, '".($dr[1])."', ".($dr[2]*1).", 0, ".($dr[1]).", 'bn');");			
			}
		}
			
	}	


	
	$presta=json_decode($_POST['prestaciones']);	
	//var_dump($presta);
	for($i=0;$i<sizeof($presta);$i++) {
		
		$valort=$presta[$i]->precio*1;	
		$valorp=$presta[$i]->copago*1;	
		
		if($presta[$i]->cobro=='N')
			continue;

		/*$pr=cargar_registros_obj("
			SELECT * FROM prestacion WHERE presta_id=".($presta[$i]->presta_id*1)."			
		");*/

	
		pg_query("INSERT INTO boletin_detalle VALUES (
			DEFAULT, $currval, 
			".($presta[$i]->presta_id*1).", ".($valorp).",
			0, 
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->glosa)))."',	
			'',
			'".pg_escape_string(html_entity_decode($presta[$i]->codigo))."',
			'".pg_escape_string(html_entity_decode($presta[$i]->cobro))."',
			".$valort.", '".pg_escape_string(html_entity_decode($presta[$i]->fecha))."', ".($presta[$i]->cantidad*1)."
		);");
		
		if(($presta[$i]->presta_id*1)>0 AND isset($presta[$i]->ptipo)) {
			if($presta[$i]->ptipo=='P') {
				pg_query("UPDATE nomina_detalle SET nomd_pago=1 WHERE nomd_id=".($presta[$i]->presta_id*1));
			}
			if($presta[$i]->ptipo=='R') {
				pg_query("INSERT INTO sga_recetas_pago VALUES (".($presta[$i]->presta_id*1).", 1)");
			}
		}
		
		$bolnumd="CURRVAL('boletin_detalle_bdet_id_seq')";
	
		$getid=cargar_registro("SELECT $bolnumd AS bnumd;");
		$getid2=cargar_registro("SELECT bdet_id  FROM boletin_detalle WHERE bolnum=".$getid['bnumd']);
	
		$arr[$i]=$getid['bnumd'];
		

	
	}


	$seguros=json_decode($_POST['seguros']);	
	
	$companias=explode("\n",trim(file_get_contents('../ingresos/companias.list')));
	
	$cias='';
	
	for($i=0;$i<sizeof($companias);$i++) {
		if(trim($companias[$i])=='') continue;
		$cias[]=($companias[$i]);
	}
	
	sort($cias);
	
	if($seguros)
	for($i=0;$i<sizeof($seguros);$i++) {
	
/*
create table seguros (
seg_id bigserial,
bolnum bigint,
seg_tipo smallint,
compania text,
poliza text,
rut text,
nombre text,
fecha date,
serie text
);
*/	
		
		pg_query("INSERT INTO seguros VALUES (
			DEFAULT, $currval, 
			".($seguros[$i]->tipo*1).", 
			'".pg_escape_string($cias[$seguros[$i]->compania*1])."',		
			'".pg_escape_string($seguros[$i]->poliza)."',		
			'".pg_escape_string($seguros[$i]->rut)."',		
			'".pg_escape_string($seguros[$i]->nombre)."',		
			'".pg_escape_string($seguros[$i]->fecha)."',		
			'".pg_escape_string($seguros[$i]->serie)."'
			''		
		);");
	
	}



	
	if($cheques)
	for($i=0;$i<sizeof($cheques);$i++) {

		$c=explode('|',$cheques[$i]);
		
		if($c[2]=='')		
			$c[2]=date('d/m/Y');
		else {
			switch(strlen($c[2])) {			
				case 8:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/'.substr($c[2],4,5);
				break;
				case 6:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/20'.substr($c[2],4,2);
				break;
			}
		}		
		
		pg_query("INSERT INTO cheques VALUES (
			DEFAULT,
			$currval,
			'".pg_escape_string($c[0])."',		
			'".pg_escape_string($c[1])."',		
			'".pg_escape_string($c[2])."',		
			'".pg_escape_string($c[3])."',		
			".pg_escape_string($c[4]).",		
			'".pg_escape_string($c[5])."'		
		);");
		
	}

	if($otras)
	for($i=0;$i<sizeof($otras);$i++) {

		$c=explode('|',$otras[$i]);
		
		if($c[3]=='')		
			$c[3]=date('d/m/Y');
		else {
			switch(strlen($c[3])) {			
				case 8:
				$c[2]=substr($c[3],0,2).'/'.substr($c[3],2,2).'/'.substr($c[3],4,5);
				break;
				case 6:
				$c[2]=substr($c[3],0,2).'/'.substr($c[3],2,2).'/20'.substr($c[3],4,2);
				break;
			}
		}		
		
		$num=($c[5])*1;
		$valor= $arr[$num];
		pg_query("INSERT INTO forma_pago VALUES (
			DEFAULT,
			$currval,
			".(pg_escape_string($c[0])*1).",		
			'".pg_escape_string($c[2])."',		
			'".pg_escape_string($c[3])."',		
			".(pg_escape_string($c[4])*1).",
			upper('".pg_escape_string($c[1])."'),
			".$valor."
				
		);");
	}



	if( $pie < ( $proval - $total_descuento ) ) {

	$diapago=$_POST['diapago']*1;
	$offset_mes=0;
	
	if($garantia=='true') {

		$cuofec=date('d-m-Y',mktime(0,0,0,date('m'),date('d'),date('Y')));	

		pg_query("
		INSERT INTO cuotas VALUES (
		CURRVAL('creditos_crecod_seq'),
		0, $pie, '$cuofec', current_timestamp,
		$currval, 'P', $pie, null,
		NEXTVAL('cuotas_cuoid_seq')		
		);		
		");

		$cuofec=date('d-m-Y',mktime(0,0,0,date('m'),(date('d')*1)+6,date('Y')));	

		pg_query("
			INSERT INTO cuotas VALUES (
			CURRVAL('creditos_crecod_seq'),
			1, $cuomon, '$cuofec', null, 0, null, null, null,
			NEXTVAL('cuotas_cuoid_seq')		
			);		
		");
		
	} else {
	
	for($i=0;$i<=$cuonro;$i++) {
	
		//$cuofec=date('d-m-Y',mktime(0,0,0,date('m')+$i));	
		
		if($i>0) {
				
			/*if((mktime(0,0,0,date('m')+$i,$diapago,date('Y')) - time()) < (86400*30)) {
				// Si la fecha de la primera cuota es menor a 30 días desde la creación del crédito
				// empieza al mes próximo...
				$offset_mes=1;
			}*/		
			
			$cuofec=date('d-m-Y',mktime(0,0,0,date('m')+$offset_mes+$i,$diapago,date('Y')));	

			pg_query("
			INSERT INTO cuotas VALUES (
			CURRVAL('creditos_crecod_seq'),
			$i, $cuomon, '$cuofec', null, 0, null, null, null,
			NEXTVAL('cuotas_cuoid_seq')		
			);		
			");
			
		} else {	
			
			$cuofec=date('d-m-Y',mktime(0,0,0,date('m'),date('d'),date('Y')));	

			pg_query("
			INSERT INTO cuotas VALUES (
			CURRVAL('creditos_crecod_seq'),
			0, $pie, '$cuofec', current_timestamp,
			$currval, 'P', $pie, null,
			NEXTVAL('cuotas_cuoid_seq')		
			);		
			");
			
		}
			

	}
	
	}
		
	}

	
	pg_query('COMMIT;');
	
	print(json_encode(array(true,$boletin['bolnum'])));

?>
