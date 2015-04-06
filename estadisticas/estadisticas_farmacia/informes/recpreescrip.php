<?php

    /*
		Nombre Informe: Recetas Y prescripciones
		Rodrigo Carvajal J.
		Sistemas Expertos e Ing. en Software LTDA.
		Viña del Mar 2011
	*/

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           3   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   )
            );

    $query=
    "
		SELECT

		(SELECT COUNT(*) FROM receta
		WHERE receta_fecha_emision BETWEEN '[%fecha1]' AND '[%fecha2]' AND receta_bod_id=[%bodega]
		) AS recetas,

		(SELECT COUNT(*) FROM receta
		WHERE receta_fecha_emision BETWEEN '[%fecha1]' AND '[%fecha2]' AND receta_prescrip>receta_despacho_inicial AND receta_bod_id=[%bodega]
		) AS recetas_incompletas,

		(SELECT SUM(receta_prescrip) FROM receta
		WHERE receta_fecha_emision BETWEEN '[%fecha1]' AND '[%fecha2]' AND receta_bod_id=[%bodega]
		) AS prescripciones,

		(SELECT SUM(receta_despacho_inicial) FROM receta
		WHERE receta_fecha_emision BETWEEN '[%fecha1]' AND '[%fecha2]' AND receta_bod_id=[%bodega]
		) AS prescripciones_ok    
	";

    $formato=Array(
                Array('recetas',  					'Recetas Completas',           		1, 'right'),
                Array('recetas_incompletas',        'Recetas Incompletas',           	1, 'right'),
                Array('prescripciones_ok',      	'Prescripciones Completas',   		1, 'right'),
                Array('prescripciones',      		'Prescripciones',   				1, 'right')
              );


    ejecutar_consulta();

    procesar_formulario('Recetas y Preescripciones');

?>
