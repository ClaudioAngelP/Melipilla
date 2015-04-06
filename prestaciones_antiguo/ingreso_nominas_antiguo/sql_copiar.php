<?php 

	require_once('../../conectar_db.php');
	
	$nom_id=$_POST['nom_id']*1;
	$doc_id=$_POST['doc_id']*1;
	$fecha=pg_escape_string($_POST['fecha1']);

	pg_query("START TRANSACTION;");
	
	error_reporting(E_ALL);

	$n=cargar_registros_obj("SELECT * FROM nomina WHERE nom_id=$nom_id");
	
	$q="
		INSERT INTO nomina VALUES (
			DEFAULT,
			max_folio(),
			".$n[0]['nom_esp_id'].",
			".$doc_id.",
			'',
			".($n[0]['nom_tipo']*1).",
			".($n[0]['nom_urgente']=='t'?'true':'false').",
			'".$fecha."',
			".$n[0]['nom_orden'].",
			".($n[0]['nom_autorizado']=='t'?'true':'false').",
			".$n[0]['nom_func_id'].",
			".$n[0]['nom_func_id2'].",
			".$n[0]['nom_estado'].",
			'".pg_escape_string($n[0]['nom_motivo'])."',
			".($n[0]['nom_estado_digitacion']==''?'null':$n[0]['nom_estado_digitacion']).",
			".($n[0]['nom_fecha_digitacion']==''?'null':"'".$n[0]['nom_fecha_digitacion']."'").",
			true
		); 
	";
	
	print($q);	
	
	pg_query($q);
	
	$nom=cargar_registro("SELECT * FROM nomina WHERE nom_id=CURRVAL('nomina_nom_id_seq')");
	$nd=cargar_registros_obj("SELECT * FROM nomina_detalle WHERE nom_id=$nom_id");
	
	$folio=$nom['nom_folio'];

	if($nd)	
	for($i=0;$i<sizeof($nd);$i++) {
/*


CREATE TABLE nomina_detalle
(
  nomd_id bigserial NOT NULL,
  nom_id bigint,
  pac_id bigint,
  nomd_tipo character varying(1),
  nomd_extra character varying(1),
  nomd_diag character varying(100),
  nomd_sficha character varying(1),
  nomd_diag_cod character varying(20),
  nomd_motivo character varying(2),
  nomd_destino character varying(2),
  nomd_auge character varying(2),
  nomd_estado character varying(1),
  presta_id bigint,
  nomd_hora time without time zone,
  nomd_via_ingreso character varying(1) DEFAULT 'A'::character varying,
  nomd_folio character varying(50),
  nomd_nom_id bigint,
  nomd_observaciones text,
  CONSTRAINT nomina_detalle_nomd_id_key PRIMARY KEY (nomd_id)
)
WITH (OIDS=FALSE);

*/

	if(!isset($_POST['datos'])){
		pg_query("
			INSERT INTO nomina_detalle VALUES (
				DEFAULT,
				CURRVAL('nomina_nom_id_seq'),
				".$nd[$i]['pac_id'].",		
				'".pg_escape_string($nd[$i]['nomd_tipo'])."',		
				'".pg_escape_string($nd[$i]['nomd_extra'])."',		
				'".pg_escape_string($nd[$i]['nomd_diag'])."',		
				'".pg_escape_string($nd[$i]['nomd_sficha'])."',		
				'',		
				'',		
				'',		
				'',		
				'".pg_escape_string($nd[$i]['nomd_estado'])."',		
				0,
				'".pg_escape_string($nd[$i]['nomd_hora'])."',		
				'".pg_escape_string($nd[$i]['nomd_via_ingreso'])."',		
				'".pg_escape_string($folio)."', 
				CURRVAL('nomina_nom_id_seq'), ''		

			);		
		");
		}else{
		pg_query("
			INSERT INTO nomina_detalle VALUES (
				DEFAULT,
				CURRVAL('nomina_nom_id_seq'),
				".$nd[$i]['pac_id'].",		
				'".pg_escape_string($nd[$i]['nomd_tipo'])."',		
				'".pg_escape_string($nd[$i]['nomd_extra'])."',		
				'".pg_escape_string($nd[$i]['nomd_diag'])."',		
				'".pg_escape_string($nd[$i]['nomd_sficha'])."',		
				'".pg_escape_string($nd[$i]['nomd_diag_cod'])."',		
				'".pg_escape_string($nd[$i]['nomd_motivo'])."',		
				'".pg_escape_string($nd[$i]['nomd_destino'])."',		
				'".pg_escape_string($nd[$i]['nomd_auge'])."',		
				'".pg_escape_string($nd[$i]['nomd_estado'])."',		
				0,
				'".pg_escape_string($nd[$i]['nomd_hora'])."',		
				'".pg_escape_string($nd[$i]['nomd_via_ingreso'])."',		
				'".pg_escape_string($folio)."', 
				CURRVAL('nomina_nom_id_seq'), ''		

			);		
		");
		
		pg_query("
			INSERT INTO nomina_detalle_campos (nomd_id, nomdc_offset, nomdc_valor) SELECT CURRVAL('nomina_detalle_nomd_id_seq'), nomdc_offset, nomdc_valor FROM nomina_detalle_campos WHERE nomd_id=".$nd[$i]['nomd_id']." 
		");		
		
		}
		
		
	}		
	
	pg_query("COMMIT");	
	
?>