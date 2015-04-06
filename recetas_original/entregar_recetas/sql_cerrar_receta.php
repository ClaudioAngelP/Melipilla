<?php
    require_once('../../conectar_db.php');
    $receta_id=$_GET['receta_id']*1;
    $motivo=$_GET['motivo'];
    pg_query($conn,"START TRANSACTION;");
    pg_query("UPDATE receta SET receta_vigente=false, receta_fecha_cierre=CURRENT_TIMESTAMP, receta_func_id2=".$_SESSION['sgh_usuario_id'].", receta_motivo_termino='$motivo' WHERE receta_id=$receta_id;");
    pg_query($conn,"COMMIT;");
    print('OK');
?>
