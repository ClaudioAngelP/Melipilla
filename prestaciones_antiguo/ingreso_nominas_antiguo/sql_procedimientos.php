<?php 

	require_once('../../conectar_db.php');
	
	$nomd_id=$_POST['nomd_id']*1;
	$esp_id=$_POST['esp_id']*1;
	
	$presta=json_decode($_POST['presta'],true);
	$origen=pg_escape_string($_POST['origen']);

	$diag_cod=pg_escape_string(utf8_decode($_POST['nomd_diag_cod']));
	$diagnostico=pg_escape_string(utf8_decode($_POST['nomd_diagnostico']));
	
	$obs=pg_escape_string(utf8_decode($_POST['observaciones']));
	
	pg_query("UPDATE nomina_detalle SET 
		nomd_diag_cod='$diag_cod',
		nomd_diag='$diagnostico',
		nomd_observaciones='$obs',
		nomd_origen='$origen'		
		WHERE nomd_id=$nomd_id");

	$proc=cargar_registro("SELECT * FROM procedimiento
											WHERE esp_id=$esp_id");
											
	if($proc['esp_campos']!='') {

		$campos=explode('|', $proc['esp_campos']);

		pg_query("DELETE FROM nomina_detalle_campos WHERE nomd_id=$nomd_id");

		for($i=0;$i<sizeof($campos);$i++) {

		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
		} else {
			$cmp=$campos[$i]; $tipo=2;
		}

		if($tipo==0 OR $tipo==1) {
			$valor=(isset($_POST['campo_'.$i]))?'true':'false';	
		} else {
			$valor=pg_escape_string(utf8_decode($_POST['campo_'.$i]));
		}	
		
		pg_query("
			INSERT INTO nomina_detalle_campos VALUES (
				DEFAULT, $nomd_id, $i, '$valor'			
			);		
		");		
		
		}	
	
	}


	$p=cargar_registros_obj("SELECT * FROM procedimiento_codigo
											LEFT JOIN codigos_prestacion ON pc_codigo=codigo 
											WHERE esp_id=$esp_id");

	if(!$p OR (sizeof($p)>1 AND $_POST['cambia_presta']*1==1)) {
	
		pg_query("DELETE FROM nomina_detalle_prestaciones 
						WHERE nomd_id=$nomd_id");	
		
		for($i=0;$i<sizeof($presta);$i++) {
		
			pg_query("
				INSERT INTO nomina_detalle_prestaciones VALUES (
					DEFAULT, $nomd_id, 
					'".pg_escape_string($presta[$i]['codigo'])."',
					".($presta[$i]['cantidad']*1).", 
					".($presta[$i]['pc_id']*1)."			
				);		
			");	
			
		}
	
	} 
	
	if($p AND sizeof($p)==1){
	
		$chk=cargar_registros_obj("SELECT * FROM nomina_detalle_prestaciones 
												WHERE nomd_id=$nomd_id");
												
		if(!$chk) {
			pg_query("
				INSERT INTO nomina_detalle_prestaciones VALUES (
					DEFAULT, $nomd_id, 
					'".pg_escape_string($p[0]['pc_codigo'])."',
					1, ".($p[0]['pc_id']*1)."			
				);		
			");	
		} 
		
	}

	//exit();
	
	if($proc['esp_orden_atencion']=='t') {
	
		$oa_id=$_POST['oa_id']*1; $inter_id=0;

		$tipo_oa=$_POST['tipo_oa']*1;
		
		$fecha=pg_escape_string($_POST['fecha_oa']);
		
		if($tipo_oa==1) {

			$inst_id=0;			
			$centro_ruta=pg_escape_string($_POST['centro_ruta_oa']);
			$esp_id=$_POST['esp_id_oa']*1;
			$doc_id=$_POST['doc_id_oa']*1;
			$prof_id=0;

		} else {

			$inst_id=$_POST['inst_id_oa'];			
			$centro_ruta='';
			$esp_id=$_POST['esp_id2_oa']*1;
			$doc_id=0;
			$prof_id=$_POST['prof_id_oa']*1;
			
		}

		pg_query("DELETE FROM nomina_detalle_origen WHERE nomd_id=$nomd_id");

		pg_query("INSERT INTO nomina_detalle_origen VALUES (
			$nomd_id, $inter_id, $oa_id,
			$tipo_oa,
			'$fecha',
			$inst_id,
			$esp_id,
			'$centro_ruta',
			$doc_id,
			$prof_id			
		);");	

		$ndet=cargar_registro("SELECT * FROM nomina_detalle WHERE nomd_id=$nomd_id");
		$nom=cargar_registro("SELECT * FROM nomina WHERE nom_id=".$ndet['nom_id']);
				
		$pac_id=$ndet['pac_id']*1;
		$esp_id2=$nom['nom_esp_id']*1;

		if($oa_id==0) {
			
			if(!$p OR (sizeof($p)>1 AND $_POST['cambia_presta']*1==1)) {
				$codigo=$presta[0]['codigo'];
			} else {
				$codigo=$p[0]['pc_codigo'];
			}				


/*
CREATE TABLE orden_atencion
(
  oa_id bigserial NOT NULL,
  oa_folio integer,
  oa_fecha timestamp without time zone,
  oa_pac_id bigint,
  oa_inst_id bigint,
  oa_inst_id2 bigint,
  oa_especialidad integer,
  oa_motivo smallint,
  oa_estado smallint,
  oa_hipotesis text,
  oa_codigo character varying(30),
  oa_diag_cod character varying(20),
  oa_prof_id bigint,
  oa_doc_id bigint,
  oa_fecha_aten date,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  func_id bigint DEFAULT 0,
  func_id2 bigint DEFAULT 0,
  oa_motivo_salida DEFAULT 0,  
  CONSTRAINT orden_atencion_oa_id_key PRIMARY KEY (oa_id)
)
WITH (OIDS=FALSE);
*/			
			
			/*pg_query("INSERT INTO orden_atencion VALUES (
				DEFAULT,
				0,
				'$fecha', $pac_id, $sgh_inst_id, $sgh_inst_id,
				$esp_id, -1, 2, '', '$codigo', '', -1, $doc_id, 
				null, 0, 0, 
				".($_SESSION['sgh_usuario_id']*1).", 
				".($_SESSION['sgh_usuario_id']*1).", 
				1, '$centro_ruta', $esp_id2
			);");
			
			$reg=cargar_registro("SELECT CURRVAL('orden_atencion_oa_id_seq') AS id;");
			
			$oa_id=$reg['id']*1;*/
					
			$oa_id=0;
							
		} else {

			$orden=cargar_registro("SELECT * FROM orden_atencion WHERE oa_id=$oa_id");

			if($orden['oa_inst_id']==$sgh_inst_id) {			
				/*pg_query("
					UPDATE orden_atencion SET
					oa_fecha='$fecha',
					oa_centro_ruta='$centro_ruta',
					oa_especialidad=$esp_id,
					oa_especialidad2=$esp_id2,
					func_id2=".($_SESSION['sgh_usuario_id']*1).",
					oa_estado=2, oa_motivo_salida=1
					WHERE oa_id=$oa_id			
				");*/		
			} else {
				pg_query("
					UPDATE orden_atencion SET
					oa_especialidad2=$esp_id2,
					func_id2=".($_SESSION['sgh_usuario_id']*1).",
					oa_estado=2, oa_motivo_salida=1
					WHERE oa_id=$oa_id			
				");						
			}
			
		} 
		
		pg_query("UPDATE nomina_detalle SET oa_id=$oa_id WHERE nomd_id=$nomd_id");	
		
	}

?>