<?php

    require_once('../../conectar_db.php');
    set_time_limit(0);
    ini_set("memory_limit","512M");
    pg_query($conn, "START TRANSACTION;");
		
	// Ingresa Documento
	$prov_id=($_POST['proveedor_encontrado']*1);
	
	$interm=pg_escape_string(utf8_decode($_POST['prod_interm']));
	
	if($interm=='') {
		$orden_compra=pg_escape_string($_POST['orden_compra_num']);
		$orden_id=($_POST['orden_id']*1);
	} else {
		$orden_compra=$interm;
		$orden_id='null';		
	}
	
	
	$numero=$_POST['bodega_doc_asociado_num'];
	$tipo=$_POST['bodega_doc_asociado']*1;
	$doc_id=($_POST['doc_id']*1);
	$num_art=($_POST['artnum']);
    $observaciones=pg_escape_string(utf8_decode($_POST['observaciones']));
    $fecha_recep=pg_escape_string($_POST['fecha_recep']);

    if(isset($_POST['iva_incl']))
    {
        $descuento=($_POST['doc_descuento']*1)/$_global_iva;
    } 
    else
    {
        $descuento=($_POST['doc_descuento']*1);
    }
		
    if($doc_id==0)
    {
        pg_query($conn, "
		INSERT INTO 
		documento
		VALUES (
		DEFAULT,
		$prov_id,
		$tipo,
		$numero,
        $_global_iva,
        $descuento,
        $orden_id,
        '$orden_compra', 
        '$observaciones',
        null,
        '$fecha_recep'
        )
		");
        $doc_query="CURRVAL('documento_doc_id_seq')";
	}
    else
    {
        $doc_query=$doc_id;
    }

	$id_bodega=$_POST['prod_bodega']*1;
    // Ingresa Log
    pg_query($conn, "
	INSERT INTO
	logs
	VALUES(
	DEFAULT,
	".($_SESSION['sgh_usuario_id']*1).",
	1,
	CURRENT_TIMESTAMP,
	0,
	$doc_query,
	NULL, '', max_folio_recep($id_bodega), $id_bodega
	)
    ");

    // Ingresa Artículos
    $probar_hasta=($_POST['artnum']*1);
    for($i=0;$i<$probar_hasta;$i++)
    {
        // Verifica si la fila fue ingresada en la planilla:
        if(isset($_POST['id_art_'.$i]))
        {
            $id_articulo=($_POST['id_art_'.$i]*1);
            $cantidad=($_POST['cant_art_'.$i])*1;
            if($_POST['fecha_art_'.$i]!='')
            {
                $vencimiento="'".pg_escape_string($_POST['fecha_art_'.$i])."'";
                // Vence...
            }
            else
            {
                $vencimiento="null";
                // No Vence...
            }

			if(isset($_POST['iva_incl']))
            {
                $subtotal=($_POST['val_art_'.$i]*1)/$_global_iva;
            }
            else
            {
                $subtotal=($_POST['val_art_'.$i]*1);
            }

            pg_query($conn, "
			INSERT INTO
			stock
			VALUES (
			DEFAULT,
			$id_articulo,
			$id_bodega,
			$cantidad,
			CURRVAL('logs_log_id_seq'),
			$vencimiento,
			$subtotal
			 )
			");
				
				
				pg_query($conn, "UPDATE articulo SET 
					art_val_min=foo.art_val_min, 
					art_val_med=foo.art_val_med, 
					art_val_max=foo.art_val_max
				  FROM (SELECT art_id, art_codigo, art_glosa,
						(SELECT max(stock_subtotal/stock_cant) AS art_val_ult 
						FROM stock WHERE stock_art_id=art_id AND stock_subtotal>0) AS art_val_max,
						(SELECT min(stock_subtotal/stock_cant) AS art_val_min 
						FROM stock WHERE stock_art_id=art_id AND stock_subtotal>0) AS art_val_min,
						(SELECT avg(stock_subtotal/stock_cant) AS art_val_med 
						FROM stock WHERE stock_art_id=art_id AND stock_subtotal>0) AS art_val_med
						FROM articulo AS a1
						WHERE art_id=$id_articulo)AS foo 
				  WHERE articulo.art_id=foo.art_id;");
						
			
			pg_query($conn, "UPDATE articulo SET 
					art_val_ult=foo.art_val_ult 
				  FROM (SELECT stock.stock_art_id, stock_subtotal/stock_cant AS art_val_ult 
						FROM stock
						JOIN logs ON stock_log_id=log_id
						JOIN (SELECT stock_art_id, max(log_fecha)AS max_log_fecha 
							FROM stock
							JOIN logs ON stock_log_id=log_id
							WHERE stock_subtotal>0 AND stock_art_id=".$id_articulo."
							GROUP BY stock_art_id) AS buffer
						ON buffer.stock_art_id=stock.stock_art_id AND max_log_fecha=log_fecha)AS foo
				WHERE art_id=foo.stock_art_id;");
				
				
			if(isset($_POST['serie_art_'.$i])) 
            {
                $series = $_POST['serie_art_'.$i];
                if(trim($series)!='')
                {
                    $nser = explode(',', $series);
                    for($ii=0;$ii<count($nser);$ii++)
                    {
                        $nro = $nser[$ii];
                        if(trim($nro)!='')
                            pg_query($conn, "
                            INSERT INTO stock_refserie VALUES (
                            DEFAULT,
                            CURRVAL('stock_stock_id_seq'),
                            '".pg_escape_string($nro)."'
                            );
                            ");
                    }
                }
            }
            If(isset($_POST['partida_art_'.$i]))
            {
                $partidas = $_POST['partida_art_'.$i];
                if(trim($partidas)!='')
                {
                    $npar = explode(',', $partidas);
                    for($ii=0;$ii<count($npar);$ii++)
                    {
                        $nro = $npar[$ii];
                        if(trim($nro)!='')
                            pg_query($conn, "
                            INSERT INTO stock_refpartida VALUES (
                            DEFAULT,
                            CURRVAL('stock_stock_id_seq'),
                            '".pg_escape_string($nro)."'
                            );
                            ");
            
                    }
                }
            }

            if(comprobar_talonario($id_articulo))
            {
                $infotalonario = $_POST['talonario_'.$i];
                $tals = explode('|', $infotalonario);
                for($i=0;$i<count($tals);$i++)
                {
                    $tal=explode('-', $tals[$i]);
                    if(count($tal)==2)
                    {
                        pg_query($conn, "
                        INSERT INTO talonario VALUES (
                        DEFAULT,
                        0,
                        ".tipo_talonario($id_articulo).",
                        NULL,
                        ".$tal[0].",
                        ".$tal[1].",
                        0,
                        current_timestamp,
                        $id_bodega,
                        '',
                        0
                        );
                        ");
                    }
                }
            }
        }
        // Fin del FOR
    }
    // Actualiza estados de los pedidos asociados a la orden de compra 
    // seleccionada.

    if($orden_id) {
      
        pg_query($conn, "
			SELECT actualizar_info_pedido(pedido.pedido_nro)
			FROM orden_pedido    
			JOIN pedido ON pedido.pedido_id=orden_pedido.pedido_id
			WHERE orden_id=$orden_id
		");
        // Concluye Transacción
        
        $chq=cargar_registros_obj("
			SELECT 
			ordetalle_id, ordetalle_art_id, ordetalle_cant, COALESCE(SUM(stock_cant),0) AS recepcion 
			FROM orden_compra 
			LEFT JOIN documento ON doc_orden_id=orden_id
			LEFT JOIN logs ON log_doc_id=doc_id
			LEFT JOIN orden_detalle ON ordetalle_orden_id=orden_id
			LEFT JOIN stock ON stock_log_id=log_id AND stock_art_id=ordetalle_art_id
			WHERE orden_id=$orden_id
			GROUP BY ordetalle_id, ordetalle_art_id, ordetalle_cant;
		");
		
		$ok=0; $ok2=0;
		
		for($i=0;$i<sizeof($chq);$i++) {
			
			if($chq[$i]['recepcion']*1>=$chq[$i]['ordetalle_cant']*1) $ok++;
			if($chq[$i]['recepcion']*1>0) $ok2++;
			
		}
		
		if($ok==sizeof($chq)) 
			$estado=2;
		elseif($ok2>0 OR $ok>0) 
			$estado=1;
		else 
			$estado=0;
		
		pg_query("UPDATE orden_compra SET orden_estado=$estado WHERE orden_id=$orden_id;");
        
     }

    
    if($num_art!=0)
    {
        
        for($ii=0;$ii<$num_art;$ii++)
        {
            
            if(isset($_POST['check_ter_'.$ii]))
            {
                $cod_art=pg_escape_string($_POST['cod_art_'.$ii]);
                $pedido_nro=$_POST['pedido_nro_'.$ii];
                pg_query($conn, "select actualizar_art_pedido($pedido_nro,'$cod_art')");

            }

        }


    }



    if($doc_id==0)
    {
        $doc_id = pg_query($conn, "SELECT CURRVAL('documento_doc_id_seq');");
		$doc_arr = pg_fetch_row($doc_id);
		$doc_id_m = $doc_arr[0];
    }
    else
    {
        $doc_id_m = $doc_id;
    }
    pg_query($conn, "COMMIT;");
    print(json_encode(Array(true, $doc_id_m)));
    
?>
