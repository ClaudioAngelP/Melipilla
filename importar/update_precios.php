<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
 
  $fi=explode("\n", utf8_decode(file_get_contents('precios.csv')));
  
  pg_query("START TRANSACTION;");
  $updt=0;
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
      
     $art_codigo=trim($r[0]);
     $art_glosa=trim(strtoupper($r[1]));
     $art_precio=trim(str_replace(',','.',$r[2]));  
     
	 //Verifica si existe
	 if($art_precio==''){
	 	print(($i+1).".- ".$art_codigo." Tiene Precio.<br>");
	 	continue;
	}
	
	 $art=cargar_registro("SELECT art_id,art_val_ult FROM articulo WHERE art_codigo='$art_codigo'");     
     
     if($art){//Si existe entra
     	$art_id=$art['art_id'];
     	
     	if($art['art_val_ult']=='' OR $art['art_val_ult']==0){//Modifica Último Valor
     		pg_query("UPDATE articulo SET art_val_ult=$art_precio WHERE art_id=$art_id;");
     		print(($i+1).".- <b>".$art_codigo." - Update: ".$art['art_val_ult']." <> ".$art_precio."</b><br>");
     		$updt++;
		}else{
			print(($i+1).".- ".$art_codigo." - Ignore: ".$art['art_val_ult']." <> ".$art_precio."<br>");
		}
	}
	//print($art_glosa." --> ".$art_unidad_adm." ".$art_unidad_cantidad."[OK]<br>");	
  } 
  print("TOTAL UPDATES: ".$updt); 
  pg_query("COMMIT");

?>
