<?php 

	require_once('../../conectar_db.php');
	
	 $id_solicitud=$_POST['sol_id']*1;
	 $valor=$_POST['valor']*1;
	 $tipo=$_POST['tipo_val']*1;
	 
	 $solicitud = cargar_registro('SELECT * FROM solicitud_compra 
	 											WHERE sol_id='.$id_solicitud);
	 											
	 if($valor==2 OR $valor==3) $tipo=1; else $tipo=2;
	 
	 if(	$valor==2 AND 
	 		$solicitud['sol_tipo']==1 ) {
	 		
			$item=cargar_registro("
				SELECT * FROM item_presupuestario
				WHERE item_codigo='".pg_escape_string($solicitud['sol_item_codigo'])."'			
			");
			
			if($item['item_centro_ruta']!='') {
				
				$estado=9;				
				
			} else {
			
				$estado=2;			
				
			}	 		
	 		
	 } else {

		 if($valor==7) { 
		 	if($solicitud['sol_tipo']==0)
		 		$estado=7;
		 	else 
		 		$estado=6;
		 } else $estado=$valor;
	
	 }
	 
	 if(	
	 		( $tipo==1 AND
	 		(_func_permitido(61,$solicitud['sol_bod_id']) OR 
          _func_permitido_cc(61,$solicitud['sol_centro_ruta'])) )
          OR
         ( $tipo==2 AND _cax(62) )
         
         ) {
	   
			pg_query($conn, "
      	UPDATE solicitud_compra 
      	SET 
      	sol_fecha$tipo=now(), 
      	sol_func_id$tipo=".($_SESSION['sgh_usuario_id']*1).",
      	sol_estado=$estado 
      	WHERE sol_id=$id_solicitud
      	");
      
      
      $auto=cargar_registro("
        
        SELECT 
        
        func_nombre, 
        date_trunc('second', sol_fecha$tipo) AS fecha 
        
        FROM solicitud_compra
        
        LEFT JOIN funcionario ON 
                  solicitud_compra.sol_func_id$tipo=funcionario.func_id
        WHERE 
                  sol_id=$id_solicitud
        
        ");
      
		if($tipo==2 AND $solicitud['sol_tipo']==0) {
		
			pg_query("INSERT INTO pedido VALUES (
				DEFAULT,
				nextval('global_numero_pedido'),
				now(),
				".$solicitud['sol_func_id'].",
				".$solicitud['sol_func_id2'].",
				0, 0,
				'',
				0, true, false,
				'".pg_escape_string($solicitud['sol_centro_ruta'])."'		
			);");	
			
			$sd=cargar_registros_obj("SELECT * FROM solcompra_detalle 
												WHERE sol_id=$id_solicitud");
			
			for($i=0;$i<sizeof($sd);$i++) {
			
				pg_query("
				INSERT INTO pedido_detalle VALUES (
					DEFAULT,
					CURRVAL('pedido_pedido_id_seq'),
					".$sd[$i]['art_id'].",
					".$sd[$i]['sol_cant'].",
					false				
				);				
				");			
				
			}
			
			pg_query("UPDATE solicitud_compra 
			SET pedido_id=CURRVAL('pedido_pedido_id_seq')
			WHERE sol_id=$id_solicitud;");
			
		}      
      
      print(json_encode(Array   ( true, 
                                  $valor, 
                                  htmlentities($auto['func_nombre']), 
                                  htmlentities($auto['fecha'])
                                )
                        ));

		
            
   } else {

      print('ERROR: Funcionario no tiene permisos para realizar esta operaci&oacute;n.');

   }
  
?>