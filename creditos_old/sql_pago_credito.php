<?php 

	require_once('../conectar_db.php');

	function fecha_sql($f) {
		$fp=explode('/',$f);
		if(sizeof($fp)==3 AND checkdate($fp[1]*1, $fp[0]*1,$fp[2]*1)) 
			return "'".$f."'";
		else 
			return 'null';
	}


	$monto=$_POST['monto']*1;
	$crecod=$_POST['crecod']*1;
	$observa=pg_escape_string($_POST['observaciones']);
	$vigencia=pg_escape_string($_POST['vigencia']);

	$efectivo=$_POST['pago_efectivo']*1;
	
	if($_POST['pago_cheques']!='')	
		$cheques=explode('[|]',substr($_POST['pago_cheques'],0,-3));
	else
		$cheques=false;	

	if($_POST['pago_otras']!='')	
		$otras=explode('[|]',substr($_POST['pago_otras'],0,-3));
	else
		$otras=false;	

	pg_query("START TRANSACTION;");

	$nbolnum=$_POST['nbolnum']*1;
	$nbolfec=fecha_sql($_POST['nbolfec']);

	if($nbolnum!=0) {
		$bolnum=$nbolnum;
		$currval=$nbolnum;
		$bolfec=$nbolfec;
	} else {
		$bolnum="NEXTVAL('boletines_bolnum_seq')";
		$currval="CURRVAL('boletines_bolnum_seq')";
		$bolfec='current_timestamp';
	}	

	
	pg_query("INSERT INTO boletines VALUES (
		$bolnum,
		$bolfec,
		$monto,
		'$observa',
		1,
		0,
		$crecod, ".$_SESSION['sgh_usuario_id']."	
	);");
	
	if($cheques)
	for($i=0;$i<sizeof($cheques);$i++) {

		$c=explode('|',$cheques[$i]);
		
		if($c[2]=='')		
			$c[2]=date('d/m/Y');
		else {
			switch(strlen($c[2])) {			
				case 8:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/'.substr($c[2],4,5);
				break;
				case 6:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/20'.substr($c[2],4,2);
				break;
			}
		}		
		
		pg_query("INSERT INTO cheques VALUES (
			DEFAULT,
			$currval,
			'".pg_escape_string($c[0])."',		
			'".pg_escape_string($c[1])."',		
			'".pg_escape_string($c[2])."',		
			'".pg_escape_string($c[3])."',		
			".pg_escape_string($c[4]).",		
			'".pg_escape_string($c[5])."'		
		);");
	}

	if($otras)
	for($i=0;$i<sizeof($otras);$i++) {

		$c=explode('|',$otras[$i]);
		
		if($c[2]=='')		
			$c[2]=date('d/m/Y');
		else {
			switch(strlen($c[2])) {			
				case 8:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/'.substr($c[2],4,5);
				break;
				case 6:
				$c[2]=substr($c[2],0,2).'/'.substr($c[2],2,2).'/20'.substr($c[2],4,2);
				break;
			}
		}		
		
		pg_query("INSERT INTO forma_pago VALUES (
			DEFAULT,
			$currval,
			".(pg_escape_string($c[0])*1).",		
			'".pg_escape_string($c[1])."',		
			'".pg_escape_string($c[2])."',		
			".(pg_escape_string($c[3])*1)."		
		);");
	}
	
	
	$cuotas=cargar_registros_obj("SELECT * FROM cuotas 
											WHERE crecod=".$crecod." ORDER BY cuonum");
											
	$descontar=$monto;
	
	for($i=0;$i<count($cuotas);$i++) {
	
		$pag=$cuotas[$i]['cuopag']*1;
		$mon=$cuotas[$i]['cuomon']*1;		
		
		//print('mon:'.$mon.'pag:'.$pag);		
		
		if($pag<$mon) {

			if($descontar>=($mon-$pag)) $desc=($mon-$pag);
			else						$desc=$descontar;
			
			$q="	UPDATE cuotas 
					SET cuofecpag=current_timestamp, 
					cuopag=((COALESCE(cuopag,'0')::bigint)+$desc)::text,
					bolnum=$currval 
					WHERE cuoid=".$cuotas[$i]['cuoid'];
					
			//print($q);			
			
			pg_query($q);

			$descontar-=$desc;
			if($descontar<=0) break;
			
		}
	}

	$c=cargar_registro("SELECT * FROM creditos WHERE crecod=$crecod");

	$chk=cargar_registro("SELECT SUM(cuopag::bigint) AS pago FROM cuotas WHERE crecod=$crecod");
	
	if( $chk['pago']*1 >= ($c['cretot']*1+$c['crepie']*1) ) {
		pg_query("UPDATE creditos SET cretip='CP' WHERE crecod=$crecod;");
		pg_query("UPDATE uso_sepultura SET crecod=0 WHERE us_vigente AND crecod=$crecod");	
	} else {
		pg_query("UPDATE creditos SET cretip='$vigencia' WHERE crecod=$crecod;");
		if($vigencia!='N')	
			pg_query("UPDATE uso_sepultura SET crecod=0 WHERE us_vigente AND crecod=$crecod");
	}	
	
	$boletin=cargar_registro("SELECT $bolnum AS bolnum;");		
	
	pg_query('COMMIT;');
	
	print(json_encode(array(true,$boletin['bolnum'])));

?>
