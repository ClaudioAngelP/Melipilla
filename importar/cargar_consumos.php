<?php 

	require_once('../conectar_db.php');
	
	$l=explode("\n",utf8_decode(file_get_contents("consumos.csv")));
	
	pg_query("START TRANSACTION;");
	
	$creados=0; $no_creados=0; $insertados=0;
	
	for($i=1;$i<sizeof($l);$i++) {
	
		if(trim($l[$i])=='') continue;
	
		$r=explode('|',$l[$i]);
				
		$nped=$r[4]*1;
		
		$cresp=trim($r[0]);
		$ccost=trim($r[1]);
		
		$search='.'.$cresp[0].'%'.$cresp[strlen($cresp)-1].'.'.$ccost[0].'%'.$ccost[strlen($ccost)-1];
		
		$ccost2=str_replace('.','_',$ccost);
		$ccost2=str_replace(',','_',$ccost2);
		
		$words=explode(' ',$ccost2);
		
		$ccost_search='';
		
		for($j=0;$j<sizeof($words);$j++) {
				if(strlen($words[$j])==0) continue;
				if(strlen($words[$j])>1)
					$ccost_search.=$words[$j][0].'%'.$words[$j][strlen($words[$j])-1].' ';
				else
					$ccost_search.=$words[$j][0].' ';
				
		}
		
		$ccost_search=trim($ccost_search);
		
		$centro_costo=cargar_registros_obj("SELECT * FROM centro_costo WHERE centro_ruta ILIKE '$search' AND centro_nombre ILIKE '$ccost_search';");
		
		if(!$centro_costo) {
			
			print("[L&iacute;nea ".($i+1)."] CENTRO DE COSTO ($cresp $ccost) NO COINCIDE.<br>$search $ccost_search<br>"); 
			
			continue;
		} 

		if(sizeof($centro_costo)>1) {
			
			$wccost=sizeof(explode(' ',$ccost));
			
			$ok=false;
			
			for($j=0;$j<sizeof($centro_costo);$j++) {
				$w2ccost=sizeof(explode(' ',$centro_costo[$j]['centro_nombre']));
				if($w2ccost==$wccost) { $es='SI'; $ok=true; $correcta=$centro_costo[$j]['centro_ruta']; }  else { $es='NO'; }
				//print($centro_costo[$j]['centro_ruta'].' '.$centro_costo[$j]['centro_nombre']." ($wccost $w2ccost ($es))<br>");
			}
		
						
			if(!$ok) {
			
				print("[L&iacute;nea ".($i+1)."] CENTRO DE COSTO ($cresp $ccost) TIENE MAS COINCIDENCIAS.<br>"); 

				for($j=0;$j<sizeof($centro_costo);$j++) {
					$w2ccost=sizeof(explode(' ',$centro_costo[$j]['centro_nombre']));
					if($w2ccost==$wccost) { $es='SI'; $ok=true; $correcta=$centro_costo[$j]['centro_ruta']; }  else { $es='NO'; }
					print($centro_costo[$j]['centro_ruta'].' '.$centro_costo[$j]['centro_nombre']." ($wccost $w2ccost ($es))<br>");
				}
				
				continue;
				
			}
			
			$centro_costo[0]['centro_ruta']=$correcta;
			
		} 
		
		$centro_ruta=$centro_costo[0]['centro_ruta'];
		
		$acod=$r[5]*1;
		
		$cod='ART'.str_repeat('0',6-strlen($acod)).$acod;
		
		$a=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
		
		if(!$a) { 
			
			print("[L&iacute;nea ".($i+1)."] ARTICULO ($cod) [".$r[6]."] NO EXISTE.<br>"); 
			
			/*if($i==1292 OR $i==1362 OR $i==1419)*/ continue; 
/*
SE SALTA ESTOS:
[Línea 1293] ARTICULO (ART002077) [CAMARA DE VIGILANCIA VIVOTEK IP 78361] NO EXISTE.
[Línea 1363] ARTICULO (ART002075) [COMPUTADOR LENOVO DESKTOP THINKCENTRE M90P] NO EXISTE.
[Línea 1420] ARTICULO (ART002076) [SOFTWARE DAMEWARE 10 USER] NO EXISTE.
			pg_query("INSERT INTO articulo VALUES (DEFAULT, '$cod', '1','".$r[11]."','".$r[11]."', 0, false, 0, false, '2201001',0,0,0,0,1,true,0,false);");
			
			print("ARTICULO CREADO...<br />");

			$a=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
		
			$art_id=$a['art_id']*1;
			
			pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT, $art_id, 1);");

 * */
						
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

		

		$fecha=str_replace('-','/',$r[3]);
		
		$cantidad=str_replace(',','.',$r[7])*1;
				
		$chk=cargar_registro("SELECT * FROM logs JOIN cargo_centro_costo USING (log_id) WHERE log_tipo=18 AND log_fecha='$fecha' AND centro_ruta='$centro_ruta';");
		
		if(!$chk) {
		
			pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 18, '$fecha', 0, null, null, 'CARGA MASIVA CONSUMOS (SEIS ".date('d/m/Y H:i:s').")', null, 1);");
			pg_query("INSERT INTO cargo_centro_costo VALUES (CURRVAL('logs_log_id_seq'), '$centro_ruta');");
			
			$log_id="CURRVAL('logs_log_id_seq')";
			
			$creados++;
		
		} else {
			
			$log_id=$chk['log_id'];
			
			$no_creados++;
			
		}
		
		$subtotal=0;
		
		/*
		
		CREATE OR REPLACE FUNCTION rebajar_articulos(bigint, bigint, bigint, numeric, timestamp without time zone)
		
		*/
		
		pg_query("SELECT rebajar_articulos($art_id, 1, $log_id, $cantidad, '$fecha'::timestamp without time zone);");
		
		$insertados++;
		
	
	}


	print("CREADOS $creados ... NO CREADOS $no_creados ... INSERTADOS $insertados");

	pg_query("COMMIT;");

?>
