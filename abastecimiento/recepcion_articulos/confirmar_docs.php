<?php

  require_once('../../conectar_db.php');
  
  	pg_query($conn, "START TRANSACTION;");
		
		$prov_id=($_GET['proveedor_encontrado']*1);
		
		$numero=$_GET['bodega_doc_asociado_num'];
		
		$tipo=$_GET['bodega_doc_asociado']*1;
		
    // Antes de ingresar verifica que no esté presente en la base de datos...
		
		$existe = pg_query($conn,"SELECT * FROM documento WHERE doc_tipo=$tipo AND doc_num=$numero AND doc_prov_id=$prov_id");
		
		if(pg_num_rows($existe)>0) {
		
		  $docu = pg_fetch_row($existe);
			
      exit(json_encode($docu[0]));
			
		} else {
		
      exit(json_encode(0));
    
    }


?>
