<?php
    require_once('../../conectar_db.php');
    $hosp_id=$_POST['hosp_id']*1;
    pg_query("UPDATE hospitalizacion SET hosp_anulado=1, hosp_func_id2=".$_SESSION['sgh_usuario_id'].", hosp_numero_cama=0 WHERE hosp_id=$hosp_id");
    pg_query("UPDATE inte_hospitalizados SET hosp_estado_hosp=3, hosp_estado_leido=0 WHERE intehosp_cta_corriente=$hosp_id");
    pg_query("UPDATE hospitalizacion SET hosp_estado_envio=1 WHERE hosp_id=$hosp_id");
    print(json_encode(true));
    // INTEGRACION GRIFOLS (PYXIS ADT)....

    $_GET['hosp_id']=$hosp_id*1;
    $script=true;
    ob_start();
    /*require_once('../../conectores/grifols/send_ADT.php'); ELIMINADO POR NO TENER GRIFOLS*/ 
    ob_end_clean();
?>
