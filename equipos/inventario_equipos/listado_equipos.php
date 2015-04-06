<?php

  require_once('../../conectar_db.php');
  
  $centro_ruta=pg_escape_string($_POST['centro_ruta']);
  $clasificacion=(utf8_decode($_POST['clasificacion']))*1;
  $marca=pg_escape_string(utf8_decode($_POST['marca']));
  $modelo=pg_escape_string(utf8_decode($_POST['modelo']));
  
  if($centro_ruta!='')
    $centroq="equipo_centro_ruta='$centro_ruta'";
  else
    $centroq="true";

  
  if($clasificacion!=-1)
    $clasificaq="equipo_eclase_id=$clasificacion";
  else
    $clasificaq="true";

  if($marca!=-1)
    $marcaq="equipo_marca='$marca'";
  else
    $marcaq="true";

  if($modelo!=-1)
    $modeloq="equipo_modelo='$modelo'";
  else
    $modeloq="true";
    
  $lista=cargar_registros_obj("
    SELECT * FROM equipos_medicos 
    JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
    WHERE 
    $centroq AND $clasificaq AND $marcaq AND $modeloq
  ");

?>

<table style='width:100%;'>

<tr class='tabla_header'>
<td>Clasificaci&oacute;n</td>
<td>Marca</td>
<td>Modelo</td>
<td>Nro. Serie</td>
<td>Nro. Inv.</td>
<td>Im&aacute;gen</td>
<td>Estado</td>
</tr>

<?php

  if($lista)
  for($i=0;$i<count($lista);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
  
    if($lista[$i]['equipo_foto']=='') {
      $lista[$i]['equipo_foto']='imagenes/sin_fotografia.jpg';
    } else {
      $lista[$i]['equipo_foto']='fotos/'.htmlentities($lista[$i]['equipo_foto']); 
    }
    
    switch($lista[$i]['equipo_estado']) {
      case 0: $estado='En uso normal.'; break;
      case 1: $estado='En mant. preventiva..'; break;
      case 2: $estado='En mant. correctiva.'; break;
      case 3: $estado='En garant&iacute;a.'; break;
    }
  
  
    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"$clase\";'
    onClick='abrir_equipo(".$lista[$i]['equipo_id'].");'>
    <td>".htmlentities($lista[$i]['eclase_nombre'])."</td>
    <td>".htmlentities($lista[$i]['equipo_marca'])."</td>
    <td>".htmlentities($lista[$i]['equipo_modelo'])."</td>
    <td>".htmlentities($lista[$i]['equipo_serie'])."</td>
    <td>".htmlentities($lista[$i]['equipo_inventario'])."</td>
    <td>
    <center>
    <img src='".$lista[$i]['equipo_foto']."'
    style='width:60px;height:45px;border:1px solid black;'>
    </center>
    </td>
    <td>".$estado."</td>    
    </tr>
    ");
  
  
  }

?>


</table>
