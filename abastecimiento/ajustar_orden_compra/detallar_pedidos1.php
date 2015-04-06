<?php

  require_once('../../conectar_db.php');


  $bodega_origen = ($_GET['bodega_origen']*1);
  $id_pedido = ($_GET['id_pedido']*1);

  $pedido = cargar_registros_obj(
  "SELECT 
  pedido_id, 
  pedido_nro, 
  date_trunc('second', pedido_fecha) AS pedido_fecha, 
  'Abastecimiento' AS bod_glosa, 
  origen_bod_id, 
  pedido_comentario, 
  func_nombre
  FROM pedido 
  LEFT JOIN bodega ON bod_id=origen_bod_id
  JOIN funcionario ON pedido_func_id=func_id 
  WHERE
  origen_bod_id=$bodega_origen
  AND
  pedido_estado=0
  AND 
  pedido_id=$id_pedido
  ORDER BY pedido_fecha
  ", true);

  $art_filas = cargar_registros_obj("
  SELECT 
  art_codigo, 
  art_glosa, 
  pedidod_cant, 
  art_id, 
  coalesce(sum(stock_cant),0) AS stock_cant, 
  pedidod_estado,
  art_val_ult, item_codigo, item_glosa
  FROM pedido_detalle 
  JOIN articulo USING (art_id)
  LEFT JOIN item_presupuestario ON art_item=item_codigo
  LEFT JOIN stock_precalculado_trans ON
    stock_art_id=art_id AND stock_bod_id=$bodega_origen
  WHERE pedido_id=".$id_pedido."
  GROUP BY art_codigo, art_glosa, pedidod_cant, art_id, pedidod_estado, art_val_ult, item_codigo, item_glosa
  ", true);
    
    $detalle_html='';
    
    for($u=0;$u<count($art_filas);$u++) {
    
      $art_fila = $art_filas[$u];
      
      ($u%2==0)     ?   $clase2='tabla_fila'   :  $clase2='tabla_fila2';
      
      if($art_fila['pedidod_estado']=='t') $tachar='text-decoration: line-through;';
      else                                 $tachar='';
      
      $detalle_html.="
      <tr class='".$clase2."'>
      <td style='text-align: right; $tachar'>".$art_fila['art_codigo']."</td>
      <td style='$tachar'>".htmlentities($art_fila['art_glosa'])."</td>
      <td style='text-align: right; $tachar'>".number_formats($art_fila['pedidod_cant']).".-</td>
      <td style='text-align: right; $tachar'>".number_formats($art_fila['stock_cant']).".-</td>
      <td class='no_screen'><center>_________</center></td>
      </tr>
      ";      
    
    }
    
    
    print('
    <input type="hidden" 
    id="__numero_pedido_sel" 
    name="__numero_pedido_sel" 
    value="'.$pedido[0]['pedido_id'].'">
    
    <table style="font-size: 11px;" width=100%>
    <tr class="tabla_header"><td colspan=4>
    <center><b>Datos del Pedido</b></center>
    </td>
    </tr>
    <tr>
    <td style="text-align: right;">N&uacute;mero de Pedido:</td>
    <td style="font-weight: bold; width:200px;">'.$pedido[0]['pedido_nro'].'</td>
    <td>Fecha de Emisi&oacute;n:</td>
    <td style="font-weight: bold;">'.$pedido[0]['pedido_fecha'].'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Funcionario Emisor:</td>
    <td style="font-weight: bold;" colspan=3>'.htmlentities($pedido[0]['func_nombre']).'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Destino:</td>
    <td style="font-weight: bold;" colspan=3>'.htmlentities($pedido[0]['bod_glosa']).'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Comentarios:</td>
    <td colspan=3>'.$pedido[0]['pedido_comentario'].'</td>
    </tr>
    
    </table>
    <table width=100% style="font-size: 11px;">
    <tr class="tabla_header">
    <td width=20%>C&oacute;digo Int.</td>
    <td>Nombre</td>
    <td width=13%>Cant. Solicitada</td>
    <td width=10%>Stock</td>
    <td width=10% class="no_screen">Saldo F&iacute;sico</td>
    </tr>
    
    '.$detalle_html.'
    
    </table>
    ');
    
    $art_filas['length']=count($art_filas);
    

?>

<script>

pedido_detalle=<?php echo json_encode($art_filas); ?>;

pedido_datos=<?php echo json_encode($pedido); ?>;

</script>
