<?php 

	require_once('../conectar_db.php');
	
	$l=explode("\n",utf8_decode(file_get_contents("recepciones.csv")));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($l);$i++) {
	
		if(trim($l[$i])=='') continue;
	
		$r=explode('|',$l[$i]);
				
		$ndoc=$r[6]*1;
		
		switch(strtoupper(trim($r[5]))) {
			
			case 'GUIA DESPACHO': $tdoc=0; break;
			case 'FACTURA IVA': $tdoc=1; break;
			case 'BOLETA': $tdoc=2; break;
			default: continue; break;
			
		}
		
		$prut=strtoupper(str_replace('.','',trim($r[7])));
		
		$p=cargar_registro("SELECT * FROM proveedor WHERE prov_rut='$prut'");
		
		if(!$p) { print("[L&iacute;nea ".($i+1)."] PROVEEDOR $prut NO EXISTE.<br>"); continue; }
		
		$prov_id=$p['prov_id']*1;
		
		
		
		
		$acod=$r[10]*1;
		
		$cod='ART'.str_repeat('0',6-strlen($acod)).$acod;
		
		$a=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
		
		if(!$a) { 
			
			print("[L&iacute;nea ".($i+1)."] ARTICULO ($cod) [".$r[11]."] NO EXISTE.<br>"); 
			
			if($i==1292 OR $i==1362 OR $i==1419) continue; 
/*
SE SALTA ESTOS:
[Línea 1293] ARTICULO (ART002077) [CAMARA DE VIGILANCIA VIVOTEK IP 78361] NO EXISTE.
[Línea 1363] ARTICULO (ART002075) [COMPUTADOR LENOVO DESKTOP THINKCENTRE M90P] NO EXISTE.
[Línea 1420] ARTICULO (ART002076) [SOFTWARE DAMEWARE 10 USER] NO EXISTE.
 * */
			pg_query("INSERT INTO articulo VALUES (DEFAULT, '$cod', '1','".$r[11]."','".$r[11]."', 0, false, 0, false, '2201001',0,0,0,0,1,true,0,false);");
			
			print("ARTICULO CREADO...<br />");

			$a=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
		
			$art_id=$a['art_id']*1;
			
			pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT, $art_id, 1);");
						
		} 
		
		$art_id=$a['art_id']*1;
		
		$tmp_vence=str_replace('-','/',trim($r[17]));
		
		if($tmp_vence=='') {
			if($a['art_vence']=='1')
				$vence="'01/01/2030'";
			else
				$vence='null';
		} else {
			if($a['art_vence']=='0')
				$vence="null";
			else
				$vence="'$tmp_vence'";			
		}

		

		$fecha=str_replace('-','/',$r[0]);
		
		$cantidad=str_replace(',','.',$r[13])*1;
		$punit=str_replace(',','.',$r[14])*1;
		$subtotal=$cantidad*$punit;
				
		$chk=cargar_registro("SELECT * FROM documento JOIN logs ON log_doc_id=doc_id WHERE doc_prov_id=$prov_id AND doc_tipo=$tdoc AND doc_num=$ndoc;");
		
		if(!$chk) {
		
			pg_query("INSERT INTO documento VALUES (DEFAULT, $prov_id, $tdoc, $ndoc, 1.19, 0, 0, '', '', null, '$fecha');");
		
			pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 1, '$fecha', 0, CURRVAL('documento_doc_id_seq'), null, 'CARGA MASIVA RECEPCIONES (SEIS ".date('d/m/Y H:i:s').")', null, 1);");
			
			$log_id="CURRVAL('logs_log_id_seq')";
		
		} else {
			
			$log_id=$chk['log_id'];
			
		}
		
		pg_query("INSERT INTO stock VALUES (DEFAULT, $art_id, 1, $cantidad, $log_id, $vence, $subtotal);");
		
		
	
	}


	pg_query("COMMIT;");

?>
