<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
 
  $fi=explode("\n", utf8_decode(file_get_contents('artshgf.csv')));
  
  pg_query("START TRANSACTION;");
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
      
     $art_codigo=trim($r[0]);
     $art_fusion=trim(strtoupper($r[1]));
     $art_glosa=trim(strtoupper($r[2]));
	 $art_bodega=trim($r[3]);
	 $art_activado=trim(strtoupper($r[4]));     
       
	 //Verifica si existe
	 $art=cargar_registro("SELECT art_id FROM articulo WHERE art_codigo='$art_codigo'");
     
     if($art){//Si existe entra
     	$art_id=$art['art_id'];

     	if($art_activado=='S') $art_activado='true'; else $art_activado='false';
     	
     	//Modifica Nombres y Estado 
     	pg_query("UPDATE articulo SET art_glosa='$art_glosa',art_nombre='$art_glosa',art_activado='$art_activado' WHERE art_id=$art_id;");

     		if($art_fusion){
     		//Fusiona Registros Históricos
     			$art_ant=cargar_registro("SELECT art_id FROM articulo WHERE art_codigo='$art_fusion'");
     			$art_id2=$art_ant['art_id'];
     			pg_query("UPDATE pedido_detalle SET art_id=$art_id WHERE art_id=$art_id2");
     			pg_query("UPDATE orden_detalle SET ordetalle_art_id=$art_id WHERE ordetalle_art_id=$art_id2");
     			pg_query("UPDATE recetas_detalle SET recetad_art_id=$art_id WHERE recetad_art_id=$art_id2");
				pg_query("UPDATE stock SET stock_art_id=$art_id WHERE stock_art_id=$art_id2");
			}
		pg_query("DELETE FROM articulo_bodega WHERE artb_art_id=$art_id");
		
		if($art_activado=='true'){
			pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$art_bodega)");
			pg_query("DELETE FROM stock_critico WHERE critico_art_id=$art_id AND critico_bod_id!=$art_bodega");
			pg_query("DELETE FROM stock WHERE stock_art_id=$art_id AND stock_bod_id!=$art_bodega");
		}else{
			pg_query("DELETE FROM stock_critico WHERE critico_art_id=$art_id");
			pg_query("DELETE FROM stock WHERE stock_art_id=$art_id");
		}
			
		
				
	}
	//print($art_glosa." --> ".$art_unidad_adm." ".$art_unidad_cantidad."[OK]<br>");	
  }  
  pg_query("COMMIT");

?>
