<?php
    require_once('../../conectar_db.php');
    $pedido_id=($_POST['id_pedido']*1);
    $log_id=($_POST['id_log']*1);
    $cantidad=($_POST['cantidad']*1);
    if($_POST['accion']!='aceptar')
    {
        pg_query($conn, "START TRANSACTION;");
        pg_query($conn, "UPDATE pedido SET pedido_estado=0 WHERE pedido_id=".$pedido_id.";");
        for($i=0;$i<$cantidad;$i++)
        {
            $id_art = $_POST['id_art_'.$i];
            pg_query($conn, "SELECT rechazar_stock_pedido(".$pedido_id.", ".$log_id.", ".$id_art.");");
        }
        pg_query($conn, "INSERT INTO pedido_log_rev VALUES (DEFAULT,$pedido_id,$log_id, current_timestamp, ".($_SESSION['sgh_usuario_id']*1).",'');");
        pg_query($conn, "COMMIT;");
        die('OK');
    }
    pg_query($conn, "START TRANSACTION;");
    // Si no hay articulos sin aceptar se marcará el pedido
    // como aceptado por completo.
    // $estado=2;
    for($i=0;$i<$cantidad;$i++)
    {
        $id_art = $_POST['id_art_'.$i];
        $estado_art = isset($_POST['acepta_art_'.$i]);
        if(!$estado_art)
        {
            // En caso de no haber marcado algo de la lista
            // el pedido será devuelto con el o los articulos 
            // con problemas.
      
      pg_query($conn, "
      SELECT rechazar_stock_pedido(".$pedido_id.", ".$log_id.", ".$id_art.");
      ");
      
      //$estado=0;
    
    } else {

      pg_query($conn, "
      UPDATE pedido_detalle SET pedidod_estado=true 
      WHERE 
      pedido_id=".$pedido_id."
      AND
      art_id=".$id_art."
      ");

		
	}
  
  }

  pg_query($conn, "
    INSERT INTO pedido_log_rev VALUES (
    DEFAULT,
    $pedido_id, 
    $log_id, 
    current_timestamp, 
    ".($_SESSION['sgh_usuario_id']*1).",
    '');
  ");
 
  
  $chq=pg_query("
  
	SELECT COUNT(*) AS pendientes FROM (
  
	SELECT *,

	COALESCE((select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	join pedido_log_rev using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0),0) AS recepcionado,

	COALESCE((select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	left join pedido_log_rev AS plog using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0 AND plog.pedidolog_id is null),0) AS no_recepcionado

	FROM pedido_detalle AS pd1 
	JOIN articulo USING (art_id)
	WHERE pedido_id=$pedido_id
	
	) AS foo WHERE pedidod_cant>recepcionado;
	
  ");
  
    $chk=pg_fetch_assoc($chq);
  
    if($chk['pendientes']*1>0)
    {
	if(isset($_POST['check_end']))
        {
             $estado=2;
        }
        else
        {
            $estado=0; 
        }
    }
    else
    {
        $estado=2;
    }
  
  pg_query($conn, "
    UPDATE pedido SET pedido_estado=".$estado." WHERE pedido_id=".$pedido_id.";
  ");
  
  $bod=cargar_registro("SELECT * FROM pedido WHERE pedido_id=".$pedido_id.";");
  if($bod['origen_bod_id']==2){
  	pg_query("INSERT INTO mensajeria_integraciones VALUES(DEFAULT,12,".$pedido_id.",current_timestamp,null,0,null);");
  }
    
  pg_query($conn, "COMMIT;");
  
  print(json_encode(Array('OK',$log_id)));

?>
