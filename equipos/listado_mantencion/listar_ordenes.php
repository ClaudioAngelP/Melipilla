<?php

  require_once('../../conectar_db.php');
  
  $eot_id = $_POST['eot_id'];
  
  $ordenes = cargar_registros_obj(
  "
  SELECT 
  orden_id,
  COALESCE(orden_numero, orden_id::text) AS orden_numero,
  date_trunc('second', orden_fecha) AS orden_fecha,
  prov_rut,
  prov_glosa,
  array(
    SELECT pedido_nro FROM orden_pedido 
    JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id
    WHERE orden_pedido.orden_id=orden_compra.orden_id
  ) AS pedido_nro,
  orden_estado
  FROM orden_compra
  JOIN equipo_orden_compra USING (orden_id)
  JOIN proveedor ON prov_id=orden_prov_id
  WHERE equipo_orden_compra.eot_id=$eot_id
  ORDER BY orden_fecha DESC
  ", 
  false
  );

?>

<table style="width:100%">
<tr class="tabla_header">
<td>
Nro. Interno
</td>
<td>
C&oacute;digo &Oacute;rden
</td>
<td>
Fecha Ingreso
</td>
<td>
RUT Proveedor
</td>
<td>
Nombre Proveedor
</td>
<td colspan=2>
Acci&oacute;n
</td>
</tr>

<?php 

  if($ordenes)
  for($i=0;$i<count($ordenes);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    $orden=$ordenes[$i];
    
    switch($orden['orden_estado']) {
      case 0: $estado='Espera Recep.'; break;
      case 1: $estado='Recep. Parcial'; break;
      case 2: $estado='Recep. Completa'; break;
      case 3: $estado='Recep. Anulada'; break;
      
    }
    
    $pnro=pg_array_parse($orden['pedido_nro']);
    $pedidoshtml='';
    
    for($j=0;$j<count($pnro);$j++) {
      $pedidoshtml.=$pnro[$j];
      if($j<count($pnro)-1) $pedidoshtml.='<br>';
    }
    
    print('
    <tr class="'.$clase.'" style="cursor:pointer;"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\'">
    <td style="text-align:center;">'.$orden['orden_id'].'</td>
    <td style="text-align:center;">'.$orden['orden_numero'].'</td>
    <td style="text-align:center;">'.$orden['orden_fecha'].'</td>
    <td style="text-align:center;">'.$orden['prov_rut'].'</td>
    <td>'.htmlentities($orden['prov_glosa']).'</td>
    <td style="text-align:center;">
        <center>
        <img src="iconos/magnifier.png" style="cursor:pointer;" onClick="abrir_orden('.$orden['orden_id'].');">
        </center>
    </td>
    <td style="text-align:center;">
        <center>
        <img src="iconos/delete.png" style="cursor:pointer;" onClick="quitar_oc('.$orden['orden_id'].');">
        </center>
    </td>
    </tr>
    ');
    
  }

?>

</table>


