<?php
    set_time_limit(0);
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $paciente = file_get_contents('depresionf.txt');
    $pacientes=explode("\n", $paciente);
    $pacientes_general='';
    for($i=0;$i<count($pacientes);$i++)
    {
            $pacientes[$i]=trim($pacientes[$i]);
            $datos_paciente=cargar_registros_obj("select pac_id from pacientes where pac_rut='$pacientes[$i]'",true);
            if($datos_paciente)
            {
                $pacientes_general.=$datos_paciente[0]['pac_id'].',';
            }
            else
            {
                $noencontrado[]=$pacientes[$i] ;
            }

    }

    $pacientes_general=substr($pacientes_general,0,-1);
    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
              
            );
             
    $query=
    "SELECT art_codigo,
            art_glosa,
            ABS(SUM(stock_cant)) as cant,
            ABS(SUM(stock_cant)*art_val_ult) as valor
            FROM pacientes
            INNER JOIN receta on receta_paciente_id=pac_id
            INNER JOIN recetas_detalle on recetad_receta_id=receta_id
	        INNER JOIN articulo on recetad_art_id=art_id
            INNER JOIN logs on log_recetad_id=recetad_id
            INNER JOIN stock on stock_log_id=log_id
            WHERE date_trunc('day',receta_fecha_emision)::date BETWEEN '[%fecha1]' AND '[%fecha2]'
            AND stock_bod_id=[%bodega]
            AND pac_id in ($pacientes_general)
            and(receta_centro_ruta='.subdireccinmdicoasistencial.saludmental.saludmental' or
            receta_centro_ruta='.subdireccinmdicoasistencial.saludmental')
            GROUP BY articulo.art_codigo,articulo.art_glosa,articulo.art_val_ult
            ORDER BY art_codigo;
            ";



   
    $formato=Array(
                Array('art_codigo',           'C&oacute;digo',          0, 'left'),
                Array('art_glosa',            'Art&iacute;culo',        0, 'left'),
                Array('cant',                 'Cant.',                  0, 'right'),
                Array('valor',                'Valor',                  3, 'right')


              );

     ejecutar_consulta();

     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=3>Valor Total:</td>
      <td>'.number_format(infoSUM('valor'),0,',','.').'</td>
      </tr>
    ';

     procesar_formulario('Consumo Valorizado colecci&oacute;n de pacientes por Depresi&oacute;n');

?>
