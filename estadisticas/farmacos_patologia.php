<?php
   /*
   Nombre Informe: Perfil Farmacologico
   Entrega informacion por paciente, especificando
   dosis y frecuencia de medicamentos despachados
   seg�n fecha de receta.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Vi�a del mar.
   */
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

	$patols=array(
				array('%asma%','ASMA'),
				array('%epoc%','EPOC')
			);

    $campos=Array(
              Array(  	'fecha1',   	'Fecha de Inicio',          1   ),
              Array(  	'fecha2',   	'Fecha de T&eacute;rmino',  1   ),
              Array(	'patologia', 	'Patolog&iacute;a',			10,		$patols )
            );

    $query=
    " 

		SELECT pac_rut, pac_appat, pac_apmat, pac_nombres, art_codigo, art_glosa, -(stock_cant) AS cantidad
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
		ORDER BY pac_appat, pac_apmat, pac_nombres    
        
	";

    $formato=Array(
                Array('pac_rut',      		'RUT',          		0, 'right'),
                Array('pac_ficha',      	'Ficha',          		0, 'center'),
                Array('pac_appat',       	'Paterno',        		0, 'left'),
                Array('pac_apmat',  		'Materno',  			0, 'left'),
                Array('pac_nombres',       	'Nombres',          	0, 'left'),
                Array('art_codigo',         'Codigo',           	0, 'right'),
                Array('art_glosa',          'Descripci&oacute;n',   0, 'left'),
                Array('cantidad',          	'Cant.',            	1, 'right')

              );

     ejecutar_consulta();

     procesar_formulario('Pacientes con Patolog&iacute;a y Medicamentos');

?>
