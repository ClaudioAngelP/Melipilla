<?php

  require_once('../../conectar_db.php');
  
  $bodega = ($_GET['bodega_origen']*1);
  $tipo_mov = ($_GET['tipo_mov']*1);
  $documento = ($_GET['documento']*1);
  $prov_id = ($_GET['prov_id']*1);
  $nro = ($_GET['nro'])*1;
  $nro_oc = pg_escape_string($_GET['nro_orden']);
  $nro_corr = ($_GET['nro_corr'])*1;
  $fecha1=pg_escape_string($_GET['fecha1']);
  $fecha2=pg_escape_string($_GET['fecha2']);
  
  if($tipo_mov==1) $tipo_mov='1, 50';

  if($tipo_mov==30) $tipo_mov='30, 31, 32';
  
  $where_in=false;
  
  if($bodega!='-1' AND $bodega!='-2') {
    $bodega='AND stock_bod_id='.$bodega;
  } elseif($bodega=='-2') {
	$bodega='AND stock_bod_id IN (1,10,13,14)'; // ESTO DEBIERA REFERENCIAR UNA TABLA DE CONFIGURACIÓN dentro de IN (...)...
  } else {
    $bodega='';
  }
  
  if($nro_oc!='') {
	  $oc_w="(doc_orden_desc ILIKE '%$nro_oc%' OR orden_numero ILIKE '%$nro_oc%')";
  } else {
	  $oc_w='true';
  }
  
    $query="
    SELECT DISTINCT
    doc_id,
    doc_tipo,
    doc_num,
    prov_rut,
    prov_glosa,
    date_trunc('second', log_fecha),
    COALESCE(doc_id, log_id),
    log_tipo, log_folio, COALESCE(orden_numero, doc_orden_desc),
    instsol_desc,tipo_mov,log_comentario,
    doc_interm
    FROM logs
    LEFT JOIN documento ON log_doc_id=doc_id
    LEFT JOIN orden_compra ON doc_orden_id=orden_id
    LEFT JOIN stock ON stock_log_id=log_id
    LEFT JOIN proveedor ON doc_prov_id=prov_id
    LEFT JOIN cargo_instsol USING (log_id)
    LEFT JOIN institucion_solicita USING (instsol_id)
    WHERE
    log_tipo IN ($tipo_mov)
    $bodega AND $oc_w
    AND
    (date_trunc('day', log_fecha)>='$fecha1' AND date_trunc('day', log_fecha)<='$fecha2')
    ";
  
    if($tipo_mov==1) {
        if($documento!=-1) {
            $query.=" AND doc_tipo=".($documento*1)."";
        }
  
        if($prov_id!=0) {
            $query.=" AND doc_prov_id=".($prov_id*1)."";
        }
  
        if(($nro*1)!=0) {
            $query.=" AND doc_num=".($nro*1)."";
        }
  
        if(($nro_corr*1)!=0) {  
            $query.="AND doc_id=".($nro_corr*1)."";
        }
    }
    $query.="order by doc_id desc";
 
    $recepciones = pg_query($conn, $query);

    $resultadohtml = '';

    for($i=0;$i<pg_num_rows($recepciones);$i++) {
        $recepcion_a = pg_fetch_row($recepciones);
        ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
        if($tipo_mov==1) {
            switch($recepcion_a[1]) {
                case 0: $recepcion_a[1]='Guía de Despacho'; break;
                case 1: $recepcion_a[1]='Factura'; break;
                case 2: $recepcion_a[1]='Boleta'; break;
                case 3: $recepcion_a[1]='Pedido'; break;
                case 4: $recepcion_a[1]='Resoluci&oacute;n (Donaciones)'; break;
            }
        } else {
            switch($recepcion_a[7]) {
                case 1: $recepcion_a[1]='Ingreso desde Proveedor.'; break;
                case 2: $recepcion_a[1]='Traslado de Productos.'; break;
                case 4: $recepcion_a[1]='Ingreso por Excedente.'; break;
                case 5: $recepcion_a[1]='Ingreso por Donaci&oacute;n.'; break;
                case 6: $recepcion_a[1]='Pr&eacute;stamo/Devoluci&oacute;n de Art&iacute;culos.'; break;
                case 7: $recepcion_a[1]='Baja por Vencimiento.'; break;
                case 8: $recepcion_a[1]='Dado de Baja.'; break;
                case 9: $recepcion_a[1]='Gasto por Receta.'; break;
                case 10: $recepcion_a[1]='Utilizado en Farmacia Magistral.'; break;
                case 15: $recepcion_a[1]='Despacho a Servicio.'; break;
                case 16: $recepcion_a[1]='Devoluci&oacute;n desde Servicio.'; break;
                case 20: $recepcion_a[1]='Inicio de Control por Sistema.'; break;
                case 30: $recepcion_a[1]='Ajuste de Saldos.'; break;
                case 31: $recepcion_a[1]='Ajuste de Saldos por Merma.';
                case 32: $recepcion_a[1]='Ajuste de Saldos por Vencimiento.';
            }
        }
        $resultadohtml .= '
        <tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';" ';
        if($tipo_mov==1) 
            $resultadohtml .='onClick="abrir_recepcion('.$recepcion_a[0].');"';
        else
            $resultadohtml .='onClick="abrir_movimiento('.$recepcion_a[6].');"';
    
        $resultadohtml .='>';
    
        if($tipo_mov==1) {
            $resultadohtml .='<td style="text-align: center;"><b>'.$recepcion_a[8].'</b></td>';
            $resultadohtml .='<td style="text-align: center;"><b>'.$recepcion_a[6].'</b></td>';
        } else {
            $resultadohtml .='<td style="text-align: center;"><b>'.$recepcion_a[6].'</b></td>';
        }
    
        $resultadohtml .='<td style="text-align: center;">'.$recepcion_a[5].'</td>';
        
        if($tipo_mov==1){
            $resultadohtml.='
            <td style="text-align: center;">'.htmlentities($recepcion_a[9]).'</td>
            <td style="text-align: center;">'.htmlentities($recepcion_a[13]).'</td>
            <td>'.htmlentities($recepcion_a[1]).'</td>
            <td style="text-align: center;">'.htmlentities($recepcion_a[2]).'</td>
            <td style="text-align: center;">'.htmlentities($recepcion_a[3]).'</td>
            <td>'.htmlentities($recepcion_a[4]).'</td>';
        }
        else if ($tipo_mov==6){
            if($recepcion_a[11]==0)
                $recepcion_a[11]='Salida de Pr&eacute;stamo';
            else if ($recepcion_a[11]==1)
                $recepcion_a[11]='Salida de Devolución';
            else if ($recepcion_a[11]==2)
                $recepcion_a[11]='Entrada de Préstamo';
            else
                $recepcion_a[11]='Entrada de Devolcuión';
        
            $resultadohtml.='<td>'.htmlentities($recepcion_a[10]).'</td>';
            $resultadohtml.='<td>'.($recepcion_a[11]).'</td>';
            $resultadohtml.='<td>'.htmlentities($recepcion_a[12]).'</td>';
        }else
            $resultadohtml.='<td>'.htmlentities($recepcion_a[1]).'</td>';
        $resultadohtml.='</tr>';
    }
?>
<table width='100%'>
    <tr class='tabla_header' style='font-weight: bold;'>
        <?php if($tipo_mov==1) { ?>
        <td>Folio Recep.</td>
        <td>Folio Doc.</td>
        <?php }  else { ?>
        <td>Id Mov.</td>
        <?php } ?>
        <td>Fecha/Hora</td>
        <?php if($tipo_mov==1) { ?>
        <td>Orden de Compra</td>
        <td>Intermediaci&oacute;n</td>
        <td>Documento</td>
        <td>N&uacute;mero</td>
        <td>RUT</td>
        <td>Nombre</td>
        <?php }  else if ($tipo_mov==6) { ?>
        <td>Instituci&oacute;n</td>
        <td>Tipo de Movimiento</td>
        <td>Comentario</td>
        <?php } else { ?>
        <td>Tipo de Movimiento</td>
        <?php } ?>
    </tr>
    <?php echo $resultadohtml; ?>
</table>
