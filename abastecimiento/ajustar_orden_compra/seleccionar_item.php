<?php

  require_once('../../conectar_db.php');
  
  $fila = $_GET['fila']*1;
  $item = $_GET['item'];
  
  $items = pg_query("
            SELECT * FROM item_presupuestario 
            ORDER BY item_codigo
          ");
  
?>  

<html><title>Seleccionar Item Presupuestario</title>

<?php cabecera_popup('../..'); ?>

<script>

  seleccionar_item = function (itempres,itemglosa) {

    window.opener.$('item_id_<?php echo $fila?>').innerHTML=itempres;
    window.opener.$('txt_id_item_<?php echo $fila?>').value=itempres;
    html='<font color="0">'+itemglosa+'</font>';
    window.opener.$('txt_glosa_item_<?php echo $fila?>').value=itemglosa;
    window.opener.$('span_item_<?php echo $fila?>').innerHTML=html;
    window.close();

  }

</script>

<body class='fuente_por_defecto popup_background'>

<table style='width:100%; font-size:12px;'>
<tr class='tabla_header'>
<td>C&oacute;digo</td>
<td>Item Presupuestario</td>
</tr>
<?php   
  
  for($i=0;$i<pg_num_rows($items);$i++) {
  
    	($i%2==0) ? 	$clase='tabla_fila' : $clase='tabla_fila2';

      $datos = pg_fetch_row($items);

      if($datos[0]!=$item) 

      print ("
        <tr class='$clase' onClick='seleccionar_item(\"".$datos[0]."\",\"".$datos[1]."\");'
        onMouseOver='this.className=\"mouse_over\";'
        onMouseOut='this.className=\"$clase\";'>
        <td>".$datos[0]."</td>
        <td>".$datos[1]."</td>
        </tr>
      ");
      
      else
      
      print ("
        <tr class='$clase' onClick='(\"".$datos[0]."\",\"".$datos[1]."\");'
        onMouseOver='this.className=\"mouse_over\";'
        onMouseOut='this.className=\"$clase\";'
        style='font-weight: bold;'>
        <td>".$datos[0]."</td>
        <td>".$datos[1]."</td>
        </tr>
      ");
      
  
  }
  
?>

</table>
</body>
</html>
