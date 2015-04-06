<?php
    error_reporting(E_ALL);
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    //--------------------------------------------------------------------------
    //pg_query("START TRANSACTION;");
    $registros=cargar_registros_obj("select * from (select count(*)as cant,log_id from pedido_log_rev group by log_id)as foo where cant>1 order by cant desc");
	if($registros)
	{
		for($i=0;$i<count($registros);$i++)
		{
			$reg_logpedido=cargar_registros_obj("select * from pedido_log_rev where log_id=".$registros[$i]['log_id']." order by pedidolog_fecha");
			if($reg_logpedido)
			{
				$string_id="";
				for($x=1;$x<count($reg_logpedido);$x++)
				{
					$string_id.=$reg_logpedido[$x]['pedidolog_id'].",";
				}
				$string_id=trim($string_id, ',');
				print("<br>");
				print("delete from pedido_log_rev where pedidolog_id in (".$string_id.");");
				print("<br>");
			}
		}
	}
    //pg_query("COMMIT");
?>
