<?php 

	require_once("../../conectar_db.php");


	$fecha=pg_escape_string($_POST['fecha1']);


	 $nomdl1=cargar_registros_obj("
                SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                                        upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
                                        pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,
                                        date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
                                        especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
                                        COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                                        COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                                        COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
                                        FROM nomina_detalle
                                        LEFT JOIN nomina USING (nom_id)
                                        LEFT JOIN especialidades ON nom_esp_id=esp_id
                                        LEFT JOIN doctores ON nom_doc_id=doc_id
                                        JOIN pacientes USING (pac_id)
                                        WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
                                        AND nom_fecha::date='$fecha'
                                        ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
                ", true);

                //if($tipo_inf==3)
                $nomdl2=cargar_registros_obj("SELECT fesp_fecha AS fecha_sol, fesp_fecha::date AS fecha_asigna,esp_id,doc_rut,
                   pacientes.pac_ficha, upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
                   pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,
                   fesp_estado, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id ,fesp_id AS nomd_id,pac_id,amp_nombre,
                        COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                        COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                        COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
                   FROM ficha_espontanea
                   LEFT JOIN especialidades using(esp_id)
                   LEFT JOIN doctores using (doc_id )
                   LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                   JOIN pacientes USING (pac_id)
                   WHERE esp_ficha AND fesp_fecha::date='$fecha' AND fesp_estado=0
                   GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
                                                   pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
		 ORDER BY fesp_fecha,esp_desc,doc_nombre", true);

                if($nomdl1 AND $nomdl2) $nomdl=array_merge($nomdl1,$nomdl2);
                else if($nomdl1 AND !$nomdl2) $nomdl=$nomdl1;
                else if(!$nomdl1 AND $nomdl2) $nomdl=$nomdl2;
                else $nomdl=false;

	if($nomdl)
	for($i=0;$i<sizeof($nomdl);$i++) {


	$nomd=$nomdl[$i];

	$nomd_id=$nomd['nomd_id']*1;

	$estado_actual=$nomd['am_estado']*1;

	if($estado_actual!=1) continue;

	$pac_id=$nomd['pac_id']*1;
	$pac_ficha=pg_escape_string($nomd['pac_ficha']);

        $esp_id=$nomd['esp_id_actual']*1;
        $doc_id=$nomd['doc_id_actual']*1;
        $esp_id2=$nomd['esp_id_actual']*1;
        $doc_id2=$nomd['doc_id_actual']*1;

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

	$estado=2;

	pg_query("START TRANSACTION;");

        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
        pg_query("INSERT INTO archivo_movimientos VALUES (
                DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true
        );");

        pg_query("COMMIT;");


	}


?>
