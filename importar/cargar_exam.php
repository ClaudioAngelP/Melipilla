<?php 
    require_once('../config.php');
    require_once('../conectores/sigh.php');

    $f=explode("\n", file_get_contents('exam_lab.csv'));
    $esp_id=6120;
    pg_query("START TRANSACTION;");
    for($i=1;$i<sizeof($f);$i++)
    {
        if(trim($f[$i])=="")
            continue;
	
        $r=explode(';',$f[$i]);
        print("<br>");
        print_r($r);
        print("<br>");
        print("<br>");
	$cod_exam=trim($r[0]);
	$desc_exam=trim($r[1]);
	$grupo_exam="";
	$tipo_exam="";
        $art=cargar_registro("SELECT * FROM procedimiento_codigo WHERE esp_id=$esp_id and pc_codigo='$cod_exam'");
        if($art)
        {
            print("<br>");
            print("Examen Existe Linea:".($i+1));
            print("<br>");
            print("<br>");
            print_r($r);
            print("<br>");
            print("<br>");
            print($r[1]);
            print("<br>");
            print($desc_exam);
            print("<br>");
        }
        else
        {
            print("<br>");
            print("NO Existe Linea:".($i+1));
            print("<br>");
            print("INSERT INTO procedimiento_codigo VALUES (default, $esp_id,'$desc_exam', '$cod_exam', null, null);");
            print("<br>");
            pg_query("INSERT INTO procedimiento_codigo VALUES (default, $esp_id,'$desc_exam', '$cod_exam', null, null);");
        }
        
        print("--------------------------------------------------");
    }
    pg_query("COMMIT;");
?>
