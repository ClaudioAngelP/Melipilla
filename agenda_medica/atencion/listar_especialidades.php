<?php

  require_once('../../conectar_db.php');
  
  $fecha = $_GET['fecha'];
  
  $especialidades = cargar_registros(
    "SELECT DISTINCT esp_id, esp_desc FROM especialidades 
    JOIN cupos_atencion ON cupos_esp_id=esp_id
    WHERE cupos_fecha::date = '".pg_escape_string($fecha)."'
    ORDER BY esp_desc", true
  );
  
  if($especialidades) {
  
?>

<table style="width:100%;">

<?php 

  if($especialidades)
  for($i=0;$i<count($especialidades);$i++) {
  
    if(($i%2)==0) $clase='tabla_fila'; else $clase='tabla_fila2';
  
    print('
    <tr class="'.$clase.'" style="cursor: pointer;"
    onMouseOver="this.className=\'mouse_over\'"
    onMouseOut="this.className=\''.$clase.'\'" 
    onClick="cargar_medicos('.($especialidades[$i][0]*1).');">
    <td>'.($especialidades[$i][1]).'</td>
    </tr>
    ');
  
  }

?>

</table>

<?php 
  
  } else {
  
?>

  (No hay horas de atenci&oacute;n asignadas para esta fecha)

<?php 

  }

?>
