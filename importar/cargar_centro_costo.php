<?php

	require_once('../conectar_db.php');
	
	$f=explode("\n", utf8_decode(file_get_contents('centros_costo.csv')));
  
	for($i=0;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=="") continue;
	
		$r=explode('|',$f[$i]);
		
		$cod1=trim($r[0]);
		$cod2=trim($r[1]);
		$nombre=ucwords(trim($r[2]));
    
		if(trim($r[3])!='')
      $nombre_winsig=strtoupper(trim($r[3]));
      
    if(trim($r[4])!='')
      $nombre_antiguo=strtoupper(trim($r[4]));
    
    if($cod2=='') {
      pg_query("INSERT INTO centro_costo VALUES ('.$cod1', '$nombre',false,true,false);");
      pg_query("UPDATE centro_costo SET centro_winsig='$nombre_winsig', centro_grupo='$nombre_antiguo' 
                WHERE centro_ruta='.$cod1'");
    }
    
    if($cod1=='') {
      pg_query("INSERT INTO centro_costo VALUES ('.$cod2', '$nombre',false,true,false);");
      pg_query("UPDATE centro_costo SET centro_winsig='$nombre_winsig', centro_grupo='$nombre_antiguo' 
                WHERE centro_ruta='.$cod2'");    
    }
    
  }

?>