<?php

  require_once('../../conectar_db.php');
  
  	$id = ($_GET['bodega_id']*1);
		$nombre = iconv("UTF-8", "ISO-8859-1", $_GET['bodega_glosa']);
		$ubica = iconv("UTF-8", "ISO-8859-1", $_GET['bodega_ubica']);
		
    $_GET['bodega_proveedor']=='true'?$proveedor='true':$proveedor='false';
		$_GET['bodega_inter']=='true'?$bod_inter='true':$bod_inter='false';
		$_GET['bodega_despacho']=='true'?$bod_despacho='true':$bod_despacho='false';
		$_GET['bodega_controlados']=='true'?$bod_controlados='true':$bod_controlados='false';
		$_GET['bodega_repone']=='true'?$bod_repone='true':$bod_repone='false';
		if($_GET['bodega_consume']=='true') {
      $ccosto=($_GET['servicios']*1);
    } else {
      $ccosto=0;
		
    }
    
    $centro = iconv("UTF-8", "ISO-8859-1", $_GET['bodega_costoid']);
			
		if($id!=0) {
		
			// Edición de Personal
			
			pg_query($conn, "
			
			UPDATE bodega
			SET
			bod_glosa='$nombre',
			bod_ubica='$ubica',
			bod_proveedores=$proveedor,
			bod_inter=$bod_inter,
			bod_despacho=$bod_despacho,
			bod_controlados=$bod_controlados,
			bod_desp_ccosto=$ccosto,
			bod_costo='$centro',
			bod_repone='$bod_repone'
			WHERE bod_id=$id
			
			");
			
		
		} else {
		
			// Ingreso de Personal nuevo
		
			pg_query($conn, "
			
			INSERT INTO bodega
			VALUES (
			DEFAULT,
			'$nombre',
			'$ubica',
			$proveedor,
			'$centro',
			$bod_inter,
			$bod_despacho,
			$bod_controlados,
			$ccosto,
			$bod_repone
			)
			");
		
		}
		
		print('1');
	


?>
