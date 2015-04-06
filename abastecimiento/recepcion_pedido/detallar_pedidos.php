<?php

  require_once('../../conectar_db.php');

  $bodega_origen = pg_escape_string($_GET['bodega_origen']);
  $id_pedido = ($_GET['id_pedido']*1);

	$signo='';

  if(strstr($bodega_origen,'.'))
  	 $centro=cargar_registro("SELECT * FROM centro_costo 
  	 									WHERE centro_ruta='$bodega_origen'");

  if(strstr($bodega_origen,'.') AND $centro['centro_stock']=='t') {

    $cond="origen_centro_ruta='$bodega_origen'";
    $cond2="stock_centro_ruta='$bodega_origen'";
    $tabla_stock="stock_servicios";

  } else {
  
    if(strstr($bodega_origen,'.')) {
  		$cond="origen_centro_ruta='$bodega_origen'";
  		$destino = cargar_registro("SELECT * FROM pedido WHERE pedido_id=".$id_pedido);
    	$cond2="stock_bod_id=".$destino['destino_bod_id'];
    } else {
  		$cond="origen_bod_id=$bodega_origen";
    	$cond2="stock_bod_id=$bodega_origen";
    }

    $tabla_stock="stock";  		
  		
  }


  $pedido = pg_query($conn,
  "SELECT 
  	pedido_id, 
  	pedido_nro, 
  	date_trunc('second', pedido_fecha) AS pedido_fecha, 
  	bod_glosa, 
  	origen_bod_id, 
  	pedido_comentario, 
  	func_nombre
  FROM pedido 
  	JOIN bodega ON bod_id=destino_bod_id
  	JOIN funcionario ON pedido_func_id=func_id 
  WHERE
  	$cond
  AND
  	pedido_estado=1
  AND 
  	pedido_id=$id_pedido
  ORDER BY pedido_fecha
  "
  );

  $pedido_a = pg_fetch_row($pedido);

  /*
  $detalle = pg_query($conn, "
  SELECT art_codigo, art_glosa, pedidod_cant, art_id, calcular_stock(art_id, $bodega_origen)
  FROM pedido_detalle 
  JOIN articulo USING (art_id)
  WHERE pedido_id=".$id_pedido."
  ");
  */
    
  $detalle = cargar_registros_obj("
  
	SELECT *,

	COALESCE(
	
	(select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	join pedido_log_rev using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0)
	
	,0) AS recepcionado,

	COALESCE(
	
	(select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	left join pedido_log_rev AS plog using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0 AND plog.pedidolog_id is null)	
	
	,0) AS no_recepcionado

	FROM pedido_detalle AS pd1 
	JOIN articulo USING (art_id)
	WHERE pedido_id=$id_pedido
	ORDER BY art_glosa;

  ",true);
  
    $detalle_html='';
    
    for($u=0;$u<sizeof($detalle);$u++) {
    
      $art_fila = $detalle[$u];
      
      ($u%2==0)     ?   $clase2='tabla_fila'   :  $clase2='tabla_fila2';
      
      //if($art_fila[5]=='t') $tachar='text-decoration: line-through;';
      //else                  $tachar='';
      
	  $pendiente=($art_fila['pedidod_cant']*1)-($art_fila['recepcionado']*1);
      
      $enviada=$art_fila['no_recepcionado']*1;
      
      $diferencia=($pendiente*1)-($art_fila['no_recepcionado']*1);
      
      if($diferencia>0) $signo='-'; else $signo='+';
      
      $diferencia=abs($diferencia);
      
      $detalle_html.="
      <tr class='".$clase2."'>
      <td style='text-align: right; $tachar'>".$art_fila['art_codigo']."</td>
      <td style='$tachar'>".htmlentities($art_fila['art_glosa'])."</td>
      <td style='text-align: right; $tachar'
      >".number_format($pendiente,1,',','.').".-</td>
      <td style='text-align: right; $tachar'
      >";
      
      $detalle_html.=number_format($enviada,1,',','.');
      
      $detalle_html.=".-</td>
      <td style='text-align: right; $tachar'>
      ".$signo.''.number_format($diferencia,1,',','.').".-
      </td>
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
    <td style="text-align: right;">Procedencia:</td>
    <td style="font-weight: bold;" colspan=3>'.htmlentities($pedido_a[3]).'</td>
    </tr>
    <tr>
    <td style="text-align: right;">Comentarios:</td>
    <td colspan=3>'.htmlentities($pedido_a[5]).'</td>
    </tr>
    
    </table>
    <table width=100% style="font-size: 11px;">
    <tr class="tabla_header">
    <td width=20%>C&oacute;digo Int.</td>
    <td>Nombre</td>
    <td width=13%>Cant. Pendiente</td>
    <td width=10%>Cant. Enviada</td>
    <td width=10%>Diferencia</td>
    </tr>
    
    '.$detalle_html.'
    
    </table>
    ');

?>
