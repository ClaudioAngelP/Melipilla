<?php

  require_once('../../conectar_db.php');
  
		$id 		= ($_GET['proveedor_id']*1);
		$rut 		= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_rut']);
		$nombre 	= strtoupper(iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_nombre']));
		$direccion 	= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_direccion']);
		$ciudad		= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_ciudad']);
		$fono	 	= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_fono']);
		$fax		= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_fax']);
		$mail 		= iconv("UTF-8", "ISO-8859-1", $_GET['proveedor_mail']);


   if($id==0) {

   // Valida que el proveedor no exista en la base de datos

          $existe = pg_query($conn, "SELECT prov_rut FROM proveedor
                                     WHERE prov_rut='$rut'");

          if(pg_num_rows($existe)>0) {
            $medi = pg_fetch_row($existe);
             print('2');

          } else {


           // Ingreso de Proveedor nuevo

			pg_query($conn, "
			INSERT INTO proveedor
			VALUES (
			DEFAULT,
			'".pg_escape_string($rut)."',
			'".pg_escape_string($nombre)."',
			'".pg_escape_string($direccion)."',
			'".pg_escape_string($ciudad)."',
			'".pg_escape_string($fono)."',
			'".pg_escape_string($fax)."',
			'".pg_escape_string($mail)."'
			)
			");

            print('3');
          }

     }


    if($id!=0) {

            	// Edición de Proveedor

			pg_query($conn, "
			UPDATE proveedor
			SET
			prov_rut='".pg_escape_string($rut)."',
			prov_glosa='".pg_escape_string($nombre)."',
			prov_direccion='".pg_escape_string($direccion)."',
			prov_ciudad='".pg_escape_string($ciudad)."',
			prov_fono='".pg_escape_string($fono)."',
			prov_fax='".pg_escape_string($fax)."',
			prov_mail='".pg_escape_string($mail)."'
			WHERE prov_id=$id
			");

          	print('1');
		}






?>
