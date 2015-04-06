<?php 

	require_once('../../conectar_db.php');
	require_once('../../num2texto.php');
	require_once('../../fpdf/fpdf.php');
	
	function trunc($str, $len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}

	$aqc_id=$_GET['aqc_id']*1;
	

	
	$vmes[1]='Enero';
	$vmes[2]='Febrero';
	$vmes[3]='Marzo';
	$vmes[4]='Abril';
	$vmes[5]='Mayo';
	$vmes[6]='Junio';
	$vmes[7]='Julio';
	$vmes[8]='Agosto';
	$vmes[9]='Septiembre';
	$vmes[10]='Octubre';
	$vmes[11]='Noviembre';
	$vmes[12]='Diciembre';
	
	//$ac=cargar_registro("SELECT * FROM apertura_cajas JOIN funcionario USING (func_id) WHERE ac_id=".$ac_id);

	$aqc=cargar_registro("SELECT * FROM arqueo_cajas JOIN funcionario USING (func_id) WHERE aqc_id=$aqc_id");
	
	class PDF extends FPDF {
		function header() {
		
			GLOBAL $aqc_id, $aqc;

			$this->SetFont('Arial','BU', 18);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('../../imagenes/logo_caja.png',10,15,40,25);

			$this->SetY(20);	
			$this->Cell(200,7,utf8_decode('CONSOLIDADO DE RECAUDACIÓN #'.number_format($aqc_id,0,',','.')),0,0,'C');	

			$this->Ln();
			
			$this->SetY(40);	
			$this->SetFont('Arial','B', 16);

        		$this->Cell(70,10,utf8_decode('Fecha Consolidado:'),0,0,'R');
		        $this->Cell(50,10,substr($aqc['aqc_fecha'],0,16),0,0,'L');
		        //$this->Cell(50,10,utf8_decode('Fecha Cierre:'),0,0,'R');
		        //$this->Cell(50,10,substr($ac['ac_fecha_cierre'],0,16),0,0,'L');
		        $this->Ln();

			$this->SetFontSize(10);

		
		}

		function footer() {
		
			$this->SetY(300);
				
			$this->SetFont('','',10);
			$this->Cell(200,6,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	

		function footer() {

			GLOBAL $pdf, $aqc;

				$pdf->Ln(10);
				$pdf->Ln(10);
			
				$pdf->SetFontSize(12);
				$pdf->SetFont('','BU');	
				$pdf->Cell(100,6,formato_rut($aqc['func_rut']).' '.$aqc['func_nombre'],0,0,'C');	
				$pdf->SetFont('','B');	
				$pdf->Cell(100,6,'__________________________',0,0,'C');
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(100,6,'Firma Funcionario',0,0,'C');	
				$pdf->Cell(100,6,'Finanzas',0,0,'C');
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->SetFontSize(10);
				
		}
		
		function texto_fecha($str) {
		
			GLOBAL $vmes;
		
			$ff=explode('/',$str);
			
			return $ff[0].' de '.$vmes[$ff[1]*1].' del '.$ff[2];
		}

	
	$pdf=new PDF('P','mm','Legal');
	$pdf->AliasNbPages();
	
	$pdf->SetAutoPageBreak(true,70);
	
	$pdf->AddPage();
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(60,6,'R.U.N.',1,0,'C');	
	$pdf->Cell(140,6,'Nombre Funcionario',1,0,'C');	
	$pdf->Ln();
	
	$pdf->Cell(60,7,formato_rut($aqc['func_rut']),1,0,'R');
	$pdf->Cell(140,7,$aqc['func_nombre'],1,0,'L');
	$pdf->Ln();
	
	$pdf->SetFontSize(10);	

	$pdf->SetFont('Arial','');
	
	$pdf->Ln();
	
	$pdf->SetFillColor(130,130,130);

	$pdf->SetFont('Arial','B', 13);
	
	$pdf->Cell(200,7,'Detalle Monto Recaudado',1,0,'C');
	
	$pdf->SetFont('Arial','', 10);
	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);

	$pdf->SetFontSize(8);	
	
	$pdf->Cell(50,5,'Total General',1,0,'C');	
	$pdf->Cell(50,5,'Total Efectivo',1,0,'C');	
	$pdf->Cell(50,5,'Total Cheque(s)',1,0,'C');	
	$pdf->Cell(50,5,'Total Otros Medios de Pago',1,0,'C');	
	$pdf->Ln();	

	$pdf->SetFontSize(16);	

	$ac_ids="SELECT ac_id FROM arqueo_cajas_detalle WHERE aqc_id=$aqc_id";
	
	$ac_cantidad=cargar_registro("SELECT count(ac_id)::numeric as cant FROM arqueo_cajas_detalle WHERE aqc_id=$aqc_id"); 

	$efe3=cargar_registros_obj("SELECT *, boletines.bolnum AS realbolnum, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre, (SELECT SUM(monto) FROM cheques WHERE cheques.bolnum=boletines.bolnum) AS total_cheques, (SELECT SUM(monto) FROM forma_pago WHERE forma_pago.bolnum=boletines.bolnum) AS total_fpago FROM boletines LEFT JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) ORDER BY boletines.bolnum");
	$efe_dev=cargar_registros_obj(" SELECT * FROM devolucion_boletines 	left join boletines on devolucion_boletines.bolnum=boletines.bolnum 	LEFT JOIN pacientes USING (pac_id) 	WHERE devolucion_boletines.bolnum IN (SELECT bolnum FROM apertura_cajas JOIN devolucion_boletines ON dev_ejecuta BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND devolucion_boletines.func_id_ejecuta=apertura_cajas.func_id WHERE apertura_cajas.ac_id in ($ac_ids)) ORDER BY boletines.bolnum");
	$tot_dev=cargar_registros_obj(" SELECT sum(monto_total)as monto_total FROM devolucion_boletines 	left join boletines on devolucion_boletines.bolnum=boletines.bolnum 	LEFT JOIN pacientes USING (pac_id) 	WHERE devolucion_boletines.bolnum IN (SELECT bolnum FROM apertura_cajas JOIN devolucion_boletines ON dev_ejecuta BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND devolucion_boletines.func_id_ejecuta=apertura_cajas.func_id WHERE apertura_cajas.ac_id in ($ac_ids)) ");
	
	
	
	$efe2=cargar_registros_obj("SELECT * FROM caja_detalle WHERE ac_id IN ($ac_ids) ORDER BY cd_tipo DESC");
	$efe=cargar_registros_obj("SELECT cd_tipo, SUM(cd_monto) AS cd_monto FROM caja_detalle WHERE ac_id IN ($ac_ids) GROUP BY cd_tipo ORDER BY cd_tipo DESC");
	$tmp=cargar_registro("SELECT SUM(cd_monto) AS total FROM caja_detalle WHERE ac_id IN ($ac_ids);");
	
	$total_efectivo=$tmp['total']*1-50000*$ac_cantidad['cant']*1;
	
	$chq2=cargar_registros_obj("SELECT COALESCE(COUNT(*),0) AS cnt FROM cheques JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion=''");
	$chq=cargar_registros_obj("SELECT *, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM cheques JOIN boletines USING (bolnum) JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion=''");
	$total_chq=cargar_registros_obj("SELECT COALESCE(SUM(monto),0) AS total FROM cheques JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion=''");
	$total_chq=$total_chq[0]['total']*1;

	$pag2=cargar_registros_obj("SELECT fpago_nombre, COUNT(*) AS cnt, SUM(monto) AS total FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut='61953200-7' GROUP BY fpago_nombre");
	$pag4=cargar_registros_obj("SELECT fpago_nombre, COUNT(*) AS cnt, SUM(monto) AS total FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut!='61953200-7' GROUP BY fpago_nombre");
	
	$pag=cargar_registros_obj("SELECT *, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut='61953200-7'");
	$pag5=cargar_registros_obj("SELECT *, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut!='61953200-7'");
	
	$total_pag=cargar_registros_obj("SELECT SUM(monto) AS total FROM forma_pago JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut='61953200-7'");
	$total_pag=$total_pag[0]['total']*1;
	
	$total_pag2=cargar_registros_obj("SELECT SUM(monto) AS total FROM forma_pago JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND anulacion='' AND rut!='61953200-7'");
	$total_pag2=$total_pag2[0]['total']*1;

	$pagares=cargar_registros_obj("SELECT COUNT(*) AS cnt, SUM(cretot) AS total FROM boletines JOIN creditos USING (crecod) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND pagare AND anulacion=''");
	$detalle_pagares=cargar_registros_obj("SELECT *, boletines.bolnum AS realbolnum, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM boletines JOIN pacientes USING (pac_id) JOIN creditos USING (crecod) join cuotas on cuotas.crecod=creditos.crecod AND cuonum=1 WHERE boletines.bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id IN ($ac_ids)) AND pagare");
	
	
	

	$total=$total_efectivo+$total_chq+$total_pag;
	
	$pago_conve=cargar_registros_obj("

select bdet_codigo,numboleta,bolmon,(doc_nombres ||' '|| doc_paterno ||' '|| doc_materno)as nombre_doc,
(CASE WHEN bdet_codigo ilike '%DP000%' THEN 
			bdet_valor
		ELSE 
		
		ceil( bdet_valor*(select porc_crs from codigos_prestacion_convenio where bdet_codigo=codigo and upper(rut)=convenios and tipo='mle' limit 1) /100)
		END) as montoCRS,
		(CASE WHEN bdet_codigo ilike '%DP000%' THEN 
			0
		ELSE 
		 floor(bdet_valor*(select porc_doc from codigos_prestacion_convenio where bdet_codigo=codigo and upper(rut)=convenios and tipo='mle' limit 1) /100)
		END) as montoMED,upper(rut)as rut,boletines.bolnum,bolfec
		 from apertura_cajas
	 
JOIN boletines ON boletines.func_id=apertura_cajas.func_id AND boletines.anulacion='' AND boletines.pagare=FALSE AND  bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre

join boletin_detalle on boletines.bolnum=boletin_detalle.bolnum 
join forma_pago on forma_pago.bolnum=boletin_detalle.bolnum 
join doctores on rut=doc_rut

where tipo=4  and bdet_codigo in (select distinct codigo from codigos_prestacion_convenio) and rut!='61953200-7' and  
 apertura_cajas.ac_id  in ($ac_ids)
order by bolfec asc  ");
	
	$cuentas_pre=cargar_registros_obj("


select sum((CASE WHEN (COALESCE((prop)::TEXT, '') = '') THEN  (CASE WHEN dif>0  THEN dif ELSE copago END) ELSE prop END)) as copago,
(cuenta_numero)as cuenta,item_nombre
from (

select* from (
SELECT distinct boletin_detalle.bdet_id,boletines.bolmod as modalidad,
item_nombre,tipo_prestacion,seg_id,cuenta_numero,cuenta_item,cuenta_contable,
centro_nombre,bdet_codigo,
boletines.bolnum as numero,bdet_valor*bdet_cantidad as copago,
ROUND(boletines.bolmon*(bdet_valor*bdet_cantidad/proval)) as prop,
ROUND(boletin_detalle.bdet_valor-forma_pago.monto) as dif,
sum(bdet_valor_total*bdet_cantidad) as MontoTotal,(CASE WHEN COALESCE(nomd_tipo_atencion, '') = '' THEN 'M' ELSE nomd_tipo_atencion END) AS nomd_tipo_atencion,
(CASE WHEN COALESCE(tipo::TEXT, '') = '' THEN 0::SMALLINT ELSE tipo END) AS tipo
FROM apertura_cajas 
JOIN boletines ON boletines.func_id=apertura_cajas.func_id AND boletines.anulacion='' AND boletines.pagare=FALSE AND  bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre
left join creditos on creditos.crecod=boletines.crecod
left join cuotas on cuotas.crecod=boletines.crecod AND cuonum=0
left join boletines as b2 on b2.bolnum=cuotas.bolnum
join boletin_detalle on (boletin_detalle.bolnum=cuotas.bolnum or boletin_detalle.bolnum=boletines.bolnum )
left join seguros on seguros.bolnum = boletines.bolnum 
left join forma_pago on forma_pago.bolnum=boletin_detalle.bolnum and forma_pago.bdet_id=boletin_detalle.bdet_id
left join nomina_detalle on  nomd_id=bdet_presta_id
left join centro_costo on centro_ruta=(select esp_centro_ruta from especialidades where esp_id=(select nom_esp_id from nomina where nomina.nom_id=nomina_detalle.nom_id))
left join codigos_prestacion_item ON (
CASE WHEN seg_id >0 THEN 
	((CASE WHEN char_length(boletin_detalle.bdet_codigo)>10 THEN 
			codigo='FARMACIA_CODIGO' 
		ELSE 
			boletin_detalle.bdet_codigo ilike codigos_prestacion_item.codigo || '%' 
		END) 
	AND tipo_prestacion=(select lol from
		(select distinct seg_tipo,
			CASE 
			    WHEN seg_tipo=1 THEN '5' 
			    WHEN seg_tipo=2 THEN '6' 
			    WHEN seg_tipo=3 THEN '6' 
			ELSE 
				'1'
			END as lol 
		 from seguros 
		  where seguros.bolnum=boletines.bolnum or seguros.bolnum=b2.bolnum) as foo))
 ELSE (CASE WHEN (CASE WHEN (boletines.prvcod='12' OR b2.prvcod='12' OR boletines.prvcod='5' OR b2.prvcod='5' OR boletines.prvcod='6' OR b2.prvcod='6' or boletines.bolmod='mle' or  b2.bolmod='mle') 
		  THEN
		        FALSE
		  ELSE
			(CASE WHEN COALESCE(modalidad, '') = ''THEN b2.bolmod='mai' ELSE  modalidad='mai' END)
		  END) 
	THEN 
	   (CASE WHEN char_length(boletin_detalle.bdet_codigo)>10 
	    THEN 
		codigo='FARMACIA_CODIGO' 
	    ELSE 
		boletin_detalle.bdet_codigo ilike codigos_prestacion_item.codigo || '%' 
	    END) AND  tipo_prestacion='1'  
		
       ELSE
	   (CASE WHEN (boletines.prvcod='12' OR b2.prvcod='12' OR boletines.prvcod='6' OR b2.prvcod='6')
	   THEN  
		(CASE WHEN char_length(boletin_detalle.bdet_codigo)>10
	         THEN 
		   codigo='FARMACIA_CODIGO' 
	       ELSE 
		   boletin_detalle.bdet_codigo ilike codigos_prestacion_item.codigo || '%' 
	       END) AND tipo_prestacion='3'
	   ELSE 
		(CASE WHEN (boletines.prvcod='5' OR b2.prvcod='5')
		THEN 
			(CASE WHEN char_length(boletin_detalle.bdet_codigo)>10
			THEN 
				codigo='FARMACIA_CODIGO' 
			ELSE 
				boletin_detalle.bdet_codigo ilike codigos_prestacion_item.codigo || '%' 
			END) AND tipo_prestacion='4'
		ELSE
			(CASE WHEN (CASE WHEN COALESCE(modalidad, '') = ''THEN b2.bolmod='mle' ELSE  modalidad='mle' END)
			THEN 
				(CASE WHEN char_length(boletin_detalle.bdet_codigo)>10
				THEN 
					codigo='FARMACIA_CODIGO' 
				ELSE 
					boletin_detalle.bdet_codigo ilike codigos_prestacion_item.codigo || '%' 
				END) AND codigos_prestacion_item.modalidad=boletines.bolmod and tipo_prestacion='2'
			END)
		END)
			
	  END)
       END)
END) 

WHERE
 apertura_cajas.ac_id  in ($ac_ids)
AND COALESCE(nomd_tipo_atencion, '') NOT IN ('PC052','PC053')


  GROUP BY 

boletines.bolfec,boletin_detalle.bdet_valor,
boletines.bolobs,boletines.bolmod,boletines.bolnum,boletines.crecod,boletines.pagare,boletines.garantia,boletines.bolnumx,
apertura_cajas.ac_id,boletin_detalle.bdet_codigo,codigos_prestacion_item.codigo,codigos_prestacion_item.item_nombre,tipo_prestacion,
seguros.seg_id,codigos_prestacion_item.cuenta_numero,centro_nombre,cuenta_item,cuenta_contable,boletines.bolmon,proval,bdet_cantidad,boletin_detalle.bdet_id,nomd_tipo_atencion,tipo,
forma_pago.monto


order by numero,boletines.bolnum) as foo where (CASE WHEN dif >0 THEN TRUE ELSE tipo not in (1,2,3,4) END)
)as foo2 group by foo2.cuenta_numero,foo2.cuenta_item,foo2.cuenta_contable,foo2.item_nombre


");


	$pdf->Cell(50,7,'$'.number_format($total,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_efectivo,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_pag,0,',','.').'.-',1,0,'R');	
	$pdf->Ln();

	
	if($efe) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(40,5,'Efectivo',1,0,'C');	
		$pdf->Cell(40,5,'Moneda/Billetes',1,0,'C');	
		$pdf->Cell(40,5,'Cantidad',1,0,'C');	
		$pdf->Cell(80,5,'Monto ($)',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($efe);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(40,5,'$'.number_format($efe[$i]['cd_tipo'],0,',','.').'.-',1,0,'R');	
			$pdf->Cell(40,5,$efe[$i]['cd_monto']/$efe[$i]['cd_tipo'],1,0,'R');	
			$pdf->Cell(80,5,'$'.number_format($efe[$i]['cd_monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Ln();	
		
	}

	// Datos de Cheques	
	
	/*if($chq) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(40,5,'Cheque(s)',1,0,'C');	
		$pdf->Cell(40,5,'Banco',1,0,'C');	
		$pdf->Cell(40,5,'Serie',1,0,'C');	
		$pdf->Cell(40,5,'Fecha',1,0,'C');	
		$pdf->Cell(40,5,'Monto',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($chq);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(40,5,$chq[$i]['banco'],1,0,'L');	
			$pdf->Cell(40,5,$chq[$i]['serie'],1,0,'L');	
			$pdf->Cell(40,5,$chq[$i]['fecha'],1,0,'C');	
			$pdf->Cell(40,5,'$'.number_format($chq[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 
	
	}*/

	if($chq2) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(100,5,utf8_decode('Número de Cheques'),1,0,'C');	
		$pdf->Cell(100,5,'Valor Total Cheques',1,0,'C');	
		$pdf->Ln();	

		
		$pdf->Cell(100,5,$chq2[0]['cnt'],1,0,'C');	
		$pdf->Cell(100,5,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	
		$pdf->Ln();	
	
	}

	// Otras Formas de Pago

	/*if($pag) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(40,5,'Otros Medios Pago',1,0,'C');	
		$pdf->Cell(40,5,'Tipo Pago',1,0,'C');	
		$pdf->Cell(40,5,utf8_decode('Número'),1,0,'C');	
		$pdf->Cell(40,5,'Fecha',1,0,'C');	
		$pdf->Cell(40,5,'Monto',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(40,5,$pag[$i]['fpago_nombre'],1,0,'L');	
			$pdf->Cell(40,5,$pag[$i]['numero'],1,0,'C');	
			$pdf->Cell(40,5,$pag[$i]['fecha'],1,0,'C');	
			$pdf->Cell(40,5,'$'.number_format($pag[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

	
	}*/

	if($pag2) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(75,5,'Prestador',1,0,'C');	
		$pdf->Cell(50,5,'N Bonos',1,0,'C');	
		$pdf->Cell(75,5,'Total',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag2);$i++) {
		
			$pdf->Cell(75,5,'CRS',1,0,'L');	//$pag2[$i]['fpago_nombre']
			$pdf->Cell(50,5,$pag2[$i]['cnt'],1,0,'C');	
			$pdf->Cell(75,5,'$'.number_format($pag2[$i]['total'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Ln();	
	
	}
	
	$pdf->SetFontSize(12);	

	$pdf->Ln();	
	
	if($pagares) {

		$pdf->Cell(100,5,utf8_decode('Número de Pagarés'),1,0,'C');	
		$pdf->Cell(100,5,utf8_decode('Total Pagarés'),1,0,'C');	
		$pdf->Ln();	

		
		$pdf->Cell(100,5,$pagares[0]['cnt'],1,0,'C');	
		$pdf->Cell(100,5,'$'.number_format($pagares[0]['total']*1,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	
		$pdf->Ln();	
	
	}
	
	$tmpx=$pdf->GetX();
	$tmpy=$pdf->GetY();
	$pdf->SetX($tmpx+100);
	
	$pdf->SetFontSize(10);	
	$pdf->SetFont('Arial','');	
	$pdf->Multicell(100,5,utf8_decode(num2texto($tot_dev[0]['monto_total']*-1)).' PESOS.',1,'C');

	$h=$pdf->GetY()-$tmpy;
	
	$pdf->SetXY($tmpx,$tmpy);
	
	$pdf->Cell(50,$h,'Devoluciones:',1,0,'R');
	
	$pdf->SetFontSize(14);	
	$pdf->SetFont('Arial','B');	
	$pdf->Cell(50,$h,'$ '.number_format($tot_dev[0]['monto_total']*-1,0,',','.').'.-',1,0,'C');

	$pdf->Ln();
	
	
	$tmpx=$pdf->GetX();
	$tmpy=$pdf->GetY();
	$pdf->SetX($tmpx+100);
	
	$pdf->SetFontSize(10);	
	$pdf->SetFont('Arial','');	
	$pdf->Multicell(100,5,utf8_decode(num2texto($total)).' PESOS.',1,'C');

	$h=$pdf->GetY()-$tmpy;
	
	$pdf->SetXY($tmpx,$tmpy);
	
	$pdf->Cell(50,$h,'Total Recaudado:',1,0,'R',1);
	
	$pdf->SetFontSize(14);	
	$pdf->SetFont('Arial','B');	
	$pdf->Cell(50,$h,'$ '.number_format($total,0,',','.').'.-',1,0,'C');

	$pdf->Ln();
	$pdf->Ln();
	
	if($pag4) {

		$pdf->SetFontSize(12);	
	$pdf->SetFont('Arial','');

		$pdf->Cell(75,5,'Prestador',1,0,'C');	
		$pdf->Cell(50,5,'N Bonos',1,0,'C');	
		$pdf->Cell(75,5,'Total',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag4);$i++) {
		
			$pdf->Cell(75,5,'CONV. MEDS.',1,0,'L');	//$pag2[$i]['fpago_nombre']
			$pdf->Cell(50,5,$pag4[$i]['cnt'],1,0,'C');	
			$pdf->Cell(75,5,'$'.number_format($pag4[$i]['total'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Ln();	
	
	}

	footer();

	$pdf->AddPage();

	$pdf->SetFillColor(130,130,130);

    $pdf->SetFont('Arial','B', 13);

    $pdf->Cell(200,7,utf8_decode('Detalle Recaudación'),1,1,'C');

	$pdf->SetFillColor(200,200,200);

	/*$p=cargar_registros_obj("SELECT * FROM boletin_detalle WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id AND anulacion='') ORDER BY bolnum, bdet_id");

	$pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(25,5,utf8_decode('Fecha y Hora'),1,0,'C');
        $pdf->Cell(20,5,utf8_decode('Código'),1,0,'C');
        $pdf->Cell(90,5,utf8_decode('Descripción'),1,0,'C');
        $pdf->Cell(5,5,utf8_decode('#'),1,0,'C');
        $pdf->Cell(30,5,'Valor ($)',1,0,'C');
        $pdf->Cell(30,5,'Copago ($)',1,0,'C');
        $pdf->Ln();

        $pdf->SetFillColor(200,200,200);

        $totalt=0; $totalb=0;

        //if(!$paga_cuota) {

                if($p)
                for($i=0;$i<sizeof($p);$i++) {

                        $pdf->SetFontSize(8);
                        $pdf->SetFont('','');
                        $pdf->Cell(25,4,substr(trim($p[$i]['bdet_fecha']),0,16),1,0,'C');
                        $pdf->SetFontSize(8);
                        $pdf->SetFont('','B');
                        $pdf->Cell(20,4,trim($p[$i]['bdet_codigo']),1,0,'C');
                        $pdf->SetFont('','');
                        $pdf->SetFontSize(8);
                        $pdf->Cell(90,4,trunc(trim($p[$i]['bdet_prod_nombre']),45),1,0,'L');
                        $pdf->Cell(5,4,trim($p[$i]['bdet_cantidad']),1,0,'C');
                        $pdf->SetFontSize(10);

                                $pdf->Cell(30,4,'$ '.number_format($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
                                if($p[$i]['bdet_cobro']=='S') {
                                        $pdf->Cell(30,4,'$ '.number_format($p[$i]['bdet_valor']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
                                } else {
                                        $pdf->Cell(30,4,'$ '.number_format(0,0,',','.').'.-',1,0,'R');
                                }
                                $pdf->SetFontSize(8);
                                $pdf->Ln();

                                if($p[$i]['bdet_cobro']=='S') {
                                        $totalt+=round($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad']);
                                        $totalb+=round($p[$i]['bdet_valor']*$p[$i]['bdet_cantidad']);
                                }
                }

	*/

	
	if($efe3) {

		$pdf->Ln();	

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Efectivo',1,0,'C');	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C');	
		$pdf->Cell(25,5,'RUT Pac.',1,0,'C');	
		$pdf->Cell(90,5,'Nombre Pac.',1,0,'C');	
		$pdf->Cell(45,5,'Monto',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($efe3);$i++) {
			
			if($efe3[$i]['bolmon']*1==0) {
				
			} else {
				
				if(($efe3[$i]['bolmon']*1)-($efe3[$i]['total_cheques']*1)-($efe3[$i]['total_fpago']*1)==0)
				{
					
				}else{
				
					if($efe3[$i]['anulacion']=='') {
						$anula=''; $fill=0;
					} else {
						$anula=' (A) '; $fill=1;
					}
				
					$pdf->Cell(20,5,'#'.($i+1),1,0,'C');	
					$pdf->Cell(20,5,$efe3[$i]['realbolnum'].$anula,1,0,'C',$fill);	
					if(($efe3[$i]['pac_rut']+'')==''){
						$identifica_efe3=$efe3[$i]['id_sidra']	;
					
					}else{
						$identifica_efe3=formato_rut($efe3[$i]['pac_rut'])	;
					
					}
					$pdf->Cell(25,5,$identifica_efe3,1,0,'R',$fill);	
					$pdf->Cell(90,5,trunc($efe3[$i]['pac_nombre'],40),1,0,'L',$fill);
					$pdf->Cell(45,5,$anula.'$'.number_format(($efe3[$i]['bolmon']*1)-($efe3[$i]['total_cheques']*1)-($efe3[$i]['total_fpago']*1),0,',','.').'.-',1,0,'R',$fill);
					$pdf->Ln();	
				}
			}
		}
		
	
		
	}

$pdf->Cell(155,5,'Total Ingresos:',1,0,'R',1);	
		$pdf->Cell(45,5,'$'.number_format($total_efectivo-$tot_dev[0]['monto_total']*1,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	

		$pdf->Ln();


if($efe_dev) {

		/*$pdf->Ln();	

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Efectivo',1,0,'C');	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C');	
		$pdf->Cell(25,5,'RUT Pac.',1,0,'C');	
		$pdf->Cell(90,5,'Nombre Pac.',1,0,'C');	
		$pdf->Cell(45,5,'Monto',1,0,'C');	
		$pdf->Ln();	*/

		for($i=0;$i<sizeof($efe_dev);$i++) {
		
			
				$anula=' (D) '; $fill=1;
			
			
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(20,5,$efe_dev[$i]['devol_id'].$anula,1,0,'C',$fill);
			if(($efe_dev[$i]['pac_rut']+'')==''){
				$identifica_efe3=$efe_dev[$i]['id_sidra']	;
			
			}else{
				$identifica_efe3=formato_rut($efe_dev[$i]['pac_rut'])	;
			
			}
			$pdf->Cell(25,5,$identifica_efe3,1,0,'R',$fill);	
			$pdf->Cell(90,5,trunc($efe_dev[$i]['pac_nombres'],40),1,0,'L',$fill);
			$pdf->Cell(45,5,$anula.'$'.number_format(($efe_dev[$i]['monto_total']*1),0,',','.').'.-',1,0,'R',$fill);
			$pdf->Ln();	
			
		}
		
		
			$pdf->Cell(155,5,'Total Devoluciones:',1,0,'R',1);	
		$pdf->Cell(45,5,'$'.number_format($tot_dev[0]['monto_total']*1,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	
		
		

		$pdf->Ln();	
	}

		$pdf->Cell(155,5,'Total Efectivo:',1,0,'R',1);	
		$pdf->Cell(45,5,'$'.number_format($total_efectivo,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	
		$pdf->Ln();
	
	if($chq) {

		$pdf->Ln();	

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Cheque(s)',1,0,'C');	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C');	
		$pdf->Cell(25,5,'RUT Pac.',1,0,'C');	
		$pdf->Cell(40,5,'Nombre Pac.',1,0,'C');	
		$pdf->Cell(35,5,'Banco',1,0,'C');	
		$pdf->Cell(20,5,'Serie',1,0,'C');	
		$pdf->Cell(20,5,'Fecha',1,0,'C');	
		$pdf->Cell(20,5,'Monto',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($chq);$i++) {
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(20,5,$chq[$i]['bolnum'],1,0,'C');	
			$pdf->Cell(25,5,formato_rut($chq[$i]['rut']),1,0,'R');	
			$pdf->Cell(40,5,trunc($chq[$i]['nombre'],20),1,0,'L');	
			$pdf->Cell(35,5,$chq[$i]['banco'],1,0,'L');	
			$pdf->Cell(20,5,$chq[$i]['serie'],1,0,'L');	
			$pdf->Cell(20,5,$chq[$i]['fecha'],1,0,'C');	
			$pdf->Cell(20,5,'$'.number_format($chq[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		}
		
		$pdf->Cell(160,5,'Total:',1,0,'R',1);	
		$pdf->Cell(40,5,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	

		$pdf->Ln();	
		
	}
	
	
	if($pag) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(25,5,'Bonos CRS',1,0,'C');	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C');	
		$pdf->Cell(25,5,utf8_decode('R.U.T.'),1,0,'C');	
		$pdf->Cell(55,5,'Nombre',1,0,'C');	
		$pdf->Cell(25,5,'Tipo Pago',1,0,'C');	
		$pdf->Cell(25,5,utf8_decode('Número'),1,0,'C');	
		//$pdf->Cell(30,5,'Fecha',1,0,'C');	
		$pdf->Cell(25,5,'Monto',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag);$i++) {
		
			$pdf->Cell(25,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(20,5,$pag[$i]['bolnum'],1,0,'C');
			if(($pag[$i]['pac_rut']+'')==''){
				$identifica_pag=$pag[$i]['id_sidra']	;
			
			}else{
				$identifica_pag=formato_rut($pag[$i]['pac_rut'])	;
			
			}	
			$pdf->Cell(25,5,$identifica_pag,1,0,'R');	
			$pdf->Cell(55,5,trunc($pag[$i]['pac_nombre'],20),1,0,'L');	
			$pdf->Cell(25,5,$pag[$i]['fpago_nombre'],1,0,'L');	
			$pdf->Cell(25,5,$pag[$i]['numero'],1,0,'C');	
			//$pdf->Cell(30,5,$pag[$i]['fecha'],1,0,'C');	
			$pdf->Cell(25,5,'$'.number_format($pag[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Cell(160,5,'Total:',1,0,'R');	
		$pdf->Cell(40,5,'$'.number_format($total_pag,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	

		$pdf->Ln();	

	
	}
	
	if($pago_conve) {
		
		$pdf->SetFillColor(130,130,130);

		$pdf->SetFont('Arial','B', 13);
	
		$pdf->Cell(200,7,'Detalle Profesional en convenio',1,0,'C');	

		$pdf->Ln();
		
		$pdf->SetFontSize(10);	
		
		
		$pdf->Cell(50,5,'Bonos MEDS',1,0,'C');	
		$pdf->Cell(25,5,utf8_decode('R.U.T.'),1,0,'C');	
		$pdf->Cell(20,5,'N Boleta',1,0,'C');
		$pdf->Cell(20,5,'N Boletin',1,0,'C');	
		
		$pdf->Cell(20,5,'Fecha',1,0,'C');	
		$pdf->Cell(25,5,'Monto Total',1,0,'C');	
		$pdf->Cell(20,5,'% MED',1,0,'C');	
		$pdf->Cell(20,5,'% CRS',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pago_conve);$i++) {
		
			
			$pdf->Cell(50,5,substr(trim($pago_conve[$i]['nombre_doc']),0,30),1,0,'C');
			
			$pdf->Cell(25,5,formato_rut($pago_conve[$i]['rut']),1,0,'R');	
			$pdf->Cell(20,5,trunc($pago_conve[$i]['numboleta'],20),1,0,'L');	
			$pdf->Cell(20,5,$pago_conve[$i]['bolnum'],1,0,'L');	
			$pdf->Cell(20,5,substr(trim($pago_conve[$i]['bolfec']),0,10),1,0,'C');
			//$pdf->Cell(30,5,$pag[$i]['fecha'],1,0,'C');	
			$pdf->Cell(25,5,'$'.number_format($pago_conve[$i]['bolmon'],0,',','.').'.-',1,0,'R');
			$pdf->Cell(20,5,'$'.number_format($pago_conve[$i]['montomed'],0,',','.').'.-',1,0,'R');
			$pdf->Cell(20,5,'$'.number_format($pago_conve[$i]['montocrs'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Cell(160,5,'Total:',1,0,'R');	
		$pdf->Cell(40,5,'$'.number_format($total_pag2,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	

		$pdf->Ln();	

	
	}

////cuentas presupuestarias
		$pdf->AddPage();
	$pdf->SetFillColor(130,130,130);

    $pdf->SetFont('Arial','B', 13);

    $pdf->Cell(200,7,utf8_decode('Cuentas Presupuestarias'),1,1,'C');
	$pdf->SetFillColor(200,200,200);
		$pdf->SetFontSize(10);	

		//$pdf->Cell(20,5,utf8_decode('Cuentas Presupuestarias'),1,0,'C');	
			
		$pdf->Ln();	
		$pdf->Cell(70,5,'Item Nombre',1,0,'C');	
		$pdf->Cell(80,5,'Cuenta',1,0,'C');	
	/*	$pdf->Cell(30,5,'Item Nombre',1,0,'C');	
		$pdf->Cell(28,5,'Cod. Prestacion',1,0,'C');	
		$pdf->Cell(30,5,'Recaudador',1,0,'C');	
		$pdf->Cell(25,5,'Centro Costo',1,0,'C');	
	 * */
		$pdf->Cell(50,5,'Monto',1,0,'C');	
		$pdf->Ln();	
		$total_cuenta=0;
		for($i=0;$i<sizeof($cuentas_pre);$i++) {
			$total_cuenta=$cuentas_pre[$i]['copago']*1+$total_cuenta;
			/*if($cuentas_pre[$i]['centro_nombre']=='')
			{
				$centro_n='Carga Manual.';
			}else{
				$centro_n=$cuentas_pre[$i]['centro_nombre'];
			}
			
			 * */
			  $pdf->Cell(70,5,$cuentas_pre[$i]['item_nombre'],1,0,'C');
			$pdf->Cell(80,5,$cuentas_pre[$i]['cuenta'],1,0,'C');	
				/*
				  $pdf->Cell(30,5,substr(trim($cuentas_pre[$i]['item_nombre']),0,14),1,0,'C');		
			$pdf->Cell(28,5,$cuentas_pre[$i]['bdet_codigo'],1,0,'C');	
			$pdf->Cell(30,5,$cuentas_pre[$i]['cajera'],1,0,'C');	
			$pdf->Cell(25,5,substr(trim($centro_n),0,14),1,0,'C');	 
				 
				 */
			$pdf->Cell(50,5,'$'.number_format($cuentas_pre[$i]['copago'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 
		$pdf->Ln();
		$pdf->Cell(150,5,'Total Cuentas Presupuestarias',1,0,'C');
		$pdf->Cell(50,5,'$'.number_format($total_cuenta,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	
		
		$pdf->Ln();	
	

	if($detalle_pagares) {

		$pdf->AddPage();
	
		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,utf8_decode('Pagarés'),1,0,'C');	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C');	
		$pdf->Cell(30,5,utf8_decode('R.U.T.'),1,0,'C');	
		$pdf->Cell(60,5,'Nombre',1,0,'C');	
		$pdf->Cell(35,5,'Monto',1,0,'C');	
		$pdf->Cell(35,5,'Fecha Venc.',1,0,'C');	
		$pdf->Ln();	

		for($i=0;$i<sizeof($detalle_pagares);$i++) {

			if($detalle_pagares[$i]['anulacion']=='') {
				$anula=''; $fill=0;
			} else {
				$anula=' (A) '; $fill=1;
			}
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C');	
			$pdf->Cell(20,5,$detalle_pagares[$i]['realbolnum'].$anula,1,0,'C',$fill);
			if(($detalle_pagares[$i]['pac_rut']+'')==''){
				$identifica_detPag=$detalle_pagares[$i]['id_sidra']	;
			
			}else{
				$identifica_detPag=formato_rut($detalle_pagares[$i]['pac_rut'])	;
			
			}	
			$pdf->Cell(30,5,$identifica_detPag,1,0,'R',$fill);	
			$pdf->Cell(60,5,trunc($detalle_pagares[$i]['pac_nombre'],20),1,0,'L',$fill);	
			$pdf->Cell(35,5,$anula.'$'.number_format($detalle_pagares[$i]['cretot'],0,',','.').'.-',1,0,'R',$fill);
			$pdf->Cell(35,5,substr($detalle_pagares[$i]['cuofec'],0,10),1,0,'C',$fill);	
			$pdf->Ln();	
			
		} 

		$pdf->Cell(130,5,'Total:',1,0,'R',1);	
		$pdf->Cell(35,5,'$'.number_format($pagares[0]['total'],0,',','.').'.-',1,0,'R',1);
		$pdf->Cell(35,5,'',1,0,'R',1);
		$pdf->Ln();	
		
		$pdf->Ln();	
	
	}
	

	$pdf->Output('CIERRE_CAJA_'.$ac_id.'.pdf','I');	

?>
