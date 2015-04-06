<?php
    error_reporting(E_ALL);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    //--------------------------------------------------------------------------
    pg_query("START TRANSACTION;");
    $art=cargar_registros_obj("SELECT art_id,art_codigo FROM articulo");
    if($art) {
        for($i=1;$i<sizeof($art);$i++)
        {
            
            $reg_precios=cargar_registro("SELECT (stock_subtotal/stock_cant)as precio FROM stock
            JOIN logs ON stock_log_id=log_id
            WHERE stock_art_id=".$art[$i]['art_id']."
            AND log_tipo=1 
            AND stock_subtotal>0 AND stock_cant>0
            ORDER BY log_fecha desc limit 1");
            if($reg_precios) {
                if(($reg_precios['precio']*1)>0) {
                    print("Linea ".$i."  Codigo:".$art[$i]['art_codigo']."  -  ".$reg_precios['precio']."<br>");
                    print("UPDATE articulo SET art_val_ult=".($reg_precios['precio']*1)." WHERE art_id=".($art[$i]['art_id']*1)."");
                    print("<br>");
                    pg_query("UPDATE articulo SET art_val_ult=".($reg_precios['precio']*1)." WHERE art_id=".($art[$i]['art_id']*1)."");
                }
            }
        }
    }
    pg_query("COMMIT");
?>