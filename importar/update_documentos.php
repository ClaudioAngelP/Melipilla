<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  $act=0;
  
  $fi=explode("\n", utf8_decode(file_get_contents('items2.csv')));
  
  //pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
   
     $item_cod=trim(strtoupper($r[0]));
	  $item_glosa=trim(strtoupper($r[1]));
	  
	 
	  	pg_query("INSERT INTO item_presupuestario VALUES ('$item_cod','$item_glosa');");
	  	print("INSERT INTO item_presupuestario VALUES ('$item_cod','$item_glosa');<br>");
	
  }
  
  
  
  //pg_query("COMMIT");

?>
