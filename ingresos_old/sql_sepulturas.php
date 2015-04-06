<?php 

	require_once('../conectar_db.php');
	
	$clirut=pg_escape_string($_POST['clirut']);
	$clipat=pg_escape_string(utf8_decode($_POST['clipat']));
	$climat=pg_escape_string(utf8_decode($_POST['climat']));
	$clinom=pg_escape_string(utf8_decode($_POST['clinom']));
	$clidir=pg_escape_string(utf8_decode($_POST['clidir']));
	$comcod=pg_escape_string(utf8_decode($_POST['comcod']))*1;
	$clifon=pg_escape_string($_POST['clifon']);
	$clicel=pg_escape_string($_POST['clicel']);
	$climail=pg_escape_string(utf8_decode($_POST['climail']));
	$cliobs=pg_escape_string(utf8_decode($_POST['cliobs']));

	$clirut2=pg_escape_string($_POST['clirut2']);
	$clipat2=pg_escape_string(utf8_decode($_POST['clipat2']));
	$climat2=pg_escape_string(utf8_decode($_POST['climat2']));
	$clinom2=pg_escape_string(utf8_decode($_POST['clinom2']));
	$clidir2=pg_escape_string(utf8_decode($_POST['clidir2']));
	$comcod2=pg_escape_string(utf8_decode($_POST['comcod2']))*1;
	$clifon2=pg_escape_string($_POST['clifon2']);
	$clicel2=pg_escape_string($_POST['clicel2']);
	$climail2=pg_escape_string(utf8_decode($_POST['climail2']));
	$cliobs2=pg_escape_string(utf8_decode($_POST['cliobs2']));

	$d=explode('-',$clirut);
	
	$d2=explode('-',$clirut2);

	pg_query("START TRANSACTION;");

	$chk=cargar_registro("SELECT * FROM clientes WHERE clirut=".($d[0]*1));	

	if($chk) {
	
		pg_query("
			UPDATE clientes SET
			clipat='$clipat',
			climat='$climat',
			clinom='$clinom',
			clidir='$clidir',
			comcod=$comcod,
			clifon='$clifon',
			clicel='$clicel',
			climail='$climail',
			cliobs='$cliobs'
			WHERE clirut=".($d[0]*1));	
	
	} else {

		pg_query("
			INSERT INTO clientes VALUES (
			".($d[0]*1).",'".$d[1]."',
			'$clipat', '$climat', '$clinom',
			5, 89, $comcod,
			'$clifon', '$clicel', '$climail', '$cliobs', '$clidir'			
			);		
		");

	}
	
	$chk=cargar_registro("SELECT * FROM clientes WHERE clirut=".($d2[0]*1));	

	if($chk) {
	
		pg_query("
			UPDATE clientes SET
			clipat='$clipat2',
			climat='$climat2',
			clinom='$clinom2',
			clidir='$clidir2',
			comcod=$comcod2,
			clifon='$clifon2',
			clicel='$clicel2',
			climail='$climail2',
			cliobs='$cliobs2'
			WHERE clirut=".($d2[0]*1));	
	
	} else {

		pg_query("
			INSERT INTO clientes VALUES (
			".($d2[0]*1).",'".$d2[1]."',
			'$clipat2', '$climat2', '$clinom2',
			5, 89, $comcod2,
			'$clifon2', '$clicel2', '$climail2', '$cliobs2', '$clidir2'			
			);		
		");

	}	


	$bolnum=$_POST['bolnum']*1;
	$crecod=$_POST['crecod']*1;
	$bolfec=fecha_sql(pg_escape_string(utf8_decode($_POST['fecha1'])));
	$bolmon=$_POST['bolmon']*1;
	$bolobs=pg_escape_string(utf8_decode($_POST['bolobs']));

	$clase=pg_escape_string(utf8_decode($_POST['clase']));
	$codigo=pg_escape_string(utf8_decode($_POST['codigo']));
	$numero=$_POST['numero']*1;
	$letra=pg_escape_string(utf8_decode($_POST['letra']));

	$s=cargar_registro("SELECT * FROM propiedad_sepultura
									LEFT JOIN clientes USING (clirut) 
									WHERE ps_clase='$clase' AND ps_codigo='$codigo'
									AND ps_numero=$numero AND ps_vigente
									", true);


	$vence=fecha_sql(pg_escape_string($_POST['fecha2']));

	if(!$s) {
		pg_query("INSERT INTO propiedad_sepultura VALUES (
						DEFAULT,
						'$clase', '$codigo', $numero, 
						".($d[0]*1).", $bolnum, true, '', null, $vence
					);");
	} else {
		if(isset($_POST['anular']))
			$estado='false'; else $estado='true';
			
		pg_query("UPDATE propiedad_sepultura SET
						clirut=".($d[0]*1).", 
						bolnum=$bolnum,
						ps_vigente=$estado, ps_vence=$vence
					WHERE ps_id=".$s['ps_id']);
	}
	
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
					".($d2[0]*1).");");
	} else {
		pg_query("UPDATE boletines SET 
						bolfec=$bolfec, bolmon=$bolmon, 
						bolobs='$bolobs', clirut=".($d2[0]*1)."
					WHERE bolnum=".$bolnum);
	}
	

	/*
	pg_query("DELETE FROM uso_sepultura 
					WHERE us_vigente AND sep_clase='$clase' AND 
							sep_codigo='$codigo' AND 
							sep_numero=$numero AND 
							sep_letra='$letra'");	
	*/

	$us_ids=array();

	$uso=cargar_registros_obj("
			SELECT * FROM uso_sepultura
			WHERE us_vigente AND sep_clase='$clase' AND 
							sep_codigo='$codigo' AND 
							sep_numero=$numero AND 
							sep_letra='$letra'
	");		
		
	for($i=0;;$i++) {

		if(!isset($_POST["r_".$i."_bolnum"])) break;
		
		$usid=$_POST["r_".$i."_us_id"]*1;

		if($usid=='0')
			$us_id='DEFAULT';
		else 
			$us_id=$usid;
			
		if($usid!='0') $us_ids[]=$usid;
			
		$bnum=$_POST["r_".$i."_bolnum"]*1;
		$fsep=fecha_sql(pg_escape_string(utf8_decode($_POST["r_".$i."_fecha_sep"])));
		$frut=pg_escape_string(utf8_decode($_POST["r_".$i."_rut"]));
		$fnom=pg_escape_string(utf8_decode($_POST["r_".$i."_nombre"]));
		$fref=pg_escape_string(utf8_decode($_POST["r_".$i."_referencias"]));
		$fven=fecha_sql(pg_escape_string(utf8_decode($_POST["r_".$i."_vence"])));
		$uubi=pg_escape_string(utf8_decode(trim($_POST["r_".$i."_ubicacion"])));
		$uest=$_POST["r_".$i."_estado"]*1;

		if($usid*1==0)
			pg_query("INSERT INTO uso_sepultura VALUES (
						DEFAULT,
						$fsep, 
						'$frut', '$fnom', '$fref', null, $uest, true, $bnum, 0,
						'$clase', '$codigo', $numero, '$letra',
						'$uubi', $fven
					);");
		else 
			pg_query("UPDATE uso_sepultura SET
						us_fecha_sep=$fsep,
						us_rut='$frut',
						us_nombre='$fnom',
						us_referencias='$fref',
						us_ubicacion='$uubi',
						us_vence=$fven,
						us_estado=$uest,
						bolnum=$bnum
					WHERE us_id=$usid");

	}

	for($i=0;$i<sizeof($uso);$i++) {

		$us_cmp=$uso[$i]['us_id']*1; $fnd=false;
	
		for($j=0;$j<sizeof($us_ids);$j++) {
			if($us_ids[$j]==$us_cmp) {
				$fnd=true; break;	
			}
		}
		
		if(!$fnd) {
			pg_query("DELETE FROM uso_sepultura WHERE us_id=$us_cmp");	
		}	
		
	}

	pg_query("COMMIT;");

	
?>