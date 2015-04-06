<?php
    error_reporting(E_ALL);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    $bod_id=36;
    $comentarios = "Carga en Opcion de Ajuste para Farmacia CAE";
    //--------------------------------------------------------------------------
    pg_query("START TRANSACTION;");
    
    pg_query("INSERT INTO logs VALUES (DEFAULT, 7, 30, now(), 0, 0, 0, '$comentarios')");
    $log = "CURRVAL('logs_log_id_seq')";
    //--------------------------------------------------------------------------
    
    $fi=explode("\n", utf8_decode(file_get_contents('articulo_inventario_cae.csv')));
    $art_id_ant='';
    for($i=1;$i<sizeof($fi);$i++)
    {
        print("<br>");
        print_r($fi[$i]);
        print("<br>");
        if(trim($fi[$i])=='')
            continue;
        
        $r=explode(';',$fi[$i]);
        $art_codigo=trim(strtoupper($r[0]));
        if($art_codigo=='')
            continue;
        
        /*
        $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
        if($art)
        { 
            $art_id=$art['art_id']*1;
            if($art_id!=$art_id_ant)
            {
                pg_query("insert into stock (stock_art_id, stock_bod_id, stock_cant, stock_log_id, stock_vence, stock_subtotal)
                select stock_art_id, $bod_id, -cantidad, $log, stock_vence, 0 from (
                select stock_art_id, stock_vence, SUM(stock_cant) AS cantidad
                FROM stock
                WHERE stock_bod_id=$bod_id AND stock_art_id=".$art_id."
                group by stock_art_id, stock_vence
                ) AS foo where cantidad<>0;");
                
                $art_id_ant=$art_id;
            }
        }
        else
        {
            print("<br>");
            print(" No Existe Articulo Stock Actual Valorizado: ".$art_codigo);
            print("<br>");
            print("<br>");
        }
         * 
         */
    }

    $fi=explode("\n", utf8_decode(file_get_contents('articulo_inventario_cae.csv')));
    for($i=1;$i<sizeof($fi);$i++)
    {
        if(trim($fi[$i])=='')
            continue;
        
        $r=explode(';',$fi[$i]);
        $art_codigo=trim(strtoupper($r[0]));
        //$art_glosa=trim(strtoupper($r[1]));
        //$art_forma=trim(strtoupper($r[2]));
        //$item_codigo=trim(strtoupper($r[3]));
        //$punto_pedido=trim(str_replace('.','',$r[4]))*1;
        //$punto_critico=trim(str_replace('.','',$r[5]))*1;
        $stock=trim(str_replace('.','',$r[4]))*1;
        if(($stock)=='')
        {
            continue;
        }
        if(($stock*1)==0)
        {
            continue;
        }
        if($art_codigo=='')
            continue;
        
        $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
        if(!$art)
        { 
            print("<br>");
            print(" No Existe articulo en los registros: ".$art_codigo);
            print("<br>");
            //print("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
            print("<br>");
            
        }
        else
        {
            $art_id=$art['art_id']*1;
            $fec="'".str_replace('-','/',trim($r[5]))."'";
            if(trim($r[5])=="")
            {
                $fec='null';
            }
            /*
            if($art['art_vence']==true)
            {
                $fec='31/12/2014';
            }
            else
            {
                $fec='null';
            }
            */
            if($stock!=0)
            {
                if($fec!='null')
                {
                    print("<br>");
                    print("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $stock, $log, $fec, 0 )");
                    print("<br>");
                    pg_query("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $stock, $log, $fec, 0 )");
                }
                else
                {
                    print("<br>");
                    print("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $stock, $log, $fec, 0 )");
                    print("<br>");
                    pg_query("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $stock, $log, $fec, 0 )");
                    
                }
                if(trim($r[6])!="")
                {
                    $stock_id = "CURRVAL('stock_stock_id_seq')";
                    print("<br>");
                    print("INSERT INTO stock_refpartida VALUES (DEFAULT, $stock_id, '".trim($r[6])."')");
                    print("<br>");
                    pg_query("INSERT INTO stock_refpartida VALUES (DEFAULT, $stock_id, '".trim($r[6])."')");
                }
                
                
            }
            
            $bod=cargar_registro("SELECT * FROM articulo_bodega WHERE artb_art_id=$art_id and artb_bod_id=$bod_id");
            if(!$bod)
            { 
                print("<br>");
                print("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
                print("<br>");
                pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
            }
            //$gasto=$punto_critico+$punto_pedido;
            //if($gasto!=0)
            //{
            //    $chk=cargar_registro("SELECT * FROM stock_critico WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
            //    if(!$chk)
            //        pg_query("INSERT INTO stock_critico VALUES ($art_id, $punto_pedido, $punto_critico, $bod_id, $gasto);");
            //    else
            //        pg_query("UPDATE stock_critico SET critico_pedido=$punto_pedido, critico_critico=$punto_critico, critico_gasto=$gasto WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
            //}
        }
    }
     
     
    pg_query("COMMIT");
?>