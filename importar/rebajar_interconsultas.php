<?php 

	require_once('../config.php');
  	require_once('../conectores/sigh.php');
  
  	error_reporting(E_ALL);
  
  	$f=explode("\n", utf8_decode(file_get_contents('rebaja_interconsultas.csv')));
  
  	pg_query("START TRANSACTION;");
  		$si=1;
 		$no=1;
 		$html='';
  
	for($i=1;$i<sizeof($f);$i++) {
      
   	$r=explode('|',pg_escape_string(utf8_decode($f[$i])));
   	
   	if(trim($r[0])!=''){
   		$folio=trim($r[0]);
   		$inter=cargar_registro("SELECT inter_id FROM interconsulta WHERE inter_folio=$folio;");
   	}else{
   		
   		if(trim($r[1])!=''){
   			$rut=trim($r[1]);
   			$pac_id=cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='$rut';");
 			}else
 				$rut='';
 			
 			if(trim($r[2])!='')
 				$fecha=pg_escape_string(trim($r[2]));
 			else
 				$fecha='';
 			
 			if(trim($r[4])!=''){
 				$polid=trim($r[4]);
 				$poli=cargar_registro("SELECT esp_id FROM especialidades WHERE esp_desc ILIKE '%$polid%'");
 			}else
 				$poli='';
 		
 			
 			$inter=cargar_registro("SELECT inter_id FROM interconsulta 
 											WHERE inter_pac_id=".$pac_id['pac_id']." 
 											AND inter_fecha='$fecha'
 											AND inter_especialidad=".$poli['esp_id']
 											);
 		}		
 		
 		if(!$inter){
			//print("No carg&oacute; -> SELECT * FROM orden_atencion WHERE oa_pac_id=".$pac_id['pac_id']." AND oa_fecha='$fecha'; ".$no++."<br><br>");
			$html.="<tr><td>".$folio."</td><td>No carg&oacute;</td></tr>";
 		}else{
 		
			pg_query("UPDATE interconsulta SET inter_motivo_salida=1, inter_fecha_salida='$r[3]' WHERE inter_id=".$inter['inter_id'].";");
			//print("Si carg&oacute; -> UPDATE orden_atencion SET oa_motivo_salida=$r[5], oa_fecha_salida='$r[6]' WHERE oa_id=".$oa['oa_id']."; ".$si++."<br><br>"); 			
 			
 		}  
 		
 	}
 	print("<table>");
 		print($html);
 		print("</table>");
    
  
 	pg_query("COMMIT");

?>