<?php require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$act=$_POST['act'];
	//$ids=explode('|',trim($_POST['ids'],'| '));
	$nid=$_POST['nid'];
	$pid='PSI-'.$_POST['pid'];
	$aid=$_POST['aid'];
	$eid=$_POST['eid'];

	if($act==1){
		
		$chk=cargar_registro("SELECT * FROM archivo_fichas WHERE pac_ficha='$pid' AND arc_estado=1 ");

		if(!$chk){
			pg_query("INSERT INTO archivo_fichas (nom_id,doc_id,esp_id,pac_id,pac_ficha,arc_estado,arc_fecha_asigna,arc_fecha_salida)
					  SELECT nom_id,nom_doc_id,nom_esp_id,pac_id,pac_ficha,1 AS arc_estado,COALESCE(nomd_fecha_asigna,nom_fecha),current_timestamp FROM nomina_detalle
					  LEFT JOIN nomina USING (nom_id)
					  LEFT JOIN pacientes USING (pac_id)
					  WHERE pac_ficha='$pid' AND nomd_id=$nid");
		}
	}else if($act==2){
		
		$chk=cargar_registro("SELECT * FROM archivo_fichas WHERE arc_id=$aid");
		
		if($chk){
			pg_query("UPDATE archivo_fichas SET arc_estado=2,arc_fecha_entrada=current_timestamp WHERE arc_id=$aid;");
			}
	}elseif($act==3){
		
		$chk=cargar_registro("SELECT * FROM archivo_fichas WHERE pac_ficha='$pid' AND arc_estado=1 ");

		if(!$chk){
			pg_query("INSERT INTO archivo_fichas (nom_id,doc_id,esp_id,pac_id,pac_ficha,arc_estado,arc_fecha_asigna,arc_fecha_salida)
					  SELECT -1,doc_id,esp_id,pac_id,pacientes.pac_ficha,1 AS arc_estado,fesp_fecha,current_timestamp 
					  FROM ficha_espontanea
					  LEFT JOIN pacientes USING (pac_id)
					  LEFT JOIN doctores USING (doc_id)
					  WHERE fesp_id=$eid");
					  
			pg_query("UPDATE ficha_espontanea SET fesp_estado=1 WHERE fesp_id=$eid");
					 
		}
	}

?>
