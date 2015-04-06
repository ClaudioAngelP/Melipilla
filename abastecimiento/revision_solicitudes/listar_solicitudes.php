<?php

  require_once('../../conectar_db.php');
  
  $bodega_id=$_GET['bodega_id'];
  
  if(strstr($bodega_id,'.')) {
    $ubica="sol_centro_ruta='$bodega_id'";
  } else {
    $ubica="sol_bod_id=".($bodega_id*1);
  }
  
  $sols=cargar_registros_obj("
    SELECT *,
    date_trunc('second', sol_fecha) AS sol_fecha,
    COALESCE(bod_glosa, centro_nombre) AS sol_ubica
    FROM solicitud_compra 
    LEFT JOIN bodega ON sol_bod_id=bod_id
    LEFT JOIN centro_costo ON sol_centro_ruta=centro_ruta
    LEFT JOIN solcompra_estado ON sol_estado=solce_id
    WHERE $ubica
    ORDER BY solicitud_compra.sol_fecha DESC
  ");
  
?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>N&uacute;mero</td>
<td>Fecha</td>
<td>Ubicaci&oacute;n</td>
<td>Urgente</td>
<td>Estado</td>

</tr>

<?php
  
  for($i=0;$i<count($sols);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    ($sols[$i]['sol_urgente']=='t') ? $urgente='S&iacute;' : $urgente='No';
  
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\";'
    onClick='abrir_solicitud(".$sols[$i]['sol_id'].");'>
    <td style='text-align:center;font-weight:bold;'>".$sols[$i]['sol_id']."</td>
    <td style='text-align:center;'>".$sols[$i]['sol_fecha']."</td>
    <td>".htmlentities($sols[$i]['sol_ubica'])."</td>
    <td style='text-align:center;'>".$urgente."</td>
    <td style='text-align:center;'>".htmlentities($sols[$i]['solce_desc'])."</td>
    </tr>
    ");  
  
  }

?>

</table>
