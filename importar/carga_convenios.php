<?php 

	require_once("../config.php");
	require_once("../conectores/sigh.php");
	
	$c=explode("\n", utf8_decode(file_get_contents("convenios.csv")));
	
	for($i=1;$i<sizeof($c);$i++) {
	
		$r=explode('|',$c[$i]);
	
		$nombre=$r[2];
		$prov_rut=str_replace(',','',$r[3]);
		$prov_rut=str_replace('.','',$prov_rut);
		$prov_rut=trim($prov_rut.'-'.$r[4]);
		
		$tmp=cargar_registro("SELECT * FROM proveedor WHERE prov_rut='$prov_rut'");
		
		if(!$tmp) continue;
		
		$prov_id=$tmp['prov_id']*1;
		
		$monto=str_replace(',','',$r[8]);
		$monto=str_replace('.','',$monto);
		$monto=str_replace('$','',$monto);
		$monto=trim(str_replace(' ','',$monto))*1;
		
		$plazo=0;
		
		$licitacion=strtoupper(trim($r[6]));
		
		$nro_res=$c[7];
		
		$finit=str_replace('-','/',$r[14]);
		$ffin=str_replace('-','/',$r[15]);
		
		$fres=str_replace('-','/',$r[13]);
		
		$mails='';
		
		$func_id=7;
		
		$chk=cargar_registro("SELECT * FROM convenio WHERE prov_id=$prov_id AND convenio_licitacion='$licitacion'");
		
		if(!$chk) {
			pg_query("
			INSERT INTO convenio VALUES (
				DEFAULT, '$nombre', $prov_id, $monto, $plazo, '$mails', '$finit', '$ffin', '$licitacion', 
				'$nro_res'
			);
			");
			pg_query("UPDATE convenio SET func_id=7 WHERE convenio_id=CURRVAL('convenio_convenio_id_seq');");
			$convenio_id="CURRVAL('convenio_convenio_id_seq')";
		} else {
			$convenio_id=$chk['convenio_id']*1;
		}
		
		$art_codigo=$r[9];
		
		$tmp=cargar_registro("SELECT * FROM articulo WHERE art_codigo ILIKE '%$art_codigo';");
		
		if(!$tmp) continue;
		
		$art_id=$tmp['art_id']*1;
		
		$monto=str_replace(',','',$r[11]);
		$monto=str_replace('.','',$monto);
		$monto=str_replace('$','',$monto);
		$monto=trim(str_replace(' ','',$monto))*1;
		
		$chk2=cargar_registros_obj("SELECT * FROM convenio_detalle WHERE art_id=$art_id AND convenio_id=$convenio_id");
		
		if(!$chk2)
			pg_query("INSERT INTO convenio_detalle VALUES ($convenio_id, $art_id, DEFAULT, $monto);");
	
	}

?>
