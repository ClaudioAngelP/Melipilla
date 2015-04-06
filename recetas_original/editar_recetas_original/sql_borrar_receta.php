<?php error_reporting(E_ALL);

  require_once('../../conectar_db.php');
  
  $receta_id=($_GET['receta_id']*1);
  $motivo=$_GET['motivo'];
  $user=$_GET['user'];



   // Antes de eliminar la receta verifica que no se encuentre asociado a alguna reposicion

		$existe = pg_query($conn, "SELECT * FROM reposicion_detalle
                                  where receta_id=$receta_id");

		if(pg_num_rows($existe)>0) {

		  $rece = pg_fetch_row($existe);
           print(json_encode(false));

		} else {

           if(pg_query($conn, "
            select anular_receta(".$receta_id.",'$motivo',".$user.");
           ")){
				
				print(json_encode(true));
			}
        }

?>
