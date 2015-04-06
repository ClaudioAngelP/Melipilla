<?php	require_once('../../conectar_db.php');
	
	$ids=$_POST['ids'];

	$origen=$_POST['origen'];
	$destino=$_POST['destino'];
	
	$ini_r=substr($origen,0,1);
	if($ini_r!='.')$b=cargar_registro("SELECT bod_id,'' AS centro_ruta FROM bodega WHERE bod_id=$origen");
	else $b=cargar_registro("SELECT 0 AS bod_id,centro_ruta FROM centro_costo WHERE centro_ruta='$origen'");
	
	if(_func_permitido(29,$b['bod_id']) OR 
        _func_permitido_cc(29,$b['centro_ruta']))
      $autoriza='true'; else $autoriza='false';
	
pg_query("START TRANSACTION;");	
	
  $pedido = pg_query("
    INSERT INTO pedido VALUES (
    DEFAULT,
    nextval('global_numero_pedido'),
    current_timestamp,
    ".($_SESSION['sgh_usuario_id']*1).",
    ".($_SESSION['sgh_usuario_id']*1).",
    ".$b['bod_id'].",
    $destino,
    '".utf8_decode('REPOSICIÓN GENERADA MANUALMENTE')."',
    0, $autoriza,false,'".$b['centro_ruta']."'
    )
  ");
  
  	$lista=explode(',',trim($ids,', '));
  	
  	//print_r($lista);
  	  		
	for($i=0;$i<sizeof($lista);$i++) {

		
		$r=$lista[$i];
				
		$cant=$_POST['cant_'.$r]*1;
/*
		
		if(isset($_POST['art_pedidos_'.$r])) {

			if(trim($_POST['art_pedidos_'.$r])!=''){
			
			$pedidos=explode(',',trim($_POST['art_pedidos_'.$r],', '));
			$cerrar='';	
					
				for($u=0;$u<sizeof($pedidos);$u++){
					$pedido=explode('|',$pedidos[$u]);			
					$cerrar.=$pedido[0].',';	
				}
						
				pg_query("UPDATE pedido SET pedido_estado=2 WHERE pedido_id IN (".trim($cerrar,', ').")");
			}
			
		}
		*/
		if($cant>0)
			pg_query("INSERT INTO pedido_detalle (pedido_id, art_id, pedidod_cant, pedidod_estado) 
			VALUES (
			CURRVAL('pedido_pedido_id_seq'),
			$r,
			$cant,
			false
			);");
		
	}
/*
	if($origen*1)
 		$cc='';
 	else
 		$cc="AND origen_centro_ruta='".$b['centro_ruta']."'";
 		
 	pg_query("UPDATE pedido SET pedido_estado=2 
  				WHERE pedido_nro<CURRVAL('global_numero_pedido') AND
  				destino_bod_id=$destino AND 
  				origen_bod_id=".$b['bod_id']." $cc
  				AND pedido_estado=0  
		");
*/
		
  $pedido_q = pg_query("SELECT CURRVAL('global_numero_pedido')");
  
 	
				
	pg_query("COMMIT;");
  
  list($pedido_nro)=pg_fetch_row($pedido_q);
    
  print(json_encode(Array(true, $pedido_nro)));

?>
