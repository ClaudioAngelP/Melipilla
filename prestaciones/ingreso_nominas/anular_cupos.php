<?php 

	require_once('../../conectar_db.php');
	
	$pac_id=$_POST['pac_id']*1;
	$esp_id=$_POST['esp_id']*1;
	$doc_id=$_POST['doc_id']*1;
	$f1=pg_escape_string($_POST['fecha1']);
	$f2=pg_escape_string($_POST['fecha2']);
	$h1=pg_escape_string($_POST['hora1']);
	$h2=pg_escape_string($_POST['hora2']);

	$motivo_anula=pg_escape_string($_POST['motivo_anula']);
	
	
	if($pac_id!=0) 
		$pac_w="pac_id=$pac_id";
	else
		$pac_w='pac_id=0';

	if($doc_id!=0) 
		$doc_w="nom_doc_id=$doc_id";
	else
		$doc_w='true';

	if($esp_id!=0) 
		$esp_w="nom_esp_id=$esp_id";
	else
		$esp_w='true';
		
	if($f1!='')
		$f1_w="nom_fecha>='$f1'";
	else
		$f1_w='true';

	if($f2!='')
		$f2_w="nom_fecha<='$f2'";
	else
		$f2_w='true';

	if($h1!='')
		$h1_w="nomd_hora>='$h1'";
	else
		$h1_w='true';

	if($h2!='')
		$h2_w="nomd_hora<='$h2'";
	else
		$h2_w='true';
		
		
	if($pac_w=='pac_id=0' AND $esp_w=='true' AND $doc_id=='true') {
?>

<center><h2>Ingrese par&aacute;metros para su b&uacute;squeda.</h2></center>

<?php 

	exit();	
	
}

	$tmp=cargar_registro("
		SELECT count(*) AS cuenta  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE nomd_diag_cod NOT IN ('X','T') AND NOT pac_id=0 AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w
	", true);
		
	$num=$tmp['cuenta']*1;

	$tmp2=cargar_registro("
		SELECT count(*) AS cuenta  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE nomd_diag_cod NOT IN ('X','T') AND pac_id=0 AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w
	", true);
		
	$num2=$tmp2['cuenta']*1;


	pg_query("
		UPDATE nomina_detalle SET nomd_diag_cod='X', nomd_codigo_cancela='$motivo_anula' WHERE nomd_id IN 
		(SELECT nomd_id FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE nomd_diag_cod NOT IN ('X','T') AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w)
	");
	
?>

<center><h2>Anulaci&oacute;n masiva de <?php echo $num ?> cupos ocupados y <?php echo $num2 ?> cupos libres realizada exitosamente.</h2></center>
