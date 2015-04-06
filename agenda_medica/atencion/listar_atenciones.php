<?php

  require_once('../../conectar_db.php');
  
  $fecha = pg_escape_string($_GET['fecha']);
  $doc_id = $_GET['doc_id']*1;
  $esp_id = $_GET['esp_id']*1;

  $cupos = cargar_registros_obj(
    "SELECT
    pacientes.*, asigna_hora, asigna_id, cupos_asigna.inter_id, control_id
    FROM cupos_asigna 
    JOIN interconsulta ON cupos_asigna.inter_id=interconsulta.inter_id 
    JOIN pacientes ON pacientes.pac_id=interconsulta.inter_pac_id
    JOIN cupos_atencion USING (cupos_id)
    WHERE date_trunc('day', cupos_atencion.cupos_fecha)='$fecha' AND 
          cupos_doc_id=$doc_id AND cupos_esp_id=$esp_id
    ORDER BY asigna_hora
    ", true
  );
  
  list($doc) = cargar_registros_obj("
    SELECT * FROM doctores WHERE doc_id=$doc_id
  ");

  list($esp) = cargar_registros_obj("
    SELECT * FROM especialidades WHERE esp_id=$esp_id
  ");
  
?>

<table style="width:100%;">
<tr>
<td style='text-align:right;' class='tabla_header'>Fecha:</td>
<td colspan=7 style='font-weight:bold;font-size:14px;'><?php echo $fecha; ?></td>
</tr>

<tr>
<td style='text-align:right;' class='tabla_header'>Especialidad:</td>
<td colspan=7 style='font-weight:bold;font-size:14px;'>
<?php echo htmlentities($esp['esp_desc']); ?></td>
</tr>

<tr>
<td style='text-align:right;' class='tabla_header'>M&eacute;dico:</td>
<td colspan=7>
<?php 
echo $doc['doc_rut'].' <b>'.htmlentities($doc['doc_paterno'].' '.$doc['doc_materno'].' '.$doc['doc_nombres']).'</b>'; 
?></td>
</tr>

<tr class="tabla_header" style="font-weight:bold;">
<td>Hora</td>
<td>R.U.T./ID</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>Tipo</td>
<td>Ficha I.C.</td>
<td>Registro</td>
<td>Estado</td>
</tr>

<?php 

  if($cupos)
  for($i=0;$i<count($cupos);$i++) {
  
    if(($i%2)==0) $clase='tabla_fila'; else $clase='tabla_fila2';
  
    $id=$cupos[$i]['asigna_id'];
  
    print('
    <tr class="'.$clase.'" style="cursor: pointer;"
    onMouseOver="this.className=\'mouse_over\'"
    onMouseOut="this.className=\''.$clase.'\'">
    <td style="text-align: center;">'.($cupos[$i]['asigna_hora']).'</td>
    <td style="text-align: right;">'.($cupos[$i]['pac_rut']).'</td>
    <td>'.($cupos[$i]['pac_appat']).'</td>
    <td>'.($cupos[$i]['pac_apmat']).'</td>
    <td>'.($cupos[$i]['pac_nombres']).'</td>
    <td style="text-align: center;font-weight:bold;">'.(($cupos[$i]['control_id']==0)?'N':'C').'</td>
    
    <td>
    <center>
    <img src="iconos/magnifier.png" style="cursor:pointer;"
    onClick="abrir_ficha('.$cupos[$i]['inter_id'].');">
    </center>
    </td>
      
    <td>
    <center>
    <img src="iconos/page_white_edit.png" 
    onClick="definir_registro('.($cupos[$i]['asigna_id']).');"
    style="cursor:pointer;">
    </center>
    </td>
    
    <td style="text-align:center;"></td>
    </tr>
    ');
  
  }

?>

</table>
