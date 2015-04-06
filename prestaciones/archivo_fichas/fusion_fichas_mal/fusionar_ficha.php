<?php
    require_once("../../../conectar_db.php");
    $pac_id_original=$_POST['pac_id_original']*1;
    $pac_id_remplazo=$_POST['pac_id_remplazo']*1;
    //--------------------------------------------------------------------------
    if($pac_id_original=='' or $pac_id_original==0)
        exit('1');
    
    if($pac_id_remplazo=='' or $pac_id_remplazo==0)
        exit('2');
    
    pg_query("START TRANSACTION;");
    pg_query("update nomina_detalle set pac_id=$pac_id_original where pac_id=$pac_id_remplazo");
    //pg_query("delete from pacientes where pac_id=$pac_id_remplazo");
    pg_query("COMMIT;");
    print(json_encode(Array('OK')));
?>