<?php

  require_once('../../conectar_db.php');

   pg_query($conn, "START TRANSACTION;");

	$prove_id = ($_GET['buscar']*1);


    // Antes de eliminar al proveedor verifica que no se encuentre asociado a otra(s)  tablas

		$existe = pg_query($conn, "SELECT * FROM proveedor
                             INNER JOIN documento on documento.doc_prov_id=proveedor.prov_id
                             where proveedor.prov_id=$prove_id");

		if(pg_num_rows($existe)>0) {

		  $prove = pg_fetch_row($existe);

          exit(json_encode($prove[0]));

		} else {

          exit(json_encode(0));

    }

?>
