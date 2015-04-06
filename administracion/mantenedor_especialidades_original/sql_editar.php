<?php 

	require_once('../../conectar_db.php');
	
	$esp_id	=	$_POST['esp_id']*1;

	$desc		=	pg_escape_string(utf8_decode($_POST['esp_desc']));
	$codint	=	pg_escape_string(utf8_decode($_POST['esp_codigo_int']));
	
	pg_query("START TRANSACTION;");

	if($esp_id!=0) {

		pg_query("UPDATE especialidades SET 
				esp_desc='$desc', 
				esp_codigo_int='$codint'
				WHERE esp_id=$esp_id");

		$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id");
			
	} else {
		
		pg_query("INSERT INTO especialidades VALUES (
			DEFAULT,
			'$desc',
			0,
			1,
			'',
			'$codint'		
		);");
		
		$esp=cargar_registro("SELECT * FROM especialidades 
				WHERE esp_id=CURRVAL('especialidades_esp_id_seq');");
				
		$esp_id=$esp['esp_id']*1;		
		
	}	
	
	$proc			=	isset($_POST['proce'])?'true':'false';
	$info			=	isset($_POST['informe'])?'true':'false';
	$orden		=	isset($_POST['orden'])?'true':'false';
	$equipos		=	pg_escape_string(utf8_decode($_POST['equipos']));
	$campos		=	pg_escape_string(utf8_decode($_POST['campos']));
	
	$presta		=	json_decode($_POST['presta'], true);

	$pr=cargar_registros_obj("
		SELECT * FROM procedimiento_codigo
		WHERE esp_id=$esp_id
	", true);
	
	pg_query("DELETE FROM procedimiento WHERE esp_id=$esp_id");
		
	if($proc=='true'){
		pg_query("INSERT INTO procedimiento VALUES (
		 $esp_id, '".$esp['esp_desc']."', '$equipos', '$campos', $info, $orden
		);");	
	}

	if($presta)
	for($i=0;$i<sizeof($pr);$i++) {
		
		$fnd=false;
		
		for($j=0;$j<sizeof($presta);$j++) {
			
			if($pr[$i]['pc_id']==$presta[$j]['pc_id']) {
				pg_query("UPDATE procedimiento_codigo SET
					pc_codigo='".pg_escape_string($presta[$j]['pc_codigo'])."', 
					pc_desc='".pg_escape_string(utf8_decode($presta[$j]['pc_desc']))."'
				WHERE pc_id=".$presta[$j]['pc_id']);
				$fnd=true; break;	
			}	
		}
		
		if(!$fnd) { 
			pg_query("DELETE FROM procedimiento_codigo 
							WHERE pc_id=".$pr[$i]['pc_id']); 
		}
	}
	
	if($presta)	
	for($i=0;$i<sizeof($presta);$i++) {

		if($presta[$i]['pc_id']==0) {
			pg_query("INSERT INTO procedimiento_codigo VALUES (
				DEFAULT, $esp_id, 
				'".pg_escape_string(utf8_decode($presta[$i]['pc_desc']))."', 
				'".pg_escape_string($presta[$i]['pc_codigo'])."'			
			);");	
		}
		
	}

	pg_query("COMMIT;");

?>