<?php
    require_once('../../conectar_db.php');
    $id_gasto = $_POST['gasto_id']*1;
    $item = pg_escape_string($_POST['item_codigo']);
    $nombre_gasto = pg_escape_string(utf8_decode($_POST['gasto_nombre']));
    $unidad_gasto = pg_escape_string(utf8_decode($_POST['gasto_unidad']));
    $valmax_gasto = $_POST['gasto_valmax']*1;
    $item=pg_escape_string(utf8_decode($_POST['item_serv']));
    if($id_gasto==0)
    {
        // Inserta Gasto nuevo.
        pg_query($conn, "INSERT INTO gasto_externo VALUES (DEFAULT,'$nombre_gasto','$unidad_gasto',$valmax_gasto,'$item')");
        $query_id="CURRVAL('gasto_externo_gastoext_id_seq')";
    }
    else
    {
        // Modifica Gasto ya existente.
        pg_query($conn, "UPDATE gasto_externo SET gastoext_nombre='$nombre_gasto', gastoext_unidad='$unidad_gasto', gastoext_valortotal=$valmax_gasto, gastoext_item_codigo=$item WHERE gastoext_id=$id_gasto");
        $query_id=$id_gasto;
    }
    $max=cargar_registro("SELECT COUNT(*) AS max FROM centro_costo;");
    if($max)
        for($i=0;$i<$max['max'];$i++)
        {
            if(!isset($_POST['gextn_'.$i]))
            {
                continue;
            }
            $centro_ruta = pg_escape_string($_POST['gextn_'.$i]);
            $gastod_cant = pg_escape_string($_POST['gextd_'.$i]*1);
            if($gastod_cant==0 AND $id_gasto!=0)
            {
                pg_query($conn, "DELETE FROM gastoext_detalle WHERE gastoextd_centro_ruta='$centro_ruta' AND gastoextd_gastoext_id=$query_id");
            }
            else
            {
                if($id_gasto==0)
                {
                    pg_query($conn, "INSERT INTO gastoext_detalle VALUES (DEFAULT,'$centro_ruta','$gastod_cant',$query_id)");
                }
                else
                {
                    $comp = cargar_registro("SELECT * FROM gastoext_detalle WHERE gastoextd_centro_ruta='$centro_ruta' AND gastoextd_gastoext_id=$id_gasto");
                    if(!$comp)
                    {
                        pg_query($conn, "INSERT INTO gastoext_detalle VALUES (DEFAULT,'$centro_ruta','$gastod_cant',$query_id)");
                    }
                    else
                    {
                        pg_query($conn, "UPDATE gastoext_detalle SET gastoextd_valor=$gastod_cant WHERE gastoextd_gastoext_id=$query_id AND gastoextd_centro_ruta='$centro_ruta'");
                    }
                }
            }
        }
        print(json_encode(true));
?>