<?php
    require_once("../../conectar_db.php");
    if(isset($_POST['norden']))
    {
        $nroorden=pg_escape_string($_POST['norden']);
        $nroorden=str_replace('*','%', $nroorden);
        $resultado=cargar_registros_obj("Select orden_id, orden_numero, orden_fecha, orden_estado, proveedor.prov_id, proveedor.prov_rut, proveedor.prov_glosa,
                            proveedor.prov_direccion,proveedor.prov_ciudad, proveedor.prov_fono, orden_iva, orden_numero
                            from orden_compra
                            join proveedor on orden_prov_id=prov_id where orden_numero='$nroorden'",true);
        if($resultado)
        {
                $ordenid=$resultado[0]['orden_id'];
                $detalle_orden=cargar_registros_obj("Select orden_detalle.*, art_glosa, art_codigo,
                                                    item_glosa, item_codigo from orden_detalle
                                                    join articulo on ordetalle_art_id=art_id
                                                    join item_presupuestario on art_item=item_codigo
                                                    where ordetalle_orden_id='$ordenid'",true);
                $detalle_serv=cargar_registros_obj("Select * from orden_servicios
                                                    left join item_presupuestario on orserv_item=item_codigo
                                                    where orserv_orden_id='$ordenid'",true);
                
                $pedido_orden=cargar_registros_obj("Select orden_pedido.*, pedido.pedido_nro from orden_pedido
                                                    join pedido on orden_pedido.pedido_id=pedido.pedido_id
                                                    where orden_id='$ordenid'",true);


                echo json_encode(array($resultado,$detalle_orden,$detalle_serv,$pedido_orden));
        }
        else
        {
            $nroorden=$nroorden*1;
            $resultado=cargar_registros_obj("Select orden_id, orden_numero, orden_fecha, orden_estado, proveedor.prov_id, proveedor.prov_rut, proveedor.prov_glosa,
                                proveedor.prov_direccion, proveedor.prov_ciudad, proveedor.prov_fono, orden_iva, orden_desc,
                                from orden_compra
                                join proveedor on orden_prov_id=prov_id where orden_id='$nroorden'",true);
            if($resultado)
            {
              
                $ordenid=$resultado[0]['orden_id'];
                $detalle_orden=cargar_registros_obj("Select orden_detalle.*, art_glosa, art_codigo,
                                                   item_glosa, item_codigo from orden_detalle
                                                   join articulo on ordetalle_art_id=art_id
                                                   join item_presupuestario on art_item=item_codigo
                                                    where ordetalle_orden_id='$ordenid'",true);
                $detalle_serv=cargar_registros_obj("Select * from orden_servicios
                                                    left join item_presupuestario on orserv_item=item_codigo
                                                    where orserv_orden_id='$ordenid'",true);

                $pedido_orden=cargar_registros_obj("Select orden_pedido.*, pedido.pedido_nro from orden_pedido
                                                    join pedido on orden_pedido.pedido_id=pedido.pedido_id
                                                    where orden_id='$ordenid'",true);

                echo json_encode(array($resultado,$detalle_orden,$detalle_serv,$pedido_orden));

            }
            else
            {
                
                echo json_encode(false);
            }

        }





    }


    //if(isset($_GET['codigo']))
 //   /{
///
  //      $buscar = pg_escape_string ($_GET['codigo']);
   //     $buscar = str_replace('*', '%', $buscar);
    //    $resultado = pg_query("Select log_id from logs where log_doc_id=".$buscar);
    //    if(pg_num_rows($resultado)==0) die('no hay');
    //    $var = pg_fetch_row($resultado);
     //   $log_id=$var[0];
     //   $detalle_pedido=cargar_registros_obj("select * from stock join articulo on stock_art_id=art_id where stock_log_id=$log_id",false);
    // }
   // echo json_encode($detalle_pedido);
?>


