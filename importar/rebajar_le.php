<?php 

	require_once('../config.php');
  	require_once('../conectores/sigh.php');
  
  	error_reporting(E_ALL);
  
  	$f=explode("\n", utf8_decode(file_get_contents('rebaja_le_cerrada.csv')));
  
  	pg_query("START TRANSACTION;");
  		$si=1;
 		$no=1;
 		//$html='';
  
	for($i=1;$i<sizeof($f);$i++) {
      
   	$r=explode('|',pg_escape_string(utf8_decode($f[$i])));
   	
   	if(trim($r[0])!=''){
   		$rut=trim($r[0]);
   		$pac_id=cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='$rut';");
   	}else
   		$rut='';
   		
   	if(trim($r[4])!='')
   		$fecha=trim($r[4]);
 		else
 			$fecha='';
 							
 			
 		$oa=cargar_registro("SELECT oa_id FROM orden_atencion WHERE oa_pac_id=".$pac_id['pac_id']." AND oa_fecha='$fecha';");
 		
 		if(!$oa){
			print("No carg&oacute; -> SELECT * FROM orden_atencion WHERE oa_pac_id=".$pac_id['pac_id']." AND oa_fecha='$fecha'; ".$no++."<br><br>");
			$html.="<tr><td>".$rut."</td><td>No carg&oacute;</td></tr>";
 		}else{
 		
			pg_query("UPDATE orden_atencion SET oa_motivo_salida=$r[5], oa_fecha_salida='$r[6]' WHERE oa_id=".$oa['oa_id'].";");
			print("Si carg&oacute; -> UPDATE orden_atencion SET oa_motivo_salida=$r[5], oa_fecha_salida='$r[6]' WHERE oa_id=".$oa['oa_id']."; ".$si++."<br><br>"); 			
 			
 		}  
 		
 	}
 		print("<table>");
 		print($html);
 		print("</table>");
    
  
 	pg_query("COMMIT");

?>