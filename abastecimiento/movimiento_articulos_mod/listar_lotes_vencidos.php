<?php
  set_time_limit(0);
  require_once('../../conectar_db.php');

   
  print("
  
	<div class='sub-content2'
		style='min-height:220px;
  		
  		height:220px;
      overflow: auto;'>
		
  ");
  
  $bodega=($_GET['bodega_origen']*1);
  
  $lotes_vencidos = pg_query($conn, "
  
  SELECT 
  stock_art_id,
  art_codigo,
  art_glosa,
  SUM(stock_cant) AS cantidad,
  stock_vence
  FROM stock
  LEFT JOIN articulo ON art_id=stock_art_id
  WHERE 
  stock_vence<current_date 
  AND 
  stock_vence IS NOT null
  AND
  stock_bod_id=$bodega
  GROUP BY stock_art_id, art_codigo, art_glosa, stock_vence
  ORDER BY stock_vence
  
  ");	
  
  
  
  

  
  
  
  
  if(pg_num_rows($lotes_vencidos)>0) {
  
  print("
  
  <table width=100%>
    <tr class='tabla_header' style='font-weight: bold;'>
      <td>C&oacute;digo Int.</td>
      <td>Nombre</td>
      <td>Fecha Venc.</td>
      <td>Cantidad</td>
      <td>Sel.</td>
    </tr>
  ");
  
  for($i=0;$i<pg_num_rows($lotes_vencidos);$i++) {
  
  ($i%2==1)		?		$clase='tabla_fila'	:	$clase='tabla_fila2';
  	
	$lote = pg_fetch_row($lotes_vencidos);

	print("
  <tr class='".$clase."' name='lote_".$lote[0]."' id='lote_".$lote[0]."'>
    <td style='text-align: right;'><i><b>".$lote[1]."</b></i></td>
    <td><b>".htmlentities($lote[2])."</b></td>
    <td style='text-align: right;'>".$lote[4]."</td>
    <td style='text-align: center;'><b><i>".$lote[3]."</i></b></td>
    <td>
      <center>
        <img src='iconos/error_go.png'
        onMouseOver='$(\"lote_".$lote[0]."\").className=\"mouse_over\";'
        onMouseOut='$(\"lote_".$lote[0]."\").className=\"".$clase."\";'
        onClick='seleccionar_articulo(".$lote[0].",\"".$lote[1]."\",\"".$lote[1]."\");'
        alt='Seleccionar Lote Vencido...'
        text='Seleccionar Lote Vencido...'
        >
      </center>
    </td>
  </tr>
  ");
}
  
print("</table>");
  
  } else {
  
  print("(No se encontr&oacute; stock vencido en la ubicaci&oacute;n seleccionada...)");
  
  }
  
  print("
  
  		</div>
		
  
  		</body>
  		</html>
  ");

?>
