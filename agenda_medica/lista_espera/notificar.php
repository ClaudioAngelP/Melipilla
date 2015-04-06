<?php

  require_once('../../conectar_db.php');
  
  if(isset($_GET['inter_id'])) {
  	
    $inter_id=$_GET['inter_id']*1;
    $control_id=$_GET['control_id']*1;
    
  } else {
  	
    $inter_id=$_POST['inter_id']*1;
    $control_id=$_POST['control_id']*1;

    $comentarios=pg_escape_string($_POST['comentarios']);
    $notificado=isset($_POST['notificado']);
    
    pg_query("
      INSERT INTO cupos_notificacion VALUES (
      DEFAULT,
      $inter_id,
      '$comentarios',
      ".($notificado?'true':'false').",
      now(),
      $control_id,
      ".($_SESSION['sgh_usuario_id']*1)."
      )
    ");
  
  }
  
  list($a)=cargar_registros_obj("
    SELECT * FROM interconsulta
    JOIN pacientes ON inter_pac_id=pac_id
    JOIN especialidades ON esp_id=inter_especialidad
    WHERE inter_id=$inter_id
  ");

  list($pac)=cargar_registros_obj("
    SELECT * FROM pacientes WHERE pac_id=".$a['pac_id']."
  ");

	/*
  list($doc)=cargar_registros_obj("
    SELECT * FROM doctores WHERE doc_id=".$a['cupos_doc_id']."
  ");

  list($esp)=cargar_registros_obj("
    SELECT * FROM especialidades WHERE esp_id=".$a['cupos_esp_id']."
  ");
  */
                          
  $n=cargar_registros_obj("
    SELECT *, date_trunc('second',notif_fecha) AS notif_fecha 
    FROM cupos_notificacion
    JOIN funcionario USING (func_id)
    WHERE inter_id=$inter_id AND control_id=$control_id
    ORDER BY cupos_notificacion.notif_fecha DESC
  ");
  
?>

<html>
<title>Definir Destino de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>

<form id='dato' name='dato' onSubmit='' 
action='notificar.php' method='post'>

<input type='hidden' id='inter_id' name='inter_id' 
value='<?php echo $inter_id; ?>'>

<input type='hidden' id='control_id' name='control_id' 
value='<?php echo $control_id; ?>'>

<div class='sub-content'>
<img src='../../iconos/phone.png'>
<b>Notificaci&oacute;n de Citaciones 
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;'>R.U.T.:</td>
<td><b><?php echo $pac['pac_rut']; ?></b></td>
</tr>

<tr>
<td style='text-align:right;'>Nombre:</td>
<td><b><?php echo htmlentities($pac['pac_appat'].' '.$pac['pac_apmat'].' '.$pac['pac_nombres']); ?></b></td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono:</td>
<td><b><?php echo ($pac['pac_fono']!=''?htmlentities($pac['pac_fono']):'<i>(No hay registros)</i>') ?></td>
</tr>

<tr>
<td style='text-align:right;'>Especialidad:</td>
<td><b><?php echo htmlentities($a['esp_desc']); ?></td>
</tr>

</table>

</div>

<div class='sub-content2' style='overflow:auto;height:160px;'>

<table style='width:100%;font-size:12px;'>

<?php 

  if($n)
  for($i=0;$i<count($n);$i++) {
  
    print("
      <tr><td class='tabla_fila2' style='text-align:right;width:100px;'>Fecha:</td>
      <td class='tabla_fila' 
      style='font-weight:bold;'>".$n[$i]['notif_fecha']."</td></tr>
      <tr><td class='tabla_fila2' style='text-align:right;'>Funcionario:</td>
      <td class='tabla_fila'>".htmlentities($n[$i]['func_nombre'])."</td></tr>
      <tr><td class='tabla_fila2' style='text-align:right;'>Comentarios:</td>
      <td class='tabla_fila'><i>".htmlentities($n[$i]['notif_comentario'])."</i></td></tr>
      <tr><td class='tabla_fila2' style='text-align:right;'>Notificado:</td>
      <td class='tabla_fila'><img 
      src='../../iconos/".(($n[$i]['notif_estado']=='t')?'tick':'cross').".png' /></td></tr>
      </tr>
    ");
  
  }

?>

</table>

</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;' valign='top'>Comentarios:</td>
<td>
<textarea id='comentarios' name='comentarios'
cols=60 rows=2></textarea>
</td>
</tr>

<tr>
<td style='text-align:right;'>Notificado:</td>
<td>
<input type='checkbox' id='notificado' name='notificado'>
</td>
</tr>

<tr>
<td colspan=2 style='text-align:center;'>
<input type='submit' value=' -- Guardar Notificaci&oacute;n -- '>
</td>
</tr>

</table>
</div>

</form>

</body>
</html>
