<?php 
    set_time_limit(0);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    $f=explode("\n", file_get_contents('diagnosticos_tapsa.csv'));
    
    pg_query("START TRANSACTION;");
    pg_query("set client_encoding to 'latin1'");
    for($i=1;$i<sizeof($f);$i++)
    //for($i=1;$i<34;$i++)
    {
        if(trim($f[$i])=="")
            continue;
	
        $r=explode(';',$f[$i]);
        //print("<br>");
        //print_r($r);
        //print("<br>");
	$diag_cod=trim($r[0]);
	$diag_desc=trim($r[1]);
	$diag_cie10=trim($r[2]);
	$diag_vector=trim($r[3]);
        
        $reg_diag=cargar_registro("SELECT * FROM diagnosticos_tapsa WHERE nomdiag_cod='$diag_cod';");
        if($reg_diag)
        {
            //print("<br>");
            //print("Diagnostico Tapsa Existe:".($i+1));
            //print("<br>");
        }
        else
        {
            print("<br>");
            print("NO Existe Diagnostico Tapsa: ".($i+1));
            print("<br>");
            print("INSERT INTO diagnosticos_tapsa VALUES ('$diag_cod', '$diag_desc','$diag_cie10', '$diag_vector');");
            print("<br>");
            pg_query("INSERT INTO diagnosticos_tapsa VALUES ('$diag_cod','$diag_desc','".utf8_encode($diag_cie10)."','$diag_vector');");
        }
    }
    pg_query("set client_encoding to 'UTF8'");
    pg_query("COMMIT;");
?>
