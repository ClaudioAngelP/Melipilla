<?php

  require_once('../../conectar_db.php');
  
  $estado = $_GET['estado'];
  $rutProveedor = $_GET['rutproveedor'];
  //$portal = $_GET['portal'];
  $portal=cargar_registros_obj("SELECT DISTINCT orden_estado_portal FROM orden_compra WHERE orden_estado_portal!='' AND orden_estado_portal IS NOT NULL ORDER BY orden_estado_portal");
  
  if($estado!=-1) {
    $estado_q="WHERE orden_estado=".$estado*1;
  } 
  
  $w_portal="";
  
	if(isset($_GET['filtrar_ordenes']) AND trim($_GET['filtrar_ordenes'])!="") {
		$orden_nombre = "orden_nombre ILIKE '".$_GET['filtrar_ordenes']."'";
	} else {
		$orden_nombre = "true";
	}
	
	if(isset($_GET['orden_fecha1']) AND trim($_GET['orden_fecha1'])!="") {
		$orden_fecha1 = "orden_fecha >= '".$_GET['orden_fecha1']."'";
	} else {
		$orden_fecha1 = "true";
	}
	
	if(isset($_GET['orden_fecha2']) AND trim($_GET['orden_fecha2'])!="") {
		$orden_fecha2 = "orden_fecha <= '".$_GET['orden_fecha2']."'";
	} else {
		$orden_fecha2 = "true";
	}
	
	if(isset($_GET['filtrar_comprador']) AND $_GET['filtrar_comprador']!="-1") {
		if ($_GET['filtrar_comprador']!="-2") {
			$orden_comprador = "orden_mail ='".$_GET['filtrar_comprador']."'";
		} else {
			$orden_comprador = "orden_mail NOT ILIKE '%redsalud.gov.cl%'";
			}
	} else {
		$orden_comprador = "true";
	}
	
	if(isset($_GET['filtrar_item']) AND trim($_GET['filtrar_item'])!="-1") {
		$orden_costo = "orden_centro_costo ILIKE '%".$_GET['filtrar_item']."%'";
	} else {
		$orden_costo = "true";
	}
	
	  for($i=0;$i<sizeof($portal);$i++){
		 
		 if(isset($_GET['chk_'.$i])){
			$w_portal.="'".$portal[$i]['orden_estado_portal']."',";
		 }
			
		  
		}
	if($w_portal=="") $w_portal="true";
	else $w_portal=" orden_estado_portal IN ( ".trim($w_portal,",")." )";
  /*if($portal!='') {
	  if($estado!=-1)
		$estado_q.=" AND orden_estado_portal='$portal'";
	  else
		$estado_q="WHERE orden_estado_portal='$portal'";
  } */
  
  if(isset($_GET['ordenes_propias'])) {
    if($estado!=-1 OR $portal!='')    
      $estado_q.=" AND orden_func_id=".($_SESSION['sgh_usuario_id']*1); 
    else
      $estado_q="WHERE orden_func_id=".($_SESSION['sgh_usuario_id']*1);
  } 
  if($_GET['rutproveedor']){
  	  $estado_q.=" AND prov_rut='".$rutProveedor."'";
  }
  
   // maximo por pagina
	$limit = 100;

	// pagina pedida
	$pag = (int) $_GET["pag"];
	if ($pag < 1)
	{
	   $pag = 1;
	}
	  
  if(isset($_GET['xls'])) {
	    header("Content-type: application/vnd.ms-excel");
      	header("Content-Disposition: filename=\"OrdenesDeCompra.XLS\";");
    	$strip_html=true;
    	$limite = "";
  	} else {
		$offset = ($pag-1) * $limit;
		$limite = "LIMIT $limit OFFSET $offset";
	}
	
	// Total de registros sin limit
	$rs_total=pg_query("
	  SELECT 
	  orden_id
	  FROM orden_compra
	  JOIN proveedor ON prov_id=orden_prov_id
	  $estado_q AND $w_portal AND $orden_nombre AND $orden_fecha1 AND $orden_fecha2 AND $orden_comprador AND $orden_costo
	  ORDER BY orden_fecha DESC");
	 $total=pg_num_rows($rs_total)*1;
	 
  //~ $ordenes = cargar_registros_obj(
  //~ "
  //~ SELECT 
  //~ orden_id,
  //~ COALESCE(orden_numero, orden_id::text) AS orden_numero,
  //~ date_trunc('second', orden_fecha) AS orden_fecha,
  //~ prov_rut,
  //~ prov_glosa,
  //~ array(
    //~ SELECT pedido_nro FROM orden_pedido 
    //~ JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id
    //~ WHERE orden_pedido.orden_id=orden_compra.orden_id
  //~ ) AS pedido_nro,
  //~ orden_estado,orden_estado_portal,orden_nombre, (coalesce((select sum(ordetalle_subtotal) from orden_detalle where ordetalle_orden_id=orden_id),0) +
  //~ coalesce((select sum(orserv_subtotal) from orden_servicios where orserv_orden_id=orden_id),0))*1.19 AS total_oc, orden_centro_costo
  //~ FROM orden_compra
  //~ JOIN proveedor ON prov_id=orden_prov_id
  //~ $estado_q AND $w_portal AND $orden_nombre AND $orden_fecha1 AND $orden_fecha2 AND $orden_comprador AND $orden_costo
  //~ ORDER BY orden_fecha ASC
  //~ $limite",
  //~ false
  //~ );
  $ordenes = cargar_registros_obj(
  "
  SELECT 
  orden_id,
  COALESCE(orden_numero, orden_id::text) AS orden_numero,
  date_trunc('second', orden_fecha) AS orden_fecha,
  prov_rut,
  prov_glosa,
  array(
    SELECT pedido_nro FROM orden_pedido 
    JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id
    WHERE orden_pedido.orden_id=orden_compra.orden_id
  ) AS pedido_nro,
  orden_estado,orden_estado_portal,orden_nombre, orden_monto AS total_oc, orden_centro_costo
  FROM orden_compra
  JOIN proveedor ON prov_id=orden_prov_id
  $estado_q AND $w_portal AND $orden_nombre AND $orden_fecha1 AND $orden_fecha2 AND $orden_comprador AND $orden_costo
  ORDER BY orden_fecha DESC
  $limite",
  false
  );
?>

<table style="width:100%">
<tr class="tabla_header">
<td>
ID Orden
</td>
<td style='width:100px;'>
C&oacute;digo Orden
</td>
<td>Nombre</td>
<td>
Fecha Ingreso
</td>
<td>
RUT Proveedor
</td>
<td>
Nombre Proveedor
</td>
<td>
Estado G.I.S.
</td>
<td>
Estado Portal
</td>
<td>
Total con IVA
</td>
<td>
&Iacute;tem C&oacute;digo
</td>
</tr>

<?php 

  if($ordenes)
  for($i=0;$i<count($ordenes);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    $orden=$ordenes[$i];
    
    switch($orden['orden_estado']) {
      case 0: $estado='Espera Recep.'; break;
      case 1: $estado='Recep. Parcial'; break;
      case 2: $estado='Recep. Completa'; break;
      case 3: $estado='Recep. Anulada'; break;
      
    }
    
    $pnro=pg_array_parse($orden['pedido_nro']);
    $pedidoshtml='';
    
    for($j=0;$j<count($pnro);$j++) {
      $pedidoshtml.=$pnro[$j];
      if($j<count($pnro)-1) $pedidoshtml.='<br>';
    }
    
    if(empty($orden['total_oc']) || $orden['total_oc']=='' || $orden['total_oc']==null){
		
		pg_query("UPDATE orden_compra 
				  SET orden_monto=
				  (coalesce((select sum(ordetalle_subtotal) from orden_detalle where ordetalle_orden_id=orden_id),0) +
				   coalesce((select sum(orserv_subtotal) from orden_servicios where orserv_orden_id=orden_id),0))
				   WHERE orden_id=".$orden['orden_id']);
		$orden_monto=cargar_registro("SELECT orden_monto as total_oc
							  --(coalesce((select sum(ordetalle_subtotal) from orden_detalle where ordetalle_orden_id=orden_id),0) +
							  --coalesce((select sum(orserv_subtotal) from orden_servicios where orserv_orden_id=orden_id),0)) AS total_oc
							  FROM orden_compra
							  WHERE orden_id=".$orden['orden_id']);		
		$total_oc = $orden_monto['total_oc']*1.19;
	}else{
		$total_oc = $orden['total_oc']*1.19;
	}
    
    print('
    <tr class="'.$clase.'" style="cursor:pointer;"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\'"
    onClick="abrir_orden(\''.$orden['orden_id'].'\');">
    <td style="text-align:center;">'.$orden['orden_id'].'</b></td>
    <td style="text-align-center;"><b>'.$orden['orden_numero'].'</td>
<td style="text-align:center;"><i>'.htmlentities($orden['orden_nombre']).'</i></td>
    <td style="text-align:center;">'.substr($orden['orden_fecha'],0,10).'</td>
    <td style="text-align:center;">'.$orden['prov_rut'].'</td>
    <td>'.htmlentities($orden['prov_glosa']).'</td>
    <td style="text-align:center;">'.$estado.'</td>
    <td style="text-align:center;">'.htmlentities($orden['orden_estado_portal']).'</td>
    <td style="text-align:right;">$'.number_format($total_oc ,0, ',', '.').'</td>
    <td style="text-align:center;">'.htmlentities($orden['orden_centro_costo']).'</td>
    </tr>
    ');
    
  }

?>
 <?php if(!isset($_GET['xls'])) { ?>
<tr>
		<td colspan="10" align="center">
			P&aacute;gina: <select onchange='listar_ordenes(this.value);'>
				  <?php
					 $totalPag = ceil($total/$limit);
					 $links = array();
					 for( $i=1; $i<=$totalPag ; $i++)
					 {
						 if($i==$pag){
							echo  "<option value='$i' SELECTED>$i</option>"; 
						}else{
							echo  "<option value='$i'>$i</option>";
						}
					 }
					 //echo implode(" - ", $links);
				  ?>
			</select>
         </td>
      </tr>
  <?php } ?>
</table>
