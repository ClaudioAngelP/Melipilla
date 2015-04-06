<?php 

	require_once('../../conectar_db.php');

	$e=$_POST['esp_id']*1;
	$p=$_POST['doc_id']*1;
	$t=pg_escape_string($_POST['nom_motivo']);

	if($e==0)
		$ew='true';
	else
		$ew='nom_esp_id='.$e;

	if($p==0)
		$pw='true';
	else
		$pw='nom_doc_id='.$p;

	if($t=='')
                $tw='true';
        else
                $tw="nom_motivo='$t'";

	$f1=pg_escape_string($_POST['fecha1']);
	$f2=pg_escape_string($_POST['fecha2']);

	if($f1!='')
		$f1w="nom_fecha::date>='$f1'";
	else
		$f1w="true";

	if($f2!='')
                $f2w="nom_fecha::date<='$f2'";
        else
                $f2w="true";

	$h1=pg_escape_string($_POST['hora1']);
        $h2=pg_escape_string($_POST['hora2']);

        if($h1!='')
                $h1w="nomd_hora>='$h1'";
        else
                $h1w="true";

        if($h2!='')
                $h2w="nomd_hora<='$h2'";
        else
                $h2w="true";


	pg_query("START TRANSACTION;");

	$q1=cargar_registros_obj("

		SELECT *, doc_nombres||' '||doc_paterno||' '||doc_materno AS doc_nombre, pac_nombres||' '||pac_appat||' '||pac_apmat AS pac_nombre 
		FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		LEFT JOIN pacientes USING (pac_id)
		WHERE $f1w AND $f2w AND $h1w AND $h2w AND $tw AND nomd_diag_cod='X' AND NOT pac_id=0 AND $ew AND $pw ORDER BY nom_fecha, nomd_hora;

	",true);

	$q2=cargar_registros_obj("

                SELECT COUNT(*) AS cnt FROM nomina_detalle
                JOIN nomina USING (nom_id)
                WHERE $f1w AND $f2w AND $h1w AND $h2w AND $tw AND nomd_diag_cod='X' AND pac_id=0 AND $ew AND $pw;

        ");

	pg_query("UPDATE nomina_detalle SET nomd_diag_cod='' WHERE nomd_id IN (
		SELECT nomd_id FROM nomina_detalle 
		JOIN nomina USING (nom_id)
		WHERE $f1w AND $f2w AND $h1w AND $h2w AND $tw AND nomd_diag_cod='X' AND $ew AND $pw)");

	pg_query("COMMIT;");

?>

<h2 style='color:green;'>Se recuperaron <b><?php echo $q2[0]['cnt']; ?></b> cupos libres bloqueados.<br/>
Se recuperaron <b><?php echo $q1?sizeof($q1):'0'; ?></b> cupos utilizados bloqueados.</h2>
<br/>

<table style='width:100%;font-size:10px;color:green;'>
<tr class='tabla_header'>
<td>#</td><td>Fecha/Hora</td><td>Especialidad/Ubicaci&oacute;n</td><td>Profesional/Servicio</td><td>Paciente</td>
</tr>

<?php 

if($q1)
for($i=0;$i<sizeof($q1);$i++) {

	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';

	print("<tr class='$clase'>
	<td style='text-align:right;font-weight:bold;'>".($i+1)."</td>
	<td style='text-align:center;'>".substr($q1[$i]['nom_fecha'],0,10)."
	".substr($q1[$i]['nomd_hora'],0,5)."</td>
	<td style='text-align:left;'>".$q1[$i]['esp_desc']."</td>
	<td style='text-align:left;'>".$q1[$i]['doc_nombre']."</td>
	<td style='text-align:left;font-weight:bold;'>".$q1[$i]['pac_nombre']."</td>
	</tr>");

}


?>

</table>


