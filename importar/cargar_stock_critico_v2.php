<?php
    error_reporting(E_ALL);
    require_once('../conectar_db.php');
    $bod_id=66;
    $fi=explode("\n", file_get_contents('PLANILLA INSUMOS PEDIATRIA_v2.csv'));
    pg_query("START TRANSACTION;");
    //pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 20, '04/05/2014 05:32:00', 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos 04_05-2014.' );");
    for($i=1;$i<sizeof($fi);$i++)
    {
        print("<br>");
        print("LINEA: ".$i);
        print("<br>");
        
        $r=explode(';',$fi[$i]);
        //----------------------------------------------------------------------
        if(!isset($r[0]) OR trim($r[0])=='')
        {
            print("<br>");
            print("No tiene Codigo");
            print("<br>");
            continue;
        }
        //----------------------------------------------------------------------
        $art_codigo=trim(strtoupper($r[0]));
        //----------------------------------------------------------------------
        if(trim(str_replace('.','',$r[7]))=="")
        {
            $punto_pedido=0;
        }
        else
        {
            $punto_pedido=trim(str_replace('.','',$r[7]))*1;
        }
        
        if(trim(str_replace('.','',$r[6]))=="")
        {
            $punto_critico=0;
        }
        else
        {
            $punto_critico=trim(str_replace('.','',$r[6]))*1;
        }
        
        if(trim(str_replace('.','',$r[8]))=="")
        {
            $gasto_mensual=0;
        }
        else
        {
            $gasto_mensual=trim(str_replace('.','',$r[8]))*1;
        }
        $encontrado=true;
        $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
        if(!$art)
        {
            print('<br>');
            print("Articulo No Encontrado :|".$i."|".$art_codigo);
            print('<br>');
            $encontrado=false;
        }
        if($encontrado)
        {
            $art_id=$art['art_id']*1;
            if($gasto_mensual!=0)
            {
                $grabar=true;
                if($grabar)
                {
                    $chk=cargar_registro("SELECT * FROM stock_critico WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
                    if(!$chk)
                    {
                        print("<br>");
                        print("INSERT INTO stock_critico VALUES ($art_id, $punto_pedido, $punto_critico, $bod_id, $gasto_mensual);");
                        print("<br>");
                        pg_query("INSERT INTO stock_critico VALUES ($art_id, $punto_pedido, $punto_critico, $bod_id, $gasto_mensual);");
                    }
                    else
                    {
                        print("<br>");
                        print("UPDATE stock_critico SET critico_pedido=$punto_pedido, critico_critico=$punto_critico, critico_gasto=$gasto_mensual WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
                        print("<br>");
                        pg_query("UPDATE stock_critico SET critico_pedido=$punto_pedido, critico_critico=$punto_critico, critico_gasto=$gasto_mensual WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
                    }
                }
                else
                {
                    print("<br>");
                    print("No se ha grabado el articulo : ".$art_codigo);
                    print("<br>");
                }
            }
        }
    }
    pg_query("COMMIT");
?>