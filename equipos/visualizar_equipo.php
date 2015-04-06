<?php

  require_once('../conectar_db.php');
  
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
      SELECT * FROM equipo_orden_trabajo 
      WHERE eot_equipo_id=$equipo_id AND NOT eot_estado=5
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
    

    if($equipo['equipo_accesorios']!='') {
      $a=explode(',', $equipo['equipo_accesorios']);
    
      $ahtml='';
    
      for($i=0;$i<count($a);$i++) {
        $ahtml.='<li>'.htmlentities(trim($a[$i])).'</li>';
      }
      
    } else $ahtml='';
    
    $eots=cargar_registros_obj("
      SELECT *, date_trunc('second', eot_fecha_ing) AS eot_fecha_ing 
      FROM equipo_orden_trabajo
      WHERE eot_equipo_id=$equipo_id ORDER BY eot_id DESC
    ");
    
    $historial='';
    
    if($eots)
    for($i=0;$i<count($eots);$i++) {
    
       $historial.="<table style='width:100%;'>
                    <tr><td class='tabla_header' 
                    style='width:150px;text-align:right;'>Orden de Trabajo:</td>
                    <td class='tabla_fila'>
                    <span style='font-weight:bold;cursor:pointer;color:blue;font-size:16px;' 
                    onClick='abrir_eot(".$eots[$i]['eot_id'].");'>
                    #".$eots[$i]['eot_id']."
                    </span>
                    </td></tr>
                    <tr><td class='tabla_header' style='text-align:right;'>Fecha Ingreso:</td>
                    <td class='tabla_fila'>".$eots[$i]['eot_fecha_ing']."</td></tr>
                    <tr><td class='tabla_header' 
                    colspan=2>Res&uacute;men de Eventos</td></tr>
                    ";
       
       $e=cargar_registros_obj("
        SELECT *, date_trunc('second', eevento_fecha) AS eevento_fecha 
        FROM equipo_orden_evento
        WHERE eot_id=".$eots[$i]['eot_id']." ORDER BY equipo_orden_evento.eevento_fecha
       ");
       
       if($e)
       for($j=0;$j<count($e);$j++) {
       
        $historial.="<tr><td class='tabla_header' 
                      style='text-align:right;'>Fecha:</td>
                      <td>".$e[$j]['eevento_fecha']."</td>";
        $historial.="<tr><td class='tabla_header' 
                      style='text-align:right;'>Evento:</td>
                      <td>".$e[$j]['eevento_estado2']."</td>";
        $historial.="<tr><td class='tabla_header' 
                      style='text-align:right;'>Observaciones:</td>
                      <td>".$e[$j]['eevento_observaciones']."</td>";
       
       }
       else {
       
        $historial.='<tr><td colspan=2 style="text-align:center;"><i>(Sin eventos asociados...)</i></td></tr>';
       
       }
                    
       $historial.='</table>';
    
    }

    
?>

<html>
<title>Visualizar Equipo M&eacute;dico</title>

<?php cabecera_popup('..'); ?>

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

solicitar_atencion = function() {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open("solicitar_atencion/form.php?equipo_id=<?php echo $equipo_id; ?>", 
                    'solicitar',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();
  window.close();
  
}

abrir_eot = function(eot_id) {

  window.open("visualizar_eot.php?eot_id="+eot_id, "_self");

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
      <img src='../iconos/page_white_database.png'>
      Datos Generales</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_calendario' style='cursor: pointer;'
      onClick='ver_calendario();'>
      <img src='../iconos/calendar_view_day.png'>
      Calendario de Mant.</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_historial' style='cursor: pointer;'
      onClick='ver_historial();'>
      <img src='../iconos/clock.png'>
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
<img src='../<?php echo $equipo['foto']; ?>' id='foto' name='foto' 
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
    <a href='#' onClick='solicitar_atencion();' class='boton2'>
    Solicitar Atenci&oacute;n U.E.M. ...</a>
		
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

  <?php echo $historial; ?>

</div>

</td>
</tr>


</table>

</center>

</td></tr></table>


    <center>
    <div class='boton'>
		<table><tr><td>
		<img src='../iconos/printer.png'>
		</td><td>
		<a href='#' onClick='imprimir_equipo();'>
		
    Imprimir Hoja del Equipo...
    
    </a>
		</td></tr></table>
		</div>
    </center>
  
</form>
