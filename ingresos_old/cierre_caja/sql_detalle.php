<?php 

	require_once('../../conectar_db.php');
	
	$fecha=fecha_sql($_POST['fecha']);
	$func_id=$_POST['func_id']*1;

	$chq=array();
	
	$chq[1]=20000;
	$chq[2]=10000;
	$chq[3]=5000;
	$chq[4]=2000;
	$chq[5]=1000;
	$chq[6]=500;
	$chq[7]=100;
	$chq[8]=50;
	$chq[9]=10;
	$chq[10]=5;
	$chq[11]=1;


	pg_query("DELETE FROM caja_detalle WHERE cd_fecha=$fecha AND func_id=$func_id");

	for($i=1;$i<=11;$i++) {
		
		$val=$_POST['m_'.$i]*1;
		
		if($val>0)
		pg_query("
			INSERT INTO caja_detalle VALUES (
				DEFAULT,
				$fecha,
				$func_id,
				".$chq[$i].",
				".$val."		
			);	
		");
		
	}

?>