<?php

  require_once('../../conectar_db.php');
  
  $receta_id=($_GET['receta_id']*1);
  
  $nro = pg_fetch_row($num);
  $numero = $nro[0];
  
  pg_query($conn, "
  DELETE FROM receta WHERE receta_id=".$receta_id.";
  DELETE FROM recetas_detalle WHERE recetad_receta_id=".$receta_id.";
  ");

?>
