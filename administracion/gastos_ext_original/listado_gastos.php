<?php

  require_once('../../conectar_db.php');

?>

<table style='width: 100%;' cellspacing=0>

<tr class='tabla_header' style='font-weight: bold;'>

<td style='width: 70%;'>Nombre</td>
<td>Total</td>
<td>Unidad</td>
</tr>

<?php 

$gastos = pg_query("SELECT * FROM gasto_externo ORDER BY gastoext_nombre");

$num = pg_num_rows($gastos);

for($i=0;$i<$num;$i++) {

  $gasto = pg_fetch_row($gastos);

  ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
  print("
  <tr class='".$clase."' 
  onMouseOver='this.className=\"mouse_over\";'
  onMouseOut='this.className=\"".$clase."\";'
  onClick='cargar_gasto(".$gasto[0].");'>
  <td style='text-align: left; font-weight: bold;'>
  ".htmlentities($gasto[1])."</td>
  <td style='text-align: right;'>".$gasto[3]."</td>
  <td style='text-align: left;'>".$gasto[2]."</td>
  </tr>
  ");

}

?>

</table>
