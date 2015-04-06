<?php

  require_once('../../conectar_db.php');
  
  // Debe loguearse un técnico!!!
    $tec_id=$_SESSION['sgh_usuario_id'];
                                  
  $modo_admin=isset($_POST['modo']);
  
  if(!$modo_admin)
  //$tec_w="equipo_orden_tecnico.tec_id=$tec_id";
  $tec_w="true";
  else
  $tec_w="true";
  
  $eots=cargar_registros_obj("
  
  SELECT * FROM (
  
  SELECT  
    eot_id AS ot,
    eot_tipo AS tipo,
    eot_fecha_ing::date AS ingreso_fecha,
    eot_fecha_ing::time AS ingreso_hora,
    func_nombre AS nombre,
    equipo_medico_clase.*, equipos_medicos.*, eot_tipo, eot_estado
  FROM equipo_orden_trabajo 
  JOIN equipo_orden_tecnico USING (eot_id)
  LEFT JOIN funcionario ON eot_func_id=func_id
  LEFT JOIN equipos_medicos ON eot_equipo_id=equipo_id
  LEFT JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
  WHERE $tec_w AND NOT eot_estado=5

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
    
    
    print("<td style='text-align:center;font-weight:bold;'>".$eots[$i]['ot']."</td>
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
    <img src='iconos/magnifier.png' style='cursor:pointer;'
              onClick='abrir_eot(".$eots[$i]['ot'].");'>
    </td>
    <td style='text-align:center;'>
      <img src='iconos/wrench_orange.png' style='cursor:pointer;'
      onClick='cargar_eot(".$eots[$i]['ot'].", ".$eots[$i]['eot_estado'].");'></td>");
    
    // <td style='text-align:center;'>".$eots[$i]['equipo_serie']."</td>
    
    print("</tr>");
  
  }

?>

</table>