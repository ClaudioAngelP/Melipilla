<?php
  require_once('../../conectar_db.php');
  $ca_id = $_POST['casos_auge']*1;
  if($ca_id!=-1) {

  	list($ca)=cargar_registros_obj("   	SELECT * FROM casos_auge WHERE ca_id=$ca_id 	");
  	if($ca)
  		$pac_id=$ca['ca_pac_id'];
  	else {
	  	$pac_id=$_POST['pac_id'];
	  	$ca['id_sigges']=0;	
  	}
  
		$caso_w="id_caso=".$ca['id_sigges'];  
  
  } else {
  
	  	$pac_id=$_POST['pac_id'];
		$caso_w='true';  
  	
  }
  $ic=cargar_registros_obj("      SELECT *, 
      inter_fecha_ingreso::date AS inter_fecha,
      i1.inst_nombre AS inst_desc1,
      i2.inst_nombre AS inst_desc2        FROM interconsulta 
		LEFT JOIN instituciones AS i1 ON inter_inst_id1=i1.inst_id
		LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id		       
      WHERE inter_pac_id=$pac_id AND $caso_w", true);

      $ipd=cargar_registros_obj("
	    SELECT *, ipd_fecha_ingreso::date AS ipd_fecha,
	    i1.inst_nombre AS inst_desc    	 FROM formulario_ipd 		 LEFT JOIN instituciones AS i1 ON ipd_inst_id=i1.inst_id
    	 WHERE ipd_pac_id=$pac_id AND $caso_w
    	 ORDER BY ipd_fecha_ingreso DESC
  ", true);
  
  $oa=cargar_registros_obj("
    SELECT *, oa_fecha::date AS oa_fecha,
    i1.inst_nombre AS inst_desc1,
    i2.inst_nombre AS inst_desc2   
    FROM orden_atencion 	LEFT JOIN instituciones AS i1 ON oa_inst_id=i1.inst_id
	LEFT JOIN instituciones AS i2 ON oa_inst_id2=i2.inst_id		       

    WHERE oa_pac_id=$pac_id AND $caso_w
  ", true);
  
  $cc=false;

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Fecha</td>
<td>Nro. Folio</td>
<td style='width:50%;'>Documento</td>
<td>Or&iacute;gen</td>
<td>Destino</td>

<td>Ver Documento</td>
</tr>

<?php 

  if($ipd) {
  
    $clase=($o%2==0)?'tabla_fila':'tabla_fila2';
  	 for($i=0;$i<sizeof($ipd);$i++) {

    if($ipd[$i]['ipd_folio']==-1) $ipd[$i]['ipd_folio']='<i>(s/n)</i>';
  
    if($ipd[$i]['ipd_confirma']=='t') {
      $tipo='CONFIRMACI&Oacute;N'; $color='green';
    } else {
      $tipo='DESCARTE'; $color='red';
    }
  
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\"'>
    <td style='text-align:center;'>".$ipd[$i]['ipd_fecha']."</td>
    <td style='text-align:center;font-size:18px;'>".$ipd[$i]['ipd_folio']."</td>
    <td style='font-weight:bold;'>Informe de Proceso de Diagn&oacute;stico</td>
    <td style='text-align:center;'>".$ipd[$i]['inst_desc']."</td>	 

    <td style='text-align:center;font-weight:bold;'><span style='color:$color'>$tipo</span></td>

    <td>
    <center>
      <img src='../../iconos/magnifier.png' style='cursor:pointer;' onClick='abrir_ipd(".$ipd[$i]['ipd_id'].");'>
    </center>
    </td>
    </tr>
    ");
    
    $o++;
    
    }
    
  }


  if($ic) {

	for($i=0;$i<sizeof($ic);$i++) {
		
    if($ic[$i]['inter_folio']==-1) $ic[$i]['inter_folio']='<i>(s/n)</i>';
    print("
    <tr class='tabla_fila'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"tabla_fila\"'>
    <td style='text-align:center;'>".$ic[$i]['inter_fecha']."</td>
    <td style='text-align:center;font-size:18px;'>".$ic[$i]['inter_folio']."</td>
    <td style='font-weight:bold;'>Solicitud de Interconsulta</td>
    <td style='text-align:center;'>".$ic[$i]['inst_desc1']."</td>
        <td style='text-align:center;'>".$ic[$i]['inst_desc2']."</td>
    <td>
    <center>
      <img src='../../iconos/magnifier.png' style='cursor:pointer;' onClick='abrir_ic(".$ic[$i]['inter_id'].");'>
    </center>
    </td>
    </tr>
    ");
	}    
  }

  if($oa) {

	for($i=0;$i<sizeof($oa);$i++) {
		
    if($oa[$i]['oa_folio']==-1) $oa[$i]['oa_folio']='<i>(s/n)</i>';
    print("
    <tr class='tabla_fila'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"tabla_fila\"'>
    <td style='text-align:center;'>".$oa[$i]['oa_fecha']."</td>
    <td style='text-align:center;font-size:18px;'>".$oa[$i]['oa_folio']."</td>
    <td style='font-weight:bold;'>Orden de Atenci&oacute;n</td>
    <td style='text-align:center;'>".$oa[$i]['inst_desc1']."</td>
    
    ");
    
    if($oa[$i]['inst_desc2']) {    	print("<td style='text-align:center;'>".$oa[$i]['inst_desc2']."</td>");
    } else {
    	print("<td style='text-align:center;'><u>Extrasistema:</u><br /><i>".$oa[$i]['oa_compra_extra']."</i></td>");    
    }
	print("

    <td>
    <center>
      <img src='../../iconos/magnifier.png' style='cursor:pointer;' onClick='abrir_oa(".$oa[$i]['oa_id'].");'>
    </center>
    </td>
    </tr>
    ");
	}    
  }

  if($cc) {
  
    $clase=($o%2==0)?'tabla_fila':'tabla_fila2';
  
    if($cc[0]['ccaso_folio']==-1) $cc[0]['ccaso_folio']='<i>(s/n)</i>';
  
    $color='black;';
  
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\"'>
    <td style='text-align:center;'>".$cc[0]['ccaso_fecha']."</td>
    <td style='font-weight:bold;'>Cierre de Caso AUGE</td>
    <td style='text-align:center;'><span style='color:$color'>".strtoupper($cc[0]['causal_nombre']." ".$cc[0]['subcausal_nombre'])."</span></td>
    <td style='text-align:center;'>".$cc[0]['ccaso_folio']."</td>
    <td>
    <center>
      <img src='../../iconos/magnifier.png' style='cursor:pointer;' onClick='abrir_cc(".$cc[0]['ccaso_id'].");'>
    </center>
    </td>
    </tr>
    ");
    
    $o++;
    
  }
?>

</table>
