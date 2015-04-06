<?php

  require_once('../../conectar_db.php');


  $bodega_origen = ($_GET['bodega_origen']*1);
  $id_pedido = ($_GET['id_pedido']*1);

  $pedido = pg_query($conn,
  "SELECT pedido_id, pedido_nro, date_trunc('second', pedido_fecha) AS pedido_fecha, bod_glosa, origen_bod_id, pedido_comentario, func_nombre
  FROM pedido 
  JOIN bodega ON bod_id=origen_bod_id
  JOIN funcionario ON pedido_func_id=func_id 
  WHERE
  destino_bod_id=$bodega_origen
  AND
  pedido_estado=0
  AND 
  pedido_id=$id_pedido
  ORDER BY pedido_fecha
  "
  );

  $pedido_a = pg_fetch_row($pedido);

  $detalle = pg_query($conn, "
  SELECT art_codigo, art_glosa, pedidod_cant, art_id, calcular_stock_trans(art_id, $bodega_origen), pedidod_estado
  FROM pedido_detalle 
  JOIN articulo USING (art_id)
  WHERE pedido_id=".$id_pedido."
  ");
    
    $detalle_html='';
    
    for($u=0;$u<pg_num_rows($detalle);$u++) {
    
      $art_fila = pg_fetch_row($detalle);
      
      ($u%2==0)     ?   $clase2='tabla_fila'   :  $clase2='tabla_fila2';
      
      if($art_fila[5]=='t') $tachar='text-decoration: line-through;';
      else                  $tachar='';
      
      $detalle_html.="
      <tr class='".$clase2."'>
      <td style='text-align: right; $tachar'>".$art_fila[0]."</td>
      <td style='$tachar'>".htmlentities($art_fila[1])."</td>
      <td style='text-align: right; $tachar'>".number_formats($art_fila[2]).".-</td>
      <td style='text-align: right; $tachar'>".number_formats($art_fila[4]).".-</td>
      <td class='no_screen'><center>_________</center></td>
      </tr>
      ";      
    
    }
    
    
    print('
    <input type="hidden" 
    id="__numero_pedido_sel" 
    name="__numero_pedido_sel" 
    value="'.$pedido_a[1].'">
    
    <table style="font-size: 11px;" width=100%>
    <tr class="tabla_header"><td colspan=4>
    <center><b>Datos del Pedido</b></center>
    </td>
    </tr>
    <tr>
    <td style="text-align: right;">N&uacute;mero de Pedido:</td>
    <td style="font-weight: bold; width:200px;">'.$pedido_a[1].'</td>
    <td>Fecha de Emisi&oacute;n:</td>
    <td style="font-weight: bold;">'.$pedido_a[2].'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Funcionario Emisor:</td>
    <td style="font-weight: bold;" colspan=3>'.htmlentities($pedido_a[6]).'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Destino:</td>
    <td style="font-weight: bold;" colspan=3>'.htmlentities($pedido_a[3]).'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Comentarios:</td>
    <td colspan=3>'.$pedido_a[5].'</td>
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

?>
