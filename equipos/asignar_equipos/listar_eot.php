<?php

  require_once('../../conectar_db.php');
  
  $estado=$_POST['estado']*1;
  
  if($estado==-1) {
    $preventivas="
      UNION
  
      SELECT  
        eagenda_id AS ot,
        1 AS tipo,
        eagenda_fecha AS ingreso_fecha,
        '00:00:00' AS ingreso_hora,
        '' AS nombre, -1 AS estado,
        equipo_medico_clase.*, equipos_medicos.*, 1
      FROM equipo_agenda_preventiva 
      LEFT JOIN equipos_medicos USING (equipo_id)
      LEFT JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
      WHERE (eagenda_fecha<=(CURRENT_TIMESTAMP+'3 days'::interval)) AND eot_id IS NULL

    ";
  } else {
    $preventivas='';
  }

  if($estado!=-2)
    $estadoq='eot_estado='.$estado;
  else
    $estadoq='true';
  
  $eots=cargar_registros_obj("
  
  SELECT * FROM (
  
  SELECT  
    eot_id AS ot,
    0 AS tipo,
    eot_fecha_ing::date AS ingreso_fecha,
    eot_fecha_ing::time AS ingreso_hora,
    func_nombre AS nombre, eot_estado AS estado,
    equipo_medico_clase.*, equipos_medicos.*, eot_tipo 
  FROM equipo_orden_trabajo 
  LEFT JOIN funcionario ON eot_func_id=func_id
  LEFT JOIN equipos_medicos ON eot_equipo_id=equipo_id
  LEFT JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
  WHERE $estadoq

  $preventivas  
  
  ) AS foo
  
  ORDER BY ingreso_fecha, ingreso_hora
  ");

?>

<table style="width:100%;">
<tr class="tabla_header">
<td rowspan=2># O.T.</td>
<td rowspan=2>Tipo</td>
<td rowspan=2>Fecha Solicitud</td>
<td rowspan=2>Funcionario</td>
<td colspan=4>Equipo Electrom&eacute;dico</td>
<td rowspan=2 colspan=2>Acciones</td>
</tr>
<tr class="tabla_header">
<td>Clasificaci&oacute;n</td>
<td>Marca</td>
<td>Modelo</td>
<td>Inventario</td>

</tr>

<?php 

  if($eots)
  for($i=0;$i<count($eots);$i++) {
  
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    print("<tr class='$clase'
            onMouseOver='this.className=\"mouse_over\";'
            onMouseOut='this.className=\"$clase\";'>");    
    
    switch($eots[$i]['eot_tipo']) {
      case 0: $tipo='Correctiva'; break;
      case 1: $tipo='Preventiva'; break;      
    }
    
    
    if($eots[$i]['tipo']=='0')
      print("<td style='text-align:center;font-weight:bold;'>".$eots[$i]['ot']."</td>");
    else
      print("<td style='text-align:center;font-weight:bold;'>(n/a)</td>");
    
    print("
    <td style='text-align:center;'>".$tipo."</td>
    <td style='text-align:center;'>".$eots[$i]['ingreso_fecha']." ");
    
    if($eots[$i]['eot_tipo']=='0') {
      print(substr($eots[$i]['ingreso_hora'],0,5).'</td>');
      print("<td style='font-size:10px;'>".$eots[$i]['nombre']."</td>");  
    } else {
      print("</td><td><i>(Autom&aacute;tico...)</i></td>"); 
    }
    
    print("
    <td style='font-size:11px;'>".$eots[$i]['eclase_nombre']."</td>
    <td>".$eots[$i]['equipo_marca']."</td>
    <td>".$eots[$i]['equipo_modelo']."</td>
    <td style='text-align:center;'>".$eots[$i]['equipo_inventario']."</td>
    <td style='text-align:center;'>
    ");
    
    if($eots[$i]['tipo']=='0')
      print("<img src='iconos/magnifier.png' style='cursor:pointer;'
              onClick='abrir_eot(".$eots[$i]['ot'].");'>");
    else
      print('&nbsp;');
    
    print("
      </td>
    ");
    
    if($eots[$i]['estado']==-1) {
    
      print("
      <td style='text-align:center;'>
        <img src='iconos/database_go.png' style='cursor:pointer;'
      ");
    
      if($eots[$i]['tipo']=='0')
        print("onClick='asignar_eot(".$eots[$i]['ot'].");'>");
      else
        print("onClick='asignar_prev(".$eots[$i]['ot'].");'>");
  
      print("</td>");
    
    } else print('<td>&nbsp;</td>');
    
    print("</tr>");
  
  }

?>

</table>