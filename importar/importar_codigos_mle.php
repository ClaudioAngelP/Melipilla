<?php 

	require_once('../conectar_db.php');
	
	$mle=cargar_registros_obj("
		SELECT (grupo || sub_grupo || presta) AS mle_codigo FROM mle WHERE corr='0000';	
	");
	
	for($i=0;$i<sizeof($mle);$i++) {
	
		$codmle=$mle[$i]['mle_codigo'];
		$glosa='';	
		$cod=cargar_registros_obj("SELECT glosa FROM mle WHERE grupo || sub_grupo || presta = '".$codmle."'");
		for($j=0;$j<sizeof($cod);$j++)
			$glosa.=$cod[$j]['glosa'];	
		
		$glosa=pg_escape_string($glosa);

		$query="INSERT INTO codigos_prestacion VALUES ('$codmle','$glosa','mle');";
		
		$chk=cargar_registro("SELECT * FROM codigos_prestacion WHERE codigo='$codmle'");		
		
		if(!$chk) {
			pg_query($query);
			print("$query<br>");		
		} else {
			print("<b>$query</b><br>");				
		}
		
		flush();
		
	}

?>