<?php

	require_once('../../conectar_db.php');
	
	function fixstr($str) {
		return pg_escape_string(str_replace("\n",'<br>',utf8_decode($str)));
	}

	$fap_id=$_POST['fap_id']*1;
	$fcl_id=$_POST['selector']*1;

	$c=cargar_registro("SELECT * FROM fap_checklist WHERE fcl_id=$fcl_id");

	$campos=explode("\n",$c['fcl_campos']);

	$data='';

	for($i=0;$i<sizeof($campos);$i++) {

		if(trim($campos[$i])=='') continue;
		
		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=($cmp[0]); $tipo=$cmp[1]*1;
		} else {
			$cmp=$campos[$i]; $tipo=0;
			$nombre=$campos[$i];
		}

		if($tipo==0 OR $tipo==1){
			if(isset($_POST['campo_'.$i]) AND $_POST['campo_'.$i]=='S')
				$data.=$nombre."|S|$tipo|\n";
			elseif(isset($_POST['campo_'.$i]) AND $_POST['campo_'.$i]=='N')
				$data.=$nombre."|N|$tipo|\n";
			else
				$data.=$nombre."||$tipo|\n";
			//$data.=$nombre.'|'.((isset($_POST['campo_'.$i]) AND $_POST['campo_'.$i]=='S')?'S':'N')."|$tipo\n";
		}else if($tipo==6) {
			
			$opts=explode('//',$cmp[2]);
			$datos='';
			
			for($k=0;$k<sizeof($opts);$k++)
				if(isset($_POST['campo_'.$i.'_'.$k]))
					$datos.=$opts[$k].'//';
					
			$datos=trim($datos,'//');
			
			$data.=$nombre.'|'.fixstr($datos)."|$tipo\n";
			
		} else if($tipo!=20)
			$data.=$nombre.'|'.fixstr($_POST['campo_'.$i])."|$tipo\n";
		else if($tipo==20)
			$data.=$nombre."||$tipo\n";

	}

	$func_id=$_SESSION['sgh_usuario_id']*1;

	pg_query("INSERT INTO fap_checklist_detalle VALUES (DEFAULT, $fap_id, $fcl_id, CURRENT_TIMESTAMP, $func_id, '$data');");

?>
