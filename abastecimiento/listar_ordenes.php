<?php

  require_once('../conectar_db.php');

  $prov_id=$_GET['id_proveedor']*1;
  $art_id=$_GET['id_articulo']*1;
  
  if($prov_id!=-1 OR $art_id!=-1)
    $where_q=' WHERE ';
  else
    $where_q='';
    
  if($prov_id!=-1)
    $where_q.='prov_id='.$prov_id;
  
  if($prov_id!=-1 AND $art_id!=-1)
    $where_q.=' AND ';
    
  if($art_id!=-1)
    $where_q.='ordetalle_art_id='.$art_id;
  
  $query="
  SELECT DISTINCT
  orden_id,
  orden_numero,
  date_trunc('second', orden_fecha) AS orden_fecha,
  prov_rut,
  prov_glosa
  FROM orden_compra
  JOIN proveedor ON prov_id=orden_prov_id
  JOIN orden_detalle ON orden_id=ordetalle_orden_id
  $where_q
  ORDER BY orden_fecha 
  ";
  
  $ordenes=cargar_registros_obj($query);

  if($ordenes) {
  
?>

<table style='width:100%;font-size:12px;'>
<tr class="tabla_header">
<td>Orden Nro.</td>
<td>Fecha Emisi&oacute;n</td>
<td>RUT Proveedor</td>
<td>Nombre Proveedor</td>
<td colspan=2>Acciones</td>
</tr>

<?php  
  
  for($i=0;$i<count($ordenes);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    print('
    <tr class="'.$clase.'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';">
    <td style="text-align:center;font-weight:bold;"
    >'.$ordenes[$i]['orden_numero'].'</td>
    <td style="text-align:center;">'.$ordenes[$i]['orden_fecha'].'</td>
    <td style="text-align:right;">'.$ordenes[$i]['prov_rut'].'</td>
    <td>'.htmlentities($ordenes[$i]['prov_glosa']).'</td>
    <td><center><img src="../iconos/magnifier.png" style="cursor:pointer;"
    onClick="abrir_orden_compra(\''.$ordenes[$i]['orden_id'].'\');"></center></td>
    <td><center>
    <img src="../iconos/page_white_swoosh.png" style="cursor:pointer;"
    onClick="
    usar_orden(
    \''.$ordenes[$i]['orden_numero'].'\',
    \''.$ordenes[$i]['prov_rut'].'\'
    );"></center></td>
    </tr>
    ');
  
  }

} else {

  print('No se encontraron &oacute;rdenes de compra pendientes.');

}

?>
