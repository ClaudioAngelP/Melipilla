<?php
	require_once('../../conectar_db.php');
	$id_talonario = $_POST['talonario_id'];
	$tipo_talonario = $_POST['tipo_talonario_2'];
	$nro_talonario = $_POST['nro_talonario']*1;
	$func_id = $_POST['func_id'];
	$nro_inicial = $_POST['nro_inicial'];
	$nro_final = $_POST['nro_final'];
	$estado = $_POST['_estado_talonario'];
	$servicio =$_POST['centro_ruta'];
	function comprobar_receta_existe($nro_receta) {
		GLOBAL $recetas;
		for($u=0;$u<count($recetas);$u++) {
			if($recetas[$u]['receta_numero']==$nro_receta) return 1;
		}
		return 0;
	}
  
	$recetas = cargar_registros('
	SELECT * FROM receta WHERE 
	receta_tipotalonario_id='.$tipo_talonario.' AND
	receta_numero>='.$nro_inicial.' AND 
	receta_numero<='.$nro_final.'
	', true);
  
	//list($estado, $error) = validez_talonario($tipo_talonario, $nro_talonario, $nro_inicial, $nro_final);
  
	//if(!$estado) die(json_encode(Array(false, $error)));
	if($id_talonario>0) {
		pg_query($conn, "
		UPDATE talonario SET
		talonario_func_id=$func_id,
		talonario_tipotalonario_id=$tipo_talonario,
		talonario_numero=$nro_talonario,
		talonario_inicio=$nro_inicial,
		talonario_final=$nro_final,
		talonario_estado=$estado,
		talonario_centro_ruta='$servicio'
		WHERE talonario_id=$id_talonario 
		");
	} else {
		pg_query("INSERT INTO talonario VALUES (DEFAULT, $func_id, $tipo_talonario, $nro_talonario, $nro_inicial, $nro_final, $estado, CURRENT_TIMESTAMP, null, '$servicio', null,".($_SESSION['sgh_usuario_id']*1)."); ");
	}
	// talonario_id | talonario_func_id | talonario_tipotalonario_id | talonario_numero | talonario_inicio | talonario_final | talonario_estado |      talonario_fecha       | talonario_bod_id |                          talonario_centro_ruta                          | talonario_pedidod_id
	if($id_talonario!=0)
		for($i=$nro_inicial;$i<=$nro_final;$i++) {
			$act = comprobar_receta_existe($i);
			if($act==0) {
				if(!isset($_POST['check_receta_'.$i])) {
					$ex = pg_query($conn, 'SELECT * FROM receta_anulada WHERE receta_tipotalonario_id='.$tipo_talonario.' AND receta_numero='.$i);
					if(pg_num_rows($ex)==0)
						pg_query($conn, "INSERT INTO receta_anulada VALUES (".$tipo_talonario.",".$i.",'".$_POST['causal_receta_'.$i]."');");
					else
						pg_query($conn, "UPDATE receta_anulada SET causal='".$_POST['causal_receta_'.$i]."' WHERE receta_tipotalonario_id=".$tipo_talonario." AND receta_numero=".$i);
					} else {
						pg_query($conn, 'DELETE FROM receta_anulada WHERE receta_tipotalonario_id='.$tipo_talonario.' AND receta_numero='.$i);
					}
			}    
		}
	die(json_encode(Array(true, 'OK')));
?>
