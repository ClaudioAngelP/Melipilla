<?php

  require_once('../../conectar_db.php');
  

	$prove_id = ($_GET['buscar']*1);

	$sql = pg_query($conn, "DELETE FROM
                            proveedor
                            WHERE prov_id='$prove_id'
                	");


	

?>
