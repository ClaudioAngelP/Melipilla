<?php

  require_once("../../conectar_db.php");

?>


  <script>
  
  __ver_pedido = function(id_pedido) {
  
  $('__lista_pedidos').style.height='110px';
  $('__detalle_pedidos').style.display='';
  $('__funciones_pedido').style.display='';
  
  $('descrip_pedido_'+id_pedido).scrollTo();
  
  $('__detalle_pedidos').innerHTML='<table><tr><td style="width: 530; height:160px;"><center><img src="imagenes/ajax-loader2.gif"></center></td></tr></table>';
  
  var myAjax = new Ajax.Updater(
  '__detalle_pedidos',
  'abastecimiento/movimiento_articulos/detallar_pedidos.php',
  {
    method: 'get',
    parameters: 'id_pedido='+id_pedido+'&'+$('bodega_origen').serialize()
  }
  );
  
  }
  
  __usar_pedido = function(__nro_pedido) {
  
    $('numero_pedido').value=$('__numero_pedido_sel').value;
    
    $("busca_pedidos").win_obj.close();
    
    descargar_pedido();
  
  }
  
  __imprimir_pedido = function() {
  
    imprimirHTML($('__detalle_pedidos').innerHTML);
  
  }
  
  
  </script>

  <div class="sub-content2" id="__lista_pedidos"
  style="height: 330px; overflow: auto;">
  
  <table width=100% style="font-size:12px;" id="__lista_pedidos">
  <tr class="tabla_header" style="font-weight: bold;">
  <td>Nro. de Pedido</td>
  <td>Ubicaci&oacute;n Destino</td>
  <td>Fecha de Emisi&oacute;n</td>
  <td>Detalle</td>
  </tr>
  


  <?php

  $bodega_origen=($_GET['bodega_origen']*1);
  
  $pedidos = pg_query($conn,
  "SELECT 
    pedido_id, 
    pedido_nro, 
    date_trunc('second', pedido_fecha) AS pedido_fecha, 
    COALESCE(bod_glosa, centro_costo.centro_nombre), 
    origen_bod_id, origen_centro_ruta
  FROM pedido 
  LEFT JOIN bodega ON bod_id=origen_bod_id
  LEFT JOIN centro_costo ON origen_centro_ruta=centro_ruta
  WHERE
  destino_bod_id=$bodega_origen
  AND
  pedido_estado=0 AND pedido_autorizado
  ORDER BY pedido_fecha
  "
  );
  
  for($i=0;$i<pg_num_rows($pedidos);$i++) {
    
    $fila = pg_fetch_row($pedidos);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    if($fila[4]==0)
      $estilo='font-style:italic;';
    else
      $estilo='';
    
    print('
    
    <tr id="descrip_pedido_'.$fila[0].'" 
    class="'.$clase.'" style="cursor: default;"
    onMouseOver="this.className=\'mouse_over\';" 
    onMouseOut="this.className=\''.$clase.'\';"
    onClick="__ver_pedido('.$fila[0].');"
    >
    <td style="text-align: center;">'.$fila[1].'</td>
    <td style="text-align: center;'.$estilo.'">'.htmlentities($fila[3]).'</td>
    <td style="text-align: center;">'.$fila[2].'</td>
    <td>
    <center>
    <img src="iconos/page_white_put.png" style="cursor: pointer;"
    alt="Desplegar Detalle..."
    title="Desplegar Detalle...">
    </center>
    </td>
    </tr>
    
    ');
    
  }
  
?>

  </table>
  
  </div>
  
  <div class='sub-content2' id='__detalle_pedidos'
  style='display: none; height:180px; overflow: auto;'>
  
  </div>
  
  <div id='__funciones_pedido' style='display: none;'>
  
  <center>
  
  <table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/page_white_swoosh.png'>
		</td><td>
		<a href='#' onClick='__usar_pedido();'> Utilizar Pedido...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='__imprimir_pedido();'>
		Imprimir Pedido...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
  
  </center>
  
  </div>
  
