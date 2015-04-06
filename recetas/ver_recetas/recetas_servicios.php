<?php
/*********************************************************************************/
$temp_5_1=microtime();

  require_once('../../conectar_db.php');

   $bodega = ($_GET['bodega_id']*1);
   $fecha = pg_escape_string($_GET['fecha']);


  if(isset($_GET['centro_c'])) {
   if($_GET['centro_servicio']==-1) {
      $centro_costo = $_GET['centro_costo'];
     } else {
        $centro_costo = $_GET['centro_servicio'];
     }

     $centro = pg_escape_string($centro_costo);
    }


     $recetas = pg_query("
        SELECT DISTINCT
          receta_id,
          centro_nombre,
          date_trunc('second', receta_fecha_emision),
          pac_rut,
          pac_nombres || ' ' || pac_appat || ' ' || pac_apmat,
          centro_ruta,
          func_nombre,
          diag_desc
        FROM receta
          LEFT JOIN pacientes ON receta_paciente_id=pac_id
          LEFT JOIN centro_costo ON receta_centro_ruta=centro_ruta
          LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
          LEFT JOIN logs ON log_recetad_id=recetad_id
          LEFT JOIN stock ON stock_log_id=log_id
          LEFT JOIN funcionario on func_id=receta_func_id
          LEFT JOIN diagnosticos on diag_cod=receta_diag_cod
        WHERE
          date_trunc('day', receta_fecha_emision)='$fecha'
          AND
          receta.receta_centro_ruta = '$centro'
          AND stock_bod_id=$bodega AND pac_id<>43

    ");


     if(pg_num_rows($recetas)==0) {
      die('<i>No existen recetas para el servicio seleccionado</i>');
    }


     if($bodega==-1) {
        $bodega_where='';
      } else {
        $bodega_where="
        AND
        stock_bod_id=$bodega
        ";
      }

      $bodega_reg = pg_query($conn, "
      SELECT bod_glosa FROM bodega WHERE bod_id=".$bodega."
      ");

      $bodega_row = pg_fetch_row($bodega_reg);

      $bodega_nombre = $bodega_row[0];





   for($i=0;$i<pg_num_rows($recetas);$i++) {

    $fila = pg_fetch_row($recetas);



    print('
   <table style="font-size:12px;">
   <tr style="text-align:center;">
   <td colspan=3 "font-weight:bold;">
   <font size=+1><b>
          Servicio de Salud Vi&ntilde;a del Mar - Quillota<br>
          Hospital San Mart&iacute;n de Quillota<br>
          Receta Hospitalizados</b>
   </font>
   </td>
   </tr>
   </table>
   <br>
   <table>
   <tr style="text-align:left;">
    <td>
    Centro/Servicio : <b>'.htmlentities($fila[1]).'</b>
    </td></tr>
   <tr style="text-align:left;"><td>Ubicaci&oacute;n :
   <b>'.htmlentities($bodega_nombre).'</b></td><td><b>Cama : _________</b></td></tr>
   <tr style="text-align:left;">
    <td>Receta Emisi&oacute;n : <b><i>'.htmlentities($fila[2]).'</i></b>
    </td></tr>
    <tr style="text-align:left;">
    <td>Rut : <b>'.htmlentities($fila[3]).'</b></td>
    <td>Nombre : <b>'.htmlentities($fila[4]).'</b></td>
    </tr>
    <tr style="text-align:left;">
    <td>Diagn&oacute;stico : <b>'.htmlentities($fila[7]).'</b></td>
    </tr>

    </table>
    ');

  print("
   <br>
   <br>
   <table align='center'>
    <div class='bincard' style='height:260px; >
    <font size=+2><b>
    <tr style='text-align:center;'><b>Prescripci&oacute;n : </b></tr>
    </font>
    </div>
    <br>
    <br>
    <br>
    <br>
  <td style='text-align:center;'>________________________<br>Firma M&eacute;dico</td>
  </table>
  ");

  print("

  <table>
  <br>
  <br>
  <br>
  <br>
  <tr>
  <td>Fecha : _____________</td>
 </tr>
  <br>
  <br>
  <br>
  <tr><td style=text-align: left;>Funcionario : ".htmlentities($fila[6])."</td></tr>
  </table>

   <div style='page-break-after: always;'>
  </div>
   ");

   }
   
?>



