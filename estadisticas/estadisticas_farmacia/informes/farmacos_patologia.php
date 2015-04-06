<?php
   /*
   Nombre Informe: Perfil Farmacologico
   Entrega informacion por paciente, especificando
   dosis y frecuencia de medicamentos despachados
   según fecha de receta.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

	$patols=array(
				array('%asma%','ASMA'),
				array('%epoc%','EPOC'),
				array('%cardi%','TX CARDIACO'),
				array('%renal%','TX RENAL'),
				array('INFECTOLOGIA','INFECTOLOGIA')
			);

    $campos=Array(
              Array(  	'fecha1',   	'Fecha de Inicio',          1   ),
              Array(  	'fecha2',   	'Fecha de T&eacute;rmino',  1   ),
              Array(	'patologia', 	'Patolog&iacute;a',			10,		-1,	$patols )
            );

    $query=
    " 

		SELECT pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, 
		date_part('year',age(log_fecha::date, pac_fc_nac)) as edad_anios,  
		art_codigo, art_glosa, -(stock_cant) AS cantidad, bod_glosa, log_fecha::date
		FROM (
		SELECT DISTINCT pac_id
		FROM pacientes_patologia 
		JOIN pacientes using (pac_id)
		WHERE pacpat_descripcion ilike '[%patologia]'
		UNION
		SELECT DISTINCT ca_pac_id AS pac_id
		FROM casos_auge 
		WHERE ca_patologia ilike '[%patologia]'
		) AS foo
		JOIN pacientes USING (pac_id)
		JOIN receta ON receta_paciente_id=pac_id
		JOIN recetas_detalle ON recetad_receta_id=receta_id
		JOIN logs ON log_recetad_id=recetad_id AND log_fecha BETWEEN '[%fecha1]' AND '[%fecha2]'
		JOIN stock ON stock_log_id=log_id
		JOIN articulo ON stock_art_id=art_id
		JOIN bodega ON stock_bod_id=bod_id
		WHERE pac_id>0
		ORDER BY pac_appat, pac_apmat, pac_nombres    
        
	";

    $formato=Array(
                Array('pac_rut',      		'RUT',          		0, 'right'),
                Array('pac_ficha',      	'Ficha',          		0, 'center'),
                Array('pac_appat',       	'Paterno',        		0, 'left'),
                Array('pac_apmat',  		'Materno',  			0, 'left'),
                Array('pac_nombres',       	'Nombres',          	0, 'left'),
                Array('edad_anios',       	'Edad',          		1, 'right'),
                Array('log_fecha',          'Fecha Desp.',          0, 'center'),
                Array('art_codigo',         'Codigo',           	0, 'right'),
                Array('art_glosa',          'Descripci&oacute;n',   0, 'left'),
                Array('cantidad',          	'Cant.',            	1, 'right'),
                Array('bod_glosa',          	'Bodega',            	0, 'left')
              );

     ejecutar_consulta();

     procesar_formulario('Pacientes con Patolog&iacute;a y Medicamentos');

?>
