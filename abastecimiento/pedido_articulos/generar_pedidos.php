<?php

    require_once("../../conectar_db.php");

    $articulos = $_POST['articulos'];
    
    $arts = split( '!', $articulos);
    
    $art_cadena='';
    $bod_cadena='';
    
    if($arts[0]=='' or $arts[0]==null) {
      die('[No hay art&iacute;culos seleccionados para realizar pedidos.]
      <script> $("nro_destinos").innerHTML="[ No hay destino(s) ]" </script>');
    }
      
    for($i=0;$i<count($arts);$i++) {
    
      $artx=split('-',$arts[$i]);
    
      $arts_cant[$artx[0]]=$artx[1];
    
      if($i==0) {
        if($i==(count($arts)-1))  $cadena_articulos="(".$artx[0];
        else                      $cadena_articulos="(".$artx[0].",";
      } else {
        if($i==(count($arts)-1))  $cadena_articulos.=$artx[0];
        else                      $cadena_articulos.=$artx[0].",";
      }
      
      if($i==(count($arts)-1)) $cadena_articulos.=')';
    
    }
    
      
      $_articulos = pg_query($conn,"
      SELECT art_id, art_codigo, art_glosa, bod_glosa, bod_id FROM (
      SELECT art_id, art_codigo, art_glosa 
      FROM articulo 
      WHERE art_id IN ".$cadena_articulos."
      ) AS articulov1
      JOIN bodega USING (bod_id)
      ORDER BY bod_glosa, art_glosa;
      "
      );
      
      $bod_glosa='';
      $pedido_contador=0;
      
      for($i=0;$i<pg_num_rows($_articulos);$i++) {
    
      $campos_art = pg_fetch_row($_articulos);
    
      if($campos_art[3]!=$bod_glosa) {
        
        if($bod_glosa!='') print('</table><hr>');
        
        print('<table width=100%>
        <tr>
        <tr class="tabla_header" style="font-weight: bold;">
        <td colspan=4>Destino del Proveedor:</td></tr>
        <tr class="tabla_fila">
        <td style="width: 100px; text-align: right;">Solicitar a:</td>
        <td colspan=2><b>'.htmlentities($campos_art[3]).'</b></td>
        <td style="width: 150px;">
        
        <table>
        <tr><td><img src="iconos/table.png"></td>
        <td>
        <a href="#" 
        onClick="
        toggle_visible(\'detalle_pedido_'.$pedido_contador.'\');
        toggle_text(this, \' Ver Detalle...\', \' Ocultar Detalle...\');
        "> Ver Detalle...</a></td></tr>
        </table>
        
        </td></tr>
        
        
        </table>
        <table style="display: none; width: 100%;" 
        id="detalle_pedido_'.$pedido_contador.'">
        <tr class="tabla_header" style="font-weight: bold;">
        <td>C&oacute;digo</td><td>Glosa</td>
        <td>Ubicaci&oacute;n</td><td>Cantidad</td></tr>
        ');
        
        $bod_glosa=$campos_art[3];
        $pedido_contador++;
    
        $bod_cadena.=$campos_art[4].',';
    
      }
      
      ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
      
      print("
      <tr class='$clase'>
      <td style='text-align: right;'><b>".$campos_art[1]."</b></td>
      <td><span id='_articulo_".$campos_art[0]."_2' class='texto_tooltip'
      >".htmlentities($campos_art[2])."</span></td>
      <td style='text-align: center;'>".htmlentities($campos_art[3])."</td>
      <td style='text-align: right;'>
      ".number_formats($arts_cant[$campos_art[0]]).".-
      </td>
      </tr>
      
      <script>
      TooltipManager.addAjax('_articulo_".$campos_art[0]."_2', { url: 'popups.php', options: {method: 'get', parameters: 'tipo=articulo&art_id=".($campos_art[0]*1)."'}}, 
      300, 300);
      </script>
      
      ");
      
      if($i!=0) {
        $art_cadena.='!'.$campos_art[4].'-'.$campos_art[0].'-'.$arts_cant[$campos_art[0]];
      } else {
        $art_cadena.=$campos_art[4].'-'.$campos_art[0].'-'.$arts_cant[$campos_art[0]];
      }
      
      }
    
      $bod_cadena=substr($bod_cadena,0,strlen($bod_cadena)-1);
    
      print('</table>
      
      <input type="hidden" id="articulos_pedidos" name="articulos_pedidos"
      value="'.$art_cadena.'">
      
      <input type="hidden" id="bodegas_pedidos" name="bodegas_pedidos"
      value="'.$bod_cadena.'">
      
      <script> $("nro_destinos").innerHTML="[ '.$pedido_contador.' destino(s) ]" </script>
       
      ');
    
    
?>
