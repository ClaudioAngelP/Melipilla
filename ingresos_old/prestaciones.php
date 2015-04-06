<?php 

	require_once('../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;
	$prevision=$_POST['prevision'];

	if($prevision=='FONASA A')
		$cmpval='ar_fonasa_a';
	elseif($prevision=='FONASA B')
		$cmpval='ar_fonasa_b';
	elseif($prevision=='FONASA C')
		$cmpval='ar_fonasa_c';
	elseif($prevision=='FONASA D')
		$cmpval='ar_fonasa_d';
	else
		$cmpval='ar_particular';
	
	$p=cargar_registros_obj("
	
		SELECT * FROM (
		SELECT presta_id, 
		COALESCE(codigo, presta_codigo_v) AS codigo, 
		COALESCE(codigos_prestacion.glosa, presta_desc) AS glosa, 
		COALESCE(mai.precio,presta_valor) AS precio, 'R' AS tipo, presta_fecha AS fecha, COALESCE(ar_precio_total, presta_valor) AS valor
		FROM prestacion 
		LEFT JOIN codigos_prestacion ON presta_codigo_v=codigo
		LEFT JOIN mai ON ((grupo || sub_grupo || presta) = presta_codigo_v) AND corr='0000'
		LEFT JOIN (SELECT ar_codigo, ar_glosa, $cmpval AS ar_precio, ar_particular AS ar_precio_total FROM aranceles) AS foo_aranceles ON COALESCE(codigo, presta_codigo_v)=ar_codigo
		WHERE pac_id=$pac_id AND presta_estado=1
		
		UNION
		
		SELECT 
		nomd_id AS presta_id, 
		esp_codigo_fonasa AS codigo,
		'[' || nom_fecha::date || ' ' || substr(nomd_hora::text,1,5) || '] CONSULTA ' || upper(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno) AS glosa,
		COALESCE(ar_precio,0) AS precio, 'P' AS tipo, nom_fecha AS fecha, ar_precio_total AS valor
		FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN doctores ON nom_doc_id=doc_id
		JOIN especialidades ON nom_esp_id=esp_id
		LEFT JOIN (SELECT ar_codigo, ar_glosa, $cmpval AS ar_precio, ar_particular AS ar_precio_total FROM aranceles) AS foo_aranceles ON esp_codigo_fonasa=ar_codigo
		WHERE pac_id=$pac_id  
		AND nomd_pago=0 AND nomd_diag_cod NOT IN ('X', 'T')
		
		) AS foo ORDER BY fecha, precio DESC;
		
	", true);
	
	// Cuando son distintos codigos:
	//AND nomd_codigo_presta IS NOT NULL
	//LEFT JOIN codigos_prestacion ON nomd_codigo_presta=codigo
	//LEFT JOIN mai ON ((grupo || sub_grupo || presta) = nomd_codigo_presta) AND corr='0000'	
	
	echo json_encode($p);

?>
