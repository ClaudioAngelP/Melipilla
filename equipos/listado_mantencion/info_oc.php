<?php 

    require_once('../../conectar_db.php');
    
    $orden_id=$_POST['orden_id'];

      $cabecera = cargar_registros_obj("
      SELECT orden_compra.*, func_nombre, prov_rut, prov_glosa,
      date_trunc('second', orden_fecha) AS orden_fecha,
      orden_observacion, orden_iva, prov_fono, prov_ciudad, prov_direccion
      FROM orden_compra
      JOIN proveedor ON orden_prov_id=prov_id
      LEFT JOIN funcionario ON orden_func_id=func_id
      WHERE orden_id=".$orden_id."
      ");

    $dorden = cargar_registros_obj("
    SELECT 
    COALESCE(ordetalle_cant, 1) AS ordetalle_cant, 
    ordetalle_subtotal, 
    art_codigo, art_glosa, item_glosa FROM
    orden_detalle
    JOIN articulo ON ordetalle_art_id=art_id
    LEFT JOIN item_presupuestario ON art_item=item_codigo
    WHERE ordetalle_orden_id=".$cabecera[0]['orden_id']."
    ");
 
    $sorden = cargar_registros_obj("
    SELECT 
    orserv_subtotal, 
    orserv_glosa, orserv_cant, item_glosa
    FROM
    orden_servicios
    LEFT JOIN item_presupuestario ON orserv_item=item_codigo
    WHERE orserv_orden_id=".$cabecera[0]['orden_id']."
    ");
    
    
    $detalles_html='
    <table width=100% style="font-size: 11px;">
    <tr class="tabla_header" style="font-weight: bold;">
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Item Presupuestario</td>
    <td>Cantidad</td>
    <td>P. Unit.</td>
    <td>Subtotal</td>
    </tr>
    ';
    
    $total=0;
  
    if($dorden) {
      for($i=0;$i<count($dorden);$i++) {
    
      ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
      
      if($dorden[$i]['ordetalle_cant']==0)
        $dorden[$i]['ordetalle_cant']=1;
      
      $detalles_html.="
      <tr class='".$clase."'>
      <td style='text-align: right;'>".$dorden[$i]['art_codigo']."</td>
      <td>".htmlentities($dorden[$i]['art_glosa'])."</td>
      <td>".htmlentities($dorden[$i]['item_glosa'])."</td>
      <td style='text-align: right;'>".$dorden[$i]['ordetalle_cant']."</td>
      <td style='text-align: right;'>$".number_format($dorden[$i]['ordetalle_subtotal']/$dorden[$i]['ordetalle_cant'],1,',','.').".-</td>
      <td style='text-align: right;'>
      $".number_format($dorden[$i]['ordetalle_subtotal'],1,',','.').".-
      </td>
      </tr>
      ";
      
      $total+=($dorden[$i]['ordetalle_subtotal']*1);
      
      }
    }
 
    if($sorden) {
      for($i=0;$i<count($sorden);$i++) {
    
      ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
      
      $detalles_html.="
      <tr class='".$clase."'>
      <td style='text-align: right;'>(n/a)</td>
      <td>".htmlentities($sorden[$i]['orserv_glosa'])."</td>
      <td>".htmlentities($sorden[$i]['item_glosa'])."</td>
      <td style='text-align:right;'>
      ".number_format($sorden[$i]['orserv_cant'],1,',','.')."</td>
      <td style='text-align: right;'>
      $".number_format($sorden[$i]['orserv_subtotal']/$sorden[$i]['orserv_cant'],1,',','.').".-
      </td>
      <td style='text-align: right;'>
      $".number_format($sorden[$i]['orserv_subtotal'],1,',','.').".-
      </td>
      </tr>
      ";
      
      $total+=($sorden[$i]['orserv_subtotal']*1);
      
      }
    }
  
   
   $totalciva=$total*$cabecera[0]['orden_iva'];
   $iva=$totalciva-$total;
   
   $detalles_html.='
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>Neto:</td>
   <td>$'.number_format($total,1,',','.').'.-</td>
   </tr>
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>I.V.A.:</td>
   <td>$'.number_format($iva,1,',','.').'.-</td>
   </tr>
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>Total:</td>
   <td>$'.number_format($totalciva,1,',','.').'.-</td>
   </tr>
   </table>';

?>

<input type='hidden' id='orden_id' name='orden_id' value='<?php echo $orden_id; ?>'>

<table width=100% style="font-size: 12px;">
  
<tr>
<td style='text-align: right; width:150px;'>C&oacute;digo Interno:</td>
<td style='font-size: 20px;'><b><?php echo $cabecera[0]['orden_id'] ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>C&oacute;digo de &Oacute;rden:</td>
<td><b><?php echo $cabecera[0]['orden_numero'] ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>Fecha Emisi&oacute;n:</td>
<td><?php echo $cabecera[0]['orden_fecha']; ?></td>
</tr>
<tr>
<td style='text-align: right;'>RUT Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_rut']); ?>
</b></td>
</tr>

<tr>
<td style='text-align: right;'>Nombre Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_glosa']); ?>
</b></td>
</tr>

<tr>
<td style='text-align: right;'>Observaciones:</td>
<td>

<?php 
  if($cabecera[0]['orden_observacion'])
  {
    echo htmlentities($cabecera[0]['orden_observacion']);
  }
  else
  {
    echo '<i>No hay comentarios.</i>';
  }
?>

</td>
</tr>

</table>

<?php echo $detalles_html; ?>