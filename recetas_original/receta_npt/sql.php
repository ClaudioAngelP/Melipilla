<?php 

	require_once('../../conectar_db.php');
	
	$npt_id=$_POST['rnpt_id'];
	
	$fecha=pg_escape_string($_POST['rnpt_fecha']);
	$fecha2=pg_escape_string($_POST['rnpt_fecha2']);
	$fecha_emision=pg_escape_string($_POST['rnpt_fecha_emision']);
	$modificar=$_POST['modificar'];
	
	$pac_id=$_POST['pac_id']*1;
	$doc_id=$_POST['doc_id']*1;
	
	$centro_ruta=pg_escape_string($_POST['centro_servicio']);

	$bajada=pg_escape_string($_POST['tipo_bajada']);
	
	$peso=$_POST['peso']*1;
	
	$diag_cod=pg_escape_string($_POST['nomd_diag_cod']);
	$diagnostico=pg_escape_string(utf8_decode($_POST['nomd_diagnostico']));
	
	$tipo_receta=pg_escape_string(utf8_decode($_POST['tipo_receta']));
	
	if($tipo_receta=='')
		$volumen=$_POST['campo_37']*1;
	else {
		$tmp=explode('|', $tipo_receta);
		$tipo_receta=$tmp[0]."|1|UD\n";
		$volumen=$tmp[1]*1;
	}
	
	$f=cargar_registros_obj("SELECT * FROM receta_formatos ORDER BY rf_id;", true);

	$detalle='';

	if($tipo_receta=='') {
		for($i=0;$i<sizeof($f);$i++) {
			if(($_POST['campo_'.$f[$i]['rf_id']]*1)==0) continue;
			$detalle.=$f[$i]['rf_compuesto'].'|'.($_POST['campo_'.$f[$i]['rf_id']]*1).'|'.$f[$i]['rf_unidad'].'|'.$f[$i]['rf_osmolaridad']."\n";
		}
	} else {
		$detalle=$tipo_receta;
	}
	
	$f=explode('/',$fecha);
	$fi=mktime(0,0,0,$f[1], $f[0], $f[2]);
	$f=explode('/',$fecha2);
	$ff=mktime(0,0,0,$f[1], $f[0], $f[2]);
	$resp='';
	
	if($modificar!=1){
		for($i=$fi;$i<=$ff;$i+=86400) {
	
			$fecha=date('d/m/Y', $i);
			
			pg_query("INSERT INTO receta_npt (rnpt_id,rnpt_fecha_emision,rnpt_func_id,pac_id,doc_id,rnpt_detalle,
			centro_ruta,rnpt_volumen_total,rnpt_peso_gr,rnpt_diag_cod,rnpt_diagnostico,rnpt_tipo_bajada)
			VALUES (DEFAULT, '$fecha', ".$_SESSION['sgh_usuario_id'].", $pac_id, $doc_id, 
			'$detalle', '$centro_ruta', $volumen, $peso, '$diag_cod', '$diagnostico', '$bajada');");
			
			
			$npt_id=cargar_registro("SELECT CURRVAL('receta_npt_rnpt_id_seq')AS id;");
			$resp.=$npt_id['id'].'|'.$fecha.';';
			
		}
	}else{
		pg_query("UPDATE receta_npt SET
							rnpt_fecha_emision='$fecha_emision', 
							pac_id=$pac_id, doc_id=$doc_id, 
							rnpt_detalle='$detalle', centro_ruta='$centro_ruta', 
							rnpt_volumen_total=$volumen, rnpt_peso_gr=$peso, 
							rnpt_diag_cod='$diag_cod', rnpt_diagnostico='$diagnostico', 
							rnpt_tipo_bajada='$tipo_bajada'
						WHERE rnpt_id=$npt_id;");
				$resp=$npt_id.'|'.$fecha_emision.';';	
	}
	$centro=cargar_registro("SELECT centro_nombre FROM centro_costo WHERE centro_ruta='".$centro_ruta."'");
	//print($modificar);
	print(trim($resp,';')); //ESTELA abastecimiento... correo dudas gestion de camas o llamar a LUIS 
	
?>
