<?php

  require_once('../../conectar_db.php');
  
  $id=$_GET['id_talonario']*1;
  
  $talonario = cargar_registro(
                    "SELECT talonario.*, doctores.*, 
                    (doc_rut) AS func_rut 
                    FROM talonario 
                    LEFT JOIN doctores ON doc_id=talonario_func_id
                    JOIN receta_tipo_talonario ON talonario_tipotalonario_id=tipotalonario_id
                    WHERE talonario_id=".$id
                );
                
  print(json_encode($talonario));

?>
