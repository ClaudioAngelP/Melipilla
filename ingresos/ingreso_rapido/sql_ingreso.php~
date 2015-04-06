<?php 

	require_once('../../conectar_db.php');
	
	$bolnum=$_POST['bolnum']*1;
	$rut=pg_escape_string(utf8_decode($_POST['rut']));
	$nombre=pg_escape_string(utf8_decode($_POST['nombre']));
	$ref=pg_escape_string(utf8_decode($_POST['referencias']));
	$fechasep=fecha_sql($_POST['fechasep']);
	$vence=fecha_sql($_POST['vence']);
	$estado=($_POST['estado']*1);
	$ubicacion=pg_escape_string(utf8_decode($_POST['ubicacion']));

	$clase=pg_escape_string(utf8_decode($_POST['sel_clases']));
	
	$codigo=pg_escape_string(utf8_decode($_POST['sel_codigos']));
	
	if($codigo=='-1')
		$codigo=pg_escape_string(utf8_decode($_POST['sel_codigon']));	

	$numero=($_POST['numero']*1);
	$letra=pg_escape_string(trim(utf8_decode($_POST['letra'])));
	
	$bolnum2=($_POST['bolnum2']*1);	
	$crecod=($_POST['crecod']*1);
	
	if($bolnum2!=0) $bloqueo='true'; else {
		$crecod=0;	
		$bloqueo='false';
	}	
	
	$prop=cargar_registros_obj("
		SELECT * FROM propiedad_sepultura 
		WHERE ps_clase='$clase' AND 
		ps_codigo='$codigo' AND 
		ps_numero='$numero' AND 
		ps_letra='$letra' 	
	");
	
	if(!$prop) {	
		
		if(trim($rut)!='')	
			$d=explode('-',$rut);
		else 
			$d[0]=0;
			
		pg_query("INSERT INTO propiedad_sepultura VALUES (
						DEFAULT,
						'$clase', '$codigo', $numero, '$letra',
						".($d[0]*1).", $bolnum, true, '', 0, $vence
					);");
					
	}	
	
	pg_query("
		INSERT INTO uso_sepultura VALUES (
			DEFAULT,
			$fechasep,
			'$rut','$nombre',
			'$ref', null,
			$estado, true,
			$bolnum, 0,
			'$clase', '$codigo', $numero, '$letra', '$ubicacion', $vence,
			$crecod, $bloqueo, $bolnum2		
		);	
	");
	
?>