<?php

  require_once("../../conectar_db.php");

?>

<html>
<title>Lista Pedidos</title>

<?php cabecera_popup('../..'); ?>

  <script>
  
  modo_win=0;
  
  __ver_pedido = function(id_pedido) {
  
  modo_win=1;
  fix_height();
  
  $('descrip_pedido_'+id_pedido).scrollTo();
  
  $('__detalle_pedidos').innerHTML='<table><tr><td style="width: 530px; height:160px;"><center><img src="../../imagenes/ajax-loader2.gif"></center></td></tr></table>';
  
  var myAjax = new Ajax.Updater(
  '__detalle_pedidos',
  'detallar_pedidos.php',
  {
    method: 'get',
    parameters: 'id_pedido='+id_pedido+'&'+window.opener.$('bodega_origen').serialize(),
    onComplete:function() {
		fix_height();
	}
  }
  );
  
  }
  
  __usar_pedido = function(__nro_pedido) {
  
    window.opener.$('numero_pedido').value=$('__numero_pedido_sel').value;
    
    var fn = window.opener.descargar_pedido.bind(window.opener);
    
    fn();

    window.close();
      
  }
  
  __imprimir_pedido = function() {
  
    imprimirHTML($('__detalle_pedidos').innerHTML);
  
  }
  
  fix_height=function() {
	  
	  var total=window.innerHeight-50;
	  
	  if(modo_win==1) {
		  
		$('__detalle_pedidos').style.display='';
		$('__funciones_pedido').style.display='';
	  
	  var height1=Math.round(total*0.3);
	  var height2=Math.round(total*0.6);
	  
	  $('__lista_pedidos').style.height=height1;
	  $('__detalle_pedidos').style.height=height2;
	  
	} else {

		$('__detalle_pedidos').style.display='none';
		$('__funciones_pedido').style.display='none';
		
	  $('__lista_pedidos').style.height=Math.round(total*0.9);
		
	} 	
	  
  }
  
  
  </script>

<body class='fuente_por_defecto popup_background' onLoad='fix_height();' onResize='fix_height();'>

  <div class="sub-content2" id="__lista_pedidos"
  style="height: 330px; overflow: auto;">
  
  <table width=100% style="font-size:12px;" id="__lista_pedidos">
  <tr class="tabla_header" style="font-weight: bold;">
  <td>Nro. de Pedido</td>
  <td>Procedencia</td>
  <td>Fecha de Emisi&oacute;n</td>
  <td>Detalle</td>
  </tr>
  


  <?php

  $bodega_origen=pg_escape_string($_GET['bodega_origen']);
  
  if(strstr($bodega_origen,'.')) {
    $cond="origen_centro_ruta='$bodega_origen'";
  } else {
    $cond="origen_bod_id=$bodega_origen";
  }

  
  $pedidos = pg_query($conn,
  "SELECT 
      pedido_id, 
      pedido_nro, 
      date_trunc('second', pedido_fecha) AS pedido_fecha, 
      COALESCE(b1.bod_glosa,b2.bod_glosa)AS bod_glosa,
      origen_bod_id
  FROM pedido 
  LEFT JOIN bodega AS b1 ON b1.bod_id=destino_bod_id
  LEFT JOIN bodega AS b2 ON b2.bod_id=origen_bod_id
  WHERE
  $cond
  AND
  pedido_estado=1
  ORDER BY pedido_fecha
  "
  );

  
  for($i=0;$i<pg_num_rows($pedidos);$i++) {
    
    $fila = pg_fetch_row($pedidos);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    
    
    print('
    
    <tr id="descrip_pedido_'.$fila[0].'" 
    class="'.$clase.'" style="cursor: default;"
    onMouseOver="this.className=\'mouse_over\';" 
    onMouseOut="this.className=\''.$clase.'\';"
    onClick="__ver_pedido('.$fila[0].');"
    >
    <td style="text-align: center;">'.$fila[1].'</td>
    <td style="text-align: center;">'.$fila[3].'</td>
    <td style="text-align: center;">'.$fila[2].'</td>
    <td>
    <center>
    <img src="../../iconos/page_white_put.png" style="cursor: pointer;"
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
		<img src='../../iconos/page_white_swoosh.png'>
		</td><td>
		<a href='#' onClick='__usar_pedido();'> Utilizar Pedido...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='../../iconos/printer.png'>
		</td><td>
		<a href='#' onClick='__imprimir_pedido();'>
		Imprimir Pedido...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
  
  </center>
  
  </div>

</body>
</html>
