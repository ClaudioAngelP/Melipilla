<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('listado_proveedores.csv'));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',pg_escape_string(utf8_decode($f[$i])));
		
		$rut=(str_replace('.','',$r[0])*1).'-'.strtoupper($r[1]);		
		$nombre=pg_escape_string(trim($r[2]));
		$direccion=pg_escape_string(trim($r[3]));
		$ciudad=pg_escape_string(trim($r[4]));
		$telefono=pg_escape_string(trim($r[5]));
		$fax=pg_escape_string(trim($r[6]));
		//$observa=trim($r[7]);
		
		$chk=cargar_registro("SELECT * FROM proveedor WHERE prov_rut='$rut'");
		
		if(!$chk) {		

			pg_query("INSERT INTO proveedor VALUES (
        DEFAULT, '$rut', 
        '$nombre', '$direccion', 
        '$ciudad', '$telefono', '$fax', '');");
		
			
		}
		
	}
	
	pg_query("COMMIT;");

?>
