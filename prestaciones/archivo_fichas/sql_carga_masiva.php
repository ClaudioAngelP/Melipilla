<?php

	require_once('../../conectar_db.php');

function findexts ($filename) 
 { 
 $filename = strtolower($filename) ; 
 $exts = split("[/\\.]", $filename) ; 
 $n = count($exts)-1; 
 $exts = $exts[$n]; 
 return $exts; 
 } 

	if(!isset($_FILES['archivo'])) {
		exit("ERROR AL LEER ARCHIVO.");
	}

	$fname=date('YmdHis');

	echo 'PESO: <b>'.number_format(filesize($_FILES['archivo']['tmp_name'])/1024,2,',',',').' kB</b> - ';

	$ext=findexts($_FILES['archivo']['name']);

	echo 'FORMATO: <b>'.$ext."</b>";

	//echo exec(dirname(__FILE__).'/xlsx2csv.py -d ^ '.$_FILES['archivo']['tmp_name'].' '.dirname(__FILE__).'/log/'.$fname.'.csv');

	if($ext=='xls') {

			exit(" --- ERROR: FORMATO NO RECONOCIDO.");

	echo exec('cp '.$_FILES['archivo']['tmp_name'].' '.dirname(__FILE__).'/log/'.$fname.'.xls');

	echo exec('xls2csv -f%d/%m/%Y -q0 -c\| '.$_FILES['archivo']['tmp_name'].' > '.dirname(__FILE__).'/log/'.$fname.'.csv');

	} else if($ext=='xlsx') {

			exit(" --- ERROR: FORMATO NO RECONOCIDO.");

	echo exec('cp '.$_FILES['archivo']['tmp_name'].' '.dirname(__FILE__).'/log/'.$fname.'.xlsx');

	echo exec(dirname(__FILE__).'/xlsx2csv.py -f %d/%m/%Y -d \| '.$_FILES['archivo']['tmp_name'].' '.dirname(__FILE__).'/log/'.$fname.'.csv');

	} else if($ext=='csv') {

        echo exec('cp '.$_FILES['archivo']['tmp_name'].' '.dirname(__FILE__).'/log/'.$fname.'.csv');

        } else {

		exit(" --- ERROR: FORMATO NO RECONOCIDO.");

	}

	$csv=explode("\n", file_get_contents('log/'.$fname.'.csv'));

	print("  [  ".sizeof($csv)." LINEAS  ] <br/><br /><b><u>RESULTADO POR L&Iacute;NEA:</u></b><br/>");
	$meses=Array('ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic');

	pg_query("START TRANSACTION;");

	for($i=0;$i<sizeof($csv);$i++) {

	print(($i+1).') ');

	if($ext=='xls' OR $ext=='xlsx') {
	
		$r=explode('|', $csv[$i]);
		
		$dias_inicio=round(date('U',mktime(0,0,0,1,1,1900))/86400);
		
		if($ext=='xls') {
			$fecha=date('d/m/Y',mktime(0,0,0,0,$dias_inicio+($r[3]*1)));
			$hora=date('H:i',mktime(0,0,$r[6]*86400));
		} else {
			$fecha=$r[3];
			$hora=$r[6];
		}
	} else {
	
		$r=explode(';', $csv[$i]);
		for($j=0;$j<sizeof($r);$j++) 
			$r[$j]=trim($r[$j],'"');
		$fecha=strtolower($r[3]);
		for($j=0;$j<sizeof($meses);$j++)
			$fecha=str_replace($meses[$j], ''.($j+1), $fecha);
		$fecha=str_replace('-', '/', $fecha);
		$hora=$r[6];
		$tmphora=explode(':',$hora);
		if(strstr($hora,'PM')) 
			$hora=(($tmphora[0]*1)+12).':'.$tmphora[1];
		else
			$hora=$tmphora[0].':'.$tmphora[1];
			
	}

	$rut=strtoupper(trim($r[8]));
	$ficha=trim(ltrim($r[9],'0'));
	$pabellon=utf8_decode('PABELLÓN ').$r[5];
	$cirujano=$r[4];

	if(!strstr($fecha,'/') OR $rut=='' OR $cirujano=='' OR $r[5]=='') {
		print(" [ <i>IGNORADO, FALTAN DATOS</i> ]<br/>");
		continue;
	}

	print(htmlentities(" < $fecha $hora > [$pabellon] [$cirujano] ").'<b>'.$rut.' / '.$ficha.'</b> '); 

	$pac=false;
	
	if($rut!='' AND strlen($rut)>5)
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$rut' ORDER BY pac_id;");
	
	if($ficha!='' AND !$pac)
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_ficha='$ficha' ORDER BY pac_id;");

	if(!$pac) {
		print(" [ <i>PACIENTE NO EXISTE</i> ] <br/>");
		continue;
	}
	
	$pac_id=$pac['pac_id']*1;

	if($ficha!='' AND $pac['pac_ficha']=='') {
		pg_query("UPDATE pacientes SET pac_ficha='$ficha' WHERE pac_id=$pac_id;");
	}
	
	$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='$pabellon';");

	if($esp)
		$esp_id=$esp['esp_id']*1;
	else {
		pg_query("INSERT INTO especialidades VALUES (DEFAULT, '$pabellon');");
		$esp_id="CURRVAL('especialidades_esp_id_seq')";

	}

	$doc=cargar_registro("SELECT * FROM doctores WHERE doc_nombres='$cirujano';");

	if($doc)
		$doc_id=$doc['doc_id']*1;
	else {
		pg_query("INSERT INTO doctores VALUES (DEFAULT, '', '', '', '$cirujano');");
		$doc_id="CURRVAL('doctores_doc_id_seq')";
	}

	$nom=cargar_registro("SELECT * FROM nomina WHERE nom_fecha='$fecha' AND nom_esp_id=$esp_id AND nom_doc_id=$doc_id");

	if($nom) {

		$nom_id=$nom['nom_id']*1;

	} else {

			pg_query("INSERT INTO nomina VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$fecha');");

			pg_query("
				INSERT INTO cupos_atencion VALUES (
				DEFAULT,
				$esp_id, $doc_id,
				'$fecha', '00:00:00', '00:00:00',
				0, 0, 0, 0, 0, true, 0, CURRVAL('nomina_nom_id_seq')
				)
			  ");
			  
			  $nom_id="CURRVAL('nomina_nom_id_seq')";
	}

	$chk=cargar_registro("SELECT * FROM nomina_detalle WHERE nom_id=$nom_id AND pac_id=$pac_id;");

	if(!$chk) {

		$func_id=$_SESSION['sgh_usuario_id'];
		

		pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna) 
	  VALUES (DEFAULT, $nom_id, 'N', 'N', '$hora', $pac_id, '', $func_id, CURRENT_TIMESTAMP);");

	} else {

		print(" <i>[ REGISTRO REPETIDO ]</i><br/>");
		continue;

	}

	print("<br/>");	

	}

	pg_query("COMMIT;");

	print("<br/><br/><b><u>TERMINADO.</u></b>");

?>
