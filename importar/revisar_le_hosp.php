<?php 

	require_once('../config.php');
  	require_once('../conectores/sigh.php');
  
 	error_reporting(E_ALL);
  
  	$f=explode("\n", utf8_decode(file_get_contents('cargar_lista_espera_hosp.csv')));
  
	pg_query("START TRANSACTION;");
 
 	$sin=0;
 	$no=0;
 	$si=0;
  
  	for($i=1;$i<sizeof($f);$i++) {
      
    	$r=explode('|',$f[$i]);
        
     	if(trim($r[3])!='')
     		$fecha_oa=trim($r[3]);
     	else
     		$fecha_oa='';
     		
     	$pac_rut=trim(strtoupper($r[0]));
     	$pac_ficha=trim($r[1]);
   
  	   	$pac_id=cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='".$pac_rut."' OR pac_ficha='".$pac_ficha."';");     
		
	  	if(trim($r[8])!='')
     		$cod_prestacion=$r[8];
     	else
     		$cod_prestacion='';
     		 		
     	if(trim($r[7])!=''){		
     		$diag_cod=$r[7];
     		$diagnostico=cargar_registro("SELECT diag_desc FROM diagnosticos WHERE diag_cod='$diag_cod' limit 1;");
	  	}else{
	  		$diag_cod='';
	  		$diagnostico='';    		
     	}
     		    
     	if(trim($r[2])!=''){
     		$doct_rut=trim($r[2]);
     		$doct_id=cargar_registro("SELECT doc_id FROM doctores where doc_rut='$doct_rut';");
     	}else{
     		$doct_id=-7;
    	}	
     		
     	if(trim($r[9])!=''){
		   $fecha_aten=trim($r[9]);
		   $fecha_salida=trim($r[9]);
	  	}else{
	  		$fecha_aten='01/01/01';
	  		$fecha_salida='01/01/01';
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
     
    /* pg_query("INSERT INTO orden_atencion VALUES (
		DEFAULT, 0, '$fecha_oa', ".$pac_id['pac_id'].", 
		$sgh_inst_id, $sgh_inst_id, 0, -1, 1, '', 
		'$cod_prestacion', '$diag_cod', -1, ".$doct_id['doc_id'].", '$fecha_aten', 0, NULL, 
		7, 0, $motivo_salida, '$centro_ruta', $prioridad, 
		DEFAULT, '".$diagnostico['diag_desc']."', '', '', NULL, '$fecha_salida', 
		$ges, $tipo_hosp, $carpeta, '$observacion');
		");
		*/	
		
		//echo "pac_bd: ".$pac_id['pac_id']." - rut_f: ".$pac_rut." ficha_f: ".$pac_ficha."<br><br>";		
		
		if($pac_id==''){
			if($pac_rut)
				print("L: ".($i+1)." -> RUT [".$pac_rut."] Sin pac_id.<br>");
			else
				print("L: ".($i+1)." -> Ficha [".$pac_ficha."] Sin pac_id.<br>");
			
			//print("SELECT pac_id FROM pacientes WHERE pac_rut='".$pac_rut."' OR pac_ficha='".$pac_ficha."';<br>");
 			$sin++;
 			$res='';
		}else{		
			$res=pg_query("SELECT * FROM orden_atencion 
							WHERE oa_pac_id=".$pac_id['pac_id']." AND oa_fecha='$fecha_oa' 
							AND oa_carpeta_id=$carpeta AND oa_diag_cod='$diag_cod';");
			$si++;
		}
		
		if($res==''){
			if($pac_rut)
				print("L: ".($i+1)." -> RUT [".$pac_rut."] No encontrada en la Base de datos (No Carg&oacute;).<br><br>");
			else
				print("L: ".($i+1)." -> Ficha [".$pac_ficha."] No encontrada en la Base de datos (No Carg&oacute;).<br><br>");
			
			
			$no++;
		}
 		
 	/*		
 		echo "INSERT INTO orden_atencion VALUES (
		DEFAULT, 0, '$fecha_oa', ".$pac_id['pac_id'].", 
		$sgh_inst_id, $sgh_inst_id, 0, -1, 1, '', 
		'$cod_prestacion', '$diag_cod', -1, ".$doct_id['doc_id'].", '$fecha_aten', 0, NULL, 
		7, 0, $motivo_salida, '$centro_ruta', $prioridad, 
		DEFAULT, '".$diagnostico['diag_desc']."', '', '', NULL, '$fecha_salida', 
		$ges, $tipo_hosp, $carpeta, '$observacion');
		<br><br>";
	*/	
		flush();

  	}
  
  	print("<br><br>No cargaron ".($no)." - Cargadas ".($si)."<br><br>");
  	print("Sin PAC_ID ".$sin."");
  
 	pg_query("COMMIT");

?>