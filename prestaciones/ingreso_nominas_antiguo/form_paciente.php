<?php 

	require_once('../../conectar_db.php');
	
	$pac_id=$_GET['pac_id']*1;
	$nom_id=$_GET['nom_id']*1;
	$nomd_hora=pg_escape_string($_GET['nomd_hora']);

	$duracion='';
	if(isset($_GET['duracion']))
		$duracion=$_GET['duracion']*1;
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id", true);

?>

<html>
<title>Actualizar Datos del Paciente</title>

<?php cabecera_popup('../..'); ?>

<script>

	function imprimir_citacion(nomd_id) {
	
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('citaciones.php?nomd_id='+nomd_id,
      '_self', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}


	function guardar_cupo() {

					var myAjax=new Ajax.Request(
						'sql_tomar_cupo.php',
						{
							method:'post',
							parameters:$('datos_pac').serialize(),
							onComplete:function(resp2) {
								
								var fn2=window.opener.abrir_nomina.bind(window.opener);
								
								fn2(window.opener.$("folio_nomina").value, 1);								
								imprimir_citacion(resp2.responseText*1);
								
								//window.close();

							}	
						}					
					);		


	}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/user_go.png' />
<b>Actualizar Datos de Paciente</b>
</div>

<form id='datos_pac' name='datos_pac' onSubmit='return false;'>

<input type='hidden' id='pac_id' name='pac_id' value='<?php echo $pac_id; ?>' />
<input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
<input type='hidden' id='nomd_hora' name='nomd_hora' value='<?php echo $nomd_hora; ?>' />
<input type='hidden' id='duracion' name='duracion' value='<?php echo $duracion; ?>' />

<div class='sub-content'>

<table style='width:100%;'>

<tr><td style='text-align:right;' class='tabla_fila2'>
R.U.N.:
</td><td style='tabla_fila'>

<input type='text' id='pac_rut' name='pac_rut' value='<?php echo $pac['pac_rut']; ?>' style='font-size:14px;font-weight:bold;' />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Pasaporte/ID:
</td><td style='tabla_fila'>

<input type='text' id='pac_pasaporte' name='pac_pasaporte' value='<?php echo $pac['pac_pasaporte']; ?>' style='font-size:14px;font-weight:bold;' />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
N&uacute;mero Ficha:
</td><td style='tabla_fila'>

<input type='text' id='pac_ficha' name='pac_ficha' value='<?php echo $pac['pac_ficha']; ?>' style='font-size:14px;font-weight:bold;' />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Nombres:
</td><td style='tabla_fila'>

<input type='text' id='pac_nombres' name='pac_nombres' value='<?php echo $pac['pac_nombres']; ?>' style='font-size:12px;font-weight:bold;' size=30 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Apellido Paterno:
</td><td style='tabla_fila'>

<input type='text' id='pac_appat' name='pac_appat' value='<?php echo $pac['pac_appat']; ?>' style='font-size:12px;font-weight:bold;' size=30 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Apellido Materno:
</td><td style='tabla_fila'>

<input type='text' id='pac_apmat' name='pac_apmat' value='<?php echo $pac['pac_apmat']; ?>' style='font-size:12px;font-weight:bold;' size=30 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Fecha de Nacimiento:
</td><td style='tabla_fila'>

<input type='text' id='pac_fc_nac' name='pac_fc_nac' value='<?php echo $pac['pac_fc_nac']; ?>' style='text-align:center;' size=10 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Sexo:
</td><td style='tabla_fila'>

<select id='sex_id' name='sex_id' style='font-size:14px;'>
<?php 
	$sexs=cargar_registros_obj("SELECT * FROM sexo ORDER BY sex_id;", true);
	for($i=0;$i<sizeof($sexs);$i++) {
		if($pac['sex_id']==$sexs[$i]['sex_id']) $sel='SELECTED'; else $sel='';
		print("<option value='".$sexs[$i]['sex_id']."' $sel >".$sexs[$i]['sex_desc']."</option>");
	}
?>
</select>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Direcci&oacute;n:
</td><td style='tabla_fila'>

<input type='text' id='pac_direccion' name='pac_direccion' value='<?php echo $pac['pac_direccion']; ?>' size=40 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Comuna:
</td><td style='tabla_fila'>

<select id='ciud_id' name='ciud_id' style='font-size:14px;'>
<?php 
	$coms=cargar_registros_obj("SELECT * FROM comunas ORDER BY ciud_desc;", true);
	for($i=0;$i<sizeof($coms);$i++) {
		if($pac['ciud_id']==$coms[$i]['ciud_id']) $sel='SELECTED'; else $sel='';
		print("<option value='".$coms[$i]['ciud_id']."' $sel >".$coms[$i]['ciud_desc']."</option>");
	}
?>
</select>

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Tel&eacute;fono:
</td><td style='tabla_fila'>

<input type='text' id='pac_fono' name='pac_fono' value='<?php echo $pac['pac_fono']; ?>' size=20 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Celular:
</td><td style='tabla_fila'>

<input type='text' id='pac_celular' name='pac_celular' value='<?php echo $pac['pac_celular']; ?>' size=20 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
e-mail:
</td><td style='tabla_fila'>

<input type='text' id='pac_mail' name='pac_mail' value='<?php echo $pac['pac_mail']; ?>' size=30 />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Ocupaci&oacute;n:
</td><td style='tabla_fila'>

<input type='text' id='pac_ocupacion' name='pac_ocupacion' value='<?php echo $pac['pac_ocupacion']; ?>' style='font-size:14px;font-weight:bold;' />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Representante:
</td><td style='tabla_fila'>

<input type='text' id='pac_padre' name='pac_padre' value='<?php echo $pac['pac_padre']; ?>' style='font-size:14px;font-weight:bold;' />

</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Previsi&oacute;n:
</td><td style='tabla_fila'>

<select id='prev_id' name='prev_id' style='font-size:14px;'>
<?php 
	$prvs=cargar_registros_obj("SELECT * FROM prevision ORDER BY prev_id;", true);
	for($i=0;$i<sizeof($prvs);$i++) {
		if($pac['prev_id']==$prvs[$i]['prev_id']) $sel='SELECTED'; else $sel='';
		print("<option value='".$prvs[$i]['prev_id']."' $sel >".$prvs[$i]['prev_desc']."</option>");
	}
?>
</select>

</td></tr>

</table>

<center>

<input type='button' id='guarda' name='guarda' value='--[[ Guardar Datos de Paciente... ]]--' onClick='guardar_cupo();' style='font-size:18px;margin:5px;' />

</div>

</form>

</body>

</html>
