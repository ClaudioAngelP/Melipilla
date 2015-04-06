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

	$tipos_inf=Array(
				array(1,'Antirretrovirales'),
				array(2,'Enfermedades Oportunistas')
			);

    $campos=Array(
              Array(  	'fecha1',   	'Fecha de Inicio',          1   ),
              Array(  	'fecha2',   	'Fecha de T&eacute;rmino',  1   ),
              Array(	'tipo_rep',		'Tipo de F&aacute;rmacos',			10, -1, $tipos_inf)
            );

    $query=
    " 

		SELECT 
			
			(substr(trim(pac_nombres), 1, 1) || 
			substr(trim(pac_appat), 1, 1) || 
			substr(trim(pac_apmat), 1, 1) || 
			lpad(date_part('day',pac_fc_nac)::text,2,'0') || 
			lpad(date_part('month',pac_fc_nac)::text,2,'0') || 
			substr(date_part('year',pac_fc_nac)::text,3,2) || 
			substr(trim(pac_rut), length(trim(pac_rut))-4,5)) AS pac_codigo, 

			date_part('year',age(log_fecha::date, pac_fc_nac)) as edad_anios,  
			log_fecha::date,		
			
			art_codigo, 
			art_glosa, 
			-(stock_cant) AS cantidad, 
			(-(stock_cant)*art_val_ult) AS subtotal,
			bod_glosa
			
		FROM (
		SELECT DISTINCT pac_id
		FROM pacientes_patologia 
		JOIN pacientes using (pac_id)
		WHERE pacpat_descripcion ilike 'INFECTOLOGIA'
		) AS foo
		JOIN pacientes USING (pac_id)
		JOIN receta ON receta_paciente_id=pac_id
		JOIN recetas_detalle ON recetad_receta_id=receta_id
		JOIN logs ON log_recetad_id=recetad_id AND log_fecha BETWEEN '[%fecha1]' AND '[%fecha2]'
		JOIN stock ON stock_log_id=log_id
		JOIN articulo ON stock_art_id=art_id
		JOIN bodega ON stock_bod_id=bod_id
		WHERE pac_id>0
		
		[if %tipo_rep==1] AND art_codigo ILIKE '270%' [/if]
        [if %tipo_rep==2] AND art_codigo NOT ILIKE '270%' [/if]
                
		ORDER BY pac_appat, pac_apmat, pac_nombres    
        
	";

    $formato=Array(
                Array('pac_codigo',      	'Paciente',          		0, 'center'),
                Array('art_codigo',         'Codigo',           		0, 'right'),
                Array('art_glosa',          'Descripci&oacute;n',   	0, 'left'),
                Array('edad_anios',       	'Edad',          		1, 'right'),
                Array('log_fecha',          'Fecha Desp.',          0, 'center'),
                Array('cantidad',          	'Cant.',            		1, 'right'),
                Array('subtotal',          	'Subtotal($)',            		1, 'right'),
                Array('bod_glosa',          'Bodega',            		0, 'left')
              );

     ejecutar_consulta();
     
     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=7>Total de Pacientes:</td>
      <td>'.number_format(infoCOUNT('pac_codigo'),0,',','.').'</td>
      </tr>
    ';

     procesar_formulario('Pacientes con Patolog&iacute;a y Medicamentos (INFECTOLOGIA)');

?>

