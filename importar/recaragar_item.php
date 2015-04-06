<?php
    error_reporting(E_ALL);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    $fi=explode("\n", file_get_contents('inventario stock bod.abastecimiento.csv'));
    for($i=0;$i<sizeof($fi);$i++)
    {
        if(trim($fi[$i])=='')
            continue;
        
        $r=explode(';',$fi[$i]);
        $art_codigo=trim(strtoupper($r[0]));
        $item=explode(' ',trim($r[2]));
        
        if($art_codigo=='')
            continue;
             
        $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
        if(!$art)
        {
            print("<br>");
            print(" No Existe articulo Ingreso Stock: ".$art_codigo);
            print("<br>");
        }
        else
        {
            $art_id=$art['art_id']*1;
            $item_pres=$item[0];
            $reg_item=cargar_registro("select * from item_presupuestario where item_codigo='$item_pres'");
            if(!$reg_item)
            {
                $col_item=trim($r[2]);
                print("<br>");
                print("Linea ".$i);
                print("<br>");
                print($fi[$i]);
                print("<br>");
                print(" No Existe Item: ".$item_pres);
                print("<br>");
                print("<br>");
                
                $col_item=trim($r[2]);
                $col_item=preg_replace('/ /','_',$col_item,1);
                $items=explode('_',$col_item);
                print("insert into item_presupuestario values('$items[0]','$items[1]',null);");
                print("<br>");
                print("<br>");
                pg_query("insert into item_presupuestario values('$items[0]','$items[1]',null);");
                $item_pres=$items[0];

            }
            print("<br>");
            print("UPDATE articulo SET art_item='$item_pres' where art_id=$art_id");
            print("<br>");
            pg_query("UPDATE articulo SET art_item='$item_pres' where art_id=$art_id");
        }
    }
    pg_query("COMMIT");
?>