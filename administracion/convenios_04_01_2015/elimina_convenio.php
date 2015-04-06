<?php

  require_once('../../conectar_db.php');
  

	$id_convenio = ($_GET['convenio_id']*1);

	$sql = pg_query($conn, "DELETE FROM
                            convenio
                            WHERE convenio_id='$id_convenio'
                	");

     print('2'); 
	

?>
