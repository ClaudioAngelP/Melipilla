<?php 

	require_once('../../conectar_db.php');
	
	pg_query("START TRANSACTION;");

	$bolnum=$_POST['bolnum']*1;
	$crecod=$_POST['crecod']*1;
	$bolfec=fecha_sql(pg_escape_string(utf8_decode($_POST['fecha1'])));
	$bolmon=$_POST['bolmon']*1;
	$bolobs=pg_escape_string(utf8_decode($_POST['bolobs']));

	$us_id=$_POST['us_id']*1;

	if(!isset($_POST['sepulturas'])) {
	
		$clase=pg_escape_string(utf8_decode($_POST['sel_clases']));
		$codigo=pg_escape_string(utf8_decode($_POST['sel_codigos']));
	
		if($codigo=='-1') {
			$codigo=pg_escape_string(utf8_decode($_POST['sel_codigon']));
		}
		
		$numero=$_POST['numero']*1;
		$letra=pg_escape_string(trim(utf8_decode($_POST['letra'])));
	
	} else {
		
		$p=cargar_registro("SELECT * FROM propiedad_sepultura WHERE ps_id=".($_POST['sepulturas']*1));
		
		$clase=$p['ps_clase'];
		$codigo=$p['ps_codigo'];
		$numero=$p['ps_numero'];
		$letra=$p['ps_letra'];
		
	}

	$fecha_sep=pg_escape_string(utf8_decode($_POST['fecha_sep']));
	//$referencias=pg_escape_string(utf8_decode($_POST['referencias']));
	$ubicacion=pg_escape_string(utf8_decode($_POST['sel_ubicaciones']));
	$estado=($_POST['estado'])*1;

	$chk=cargar_registro("SELECT * FROM boletines WHERE bolnum=".$bolnum);
	
	if(!$chk) {
		if($bolnum!=0)
		pg_query("INSERT INTO boletines VALUES (
					$bolnum, 
					$bolfec, 
					$bolmon, 
					'$bolobs',
					0,0,0,
					".$_SESSION['sgh_usuario_id'].", 
					".($d[0]*1).");");
	} 
		
	$traslado=$_POST['traslado']*1;		
		
	if($traslado==1) {
		$traslado="		sep_clase='$clase',
							sep_codigo='$codigo',
							sep_numero=$numero,
							sep_letra='$letra',
							bolnum2=0,
							us_bloqueo=false,
							us_ubicacion='$ubicacion',
						";
	} elseif($traslado==2) {
		$traslado="		sep_clase='FOSA',
							sep_codigo='',
							sep_numero=0,
							sep_letra='', 
							us_ubicacion='',
						";	
	} else {
		$traslado='';
	}

	pg_query("UPDATE uso_sepultura SET
				bolnum=$bolnum,
				us_fecha_sep='$fecha_sep',
				$traslado	
				us_estado=$estado
				WHERE us_id=$us_id");

	pg_query("COMMIT;");

	
?>