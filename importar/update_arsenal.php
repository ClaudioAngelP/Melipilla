<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  $act=0;
  
  $fi=explode("\n", utf8_decode(file_get_contents('art_arsenal.csv')));
  
  //pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
   
     $codigo=trim(strtoupper($r[1]));
	 
 	 pg_query("UPDATE articulo SET art_arsenal=true where art_codigo='$codigo';");
 	
 
  }
  
  
  //pg_query("COMMIT");

?>
