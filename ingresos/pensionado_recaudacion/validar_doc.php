<?php	require_once('../../conectar_db.php');
  
	$doc = strtoupper(trim($_POST['doctor']));
	//print($doc);
	$chk = cargar_registro("SELECT doc_rut,doc_id FROM doctores where ( doc_paterno || ' ' || doc_materno || ' ' || doc_nombres) ilike '%$doc%' limit 1 ");
	if(!$chk){
		$resp=FALSE;
	}else{
		$resp=TRUE;
	}

	print(json_encode(array($resp,$chk['doc_rut'],$chk['doc_id'])));
?>
