<?php

  require_once('../conectar_db.php');

    $eot_id=$_GET['eot_id']*1;
    
    $eot=cargar_registro("
      SELECT *, date_trunc('second', eot_fecha_ing) AS eot_fecha_ing 
      FROM equipo_orden_trabajo WHERE eot_id=$eot_id
    ");
    
    $t=cargar_registros_obj("
        SELECT * FROM equipo_orden_tecnico 
        JOIN tecnico USING (tec_id)
        WHERE eot_id=$eot_id
    ");
  
    $equipo_id=$eot['eot_equipo_id']*1;
    
    if(isset($_GET['nowin'])) {
      $ruta=''; $ruta_adj='equipos/'; 
    } else {
      $ruta='../'; $ruta_adj='';
    }
    
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

    if($equipo['equipo_accesorios']!='') {
      $b=explode(',', $eot['eot_accesorios']);
      $a=explode(',', $equipo['equipo_accesorios']);
    
      $ahtml='';
    
      for($i=0;$i<count($a);$i++) {
        $fnd=false; $a[$i]=trim($a[$i]);
        for($j=0;$j<count($b);$j++)
          if($a[$i]==trim($b[$j])) $fnd=true;
          
        $ahtml.='<img style="width:8px;height:8px;" src="'.$ruta.'iconos/'.($fnd?'tick.png':'cross.png').'"> '.htmlentities($a[$i]).'<br>';
      }
      
    } else $ahtml='';

    if($eot['eot_estado_equipo']!='') {
    
      $estados=cargar_registros_obj('SELECT * FROM eot_estado_equipo WHERE eoteseq_id IN ('.$eot['eot_estado_equipo'].');');
    
    if($estados) {
    
      $ehtml='';
      
      for($i=0;$i<count($estados);$i++) {
        $ehtml.='<li>'.htmlentities($estados[$i]['eoteseq_desc']).'</li>';
      }
      
      $ehtml.='';
    
    } else $ehtml='';
    
    } else $ehtml='';
    
    $techtml='';

    if($t)    
    for($i=0;$i<count($t);$i++) { 
    
        $techtml.='<li>'.htmlentities($t[$i]['tec_nombre']).'</li>';
    
    }
    


if(!isset($_GET['nowin'])) {
    
?>

<html>
<title>Visualizar &Oacute;rden de Trabajo de Equipo M&eacute;dico</title>

<?php 

cabecera_popup('..'); 
     
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


  window.open("visualizar_equipo.php?equipo_id="+equipo_id,"_self");

}

<?php } else { ?>

ver_equipo = function (equipo_id) {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open('equipos/visualizar_equipo.php?equipo_id='+equipo_id, 'ver_eot',
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
      <img src='<?php echo $ruta; ?>iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_trabajo' style='cursor: pointer;'
      onClick='ver_trabajo();'>
      <img src='<?php echo $ruta; ?>iconos/wrench.png'>
      Trabajo Realizado</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
      onClick='ver_historial();'>
      <img src='<?php echo $ruta; ?>iconos/clock.png'>
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
<img src='<?php echo $ruta; ?><?php echo $equipo['foto']; ?>' id='foto' name='foto' 
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

<?php if($ehtml!='') { ?>
<tr>
<td style='text-align:right;' valign='top'>
Estado General:
</td>
<td colspan=2>
<?php echo $ehtml; ?>
</td>
</tr>

<?php } ?>


<?php if($ahtml!='') { ?>
<tr>
<td style='text-align:right;' valign='top'>
Accesorios Inclu&iacute;dos:
</td>
<td colspan=2>
<?php echo $ahtml; ?>
</td>
</tr>

<?php } ?>


<?php if($eot['eot_observaciones']) { ?>
<tr>
<td style='text-align:right;'>
Observaciones:
</td>
<td>
<?php echo htmlentities($eot['eot_observaciones']); ?>
</td>
</tr>
<?php } ?>




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

<?php if($techtml!='') { ?>
<tr>
<td style='text-align:right;' valign='top'>
T&eacute;cnicos Asignados:
</td>
<td colspan=2>
<?php echo $techtml; ?>
</td>
</tr>
<?php } ?>


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
    <a href='#' onClick='ver_equipo(<?php echo $equipo_id; ?>);' class='boton2'>
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
    SELECT * FROM equipo_orden_evento 
    LEFT JOIN tecnico ON eevento_func_id=tec_id
    LEFT JOIN funcionario ON eevento_func_id=func_id
    WHERE eot_id=$eot_id
  ");

  print("
  <table style='width:100%;font-size:12px;'>
  <tr><td style='text-align:right;width:120px;' class='tabla_fila2'>Fecha:</td>
  <td class='tabla_fila'>".$eot['eot_fecha_ing']."</td></tr>
    <tr><td style='text-align:right;' class='tabla_fila2'>
    Funcionario:</td><td class='tabla_fila'>".htmlentities($func['func_nombre'])."</td></tr>
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
          case 5: $estado='Cotizaci&oacute;n'; break;
      }
      
      $eevento_id=$e[$i]['eevento_id'];
      
      $docs=cargar_registros_obj("
        SELECT * FROM equipo_orden_doc WHERE eevento_id=$eevento_id
      ");
    
        if($e[$i]['tec_nombre']!='') {
            $nom=$e[$i]['tec_nombre'];
        } else {
            $nom=$e[$i]['func_nombre'];
        }
    
      print("
      <table style='width:100%;font-size:12px;'>
      <tr><td style='text-align:right;width:120px;' class='tabla_fila2'>Fecha:</td>
      <td class='tabla_fila'>".$fec."</td></tr>
        <tr><td style='text-align:right;' class='tabla_fila2'>
        T&eacute;cnico/Funcionario:</td><td class='tabla_fila'>".htmlentities($nom)."</td></tr>
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
          $url_archivo=$ruta_adj.'adjuntos/'.($archivo);
          $tamano=number_format((@filesize($url_archivo)/1024),'1','.',',').' KB';
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
		<img src='<?php echo $ruta; ?>iconos/printer.png'>
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

?>
