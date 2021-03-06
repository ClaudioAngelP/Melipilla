<?php 

	require_once('../../conectar_db.php');
	
	$planilla=utf8_decode(file_get_contents($_FILES['planilla']['tmp_name']));
	$archivo=explode("\n",$planilla);

	for($i=1;$i<sizeof($archivo);$i++) {

		if(trim($archivo[$i])=='') continue;
		
		$fila=explode(';',$archivo[$i]);
		
		$codigo=str_replace(".",'',$fila[0]);
		
		if(strstr($codigo,'/')) {
			$cod=substr($codigo,1,2).''.substr($codigo,4,2);
		} else $cod=$codigo; 
		
		$codigo=$cod*1;	
		
		if($codigo==0) continue;
		
		$nombre=pg_escape_string($fila[1]);
		
		$req=str_replace(".",'',str_replace(",",'',$fila[2]))*1;
		$com=str_replace(".",'',str_replace(",",'',$fila[3]))*1;
		$dis=str_replace(".",'',str_replace(",",'',$fila[4]))*1;
		$dev=str_replace(".",'',str_replace(",",'',$fila[5]))*1;
		$pag=str_replace(".",'',str_replace(",",'',$fila[7]))*1;
	
		
		$chk=cargar_registro("SELECT * FROM item_presupuestario_sigfe
			WHERE item_codigo='$codigo'");
			
		if(!$chk) {	
		
			pg_query("INSERT INTO item_presupuestario_sigfe VALUES (
			'$codigo',
			'$nombre',
			$req, $com, $dis, $dev, $pag
			);");
			
			print("[$codigo] INSERTADO...<br><br>");
		
		} else {
		
			pg_query("UPDATE item_presupuestario_sigfe SET
				item_nombre='$nombre',
				item_requerimiento=$req,
				item_compromiso=$com,
				item_disponibilidad=$dis,
				item_devengado=$dev,
				item_pagado=$pag
			WHERE item_codigo='$codigo'");

			print("[$codigo] ACTUALIZADO...<br><br>");
			
		}

	}

?>
