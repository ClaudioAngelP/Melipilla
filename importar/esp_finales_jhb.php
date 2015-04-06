<?php	require_once('../conectar_db.php');	error_reporting(E_ALL);

  $fi=explode("\n", utf8_decode(file_get_contents('esp_final.csv')));
  
  //pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
   
	 $esp_desc=trim(strtoupper($r[1]));
	 $esp_fono=trim($r[2]);
	 $esp_cod=trim($r[3]);
	 if($esp_desc=='') continue;
	 $chk=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='$esp_desc';");
	 
	 if($chk){
		 print("1<br>");
		 pg_query("UPDATE especialidades SET esp_fono='$esp_fono' WHERE esp_id=".$chk['esp_id'].";");
	 }else{
		 print("2<br>");
		 pg_query("INSERT INTO especialidades (esp_id,esp_desc,esp_fono) VALUES (DEFAULT,'$esp_desc','$esp_fono');");
		 print("INSERT INTO especialidades (esp_id,esp_desc,esp_fono) VALUES (DEFAULT,'$esp_desc','$esp_fono');<br>");
	 }
 	 
     
  }
  
  //pg_query("COMMIT");

?>
