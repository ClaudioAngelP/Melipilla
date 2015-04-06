<?php 

	require_once('../../conectar_db.php');
	
	$fecha=date('d/m/Y');
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
	
	$ttmp=cargar_registro("SELECT * FROM apertura_cajas WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");
	
	$ac_id=$ttmp['ac_id']*1;

	for($i=1;$i<=11;$i++) {
		
		$val=$_POST['m_'.$i]*1;
		
		if($val>0)
		pg_query("
			INSERT INTO caja_detalle VALUES (
				DEFAULT,
				$ac_id,
				".$chq[$i].",
				".$val."		
			);	
		");
		
	}
	
	pg_query("UPDATE apertura_cajas SET ac_fecha_cierre=CURRENT_TIMESTAMP WHERE func_id=$func_id AND ac_fecha_cierre IS NULL;");

	$_GET['ac_id']=$ac_id;

	require_once('imprimir_cierre_caja.php');

?>
