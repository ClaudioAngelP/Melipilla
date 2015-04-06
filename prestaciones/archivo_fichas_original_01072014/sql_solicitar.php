<?php
    require_once('../../conectar_db.php');
    error_reporting(E_ALL);
    if(isset($_POST['servicio']))
    {
        if(($_POST['servicio']*1)==1)
        {
            $servicio=true;
            $centro_ruta=pg_escape_string($_POST['centro_ruta']);
        }
        else
        {
            $servicio=false;
        }
    }
    else
    {
        $servicio=false;
    }
    $func_id=$_SESSION['sgh_usuario_id']*1;
    if(!$servicio)
    {
        $pid=$_POST['pac_id']*1;
        $did=$_POST['doc_id']*1;
        $eid=$_POST['esp_id']*1;
        //$motivo=$_POST['motivo'];
        $aid=$_POST['amp_id']*1;
        $p=cargar_registro("SELECT pac_ficha FROM pacientes WHERE pac_id=$pid;");
        pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,$eid,$did,$pid,'".$p['pac_ficha']."',current_timestamp,0,null,$aid,null,$func_id)");
    }
    else
    {
        $pid=$_POST['pac_id']*1;
        $aid=$_POST['amp_id']*1;
        $p=cargar_registro("SELECT pac_ficha FROM pacientes WHERE pac_id=$pid;");
        pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,0,0,$pid,'".$p['pac_ficha']."',current_timestamp,0,null,$aid,'$centro_ruta',$func_id)");
    }
	

?>
