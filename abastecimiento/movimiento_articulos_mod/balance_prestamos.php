<?php

  require_once('../../conectar_db.php');
  
  $bodega_origen    = ($_GET['bodega_origen']*1);
  $institucion      = ($_GET['institucion_destino']*1);
  
  $prestamos = pg_query($conn, "
  SELECT foo.*, instsol_desc, art_codigo, art_glosa FROM (
  SELECT instsol_id, stock_art_id, SUM(stock_cant) AS cantidad,
  date_trunc('second', log_fecha) AS log_fecha, log_id,log_comentario
  FROM stock
  JOIN logs ON stock_log_id=log_id
  JOIN cargo_instsol USING (log_id)
  WHERE stock_bod_id=$bodega_origen
  AND instsol_id=$institucion
  GROUP BY instsol_id, stock_art_id, log_fecha, log_id,log_comentario
  ) AS foo
  JOIN institucion_solicita USING (instsol_id)
  JOIN articulo ON stock_art_id=art_id
  WHERE NOT cantidad=0
  ORDER BY log_fecha DESC
  ");
  
  $totales = pg_query($conn, "
  SELECT * FROM (
  SELECT art_codigo, art_glosa, SUM(cantidad) AS saldo FROM (
  SELECT instsol_id, stock_art_id, SUM(stock_cant) AS cantidad,
  date_trunc('second', log_fecha) AS log_fecha, log_id
  FROM stock
  JOIN logs ON stock_log_id=log_id
  JOIN cargo_instsol USING (log_id)
  WHERE stock_bod_id=$bodega_origen
  AND instsol_id=$institucion
  GROUP BY instsol_id, stock_art_id, log_fecha, log_id
  ) AS foo
  JOIN institucion_solicita USING (instsol_id)
  JOIN articulo ON stock_art_id=art_id
  WHERE NOT cantidad=0
  GROUP BY art_codigo, art_glosa
  ORDER BY art_codigo DESC
  ) AS foo
  WHERE NOT saldo=0
  ");

  $tabla_balance = '
  <table width=100% style="font-size: 12px;">
  <tr class="tabla_header" style="font-weight: bold;">
  <td>Fecha</td>
  <td>Id. Movimiento</td>
  <td>Comentario</td>
  <td>Codigo Int.</td>
  <td>Glosa</td>
  <td>Cantidad</td>
  </tr>
  ';
  
  
  $tabla_totales = '
  <table width=100% style="font-size: 12px;">
  <tr class="tabla_header" style="font-weight: bold;">
  <td>Codigo Int.</td>
  <td>Glosa</td>
  <td>Cantidad</td>
  <td>Deuda</td>
  </tr>
  ';


  for($i=0;$i<pg_num_rows($prestamos);$i++) {
  
    $prestamo = pg_fetch_row($prestamos);

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    if($prestamo[2]>0) {
    
      // Deuda en Contra
    
      if($prestamo[2]>0) {
        $prestamo[2]='+'.number_format($prestamo[2],1,',','.');
      } else {
        $prestamo[2]='-'.number_format(abs($prestamo[2]),1,',','.');
      }
    
      $tabla_balance .='
      <tr class="'.$clase.'">
      <td style="text-align: center;">
      '.$prestamo[3].'
      </td>
      <td style="text-align: right;">
      '.$prestamo[4].'
      </td>
       <td style="text-align: right;">
      '.$prestamo[5].'
      </td>
      <td style="text-align: right;">
      '.$prestamo[7].'
      </td>
      <td>
      '.$prestamo[8].'
      </td>
      <td style="text-align: right;">
      '.$prestamo[2].'
      </td>
      </tr>
      ';

    } else {

      // Deuda a Favor

      if($prestamo[2]>0) {
        $prestamo[2]='+'.number_format($prestamo[2],1,',','.');
      } else {
        $prestamo[2]='-'.number_format(abs($prestamo[2]),1,',','.');
      }


    $tabla_balance .='
      <tr class="'.$clase.'">
      <td style="text-align: center;">
      '.$prestamo[3].'
      </td>
       <td style="text-align: right;">
      '.$prestamo[4].'
      </td>
       <td style="text-align: right;">
      '.$prestamo[5].'
      </td>
      <td style="text-align: right;">
      '.$prestamo[7].'
      </td>
      <td>
      '.$prestamo[8].'
      </td>
      <td style="text-align: right;">
      '.$prestamo[2].'
      </td>
      </tr>
      ';
    
    
    }
    
  }
  
  for($i=0;$i<pg_num_rows($totales);$i++) {
  
    $total = pg_fetch_row($totales);
    
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    if($total[2]>0) {
      $total[3]='+'.number_format($total[2],1,',','.');
    } else {
      $total[3]='-'.number_format(abs($total[2]),1,',','.');
    }
    
      $tabla_totales .='
      <tr class="'.$clase.'">
      <td style="text-align: right;">
      '.$total[0].'
      </td>
      <td>
      '.$total[1].'
      </td>
      <td style="text-align: right;">
      '.$total[3].'
      </td>
      <td style="text-align: center;">';
      
      if($total[2]>0) {
        $tabla_totales.='Deuda en Contra';
      } else {
        $tabla_totales.='Deuda a Favor';
      }
      
      $tabla_totales .='</td></tr>';
      
  }
  
  $tabla_balance.='</table>';

  $tabla_totales.='</table>';

?>

<html>
<title>Balance de Pr&eacute;stamos y Devoluciones

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>
<div class='sub-content'>
<img src='../../iconos/book.png'>
<b>Totales Generales</b>
</div>
<div id='totales' 
class='tabbed_content' style='overflow:auto;height:170px;'>
<?php echo $tabla_totales; ?>
</div>
<div class='sub-content'>
<img src='../../iconos/book_open.png'>
<b>Detalle de Movimientos</b>
</div>
<div id='movimientos' 
class='tabbed_content' style='overflow:auto;height:170px;'>
<?php echo $tabla_balance; ?>
</div>

</body>
</html>
