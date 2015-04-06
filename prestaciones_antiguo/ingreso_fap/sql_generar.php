<?php 

	require_once('../../conectar_db.php');
	
	$tipo=$_POST['tipo']*1;
	$nro=$_POST['fapnro']*1;
	
	$pac_id=$_POST['pac_id']*1;
	$nro_pab=$_POST['fap_numpabellon']*1;
	$prev_id=$_POST['prev_id']*1;
	$ciud_id=$_POST['ciud_id']*1;
	$centro_ruta=pg_escape_string($_POST['centro_ruta']);
	$diag_cod=pg_escape_string($_POST['diag_cod']);
	$suspension=pg_escape_string(trim($_POST['fap_suspension']));

	$fap_hoja_cargo=($_POST['fap_hoja_cargo'])*1;

	$fecha=pg_escape_string($_POST['fecha']);
	$hora=pg_escape_string($_POST['hora']);
	
	$func_id=$_SESSION['sgh_usuario_id']*1;

	$confirma=$_POST['confirma']*1;
	
/*

CREATE TABLE fap_pabellon
(
  fap_id bigserial NOT NULL,
  fap_fnumero bigint,
  fap_fecha timestamp without time zone,
  pac_id bigint,
  prev_id integer,
  ciud_id integer,
  fap_numpabellon integer,
  centro_ruta character varying(100),
  centro_ruta2 character varying(100),
  fap_tipopab smallint,
  fap_subtipopab smallint,
  fap_tablapab smallint,
  fap_diag_cod character varying(20),
  fap_diag_cod_1 character varying(20),
  fap_diag_cod_2 character varying(20),
  fap_diag_cod_3 character varying(20),
  fap_pab_hora1 time without time zone,
  fap_pab_hora2 time without time zone,
  fap_pab_hora3 time without time zone,
  fap_pab_hora4 time without time zone,
  fap_pab_hora5 time without time zone,
  fap_pab_hora6 time without time zone,
  fap_pab_hora7 time without time zone,
  fap_pab_hora8 time without time zone,  
  fapth_id integer,
  fapta_id1 integer,
  fapta_id2 integer,
  fap_asa integer,
  fap_eval_pre smallint,
  fap_entrega_ane smallint,
  fap_eva integer,
  fap_observaciones text,
  func_id bigint,
  func_id2 bigint,
  fap_biopsia smallint,
  CONSTRAINT fap_pabellon_fap_id_key PRIMARY KEY (fap_id)
);

*/	
	
	$chk=cargar_registros_obj("SELECT * FROM fap_pabellon
					LEFT JOIN pacientes USING (pac_id) 
					WHERE fap_pabellon.pac_id=$pac_id AND fap_fecha::date='$fecha'");
	
	if($chk AND $confirma==0) {
		// En caso de exitir FAP creado para la misma fecha,
		// solicita confirmación ...
		exit('['.sizeof($chk).']');	
	}	
	
	$hora_ing=$hora;	
	
	$fap_id=$_POST['fap_id']*1;	
	
	if(trim($suspension)=='') {
		$centro_ruta2='null';		
	} else {
		$centro_ruta2="'$centro_ruta'";				
	}
	
	if($fap_id==-1)
		$fid='DEFAULT';
	else {
		pg_query("DELETE FROM fap_pabellon WHERE fap_id=$fap_id");
		$fid=$fap_id;	
	}	
	
	pg_query("INSERT INTO fap_pabellon VALUES (
		$fid,
		nextval('fap_pabellon_seq'),
		'$fecha $hora',
		$pac_id,
		$prev_id,
		$ciud_id,
		$nro_pab,
		'$centro_ruta',
		$centro_ruta2,
		-1, -1, -1,
		'$diag_cod',
		null, null, null,
		'$hora_ing', null, null, null, null, null, null, null,
		-1, -1, -1, -1, -1, -1, -1, '',
		$func_id, 0, -1, '$suspension', null, null, $fap_hoja_cargo
	);");
	
	if($fap_id==-1) {
		$id=cargar_registro("SELECT CURRVAL('fap_fap_id_seq') AS id;");
		$fap_id=$id['id']*1;
	}
	
	echo $fap_id;	
	
?>
