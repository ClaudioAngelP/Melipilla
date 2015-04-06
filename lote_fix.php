<?php

  require_once("conectar_db.php");
  
  $art_id=$_GET['art_id'];
  
  if(isset($_GET['vence']))
    $vence="stock_vence='".$_GET['vence']."'";
  else
    $vence='true';
    
  if(isset($_GET['bod_id']))
    $bod_id="stock_bod_id='".$_GET['bod_id']."'";
  else
    $bod_id='true';
    
  
  $todo = pg_query("
  SELECT 
  log_fecha, stock_bod_id ,stock_art_id, stock_vence, stock_cant, 
  stock_id, log_tipo, log_id, pedido_id, pedido_estado
  FROM stock 
  LEFT JOIN logs ON log_id=stock_log_id
  LEFT JOIN pedido ON log_id_pedido=pedido_id
  WHERE stock_art_id=$art_id AND $vence AND $bod_id
  ORDER BY log_fecha
  ");
  
  $lotes = Array();
  
  function contabilizar($bod_id, $art_id, $vence, $cant,$estado) {
  
    GLOBAL $lotes;
    $encontrado=false;
    
    for($a=0;$a<count($lotes);$a++) {
    
    if($lotes[$a][0]==$bod_id AND $lotes[$a][1]==$art_id AND $lotes[$a][2]==$vence) {
        
        $lotes[$a][3]+=$cant;

        
        if($lotes[$a][3]<0) {
          return false;
        } else {
          return true;
        }
        
      }
      
    }
    
    if(!$encontrado) {
    	$a=sizeof($lotes);
      $lotes[$a][0]=$bod_id;
      $lotes[$a][1]=$art_id;
      $lotes[$a][2]=$vence;
      $lotes[$a][3]=$cant;

      if($cant<0) {
        return false;
      } else {
        return true;
      }

    }
    
  
  }
    
  print("<table><tr>
		<td style='text-align: center;'>N°</td>
		<td style='text-align: center;'>Fecha</td>
		<td style='text-align: center;'>Bodega</td>
		<td style='text-align: center;'>Func.</td>
		<td style='text-align: center;'>Art.</td>
		<td style='text-align: center;'>Lote</td>
		<td style='text-align: center;'>Cant.</td>
		<td style='text-align: center;'>Total</td>
		<td style='text-align: center;'>Stock</td>
		<td style='text-align: center;'>Logs</td>
		<td style='text-align: center;'>Pedido</td>
		<td style='text-align: center;'>Estado</td>
		</tr>");
  
  $saldo=0;
  
  for($i=0;$i<pg_num_rows($todo);$i++) {
  
    $fila = pg_fetch_row($todo);
    
	if ($fila[9]<>3){
    $saldo+=$fila[4];
	}
	
    if(contabilizar($fila[1], $art_id, $fila[3], $fila[4],$fila[9])) {
      $color='';
    } else {
      $color="style='background-color: #dddddd;'";
    }
    
    print("
    <tr $color>
    <td><b>".$i."</b></td>
    <td>".$fila[0]."</td>
    <td>".$fila[1]."</td>
    <td><b>".$fila[6]."</b></td>
    <td>".$fila[2]."</td>
    <td>".$fila[3]."</td>
    <td style='text-align: right;'>".$fila[4]."</td>
    <td style='text-align: right;color:green;'>".$saldo."</td>
    <td style='text-align: right;'>[STOCK:".$fila[5]."]</td>
    <td style='text-align: right;'>[LOG:".$fila[7]."]</td>
    ");
    if($fila[8]!='')
    print("
    <td style='text-align: right;'>[PEDIDO ID:".$fila[8]."]</td>
    <td style='text-align: right;'>[ESTADO:".$fila[9]."]</td>
    </tr>
    ");
    else print("<td>&nbsp;</td><td>&nbsp;</td></tr>");

  }

  print("</table>");
  
  print("<hr><table>");
  
  for($i=0;$i<count($lotes);$i++) {
  
    print("<tr>
    <td>".$lotes[$i][0]."</td>
    <td>".$lotes[$i][1]."</td>
    <td>".$lotes[$i][2]."</td>
    <td><b>".$lotes[$i][3]."</b></td>
    </tr>");
  
  }
  
  print("</table>");

?>
