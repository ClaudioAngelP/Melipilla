<?php 

	require_once('../conectar_db.php');
	
	$bolnum=$_POST['bolnum'];
	$motivo=pg_escape_string(($_POST['motivo']));
	
	if(!_cax(322)) {
	
		die('ACCESO NO AUTORIZADO.');	
	
	}
	
	$bol=cargar_registro("SELECT *, bolfec::date AS bolfec, (SELECT ac_id FROM apertura_cajas WHERE apertura_cajas.func_id=boletines.func_id AND bolfec BETWEEN ac_fecha_apertura AND COALESCE(ac_fecha_cierre, CURRENT_TIMESTAMP)) AS ac_id FROM boletines WHERE bolnum=$bolnum");
	$ac=cargar_registro("SELECT * FROM apertura_cajas WHERE ac_id=".$bol['ac_id']);
	
if($ac['ac_fecha_cierre']!='') {
 
?>

<script>
 
alert('ERROR:\n\nCIERRE DE CAJA YA FUE REALIZADO, NO ES POSIBLE ANULAR COMPROBANTE.');
window.close();

</script>

<?php exit(); 

} 

	
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
	
	//$sqlbdet = pg_query($conn, "SELECT bdet_codigo FROM boletin_detalle WHERE bolnum =".$bolnum)
	
	$bdetprestaid=cargar_registros_obj("SELECT bdet_presta_id FROM boletin_detalle WHERE bolnum =".$bolnum);
	//echo $bdet['bdet_codigo']; return;
	
	pg_query("UPDATE boletines SET anulacion='$motivo' WHERE bolnum=$bolnum");
	
		for( $i=0;$i<sizeof($bdetprestaid);$i++)
		{
			if($bdetprestaid[$i]['bdet_presta_id'] == NULL){
			
		
			}else{
			pg_query("UPDATE nomina_detalle SET nomd_pago =0 WHERE nomd_id =".$bdetprestaid[$i]['bdet_presta_id']);
			}
			
		
		}
	pg_query("UPDATE cuotas SET bolnum=0, cuopag=null, cuoest=null, cuofecpag=null WHERE bolnum=$bolnum");
	pg_query("UPDATE creditos SET  cretip='N' WHERE crecod=".$bol['crecod']);
	//pg_query("DELETE FROM forma_pago WHERE bolnum=".$bol['bolnum']);
	//pg_query("DELETE FROM descuentos WHERE bolnum=".$bol['bolnum']);
	
	/*if($cre) {
		pg_query("DELETE FROM boletines WHERE crecod=".$bol['crecod']);
		pg_query("DELETE FROM creditos WHERE crecod=".$bol['crecod']);
		pg_query("DELETE FROM cuotas WHERE crecod=".$bol['crecod']);	
		for($i=0;$i<sizeof($bols);$i++) {
			pg_query("DELETE FROM forma_pago WHERE bolnum=".$bols[$i]['bolnum']);
			pg_query("DELETE FROM descuentos WHERE bolnum=".$bols[$i]['bolnum']);
		}
	}*/
	
	pg_query("COMMIT;");

	$_GET['bolnum']=$bolnum;

	require_once('../ingresos/imprimir_boletin.php');

?>
