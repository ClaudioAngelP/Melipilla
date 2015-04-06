<?php
   /* Imprime una copia de la receta segun ubicacion y datos en general.
     ademas registra en detalle los productos recetados, en cuanto a cantidad total despachada y
     frecuencia de dosis en tratamiento.
     Cinthia Ormazabal C.
     Soluciones Computacionales.
   */

   $temp_5_1=microtime();

   require_once('../../conectar_db.php');

   $id_receta = ($_GET['receta_id']*1);
   $bodega = ($_GET['bodega_id']*1);

     $encabezado_receta = pg_query("
        SELECT DISTINCT
	    receta_id,
        receta_numero,
        date_trunc('second',receta_fecha_emision) as fecha,
        pac_rut,
        pac_appat || ' ' || pac_apmat || ' ' || pac_nombres as paciente,
        doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  as medico,
        COALESCE(diag_desc, '(Sin Asignar...)') as diagnostico,
       	centro_nombre,
        COALESCE(receta_comentarios,'(Sin comentario...)') as comentario,
         CASE WHEN receta_cronica=true THEN 'Cronica'
             ELSE CASE WHEN receta_cronica=false and receta_tipotalonario_id=0 THEN 'Aguda'
             ELSE CASE WHEN receta_tipotalonario_id=1 THEN 'Psicotrópicos'
                            ELSE 'Benzodiazepina' END
        END END AS tipo_receta,
        CASE WHEN receta_tipotalonario_id<>0 THEN receta_numero END AS numero_receta
             FROM receta
                  LEFT JOIN pacientes ON receta_paciente_id=pac_id
                  INNER JOIN doctores on receta_doc_id=doc_id
                  LEFT JOIN diagnosticos on diag_cod=receta_diag_cod
                  LEFT JOIN centro_costo ON receta_centro_ruta=centro_ruta
                  LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
            WHERE receta_id=".$id_receta."

    ");

     $bodega_reg = pg_query($conn, "
      SELECT bod_glosa FROM bodega WHERE bod_id=".$bodega."
      ");

      $bodega_row = pg_fetch_row($bodega_reg);

      $bodega_nombre = $bodega_row[0];


     if(pg_num_rows($encabezado_receta)==0) {
      die('<i>No existen recetas para la busqueda</i>');

    }

   print('
   <table style="font-size:12px;">
   <tr style="text-align:center;">
   <td colspan=3 "font-weight:bold;">
   <font size=+1><b>
          Servicio de Salud Vi&ntilde;a del Mar - Quillota<br>
          Hospital Dr. Gustavo Fricke<br>
          Copia de Receta</b>
   </font>
   </td>
   </tr>
   </table>
   <br>');

   $fila = pg_fetch_row($encabezado_receta);

   print('
   <table>
   <tr><td style="text-align:left;">
    Ubicaci&oacute;n : <b>'.htmlentities($bodega_nombre).'</b>&nbsp;&nbsp;&nbsp;&nbsp;
    Receta Emisi&oacute;n : <b><i>'.htmlentities($fila[2]).'</i></b></td>
    </tr>
    <tr><td style="text-align:left;">
     Rut : <b>'.htmlentities($fila[3]).'</b>&nbsp;&nbsp;
     Nombre : <b>'.htmlentities($fila[4]).'</b></td>
    </tr>
    <tr><td style="text-align:left;">
     Tipo Receta : <b><i>'.htmlentities($fila[9]).'</i></b>&nbsp;&nbsp;&nbsp;&nbsp;
     N&deg; Receta : <b><i>'.htmlentities($fila[10]).'</i></b></td>
    </tr>
    <tr><td td style="text-align:left;">
    Centro Costo : <b>'.htmlentities($fila[7]).'</b></td>
    </tr>
    <tr>
    <td>
    M&eacute;dico : '.htmlentities($fila[5]).'</td>
    </tr>
   </table>
   <table>
    <tr>
    <td style="text-align:left;">
    Diagn&oacute;stico : </td>
    <td style="text-align:left; font-style: italic;"><b>'.htmlentities($fila[6]).'</b></td>
    </tr>
    <tr>
    <td>Observaciones:</td>
      <td style="text-align:left; font-style: italic;">
      '.htmlentities($fila[8]).'</td>
    </tr>
    </table>
   ');


    $detalle_receta = pg_query("
    SELECT
        art_codigo,
        art_glosa,
        recetad_cant,
        recetad_horas,
        recetad_dias,
        ((recetad_cant*(24/recetad_horas))*recetad_dias) as total
    FROM recetas_detalle
    INNER JOIN articulo ON recetad_art_id=art_id
    WHERE recetad_receta_id=".$id_receta."

     ");

   print('
  <table width="90%" align=center >
  <tr class="tabla_header" style="font-weight: bold;">
  <td colspan=2>Datos del Medicamento</td>
  <td colspan=4>Dosis</td>


  ');



  print('
  </tr>
  <tr class="tabla_header" style="font-weight: bold;">
  <td width=15%>C&oacute;digo Int.</td>
  <td width=40% >Glosa</td>
  <td>Cant.</td>
  <td>c/Horas</td>
  <td>c/D&iacute;as</td>
  <td>Total Recetado</td>

  </tr>
  </br>
  ');

   for($i=0;$i<pg_num_rows($detalle_receta);$i++) {

    $fila = pg_fetch_row($detalle_receta);

   print('
   <tr>
   <td>'.htmlentities($fila[0]).'</td>
   <td>'.htmlentities($fila[1]).'</td>
   <td style="text-align: center;">'.htmlentities($fila[2]).'</td>
   <td style="text-align: center;">'.htmlentities($fila[3]).'</td>
   <td style="text-align: center;">'.htmlentities($fila[4]).'</td>
   <td style="text-align: center;">'.htmlentities($fila[5]).'</td>
   </tr>');

  }

  print('</table>');

 print('<div style="page-break-after: always;"></div>');


?>