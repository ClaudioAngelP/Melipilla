<?php

    require_once("../../conectar_db.php");

    $codigo = pg_escape_string($_GET['codigo']);
	  $bodega = ($_GET['bodega']*1);
	  if(isset($_GET['fecha1'])) $fecha1 = pg_escape_string($_GET['fecha1']);
    if(isset($_GET['fecha2'])) $fecha2 = pg_escape_string($_GET['fecha2']);
    $modglobal = ($_GET['modglobal']*1);
    
    if(isset($fecha1) and isset($fecha2)) {
      $metodo=0;
      $gasto_query="calcular_gasto(art_id, $bodega, '$fecha1', '$fecha2')";
    } else {
      $metodo=1;
      $gasto_query='0';
    }
       
    $mods = $_GET['cant']*1;
        
    for($i=0;$i<($mods-1);$i++) {
    
      $modifica[$i][0]=$_GET['mod'.$i.'_sel'];
      $modifica[$i][1]=$_GET['mod'.$i.'_cant']*1;
      
    }
    
      
    $stocks = pg_query($conn,
    "
    SELECT *, (-(stock_gasto)+COALESCE(critico_pedido,0)) FROM
    (
    SELECT
    art_id,
    art_codigo,
    art_glosa,
    calcular_stock(art_id, $bodega) AS stock,
    $gasto_query AS stock_gasto,
    critico_critico,
    critico_pedido,
    art_clasifica_id,
    art_val_ult
    FROM
    articulo
    LEFT JOIN stock_critico ON art_id=critico_art_id AND critico_bod_id=$bodega
    WHERE art_codigo='$codigo'
    ) AS ss
    "
    );
    
      
      $fila = pg_fetch_row($stocks);
      
      $modif=$modglobal;
      
      for($u=0;$u<($mods-1);$u++) {
      if($fila[7]==$modifica[$u][0])
      $modif+=$modifica[$u][1];
      
      }
    
      ($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
      
      $sugerido=(($fila[9]*1)-($fila[3]*1))+(($fila[9]*1)-($fila[3]*1))/100*$modif;
      
      if($sugerido<0) $sugerido=0;
      
      print("
      <tr id='fila_".$fila[0]."' class='".$clase."'
      onMouseOver='this.className=\"mouse_over\";'
      onMouseOut='this.className=this.clase;'>
      <input type='hidden' name='valor_".$fila[0]."' id='valor_".$fila[0]."'
       value='".$fila[8]."'>
      <input type='hidden' name='sugiere_".$fila[0]."' id='sugiere_".$fila[0]."'
       value='".floor($sugerido)."'>
      <td style='text-align: right;'><B>".$fila[1]."</B></td>
      <td><span id='articulo_".$fila[0]."' class='texto_tooltip'
      >".htmlentities($fila[2])."</span></td>
      <td style='text-align: right;'><i>".$fila[3]."</i></td>
      <td style='text-align: right;'>".$fila[4]."</td>
      <td><center>
      <input type='text' size=6 style='text-align: right;' 
      id='cantidad_".$fila[0]."' name='cantidad_".$fila[0]."'
      value='".floor($sugerido)."' 
      onKeyUp='recalcular();'
      onClick='this.select();'>
      </center>
      </td><td>
      <center>
      <img src='iconos/lightbulb.png' id='icono_".$fila[0]."'
      style='cursor: pointer;'
      onClick='devolver_cant(".$fila[0].")'
      alt='Cantidad Sugerida: ".floor($sugerido)."'
      title='Cantidad Sugerida: ".floor($sugerido)."'>
      </center></td>
      <td style='text-align: right;'>
      </td>
      <td><center><img src='iconos/delete.png' style='cursor: pointer;'
      alt='Quitar Art&iacute;culo de la Lista...'
      title='Quitar Art&iacute;culo de la Lista...'
      onClick='eliminar_articulo(".$fila[0].");'></center></td>
      </tr>
      ");
    
?>
