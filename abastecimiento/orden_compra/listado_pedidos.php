<?php

  require_once('../../conectar_db.php');
  
  function graficar($tramite, $total)  {
		
		if(($tramite*1)>0) {
		
			$tramite=round($tramite*100/$total);
			$resto=100-($tramite);
		
		} else {
		
			$html="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0>
						<tr>";
						
			$html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
			
			$html.="</tr></table>";
			
			return $html;
		
		}
			
		$html="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($tramite>0) $html.="<td style='width:$tramite%;background-color:#22CC22;'>&nbsp;</td>";
		if($resto>0) $html.="<td style='width:$resto%;background-color:#dddddd;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}

  //if($destino!=-1) {
  //  $destino_q = '(origen_bod_id='.$origen.' AND destino_bod_id=0)';
  //} else {
    $destino_q = '(destino_bod_id=0)';
  //}
  
  $estado_pedido=$_GET['estado_pedidos']*1;
  
  if($estado_pedido==-1) {
    $estado='';
  } else if($estado_pedido==1) {
    $estado='AND pedido_tramite';
  } else {
    $estado='AND ((NOT pedido_tramite) OR pedido_tramite IS NULL)';
  }
    
  $pedidos = pg_query($conn,
  "SELECT DISTINCT pedido_id, pedido_nro, date_trunc('second', pedido_fecha) AS pedido_fecha, COALESCE(b1.bod_glosa, centro_nombre), 
  COALESCE(b2.bod_glosa, 'Abastecimiento'), pedido_estado, 
  origen_bod_id,
  destino_bod_id, pedido_autorizado, pedido_tramite,
  (SELECT count(*) FROM pedido_detalle WHERE pedido_detalle.pedido_id=pedido.pedido_id) AS total,
  (SELECT count(*) FROM pedido_detalle 	WHERE pedido_detalle.pedido_id=pedido.pedido_id AND pedidod_tramite) AS total_tramite
  FROM pedido 
  LEFT JOIN bodega AS b1 ON b1.bod_id=origen_bod_id
  LEFT JOIN logs ON log_id_pedido=pedido_id
  LEFT JOIN cargo_centro_costo ON logs.log_id=cargo_centro_costo.log_id
  LEFT JOIN centro_costo USING (centro_ruta)
  LEFT JOIN bodega AS b2 ON b2.bod_id=destino_bod_id
  JOIN funcionario AS f1 ON f1.func_id=pedido_func_id
  WHERE
  $destino_q
  AND
  pedido_estado = 0 AND pedido_autorizado $estado
  ");
  
  print('<table width=100%>
  <tr class="tabla_header" style="font-weight: bold;">
  <td>Nro. Pedido</td>
  <td>Fecha/Hora</td>');
  
  print("
  <td>Ubicaci&oacute;n Or&iacute;gen</td>
  <td>Estado</td>
  <td>Descargar Adq.</td>
  <td>En Tr&aacute;mite</td>  
  ");
  
  
  print("</tr>");
  
  for($i=0;$i<pg_num_rows($pedidos);$i++) {
  
    $pedido_a=pg_fetch_row($pedidos);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    print('
    <tr class="'.$clase.'" 
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';"
    >
    <td style="text-align: center; font-weight: bold;">'.$pedido_a[1].'</td>
    <td style="text-align: center; font-weight: bold; font-size: 10px;">
    '.$pedido_a[2].'</td>');
    
    //onClick="abrir_pedido('.$pedido_a[1].');"
    
    if($pedido_a[6]!=0)
      print('<td style="font-size: 10px;">'.htmlentities($pedido_a[3]).'</td>');
    else
      print('<td style="font-size: 10px; color: green;">
      '.htmlentities($pedido_a[3]).'</td>');
    
    
    if($pedido_a[8]=='t') {
    
      switch($pedido_a[5]) {
        case 0: $pedido_a[5]='Enviado'; break;
        case 1: $pedido_a[5]='Retornado'; break;
        case 2: $pedido_a[5]='Terminado'; break;
        case 3: $pedido_a[5]='Anulado'; break;
      }
    
    } else {
      
      $pedido_a[5]='Sin Autorizaci&oacute;n';
    
    }
    
    print('<td style="text-align: center;">'.$pedido_a[5].'</td>');

    print('<td style="text-align: center;"><img src="iconos/table_gear.png" 
                style="cursor:pointer;" 
                onClick="descargar_adq('.$pedido_a[1].');"
                id="pedido_descarga_'.$pedido_a[0].'"></td>');
    

    print('<td style="text-align: center;"><center>'.graficar($pedido_a[11],$pedido_a[10]).' <b>(
		'.$pedido_a[11].'/'.$pedido_a[10].'
      )</b></center></td>');
    
       
    
    print('</tr>');
  
  
  }
  
  print("</table>");

?>

