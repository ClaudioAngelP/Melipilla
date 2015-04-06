<?php

	require_once('../config.php');
	require_once('../conectores/sigh.php');
  
	error_reporting(E_ALL);
  
  $f=explode("\n", utf8_decode(file_get_contents('cargar_lista_espera_hosp.csv')));
  
 //pg_query("START TRANSACTION;");
 
 $ok=0; $mal=0; $repetido=0;
  
  for($i=1;$i<sizeof($f);$i++) {

     $r=explode('|',$f[$i]);

     if(trim($r[3])!='')
     		$fecha_oa=trim($r[3]);
     else
     		$fecha_oa='01/01/2000';
     		
     $pac_rut=trim(strtoupper($r[0]));
     $pac_ficha=trim($r[1]);
   
     $pac_id=cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='$pac_rut' OR pac_ficha='$pac_ficha';");     
     $pac_id=$pac_id['pac_id']*1;
		
	  if(trim($r[8])!='')
     		$cod_prestacion=$r[8];
     else
     		$cod_prestacion='';
     		 		
     if(trim($r[7])!=''){
     		$diag_cod=$r[7];
     		$diagnostico=cargar_registro("SELECT diag_desc FROM diagnosticos WHERE diag_cod='$diag_cod' limit 1;");
     		$diagnostico=$diagnostico['diag_desc'];

	  }else if (trim($r[7])=='' && trim($r[14])!=''){
	  		$diag_cod='';
	  		$diagnostico=trim($r[14]);
	  }else{
	  		$diag_cod='';
	  		$diagnostico='';
     }
     		    

     if(trim($r[2])!=''){
     		$doct_rut=strtoupper(trim($r[2]));
     		$doct=cargar_registro("SELECT doc_id FROM doctores where upper(doc_rut)='$doct_rut';");
     		if($doct_id)
            		$doct_id=$doct['doc_id'];
     		else
     		        $doct_id=-7;
     }else{
     		$doct_id=-7;
     }

     if(trim($r[9])!=''){
		   $fecha_aten=trim($r[9]);
		   $fecha_salida="'".trim($r[9])."'";
	  }else{
	  		$fecha_aten='01/01/2000';
	  		$fecha_salida='null';
	  }
	    
	  if(trim($r[10])!='')
     		$motivo_salida=$r[10]*1;
     else
     		$motivo_salida=0;
     
	  
     if(trim($r[6])!='')
     		$centro_ruta=$r[6];
     else
     		$centro_ruta='';
     		
     if(trim($r[4])!='')
     		$prioridad=$r[4]*1;
     else
     		$prioridad=0;
 	
     if(trim($r[11])!='')
     		$ges=$r[11]*1;
     else
     		$ges=2;
     		
     if(trim($r[12])!='')		
     		$tipo_hosp=$r[12]*1;
     else
     		$tipo_hosp=3;
     		
     if(trim($r[5])!='')
     		$carpeta=$r[5];
     else
     		$carpeta=0;
     		
     if(trim($r[13])!='')
     		$observacion=pg_escape_string($r[13]);
     else
     		$observacion='';
     
     $chk=pg_query("SELECT * FROM orden_atencion WHERE 
					oa_fecha='$fecha_oa' AND 
					oa_pac_id=$pac_id AND 
					oa_carpeta_id=$carpeta AND 
					oa_diag_cod='$diag_cod' AND
					oa_codigo='$cod_prestacion';");
					
	/*if(pg_num_rows($chk)>0) {
		$mal++;
	} else {
		$ok++;.
	}*/

	print( number_format($i*100/sizeof($f),2,',','.')."% ... " );
	
	flush();
	
	if(pg_num_rows($chk)>0) { $repetido++; continue; }
     
     $res = pg_query("INSERT INTO orden_atencion VALUES (
		DEFAULT, 0, '$fecha_oa', ".$pac_id.", 
		$sgh_inst_id, $sgh_inst_id, 0, -1, 1, '', 
		'$cod_prestacion', '$diag_cod', -1, ".$doct_id.", '$fecha_aten', 0, NULL, 
		7, 0, $motivo_salida, '$centro_ruta', $prioridad,
		DEFAULT, '".$diagnostico."', '', '', NULL, $fecha_salida, 
		$ges, $tipo_hosp, $carpeta, '$observacion');
		");
	
	if (!$res)
		  echo "\n\n - MALO [$i] -> INSERT INTO orden_atencion VALUES (
		DEFAULT, 0, '$fecha_oa', ".$pac_id.", 
		$sgh_inst_id, $sgh_inst_id, 0, -1, 1, '', 
		'$cod_prestacion', '$diag_cod', -1, ".$doct_id.", '$fecha_aten', 0, NULL, 
		7, 0, $motivo_salida, '$centro_ruta', $prioridad, 
		DEFAULT, '".$diagnostico."', '', '', NULL, $fecha_salida, 
		$ges, $tipo_hosp, $carpeta, '$observacion');
		\n\n";
	
	if (!$res) $mal++; else $ok++;		
     
  }
  
  print("OK: $ok MAL: $mal REPETIDOS: $repetido");
  
  //pg_query("ROLLBACK;");

?>
