<?php

  require_once('conectar_db.php');

  $doc_id=($_GET['doc_id']*1);
  $log_folio=($_GET['log_folio']);
  if(isset($_GET['pac_id'])) {
  
    $pac_id=($_GET['pac_id']*1);
    
    $p=cargar_registro("
		
		SELECT 
		
  		pac_rut, 
  		pac_appat, 
  		pac_apmat, 
  		pac_nombres,
  		pac_fc_nac,
  		pac_direccion,
  		ciud_desc,
  		prov_desc,
  		reg_desc,
  		sex_desc,
  		prevision.prev_id, prev_desc,
  		sang_desc,
  		getn_desc
		
		FROM pacientes 
		LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
		LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id
		LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id
		LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id
		LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
		LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id
		LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id
		
		WHERE pac_id=$pac_id
		
		");

  
?>

<html>
<title>Visualizar Ficha Electr&oacute;nica</title>

<?php cabecera_popup('.'); ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_historial');
  tab_down('tab_datos');
  tab_down('tab_referencia');

}

ver_historial=function() {

  tab_down('tab_general');
  tab_up('tab_historial');
  tab_down('tab_datos');
  tab_down('tab_referencia');

}

ver_datos=function() {

  tab_down('tab_general');
  tab_down('tab_historial');
  tab_up('tab_datos');
  tab_down('tab_referencia');

}

ver_referencia=function() {

  tab_down('tab_general');
  tab_down('tab_historial');
  tab_down('tab_datos');
  tab_up('tab_referencia');

}

</script>

<body class="fuente_por_defecto popup_background">

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
      onClick='ver_historial();'>
      <img src='iconos/time.png'>
      Historial Ficha</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_datos' style='cursor: pointer;'
      onClick='ver_datos();'>
      <img src='iconos/pill.png'>
      Datos Cl&iacute;nicos</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_referencia' style='cursor: pointer;'
      onClick='ver_referencia();'>
      <img src='iconos/arrow_in.png'>
      Referencias</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:360px;'>



    <table>
		<tr><td style='text-align:right;'>RUT:</td><td><b><?php echo $p['pac_rut']; ?></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Paterno:</td>
		<td><b><i><?php echo $p['pac_appat']; ?></i></b></td></tr>
		<tr><td style='text-align:right;'>Apellido Materno:</td>
		<td><b><i><?php echo $p['pac_apmat']; ?></i></b></td></tr>
		<tr><td style='text-align:right;'>Nombre(s):</td>			
		<td><b><i><?php echo $p['pac_nombres']; ?></i></b></td></tr>
		<tr><td style='text-align:right;'>Fecha de Nacimiento:</td>
    <td><?php echo $p['pac_fc_nac']; ?></td></tr>
		<tr><td style='text-align:right;'>Edad:</td>
    <td id='paciente_edad'></td></tr>
		<tr><td style='text-align:right;'>Direcci&oacute;n:</td>				
    <td><?php echo $p['pac_direccion']; ?></td></tr>
		<tr><td style='text-align:right;'>Comuna:</td>				
    <td><b><?php echo $p['ciud_desc']; ?></b>, <?php echo $p['prov_desc']; ?>, <i><?php echo $p['reg_desc']; ?></i>.- </td></tr>
		<tr><td style='text-align:right;'>Sexo:</td>				
    <td><?php echo $p['sex_desc']; ?></td></tr>
		<tr><td style='text-align:right;'>Previsi&oacute;n:</td>	
    <td><?php echo '['.$p['prev_id'].'] '.$p['prev_desc']; ?></td></tr>
		<tr><td style='text-align:right;'>Grupo Sangu&iacute;neo:</td>			
    <td><b><?php echo $p['sang_desc']; ?></b></td></tr>
		<tr><td style='text-align:right;'>Grupo &Eacute;tnico:</td>
    <td><?php echo $p['getn_desc']; ?></td></tr>
		<tr><td style='text-align:right;'>Registro Digitalizado:</td>
    <td>
    <img src='iconos/page_white_acrobat.png'>
    <b>Descargar PDF</b>
    </td></tr>
		</table>
		
</div>

<div id='tab_historial_content' 
class='tabbed_content' style='overflow:auto;height:360px;display:none;'>

<table style='width:100%;font-size:12px;'>
<tr class='tabla_header'>
<td>Fecha</td>
<td>N&oacute;mina/Solicitud</td>
<td>Servicio Solicitante</td>
<td>Funcionario</td>
<td>Acci&oacute;n</td>
</tr>

<tr class='tabla_fila'>
<td style='text-align:center;'>03/05/2009 14:20:20</td>
<td style='text-align:center;'>3</td>
<td>Neurolog&iacute;a</td>
<td>Rodrigo Alexis Carvajal</td>
<td style='text-align:center;'>Retira</td>
</tr>

<tr class='tabla_fila2'>
<td style='text-align:center;'>03/05/2009 15:30:20</td>
<td style='text-align:center;'>3</td>
<td>Neurolog&iacute;a</td>
<td>Rodrigo Alexis Carvajal</td>
<td style='text-align:center;'>Devuelve</td>
</tr>

<tr class='tabla_fila'>
<td style='text-align:center;'>04/05/2009 9:32:10</td>
<td style='text-align:center;'>5</td>
<td>Dermatolog&iacute;a</td>
<td>Rodrigo Alexis Carvajal</td>
<td style='text-align:center;'>Retira</td>
</tr>

</table>

</div>

<div id='tab_datos_content' 
class='tabbed_content' style='overflow:auto;height:360px;display:none;'>

<table style='width:100%;font-size:12px;'>
<tr class='tabla_header'>
<td>Fecha</td>
<td>Anotaci&oacute;n</td>
<td>Detalle</td>
<td>Funcionario</td>
</tr>

<tr class='tabla_fila'>
<td style='text-align:center;'>04/04/2009 9:32:10</td>
<td>Prestaci&oacute;n</td>
<td>Consulta Especialidades M&eacute;dicas</td>
<td>Rodrigo Alexis Carvajal</td>
</tr>

<tr class='tabla_fila'>
<td style='text-align:center;'>14/04/2009 10:40:10</td>
<td>Prestaci&oacute;n</td>
<td>Consulta Especialidades M&eacute;dicas</td>
<td>Rodrigo Alexis Carvajal</td>
</tr>

<tr class='tabla_fila'>
<td style='text-align:center;'>04/04/2009 9:32:10</td>
<td>Medicamentos</td>
<td>2140050 ACENOCUMAROL 4 MG.CM<br>1 cada 12 horas por 10 d&iacute;as.</td>
<td>Rodrigo Alexis Carvajal</td>
</tr>

</table>

</div>

<div id='tab_referencia_content' 
class='tabbed_content' style='overflow:auto;height:360px;display:none;'>


</div>

</td>
</tr>
</table>


<?php
  
  
  }

  if(isset($_GET['solf_id'])) {

  $solf_id=($_GET['solf_id']*1);

  $solicitud=cargar_registro("
  SELECT * FROM solicitud_ficha
  LEFT JOIN doctores ON solf_doc_id=doc_id 
  LEFT JOIN centro_costo ON solf_centro_ruta=centro_ruta
  LEFT JOIN funcionario ON solf_func_id=func_id
  WHERE solf_id=$solf_id
  ");
  
  switch($solicitud['solf_estado']) {
      case 0: $estado='Pendiente'; break;
      case 1: $estado='Autorizada'; break;
      case 2: $estado='Rechazada'; break;
      
  }
  
  $detalle = cargar_registros_obj("
  SELECT * FROM solicitudficha_detalle 
  JOIN pacientes USING (pac_id)
  WHERE
  solf_id=$solf_id
  ORDER BY pac_appat, pac_apmat, pac_nombres
  ");
  
  $detalles_html="
  <table style='width:100%;font-size:11px;'>
    <tr class='tabla_header'>
    <td>RUT/ID</td>
    <td>Paterno</td>
    <td>Materno</td>
    <td>Nombres</td>
    </tr>
  ";
  
  for($i=0;$i<count($detalle);$i++) {
  
    if($i%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
  
    $detalles_html.='
    <tr class="'.$clase.'">
    <td style="text-align:right;">'.$detalle[$i]['pac_rut'].'</td>
    <td>'.htmlentities(strtoupper($detalle[$i]['pac_appat'])).'</td>
    <td>'.htmlentities(strtoupper($detalle[$i]['pac_apmat'])).'</td>
    <td>'.htmlentities(strtoupper($detalle[$i]['pac_nombres'])).'</td>
    </tr>
    ';
  
  }

  $detalles_html.='</table>';

?>

<html>
<title>Visualizar Solicitud de Fichas a Archivo</title>

<?php cabecera_popup('.'); ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_detalle');

}

ver_detalle=function() {

  tab_down('tab_general');
  tab_up('tab_detalle');

}

imprimir_solicitud=function() {

  _general = $('tab_general_content').innerHTML;
  _detalle = $('tab_detalle_content').innerHTML;
  
  _separador2 = '<hr><h3>Detalle de Fichas Solicitadas</h3></hr>';
  
  imprimirHTML(_general+_separador2+_detalle);

}

</script>

<body class="fuente_por_defecto popup_background">

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_detalle' style='cursor: pointer;'
      onClick='ver_detalle();'>
      <img src='iconos/page_white_find.png'>
      Fichas Solicitadas</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>


<table width=100% style="font-size: 12px;">
  
<tr>
<td style='text-align: right; width:150px;'>N&uacute;mero Interno:</td>
<td style='font-size: 20px;'><b><?php echo $solicitud['solf_id']; ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>Fecha:</td>
<td><?php echo $solicitud['solf_fecha']; ?></td>
</tr>
<tr>
<td style='text-align: right;'>Servicio Solicitante:</td>
<td><b><?php echo $solicitud['centro_nombre']; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>RUT M&eacute;dico:</td>
<td><b><?php echo $solicitud['doc_rut']; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Nombre M&eacute;dico:</td>
<td><b><?php echo htmlentities($solicitud['doc_paterno'].' 
      '.$solicitud['doc_materno'].'
      '.$solicitud['doc_nombres']); ?></b></td>
</tr>

<tr>
<td style='text-align: right;'>Funcionario:</td>
<td><b><?php echo $solicitud['func_nombre']; ?></b></td>
</tr>


<tr>
<td style='text-align: right;'>Estado:</td>
<td><b><?php echo htmlentities($estado); ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Comentarios:</td>
<td>

<?php 

  if($solicitud['solf_motivo']) 
    echo htmlentities($solicitud['solf_motivo']);
  else
    echo '<i>No hay comentarios.</i>'; 
  
?>

</td>
</tr>

</table>

</div>

<div id='tab_detalle_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $detalles_html; ?>

</div>


</td>
</tr>
</table>

<center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_solicitud();'>
		Imprimir Solicitud de Fichas...</a>
		</td></tr></table>
		</div>
</center>

</body>
</html>

<?php

  }
  
  if(isset($_GET['sol_id'])) {
  
  // Visualizar una Solicitud de Compra
  
  $sol_id=($_GET['sol_id']*1);
  
  $solicitud=cargar_registros_obj("
  SELECT *, 
  f1.func_nombre AS f1_func_nombre,
  f2.func_nombre AS f2_func_nombre,
  f3.func_nombre AS f3_func_nombre,
  COALESCE(bod_glosa, centro_nombre) AS sol_ubica
  FROM solicitud_compra 
  LEFT JOIN funcionario AS f1 ON f1.func_id=sol_func_id
  LEFT JOIN funcionario AS f2 ON f2.func_id=sol_func_id1
  LEFT JOIN funcionario AS f3 ON f3.func_id=sol_func_id2
  LEFT JOIN bodega ON sol_bod_id=bod_id
  LEFT JOIN centro_costo ON sol_centro_ruta=centro_ruta

  WHERE sol_id=$sol_id
  ");
  
  if(!$solicitud[0]['sol_bod_id']) {
    $_lugar=cargar_registro(
    "SELECT * FROM centro_costo 
     WHERE centro_ruta='".$solicitud[0]['sol_centro_ruta']."'");
    $lugar=$_lugar['centro_nombre'];
  } else {
    $_lugar=cargar_registro(
    "SELECT * FROM bodega 
     WHERE bod_id=".$solicitud[0]['sol_bod_id']);
    $lugar=$_lugar['bod_glosa'];
  }
  
  $detalle=cargar_registros_obj("
  SELECT *
  FROM solcompra_detalle 
  LEFT JOIN articulo ON sol_art_id=art_id
  WHERE sol_id=$sol_id
  ");
  
    switch($solicitud[0]['sol_tipo']) {
  
      case 0:
        $tipo='Compra de art&iacute;culos'; break;
      case 1:
        $tipo='Compra de art&iacute;culo nuevo'; break;
        
    }
    
    if($solicitud[0]['sol_urgente']=='t')
      $urgente=' <b>Urgente</b>'; else $urgente='Normal';
  
    switch($solicitud[0]['sol_estado']) {
      case 0:
        $estado='Esperando confirmaci&oacute;n por Subdirecci&oacute;n';
        break;
      case 1:
        $estado='Aceptado por Subdirecci&oacute;n del Servicio';
        break;
      case 2:
        $estado='Rechazado';
        break;
      case 3:
        $estado='Rechazado';
        break;
      case 4:
        $estado='Rechazado';
        break;
      case 5:
        $estado='Rechazado';
        break;
      case 6:
        $estado='Esperando Cotizaci&oacute;n en Abastecimiento';
        break;
      case 7:
        $estado='Aceptado Subdirecci&oacute;n Administrativa';
        break;
        
    }


    if($solicitud[0]['sol_tipo']==0) {
    $totales_html='
    <table width=100% style="font-size: 11px;">
    <tr class="tabla_header" style="font-weight: bold;">
    <td>Cod. Int.</td>
    <td style="width:40%;">Glosa</td>
    <td>Cantidad</td>
    <td>P. Unitario</td>
    <td>Subtotal</td>
    </tr>
    ';
  
    $sumatoria=0;
  
    for($i=0;$i<count($detalle);$i++) {
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    if($detalle[$i]['sol_subtotal']==0) {
      $subtotal=$detalle[$i]['sol_cant']*$detalle[$i]['art_val_ult'];
    } else {
      $subtotal=$detalle[$i]['sol_subtotal'];
    }
    
    $totales_html.="
    <tr class='".$clase."'>
    <td style='text-align:right;font-weight:bold;'
    >".$detalle[$i]['art_codigo']."</td>
    <td>".$detalle[$i]['art_glosa']."</td>
    <td style='text-align: right;'>
    ".number_formats($detalle[$i]['sol_cant'])."</td>
    <td style='text-align: right;'>
    $".number_formats($subtotal/$detalle[$i]['sol_cant']).".-</td>
    <td style='text-align: right;'>
    $".number_formats($subtotal).".-</td>
    </tr>
    ";
  
    $sumatoria+=$subtotal;
  
    }

    $neto=$sumatoria;
    $total=$sumatoria*$_global_iva; // Multiplica por el IVA Asociado.
    $iva=$total-$neto;
   
    $totales_html.='
    <tr class="tabla_header">
    <td colspan=3 rowspan=3>Total General</td><td>Neto:</td>
    <td style="text-align:right;">$'.number_formats($neto).'.-</td></tr>
    <tr class="tabla_header"><td>I.V.A.:</td>
    <td style="text-align:right;">$'.number_formats($iva).'.-</td></tr>
    <tr class="tabla_header"><td>Total:</td>
    <td style="text-align:right;">$'.number_formats($total).'.-</td></tr>
    </table>';  
    
    } else {
    
    $totales_html='No se ha realizado cotizaci&oacute;n.';
    
    }
    
    if($solicitud[0]['sol_archivos']) {
      
      $solicitud[0]['sol_archivos']=pg_array_parse($solicitud[0]['sol_archivos']);
      $archivos_html='<table style="width:100%;font-size:12px;"><tr class="tabla_header"><td>Nombre</td><td style="width:50px;">Tama&ntilde;o</td></tr>';
      
      for($i=0;$i<count($solicitud[0]['sol_archivos']);$i++) {
        $nom=substr($solicitud[0]['sol_archivos'][$i],1,strlen($solicitud[0]['sol_archivos'][$i])-2);
        $url='adjuntos/'.$nom;
        $tam=filesize($url);
        ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
        $archivos_html.='
        <tr class="'.$clase.'" style="cursor:pointer;"
        onMouseOver="this.className=\'mouse_over\';"
        onMouseOut="this.className=\''.$clase.'\'"
        onClick="abrir_doc(\''.$nom.'\');">
        <td>'.htmlentities($nom).'</td>
        <td style="text-align:right;">'.number_format($tam/1024,1,',','.').' Kb</td>
        </tr>
        ';
      }
        
      $archivos_html.='</table>';
      
    } else {
    
      $archivos_html='No existen archivos adjuntos a la solicitud.';
      
    }
    
    

    
  
?>

<html>
<title>Visualizar Solicitud de Compra</title>

<?php cabecera_popup('.'); ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_archivos');
  tab_down('tab_totales');
  
}

ver_archivos=function() {

  tab_down('tab_general');
  tab_up('tab_archivos');
  tab_down('tab_totales');

}

ver_total=function() {

  tab_down('tab_general');
  tab_down('tab_archivos');
  tab_up('tab_totales');

}

abrir_doc=function(adj) {

  win = window.opener.open('adjuntos/'+encodeURIComponent(adj), '');
                    
  win.focus();


}

<?php 
      
    if(_func_permitido(61,$solicitud[0]['sol_bod_id']) OR
       _func_permitido_cc(61,$solicitud[0]['sol_centro_ruta'])) { 

?>

autorizar_sol_1 = function(checkbox) {
  
  if(checkbox.checked)  valor='true';
  else                  valor='false';
  
  checkbox.disabled=true;
  
  var myAjax = new Ajax.Request('sql.php',
  {
    method:'get',
    parameters: 'accion=autorizar_sol&sol_id=<?php echo $solicitud[0]['sol_id']; ?>&valor='+valor,
    onComplete: function(resp) {
      try {
        resultado = resp.responseText.evalJSON(true);
        
        if(resultado[1]=='true') {
          $('autorizado_por').style.display='';
          $('autorizado_fecha').style.display='';
          $('autorizado_por_campo').innerHTML=resultado[2];
          $('autorizado_fecha_campo').innerHTML=resultado[3];
          
        } else if(resultado[1]=='false') {
          $('autorizado_por').style.display='none';
          $('autorizado_fecha').style.display='none';
        }
        
      } catch(err) {
        alert(resp.responseText.unescapeHTML());
        checkbox.checked=!checkbox.checked;
      }

      checkbox.disabled=false;

    }
  }
  );
  
}

autorizar_sol_2 = function(checkbox) {
  
  if(checkbox.checked)  valor='true';
  else                  valor='false';
  
  checkbox.disabled=true;
  
  var myAjax = new Ajax.Request('sql.php',
  {
    method:'get',
    parameters: 'accion=autorizar_solicitud&sol_id=<?php echo $solicitud[0]['sol_id']; ?>&valor='+valor,
    onComplete: function(resp) {
      try {
        resultado = resp.responseText.evalJSON(true);
        
        if(resultado[1]=='true') {
          $('autorizado_por').style.display='';
          $('autorizado_fecha').style.display='';
          $('autorizado_por_campo').innerHTML=resultado[2];
          $('autorizado_fecha_campo').innerHTML=resultado[3];
          
        } else if(resultado[1]=='false') {
          $('autorizado_por').style.display='none';
          $('autorizado_fecha').style.display='none';
        }
        
      } catch(err) {
        alert(resp.responseText.unescapeHTML());
        checkbox.checked=!checkbox.checked;
      }

      checkbox.disabled=false;

    }
  }
  );
  
}


<?php } ?>

imprimir_solicitud=function() {

  _general = $('tab_general_content').innerHTML;
  _totales = $('tab_totales_content').innerHTML;
  _detalle = $('tab_movimientos_content').innerHTML;
  
  _separador1 = '<hr><h3>Totales Generales</h3><hr>';
  _separador2 = '<hr><h3>Detalle de Movimientos</h3></hr>';
  
  imprimirHTML(_general+_separador1+_totales+_separador2+_detalle);

}


</script>

<body class="fuente_por_defecto popup_background">

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_archivos' style='cursor: pointer;'
      onClick='ver_archivos();'>
      <img src='iconos/attach.png'>
      Archivos Adjuntos</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_totales' style='cursor: pointer;'
      onClick='ver_total();'>
      <img src='iconos/page_white_magnify.png'>
      Totales</div>
		  </td>
      </tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>

<table style='width:100%;'>

<tr>
<td style='text-align: right;width:35%;'>N&uacute;mero de Solicitud:</td>
<td><b><?php echo $solicitud[0]['sol_id']; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Fecha de Emisi&oacute;n:</td>
<td><b><?php echo $solicitud[0]['sol_fecha']; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Funcionario Emisor:</td>
<td><?php echo htmlentities($solicitud[0]['f1_func_nombre']); ?></td>
</tr>
<tr>
<td style='text-align: right;'>Ubicaci&oacute;n:</td>
<td><?php echo htmlentities($solicitud[0]['sol_ubica']); ?></td>
</tr>
<tr>
<td style='text-align: right;'>Tipo:</td>
<td><b><?php echo $tipo; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Prioridad:</td>
<td><b><?php echo $urgente; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Estado:</td>
<td><b><?php echo $estado; ?></b></td>
</tr>

<tr>
<td style='text-align: right;'>Validez SD:</td>
<td>
<INPUT TYPE="checkbox" NAME="" ID="" 
onChange="autorizar_sol_1(this);"> Autorizado</td>
</tr>


<tr>
<td style='text-align: right;'>Validez SDA:</td>
<td>
<INPUT TYPE="checkbox" NAME="" ID="" 
onChange="autorizar_sol_2(this);"> Autorizado</td>
</tr>

</table>

</div>

<div id='tab_archivos_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $archivos_html; ?>

</div>
<div id='tab_totales_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $totales_html; ?>

</div>

</td>
</tr>
</table>

<center>

    <div class='boton' id='imprimir_pedido_btn'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_pedido();'>
		Imprimir Pedido...</a>
		</td></tr></table>
		</div>

</center>

</body></html>

<?php } ?>

<?php if(isset($_GET['doc_id'])): ?>
<html>
	<head>
    	<?php cabecera_popup('.'); ?>
        <script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_movimientos');
  tab_down('tab_series');
  tab_down('tab_mods');

}

ver_movs=function() {

  tab_down('tab_general');
  tab_up('tab_movimientos');
  tab_down('tab_series');
  tab_down('tab_mods');

}

ver_series=function() {

  tab_down('tab_general');
  tab_down('tab_movimientos');
  tab_up('tab_series');
  tab_down('tab_mods');

}

ver_mods=function() {

  tab_down('tab_general');
  tab_down('tab_movimientos');
  tab_down('tab_series');
  tab_up('tab_mods');

}

imprimir_recepcion=function() {

  _general = $('tab_general_content').innerHTML;
  _detalle = $('tab_movimientos_content').innerHTML;
  _series = $('tab_series_content').innerHTML;
  
  _separador2 = '<hr><h3>Detalle de Movimientos</h3></hr>';
  _separador3 = '<hr><h3>Detalle de Lotes</h3></hr>';
  
  _recepcion_conforme='<br><br><br><br><center><table><tr><td style="text-align: right;">________________________<br>Firma Recepci&oacute;n Conforme</td></tr></table></center>';
  
  imprimirHTML(_general+_separador2+_detalle+_separador3+_series+_recepcion_conforme);

}


modificar_recepcion=function() {

 top=Math.round(screen.height/2)-175;
 left=Math.round(screen.width/2)-375;

  window.open('modificar_recepcion.php?doc_id=<?php echo $doc_id; ?>&log_folio=<?php echo $log_folio; ?>', 
  '_modificar_recep', 'toolbar=no, location=no, directories=no, status=no, '+
  'menubar=no, scrollbars=yes, resizable=no, width=750, height=350, '+
  'top='+top+', left='+left);

}

<?php if(_cax(501)) { ?>

autorizar_mod=function() {
	
	var myAjax=new Ajax.Request(
		'sql_autoriza_modificacion.php',
		{
			method:'post',
			parameters:'doc_id=<?php echo $doc_id; ?>',
			onComplete:function(r) {
				alert(r.responseText.unescapeHTML());
			}
		}
	)
	
}

<?php } ?>

</script>
    </head>
    <body>
    <?php
	
  
  // Visualizar una Recepción
  
 
  $recepcion=pg_query($conn, "
  SELECT
  date_trunc('second',log_fecha),
  doc_tipo,
  prov_rut,
  prov_glosa,
  doc_num,
  doc_iva,
  doc_id,
  round(doc_descuento)AS doc_descuento,
  doc_orden_id, doc_orden_desc,
  doc_observacion
  FROM documento
  JOIN proveedor ON doc_prov_id=prov_id
  JOIN logs ON log_doc_id=doc_id
  WHERE doc_id=".$doc_id."
  ");
  
  $recepcion_a = pg_fetch_row($recepcion);
  
  switch($recepcion_a[1]) {
      case 0: $recepcion_a[1]='Guía de Despacho'; break;
      case 1: $recepcion_a[1]='Factura'; break;
      case 2: $recepcion_a[1]='Boleta'; break;
      case 3: $recepcion_a[1]='Pedido'; break;
      case 4: $recepcion_a[1]='Resoluci&oacute;n (Donaciones)'; break;
  }
  
  
  $recepciones = pg_query($conn, "
	  SELECT log_id, fecha, func_nombre, bodega_id, bod_glosa, log_tipo, log_folio
	  FROM (
		SELECT 
		log_id, 
		date_trunc('second', log_fecha) AS fecha,
		func_nombre,
		(SELECT stock_bod_id FROM stock 
		WHERE stock_log_id=log_id LIMIT 1) AS bodega_id, log_tipo, log_folio
		FROM logs 
		JOIN funcionario ON log_func_if=func_id
		WHERE log_doc_id=".$doc_id."
		) AS foo
	  LEFT JOIN bodega ON bodega_id=bod_id
  ");

  $detalles_html='';

  $numrec=pg_num_rows($recepciones);
  $totalgeneral=0;

  for($r=0;$r<$numrec;$r++) {
  
    $_recepcion=pg_fetch_row($recepciones);
    
    if($_recepcion[5]==1) {
    
    $detalle_recepcion = pg_query($conn, "
		SELECT
		art_codigo,
		art_glosa,
		stock_cant,
		stock_subtotal,
		forma_nombre,
		stock_vence
		FROM 
		stock
		JOIN articulo ON stock_art_id=art_id
		LEFT JOIN bodega_forma ON art_forma=forma_id
		WHERE stock_log_id=".$_recepcion[0]."
    ");
  
    $detalles_html.='
    <table width=100% style="font-size: 11px;">
    <tr><td style="text-align: right; font-weight: bold;">Folio Recep.:</td>
    <td colspan=3 style="font-size:14px;"><b>'.$_recepcion[6].'</b></td></tr>
    <tr><td style="text-align: right; font-weight: bold;">Fecha:</td>
    <td colspan=3><b>'.$_recepcion[1].'</b></td></tr>
    <tr><td style="text-align: right; font-weight: bold;">Ubicaci&oacute;n:</td>
    <td colspan=3>'.htmlentities($_recepcion[4]).'</td></tr>
    <tr><td style="text-align: right; font-weight: bold;">Funcionario:</td>
    <td colspan=3>'.htmlentities($_recepcion[2]).'</td></tr>
    <tr class="tabla_header" style="font-weight: bold;">
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Cantidad</td>
    <td>Forma/Unidad</td>
    <td>P. Unit.</td>
    <td>Vence</td>
    <td>Subtotal</td>
    </tr>
    ';
  
    $sumatoria=0;
  
    for($i=0;$i<pg_num_rows($detalle_recepcion);$i++) {
  
    
    $detalle_a = pg_fetch_row($detalle_recepcion);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $detalles_html.="
    <tr class='".$clase."'>
    <td style='text-align: right;'>".$detalle_a[0]."</td>
    <td>".htmlentities($detalle_a[1])."</td>
    <td style='text-align: right;'>".number_formats($detalle_a[2])."</td>
    <td style='text-align: left;'>".htmlentities($detalle_a[4])."</td>
    <td style='text-align: right;'>$".number_formats($detalle_a[3]/$detalle_a[2]).".-</td>
    <td style='text-align: right;'>".$detalle_a[5]."</td>
    <td style='text-align: right;'>$".number_formats($detalle_a[3]).".-</td>
    </tr>
    ";
  
    $sumatoria+=$detalle_a[3];
  
    }

    if($recepcion_a[7]>0 AND $numrec==1) {
      
     ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    	$detalles_html.="<tr class='".$clase."' style='color:red;'><td colspan='6' style='text-align: right;'>Descuento Neto:</td><td style='text-align: right;'>$".number_formats(($recepcion_a[7])).".-</td></tr>";

      $sumatoria-=round($recepcion_a[7]);
      
    }
  
    $neto=round($sumatoria);
    $totalgeneral+=$sumatoria;
    $total=round($sumatoria*$recepcion_a[5]); // Multiplica por el IVA Asociado.
    $iva=$total-$neto;
    
//echo $recepcion_a[8]." -- ".$recepcion_a[9]."<BR>";
    if ((trim($recepcion_a[9]) == 'PROGRAMA MINISTERIALES' || trim($recepcion_a[9]) == 'INTERM' || trim($recepcion_a[9]) == 'HEPATITI C') &&(($iva - intval($iva)) > 0))//SI ES DE LA CENABAST
      $iva++;
    // Necesario para bug en impresión de Mozilla Firefox
    // inserta un page-break despues de las recepciones pares que no son
    // la ultima. Ej. Imprime max 2 recepciones por hoja y luego se pasa a la
    // sig.. 
  
    if(
      ($r==1 and $r<pg_num_rows($recepciones)-1) or 
      ($r==3 and $r<pg_num_rows($recepciones)-1) or 
      ($r==5 and $r<pg_num_rows($recepciones)-1) or 
      ($r==7 and $r<pg_num_rows($recepciones)-1) 
      ) {
      $p_break='style="page-break-after: always;"';
    } else {
      $p_break='';
    
    }
  
    if($numrec==1)  $totaltitulo='Total General'; 
    else            $totaltitulo='Subtotal';
  
    $detalles_html.='
    <tr class="tabla_header">
    <td colspan=5 rowspan=3>'.$totaltitulo.'</td><td>Neto:</td>
    <td style="text-align:right;">$'.number_formats($neto).'.-</td></tr>
    <tr class="tabla_header"><td>I.V.A.:</td>
    <td style="text-align:right;">$'.number_formats($iva).'.-</td></tr>
    <tr class="tabla_header"><td>Total:</td>
    <td style="text-align:right;">$'.number_formats($total).'.-</td></tr>
    </table><hr '.$p_break.'>';  
  
    } else {
    
    $detalle_recepcion = pg_query($conn, "
    SELECT
    serv_glosa,
    serv_cant,
    serv_subtotal,
    serv_item,
    item_glosa,
    serv_unidad,
    serv_vence
    FROM 
    servicios
    LEFT JOIN item_presupuestario ON serv_item=item_codigo
    WHERE serv_log_id=".$_recepcion[0]."
    ");
    
    $_centro=cargar_registros_obj("
    SELECT * FROM cargo_centro_costo WHERE log_id=".$_recepcion[0]."
    ");
  
    $_gasto=cargar_registros_obj("
    SELECT * FROM cargo_gasto_externo WHERE log_id=".$_recepcion[0]."
    ");
  
    if($_centro) {
    
      $centro_desc=cargar_registros_obj("
      SELECT * FROM centro_costo
      WHERE centro_ruta='".$_centro[0]['centro_ruta']."'
      ");
      
      $centros='<tr><td style="text-align: right; font-weight: bold;">
                Centro de Costo:</td>
                <td colspan=3 style="font-weight:bold;">
                '.htmlentities($centro_desc[0]['centro_nombre']).'</td>
                </tr>';
    
    } else {
    
      $gasto_desc=cargar_registros_obj("
      SELECT * FROM gasto_externo 
      WHERE gastoext_id=".$_gasto[0]['gastoext_id']."
      ");
    
      $centros='<tr><td style="text-align: right; font-weight: bold;">
                Gasto Subdistribuido:</td>
                <td colspan=3 style="font-weight:bold;">
                '.htmlentities($gasto_desc[0]['gastoext_nombre']).'</td>
                </tr>';
    
    }
  
  
    $detalles_html.='
    <table width=100% style="font-size: 11px;">
    <tr><td style="text-align: right; font-weight: bold;">Fecha:</td>
    <td colspan=3><b>'.$_recepcion[1].'</b></td></tr>
    '.$centros.'
    <tr><td style="text-align: right; font-weight: bold;">Funcionario:</td>
    <td colspan=3>'.htmlentities($_recepcion[2]).'</td></tr>
    <tr class="tabla_header" style="font-weight: bold;">
    <td style="width:40%;">Glosa</td>
    <td>Item Presupuestario</td>
    <td>UD</td>
    <td>Vence</td>
    <td>Cantidad</td>
    <td>Subtotal</td>
    </tr>
    ';
  
    $sumatoria=0;
  
    for($i=0;$i<pg_num_rows($detalle_recepcion);$i++) {
  
    
    $detalle_a = pg_fetch_row($detalle_recepcion);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $detalles_html.="
    <tr class='".$clase."'>
    <td>".htmlentities($detalle_a[0])."</td>
    <td>".$detalle_a[3]." - ".htmlentities($detalle_a[4])."</td>
    <td>".$detalle_a[5]."</td>
    <td>".$detalle_a[6]."</td>
    <td style='text-align: right;'>".number_formats($detalle_a[1])."</td>
    <td style='text-align: right;'>$".number_formats($detalle_a[2]).".-</td>
    </tr>
    ";
  
    $sumatoria+=$detalle_a[2];
  
    }
    
    if($recepcion_a[7]>0 AND $numrec==1) {
      
     ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $detalles_html.="<tr class='".$clase."' style='color:red;'><td colspan=5 style='text-align: right;'>Descuento Neto:</td><td style='text-align: right;'>$".number_formats(($recepcion_a[7])).".-</td></tr>";

      $sumatoria-=round($recepcion_a[7]);
      
    }
    
    $neto=$sumatoria;
    
    $totalgeneral+=$sumatoria;
    $total=$sumatoria*$recepcion_a[5]; // Multiplica por el IVA Asociado.
    $iva=$total-$neto;
   
    // Necesario para bug en impresión de Mozilla Firefox
    // inserta un page-break despues de las recepciones pares que no son
    // la ultima. Ej. Imprime max 2 recepciones por hoja y luego se pasa a la
    // sig.. 
  
    if(
      ($r==1 and $r<pg_num_rows($recepciones)-1) or 
      ($r==3 and $r<pg_num_rows($recepciones)-1) or 
      ($r==5 and $r<pg_num_rows($recepciones)-1) or 
      ($r==7 and $r<pg_num_rows($recepciones)-1) 
      ) {
      $p_break='style="page-break-after: always;"';
    } else {
      $p_break='';
    
    }
  
    if($numrec==1)  $totaltitulo='Total General'; 
    else            $totaltitulo='Subtotal';
  
    $detalles_html.='
    <tr class="tabla_header">
    <td colspan=4 rowspan=3>'.$totaltitulo.'</td><td>Neto:</td>
    <td style="text-align:right;">$'.number_formats($neto).'.-</td></tr>
    <tr class="tabla_header"><td>I.V.A.:</td>
    <td style="text-align:right;">$'.number_formats($iva).'.-</td></tr>
    <tr class="tabla_header"><td>Total:</td>
    <td style="text-align:right;">$'.number_formats($total).'.-</td></tr>
    </table><hr '.$p_break.'>';  
    
    }
  
  }
  
  if($numrec>1) {
  
    $neto=$totalgeneral-$recepcion_a[7];
    $total=$neto*$recepcion_a[5]; // Multiplica por el IVA Asociado.
    $iva=$total-$neto;

    if($recepcion_a[7]>0) {
      $filas=4;
      $desc='
      <td>Descuento</td>
      <td style="text-align:right;">-$'.number_formats($recepcion_a[7]).'.-
      </td></tr>
      <tr class="tabla_header">
      ';
    } else {
      $filas=3;
      $desc='';
    }

    $detalles_html.='
    <table style="width:100%; font-size:11px;">
    <tr class="tabla_header">
    <td style="width:50%;" rowspan='.$filas.'>Total General</td>
    '.$desc.'
    <td>Neto:</td>
    <td style="text-align:right;">$'.number_formats($neto).'.-</td></tr>
    <tr class="tabla_header">
    <td>I.V.A.:</td>
    <td style="text-align:right;">$'.number_formats($iva).'.-</td></tr>
    <tr class="tabla_header">
    <td>Total:</td>
    <td style="text-align:right;">$'.number_formats($total).'.-</td></tr>
    </table>';  

  
  }
  
  $orden=$recepcion_a[8];
  $ordenes_html='';
  
  if($orden!='0') {
    $ordenes_html='<tr><td style="text-align:right;">
                  &Oacute;rden de Compra:</td><td>';

    $ordenes=cargar_registros_obj("
    SELECT orden_numero, orden_id, date_trunc('second', orden_fecha) AS orden_fecha
    FROM orden_compra
    WHERE orden_numero='".($recepcion_a[9])."'
    ORDER BY orden_fecha
    ", true);

    if($ordenes) {                  
      for($i=0;$i<count($ordenes);$i++) {
        $ordenes_html.='<a href="#" class="texto_tooltip"
                        onClick="abrir_orden(\''.$ordenes[$i]['orden_id'].'\');">'.
                        $ordenes[$i]['orden_numero'].
                       '</a> <span style="font-size:10px;">['.
                       $ordenes[$i]['orden_fecha'].
                       ']</span><br>';
      }
    } else {
      $ordenes_html.=$recepcion_a[9];    
    }
    
    $ordenes_html.='</td></tr>';
    
  } else {
    
    if($recepcion_a[9]=='')
      $ordenes_html='<tr><td style="text-align:right;">
                    &Oacute;rdenes de Compra:</td><td>
                    <em>No hay ordenes de compra asociadas.</em>
                    </td></tr>';	
    else
      $ordenes_html='<tr><td style="text-align:right;">
                    &Oacute;rdenes de Compra:</td><td>
                    '.$recepcion_a[9].'
                    </td></tr>';
                  
  }

  $series = cargar_registros_obj(
  "
  SELECT art_codigo, art_glosa, forma_nombre, stock_serie, stock_vence
  FROM documento
  JOIN logs ON doc_id=log_doc_id
  JOIN stock ON stock_log_id=log_id
  JOIN articulo ON stock_art_id=art_id
  LEFT JOIN bodega_forma ON art_forma=forma_id
  JOIN stock_refserie USING (stock_id)
  WHERE doc_id=$doc_id
  ORDER BY art_codigo 
  "
  );
  
  $partidas = cargar_registros_obj(
  "
  SELECT art_codigo, art_glosa, forma_nombre, stock_partida, stock_vence
  FROM documento
  JOIN logs ON doc_id=log_doc_id
  JOIN stock ON stock_log_id=log_id
  JOIN articulo ON stock_art_id=art_id
  LEFT JOIN bodega_forma ON art_forma=forma_id
  JOIN stock_refpartida USING (stock_id)
  WHERE doc_id=$doc_id
  ORDER BY art_codigo 
  "
  );
  
  if($series) {

    $series_html='
      <table width=100% style="font-size: 11px;">
      <tr class="tabla_header" style="font-weight: bold;">
      <td>Codigo Int.</td>
      <td>Descripci&oacute;n</td>
      <td>Forma</td>
      <td>Fecha de Venc.</td>
      <td>Nro. de Serie</td>
      </tr>
    ';
    
    for($i=0;$i<count($series);$i++) {
    
      $reg=$series[$i];
      
      ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
      
      $series_html.='<tr class="'.$clase.'">';
      $series_html.='<td style="text-align:right;">'.$reg['art_codigo'].'</td>';
      $series_html.='<td>'.htmlentities($reg['art_glosa']).'</td>';
      $series_html.='<td>'.htmlentities($reg['forma_nombre']).'</td>';
      $series_html.='<td style="text-align:center;">'.$reg['stock_vence'].'</td>';
      $series_html.='<td>'.$reg['stock_serie'].'</td>';
      $series_html.='</tr>';
      
    
    }
    
    $series_html.='</table>';

  } else $series_html='';


  if($partidas) {
    
    $partidas_html='
      <table width=100% style="font-size: 11px;">
      <tr class="tabla_header" style="font-weight: bold;">
      <td>Codigo Int.</td>
      <td>Descripci&oacute;n</td>
      <td>Forma</td>
      <td>Fecha de Venc.</td>
      <td>Nro. de Partida</td>
      </tr>
    ';
    
    for($i=0;$i<count($partidas);$i++) {
    
      $reg=$partidas[$i];
      
      ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
      
      $partidas_html.='<tr class="'.$clase.'">';
      $partidas_html.='<td style="text-align:right;">'.$reg['art_codigo'].'</td>';
      $partidas_html.='<td>'.htmlentities($reg['art_glosa']).'</td>';
      $partidas_html.='<td>'.htmlentities($reg['forma_nombre']).'</td>';
      $partidas_html.='<td style="text-align:center;">
                      '.$reg['stock_vence'].'</td>';
      $partidas_html.='<td>'.$reg['stock_partida'].'</td>';
      $partidas_html.='</tr>';
      
    
    }
    
    $partidas_html.='</table>';
    
  } else $partidas_html='';
  
  $mods=cargar_registros_obj("
  SELECT dm.*, f1.func_nombre AS func_nombre1, f2.func_nombre AS func_nombre2 
  FROM documento_modificaciones AS dm
  LEFT JOIN funcionario AS f1 ON f1.func_id=func_id1
  LEFT JOIN funcionario AS f2 ON f2.func_id=func_id1
  WHERE doc_id=$doc_id
  ORDER BY docm_fecha_realiza, docm_fecha_autoriza DESC;");
  
?>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_movimientos' style='cursor: pointer;'
      onClick='ver_movs();'>
      <img src='iconos/page_white_find.png'>
      Detalle</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_series' style='cursor: pointer;'
      onClick='ver_series();'>
      <img src='iconos/folder_table.png'>
      Nros. de Serie</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_mods' style='cursor: pointer;'
      onClick='ver_mods();'>
      <img src='iconos/wrench.png'>
      Modificaciones (<?php if($mods) echo sizeof($mods); else echo '0'; ?>)</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>


<table width=100% style="font-size: 12px;">
  
<tr>
<td style='text-align: right; width:150px;'>Correlativo Int.:</td>
<td style='font-size: 20px;'><b><?php echo $recepcion_a[6]; ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>Fecha de Recepci&oacute;n:</td>
<td><?php echo $recepcion_a[0]; ?></td>
</tr>
<tr>
<td style='text-align: right;'>RUT Proveedor:</td>
<td><b><?php echo $recepcion_a[2]; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Nombre del Proveedor:</td>
<td><?php echo htmlentities($recepcion_a[3]); ?></td>
</tr>
<tr>
<td style='text-align: right;'>Tipo de Documento:</td>
<td><b><?php echo htmlentities($recepcion_a[1]); ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>N&uacute;mero:</td>
<td><b><?php echo htmlentities($recepcion_a[4]); ?></b></td>
</tr>
<?php echo $ordenes_html; ?>
<tr>
	<td style='text-align: right;'>Observaciones:</td>
	<td><?php echo htmlentities($recepcion_a[10]); ?></td>
</tr>

<?php 

	$aut=pg_query("SELECT * FROM documento_modificaciones 
				JOIN funcionario ON func_id1=func_id
				WHERE doc_id=$doc_id AND docm_fecha_realiza IS NULL;");
	
	if($dato=pg_fetch_assoc($aut)) {
		
		print("
		
		<tr>
		<td style='text-align: right;'>Autoriza Modificar:</td>
		<td><b>".htmlentities($dato['func_nombre'])."</b></td>
		</tr>
		<tr>
		<td style='text-align: right;'>Fecha Autorizaci&oacute;n:</td>
		<td>".substr($dato['docm_fecha_autoriza'],0,19)."</td>
		</tr>
		
		");

		if(_cax(501))
		print("
		<tr>
		<td style='text-align: right;'>Eliminar Autorizaci&oacute;n:</td>
		<td><input type='checkbox' id='autoriza' name='autoriza' onClick='autorizar_mod();' CHECKED /></td>
		</tr>
		");

		
	} elseif(_cax(501)) {

		print("
		<tr>
		<td style='text-align: right;'>Autorizar Modificaci&oacute;n:</td>
		<td><input type='checkbox' id='autoriza' name='autoriza' onClick='autorizar_mod();' /></td>
		</tr>
		");
		
	}
			

?>



</table>

</div>

<div id='tab_movimientos_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $detalles_html; ?>

</div>

<div id='tab_series_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $series_html; echo $partidas_html; ?>

</div>

<div id='tab_mods_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php 

	if($mods) {
		
		print("<table style='width:100%;font-size:11px;'>");
		
		for($i=0;$i<sizeof($mods);$i++) {
			
				print("<tr><td style='text-align:right;' class='tabla_fila2'>Fecha Autorizaci&oacute;n:</td><td>".substr($mods[$i]['docm_fecha_autoriza'],0,19)."</td></tr>");
				print("<tr><td style='text-align:right;' class='tabla_fila2'>Func. Autoriza:</td><td>".$mods[$i]['func_nombre1']."</td></tr>");
				print("<tr><td style='text-align:right;font-weight:bold;' class='tabla_fila2'>Fecha Modificaci&oacute;n:</td><td style='font-weight:bold;'>".(($mods[$i]['docm_fecha_realiza']!='')?substr($mods[$i]['docm_fecha_realiza'],0,19):substr($mods[$i]['docm_fecha_autoriza'],0,19))."</td></tr>");
				print("<tr><td style='text-align:right;font-weight:bold;' class='tabla_fila2'>Func. Modifica:</td><td style='font-weight:bold;'>".$mods[$i]['func_nombre2']."</td></tr>");
				
				if($mods[$i]['docm_datos_previos']!='') {
				
				print("<tr><td colspan=2 class='sub-content2'>");
				
				$d=json_decode($mods[$i]['docm_datos_previos'], true);
				
				print("<table style='width:100%;font-size:12px;'>
						   <tr>
							   <td style='text-align:right;' class='tabla_fila2'>Proveedor:</td>
							   <td><b>".$d['prov_rut']."</b> ".$d['prov_glosa']."</td>
						   </tr>
						   <tr>
							   <td style='text-align:right;' class='tabla_fila2'>O.C.:</td>
							   <td><b>".$d['doc_orden_desc']."".$d['orden_numero']."</b></td>
						   </tr>
					   </table>
					   <table style='width:100%;font-size:12px;'>
						   <tr class='tabla_header'>
							   <td>Cod.</td><td>Art&iacute;culo</td><td>Cant.</td><td>Vence</td><td>Subtotal</td>
						   </tr>");
						   
				$a=$d['detalle'];
				
				for($j=0;$j<sizeof($a);$j++) {
					
						$clase=($j%2==0)?'tabla_fila':'tabla_fila2';
					
						print("
						<tr class='$clase'>
						<td style='text-align:right;font-weight:bold;'>".$a[$j]['art_codigo']."</td>
						<td style='text-align:left;'>".$a[$j]['art_glosa']."</td>
						<td style='text-align:right;'>".$a[$j]['stock_cant']."</td>
						<td style='text-align:center;'>".$a[$j]['stock_vence']."</td>
						<td style='text-align:right;font-weight:bold;'>$".number_format($a[$j]['stock_subtotal'],0,',','.').".-</td>
						</tr>
						");
					
				}
						   
				print("</table>");
				
				
				print("</td></tr>");
				
				}
			
		}
		
		print("</table>");
		
	}

?>

</div>


</td>
</tr>
</table>

<center>
		<table>
    	<td><div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_recepcion();'>
		Imprimir Recepci&oacute;n...</a>
		</td></tr></table>
		</div></td>
		
		<?php if(_cax(500)){ // PERMISO DE MODIFICAR...
			
			if(pg_num_rows($aut)>0) { // AUTORIZADO A MODIFICAR...
			
			?>		
		<td><div class='boton'>
		<table><tr><td>
		<img src='iconos/pencil.png'>
		</td><td>
		<a href='#' onClick='modificar_recepcion();'>
		Modificar Recepci&oacute;n...</a>
		</td></tr></table>
		</div></td>
		<?php 
		
		} // IF AUTORIZACIÓN... 
		
		} // IF PERMISO MODIFICACIÓN...
		
		
		?>
		</table>
</center>
    </body>
</html>
<?php endif;?>
<?php
  if(isset($_GET['id_pedido']) OR isset($_GET['pedido_nro'])) {
  
  // Visualizar un Pedido
  
  if(isset($_GET['id_pedido'])) $where='pedido.pedido_id='.($_GET['id_pedido']*1);
  if(isset($_GET['pedido_nro'])) $where='pedido_nro='.($_GET['pedido_nro']*1);
  
  
  $pedido=pg_query($conn, "
  SELECT 
  pedido.pedido_id,
  pedido_nro,
  date_trunc('second', pedido_fecha) AS pedido_fecha,
  COALESCE(b1.bod_glosa, c1.centro_nombre, centro_costo.centro_nombre, instsol_desc), 
  COALESCE(b2.bod_glosa, 'Abastecimiento'), 
  f1.func_nombre,
  pedido_estado,
  COALESCE(centro_costo.centro_ruta,instsol_id::Text),
  b2.bod_id,
  pedido_autorizado,
  b1.bod_id,
  f3.func_id,
  COALESCE(f3.func_nombre, f1.func_nombre),
  date_trunc('second', COALESCE(pedidoa_fecha, pedido_fecha)) 
    AS autoriza_fecha,
  c1.centro_ruta, c1.centro_nombre
  
  
  FROM 
  pedido
  
  LEFT JOIN bodega AS b1 ON b1.bod_id=origen_bod_id
  LEFT JOIN bodega AS b2 ON b2.bod_id=destino_bod_id
  JOIN funcionario AS f1 ON f1.func_id=pedido_func_id
  LEFT JOIN logs ON log_id_pedido=pedido_id
  LEFT JOIN pedido_autorizacion ON pedido.pedido_id=pedido_autorizacion.pedido_id
  LEFT JOIN funcionario AS f2 ON f2.func_id=log_func_if
  LEFT JOIN funcionario AS f3 ON pedido_autorizacion.func_id=f3.func_id
  LEFT JOIN cargo_centro_costo ON logs.log_id=cargo_centro_costo.log_id
  LEFT JOIN centro_costo USING (centro_ruta)
  LEFT JOIN centro_costo AS c1 on c1.centro_ruta=origen_centro_ruta
  LEFT JOIN institucion_solicita ON instsol_id::Text=cargo_centro_costo.centro_ruta
  
  WHERE
  ".($where)."
  ");
  
  if(pg_num_rows($pedido)>0)
    $pedido_a = pg_fetch_row($pedido);
  else
    die('Error Inesperado.');
  
  $id_pedido=$pedido_a[0];
    
  switch($pedido_a[6]) {
    case 0: $pedido_a[6]='Enviado'; break;
    case 1: $pedido_a[6]='Retornado'; break;
    case 2: $pedido_a[6]='Terminado'; break;
    case 3: $pedido_a[6]='Anulado'; break;
    case 9: $pedido_a[6]='No Procesado'; break;
  }
  
  if($pedido_a[7]!='') {
    $valor_totales = '-(SUM(stock_cant))';
    $origen = 'destino_bod_id';
  } else {
    $valor_totales = 'SUM(stock_cant)';
    $origen = 'origen_bod_id';
  }
  
  if($pedido_a[8]==NULL)
    $wh=315; else $wh=335;
  
  $totales = pg_query($conn, "
  SELECT 
  art_codigo,
  art_glosa,
  pedidod_cant,
  (
  SELECT $valor_totales FROM stock
  LEFT JOIN logs ON log_id_pedido=pedido_id
  WHERE 
  stock_log_id=log_id
  AND
  stock_art_id=art_id
  AND
  stock_bod_id=$origen
  ),
  forma_nombre
  
  FROM 
  pedido_detalle
  
  JOIN articulo USING (art_id)
  JOIN pedido USING (pedido_id)
  left join bodega_forma on forma_id=art_forma
  
  WHERE
  ".($where)."
  ");
  
  $totales_html = '<table style="font-size: 11px;" width=100%>
  <tr class="tabla_header" style="font-weight: bold;"><td colspan=3>Datos del Art&iacute;culo</td><td colspan=3>Cantidades</td></tr>
  <tr class="tabla_header" style="font-weight: bold;"><td>Codigo Int.</td><td>Glosa</td><td>Forma</td><td>Solicitada</td><td>Enviada</td><td>Diferencia</td></tr>';
  
  for($i=0;$i<pg_num_rows($totales);$i++) {
  
    $total_a = pg_fetch_row($totales);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    if(($total_a[3]-$total_a[2])>0) 
      $signo='+'; 
    else
      $signo='';
    
    $totales_html.='<tr class="'.$clase.'"><td style="text-align: right;">'.$total_a[0].'</td><td>'.htmlentities($total_a[1]).'</td><td style="text-align: right;">'.$total_a[4].'</td><td style="text-align: right;">'.number_format($total_a[2],1,',','.').'.-</td><td style="text-align: right;">'.number_format($total_a[3],1,',','.').'.-</td><td style="text-align: right;">'.$signo.''.number_format($total_a[3]-$total_a[2],1,',','.').'.-</td></tr>';
  
  }
  
  $totales_html.='</table>';
  
  if($pedido_a[7]!='') {
    $valor_totales = '-(SUM(stock_cant))';
    $stock_fld = '-(stock_cant)';
    $origen = 'destino_bod_id';
  } else {
    $valor_totales = 'SUM(stock_cant)';
    $stock_fld = 'stock_cant';
    $origen = 'origen_bod_id';
  }
  
  $detalle_logs = pg_query($conn, "
  SELECT
  log_id,
  func_nombre,
  log_fecha,
  log_comentario
  FROM logs
  JOIN funcionario ON log_func_if=func_id
  WHERE
  log_id_pedido=".($id_pedido)."
  ");								
  
  $detalles_html='';
  
  for($m=0;$m<pg_num_rows($detalle_logs);$m++) {
  
  $detalle_log = pg_fetch_row($detalle_logs);
  //print_r($detalle_log);
  
  $detalle = pg_query($conn, "
  (SELECT
  art_codigo,
  art_glosa,
  stock_vence,
  $stock_fld,
  0,
  forma_nombre
  
  FROM pedido
  LEFT JOIN logs ON log_id_pedido=pedido_id
  LEFT JOIN stock ON stock_log_id=log_id
  LEFT JOIN articulo ON art_id=stock_art_id
  LEFT JOIN bodega_forma ON forma_id=art_forma
  WHERE log_id=".$detalle_log[0]."
  AND stock_bod_id=$origen
  )
  UNION
  (SELECT
  art_codigo,
  art_glosa,
  stock_vence,
  $stock_fld,
  1,
  forma_nombre
  
  FROM pedido
  LEFT JOIN logs  ON log_id_pedido=pedido_id
  LEFT JOIN stock_rechazado ON stock_log_id=log_id
  LEFT JOIN articulo ON art_id=stock_art_id
  LEFT JOIN bodega_forma ON forma_id=art_forma
  WHERE log_id=".$detalle_log[0]."
  AND stock_bod_id=$origen
  )
  
  ");

  
  $detalles_html .= '
  <table style="font-size: 11px;" width=100%>
  <tr><td style="text-align: right;">Fecha/Hora:</td>
  <td style="font-weight: bold;">'.$detalle_log[2].'</td></tr>
  <tr><td style="text-align: right;">Funcionario:</td>
  <td style="font-weight: bold;">'.htmlentities($detalle_log[1]).'</td></tr>
  <tr><td style="text-align: right;">Comentarios:</td>
  <td style="font-weight: bold;">'.htmlentities($detalle_log[3]).'</td></tr>
  
  <tr class="tabla_header" style="font-weight: bold;"><td>C&oacute;digo Int.</td><td>Glosa</td><td>Forma</td><td>Fecha Venc.</td><td>Cantidad</td></tr>';
  
  for($i=0;$i<pg_num_rows($detalle);$i++) {
  
    $detalle_a = pg_fetch_row($detalle);
  
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    if($detalle_a[4]==1)  $estilo='color: red;';
    else                  $estilo='';
    
    $detalles_html .= '<tr class="'.$clase.'" style="'.$estilo.'"><td style="text-align: right;">'.$detalle_a[0].'</td><td>'.htmlentities($detalle_a[1]).'</td><td>'.$detalle_a[5].'</td><td style="text-align: center;">'.$detalle_a[2].'</td><td style="text-align: right;">'.number_format($detalle_a[3],1,',','.').'.-</td></tr>';
  
  }

  $detalles_html .= '</table><hr>';
  
  }
  
  
  $valores_html='';
  
  if($pedido_a[8]==NULL) {
  
  $valores = pg_query('
  SELECT
  art_codigo,
  art_glosa,
  pedidod_cant,
  forma_nombre,
  art_val_ult,
  art_val_ult*pedidod_cant
  FROM 
    pedido
  JOIN pedido_detalle ON pedido_detalle.pedido_id=pedido.pedido_id
  JOIN articulo ON pedido_detalle.art_id=articulo.art_id
  LEFT JOIN bodega_forma ON art_forma=forma_id
  WHERE pedido.pedido_id='.($id_pedido).'
  ');

  $valores_html .= '
  <table style="font-size: 11px;" width=100%>
  <tr class="tabla_header" style="font-weight: bold;"><td>C&oacute;digo Int.</td><td>Glosa</td><td>Cantidad</td><td>Forma</td><td>P Unit.</td><td>Subtotal($)</td></tr>';
  
  $neto=0;
  
  for($i=0;$i<pg_num_rows($valores);$i++) {
  
    $valores_a = pg_fetch_row($valores);
  
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    if($valores_a[4]==1)  $estilo='color: red;';
    else                  $estilo='';
    
    $valores_html .= '<tr class="'.$clase.'" style="'.$estilo.'"><td style="text-align: right;">'.$valores_a[0].'</td><td>'.htmlentities($valores_a[1]).'</td><td style="text-align: right;">'.number_formats($valores_a[2]).'</td><td style="text-align: left;">'.htmlentities($valores_a[3]).'</td><td style="text-align: right;">$'.number_formats($valores_a[4]).'.-</td><td style="text-align: right;">$'.number_formats($valores_a[5]).'.-</td></tr>';

  $neto+=$valores_a[5];
  $total=$neto*$_global_iva; // Multiplica por el IVA Asociado.
  $iva=$total-$neto;


  }

  $valores_html .= '
  <tr class="tabla_header">
  <td colspan=5 style="text-align:right;">Neto:</td>
  <td style="text-align:right;">$'.number_formats($neto).'.-</td>
  </tr>
  <tr class="tabla_header">
  <td colspan=5 style="text-align:right;">I.V.A.:</td>
  <td style="text-align:right;">$'.number_formats($iva).'.-</td>
  </tr>
  <tr class="tabla_header">
  <td colspan=5 style="text-align:right;">Total:</td>
  <td style="text-align:right;">$'.number_formats($total).'.-</td>
  </tr>
  
  </table><hr>';

  } else {
  
  $valores_html = '';

  }
  
  $repo_html='';
  
  $repo=cargar_registros('
  SELECT 
  tipotalonario_medicamento_clase,
  receta_numero,
  art_codigo,
  art_nombre,
  -(stock_cant)
  FROM reposicion_detalle 
	JOIN receta ON reposicion_detalle.receta_id=receta.receta_id
	JOIN receta_tipo_talonario ON tipotalonario_id=receta_tipotalonario_id
  JOIN recetas_detalle ON recetad_receta_id=receta.receta_id
  JOIN articulo ON articulo.art_id=recetad_art_id
  JOIN logs ON log_recetad_id=recetad_id
  JOIN stock ON stock_log_id=log_id
  WHERE 
  pedido_id='.$id_pedido,true);
  
  $repo_resumen=cargar_registros("
      SELECT art_codigo, art_glosa,
      -(SUM(stock_cant)), art_id
      FROM stock
      JOIN articulo ON stock_art_id=art_id
      JOIN logs ON stock_log_id=log_id
      JOIN recetas_detalle ON log_recetad_id=recetad_id
      JOIN receta ON recetad_receta_id=receta_id
      JOIN reposicion_detalle ON reposicion_detalle.receta_id=receta.receta_id
      WHERE 
        reposicion_detalle.pedido_id=$id_pedido AND
        log_tipo = 9
      GROUP BY art_codigo, art_glosa, art_id
  ",true);
  
  if($repo) {
    
    $repo_html.='
    <table style="width:100%;font-size:11px;">
    <tr class="tabla_header">
    <td>Tipo Receta</td>
    <td>Nro.</td>
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Cantidad</td>
    </tr>
    ';
    
    for($i=0;$i<count($repo);$i++) {
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $repo_html.='<tr class="'.$clase.'">
    <td>'.($repo[$i][0]).'</td>
    <td style="text-align:right;">'.($repo[$i][1]).'</td>
    <td style="text-align:right;">'.($repo[$i][2]).'</td>
    <td>'.($repo[$i][3]).'</td>
    <td style="text-align:right;">'.number_formats($repo[$i][4]).'</td>
    </tr>';
    }
    
    $repo_html.='
    </table>
    <table style="width:100%;font-size:11px;">
    <tr class="tabla_header">
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Cantidad</td>
    </tr>
    ';
  
    for($i=0;$i<count($repo_resumen);$i++) {
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $repo_html.='
    <tr class="'.$clase.'">
    <td style="text-align:right;">'.$repo_resumen[$i][0].'</td>
    <td>'.$repo_resumen[$i][1].'</td>
    <td style="text-align:right;">'.number_formats($repo_resumen[$i][2]).'</td>
    </tr>
    ';
    
    }
    
    $repo_html.='</table>';
    
  
  }
  
  
  // Si el pedido es a abastecimiento muestra órdenes de compra
  
  if($pedido_a[8]==0) {
  
  $ordenes=cargar_registros_obj("
  SELECT orden_compra.orden_id, 
  orden_numero, date_trunc('second', orden_fecha) AS orden_fecha
  FROM orden_pedido
  JOIN orden_compra ON orden_pedido.orden_id=orden_compra.orden_id
  WHERE orden_pedido.pedido_id=".($id_pedido)."
  ORDER BY orden_fecha
  ", true);
  
  $ordenes_html='';
  
  if($ordenes) {
    $ordenes_html='<tr><td style="text-align:right;">
                  &Oacute;rdenes de Compra:</td><td>';
    for($i=0;$i<count($ordenes);$i++) {
      $ordenes_html.='<span class="texto_tooltip"
                      onClick="abrir_orden(\''.$ordenes[$i]['orden_id'].'\');"> 
                      Orden #'.$ordenes[$i]['orden_id'].
                     '</span> <span style="font-size:10px;">['.
                     $ordenes[$i]['orden_fecha'].
                     ']</span><br>';
    }
    $ordenes_html.='</td></tr>';
  } else {
    $ordenes_html='<tr><td style="text-align:right;">
                  &Oacute;rdenes de Compra:</td><td>
                  <em>No hay ordenes de compra emitidas.</em>
                  </td></tr>';
  }
  
  } else {
  
    $ordenes_html='';
  
  }
  
?>

<html>
<title>Visualizar Pedido</title>

<?php cabecera_popup('.'); ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_totales');
  tab_down('tab_movimientos');
  tab_down('tab_valores');
  tab_down('tab_reposicion');

  $('imprimir_pedido_btn').style.display='';
  $('imprimir_reposicion_btn').style.display='none';

}

ver_total=function() {

  tab_down('tab_general');
  tab_up('tab_totales');
  tab_down('tab_movimientos');
  tab_down('tab_valores');
  tab_down('tab_reposicion');

  $('imprimir_pedido_btn').style.display='';
  $('imprimir_reposicion_btn').style.display='none';

}

ver_movs=function() {

  tab_down('tab_general');
  tab_down('tab_totales');
  tab_up('tab_movimientos');
  tab_down('tab_valores');
  tab_down('tab_reposicion');

  $('imprimir_pedido_btn').style.display='';
  $('imprimir_reposicion_btn').style.display='none';


}

ver_vals=function() {

  tab_down('tab_general');
  tab_down('tab_totales');
  tab_down('tab_movimientos');
  tab_up('tab_valores');
  tab_down('tab_reposicion');

  $('imprimir_pedido_btn').style.display='';
  $('imprimir_reposicion_btn').style.display='none';

}

ver_repo=function() {

  tab_down('tab_general');
  tab_down('tab_totales');
  tab_down('tab_movimientos');
  tab_down('tab_valores');
  tab_up('tab_reposicion');

  $('imprimir_pedido_btn').style.display='none';
  $('imprimir_reposicion_btn').style.display='';
  
}

<?php 
      
    if(_func_permitido(29,$pedido_a[10]) OR 
        _func_permitido_cc(29,$pedido_a[14])) { 

?>

autorizar_pedido = function(checkbox) {
  
  if(checkbox.checked)  valor='true';
  else                  valor='false';
  
  checkbox.disabled=true;
  
  var myAjax = new Ajax.Request('sql.php',
  {
    method:'get',
    parameters: 'accion=autorizar_pedido&pedido_id=<?php echo $pedido_a[0]; ?>&valor='+valor,
    onComplete: function(resp) {
      try {
        resultado = resp.responseText.evalJSON(true);
        
        if(resultado[1]=='true') {
          $('autorizado_por').style.display='';
          $('autorizado_fecha').style.display='';
          $('autorizado_por_campo').innerHTML=resultado[2];
          $('autorizado_fecha_campo').innerHTML=resultado[3];
          
        } else if(resultado[1]=='false') {
          $('autorizado_por').style.display='none';
          $('autorizado_fecha').style.display='none';
        }
        
      } catch(err) {
        alert(resp.responseText.unescapeHTML());
        checkbox.checked=!checkbox.checked;
      }

      checkbox.disabled=false;

    }
  }
  );
  
}


modificar_pedido=function() {

  window.open('modificar.php?pedido_id=<?php echo $pedido_a[0]; ?>', '_self');

}

<?php 
      }

      if($pedido_a[8]!=NULL) { 

?>

imprimir_pedido=function() {

  _general = $('tab_general_content').innerHTML;
  _totales = $('tab_totales_content').innerHTML;
  _detalle = $('tab_movimientos_content').innerHTML;
  
  _separador1 = '<hr><h3>Totales Generales</h3><hr>';
  _separador2 = '<hr><h3>Detalle de Movimientos</h3></hr>';
  
  imprimirHTML(_general+_separador1+_totales+_separador2+_detalle);

}

<?php } else { ?>

imprimir_pedido=function() {

  _general = $('tab_general_content').innerHTML;
  _totales = $('tab_totales_content').innerHTML;
  _detalle = $('tab_movimientos_content').innerHTML;
  _valores = $('tab_valores_content').innerHTML;
  
  _separador1 = '<hr><h3>Totales Generales</h3><hr>';
  _separador2 = '<hr><h3>Detalle de Movimientos</h3><hr>';
  _separador3 = '<hr><h3>Valorizaci&oacute;n del Pedido</h3><hr>';
  
  imprimirHTML(_general+_separador1+_totales+_separador2+_detalle+
                _separador3+_valores);

}

<?php }  ?>

imprimir_reposicion=function() {

  _general = $('tab_general_content').innerHTML;
  
  _separador2 = '<hr><h3>Detalle de Reposici&oacute;n</h3></hr>';
 
  _repo=$('tab_reposicion_content').innerHTML;
  
  _firmas='<br><br><table width="100%"><tr><td><center>____________________________<br>FIRMA Q.F.</center></td><td><center>____________________________<br>FIRMA FUNCIONARIO</center></td></table>';
  
  imprimirHTML(_general+_separador2+_repo+_firmas);
  

}

descargar_adq=function() {

  window.open('xlsgen.php?pedido_nro=<?php echo $pedido_a[1]?>');

}

</script>

<body class="fuente_por_defecto popup_background">

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_totales' style='cursor: pointer;'
      onClick='ver_total();'>
      <img src='iconos/page_white_magnify.png'>
      Totales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_movimientos' style='cursor: pointer;'
      onClick='ver_movs();'>
      <img src='iconos/page_white_find.png'>
      Detalle</div>
		  </td>
      
<?php if($pedido_a[8]==NULL) { ?>
      
      <td>
		  <div class='tabs_fade' id='tab_valores' style='cursor: pointer;'
      onClick='ver_vals();'>
      <img src='iconos/money.png'>
      Valores</div>
		  </td>
		  
<?php } 

      if($repo) {

?>      

      <td>
		  <div class='tabs_fade' id='tab_reposicion' style='cursor: pointer;'
      onClick='ver_repo();'>
      <img src='iconos/arrow_refresh.png'>
      Reposici&oacute;n</div>
		  </td>


<?php } ?>
      
      </tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:<?php echo $wh?>px;'>


<table style='width:100%;'>

<tr>
<td style='text-align: right;'>N&uacute;mero de Pedido:</td>
<td><b><?php echo $pedido_a[1]; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Fecha de Emisi&oacute;n:</td>
<td><b><?php echo $pedido_a[2]; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Funcionario Emisor:</td>
<td><?php echo htmlentities($pedido_a[5]); ?></td>
</tr>
<tr>
<td style='text-align: right;'>Lugar de Or&iacute;gen:</td>
<td><?php echo htmlentities($pedido_a[3]); ?></td>
</tr>
<tr>
<td style='text-align: right;'>Lugar de Destino:</td>
<td><?php echo htmlentities($pedido_a[4]); ?></td>
</tr>

<?php echo $ordenes_html; ?>

<tr>
<td style='text-align: right;'>Estado:</td>
<td><b><?php echo $pedido_a[6]; ?></b></td>
</tr>

<tr>
<td style='text-align: right;'>Validez:</td>
<td>
<?php 
if($pedido_a[9]=='t') $auth='CHECKED'; else $auth='';
if(_func_permitido(29,$pedido_a[10]) OR
   _func_permitido_cc(29,$pedido_a[14])) 
  $enable=''; else $enable='DISABLED';
     
print('
<INPUT TYPE="checkbox" NAME="" ID="" 
onChange="autorizar_pedido(this);"
'.$auth.' '.$enable.'>');

?> Autorizado</td>
</tr>


<?php if($pedido_a[9]=='t') { ?>

<tr id='autorizado_por'>
<td style='text-align: right;'>Autorizado Por:</td>
<td id='autorizado_por_campo'><?php echo htmlentities($pedido_a[12]); ?></td>
</tr>
<tr id='autorizado_fecha'>
<td style='text-align: right;'>Fecha Autorizaci&oacute;n:</td>
<td id='autorizado_fecha_campo'><?php echo htmlentities($pedido_a[13]); ?></td>
</tr>

<?php } else { ?>

<tr id='autorizado_por' style='display: none;'>
<td style='text-align: right;'>Autorizado Por:</td>
<td id='autorizado_por_campo'></td>
</tr>
<tr id='autorizado_fecha' style='display: none;'>
<td style='text-align: right;'>Fecha Autorizaci&oacute;n:</td>
<td id='autorizado_fecha_campo'></td>
</tr>



<?php } 
	
	if($pedido_a[6]=='Enviado'){
 		 if(_func_permitido(29,$pedido_a[10]) OR _func_permitido_cc(29,$pedido_a[14])) {

?>

<tr>
<td colspan=2>

<center>
<br>

<table style='width:200px;background-color:#cccccc;'>
<tr><td style='text-align:center;'>Acciones</td></tr>
<tr><td>
<center>
    <a href='#' onClick='modificar_pedido();' class='boton2'>
    Modificar Pedido... </a>
</center>
</td></tr>
</table>

</center>

</td>
</tr>

<?php } }?>


</table>

</div>

<div id='tab_totales_content' 
class='tabbed_content' style='overflow:auto;height:<?php echo $wh?>px;display:none;'>

<?php echo $totales_html; ?>

</div>


<div id='tab_movimientos_content' 
class='tabbed_content' style='overflow:auto;height:<?php echo $wh?>px;display:none;'>

<?php echo $detalles_html; ?>

</div>

<div id='tab_valores_content' 
class='tabbed_content' style='overflow:auto;height:<?php echo $wh?>px;display:none;'>

<?php echo $valores_html; ?>

</div>

<div id='tab_reposicion_content' 
class='tabbed_content' style='overflow:auto;height:<?php echo $wh?>px;display:none;'>

<?php echo $repo_html; ?>

</div>

</td>
</tr>
</table>

<center>

    <div class='boton' id='imprimir_pedido_btn'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_pedido();'>
		Imprimir Pedido...</a>
		</td></tr></table>
		</div>

    <div class='boton' id='imprimir_reposicion_btn' style='display:none;'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_reposicion();'>
		Imprimir Reposici&oacute;n...</a>
		</td></tr></table>
		</div>
<?php if($pedido_a[8]==NULL) { ?>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/page_excel.png'>
		</td><td>
		<a href='#' onClick='descargar_adq();'>
		Descargar Adquisici&oacute;n...</a>
		</td></tr></table>
		</div>
<?php } ?>



</center>

</body></html>

<?php

  }
  
  
  if(isset($_GET['log_id'])) {
  
    $log_id = ($_GET['log_id']*1);
  
    $movimiento = pg_query($conn, "
    SELECT foo.*, bod_glosa FROM
    (
    SELECT
    date_trunc('second', log_fecha) AS log_fecha,
    log_tipo,
    func_nombre,
    (SELECT stock_bod_id FROM stock WHERE stock_log_id=logs.log_id LIMIT 1) 
    AS bodega_id,
    centro_nombre,
    instsol_desc,
    log_comentario
    FROM logs 
    JOIN funcionario ON log_func_if=func_id
    LEFT JOIN cargo_centro_costo USING (log_id)
    LEFT JOIN centro_costo USING (centro_ruta)
    LEFT JOIN cargo_instsol ON logs.log_id=cargo_instsol.log_id
    LEFT JOIN institucion_solicita ON 
              cargo_instsol.instsol_id=institucion_solicita.instsol_id
    WHERE logs.log_id=$log_id
    ) AS foo
    JOIN bodega ON bod_id=bodega_id
    ");
    
    $_movimiento = pg_fetch_row($movimiento);
    
    if($_movimiento[1]==6) $prestamo=true;
    else                   $prestamo=false;
    
    switch($_movimiento[1]) {
          case 0:
          case 1: $_movimiento[1]='Ingreso desde Proveedor.'; break;
          case 2: $_movimiento[1]='Traslado de Productos.'; break;
          case 4: $_movimiento[1]='Ingreso por Excedente.'; break;
          case 5: $_movimiento[1]='Ingreso por Donaci&oacute;n.'; break;
          case 6: $_movimiento[1]='Pr&eacute;stamo/Devoluci&oacute;n de Art&iacute;culos.'; break;
          case 7: $_movimiento[1]='Baja por Vencimiento.'; break;
          case 8: $_movimiento[1]='Dado de Baja.'; break;
          case 9: $_movimiento[1]='Gasto por Receta.'; break;
          case 10: $_movimiento[1]='Utilizado en Farmacia Magistral.'; break;
          case 15: $_movimiento[1]='Despacho a Servicio.'; break;
          case 16: $_movimiento[1]='Devoluci&oacute;n desde Servicio.'; break;
          case 20: $_movimiento[1]='Inicio de Control por Sistema.'; break;
          case 30: $_movimiento[1]='Ajuste de Saldos.'; break;
          
        }
        
    
    $detalle_movimiento = pg_query($conn, "
    SELECT
    art_codigo,
    art_glosa,
    stock_vence,
    stock_cant
    FROM 
    stock
    JOIN articulo ON stock_art_id=art_id
    WHERE stock_log_id=".$log_id."
    ");
    
    
    $detalles_html='
    <table width=100% style="font-size: 11px;">
    <tr><td style="text-align: right; font-weight: bold;">Fecha:</td>
    <td colspan=3><b>'.$_movimiento[0].'</b></td></tr>
    <tr><td style="text-align: right; font-weight: bold;">Ubicaci&oacute;n:</td>
    <td colspan=3>'.htmlentities($_movimiento[7]).'</td></tr>
    <tr><td style="text-align: right; font-weight: bold;">Funcionario:</td>
    <td colspan=3>'.htmlentities($_movimiento[2]).'</td></tr>
    <tr class="tabla_header" style="font-weight: bold;">
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Fecha de Venc.</td>
    <td>Cantidad</td>
    </tr>
    ';
  
    for($i=0;$i<pg_num_rows($detalle_movimiento);$i++) {
  
    $movimiento_a = pg_fetch_row($detalle_movimiento);
    
    ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
    
    $detalles_html.="
    <tr class='".$clase."'>
    <td style='text-align: right;'>".$movimiento_a[0]."</td>
    <td>".htmlentities($movimiento_a[1])."</td>
    <td style='text-align: center;'>".$movimiento_a[2]."</td>
    <td style='text-align: right;'>".number_format($movimiento_a[3],1,',','.')."</td>
    </tr>
    ";
  
   }
   
   $detalles_html.='</table>';
?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_movimientos');

}

ver_movs=function() {

  tab_down('tab_general');
  tab_up('tab_movimientos');

}

imprimir_movimiento=function() {
    _encabezado = '<table><td><hr></hr></td></table>'
    _general = $('tab_general_content').innerHTML;
    _detalle = $('tab_movimientos_content').innerHTML;
  
    _separador2 = '<hr><h3>Detalle de Movimientos</h3></hr>';
  
    _recepcion_conforme='<br><br><br><br><center><table><tr><td style="text-align: right;">________________________<br>Firma Recepci&oacute;n Conforme</td></tr></table></center>';
  
  imprimirHTML(_encabezado+_general+_separador2+_detalle+_recepcion_conforme);

}

</script>


<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_movimientos' style='cursor: pointer;'
      onClick='ver_movs();'>
      <img src='iconos/page_white_find.png'>
      Detalle</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>


<table width=100% style="font-size: 12px;">
  
<tr>
<td style='text-align: right; width:150px;'>N&uacute;mero Ident.:</td>
<td style='font-size: 20px;'><b><?php echo $log_id; ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>Fecha:</td>
<td><?php echo $_movimiento[0]; ?></td>
</tr>
<tr>
<td style='text-align: right;'>Tipo de Operaci&oacute;n:</td>
<td><b><?php echo $_movimiento[1]; ?></b></td>
</tr>
<tr>
<td style='text-align: right;'>Ubicaci&oacute;n:</td>
<td><b><?php echo htmlentities($_movimiento[7]); ?></b></td>
</tr>

<?php if($prestamo) { ?>
<tr>
<td style='text-align: right;'>Instituci&oacute;n:</td>
<td><b><?php echo htmlentities($_movimiento[5]); ?></b></td>
</tr>
<?php } ?>

<?php

  if($_movimiento[4]!='')
  print("
  <tr>
  <td style='text-align: right;'>Centro de Costo/Servicio:</td>
  <td><b>".htmlentities($_movimiento[4])."</b></td>
  </tr>
  ");


?>

<tr>
<td style='text-align: right;'>Funcionario:</td>
<td><?php echo htmlentities($_movimiento[2]); ?></td>
</tr>

<tr>
<td style='text-align: right;'>Comentarios:</td>
<td>

<?php 

  if($_movimiento[6]) 
    echo htmlentities($_movimiento[6]);
  else
    echo '<i>No hay comentarios.</i>'; 
  
?>

</td>
</tr>


</table>

</div>

<div id='tab_movimientos_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $detalles_html; ?>

</div>


</td>
</tr>
</table>

<center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_movimiento();'>
		Imprimir Movimiento...</a>
		</td></tr></table>
		</div>
</center>

<?php
  
  }

  
  if(isset($_GET['orden_id']) or isset($_GET['orden_numero'])) {
  
    if(isset($_GET['orden_id'])) {
      $orden_id = ($_GET['orden_id'])*1;
    
      $cabecera = cargar_registros_obj("
      SELECT orden_compra.*, func_nombre, prov_rut, prov_glosa,
      date_trunc('second', orden_fecha) AS orden_fecha,
      orden_observacion, orden_iva, prov_fono, prov_ciudad, prov_direccion,prov_mail,
      orden_estado_portal, orden_licitacion
      FROM orden_compra
      JOIN proveedor ON orden_prov_id=prov_id
      JOIN funcionario ON orden_func_id=func_id
      WHERE orden_id=".$orden_id."
      ");

    } else {
      $orden_nro = pg_escape_string($_GET['orden_numero']);
    
      $cabecera = cargar_registros_obj("
      SELECT orden_compra.*, func_nombre, prov_rut, prov_glosa,
      date_trunc('second', orden_fecha) AS orden_fecha, 
      orden_observacion, orden_iva, orden_id, prov_fono, prov_ciudad, prov_direccion,prov_mail,
      orden_estado_portal,orden_fecha_entrega,orden_licitacion
      FROM orden_compra
      JOIN proveedor ON orden_prov_id=prov_id
      JOIN funcionario ON orden_func_id=func_id
      WHERE orden_numero='".$orden_nro."'
      ");
    
    }
    
    $dorden = cargar_registros_obj("
    SELECT 
    COALESCE(ordetalle_cant, 1) AS ordetalle_cant, 
    ordetalle_subtotal, 
    art_codigo, art_glosa, item_glosa FROM
    orden_detalle
    JOIN articulo ON ordetalle_art_id=art_id
    LEFT JOIN item_presupuestario ON art_item=item_codigo
    WHERE ordetalle_orden_id=".$cabecera[0]['orden_id']."
    ");
 
    $sorden = cargar_registros_obj("
    SELECT 
    orserv_subtotal, 
    orserv_glosa, COALESCE(orserv_cant,0)AS orserv_cant, item_glosa
    FROM
    orden_servicios
    LEFT JOIN item_presupuestario ON orserv_item=item_codigo
    WHERE orserv_orden_id=".$cabecera[0]['orden_id']."
    ");
    
    
    $detalles_html='
    <table width=100% style="font-size: 11px;">
    <tr class="tabla_header" style="font-weight: bold;">
    <td>Codigo Int.</td>
    <td>Glosa</td>
    <td>Item Presupuestario</td>
    <td>Cantidad</td>
    <td>P. Unit.</td>
    <td>Subtotal</td>
    </tr>
    ';
    
    $total=0;
  
    if($dorden) {
      for($i=0;$i<count($dorden);$i++) {
    
      ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
      
      if($dorden[$i]['ordetalle_cant']==0)
        $dorden[$i]['ordetalle_cant']=1;
      
      $detalles_html.="
      <tr class='".$clase."'>
      <td style='text-align: right;'>".$dorden[$i]['art_codigo']."</td>
      <td>".htmlentities($dorden[$i]['art_glosa'])."</td>
      <td>".htmlentities($dorden[$i]['item_glosa'])."</td>
      <td style='text-align: right;'>".$dorden[$i]['ordetalle_cant']."</td>
      <td style='text-align: right;'>$".number_format($dorden[$i]['ordetalle_subtotal']/$dorden[$i]['ordetalle_cant'],1,',','.').".-</td>
      <td style='text-align: right;'>
      $".number_format($dorden[$i]['ordetalle_subtotal'],1,',','.').".-
      </td>
      </tr>
      ";
      
      $total+=($dorden[$i]['ordetalle_subtotal']*1);
      
      }
    }
 
    if($sorden) {
      for($i=0;$i<count($sorden);$i++) {
    
      ($i%2==0)     ?   $clase='tabla_fila'   :  $clase='tabla_fila2';
      
      $detalles_html.="
      <tr class='".$clase."'>
      <td style='text-align: right;'>(n/a)</td>
      <td>".htmlentities($sorden[$i]['orserv_glosa'])."</td>
      <td>".htmlentities($sorden[$i]['item_glosa'])."</td>
      <td style='text-align:right;'>
      ".number_format($sorden[$i]['orserv_cant'],1,',','.')."</td>
      <td style='text-align: right;'>
      $".number_format($sorden[$i]['orserv_subtotal']/$sorden[$i]['orserv_cant'],1,',','.').".-
      </td>
      <td style='text-align: right;'>
      $".number_format($sorden[$i]['orserv_subtotal'],1,',','.').".-
      </td>
      </tr>
      ";
      
      $total+=($sorden[$i]['orserv_subtotal']*1);
      
      }
    }
  
   
   $totalciva=$total*$cabecera[0]['orden_iva'];
   $iva=$totalciva-$total;
   
   $detalles_html.='
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>Neto:</td>
   <td>$'.number_format($total,1,',','.').'.-</td>
   </tr>
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>I.V.A.:</td>
   <td>$'.number_format($iva,1,',','.').'.-</td>
   </tr>
   <tr class="tabla_header" style="text-align:right;">
   <td colspan=5>Total:</td>
   <td>$'.number_format($totalciva,1,',','.').'.-</td>
   </tr>
   </table>';
   
   $pedidos_asoc = cargar_registros_obj(
   "
   SELECT pedido_nro FROM orden_pedido 
   JOIN pedido ON orden_pedido.pedido_id=pedido.pedido_id
   WHERE orden_id=".$cabecera[0]['orden_id']
   );
   
   
   if($pedidos_asoc) {
   
     $pedidos_html='';
     $num=count($pedidos_asoc);
     $pedido_x='';
     for($i=0;$i<$num;$i++) {
     
        $pedidos_html.='<span class="texto_tooltip"
          onClick="abrir_pedido('.$pedidos_asoc[$i]['pedido_nro'].');">';
        $pedidos_html.=$pedidos_asoc[$i]['pedido_nro'];
        $pedido_x=$pedido_x."/ ".$pedidos_asoc[$i]['pedido_nro'];
        $pedidos_html.='</span>';
        if($i<($num-1)) $pedidos_html.='<br>';
     
     }
   
   } else {
   
    $pedidos_html='<i>No hay pedidos asociados.</i>';
    $pedido_x='<i>No hay pedidos asociados.<i>';
   
   }
   $orden_numero=$cabecera[0]['orden_numero'];
   
   $recep = cargar_registros_obj("
		SELECT *, date_trunc('second',log_fecha) AS fecha FROM documento 
		JOIN logs ON log_doc_id=doc_id
		WHERE doc_orden_desc='$orden_numero' OR doc_orden_id={$_GET['orden_id']}");
   if($recep) {
   
     $recepciones_html='';
     $num=count($recep);
     
     for($i=0;$i<$num;$i++) {
     
		switch($recep[$i]['doc_tipo']*1) {
			case 0: $tipo='Gu&iacute;a de Despacho'; break;
			case 1: $tipo='Factura'; break;
			case 2: $tipo='Boleta'; break;
			default: $tipo='Pedido'; break;
		}
     
        $recepciones_html.='<span class="texto_tooltip"
          onClick="abrir_recepcion('.$recep[$i]['doc_id'].');">';
        $recepciones_html.='<b>'.$tipo.' '.$recep[$i]['doc_num'].'</b> ('.$recep[$i]['fecha'].')';
        $recepciones_html.='</span>';
        if($i<($num-1)) $recepciones_html.='<br>';
     
     }
   
   } else {
   
    $recepciones_html='<i>No hay recepciones asociadas.</i>';
   
   }

  if($cabecera[0]['orden_observacion'])
  {
   $obser_x=htmlentities($cabecera[0]['orden_observacion']);
   $obser_x = preg_replace('/[\r?\n]+/', '---', $obser_x);
   $obser_x = str_replace("'", "\'", $obser_x);
  }
  else 
  {
    $obser_x='<i>No hay comentario</>';
  }
  if($cabecera[0]['orden_numero'])
  {
   $ordennumero_x=htmlentities($cabecera[0]['orden_numero']);

  }
  else
  {
    $ordennumero_x=htmlentities($cabecera[0]['orden_id']);
  }
$nombreprov=preg_replace('/[\r?\n]+/', '--', ($cabecera[0]['prov_glosa']));
$nombreprov=str_replace("'", "\'", $nombreprov);

$direccion=preg_replace('/[\r?\n]+/', '--', ($cabecera[0]['prov_direccion']));
$direccion=str_replace("'", "\'", $direccion);

$nombre_completo = split(" ",$cabecera[0]['func_nombre']);
  $iniciales='';
  for($i=0;$i<count($nombre_completo);$i++)
  {
      $iniciales= $iniciales."".substr(($nombre_completo[$i]),0,1);
  }
 $iniciales=preg_replace('/[\r?\n]+/', '--', $iniciales);
 $iniciales=str_replace("'", "\'", $iniciales);
 $iniciales=strtolower($iniciales);
?>
<html>
<title>&Oacute;rden de Compra</title>

<?php cabecera_popup('.'); ?>

<body class="popup_background fuente_por_defecto">

<script>
abrir_recepcion=function(doc_id) {
	window.open('visualizar.php?doc_id='+doc_id,'_self');
	//window.opener.abrir_recepcion(doc_id);
	window.opener.focus();
}
ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_movimientos');

}

ver_movs=function() {

  tab_down('tab_general');
  tab_up('tab_movimientos');

}

imprimir_movimiento_orden_compra=function() {
  _encabezado = '<table border=0 style=width:100%;>';
  _encabezado+='<tr>';
  _encabezado+='<td align=center style=width:25%;font-size:8px; rowspan=2 colspan=1></td>';
  _encabezado+='<td style=width:50%;></td>';
  _encabezado+='<td></td>';
  _encabezado+='</tr>';
  _encabezado+='<tr>';
  _encabezado+='<td><br></td>';
  _encabezado+='<td style=font-size:9px;><font face="Arial Black">FECHA: &nbsp;&nbsp;<b><?php echo $cabecera[0]['orden_fecha']; ?></b></font></td>';
  _encabezado+='</tr>';
  _encabezado+='</table>';
  _encabezado+='<hr><h3></h3></hr>';
  _encabezado+='<table  border=0 style=width:100%;>';
  _encabezado+='<tr>';
  _encabezado+='<td align=center style=font-size:8px;width:27%>';
  _encabezado+='<font face="Arial Black">';
  _encabezado+='MINISTERIO DE SALUD';
  _encabezado+='<br>SERV. DE SALUD VIÑA DEL MAR - QTA.';
  _encabezado+='HOSPITAL DE QUILLOTA';
  _encabezado+='<br>RUT: 61.606.608-0';
  _encabezado+='CONCEPCION 1050 - QUILLOTA';
  _encabezado+='</font>';
  _encabezado+='</td>';
  _encabezado+='<td align=center style=font-size:15px;><font face="Arial Black">ORDEN DE COMPRA</font></td>';
  _encabezado+='<td align=center style=font-size:12px;><font face="Arial Black">N&deg;:&nbsp;<b><?php echo $cabecera[0]['orden_id']; ?></b></font></td>';
  _encabezado+='</tr>';
  _encabezado+='</table><br>';
  _encabezado+='<hr><h3></h3></hr>';
  _encabezado+='<table  border=0 style=width:100%;>';
  _encabezado+='<tr>';
  _encabezado+='<td align=center style=font-size:12px;><font face="Arial Black">N&deg;:&nbsp;<b><?php echo $ordennumero_x; ?></b></font></td>';
  _encabezado+='<td align=center style=font-size:12px;><font face="Arial Black">N&deg; PEDIDO:&nbsp;<b></font><font face="Arial"><?php echo $pedido_x; ?></font></b></td>';
  _encabezado+='<td align=center style=font-size:12px;><font face="Arial Black">EMITIDO POR:&nbsp;<b></font><font face="Arial">MEVL/<i><?php echo $iniciales; ?></i></font></b></td>';
  _encabezado+='</table><br>';
  _encabezado+='<table border=0 style=width:100%;>';
  _encabezado+='<tr>';
  _encabezado+='<td style="width:15%;font-size:11px;"><font face="Arial">RUT:&nbsp;&nbsp;<b><?php echo htmlentities($cabecera[0]['prov_rut']); ?></b></font></td>';
  _encabezado+='<td style=font-size:11px;><font face="Arial">SEÑORES:&nbsp;&nbsp;&nbsp;<b><?php echo htmlentities($nombreprov); ?></b></font></td>';
  _encabezado+='<td style=font-size:11px;><font face="Arial">FONO:&nbsp;&nbsp;<b><?php echo htmlentities($cabecera[0]['prov_fono']); ?></b></font></td>';
  _encabezado+='</tr>';
  _encabezado+='</table>';
  _encabezado+='<table border=0 style=width:100%;>';
  _encabezado+='<tr>';
  _encabezado+='<td style="width:50%;font-size:11px;"><font face="Arial">DIRECCION: &nbsp;&nbsp;<b><?php echo htmlentities($direccion); ?></b></font></td>';
  _encabezado+='<td style=font-size:11px;><font face="Arial">CIUDAD:&nbsp;&nbsp;<b><?php echo htmlentities($cabecera[0]['prov_ciudad']); ?></b></font></td>';
  _encabezado+='</tr>';
  _encabezado+='</table>';
  _encabezado+='<hr><h3></h3></hr>';
  
  _detalle = $('tab_movimientos_content').innerHTML;
  _observaciones = '<br><table style=width:100%><td style=font-size:12px;><font face="Arial">Observaciones:&nbsp;&nbsp;<?php echo $obser_x; ?></font></td></table>';
  _recepcion_conforme='<br><br><br><br><table style="width:100%;font-size:11px;"><tr><td style="text-align:center;">________________________<br>JEFE ABASTECIMIENTO</td>';
  _recepcion_conforme+='<td style="text-align:center;font-size:11px;">________________________<br>SDA</td>';
  _recepcion_conforme+='</tr>';
  _recepcion_conforme+='<tr>';
  _recepcion_conforme+='<td style="text-align:center;font-size:11px;" colspan=2 >________________________<br>JEFE FINANZAS</td>';
  _recepcion_conforme+='</tr>';
  _recepcion_conforme+='</table>';
  
  imprimirHTML(_encabezado+_detalle+_observaciones+_recepcion_conforme);

}

</script>


<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_movimientos' style='cursor: pointer;'
      onClick='ver_movs();'>
      <img src='iconos/page_white_find.png'>
      Detalle</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>
<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>


<table width=100% style="font-size: 12px;">
  
<tr>
<td style='text-align: right; width:150px;'>C&oacute;digo Interno:</td>
<td style='font-size: 20px;'><b><?php echo $cabecera[0]['orden_id'] ?></b></td>
</tr>
<tr>
<td style='text-align: right; width:150px;'>C&oacute;digo de &Oacute;rden:</td>
<td style='font-size:20px;'><b><?php echo $cabecera[0]['orden_numero'] ?></b></td>
</tr>


<?php if($cabecera[0]['orden_licitacion']!='') { ?>
<tr>
<td style='text-align: right; width:150px;'>C&oacute;digo Licitaci&oacute;n:</td>
<td style='font-size:16px;'><b><?php echo $cabecera[0]['orden_licitacion'] ?></b></td>
</tr>
<?php } ?>




<tr>
<td style='text-align: right; width:150px;'>Fecha Emisi&oacute;n:</td>
<td><?php echo $cabecera[0]['orden_fecha']; ?></td>
</tr>
<tr>
<td style='text-align: right;'>RUT Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_rut']); ?>
</b></td>
</tr>

<tr>
<td style='text-align: right;'>Nombre Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_glosa']); ?>
</b></td>
</tr>
<tr>
<td style='text-align: right;'>Fono Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_fono']); ?>
</b></td>
</tr>
<tr>
<td style='text-align: right;'>E-mail Proveedor:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_mail']); ?>
</b></td>
</tr>
<tr>
<td style='text-align: right;'>Comuna:</td>
<td><b><?php echo htmlentities($cabecera[0]['prov_ciudad']); ?>
</b></td>
</tr>
<tr>
<td style='text-align: right;'>Estado Portal:</td>
<td><b><?php echo $cabecera[0]['orden_estado_portal']; ?>
</b></td>
</tr>
<tr>
<td style='text-align: right;'>Fecha Entrega:</td>
<td><b><?php echo $cabecera[0]['orden_fecha_entrega']; ?>
</b></td>
</tr>

<tr>
<td style='text-align: right;'>Nro(s). de Pedido:</td>
<td><b><?php echo $pedidos_html; ?>
</b></td>
</tr>

<tr>
<td style='text-align: right;'>Recepciones:</td>
<td><b><?php echo $recepciones_html; ?>
</b></td>
</tr>

<!----
<tr>
<td style='text-align: right;'>Funcionario:</td>
<td><?php echo htmlentities($_movimiento[2]); ?></td>
</tr>
---->

<tr>
<td style='text-align: right;'>Observaciones:</td>
<td>

<?php 
  if($cabecera[0]['orden_observacion'])
  {
    echo htmlentities($cabecera[0]['orden_observacion']);
  }
  else
  {
    echo '<i>No hay comentarios.</i>';
  }
?>

</td>
</tr>



</table>

</div>

<div id='tab_movimientos_content' 
class='tabbed_content' style='overflow:auto;height:315px;display:none;'>

<?php echo $detalles_html; ?>

</div>


</td>
</tr>
</table>

<center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_movimiento_orden_compra();'>
		Imprimir &Oacute;rden de Compra...</a>
		</td></tr></table>
		</div>
</center>

</body>
</html>

<?php
  
  }
  
  if(isset($_GET['equipo_id']) ) {
  
    $equipo_id=$_GET['equipo_id']*1;
    $equipo=cargar_registro("
      SELECT 
      *, 
      equipo_fecha_ingreso::date AS equipo_fecha_ingreso, 
      extract(
        day from ((equipo_fecha_fabricacion + (equipo_vida_estandar || ' year')::interval)::date + (equipo_vida_extendida || ' year')::interval)::date - now()       
      ) AS vida_residual 
      
      FROM equipos_medicos 
      JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
      WHERE
      equipo_id=$equipo_id
    ");
    
    if($equipo['equipo_foto']!='')
      $equipo['foto']='fotos/'.$equipo['equipo_foto'];
    else
      $equipo['foto']='imagenes/sin_fotografia.jpg';
    
    $centro=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta'])."'
    ");
    
    $centro2=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta2'])."'
    ");
    
    $prov=cargar_registro("
      SELECT prov_rut, prov_glosa FROM proveedor WHERE
      prov_id=".($equipo['equipo_prov_id']*1)."
    ");
    
    switch($equipo['equipo_estado']) {
      case 0: $estado='En uso normal.'; break;
      case 1: $estado='En mantenci&oacute;n preventiva..'; break;
      case 2: $estado='En mantenci&oacute;n correctiva.'; break;
      case 3: $estado='En garant&iacute;a.'; break;
    }
    
    if($equipo['equipo_estado']==2) {
      $eot=cargar_registro("
      SELECT * FROM equipo_orden_trabajo WHERE eot_equipo_id=$equipo_id
      ");
    }
    
    $fing=$equipo['equipo_fecha_ingreso'];
    switch($equipo['equipo_preventiva']) {
      case 0: $intervalo=30; break;
      case 1: $intervalo=60; break;
      case 2: $intervalo=90; break;
      case 3: $intervalo=183; break;
      case 4: $intervalo=365; break;
    
    }
    
    $chtml='<table style="width:100%;font-size:12px;">';
    $chtml.='<tr class="tabla_header"><td style="width:100px;">Fecha</td><td>Estado</td><td style="width:50px;">O.T. Asignada</td></tr>';
    
    
    $fing=split('/', $equipo['equipo_fecha_ingreso']);
    $ing=mktime(0,0,0,$fing[1],$fing[0],$fing[2]);
    $fin=mktime(0,0,0,$fing[1],$fing[0]+$equipo['vida_residual'],$fing[2]);
    
    $now=mktime(0,0,0,date('m'),date('d'),date('Y'));
    
    $chtml.='<tr class="tabla_fila" style="font-weight:bold;"><td style="text-align:center;">'.$equipo['equipo_fecha_fabricacion'].'</td><td>Fecha de Fabricaci&oacute;n</td><td>(n/a)</td></tr>';
    $chtml.='<tr class="tabla_fila2" style="font-weight:bold;"><td style="text-align:center;">'.$equipo['equipo_fecha_ingreso'].'</td><td>Fecha de Ingreso</td><td>(n/a)</td></tr>';
    
    $agenda=cargar_registros_obj("
      SELECT * FROM equipo_agenda_preventiva 
      WHERE equipo_id=$equipo_id
      ORDER BY eagenda_fecha;
    ");
    
    $np=$equipo['equipo_mant_prov']*1;
    
    if($agenda)
    for($i=0;$i<count($agenda);$i++) {
      ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
      if($agenda[$i]['eot_id']==0) {

        $fcmp=explode('/',$agenda[$i]['eagenda_fecha']);
        $fc=mktime(0,0,0,$fcmp[1],$fcmp[0],$fcmp[2]);
        
        if(--$np>0) $pr='<i>(Proveedor)</i>'; else $pr='';
        
        if($fc<=$now)
          $chtml.='<tr class="'.$clase.'" style="background-color:#FFAAAA;"><td style="text-align:center;">'.$agenda[$i]['eagenda_fecha'].'</td><td>Atrasado '.$pr.'</td><td>(n/a)</td></tr>';
        else
          $chtml.='<tr class="'.$clase.'"><td style="text-align:center;">'.$agenda[$i]['eagenda_fecha'].'</td><td>Agendado '.$pr.'</td><td>(n/a)</td></tr>';

      } else
      
        $chtml.='<tr class="'.$clase.'"><td style="text-align:center;">'.$agenda[$i]['eagenda_fecha'].'</td><td>Realizado</td><td>'.$agenda[$i]['eot_id'].'</td></tr>';
    
    }
    
    $chtml.='<tr class="tabla_fila2" style="font-weight:bold;"><td style="text-align:center;">'.date('d/m/Y', $fin).'</td><td>Fin de Vida &Uacute;til</td><td>(n/a)</td></tr>';
    
    $chtml.='</table>';
    
    list($fprox) = cargar_registros_obj("
      SELECT 
        eagenda_fecha
      FROM equipo_agenda_preventiva
      WHERE equipo_id=$equipo_id AND eagenda_fecha>now() 
      ORDER BY eagenda_fecha;
    ");
    
    $fp=explode('/',$fprox['eagenda_fecha']);
    $fpp=mktime(0,0,0,$fp[1],$fp[0],$fp[2]);
    
?>

<html>
<title>Visualizar Equipo M&eacute;dico</title>

<?php cabecera_popup('.'); ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_calendario');
  tab_down('tab_historial');

}

ver_calendario=function() {

  tab_down('tab_general');
  tab_up('tab_calendario');
  tab_down('tab_historial');

}

ver_historial=function() {

  tab_down('tab_general');
  tab_down('tab_calendario');
  tab_up('tab_historial');

}

solicitar_mantencion = function() {

  window.open("equipos/solicitar_mantencion/form.php?equipo_id=<?php echo $equipo_id; ?>", "_self");

}

abrir_eot = function(eot_id) {

  window.open("visualizar.php?eot_id="+eot_id, "_self");

}

</script>

<body class="fuente_por_defecto popup_background">

<form id='equipo' name='equipo' onsubmit='return false;'>

<input type='hidden' id='equipo_id' name='equipo_id' value=<?php echo $equipo_id; ?>>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_calendario' style='cursor: pointer;'
      onClick='ver_calendario();'>
      <img src='iconos/calendar_view_day.png'>
      Calendario de Mant.</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
      onClick='ver_historial();'>
      <img src='iconos/clock.png'>
      Historial</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>

<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>


<table style='width:100%;font-size:12px;'>

<tr>
<td colspan=2>
<center>
<img src='<?php echo $equipo['foto']; ?>' id='foto' name='foto' 
style='width:200px;height:150px;border:1px solid black;'>
</center>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Clasificaci&oacute;n:
</td>
<td style='font-weight:bold;'>
<?php echo htmlentities($equipo['eclase_nombre']); ?>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Marca:
</td>
<td style='font-weight:bold;'>
<?php echo htmlentities($equipo['equipo_marca']); ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Modelo:
</td>
<td style='font-weight:bold;'>
<?php echo htmlentities($equipo['equipo_modelo']); ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Estado de Adquisici&oacute;n:
</td>
<td>
<?php if($equipo['equipo_nuevo']=='t') echo 'Nuevo'; else echo 'Usado'; ?>
</td>
</tr>

<tr>
<td style='text-align: right;'>RUT Proveedor:</td>
<td colspan=3>
<?php echo $prov['prov_rut']; ?>
</td>
<tr>
<td style='text-align: right;'>Nombre Proveedor:</td>
<td colspan=3>
<?php echo htmlentities($prov['prov_glosa']); ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Serie:
</td>
<td>
<?php echo $equipo['equipo_serie']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Inventario:
</td>
<td>
<?php echo $equipo['equipo_inventario']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Centro de Costo/Servicio (Propietario):
</td>
<td>
<?php echo htmlentities($centro['centro_nombre']); ?>
</td>
</tr>

<?php if($centro2) { ?>
<tr>
<td style='text-align:right;'>
Centro de Costo/Servicio (Ubicaci&oacute;n):
</td>
<td>
<?php echo htmlentities($centro2['centro_nombre']); ?>
</td>
</tr>
<?php } ?>

<tr>
<td style='text-align:right;'>
Garant&iacute;a:
</td>
<td>
<?php echo $equipo['equipo_garantia']; ?>&nbsp;
<?php if($equipo['equipo_garantia_medida']==0) echo 'meses.'; ?>
<?php if($equipo['equipo_garantia_medida']==1) echo 'a&ntilde;os.'; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Periodicidad Mantenci&oacute;n Preventiva:
</td>
<td>
<?php if($equipo['equipo_preventiva']==0) echo 'Mensual'; ?>
<?php if($equipo['equipo_preventiva']==1) echo 'Bimensual'; ?>
<?php if($equipo['equipo_preventiva']==2) echo 'Trimestral'; ?>
<?php if($equipo['equipo_preventiva']==3) echo 'Semestral'; ?>
<?php if($equipo['equipo_preventiva']==4) echo 'Anual'; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Mantenciones Prev. Proveedor:
</td>
<td>
<?php echo $equipo['equipo_mant_prov']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Fecha de Fabricaci&oacute;n:
</td>
<td>
<?php echo $equipo['equipo_fecha_fabricacion']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Fecha de Pr&oacute;xima Mant. Prev.:
</td>
<td>
<?php 

$diff=($fpp-$now)/86400;

if($diff<10) $color='color:orange';
elseif($diff<30) $color='color:red';
else $color='';

echo '<span style="'.$color.'">'.date('d/m/Y', $fpp); 

?>
 <i>(<?php 
  echo number_format($diff);
?> d&iacute;as m&aacute;s)</i></span>
</td>
</tr>


<tr>
<td style='text-align:right;' valign='top'>
Vida &Uacute;til:
</td>
<td style=''>
<table style='font-size:12px;'>
<tr>
<td>Est&aacute;ndar</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_estandar']; ?></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Extendida</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_extendida']; ?></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Total</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_estandar']+$equipo['equipo_vida_extendida']; ?></b></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Residual</td><td style='text-align:right;'><?php echo number_format($equipo['vida_residual']/365, 2, ',', '.'); ?></b></td><td>a&ntilde;o(s).</td>
</tr>
</table>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Estado Actual:
</td>
<td style='font-weight:bold;'>
<?php echo $estado; ?>
</td>
</tr>

<?php if($equipo['equipo_estado']!=0) { ?>
<tr>
<td style='text-align:right;'>
O.T. Vigente:
</td>
<td style='font-weight:bold;'>
<span style='cursor:pointer;color:blue;font-size:16px;' 
onClick='abrir_eot(<?php echo $eot['eot_id']; ?>);'>
#<?php echo $eot['eot_id']; ?>
</span>
</td>
</tr>
<?php } ?>

<?php if($equipo['equipo_estado']==0) { ?>

<tr>
<td colspan=2>
<center>
<br>
<table style='width:200px;background-color:#cccccc;'>
<tr><td style='text-align:center;'>Acciones</td></tr>
<tr><td>
<center>
    <a href='#' onClick='solicitar_mantencion();' class='boton2'>
    Solicitar Mantenci&oacute;n Correctiva...</a>
		
</center>
</td></tr>
</table>
</center>
</td>
</tr>
<?php } ?>



</table>

</div>


<div id='tab_calendario_content' class='tabbed_content' 
style='display:none;height:315px;overflow:auto;'>

  <?php echo $chtml; ?>

</div>

<div id='tab_historial_content' class='tabbed_content'
style='display:none;height:315px;overflow:auto;'>

</div>

</td>
</tr>


</table>

</center>

</td></tr></table>


    <center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_equipo();'>
		
    Imprimir Hoja del Equipo...
    
    </a>
		</td></tr></table>
		</div>
    </center>
  
</form>
  
<?php
  
  }
  
  
    if(isset($_GET['eot_id']) ) {
    
    $eot_id=$_GET['eot_id']*1;
    
    $eot=cargar_registro("
      SELECT * FROM equipo_orden_trabajo WHERE eot_id=$eot_id
    ");
  
    $equipo_id=$eot['eot_equipo_id']*1;
    
    $equipo=cargar_registro("
      SELECT 
      *, 
      equipo_fecha_ingreso::date AS equipo_fecha_ingreso, 
      extract(
        day from ((equipo_fecha_fabricacion + (equipo_vida_estandar || ' year')::interval)::date + (equipo_vida_extendida || ' year')::interval)::date - now()       
      ) AS vida_residual 
      
      FROM equipos_medicos 
      JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
      WHERE
      equipo_id=$equipo_id
    ");
    
    if($equipo['equipo_foto']!='')
      $equipo['foto']='fotos/'.$equipo['equipo_foto'];
    else
      $equipo['foto']='imagenes/sin_fotografia.jpg';
    
    $centro=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta'])."'
    ");
    
    $centro2=cargar_registro("
      SELECT centro_nombre FROM centro_costo WHERE 
      centro_ruta='".pg_escape_string($equipo['equipo_centro_ruta2'])."'
    ");

    $func=cargar_registro("
      SELECT func_nombre FROM funcionario WHERE 
      func_id=".($eot['eot_func_id']*1)."
    ");
    
    $prov=cargar_registro("
      SELECT prov_rut, prov_glosa FROM proveedor WHERE
      prov_id=".($equipo['equipo_prov_id']*1)."
    ");
    
    switch($eot['eot_estado']) {
      case -1: $estado='Esperando Asignaci&oacute;n de T&eacute;cnico'; break;
      case 0: $estado='Esperando Recepci&oacute;n en U.E.M.'; break;
      case 1: $estado='Recepcionado en U.E.M.'; break;
      case 2: $estado='Trabajo Iniciado'; break;
      case 3: $estado='Trabajo T&eacute;rminado'; break;
      case 4: $estado='Entregado al Servicio'; break;
      case 5: $estado='Recepci&oacute;n Conforme del Servicio'; break;
      case 10: $estado='Enviado a Garant&iacute;a del Proveedor'; break;
    }
    
    switch($eot['eot_tipo']) {
      case 0: $tipo='Mantenci&oacute;n Correctiva'; break;
      case 1: $tipo='Mantenci&oacute;n Preventiva'; break;      
    }
    
    $fing=$equipo['equipo_fecha_ingreso'];

if(!isset($_GET['nowin'])) {
    
?>

<html>
<title>Visualizar &Oacute;rden de Trabajo de Equipo M&eacute;dico</title>

<?php 

cabecera_popup('.'); 
     
} ?>

<script>

ver_general=function() {

  tab_up('tab_general');
  tab_down('tab_trabajo');
  tab_down('tab_historial');

}

ver_trabajo=function() {

  tab_down('tab_general');
  tab_up('tab_trabajo');
  tab_down('tab_historial');

}

ver_historial=function() {

  tab_down('tab_general');
  tab_down('tab_trabajo');
  tab_up('tab_historial');

}

<?php if(!isset($_GET['nowin'])) { ?>

ver_equipo=function(equipo_id) {


  window.open("visualizar.php?equipo_id="+equipo_id,"_self");

}

<?php } else { ?>

ver_equipo = function (equipo_id) {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open('visualizar.php?equipo_id='+equipo_id, 'ver_eot',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

}

<?php } ?>

</script>

<?php if(!isset($_GET['nowin'])) { ?>

<body class="fuente_por_defecto popup_background">

<?php } ?>

<form id='equipo' name='equipo' onsubmit='return false;'>

<input type='hidden' id='equipo_id' name='equipo_id' value=<?php echo $equipo_id; ?>>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
<td>

      <table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_general' style='cursor: default;' 
      onClick='ver_general();'>
      <img src='iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_trabajo' style='cursor: pointer;'
      onClick='ver_trabajo();'>
      <img src='iconos/wrench.png'>
      Trabajo Realizado</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
      onClick='ver_historial();'>
      <img src='iconos/clock.png'>
      Historial</div>
		  </td></tr>
      </table>


</td>
</tr>
<tr>
<td>

<div id='tab_general_content' 
class='tabbed_content' style='overflow:auto;height:315px;'>

<table style='width:100%;font-size:12px;'>

<tr>
<td style='text-align:right;width:150px;'>
Nro. O.T.:
</td>
<td style='font-weight:bold;font-size:16px;'>
<?php echo $eot['eot_id']; ?>
</td>
<td rowspan=3 style="text-align:right;width:100px;">
<img src='<?php echo $equipo['foto']; ?>' id='foto' name='foto' 
style='width:100px;height:75px;border:1px solid black;'>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Ingreso de Solicitud:
</td>
<td style='font-weight:bold;font-size:14px;'>
<?php 
  $fecha=explode(' ',$eot['eot_fecha_ing']);
  echo $fecha[0].' '.substr($fecha[1],0,5); 
?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Tipo de Trabajo:
</td>
<td style='font-weight:bold;font-size:14px;'>
<?php echo $tipo; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Funcionario Solicitante:
</td>
<td style="font-weight:bold;" colspan=2>
<?php 
  if($func['func_nombre']!='')
    echo htmlentities($func['func_nombre']);
   else
    echo '<i>(n/a)</i>'; 
?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Clasificaci&oacute;n:
</td>
<td style='font-weight:bold;' colspan=2>
<?php echo htmlentities($equipo['eclase_nombre']); ?>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Marca:
</td>
<td style='font-weight:bold;' colspan=2>
<?php echo htmlentities($equipo['equipo_marca']); ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Modelo:
</td>
<td style='font-weight:bold;' colspan=2>
<?php echo htmlentities($equipo['equipo_modelo']); ?>
</td>
</tr>

<tr>
<td style='text-align: right;'>RUT Proveedor:</td>
<td colspan=2>
<?php echo $prov['prov_rut']; ?>
</td>
<tr>
<td style='text-align: right;'>Nombre Proveedor:</td>
<td colspan=2>
<?php echo htmlentities($prov['prov_glosa']); ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Serie:
</td>
<td>
<?php echo $equipo['equipo_serie']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Nro. de Inventario:
</td>
<td>
<?php echo $equipo['equipo_inventario']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Centro de Costo/Servicio (Propietario):
</td>
<td colspan=2>
<?php echo htmlentities($centro['centro_nombre']); ?>
</td>
</tr>


<?php if($centro2) { ?>
<tr>
<td style='text-align:right;'>
Centro de Costo/Servicio (Ubicaci&oacute;n):
</td>
<td colspan=2>
<?php echo htmlentities($centro2['centro_nombre']); ?>
</td>
</tr>
<?php } ?>

<tr>
<td style='text-align:right;'>
Garant&iacute;a:
</td>
<td colspan=2>
<?php echo $equipo['equipo_garantia']; ?>&nbsp;
<?php if($equipo['equipo_garantia_medida']==0) echo 'meses.'; ?>
<?php if($equipo['equipo_garantia_medida']==1) echo 'a&ntilde;os.'; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Periodicidad Mantenci&oacute;n Preventiva:
</td>
<td colspan=2>
<?php if($equipo['equipo_preventiva']==0) echo 'Mensual'; ?>
<?php if($equipo['equipo_preventiva']==1) echo 'Bimensual'; ?>
<?php if($equipo['equipo_preventiva']==2) echo 'Trimestral'; ?>
<?php if($equipo['equipo_preventiva']==3) echo 'Semestral'; ?>
<?php if($equipo['equipo_preventiva']==4) echo 'Anual'; ?>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Fecha de Fabricaci&oacute;n:
</td>
<td colspan=2>
<?php echo $equipo['equipo_fecha_fabricacion']; ?>
</td>
</tr>

<tr>
<td style='text-align:right;' valign='top'>
Vida &Uacute;til:
</td>
<td style='' colspan=2>
<table style='font-size:12px;'>
<tr>
<td>Est&aacute;ndar</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_estandar']; ?></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Extendida</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_extendida']; ?></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Total</td><td style='text-align:right;'><?php echo $equipo['equipo_vida_estandar']+$equipo['equipo_vida_extendida']; ?></b></td><td>a&ntilde;o(s).</td>
</tr>
<tr>
<td>Residual</td><td style='text-align:right;'><?php echo number_format($equipo['vida_residual']/365, 2, ',', '.'); ?></b></td><td>a&ntilde;o(s).</td>
</tr>
</table>
</td>
</tr>


<tr>
<td style='text-align:right;'>
Estado Actual de O.T.:
</td>
<td style='font-weight:bold;' colspan=2>
<?php echo $estado; ?>
</td>
</tr>
<tr>
<td colspan=3>
<center>
<br>
<table style='width:200px;background-color:#cccccc;'>
<tr><td style='text-align:center;'>Acciones</td></tr>
<tr><td>
<center>
    <a href='#' onClick='ver_equipo(<? echo $equipo_id; ?>);' class='boton2'>
    Ver Hoja del Equipo...</a>
		
</center>
</td></tr>
</table>
</center>
</td>
</tr>


</table>


</div>

<div id='tab_trabajo_content' 
class='tabbed_content' style='display:none;overflow:auto;height:315px;'>

</div>


<div id='tab_historial_content' 
class='tabbed_content' style='display:none;overflow:auto;height:315px;'>

<?php 

  $e=cargar_registros_obj("
    SELECT * FROM equipo_orden_evento WHERE eot_id=$eot_id
  ");

  print("
  <table style='width:100%;font-size:12px;'>
  <tr><td style='text-align:right;width:90px;' class='tabla_fila2'>Fecha:</td>
  <td class='tabla_fila'>".$eot['eot_fecha_ing']."</td></tr>
  <tr><td style='text-align:right;' class='tabla_fila2'>Estado:</td>
  <td class='tabla_fila' style='font-weight:bold;'>Ingreso de Solicitud</td></tr>");
  
  if($eot['eot_observaciones']!='')
    print("<tr><td style='text-align:right;' class='tabla_fila2' valign='top'>Observaciones:</td>
    <td class='tabla_fila'>".htmlentities($eot['eot_observaciones'])."</td></tr>");
  
  print("</table>");

  if($e)
    for($i=0;$i<count($e);$i++) {
    
      $pf=explode(' ',$e[$i]['eevento_fecha']);
      $fec=$pf[0].' '.substr($pf[1],0,5);
      
      if($e[$i]['eevento_tipodoc']==0)
      switch($e[$i]['eevento_estado2']) {
          case -1: $estado='Esperando Asignaci&oacute;n de T&eacute;cnico'; break;
          case 0: $estado='Esperando Recepci&oacute;n en U.E.M.'; break;
          case 1: $estado='Recepcionado en U.E.M.'; break;
          case 2: $estado='Trabajo Iniciado'; break;
          case 3: $estado='Trabajo T&eacute;rminado'; break;
          case 4: $estado='Entregado al Servicio'; break;
          case 5: $estado='Recepci&oacute;n Conforme del Servicio'; break;
          case 10: $estado='Enviado a Garant&iacute;a del Proveedor'; break;
      }
      else
      switch($e[$i]['eevento_tipodoc']) {
          case 1: $estado='Correspondencia'; break;
          case 2: $estado='Orden de Salida'; break;
          case 3: $estado='Orden de Compra'; break;
          case 4: $estado='Factura/Boleta'; break;
      }
      
      $eevento_id=$e[$i]['eevento_id'];
      
      $docs=cargar_registros_obj("
        SELECT * FROM equipo_orden_doc WHERE eevento_id=$eevento_id
      ");
    
      print("
      <table style='width:100%;font-size:12px;'>
      <tr><td style='text-align:right;width:90px;' class='tabla_fila2'>Fecha:</td>
      <td class='tabla_fila'>".$fec."</td></tr>
      <tr><td style='text-align:right;' class='tabla_fila2'>");
      
      if($e[$i]['eevento_tipodoc']==0)
        print('Estado'); else print('Documento');
      
      print(":</td>
      <td class='tabla_fila' style='font-weight:bold;'>".$estado."</td></tr>");
      
      if($e[$i]['eevento_observaciones']!='')
        print("<tr><td style='text-align:right;' class='tabla_fila2' valign='top'>Observaciones:</td>
        <td class='tabla_fila'>".htmlentities($e[$i]['eevento_observaciones'])."</td></tr>");
        
      if($docs) {
        print("<tr><td style='text-align:right;' class='tabla_fila2' valign='top'>Adjunto(s):</td>
        <td class='tabla_fila'>
        <table style='width:100%;font-size:11px;'>
        <tr class='tabla_header'>
        <td>Archivo</td>
        <td>Tama&ntilde;o</td>
        </tr>");
        
        for($j=0;$j<count($docs);$j++) {
          $clase=($j%2==0)?'tabla_fila':'tabla_fila2';
          $archivo=htmlentities($docs[$j]['edoc_archivo']);
          $tamano=number_format((filesize('equipos/adjuntos/'.$archivo)/1024),'1','.',',').' KB';
          $url_archivo='equipos/adjuntos/'.($archivo);
          print("<tr class='$clase'><td>");
          print("<a href='$url_archivo' target='adjuntos'>$archivo</a></td><td style='text-align:right;'><i>$tamano</i></td></tr>");
        }
        
        print("</table></td></tr>");
      }
      
      
      print("</table>");
    
    }

?>

</div>

    <center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_eot();'>
		
    Imprimir &Oacute;rden de Trabajo...
    
    </a>
		</td></tr></table>
		</div>
    </center>
  
</form>

<?php if(!isset($_GET['nowin'])) { ?>

</body>
</html>

<?php

  }

  }

?>
