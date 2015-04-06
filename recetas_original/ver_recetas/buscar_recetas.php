<?php

  require_once('../../conectar_db.php');

  $bodega_id = ($_GET['bodega_id']*1);  //***********************************

  $fecha_w='true';
  $paciente_w='true';
  $medico_w='true';
  $art_w='true';
  $centro_w='true';
  $receta_w='true';  //***********************************
  $alta_w='true';  //***********************************
  
  if(isset($_GET['fecha1']) AND isset($_GET['fecha2'])) {
    $fecha1 = $_GET['fecha1'];
    $fecha2 = $_GET['fecha2'];
    $fecha_w = "date_trunc('day', receta_fecha_emision) BETWEEN '".pg_escape_string($fecha1)."' AND '".pg_escape_string($fecha2)."'";
  }

  if(isset($_GET['pac_c'])) {
    $paciente_id = $_GET['pac_id'];
    $paciente_w = 'pac_id='.($paciente_id*1);
  }

  if($_GET['receta_a']*1==1) {
	$alta_w='receta_prov_alta';
  } elseif($_GET['receta_a']*1==2) {
	$alta_w='NOT receta_prov_alta';
  }
  
  if(isset($_GET['med_c'])) {
  
    $medico_rut = $_GET['rut_medico'];

    $medico_q = pg_query($conn, "
    SELECT doc_id FROM doctores WHERE
    doc_rut='".pg_escape_string($medico_rut)."'
    ");

    if(pg_num_rows($medico_q)!=1) {
      die('<b>Error Inesperado. <i>(recetas/ver_recetas/buscar_recetas.php: medico buscado no existe en db.)</i></b>');
    }

    $medico_a = pg_fetch_row($medico_q);

    $medico_id=$medico_a[0];

    $medico_w = 'receta_doc_id='.($medico_id*1);
  }

  if(isset($_GET['art_c'])) {
    $art_id = $_GET['art_id'];
    $art_w = "stock_art_id=".($art_id*1);
  }

  if(isset($_GET['centro_c'])) {
    
    if($_GET['centro_servicio']==-1) {
      $centro_costo = $_GET['centro_costo'];
    } else {
      $centro_costo = $_GET['centro_servicio'];
    }

    $centro_w = "receta_centro_ruta='".pg_escape_string($centro_costo)."'";

  }

 //*******************************************************************
   if(isset($_GET['receta_c'])) {
    
    $receta_num = $_GET['receta_num']*1;

    $receta_q = pg_query($conn, "
    SELECT receta_id FROM receta WHERE
    receta_numero=".($receta_num)."
    ");

    if(!$receta_q OR pg_num_rows($receta_q)==0) {
      die('<center><br><br><b>Error Inesperado. <i>(N&uacute;mero de Receta no existente.)</i></b></center>');
    }

    $receta_ids='(';
    $num=pg_num_rows($receta_q);

    for($i=0;$i<$num;$i++) {    
      
      $receta_a = pg_fetch_row($receta_q);
      
      if($i<$num-1)
        $receta_ids .= $receta_a[0].',';
      else
        $receta_ids .= $receta_a[0];

    }
    
    $receta_ids.=')';

    $receta_w = 'receta_id IN '.($receta_ids);


  }
//*******************************************************************



?>


<div class='sub-content3' style='height:450px;overflow:auto;'>

<table width=100%>
<tr class='tabla_header' style='font-weight: bold;'>
<td colspan=4>
Datos de la Receta
</td>
<td colspan=3 style='width: 100px;'>
Acciones
</td>
</tr>
<tr class='tabla_header' style='font-weight: bold;'>
<td>Fecha Emisi&oacute;n</td>
<td>RUT Paciente</td>
<td width='40%'>Nombre Paciente</td>
<td>Centro de Costo</td>
<td>Detalle</td>
<?php
   if(_cax(21)) { print('<td>Eliminar</td>'); }
?>


</tr>

<?php

  $query="
  SELECT DISTINCT
  receta_id, date_trunc('second', receta_fecha_emision), pac_rut,
  pac_appat || ' ' || pac_apmat || ' ' || pac_nombres,
  centro_nombre, receta_fecha_emision
  FROM receta
  LEFT JOIN pacientes ON receta_paciente_id=pac_id
  LEFT JOIN centro_costo ON receta_centro_ruta=centro_ruta
  LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
  LEFT JOIN logs ON log_recetad_id=recetad_id
  LEFT JOIN stock ON stock_log_id=log_id
  WHERE
  $fecha_w AND
  $paciente_w AND
  $medico_w AND
  $art_w AND
  $centro_w AND
  $receta_w AND
  $alta_w AND
  stock_bod_id=".$bodega_id."
  ORDER BY receta_fecha_emision DESC
  ";

  // print($query);

  $recetas = pg_query($conn, $query);

  for($i=0;$i<pg_num_rows($recetas);$i++) {

    $fila = pg_fetch_row($recetas);

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

    print('
    <tr class="'.$clase.'">
    <td style="text-align: center;">
    <i>'.htmlentities($fila[1]).'</i>
    </td>
    <td style="text-align: center;">
    <b><i>'.htmlentities($fila[2]).'</i></b>
    </td>
    <td>
    '.htmlentities($fila[3]).'
    </td>
    <td style="text-align: center;">
    '.htmlentities($fila[4]).'
    </td>


    ');

      print('
    <td>
    <center>
    <img src="iconos/zoom_in.png" style="cursor: pointer;"
    onClick="visualizar_receta('.$fila[0].');"
    alt="Ver Receta..."
    title="Ver Receta...">
    </center>
    </td>

    ');


    if(_cax(21)) {

    print('
    <td>
    <center>
    <img src="iconos/delete.png" style="cursor: pointer;"
    onClick="eliminar_receta('.$fila[0].');"
    alt="Eliminar Receta..."
    title="Eliminar Receta...">
    </center>
    </td>
    ');

  	 }

	 print('</tr>');
   // onClick="editar_receta('.$fila[0].' );"
 }

?>

</table>
</div>
