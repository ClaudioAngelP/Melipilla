 
 <?php 

	require_once('../../conectar_db.php');
	
	$tipo_inf=$_POST['tipo_inf']*1;
	
	$fecha=pg_escape_string($_POST['fecha1']);
	
	$barras=trim($_POST['barras']);
	
	if($barras=='') exit(json_encode(false));
 	
	$tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras'");
	
	$pac_id=$tmp[0]['pac_id']*1;
	$pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
	
	$nomd=cargar_registro("
	SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
				upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
				pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
				date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
				especialidades.esp_id,doctores.doc_id,
				COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
				COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
				COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
				FROM nomina_detalle
				LEFT JOIN nomina USING (nom_id)
				LEFT JOIN especialidades ON nom_esp_id=esp_id
				LEFT JOIN doctores ON nom_doc_id=doc_id
				JOIN pacientes USING (pac_id)
				WHERE nomd_diag_cod NOT IN ('X','T','B')
				AND pac_id=$pac_id AND nom_fecha::date='$fecha'
				ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
	");
	
	$nomd_id=$nomd['nomd_id']*1;
	
	$esp_id=$nomd['esp_id_actual']*1;
	$doc_id=$nomd['doc_id_actual']*1;
	$esp_id2=$nomd['esp_id']*1;
	$doc_id2=$nomd['doc_id']*1;
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
/*

drop table archivo_movimientos;

create table archivo_movimientos (
am_id bigserial,
am_fecha timestamp without time zone,
am_func_id bigint,
pac_id bigint,
pac_ficha text,
nomd_id bigint,
origen_esp_id bigint,
origen_doc_id bigint,
destino_esp_id bigint,
destino_doc_id bigint,
am_estado smallint,
am_funcionario_responsable text,
am_final boolean default false
);

*/
	
	if($tipo_inf==1) $estado=2;
	if($tipo_inf==2) { 
		
		$estado=4; $esp_id2=0; $doc_id2=0; 
		
		$chk=cargar_registro("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado=2;");
		
		if(!$chk) exit(json_encode($tmp, true));
			
	}
	
	

	
	pg_query("START TRANSACTION;");
	
	pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
	pg_query("INSERT INTO archivo_movimientos VALUES (
		DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true
	);");
	
	pg_query("COMMIT;");
	
	exit(json_encode($tmp, true));

?>