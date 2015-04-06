<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  $act=0;
  
  $fi=explode("\n", utf8_decode(file_get_contents('esp_ubicacion.csv')));
  
  //pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
   
     $esp_id=trim(strtoupper($r[0]));
	  $esp_lugar=trim(strtoupper($r[1]));
	 
 	 pg_query("UPDATE especialidades SET esp_lugar='$esp_lugar' WHERE esp_codigo_int='$esp_id';");
 	 print("UPDATE especialidades SET esp_lugar='$esp_lugar' WHERE esp_codigo_int='$esp_id';<br>");
     
  }
  
  $fo=explode("\n", utf8_decode(file_get_contents('esp_especialidad.csv')));
  
  //pg_query("START TRANSACTION;");
  
  for($e=1;$e<sizeof($fo);$e++) {
      
     $t=explode('|',$fo[$e]);
   
     $esp_id=trim(strtoupper($t[0]));
	  $esp_cod_especialidad=trim(strtoupper($t[1]));
	  $esp_nombre_especialidad=trim(strtoupper($t[2]));
 
 	 pg_query("UPDATE especialidades SET esp_cod_especialidad='$esp_cod_especialidad', esp_nombre_especialidad='$esp_nombre_especialidad' WHERE esp_codigo_int='$esp_id';");
 	 print("UPDATE especialidades SET esp_cod_especialidad='$esp_cod_especialidad', esp_nombre_especialidad='$esp_nombre_especialidad' WHERE esp_codigo_int='$esp_id';<br>");
     
  }
  
  //pg_query("COMMIT");

?>
