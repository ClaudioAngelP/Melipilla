<?php

    require_once("../../conectar_db.php");

		pg_query($conn, "START TRANSACTION;");
		
		// Ingresa Pedido y Log de Movimiento
		
		$cantidad_mov = ($_POST['cant']*1);
		
		$origen_id=($_POST['bodega_origen']);
		$destino_id=($_POST['bodega_destino']*1);
		$instsol_id=($_POST['institucion_destino']*1);
		$tipo_presta=($_POST['tipo_prestamo']*1);
		$id_pedido=($_POST['id_pedido']*1);
		$nro_pedido=($_POST['numero_pedido']*1);
		$tipomov=($_POST['tipo_movimiento']*1);
		$comentario=iconv("UTF-8", "ISO-8859-1", $_POST['comentarios']);
		
		$glosa_mov=array();
		
		
		// Si es un centro de costo o una bodega
		
		if(strstr($origen_id,'.')) {
    
      // Centro de Costo
    
      $tabla_stock='stock_servicios';
      $lotes_vigentes='lotes_vigentes_cc';
      $centro_ruta='\''.pg_escape_string($origen_id).'\'';
      $ubica=$centro_ruta.'::text';
      $origen_id='-1';
      
    } else {
    
      // Bodega
      
      $tabla_stock='stock';
      $lotes_vigentes='lotes_vigentes';
      $origen_id=$origen_id*1;
      $centro_ruta='';
      $ubica=$origen_id.'::bigint';
    
    }
		
		switch($tipomov) {
        case 0: $tipo=2; break;
        case 1: $tipo=6; break;
        case 2: $tipo=8; break;
        case 3: $tipo=7; $id_pedido=0; break;
        case 5: $tipo=5; $id_pedido=0; break;
        case 6: $tipo=20; $id_pedido=0; break;
        case 7: 
          $tipo=15; 
          if($id_pedido==0) {
            $centro_costo = pg_escape_string($_POST['centro_costo']);
  		      $centro_servicio = pg_escape_string($_POST['centro_servicio']);
  		      if($centro_servicio!='-1')  $centro_costo=$centro_servicio;
  		    } else {
            $pedido=cargar_registro("SELECT * FROM pedido 
                                      WHERE pedido_id=$id_pedido");
            $centro_servicio=$pedido['origen_centro_ruta'];
            $centro_costo=$centro_servicio;
          }
          $centro=cargar_registro("SELECT * FROM centro_costo WHERE centro_ruta='$centro_servicio'");
        break;

        case 8:
          $tipo=16; 
          $centro_costo = pg_escape_string($_POST['centro_costo']);
  		    $centro_servicio = pg_escape_string($_POST['centro_servicio']);
  		    if($centro_servicio!='-1')  $centro_costo=$centro_servicio;
            $centro=cargar_registro("SELECT * FROM centro_costo WHERE centro_ruta='$centro_servicio'");
        break;
        case 9: $tipo=3; $id_pedido=0; break;
    }
		
		// Genera orden de Pedido en caso de ser necesario.
    
    //$centro=cargar_registro("SELECT * FROM centro_costo WHERE centro_ruta='$centro_servicio'");
    
    if($tipo==15 AND $centro['centro_stock']=='t') $tipo=18;
    
    
    
    if($id_pedido==0 and ($tipo==2 or $tipo==15 or $tipo==18)) {
		
		if($tipo==2) $estado_pedido=1;

    if($tipo==6) { $estado_pedido=2; $destino_id=0; }

    if($tipo==15 or $tipo==18) {
      
      if($centro['centro_stock']=='t') {
      
        // El centro de costo mantiene un stock.
        
        $estado_pedido=1;
        
      } else {
        
        // El centro de costo recibe medicamentos pero no almacena stock
        // se asume que el stock fue utilizado.
        
        $estado_pedido=2; 
        
      }

      $destino_id=0;

    }
    
    // Crea el pedido en caso de no existir...
    
    pg_query($conn, "
    INSERT INTO pedido VALUES(
    DEFAULT,
    nextval('global_numero_pedido'),
    current_timestamp,
    ".($_SESSION['sgh_usuario_id']*1).",
    0,
    $destino_id,
    $origen_id,
    'Creado junto con traslado de art�culos asociado.',
    $estado_pedido,
    true
    )
    ");
    
    $pedido_query = "CURRVAL('pedido_pedido_id_seq')";
    
    $devolver_pedido=pg_query($conn, "SELECT CURRVAL('pedido_pedido_id_seq')");
    $pedido_arr=pg_fetch_row($devolver_pedido);
    $id_pedido_devolver=$pedido_arr[0];
    
    } else {
    
      if($tipo==2 or $tipo==15 or $tipo==18) {
        $pedido_query = $id_pedido;
        $id_pedido_devolver = $id_pedido;
      } else {
        $pedido_query = '-1';
      }
    
    }
		
		// Ingresa Log
		
		pg_query($conn, "
		INSERT INTO 
		logs
		VALUES (
		DEFAULT,
		".($_SESSION['sgh_usuario_id']*1).",
		$tipo,
		current_timestamp,
		0,
		NULL,
		".$pedido_query.",
    '".pg_escape_string($comentario)."' )
		");	

    if($tipo==6) {
    
      pg_query("
      INSERT INTO cargo_instsol VALUES (
      CURRVAL('logs_log_id_seq'),
      $instsol_id,
      $tipo_presta
      )
      ");
    
    }
		
		if($tipo==15 or $tipo==18) {
    
      pg_query("
      INSERT INTO cargo_centro_costo VALUES (
      CURRVAL('logs_log_id_seq'),
      '".pg_escape_string($centro_costo)."'
      )
      ");
    
    }
    
    if($tipo==16) {
    
      pg_query("
      INSERT INTO cargo_centro_costo VALUES (
      CURRVAL('logs_log_id_seq'),
      '".pg_escape_string($centro_costo)."'
      )
      ");
    
      $devolver_operacion= pg_query($conn, 
                        "SELECT CURRVAL('logs_log_id_seq')");
      $operacion_arr=pg_fetch_row($devolver_operacion);
      $id_operacion_devolver=$operacion_arr[0];
    
    }
    

    if($tipo==6) {
      $devolver_operacion= pg_query($conn, 
                        "SELECT CURRVAL('logs_log_id_seq')");
      $operacion_arr=pg_fetch_row($devolver_operacion);
      $id_operacion_devolver=$operacion_arr[0];
    }
		
		
		// Marca pedido como respondido.
		
    if($id_pedido>0 and ($tipo==2 or $tipo==15 or $tipo==18))
    {
        // Marca si existe...
        /////////////////////
        /*
	if($tipo==15 or $tipo==18)
        {
            if($centro['centro_stock']=='t')
            {
                // El centro de costo mantiene un stock.
		pg_query("UPDATE pedido SET pedido_estado=1 WHERE pedido_id=$id_pedido;");
            }
            else
            {
                // El centro de costo recibe medicamentos pero no almacena stock
		// se asume que el stock fue utilizado.
		pg_query("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=$id_pedido;");
            }
        }
        else
        {
            pg_query("UPDATE pedido SET pedido_estado=1 WHERE pedido_id=$id_pedido;");
        }
        */
        pg_query($conn,"UPDATE pedido SET pedido_estado=1 WHERE pedido_id=$id_pedido");
		
    }
    
    // Operaciones Espec�ficas x Movimiento
    ///////////////////////////////////////
    
    
    
    // Movimiento para Traslado de Productos
    /////////////////////////////////////////
    
    if($tipomov==0 or $tipomov==7)
    for($i=0;$i<=$cantidad_mov;$i++) {
		
			if(isset($_POST['id_art_'.$i])) {
				
				// Obtiene las fechas de vencimiento de los lotes correspondientes 
        // al producto.
				
				$id_articulo = ($_POST['id_art_'.$i]*1);
				$cantidad = ($_POST['cant_art_'.$i]*1);
				
				
        // En caso de que no existiera el pedido,
        // va ingresando el detalle paralelamente a lo que se
        // ingreso en el traslado.
        
        if($id_pedido==0 and ($tipo==2 or $tipo==15 or $tipo==18)) 
        {
        
        pg_query($conn, "
        INSERT INTO pedido_detalle VALUES(
        DEFAULT,
        CURRVAL('pedido_pedido_id_seq'),
        $id_articulo,
        0
        )
        ");
        
        } else if ($tipo==2 or $tipo==15 or $tipo==18) {
        
        pg_query($conn, "
        SELECT asociar_pedido_articulo($id_pedido, $id_articulo);
        ");
        
        }
    
				
				// Realiza un listado de lotes a trasladar de acuerdo
				// a la opci�n de movimiento seleccionada...
				
				$lotes = pg_query($conn, "
				SELECT * FROM $lotes_vigentes($id_articulo, $ubica);
        ");
				
				
				$cant=$cantidad;
				
				// Ingresa movimiento correspondiente a los lotes que
				// ser�n movidos... Un movimiento por cada lote diferente.
				// Movimiento implica rebaja en bodega de origen y 
				// aumento en bodega de destino.
				
				$bloquea=0;
				
				while($cant>0) {
				
				  $bloquea++;
				  
				  if($bloquea==300) {
				    // Previene Bucle Infinito y Posibles DoS.
				    
				    $respuesta[0]=false;
				    $respuesta[1]='Error Inesperado.';
					die(json_encode($respuesta));
            
					}
				
					// $dato[0] = Fecha
					// $dato[1] = Cantidad
					
					$registro_lote=pg_fetch_row($lotes);
					
					if($cant>=$registro_lote[0]) {
						$cnt=$registro_lote[0];
					} else {
						$cnt=$cant;
					}
					
					if($registro_lote[1]!='') 	
						$vencimiento="'".$registro_lote[1]."'";
					else 							
						$vencimiento="null";
					
					
					// Rebaja de la ubicacion de origen.
					if(!pg_query($conn, "
					INSERT INTO 
					$tabla_stock
					VALUES (
					DEFAULT,
					$id_articulo,
					$ubica,
					-($cnt),
					CURRVAL('logs_log_id_seq'),
					$vencimiento,
					0
					)
					")) {
						pg_query("ROLLBACK;");
						exit("
					INSERT INTO 
					$tabla_stock
					VALUES (
					DEFAULT,
					$id_articulo,
					$ubica,
					-($cnt),
					CURRVAL('logs_log_id_seq'),
					$vencimiento,
					0
					)
					");
					}
					

					// Aumenta en la ubicacion de destino.
					// Excepto en movimientos de pr�stamos externos y
					// despachos a servicios sin stock.
					
          if($tipomov!=1 and $tipomov!=3 and $tipomov!=7)
          
          pg_query($conn, "
					INSERT INTO 
					stock
					VALUES (
					DEFAULT,
					$id_articulo,
					$destino_id,
					$cnt,
					CURRVAL('logs_log_id_seq'),
					$vencimiento,
					0
          )
					");
					
					// Si el centro de costo almacena stock lo
					// ingresa en la tabla stock_servicios
					
					if($tipomov==7 AND $centro['centro_stock']=='t') {
          
					pg_query($conn, "
						INSERT INTO 
						stock_servicios
						VALUES (
						DEFAULT,
						$id_articulo,
						'$centro_servicio',
						$cnt,
						CURRVAL('logs_log_id_seq'),
						$vencimiento,
						0
						)
  					");
          
          }
					
            // Si es un traslado y el articulo es un talonario de recetas
            // cambia la ubicacion del talonario en la tabla de talonarios.
            if($tipomov==0 or $tipomov==7)
            {
                if(comprobar_talonario($id_articulo))
                {
                    $datal = $_POST['tal_art_'.$i];
                    $tals = explode('|',$datal);
                    for($t=0;$t<=count($tals);$t++)
                    {
                        // $taldetalle[0] = ID del talonario
                        // $taldetalle[1] = ID del funcionario/m�dico
                        // (solo para despacho a servicios)
                        if(!isset($tals[$t])) break;
                        $taldetalle = explode('-', $tals[$t]);
                        if(count($taldetalle)!=2) break;
                        if($tipomov==0)
                        {
                            $id_pedidodetalle = pg_query("SELECT CURRVAL('pedido_detalle_pedidod_id_seq')");
                            $id_pedidod = pg_fetch_result($id_pedidodetalle, 0, 0);
                            pg_query($conn,
                                "UPDATE talonario SET
                                talonario_bod_id=".$destino_id.",
                                talonario_pedidod_id=".$id_pedidod."
                                WHERE
                                talonario_id=".$taldetalle[0]);
                        }
                        if($tipomov==7)
                        {
                            $id_pedidodetalle = pg_query("SELECT CURRVAL('pedido_detalle_pedidod_id_seq')");
                            $id_pedidod = pg_fetch_result($id_pedidodetalle, 0, 0);
                            if(funcionario_talonario($id_articulo)!=0)
                                $q_func = "talonario_func_id=".$taldetalle[1].",";
                            else
                                $q_func = "";
                            pg_query($conn,
                            "UPDATE talonario SET
                            ".$q_func."
                            talonario_centro_ruta='".$centro_costo."',
                            talonario_estado=1, talonario_pedidod_id=$id_pedidod
                            WHERE talonario_id=".$taldetalle[0]);
                        }
                    }
                }
            }
					
					$num=count($glosa_mov);
					
					$glosa_mov[$num][0] = $id_articulo;
          $glosa_mov[$num][1] = $cnt;
          $glosa_mov[$num][2] = $registro_lote[1]; 
					
					$cant-=$cnt;


				}
				
			}
		
		}
		if($id_pedido==0 AND !$destino_id=0 AND $tipomov==7){
		pg_query($conn, "
					INSERT INTO pedido_log_rev VALUES (
					DEFAULT,
 					CURRVAL('pedido_pedido_id_seq'), 
 					CURRVAL('logs_log_id_seq'), 
 					current_timestamp, 
 					".($_SESSION['sgh_usuario_id']*1).",
 					'');
  					");
  		}
		// Movimiento para Ingreso por Excedente/Donaci�n
		// Movimiento para Entrada v�a Pr�stamo
		/////////////////////////////////////////////////
		
		if($tipomov==4 or $tipomov==5 or $tipomov==6 or $tipomov==8 or $tipomov==9 or
          ($tipomov==1 and $tipo_presta==0))
		  for($i=0;$i<=($cantidad_mov-1);$i++) {
    
        $id_articulo = ($_POST['id_art_'.$i]*1);
				$cnt = ($_POST['cant_art_'.$i]*1);

				if($_POST['fecha_art_'.$i]=='') {
          $vencimiento = "null";
				} else {
          $vencimiento = "'".pg_escape_string($_POST['fecha_art_'.$i])."'";
				}
				
				// Aumenta en la ubicacion de or�gen.
					
        pg_query($conn, "
  			INSERT INTO 
				$tabla_stock
				VALUES (
				DEFAULT,
				$id_articulo,
				$ubica,
				$cnt,
				CURRVAL('logs_log_id_seq'),
				$vencimiento,
				0 )
				");				
				
      }
    
    // Movimiento para Salida v�a Pr�stamo
    //////////////////////////////////////
    
    if($tipomov==1 and $tipo_presta==1) {
      
      for($i=0;$i<=$cantidad_mov;$i++) {
		
			if(isset($_POST['id_art_'.$i])) {
				
				// Obtiene las fechas de vencimiento de los lotes correspondientes 
				// al producto.
				
				$id_articulo = ($_POST['id_art_'.$i]*1);
				$cantidad = ($_POST['cant_art_'.$i]*1);

				// Realiza un listado de lotes a trasladar de acuerdo
				// a la opci�n de movimiento seleccionada...
				
				$lotes = pg_query($conn, "
					SELECT * FROM $lotes_vigentes($id_articulo, $ubica);
				");
				
				
				$cant=$cantidad;
				
				// Ingresa movimiento correspondiente a los lotes que
				// ser�n movidos... Un movimiento por cada lote diferente.
				// Movimiento implica rebaja en bodega de origen y 
				// aumento en bodega de destino.
				
				$bloquea=0;
				
				while($cant>0) {
				
				  $bloquea++;
				  
				  if($bloquea==300) {
				    // Previene Bucle Infinito y Posibles DoS.
				    
				    $respuesta[0]=false;
				    $respuesta[1]='Error Inesperado.';
					die(json_encode($respuesta));
            
					}
				
					// $dato[0] = Fecha
					// $dato[1] = Cantidad
					
					$registro_lote=pg_fetch_row($lotes);
					
					if($cant>=$registro_lote[0]) {
						$cnt=$registro_lote[0];
					} else {
						$cnt=$cant;
					}
					
					if($registro_lote[1]!='') 	
						$vencimiento="'".$registro_lote[1]."'";
					else 							
						$vencimiento="null";
					
					
					// Rebaja de la ubicacion de origen.
					pg_query($conn, "
					INSERT INTO 
					$tabla_stock
					VALUES (
					DEFAULT,
					$id_articulo,
					$ubica,
					-($cnt),
					CURRVAL('logs_log_id_seq'),
					$vencimiento,
					0
					)
					");
					
					$cant-=$cnt;

				}
				
				}
				
			}
			
    }
    
    
    /*
    // Movimiento para Baja por Vencimiento
    ///////////////////////////////////////
    
    if($tipomov==3 or $tipomov==2)
		for($i=0;$i<=($cantidad_mov-1);$i++) {
    
        $id_articulo = ($_POST['id_art_'.$i]*1);
				$cnt = ($_POST['cant_art_'.$i]*1);
				$vencimiento = "'".pg_escape_string($_POST['fecha_art_'.$i])."'";
				
				// Aumenta en la ubicacion de or�gen.
					
        if($tipomov!=3)
        pg_query($conn, "
  			INSERT INTO 
				stock
				VALUES (
				DEFAULT,
				$id_articulo,
				$origen_id,
				-($cnt),
				CURRVAL('logs_log_id_seq'),
				$vencimiento,
        0 )
				");
				
				
    }
    */
		
		pg_query("COMMIT;");
		$respuesta[0] = true;
		
    // Retorna el id del pedido reci�n creado en caso de ser necesario.
    // Traslados, Pr�stamos, Despachos siempre tienen un pedido
    // asociado.
    
    if($tipo==1 OR $tipo==2 OR $tipo==15 OR $tipo==18)  
                                    $respuesta[1] = $id_pedido_devolver;
		else if($tipo==16 or $tipo==6)  $respuesta[1] = $id_operacion_devolver;
    else                            $respuesta[1] = false;
		
		
    print(json_encode($respuesta));

?>
