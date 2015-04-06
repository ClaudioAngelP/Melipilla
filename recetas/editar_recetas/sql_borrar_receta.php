<?php

  require_once('../../conectar_db.php');
  
  $receta_id=($_GET['receta_id']*1);



   // Antes de eliminar la receta verifica que no se encuentre asociado a alguna reposicion

		$existe = pg_query($conn, "SELECT * FROM reposicion_detalle
                                  where receta_id=$receta_id");

		if(pg_num_rows($existe)>0) {

		  $rece = pg_fetch_row($existe);
           print(json_encode(false));

		} else {

           pg_query($conn, "
            select eliminar_receta(".$receta_id.");
           ");
           print(json_encode(true));

        }

?>
