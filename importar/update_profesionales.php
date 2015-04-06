<?php error_reporting(E_ALL); require_once('../conectar_db.php');
 
  $fi=explode("\n", utf8_decode(file_get_contents('profesionales_psiiq.csv')));
  
  pg_query("START TRANSACTION;");
    
  for($i=1;$i<sizeof($fi)-1;$i++) {
      
     $r=explode('|',$fi[$i]);
      
     $rut=trim(strtoupper($r[0]));
     $paterno=trim(strtoupper($r[1]));
     $materno=trim(strtoupper($r[2]));  
     $nombres=trim(strtoupper($r[3]));
     
     if($rut=='') continue;
     
	 $p=cargar_registro("SELECT doc_id FROM doctores WHERE doc_rut='$rut';");     
     
     if($p){
     	$prof_id=$p['doc_id'];
     	if(pg_query("UPDATE doctores SET doc_paterno='$paterno',doc_materno='$materno',doc_nombres='$nombres' WHERE doc_id=$prof_id;"))
     	print('Upd. OK <br>');
	 }else{
		if(pg_query("INSERT INTO doctores VALUES (DEFAULT,'$rut','$paterno','$materno','$nombres');"))
		print('Ins. OK <br>');
	 }
  } 
  pg_query("COMMIT");

?>
