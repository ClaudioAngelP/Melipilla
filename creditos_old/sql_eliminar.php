<?php 

	require_once('../conectar_db.php');
	
	$bolnum=$_POST['bolnum'];
	
	if(!_cax(13)) {
	
		die('ACCESO NO AUTORIZADO.');	
	
	}
	
	$bol=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=$bolnum");
	
	if($bol['crecod']*1!=0)
		$cre=cargar_registro("SELECT * FROM creditos WHERE crecod=".$bol['crecod']);
	else
		$cre=false;
		
	if($cre) {
		$bols=cargar_registros_obj("SELECT *, bolfec::date AS bolfec FROM boletines WHERE crecod=".$bol['crecod']." ORDER BY bolnum");
		$cuos=cargar_registros_obj("SELECT * FROM cuotas WHERE crecod=".$bol['crecod']);	
	} else {
		$bols=false;
		$cuos=false;
	}

	pg_query("START TRANSACTION;");	
	
	pg_query("DELETE FROM boletines WHERE bolnum=$bolnum");
	pg_query("DELETE FROM forma_pago WHERE bolnum=".$bol['bolnum']);
	pg_query("DELETE FROM descuentos WHERE bolnum=".$bol['bolnum']);
	
	if($cre) {
		pg_query("DELETE FROM boletines WHERE crecod=".$bol['crecod']);
		pg_query("DELETE FROM creditos WHERE crecod=".$bol['crecod']);
		pg_query("DELETE FROM cuotas WHERE crecod=".$bol['crecod']);	
		for($i=0;$i<sizeof($bols);$i++) {
			pg_query("DELETE FROM forma_pago WHERE bolnum=".$bols[$i]['bolnum']);
			pg_query("DELETE FROM descuentos WHERE bolnum=".$bols[$i]['bolnum']);
		}
	}
	
	pg_query("COMMIT;");

?>

<script>

	alert('ELIMINADO EXITOSAMENTE.');
	window.close();

</script>