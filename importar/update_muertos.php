<?php 

	error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  $fi=explode("\n", utf8_decode(file_get_contents('muertos.csv')));
  
 // pg_query("START TRANSACTION;");
  
 for($i=1;$i<sizeof($fi);$i++) {
      
	$r=explode('|',$fi[$i]);
     
     //if(!isset($r[0]) OR trim($r[0])=='') continue;
      
	$pac_rut=trim(strtoupper($r[0].'-'.$r[1]));
	print($pac_rut);
      		
	$pac=cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='$pac_rut';");
	
	print_r($pac);
	
	$inter=cargar_registros_obj("SELECT inter_id FROM interconsulta WHERE inter_pac_id=".$pac['pac_id']." AND (inter_fecha_salida is not null) AND (id_sidra is not null);");
	
	print_r($inter);
	
	$fecha_salida=trim($r[14]);
	
	print($fecha_salida);
	
	if($inter)
	for($j=0;$j<sizeof($inter);$j++){ 

		pg_query("UPDATE interconsulta SET inter_motivo_salida=9,inter_fecha_salida='$fecha_salida' WHERE inter_id=".$inter[$j]['inter_id'].";");
		pg_query("INSERT INTO mensajeria_integraciones VALUES(DEFAULT,3,".$inter[$j]['inter_id'].",CURRENT_TIMESTAMP);");
  		
  	}  
  		
  		flush();
 // pg_query("COMMIT");

}

?>
