<?php

	  require_once('../../conectar_db.php');


		$bolnum = $_POST['bolnum']*1;
		$func_id = $_POST['func_id']*1;
		$prestaciones=json_decode($_POST['prestaciones']);	
		$mon_dev = $_POST['mon_dev_total']*-1;
		$ac_id = ($_POST['ac_id']*1);
		$fecha='current_timestamp';	
		$devol_id="NEXTVAL('devolucion_boletines_devol_id_seq')";
		
		
		pg_query($conn, "
				INSERT INTO devolucion_boletines
				VALUES (
				$devol_id,
				$fecha,
				$ac_id,
				$mon_dev,
				$bolnum,
				null,
				$func_id,
				null
				)
			");
			$currval="CURRVAL('devolucion_boletines_devol_id_seq')";
		
		$devol=cargar_registro("SELECT devol_id FROM devolucion_boletines WHERE devol_id=$currval;");			
		
		$ddet_id="NEXTVAL('devolucion_boletin_detalle_ddet_id_seq')";
		for($i=0;$i<sizeof($prestaciones);$i++)
		{
			
			$bdet_id=$prestaciones[$i]->bdet_id*1;		
			$bdet_valor=$prestaciones[$i]->bdet_valor*1;
			$bdet_mon_dev=$prestaciones[$i]->mon_dev*1;
			pg_query($conn, "
				INSERT INTO devolucion_boletin_detalle
				VALUES (
				$ddet_id,
				$bolnum,
				$bdet_mon_dev,			
				$ac_id,
				$bdet_id,
				".$devol['devol_id'].",
				($bdet_valor*-1)
				)
			");
		}
		
			
	print(json_encode(array(true,$devol['devol_id'])));
?>
