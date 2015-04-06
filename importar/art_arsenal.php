<?php
    error_reporting(E_ALL);
    require_once('../conectar_db.php');
    $fi=explode("\n", file_get_contents('arsenal_melipilla.csv'));
    pg_query("START TRANSACTION;");
    for($i=1;$i<sizeof($fi);$i++)
    {
        //print("<br>");
        //print("LINEA: ".$i);
        //print("<br>");
        
        $r=explode(';',$fi[$i]);
        //----------------------------------------------------------------------
        if(!isset($r[0]) OR trim($r[0])=='')
        {
            //print("<br>");
            //print("No tiene Codigo");
            //print("<br>");
            continue;
        }
        //----------------------------------------------------------------------
        $art_codigo=trim(strtoupper($r[0]));
        $nom=trim(strtoupper($r[1]));
        ////////////////////////////////////////////////////////////////////////
        $encontrado=true;
        $art=cargar_registro("SELECT * FROM articulo WHERE upper(art_codigo)=upper('$art_codigo')");
        if($art)
        {
            $art_id=$art['art_id']*1;
            //print('<br>');
            //print("update articulo set art_arsenal=true where art_id=$art_id;");
            //print('<br>');
            pg_query("update articulo set art_arsenal=true where art_id=$art_id;");
        }
        else
        {
            print($art_codigo."|".$nom);
            print('<br>');
        }    
    }
    pg_query("COMMIT");
?>