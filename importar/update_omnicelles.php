<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  $fi=explode("\n", utf8_decode(file_get_contents('omnicelles.csv')));
  
  pg_query("START TRANSACTION;");
    
 for($i=1;$i<sizeof($fi);$i++) {
      
	$r=explode('|',$fi[$i]);
     
     //if(!isset($r[0]) OR trim($r[0])=='') continue;
      
	$art_codigo=trim($r[1]);
	$bodega=trim($r[2]);
	
	$art=cargar_registro("SELECT art_id FROM articulo WHERE art_codigo='$art_codigo';");
	
	if($art){
		$art_id=$art['art_id'];
		$chk=cargar_registro("SELECT artb_id FROM articulo_bodega 
							WHERE artb_art_id=$art_id
							AND artb_bod_id=$bodega");
		if(!$chk){
			pg_query("INSERT INTO articulo_bodega VALUES(DEFAULT,$art_id,$bodega)");
			print("$bodega, $art_codigo. OK!<br>");		
		}
		
	}else{
		print("$bodega, $art_codigo. NO EXISTE!<br>");
	}
		
	flush();
}

 	 pg_query("COMMIT");
?>
