<?php 
    require_once('../../conectar_db.php');
    //if(!isset($_POST['eliminar']))
    //{
        if(!isset($_POST['articulos']))
        {
            $glosa=pg_escape_string(html_entity_decode(utf8_decode($_POST['glosa'])));
            $art_id=$_POST['art_id']*1;
            $chk=pg_query("SELECT * FROM articulo_nombres WHERE artn_nombre='$glosa';");
            $func_id=$_SESSION['sgh_usuario_id']*1;
            if(pg_fetch_assoc($chk)) 
            {
                $artn_id=$chk['artn_id']*1;
                pg_query("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
                pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
            }
            else
            {
                pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
            }
            pg_query("START TRANSACTION;");
            pg_query("INSERT INTO orden_detalle (ordetalle_orden_id, ordetalle_art_id, ordetalle_cant, ordetalle_subtotal)
            SELECT orserv_orden_id, art_id, orserv_cant, orserv_subtotal FROM orden_servicios
            join articulo_nombres ON orserv_glosa=artn_nombre;");
            pg_query("delete from orden_servicios where orserv_id in(select orserv_id from orden_servicios join articulo_nombres ON orserv_glosa=artn_nombre);");
            pg_query("COMMIT;");
        }
        else
        {

            $arts=json_decode($_POST['articulos']);
            if(count($arts)>0)
            {
                for($i=0;$i<count($arts);$i++)
                {
                    $glosa=pg_escape_string(html_entity_decode(utf8_decode($arts[$i][2])));
                    $art_id=$arts[$i][0]*1;
                    $chk=pg_query("SELECT * FROM articulo_nombres WHERE artn_nombre='$glosa';");
                    $func_id=$_SESSION['sgh_usuario_id']*1;
                    if(pg_fetch_assoc($chk)) 
                    {
                        $artn_id=$chk['artn_id']*1;
                        pg_query("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
                        pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
                    }
                    else
                    {
                        pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
                    }
                }
                pg_query("START TRANSACTION;");
                pg_query("INSERT INTO orden_detalle (ordetalle_orden_id, ordetalle_art_id, ordetalle_cant, ordetalle_subtotal)
                SELECT orserv_orden_id, art_id, orserv_cant, orserv_subtotal FROM orden_servicios
                join articulo_nombres ON orserv_glosa=artn_nombre;");
                pg_query("delete from orden_servicios where orserv_id in(select orserv_id from orden_servicios join articulo_nombres ON orserv_glosa=artn_nombre);");
                pg_query("COMMIT;");
            }
        }
    
    /*    
    }
    else
    {
        $eliminar=$_POST['eliminar']*1;
        print("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
        if($eliminar==1)
        {
            pg_query("START TRANSACTION;");
            $artn_id=$_POST['artn_id']*1;
            print("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
            pg_query("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
            pg_query("COMMIT;");
        }
    }
    */
?>