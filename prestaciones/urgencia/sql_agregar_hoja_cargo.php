<?php 
    require_once('../../conectar_db.php');
    if(isset($_POST['urghc_id'])) {
        $urghc_id=$_POST['urghc_id']*1;
	pg_query("DELETE FROM urgencia_hoja_cargo WHERE urghc_id=$urghc_id;");
        exit();
    }
    $fap_id=$_POST['fap_id']*1;
    $art_id=pg_escape_string(utf8_decode($_POST['art_id']));
    $cantidad=$_POST['art_cantidad']*1;
    $func_id=$_SESSION['sgh_usuario_id']*1;
    pg_query("INSERT INTO urgencia_hoja_cargo VALUES (DEFAULT, $fap_id, CURRENT_TIMESTAMP, $func_id, $art_id, $cantidad);");
?>
