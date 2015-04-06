<?php error_reporting(E_ALL); require_once('../conectar_db.php');

  $fi=explode("\n", utf8_decode(file_get_contents('perecibles.csv')));
  $ignorados=0;
  $ok=0;
  $lista="";
  pg_query("START TRANSACTION;");
  
  for($i=1;$i<(sizeof($fi)-1);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     if(!isset($r[0]) OR trim($r[0])==''){ 
		 $lista.="[".$r[0]."] ".$r[1]."<br>";
		 $ignorados++;
		continue;		 
	 }
           
      $artcod=explode('-',$r[0]);
      
     $art_codigo='ART0'.$artcod[1];
     
     $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
     
     if(!$art) { 
		 $ignorados++;
		 $lista.="[".$r[0]."] ".$r[1]."<br>";
		continue;		 
	 }
     
     $art_id=$art['art_id']*1; 	
					   
	 if(pg_query("UPDATE articulo SET art_vence='1' WHERE art_id=$art_id;")) $ok++;
            
  }
  
  echo "(".(sizeof($fi)-1).") $ok OK <b>$ignorados</b> ARTICULOS IGNORADOS EN LA CARGA...<br>";
  echo $lista;
  
  pg_query("COMMIT");

?>
