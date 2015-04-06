<?php 
require_once("../conectar_db.php");

$passconfirm = pg_escape_string($_POST['passconfirm']);
$passconfirmmd5 = md5($passconfirm);

$result = pg_query($conn, "SELECT 
								a.func_id, func_clave
							FROM 
								func_acceso  as a
							INNER JOIN funcionario as b	
							ON a.func_id = b.func_id
							WHERE 
								permiso_id = 325
					");
$validado = 0;
while($row = pg_fetch_array($result)){

	$func_clave = $row['func_clave'];  
	
	if($func_clave  == $passconfirmmd5 ){
		$validado = 1;
	}		
}

echo $validado;


?>


