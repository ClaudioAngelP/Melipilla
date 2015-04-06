<?php

  require_once('../../conectar_db.php');
  
  $func_id=$_SESSION['sgh_usuario_id']*1;
  $filtro=$_POST['seleccion'];
  $fecha1=pg_escape_string($_POST['fecha11']);
  $fecha2=pg_escape_string($_POST['fecha22']);
  $ids=$_POST['ids'];
/*
pg_query("START TRANSACTION;");
pg_query("
		UPDATE nomina_detalle set nomd_pago=1 where nomd_id in ($ids)");
	 
  pg_query('COMMIT;');
  */
	print(json_encode(array(true,'ok')));


?>
