<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');

	$f=explode("\n",utf8_decode(file_get_contents("arancel_melipilla3.csv")));
	
	
		pg_query("truncate table codigos_prestacion;");
			
	
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',$f[$i]);
		
		if($r[0]=='') continue;
		
		$codigo=pg_escape_string($r[0]);
		$glosa=pg_escape_string($r[1]);
		$tipo=$r[2];
		$anio=$r[3];
		
		
		$precio=$r[4]*1;
		$transf=$r[5]*1;
		$copago_a=$r[6]*1;
		$copago_b=$r[7]*1;
		$copago_c=$r[8]*1;
		$copago_d=$r[9]*1;
		$pab=$r[10];
		$canasta=$r[11];
		$convenio=$r[12];
		
		
		pg_query("INSERT INTO codigos_prestacion VALUES ('$codigo','$glosa','$tipo', $anio, $precio, $transf, $copago_a, $copago_b, $copago_c, $copago_d, '$pab', '$canasta', '$convenio' );");
	print("$i | $codigo<br>");
	}

?>