<?php

  require_once('../../conectar_db.php');
  
  $detpat_id=$_POST['detpat_id']*1;
  
  pg_query($conn, 'DELETE FROM detalle_patauge WHERE detpat_id='.$detpat_id);

  // Limpia hojas huerfanas...

  /*for($i=0;$i<5;$i++) {
    pg_query($conn, "
      DELETE FROM detalle_patauge WHERE
      detpat_padre_id NOT IN (SELECT detpat_id FROM detalle_patauge)
    ");
  }*/

?>
