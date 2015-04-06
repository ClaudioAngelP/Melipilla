<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	error_reporting(E_ALL);

	// Proceso Automático de Listas de Espera Abiertas

	pg_query("

UPDATE interconsulta AS inter1 SET inter_motivo_salida=1, inter_fecha_salida=foo.nom_fecha FROM
(
SELECT inter_id, inter_pac_id,
inter_folio, nom_folio,
 inter_fecha_ingreso, nom_fecha, e1.esp_desc AS esp_nomina, nomd_diag_cod, 
 e3.esp_desc AS esp_unidad, e2.esp_desc AS esp_inter, inter_unidad, inter_motivo_salida
FROM interconsulta AS i1
JOIN nomina_detalle ON pac_id=inter_pac_id AND nomd_diag_cod NOT IN ('NSP','XX', 'R', '')
JOIN nomina USING (nom_id)
JOIN especialidades AS e1 ON nom_esp_id=e1.esp_id
JOIN especialidades AS e2 ON inter_especialidad=e2.esp_id
JOIN especialidades AS e3 ON inter_unidad=e3.esp_id
WHERE NOT inter_folio=0 AND inter_estado=1 AND inter_motivo_salida=0 AND id_caso <= 1 AND nom_fecha>inter_fecha_ingreso AND
e1.esp_id=e3.esp_id
) AS foo 
WHERE inter1.inter_pac_id=foo.inter_pac_id 
AND inter1.inter_unidad=foo.inter_unidad 
AND inter1.inter_fecha_ingreso<foo.nom_fecha
AND inter1.inter_estado=1 
AND inter1.inter_motivo_salida=0;

	");
	
	// Proceso Automático de NSP
/*	
	pg_query("
	
UPDATE interconsulta AS inter1 SET inter_motivo_salida=8, inter_fecha_salida=foo2.fecha_ultima_nsp FROM (
SELECT * from (
SELECT inter_id, inter_pac_id,
inter_folio, inter_unidad, 
(SELECT MAX(nom_fecha) FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nom_esp_id=inter_unidad AND pac_id=inter_pac_id AND nomd_diag_cod='NSP') AS fecha_ultima_nsp,
COUNT(*) AS nsp
FROM interconsulta AS i1
JOIN nomina_detalle ON pac_id=inter_pac_id AND nomd_diag_cod='NSP'
JOIN nomina USING (nom_id)
JOIN especialidades AS e1 ON nom_esp_id=e1.esp_id
JOIN especialidades AS e2 ON inter_especialidad=e2.esp_id
JOIN especialidades AS e3 ON inter_unidad=e3.esp_id
WHERE NOT inter_folio=0 AND inter_estado=1 AND inter_motivo_salida=0 AND id_caso <= 1 AND nom_fecha>inter_fecha_ingreso AND
e1.esp_id=e3.esp_id GROUP BY inter_id, inter_pac_id, inter_folio, inter_unidad
) as foo where nsp>1 ) AS foo2
WHERE inter1.inter_unidad=foo2.inter_unidad 
AND inter1.inter_pac_id=foo2.inter_pac_id 
AND inter1.inter_fecha_ingreso<foo2.fecha_ultima_nsp
AND inter1.inter_estado=1 
AND inter1.inter_motivo_salida=0;	
	
	"); */

	// REBAJE LEC

pg_query("

UPDATE orden_atencion AS o1 SET oa_motivo_salida=1, oa_fecha_salida=foo2.fap_fecha FROM (

SELECT oa_id, oa_fecha, fap_fecha, oa_codigo, fappr_codigo
FROM orden_atencion
JOIN fap_pabellon AS f1 on oa_pac_id=pac_id and fap_fecha>oa_fecha
JOIN fap_prestacion AS f2 on f2.fap_id=f1.fap_id and fappr_codigo=oa_codigo
WHERE oa_carpeta_id IS NOT NULL AND oa_motivo_salida=0

) AS foo2

WHERE o1.oa_id=foo2.oa_id;

");


?>
