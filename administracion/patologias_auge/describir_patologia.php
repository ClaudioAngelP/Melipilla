<?php

  require_once('../../conectar_db.php');
  
  if(!isset($params)) {
    $pat_id=$_POST['pat_id']*1;
    $etapa=$_POST['etapa']*1;
    $params=false;
    $ruta_iconos='';
  } else {
    $ruta_iconos='../../';
  }
  
  $listado = cargar_registros_obj("
    SELECT * 
    FROM detalle_patauge 
    LEFT JOIN codigos_prestacion ON codigo = presta_codigo 
    WHERE pat_id=$pat_id
  ");
  
  function img($t) {
    GLOBAL $ruta_iconos;
    
    if($t=='t') return '<center><img src="'.$ruta_iconos.'iconos/tick.png"></center>';
    else return '<center><img src="'.$ruta_iconos.'iconos/cross.png"></center>';
  }
  
  function dibujar_fila($n,$l) {
  
    GLOBAL $listado;
    GLOBAL $params;
    
    if($params) {
      GLOBAL $etapa, $detpat_id, $detpat_padre_id, $ruta_iconos;
    }
  
    if($l%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
  
    switch($listado[$n]['detpat_uplazo']) {
      case 0: $up='min.'; break;
      case 1: $up='hr.'; break;
      case 2: $up='d&iacute;a(s)'; break;
      case 3: $up='mes(es)'; break;
      case -1: case -2: $up='';
    }
  
    if($listado[$n]['presta_codigo']=='') {
      $listado[$n]['detpat_plazo']='No Aplicable'; $up='';
    } else if($listado[$n]['detpat_uplazo']=='-2') 
      $listado[$n]['detpat_plazo']='Sin Plazo';
    else if($listado[$n]['detpat_plazo']=='0') 
      $listado[$n]['detpat_plazo']='*';
  
    if($listado[$n]['presta_codigo']!='')
      $formato="font-size:10px;";
    else
      $formato="font-size:12px;color:green;font-weight:bold;";
  
    print('<tr class="'.$clase.'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';">
    <td>
    <table>
    <tr>'.str_repeat('<td style="width:15px;">&nbsp;</td>',$l).'
    <td>
    '.$listado[$n]['presta_codigo'].'
    </td><td style="'.$formato.'">
    '.htmlentities(strtoupper($listado[$n]['glosa'])).'
    </td></tr>
    </table>
    </td>
    <td><center>'.$listado[$n]['detpat_plazo'].' '.$up.'</center></td>
    <td>'.img($listado[$n]['detpat_ipd']).'</td>
    <td>'.img($listado[$n]['detpat_sigges']).'</td>
    ');
    
    if(!$params) {
      print('
      <td>
      <center>
      <img src="iconos/add.png" style="cursor:pointer;"
      onClick="agregar_nivel('.$listado[$n]['detpat_id'].', 0);">
      </center>
      </td>
      <td>
      <center>
      <img src="iconos/pencil.png" style="cursor:pointer;"
      onClick="agregar_nivel(0, '.$listado[$n]['detpat_id'].');">
      </center>
      </td>
      <td>
      <center>
      <img src="iconos/delete.png" style="cursor:pointer;"
      onClick="eliminar_nivel('.$listado[$n]['detpat_id'].');">
      </center>
      </td>');
      
      $propagar=true;
      
    } else {
      
      if($listado[$n]['detpat_id']!=$detpat_id AND 
          $listado[$n]['detpat_id']!=$detpat_padre_id) {
      
        print('
        <td>
        <center>
        <img src="../../iconos/database_refresh.png" style="cursor:pointer;"
        onClick="mover_nivel('.$listado[$n]['detpat_id'].', '.$etapa.');">
        </center>
        </td>
        ');
        
        $propagar=true;
        
      } else {
        
        print('<td><center><img src="'.$ruta_iconos.'iconos/cross.png"></center></td>');
        
        if($listado[$n]['detpat_id']==$detpat_padre_id) $propagar=true;
        else $propagar=false;
      
      }
      
    }
    
    print('</tr>');
    
    if($propagar)
    for($i=0;$i<count($listado);$i++) {
      if($listado[$i]['detpat_padre_id']==$listado[$n]['detpat_id'])
        dibujar_fila($i,$l+1);
    }

  }
  
?>

<?php if(!$params) { ?>
<table style='width:100%;'>
<?php } else { ?>
<table style='width:100%;font-size:11px;'>
<?php } ?>

<tr class='tabla_header'>
<td>C&oacute;d. Prestaci&oacute;n</td>
<td>Plazo</td>
<td>I.P.D.</td>
<td>SIGGES</td>
<?php if(!$params) { ?>
<td>Agregar</td>
<td>Editar</td>
<td>Eliminar</td>
<?php } else { ?>
<td>Traer Ac&aacute;</td>
<?php } ?>
</tr>

<?php if(!$params) { ?>
<tr class='tabla_fila2'
onMouseOver='this.className="mouse_over";'
onMouseOut='this.className="tabla_fila2";'>
<td>
<b>Agregar Prestaci&oacute;n de Entrada</b>
</td>
<td colspan=3>&nbsp;</td>
<td>
<center>
<img src='iconos/add.png' style='cursor:pointer;'
onClick='agregar_nivel(0, 0);'>
</td>
<td colspan=2>&nbsp;</td>
</tr>
<?php } ?>

<?php  
  
  if(!$params) {
    if($listado)
    for($i=0;$i<count($listado);$i++) {
      if($listado[$i]['detpat_padre_id']==0 AND $listado[$i]['detpat_etapa']==$etapa)
        dibujar_fila($i,0);
    }
  } else {
    for($etapa=0;$etapa<4;$etapa++) {
    
      print('<tr class="mouse_over"><td colspan=4 style="text-align:center;font-weight:bold;
              font-size:14px;">Etapa de ');
      switch($etapa) 
      {
        case 0: echo 'Sospecha'; break;
        case 1: echo 'Diagn&oacute;stico'; break;
        case 2: echo 'Tratamiento'; break;
        default: echo 'Seguimiento'; break;
      }
      
      print('</td><td>
      <center>
      <img src="../../iconos/database_refresh.png" style="cursor:pointer;"
      onClick="mover_nivel(0, '.$etapa.');">
      </center>
      </td></tr>');
    
      if($listado)
      for($i=0;$i<count($listado);$i++) {
        if($listado[$i]['detpat_padre_id']==0 AND $listado[$i]['detpat_etapa']==$etapa)
          dibujar_fila($i,0);
      }
      
    }

  }

?>

</table>