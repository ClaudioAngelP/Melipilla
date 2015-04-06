<?php

    require_once('../../conectar_db.php');

		$id = ($_GET['func_id']*1);
		
    $rut = 
    pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_rut']));
		$nombre = 
    pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_nombre']));
		$cargo = 
    pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_cargo']));
		$clave = 
    pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['func_clave']));
		$valor = $_GET['func_valor_hh']*1;
		
    if($id!=0) {
		
			// Edición de Personal
			
			pg_query($conn, "
			
			UPDATE tecnico
			SET
			tec_rut='$rut',
			tec_nombre='$nombre',
			tec_cargo='$cargo',
			tec_clave='$clave',
			tec_valor_hh=$valor
			WHERE tec_id=$id
			
			");
			
		
		} else {
		
			// Ingreso de Personal nuevo
		
			pg_query($conn, "
			
			INSERT INTO tecnico
			VALUES (
			DEFAULT,
			'$rut',
			'$nombre',
			'$cargo',
			'$clave',
			null,
			null,
			$valor
			)
			");
		
		}
		
		print('1');

  
?>
