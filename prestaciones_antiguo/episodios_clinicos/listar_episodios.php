<?php

  require_once('../../conectar_db.php');
  
  $pac_id=$_POST['pac_id_0']*1;
  
  $lista = cargar_registros_obj("
    SELECT * FROM casos_auge 
    JOIN pacientes ON pac_id=ca_pac_id
    WHERE ca_pac_id=$pac_id
    ORDER BY ca_fecha DESC
  ");

?>

<table style='width:100%'>
<tr class='tabla_header'>
<td>Patolog&iacute;a GES</td>
<td>Etapa Actual</td>
<td>&Uacute;ltima Prestaci&oacute;n</td>
</tr>

<?php 

if($lista)
for($i=0;$i<count($lista);$i++) {

  if($i%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
  
    switch($lista[$i]['detpat_uplazo']) {
      case 0: $up='min.'; break;
      case 1: $up='hr.'; break;
      case 2: $up='d&iacute;a(s)'; break;
      case 3: $up='mes(es)'; break;
      
    }

  
  print('
  <tr class="'.$clase.'"
    onMouseOver="this.className=\'mouse_over\';"
    onMouseOut="this.className=\''.$clase.'\';"
    style="cursor:pointer;"
    onClick="abrir_episodio('.$lista[$i]['ep_id'].');">');

  if($lista[$i]['ep_estado']==0 OR $lista[$i]['ep_estado']==1) {
    switch($lista[$i]['detpat_etapa']) {
      case 3: $etapa='Seguimiento'; break;
      case 1: $etapa='Diagn&oacute;stico'; break;
      case 2: $etapa='Tratamiento'; break;
      default: $etapa='Sospecha'; break;
    }
    
    if($lista[$i]['ep_estado']!=0) {
      $etapa.='<BR>';
      $conf=$lista[$i]['epes_nombre'];
    }
    else $conf='';
      
  } else {
    
    $etapa='';
    $conf=$lista[$i]['epes_nombre'];
      
  }

  print("
  <td style='text-align:right;'>".$lista[$i]['pac_rut']."</td>
  <td>".htmlentities($lista[$i]['pac_appat'])."</td>
  <td>".htmlentities($lista[$i]['pac_apmat'])."</td>
  <td>".htmlentities($lista[$i]['pac_nombres'])."</td>
  <td>".htmlentities($lista[$i]['pat_glosa'])."</td>
  <td style='text-align:center;font-weight:bold;'>$etapa $conf</td>
  ");
  
  if($lista[$i]['codigo'])
    print("<td style='text-align:center;'>".$lista[$i]['codigo']."</td>");
  else
    print("<td>&nbsp;</td>");
  
  print("</tr>");

}

?>

</table>


