<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  $fi=explode("\n", utf8_decode(file_get_contents('adquirientes.csv')));

 pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     	$adq_rut=trim($r[0]);
     	$adq_paterno=pg_escape_string(trim(utf8_decode($r[1])));
		$adq_materno=pg_escape_string(trim(utf8_decode($r[2])));
		$adq_nombres=pg_escape_string(trim(utf8_decode($r[3])));
		
		pg_query("UPDATE receta_adquiriente SET adq_nombres='$adq_nombres', adq_appat='$adq_paterno', adq_apmat='$adq_materno' WHERE adq_rut='$adq_rut';");

	}
		
    flush();
	 
	pg_query("COMMIT;");

?>
