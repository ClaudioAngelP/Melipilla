<?php

  require_once('../../conectar_db.php');
  
  $lista = cargar_registros_obj("
  SELECT 
  pat_id, pat_glosa,
  to_char(pat_fecha_inicio, 'DD/MM/YYYY') AS pat_fecha_inicio,
  to_char(pat_fecha_final, 'DD/MM/YYYY') AS pat_fecha_final
  FROM patologias_auge ORDER BY pat_glosa
  ");

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td rowspan=2>Descripci&oacute;n</td>
<td colspan=2>Vigencia</td></tr>
<tr class='tabla_header'>
<td>Fecha Inicio</td>
<td>Fecha T&eacute;rmino</td>
</tr>

<?php 

  for($i=0;$i<count($lista);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"".$clase."\"'
    onClick='abrir_patologia(".$lista[$i]['pat_id'].");'>
    <td>".htmlentities($lista[$i]['pat_glosa'])."</td>
    <td style='text-align:center;'>".$lista[$i]['pat_fecha_inicio']."</td>
    <td style='text-align:center;'>".$lista[$i]['pat_fecha_final']."</td>
    </tr>
    ");
  
  }

?>

</table>
