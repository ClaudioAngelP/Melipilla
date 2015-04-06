<?php

  require_once('../../conectar_db.php');
  
  $bodega = $_GET['bodega']*1;
  $art_id = $_GET['art_id']*1;
  $punit = $_GET['punit']*1;
 
  
  $art = cargar_registro('SELECT * FROM articulo WHERE art_id='.$art_id);
  
  $precios = pg_query($conn, "
  SELECT 
  date_trunc('second', log_fecha),
  stock_cant,
  stock_subtotal,
  (stock_subtotal/stock_cant) as stock_punit
  FROM stock
  JOIN logs ON stock_log_id=log_id
  WHERE stock_art_id=$art_id AND log_tipo=1
  ORDER BY log_fecha;
  ");  
  
  print('<html><title>Historico de Precios</title>');
  
  cabecera_popup('../..');
  
  $tabla=''; $medio=0;
  
  for($i=0;$i<pg_num_rows($precios);$i++) {
  
    $fila = pg_fetch_row($precios);
    
    ($i%2==0) ? $clase = 'tabla_fila' : $clase = 'tabla_fila2';
    
    $tabla.="
    <tr class='".$clase."'>
    <td style='text-align: center;'>".$fila[0]."</td>
    <td style='text-align: right;'>".number_formats($fila[1])."</td>
    <td style='text-align: right;'>$".number_formats($fila[2]).".-</td>
    <td style='text-align: right;'>".number_format($fila[3],5,',','.')."</td>
    </tr>";
  
    $medio += $fila[3]; 
  
  }
  
  $valmed=$art['art_val_med'];
  
  if($valmed!=0)  $var = ($valmed-$punit)*100/$valmed;
  else            $var = 0;

  
?>

<script>

function modificar_articulo() {

  aceptar_precio=window.opener.aceptar_precio.bind(window.opener);
  
  if($('acepta').checked) {
    aceptar_precio(<?=$art_id;?>, true); window.close();
  } else {
    aceptar_precio(<?=$art_id;?>, false);
  }

}

</script>

<body class='fuente_por_defecto popup_background'
topmargin=0 rightmargin=0 leftmargin=0>

<table style='width:100%; font-size:12px;'>
<tr><td style='text-align: right;' class='tabla_header'>C&oacute;digo:</td>
<td style='font-weight:bold;font-size:13px;'>
<?php echo $art['art_codigo']; ?>
</td></tr>
<tr><td style='text-align: right;' class='tabla_header'>Glosa:</td><td>
<?php echo $art['art_glosa']; ?>
</td></tr>
<tr><td style='text-align: right;' class='tabla_header'>P. Unit. Medio:</td><td>
<b>$<?php echo number_format($valmed,5,',','.'); ?></b>
</td></tr>
<tr><td style='text-align: right;' class='tabla_header'>P. Unit. Actual:</td><td>
<b>$<?php echo number_format($punit,5,',','.'); ?></b>
</td></tr>
<tr><td style='text-align: right;' class='tabla_header'>Variaci&oacute;n:</td><td style='background-color:#ffffff;'><center>
<img src='../../graficos/variacion_precio.php?art_id=<?php echo $art_id?>&backgr=dfe6ef&pact=<?php echo urlencode($punit); ?>&bod_id=<?php echo urlencode($bodega); ?>'></center></td></tr>
<tr><td style='text-align: right;' class='tabla_header'>Diferencia:</td>
<td>
<b>
<?php

if(abs($var)>10) 
  print('<span style="color:red;">'); 
else 
  print('<span>');

if($var<=0) 
  print('+%'.number_format(abs($var),2,',','.'));
else
  print('-%'.number_format(abs($var),2,',','.'));

print('</span>');

?></b>
</td></tr>
</table>

<table style='width:100%; font-size:12px;'>
<tr class='tabla_header' style='font-weight: bold;'>
<td>Fecha</td>
<td>Cant.</td>
<td>Subtotal($)</td>
<td>P Unit.($)</td>
</tr>

<?php 

  print($tabla);
  
?>

<?php if(abs($var)>10) { ?>

<tr class='tabla_header'>
<td colspan=4 style='padding:5px;'>
<input type='checkbox' id='acepta' name='acepta' 
onClick='modificar_articulo();'>
Confirmar Ingreso con Alta Variaci&oacute;n en Precio
</td>
</tr>

<?php } else { ?>

<tr class='tabla_header'>
<td colspan=4 style='padding:5px;'>
El valor del art&iacute;culo est&aacute; dentro de los rangos aceptables de variaci&oacute;n del precio.
</td>
</tr>

<?php } ?>

</table>
</body>

<?php 

if(abs($var)>25) {

?>

<script>

if(window.opener.aceptado_precio(<?=$art_id?>)) {
  $('acepta').checked=true;
} else {
  $('acepta').checked=false;
}

</script>

<?php

}

?>

</html>
