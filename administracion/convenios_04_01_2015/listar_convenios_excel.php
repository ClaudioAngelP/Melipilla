<?php

  require_once('../../conectar_db.php');
  
  header("Content-type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=\"Convenios_".date('d-m-Y his')."\".xls\";");

  $sel_id_licitacion=pg_escape_string(trim(utf8_decode($_GET['sel_id_licitacion'])));
  $prov_id=$_REQUEST['prov_id']*1;
  $art_id=$_REQUEST['art_id2']*1;
  $estado=$_REQUEST['filtro_estado']*1;
  
  if($sel_id_licitacion!='') {
  	
	  $lic_w="trim(upper(convenio_licitacion)) ILIKE '%$sel_id_licitacion%'";
  } else {
	  $lic_w='true';
  }
  
  if($prov_id!=0) {
	  $prov_w="prov_id=$prov_id";
  } else {
	  $prov_w='true';
  }

  if($art_id!=0) {
	  $art_w="JOIN convenio_detalle AS c2 ON c2.convenio_id=c1.convenio_id AND art_id=$art_id";
  } else {
	  $art_w='';
  }

	$estado=$_REQUEST['filtro_estado']*1;

  if($estado==0) {
	$estado_w="true";
  } elseif($estado==1) {
	$estado_w="dias_vigencia>0";
	} elseif($estado==2) {
	$estado_w="dias_vigencia<=0";
	}
	
	
	
  $lista_convenios = pg_query($conn,
  "
   SELECT *, (SELECT COUNT(*) FROM convenio_detalle WHERE convenio_detalle.convenio_id=fooo.convenio_id) AS arts, (SELECT COUNT(*) FROM orden_compra WHERE orden_licitacion=convenio_licitacion AND fooo.prov_id=orden_prov_id) AS ocs FROM (SELECT DISTINCT c1.*, (convenio_fecha_final-CURRENT_DATE) AS dias_vigencia,prov_glosa FROM convenio AS c1
  JOIN proveedor USING (prov_id)
  $art_w
  WHERE $lic_w AND $prov_w
  ORDER BY c1.convenio_nombre) AS fooo WHERE $estado_w
  ORDER BY convenio_nombre
  "
  ) or die(pg_last_error());

 /**echo  "
  SELECT *, (SELECT COUNT(*) FROM convenio_detalle WHERE convenio_detalle.convenio_id=fooo.convenio_id) AS arts, (SELECT COUNT(*) FROM orden_compra WHERE orden_licitacion=convenio_licitacion AND fooo.prov_id=orden_prov_id) AS ocs FROM (SELECT DISTINCT c1.*, (convenio_fecha_final-CURRENT_DATE) AS dias_vigencia,prov_glosa FROM convenio AS c1
  JOIN proveedor USING (prov_id)
  $art_w
  WHERE $lic_w AND $prov_w
  ORDER BY c1.convenio_nombre) AS fooo WHERE $estado_w
  ";*/

  
  print('<table style="width:100%; border:1px solid black;">
  <tr style="border:1px solid black;">
  <td style="width:10%;"><b>ID Licitaci&oacute;n</b></td>
  <td style="width:50%;"><b>Nombre del Convenio</b></td>
  <td><b>Proveedor</b></td>
  <td>Art&iacute;iculos</td>
  <td>Nro. OC</td>
  </tr>');
  
  for($i=0;$i<pg_num_rows($lista_convenios);$i++) {
    
    $convenio = pg_fetch_assoc($lista_convenios);
    
    if($convenio['dias_vigencia']*1>=180) {
		$color='000000'; $deco='';
    } elseif($convenio['dias_vigencia']*1<180 AND $convenio['dias_vigencia']*1>90) {
		$color='999900'; $deco='';
    } elseif($convenio['dias_vigencia']*1<=90 AND $convenio['dias_vigencia']*1>5) {
		$color='FF0000'; $deco='';
	} else {
		$color='FF0000'; $deco='line-through';
	}	
    
    print('<tr  style="height:30px;border:1px solid black;">
    <td style="font-weight:bold;text-align:right;">
    '.htmlentities($convenio['convenio_licitacion']).'
    </td><td style="color:#'.$color.';text-decoration:'.$deco.'">
    '.htmlentities($convenio['convenio_nombre']).'
    </td><td>
    '.htmlentities($convenio['prov_glosa']).'
    </td><td style="text-align:right;">'.$convenio['arts'].'</td><td style="text-align:right;font-weight:bold;">'.$convenio['ocs'].'</td>');

    print('
    ');
    
  }
  
  print('</table>');
  
  
  
  ?>
