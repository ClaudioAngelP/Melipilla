<?php 

	require_once('../../conectar_db.php');
	
	$fapp_id=$_POST['fapp_id']*1;

	$check=$_POST['check']*1;
	
	if($check==1)
		$activado='true';
	else
		$activado='false';
	
	pg_query("UPDATE fappab_pabellones SET fapp_activado=$activado WHERE fapp_id=$fapp_id;");
	
	
	print(json_encode(true));

?>
