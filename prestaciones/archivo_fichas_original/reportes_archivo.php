<?php 

	require_once('../../conectar_db.php');

	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
		$xls=true;
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_archivo.xls\";");   
	} else {
		$xls=false;
	}
	
	$opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
	$opts_color=Array('black','gray','blue','purple','green','red');
	
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
				
	$tipo=$_POST['tipo']*1;
	
	if($tipo==0) {
	
		$l=cargar_registros_obj("
		SELECT *, 
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
		COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
	
		FROM nomina_detalle 
		JOIN nomina USING (nom_id) 
		JOIN pacientes USING (pac_id) 
		JOIN especialidades ON nom_esp_id=esp_id 
		JOIN doctores ON nom_doc_id=doc_id
		JOIN cupos_atencion USING (nom_id)
		WHERE $f1w AND $f2w AND $tw AND NOT pac_id=0 AND $ew AND $pw AND cupos_ficha AND esp_ficha
		ORDER BY (CASE WHEN pac_ficha='' THEN '0' WHEN pac_ficha IS NULL THEN '0' ELSE pac_ficha END)::bigint;
		", true);
		
?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>
<td>Nro. Ficha</td>
<td>R.U.N.</td>
<td>Nombre Completo</td>
<td>Fecha Atenci&oacute;n</td>
<td>Hora</td>
<td>Servicio Solicitante</td>
<td>Profesional</td>
<td>Tipo Solicitud</td>
<td>Local Actual</td>
<td>Estado</td>
<td>Fecha/Hora Solicitud</td>
</tr>


<?php 


	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
		<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;'>".$l[$i]['pac_ficha']."</td>
		<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_appat']." ".$l[$i]['pac_apmat']." ".$l[$i]['pac_nombres']."</td>
		<td style='text-align:center;'>".substr($l[$i]['nom_fecha'],0,10)."</td>
		 <td style='text-align:center;'>".substr($l[$i]['nomd_hora'],0,5)."</td>

		<td style='text-align:left;'>".$l[$i]['esp_desc']."</td>
		<td style='text-align:left;'>".$l[$i]['doc_paterno']." ".$l[$i]['doc_materno']." ".$l[$i]['doc_nombres']."</td>
		<td style='text-align:center;'>PROGRAMADA</td>
		<td style='text-align:left;font-weight:bold;'>".$l[$i]['ubic_actual']."</td>
		<td style='text-align:left;'>".$opts[$l[$i]['am_estado']*1]."</td>
		<td style='text-align:center;'>".substr($l[$i]['nomd_fecha_asigna'],0,16)."</td>
		</tr>
		");
		
	}

?>


</table>
	
<?php
	
	} else if($tipo==1) {

		if($f1!='')
			$f1w="fesp_fecha::date>='$f1'";
		else
			$f1w="true";

		 if($f2!='')
			$f2w="fesp_fecha::date<='$f2'";
		else
			$f2w="true";

	
		$l=cargar_registros_obj("
		SELECT *, 
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
		COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
	
		FROM ficha_espontanea 
		JOIN pacientes USING (pac_id) 
		JOIN especialidades USING  (esp_id)
		JOIN doctores USING (doc_id)
		WHERE $f1w AND $f2w AND $tw AND NOT pac_id=0 AND $ew AND $pw AND esp_ficha
		ORDER BY 
		(CASE 
		WHEN pacientes.pac_ficha='' THEN '0' 
		WHEN pacientes.pac_ficha IS NULL THEN '0' 
		ELSE pacientes.pac_ficha END)::bigint;
		", true);
		
?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>
<td>Nro. Ficha</td>
<td>R.U.N.</td>
<td>Nombre Completo</td>
<td>Fecha/Hora Solicitud</td>
<td>Servicio Solicitante</td>
<td>Profesional</td>
<td>Tipo Solicitud</td>
<td>Local Actual</td>
<td>Estado</td>
</tr>


<?php 


	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
		<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;'>".$l[$i]['pac_ficha']."</td>
		<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_appat']." ".$l[$i]['pac_apmat']." ".$l[$i]['pac_nombres']."</td>
		<td style='text-align:center;'>".substr($l[$i]['fesp_fecha'],0,16)."</td>
		<td style='text-align:left;'>".$l[$i]['esp_desc']."</td>
		<td style='text-align:left;'>".$l[$i]['doc_paterno']." ".$l[$i]['doc_materno']." ".$l[$i]['doc_nombres']."</td>
		<td style='text-align:center;'>NO PROGRAMADA</td>
		<td style='text-align:left;font-weight:bold;'>".$l[$i]['ubic_actual']."</td>
		<td style='text-align:left;'>".$opts[$l[$i]['am_estado']*1]."</td>
		</tr>
		");
		
	}

?>


</table>
	
<?php
	
	} else if($tipo==2) {


		if($e==0)
			$esp='true';
		else
			$esp='especialidades.esp_id='.$e;

		if($p==0)
			$doc='true';
		else
			$doc='doctores.doc_id='.$p;

	
		$l=cargar_registros_obj("
		SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
				upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
				pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,'' AS nomd_diag_cod, 
				date_trunc('second',
				COALESCE((SELECT nomd_fecha_asigna FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_esp_id=destino_esp_id AND nom_doc_id=destino_doc_id ORDER BY nom_fecha DESC LIMIT 1),
				(SELECT fesp_fecha FROM ficha_espontanea WHERE ficha_espontanea.pac_id=pacientes.pac_id AND ficha_espontanea.esp_id=destino_esp_id AND ficha_espontanea.doc_id=destino_doc_id ORDER BY fesp_fecha DESC LIMIT 1))
				)AS fecha_asigna,
				especialidades.esp_id,doctores.doc_id,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
				COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
				COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
				(SELECT am_fecha FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id AND am_estado=2 ORDER BY am_fecha DESC LIMIT 1) as fecha_envio
				FROM archivo_movimientos
				LEFT JOIN especialidades ON destino_esp_id=esp_id
				LEFT JOIN doctores ON destino_doc_id=doc_id
				JOIN pacientes USING (pac_id)
				WHERE 
				am_final AND am_estado IN (2,3) AND archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id
				AND $esp AND $doc
				ORDER BY esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint
		", true);
		
?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>
<td>Nro. Ficha</td>
<td>R.U.N.</td>
<td>Nombre Completo</td>
<td>Fecha/Hora Atenci&oacute;n</td>
<td>Fecha/Hora Env&iacute;o</td>
<td>Local Anterior</td>
<td>Local Actual</td>
<td>Estado</td>
</tr>


<?php 


	$esp_id=0;
	$doc_id=0;

	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($l[$i]['esp_id']*1!=$esp_id OR $l[$i]['doc_id']*1!=$doc_id) {
		print("
		<tr class='tabla_header'>
		<td colspan=2 style='text-align:right;'>Especialidad/Recurso:</td><td colspan=6 style='text-align:left;'>".$l[$i]['esp_desc']."</td>
		</tr>
		<tr class='tabla_header'>
		<td colspan=2 style='text-align:right;'>Profesional/Servicio:</td><td colspan=6 style='text-align:left;'>".$l[$i]['doc_paterno']." ".$l[$i]['doc_materno']." ".$l[$i]['doc_nombres']."</td>
		</tr>
		");
		}
		
		print("
		<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;'>".$l[$i]['pac_ficha']."</td>
		<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_appat']." ".$l[$i]['pac_apmat']." ".$l[$i]['pac_nombres']."</td>
		<td style='text-align:center;'>".substr($l[$i]['fecha_asigna'],0,16)."</td>
		<td style='text-align:center;'>".substr($l[$i]['fecha_envio'],0,16)."</td>
		<td style='text-align:left;font-weight:bold;'>".$l[$i]['ubic_anterior']."</td>
		<td style='text-align:left;font-weight:bold;'>".$l[$i]['ubic_actual']."</td>
		<td style='text-align:left;'>".$opts[$l[$i]['am_estado']*1]."</td>
		</tr>
		");
		
	}

?>


</table>

<?php
	
	} else {
	
		print("<center><h2>En Construcci&oacute;n...</h2></center>");
	
	}
			

?>
