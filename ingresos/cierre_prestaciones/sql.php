<?php

  require_once('../../conectar_db.php');
  
  $func_id=$_SESSION['sgh_usuario_id']*1;
  $filtro=$_POST['seleccion'];
  $fecha1=pg_escape_string($_POST['fecha11']);
  $fecha2=pg_escape_string($_POST['fecha22']);
  $ids=$_POST['nominas'];
   $ids2="'".$_POST['nominas']."'";
 
  $t=explode('|',$ids);

pg_query("START TRANSACTION;");
for($i=0;$i<sizeof($t);$i++)
{
	pg_query("		UPDATE nomina_detalle set nomd_pago=1 where nomd_id =$t[$i]");
	
}

pg_query(" INSERT into cierre_prestaciones values (DEFAULT,$ids2,$func_id,CURRENT_TIMESTAMP)");	
 $currval=cargar_registro("select cierre_id from cierre_prestaciones where cierre_nomd_ids=$ids2");
  pg_query('COMMIT;');
  
	print(json_encode(array(true,$currval['cierre_id']*1)));


?>
