<?php 

	require_once('../config.php');
 	require_once('../conectores/sigh.php');
  
  error_reporting(E_ALL);
  
  $f=explode("\n", utf8_decode(file_get_contents('control_patologias.csv')));
  
 // pg_query("START TRANSACTION;");
  
  for($i=1;$i<1000;$i++) {
      
     $r=explode('|',$f[$i]);
      
     $nombre=$r[1];
     $tipo=$r[2];
     
     if($r[0]!=''){
     pg_query("INSERT INTO control_patologias VALUES (
		DEFAULT, '$nombre', '$tipo')
		");
		
		echo "INSERT INTO control_patologias VALUES (
		DEFAULT, '$nombre', '$tipo')
		<br><br>";
		flush();}
		
     
  }
  
 //pg_query("COMMIT");

?>
