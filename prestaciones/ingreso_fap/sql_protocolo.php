<?php

	require_once('../../conectar_db.php');

	function cnv($str) {
		return $str*1;
	}
	
	function cnv2($str) {
		return "'".pg_escape_string(utf8_decode($str))."'";
	}		
	
	$fap_id=$_POST['fap_id']*1;
	$protocolo=pg_escape_string(utf8_decode($_POST['informe']));
	$hallazgos=pg_escape_string(utf8_decode($_POST['informe2']));
	$indicaciones=pg_escape_string(utf8_decode($_POST['indicaciones']));
	$anestesia=pg_escape_string(utf8_decode($_POST['indicaciones2']));
	$presta = json_decode($_POST['presta']);

	pg_query("UPDATE fap_pabellon 
	SET 
	fapth_id=".cnv($_POST['fapth_id']).",
	fapta_id1=".cnv($_POST['fapta_id1']).",
	fapta_id2=".cnv($_POST['fapta_id2']).",
	fap_biopsia=".cnv($_POST['fap_biopsia']).",
	fap_entrega_ane=".cnv($_POST['fap_entrega_ane']).",
	fap_protocolo='$protocolo', 
	fap_hallazgos='$hallazgos', 
	fap_indicaciones='$indicaciones', 
	fap_indicaciones_anestesia='$anestesia' ,
	fap_diag_cod_1=".cnv2($_POST['fap_diag_cod_1']).",
	fap_diag_cod_2=".cnv2($_POST['fap_diag_cod_2']).",
	fap_diag_cod_3=".cnv2($_POST['fap_diag_cod_3']).",
	fap_diagnostico_1=".cnv2($_POST['fap_diagnostico_1']).",
	fap_diagnostico_2=".cnv2($_POST['fap_diagnostico_2']).",
	fap_diagnostico_3=".cnv2($_POST['fap_diagnostico_3']).",
	fap_suspension=".cnv2($_POST['fap_suspension'])."
	WHERE fap_id=$fap_id");
	
	
	
	if($_POST['cambia_presta']*1) {   			
   			
		pg_query("DELETE FROM fap_prestacion WHERE fap_id=$fap_id");   			
	   	//print_r($presta);

		$pac_id=$_POST['pac_id']*1;
		
	   for($i=0;$i<sizeof($presta);$i++) {
	   
			pg_query("INSERT INTO fap_prestacion VALUES (
				DEFAULT,
				$fap_id,
				'".pg_escape_string($presta[$i]->codigo)."',		
				".($presta[$i]->cantidad*1).", '".($presta[$i]->fappr_tipo)."'		
			);"); 

			$cant=($presta[$i]->cantidad*1);
						
			/*pg_query("UPDATE orden_atencion SET oa_fecha_salida=CURRENT_DATE, oa_motivo_salida=1, oa_justifica_salida='CRUCE CON REGISTRO DEL PABELLON ID $fap_id EL ' || (CURRENT_TIMESTAMP::text) || '.'
					FROM (
					SELECT oa_id FROM orden_atencion
					WHERE oa_pac_id=$pac_id AND oa_codigo='".$presta[$i]->codigo."' AND oa_motivo_salida=-1
					ORDER BY oa_fecha_recepcion LIMIT $cant
					) AS foo
					WHERE foo.oa_id=orden_atencion.oa_id;");*/
			
	   }
   
   }

?>
