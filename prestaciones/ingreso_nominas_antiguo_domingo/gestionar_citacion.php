<?php 

	require_once('../../conectar_db.php');

	$nomd_id=$_GET['nomd_id']*1;

	if(isset($_POST['observaciones']) AND trim($_POST['observaciones'])!='') {
		$nomd_id=$_POST['nomd_id']*1;
		$func_id=$_SESSION['sgh_usuario_id'];
		$observaciones=pg_escape_string($_POST['observaciones']);

		pg_query("INSERT INTO nomina_detalle_Gestion values (DEFAULT, $nomd_id, $func_id, CURRENT_TIMESTAMP, '$observaciones');");
		header('Location: gestionar_citacion.php?nomd_id='.$nomd_id);
		exit();
	}

	$g=cargar_Registros_obj("SELECT * FROM nomina_detalle_gestion JOIN funcionario USING (func_id) WHERE nomd_id=$nomd_id ORDER BY reg_fecha DESC;");

	 $lista = cargar_registros_obj("
  SELECT
        nomina_detalle.nomd_id, nom_fecha::date, nomd_hora,
        doc_rut, doc_paterno, doc_materno, doc_nombres,
        COALESCE(diag_desc, cancela_desc) AS diag_desc,
        nomd_diag_cod,
        esp_desc, nomd_tipo,
        CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
        nomd_codigo_presta, glosa, esp_lugar, COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad,
        nomina_detalle.id_sidra,upper(func_nombre) AS asigna_nombre, '260194' AS esp_fono, *
  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN pacientes USING (pac_id)
  LEFT JOIN comunas USING (ciud_id)
  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
  LEFT JOIN doctores ON nom_doc_id=doc_id
  LEFT JOIN especialidades ON nom_esp_id=esp_id
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  LEFT JOIN codigos_prestacion ON nomd_codigo_presta=codigo
  LEFT JOIN cupos_atencion ON nomina.nom_id=cupos_atencion.nom_id
  LEFT JOIN funcionario ON nomd_func_id=func_id
  WHERE nomd_id=$nomd_id
  ORDER BY nomina.nom_fecha ASC, nomd_hora  LIMIT 1
  ", true);

?>

<html>
<title>Gesti&oacute;n de Citaciones</title>
<?php cabecera_popup('../..'); ?>

<script>

</script>

<body style='popup_background fuente_por_defecto'>
<center>


<table style='width:100%;font-size:12px;'>
<tr><td style='text-align:right;width:30%;' class='tabla_fila2'>RUN:</td><td class='tabla_fila'><?php echo $lista[0]['pac_rut']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Ficha:</td><td class='tabla_fila'><?php echo $lista[0]['pac_ficha']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Nombre Completo:</td><td class='tabla_fila' style='font-size:16px;'><?php echo $lista[0]['pac_nombres'].' '.$lista[0]['pac_appat'].' '.$lista[0]['pac_apmat']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Direcci&oacute;n:</td><td class='tabla_fila'><?php echo $lista[0]['pac_direccion']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Tel&eacute;fono:</td><td class='tabla_fila'><?php echo $lista[0]['pac_fono']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Celular:</td><td class='tabla_fila'><?php echo $lista[0]['pac_celular']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>email:</td><td class='tabla_fila'><?php echo $lista[0]['pac_mail']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Especialidad:</td><td class='tabla_fila' style='font-size:14px;'><?php echo $lista[0]['esp_desc']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Profesional:</td><td class='tabla_fila' style='font-size:14px;'><?php echo $lista[0]['doc_nombres'].' '.$lista[0]['doc_paterno'].' '.$lista[0]['doc_materno']; ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Fecha/Hora:</td><td class='tabla_fila' style='font-size:24px;'><?php echo substr($lista[0]['nom_fecha'],0,10).' '.substr($lista[0]['nomd_hora'],0,5); ?></td></tr>


</table>


<form id='reg' name='reg' action='gestionar_citacion.php' method='post'>
<input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />
<table><tr><td>Agregar Nuevo Registro:</td><td>
<input type='text' id='observaciones' name='observaciones' size=40 ></td><td>
<input type='submit' id='' name='' value='[Guardar Registro...]' /></td></tr></table>
</center>
</form>

<table style='width:100%;font-size:14px;'>
<?php 

	if($g)
	for($i=0;$i<sizeof($g);$i++) {
		print("<tr><td rowspan=3 class='tabla_header' style='font-size:24px;'>".(sizeof($g)-$i)."</td><td style='text-align:right;width:25%;' class='tabla_fila2'>Fecha/Hora:</td><td style='font-size:18px;'>".substr($g[$i]['reg_fecha'],0,16)."</td></tr><tr><td style='text-align:right;' class='tabla_fila2'>Funcionario:</td><td>".$g[$i]['func_nombre']."</td></tr><tr><td style='text-align:right;' class='tabla_fila2'>Observaciones:</td><td>".$g[$i]['reg_observaciones']."</td></tr>");

	}

?>
</table>
</body>

</html>
