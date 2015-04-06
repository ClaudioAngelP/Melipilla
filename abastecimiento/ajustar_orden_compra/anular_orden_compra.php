<?php
      require_once('../../conectar_db.php');

    $id_orden = $_POST['id_orden']*1;
    

    
    pg_query($conn,"UPDATE orden_compra SET orden_estado=3 WHERE orden_id=".$id_orden."" );



    //die(json_encode(Array(true)));

    die(json_encode(Array(true,$id_orden)));


 ?>
