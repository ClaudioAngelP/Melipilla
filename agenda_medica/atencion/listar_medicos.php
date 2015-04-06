<?php

  require_once('../../conectar_db.php');
  
  $fecha = $_GET['fecha'];
  $esp_id = $_GET['esp_id']*1;
  
  $especialidades = cargar_registros(
    "SELECT DISTINCT
    doc_id, 
    doc_paterno || ' ' || doc_materno || ' ' || doc_nombres
    FROM doctores 
    JOIN cupos_atencion ON cupos_esp_id=$esp_id AND cupos_doc_id=doctores.doc_id
    JOIN especialidades ON especialidades.esp_id=cupos_atencion.cupos_esp_id 
    WHERE cupos_fecha::date = '".pg_escape_string($fecha)."'
    ORDER BY doc_paterno || ' ' || doc_materno || ' ' || doc_nombres", true
  );
  
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
    onClick="cargar_atenciones('.($especialidades[$i][0]*1).');">
    <td>'.htmlentities($especialidades[$i][1]).'</td>
    </tr>
    ');
  
  }

?>

</table>
