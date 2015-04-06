<?php 

	error_reporting(E_ALL);

  	require_once('../conectar_db.php');
 
  	$fi=explode("\n", utf8_decode(file_get_contents('convenioshgf.csv')));
  
  	pg_query("START TRANSACTION;");
  
  	for($i=1;$i<sizeof($fi);$i++) {
      
    	$r=explode('|',$fi[$i]);
    	
    	$incognito=trim($r[0]);
    	$prov_rut=str_replace('.','',trim($r[1])); //Proveedor (Procesar)
  /*OK*/$convenio_id_licitacion=strtoupper(trim($r[2])); //convenio_licitacion
  /*OK*/$convenio_nombre=pg_escape_string(strtoupper(trim($r[3]))); //convenio_nombre 
  /*OK*/$convenio_nro_res_aprueba_bases=trim($r[4]); //convenio_nro_res_aprueba 
  /*OK*/$fecha_res_aprueba_bases="'".trim($r[5])."'"; //convenio_fecha_aprueba
  /*OK*/$convenio_nro_res_adjudica=trim($r[6]); //convenio_nro_res_adjudica
  /*OK*/$fecha_res_adjudica="'".trim($r[7])."'"; //convenio_fecha_adjudica
    	$convenio_nro_res_modificatoria=trim($r[8]); //convenio_nro_res_modificatoria
    	$fecha_res_modificatoria="'".trim($r[9])."'"; //convenio_fecha_modificatoria
  /*OK*/$convenio_nro_res_prorroga=trim($r[10]); //convenio_nro_res_prorroga
  /*OK*/$fecha_res_prorroga="'".trim($r[11])."'"; //convenio_fecha_prorroga
  /*OK*/$convenio_nro_res_aumento=trim($r[12]); //convenio_nro_res_aumento
  /*OK*/$fecha_res_aumento="'".trim($r[13])."'"; //convenio_fecha_aumento
  /*OK*/$convenio_nro_res_aprueba_contrato=trim($r[14]); //convenio_nro_res_contrato
  /*OK*/$fecha_res_aprueba_contrato="'".trim($r[15])."'"; //convenio_fecha_resolucion --?
    	$prov_nombre=strtoupper(trim($r[16]));
    	$convenio_nro_res_aprueba_addendum=trim($r[17]);
    	$fecha_res_aprueba_addendum="'".trim($r[18])."'";
    	$administrador_contrato=trim($r[19]); //Admin contrato (Procesar) $func_id
  /*OK*/$convenio_mails=pg_escape_string(trim($r[20])); //convenio_mails 
  /*OK*/$convenio_monto=str_replace('.','',trim($r[21])); //convenio_monto 
  /*OK*/$convenio_plazo=trim($r[22]); //convenio_plazo 
  /*OK*/$convenio_fecha_inicio="'".trim($r[23])."'"; //convenio_fecha_inicio
  /*OK*/$convenio_fecha_final="'".trim($r[24])."'"; //convenio_fecha_final
  /*OK*/$convenio_nro_boleta=trim($r[25]); //convenio_nro_boleta
  /*OK*/$convenio_fecha_boleta="'".trim($r[26]."'"); //convenio_fecha_boleta
  /*OK*/$convenio_banco_boleta=trim($r[27]); //convenio_banco_boleta
  /*OK*/$convenio_monto_boleta=str_replace('.','',trim($r[28])); //convenio_monto_boleta
  /*OK*/$convenio_descripcion=trim($r[29]); //convenio_multa
  /*OK*/$convenio_comentarios=pg_escape_string(trim($r[30])); //convenio_comentarios
		$convenio_codigo_int=trim($r[31]); //codigo_int	
		
		$prov=cargar_registro("SELECT prov_id FROM proveedor WHERE prov_rut='$prov_rut';");
		
		if($prov) $prov_id=$prov['prov_id']; else $prov_id='null';
		if(!$convenio_monto) $convenio_monto='null';
		if(!$convenio_plazo) $convenio_plazo='null';
		if(!$convenio_monto_boleta) $convenio_monto_boleta='null';
		if($fecha_res_aprueba_bases=="''") $fecha_res_aprueba_bases='null';
		if($fecha_res_adjudica=="''") $fecha_res_adjudica='null';
		if($fecha_res_modificatoria=="''") $fecha_res_modificatoria='null';
		if($fecha_res_prorroga=="''") $fecha_res_prorroga='null';
		if($fecha_res_aumento=="''") $fecha_res_aumento='null';
		if($fecha_res_aprueba_contrato=="''") $fecha_res_aprueba_contrato='null';
		if($fecha_res_aprueba_addendum=="''") $fecha_res_aprueba_addendum='null';
		if($convenio_fecha_inicio=="''") $convenio_fecha_inicio='null';
		if($convenio_fecha_final=="''") $convenio_fecha_final='null';
		if($convenio_fecha_boleta=="''") $convenio_fecha_boleta='null';	

		pg_query("
				INSERT INTO convenio VALUES(DEFAULT,'$convenio_nombre',$prov_id,$convenio_monto,$convenio_plazo,
				'$convenio_mails',$convenio_fecha_inicio,$convenio_fecha_final,'$convenio_id_licitacion',
				'$convenio_nro_res_aprueba_bases','$convenio_nro_res_adjudica','$convenio_nro_res_aprueba_contrato',
				$fecha_res_aprueba_contrato,null,'$convenio_nro_boleta','$convenio_banco_boleta',$convenio_fecha_boleta,
				$convenio_monto_boleta,'$convenio_descripcion','$convenio_comentarios',$fecha_res_aprueba_bases,
				$fecha_res_adjudica,'$convenio_nro_res_prorroga',$fecha_res_prorroga,'$convenio_nro_res_aumento',
				$fecha_res_aumento);
			");	
		    	 
    	
    	
     
  	}
  	pg_query("COMMIT;");

  
?>
