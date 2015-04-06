<?php 

  require_once('../../conectar_db.php');
  
  $pat_id = $_POST['pat_id']*1;
  $ep_id = $_POST['ep_id']*1;
  
  list($ep)=cargar_registros_obj("
    SELECT * FROM episodio_clinico WHERE ep_id=$ep_id
  ");
  
  $pac_id=$ep['ep_pac_id'];
  $fecha_ep=$ep['ep_fecha_inicio'];
  
  $listado = cargar_registros_obj("
    SELECT 
      detalle_patauge.*, 
      codigos_prestacion.*
          
    FROM detalle_patauge 
    
    JOIN codigos_prestacion ON codigo = detalle_patauge.presta_codigo 
      
    WHERE detalle_patauge.pat_id=$pat_id 
  ");
  
  $presta=cargar_registros_obj("
    SELECT 
    *,
    extract(epoch from (presta_fecha-'$fecha_ep')::interval)::bigint AS intervalo,     
    0 AS asigna FROM prestacion
    WHERE
      prestacion.presta_auge AND prestacion.pac_id=$pac_id AND
      prestacion.presta_fecha>'$fecha_ep'
    ORDER BY presta_fecha
  ");
  
  function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
  }
  
  function dibujar_fila($n,$l) {
  
    GLOBAL $listado, $presta;
  
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
    
    $fnd=false;
      
    for($j=0;$j<count($presta);$j++) {
      if((!$presta[$j]['asigna']) 
          AND ($presta[$j]['presta_codigo']==$listado[$n]['presta_codigo'])) {
        $listado[$n]['presta_fecha']=$presta[$j]['presta_fecha'];
        $listado[$n]['intervalo']=$presta[$j]['intervalo'];
        $presta[$j]['asigna']=1;
        $fnd=true; break;
      }
    }
        
    if($fnd) {
      $prestacion=$listado[$n]['presta_fecha'];
    } else {
      $prestacion='<i>(n/a)</i>';
    }
    
    if($fnd)
    switch($listado[$n]['intervalo']) {
      
      case $listado[$n]['intervalo']<=3600: 
        $lapso=number_format($listado[$n]['intervalo']/60,2,',','.').' m'; break;
      
      case ($listado[$n]['intervalo']>3600 and $listado[$n]['intervalo']<=86400): 
        $lapso=number_format($listado[$n]['intervalo']/3600,2,',','.').' h'; break;
      
      default: 
        $lapso=number_format($listado[$n]['intervalo']/86400,2,',','.').' d'; break;

    }
    else
      $lapso='<i>(n/a)</i>';
    
    print('<tr class="'.$clase.'" id="detpat_'.$listado[$n]['detpat_id'].'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';"
    style="cursor:pointer;">
    <td>
    <table style="font-size:10px;color:inherit;">
    <tr>'.str_repeat('<td style="width:15px;">&nbsp;</td>',$l).'
    <td style="font-size:10px;">
    '.$listado[$n]['presta_codigo'].'
    </td><td style="font-size:10px;">
    '.htmlentities(strtoupper($listado[$n]['glosa'])).'
    </td></tr>
    </table>
    </td>
    <td style="font-size:10px;"><center>
    '.$listado[$n]['detpat_plazo'].' '.$up.'</center></td>
    <td>'.img($listado[$n]['detpat_ipd']).'</td>
    <td>'.img($listado[$n]['detpat_sigges']).'</td>
    <td style="text-align:center;">'.($prestacion).'</td>
    <td style="text-align:center;">'.$lapso.'</td>
    </tr>');
  
    for($i=0;$i<count($listado);$i++) {
      if($listado[$i]['detpat_padre_id']==$listado[$n]['detpat_id'])
        dibujar_fila($i,$l+1);
    }

  }
  
?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>
<td>C&oacute;d. Prestaci&oacute;n</td>
<td>Plazo</td>
<td>I.P.D.</td>
<td>SIGGES</td>
<td>Fecha Prestaci&oacute;n</td>
<td>Lapso Transcurrido</td>
</tr>

<?php  
  
    for($etapa=0;$etapa<4;$etapa++) {
    
      print('<tr class="tabla_header"><td colspan=6 style="text-align:center;font-weight:bold;
              font-size:14px;">Etapa de ');
      switch($etapa) 
      {
        case 0: echo 'Sospecha'; break;
        case 1: echo 'Diagn&oacute;stico'; break;
        case 2: echo 'Tratamiento'; break;
        default: echo 'Seguimiento'; break;
      }
      
      print('</td></tr>');
    
      if($listado)
      for($i=0;$i<count($listado);$i++) {
        if($listado[$i]['detpat_padre_id']==0 AND $listado[$i]['detpat_etapa']==$etapa)
          dibujar_fila($i,0);
      }
      
    }

?>

</table>