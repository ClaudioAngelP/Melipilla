<?php require_once('../../conectar_db.php');

	$id = ($_GET['func_id']*1);
		
    $rut = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_rut']));
	$nombre = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_nombre']));
	$cargo = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_cargo']));
	$clave = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_clave']));
       
    $chk=cargar_registro("SELECT * FROM funcionario WHERE func_rut='$rut';");
		
	if($chk) $id=$chk['func_id']; else $id=0;
		
    if($id!=0) {
		
			// Edición de Personal
			
			pg_query($conn, "
			
			UPDATE funcionario
			SET
			func_rut='$rut',
			func_nombre='$nombre',
			func_cargo='$cargo',
			func_clave=md5('$clave')
			WHERE func_id=$id
			
			");
			
		
		} else {
		
			// Ingreso de Personal nuevo
		
			pg_query($conn, "
			
			INSERT INTO funcionario
			VALUES (
			DEFAULT,
			'$rut',
			'$nombre',
			'$cargo',
			md5('$clave'),
			null,
			null
			)
			");
		
		}
		
		print('1');

  
?>
