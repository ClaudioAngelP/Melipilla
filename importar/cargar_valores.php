<?php

	require_once('../config.php');
  require_once('../conectores/sigh.php');
  
  error_reporting(E_ALL);
  
  $f=explode("\n", utf8_decode(file_get_contents('cargar_valores3.csv')));
  
  pg_query("START TRANSACTION;");
  $cont=1;
  $cnt=1;
  for($i=1;$i<sizeof($f);$i++) {
      
     $r=explode('|',$f[$i]);
      
      
     if(trim($r[0])!='')
     		$codigo=trim(strtoupper($r[0]));
     else
     		$codigo='';
     		
	  if(trim($r[2])!='')
     		$valor=trim($r[2]);
     else
     		$valor='';
     		
     $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$codigo';");
     
     
     if($art['art_val_ult']==0){ 
       
     $id=$art['art_id'];     
     
     if($id!=''){
     
     		pg_query("UPDATE articulo SET art_val_ult=$valor WHERE art_id=$id;
				");
		
			if($codigo!=''){
				echo "OK -> ".$codigo." --> [UPDATE articulo SET art_val_ult=$valor WHERE art_id=$id;] ".$cont++."
				<br><br>";
				flush();
			}
		}else{
				echo "NOK -> [".$codigo."]-> ".$r[1].".- no carg&oacute; [".$cnt++."] <br><br>";		
		}
		
		}
		
     
  }
  
 	pg_query("COMMIT");

?>
