<?php

  require_once('../../conectar_db.php');
  
  $autf_id=$_POST['autf_id']*1;
  
  $autf_nombre = pg_escape_string(strtoupper(trim(utf8_decode($_POST['nombre_autorizacion']))));
  
  $valida=isset($_POST['autf_validar'])?'true':'false';
  $pat_ges = "";
  
  if($autf_id==0) {
	  
	  if(isset($_POST['pat_ges']) && $_POST['pat_ges']!="") $pat_ges=", '".utf8_decode($_POST['pat_ges'])."'";
	  
	  pg_query($conn,
	  "
		  INSERT INTO autorizacion_farmacos VALUES (
		  DEFAULT,
		  '$autf_nombre',
		  $valida
		  $pat_ges
		  );
	  ");
  
	} else {
		
		if(isset($_POST['pat_ges'])) $pat_ges=", autf_patologia_ges='".utf8_decode($_POST['pat_ges'])."'";
		
		pg_query("
		UPDATE autorizacion_farmacos 
		SET autf_nombre='$autf_nombre', autf_validar=$valida
		$pat_ges
		WHERE autf_id=$autf_id
		");
		
	}
  
  print('1');

?>
