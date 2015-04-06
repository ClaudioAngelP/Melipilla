<?php
    require_once('config.php');
    require_once('sigh.php');
	$consulta="select pedido.pedido_id,pedido.pedido_nro,
	(select log_id from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1)as log_id,
	(select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1)as log_fecha,
	(now()-(select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1))  as dif_fechas,
	((extract(epoch from age(now(),((select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1))))/60)/60)as dif_horas
	from pedido 
	where pedido_estado=1 and destino_bod_id!=0
	and ((extract(epoch from age(now(),((select log_fecha from logs where log_id_pedido=pedido_id order by log_fecha desc limit 1))))/60)/60)>=36
	order by log_fecha";
	$reg_pedidos=cargar_registros_obj($consulta);
	if($reg_pedidos)
	{
		pg_query("START TRANSACTION;");
		for($i=0;$i<count($reg_pedidos);$i++)
		{
			print('<br>');
			print($i."-".$reg_pedidos[$i]['pedido_nro']);
			print('<br>');
			print("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].", ".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'');");
			print('<br>');
                        print('<br>');
			print("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
                        print('<br>');
			pg_query("INSERT INTO pedido_log_rev VALUES (DEFAULT, ".$reg_pedidos[$i]['pedido_id'].",".$reg_pedidos[$i]['log_id'].", current_timestamp, 7,'TERMINADO AUTOMATICAMENTE POR SISTEMA.');");
			pg_query("UPDATE pedido_detalle SET pedidod_estado=true WHERE pedido_id=".$reg_pedidos[$i]['pedido_id']."");
			pg_query("UPDATE pedido SET pedido_estado=2 WHERE pedido_id=".$reg_pedidos[$i]['pedido_id'].";");
		}
		pg_query("COMMIT;");
	}
    
?>
