<?php

  require_once('../../conectar_db.php');
  
  $presta_id=$_POST['presta_id']*1;
  
  pg_query($conn, "
    DELETE FROM prestacion WHERE presta_id=$presta_id
  ");

?>
