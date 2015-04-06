<?php
    require_once('../../conectar_db.php');
    function fix($str)
    {
        $test=explode('/',$str);
	if(checkdate($test[1],$test[0],$test[2]))
                return "'".$str."'";
        else
            return 'null';
    }		
    $nomd_id=$_POST['nomd_id']*1;
    $sol_examd_id=$_POST['sol_examd_id']*1;
    $html=pg_escape_string($_POST['html']);
    $fecha1=fix($_POST['fecha1']);
    $fecha2=fix($_POST['fecha2']);
    $fecha3=fix($_POST['fecha3']);
    $doc_id=$_POST['doc_id'];
    if($doc_id=="")
    {
        $doc_id='null';
    }
    if($sol_examd_id==0)
    {
        pg_query("DELETE FROM nomina_detalle_informe WHERE nomd_id=$nomd_id");	
        pg_query("INSERT INTO nomina_detalle_informe VALUES ($nomd_id, $fecha1, $fecha2, $fecha3, '$html', DEFAULT, $doc_id,".$_SESSION['sgh_usuario_id'].", 0);");
    }
    else
    {
        //print("UPDATE solicitud_examen_detalle SET sol_examd_informe='$html', sol_examd_digitacion=$fecha2, sol_examd_entrega=$fecha3, sol_examd_doc_id=$doc_id WHERE sol_examd_id=$sol_examd_id");
        pg_query("UPDATE solicitud_examen_detalle SET sol_examd_informe='$html', sol_examd_digitacion=$fecha2, sol_examd_entrega=$fecha3, sol_examd_doc_id=$doc_id WHERE sol_examd_id=$sol_examd_id");
    }
?>