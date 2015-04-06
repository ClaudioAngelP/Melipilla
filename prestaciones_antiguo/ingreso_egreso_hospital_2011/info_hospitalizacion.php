<?php 

	require_once('../../conectar_db.php');
	
	$nro_folio=$_POST['nro_folio']*1;
	$anio=$_POST['anio']*1;

	function cnv($arr, $flds) {

		// Funcion convierte el array devuelto por "cargar_registros_obj()"
		// en un formato legible para que el script en el formulario
		// pueda listar los registros correctamente con la función "redibujar()".

		if(!$arr) return array();

		$a=array();
		
		for($i=0;$i<sizeof($arr);$i++) {
			
			$n=sizeof($a);
			$a[$n]=array();			
			
			for($j=0;$j<sizeof($flds);$j++) 
				$a[$n][$j]=$arr[$i][$flds[$j]];
			
		}
		
		return $a;
		
	}
	
	$h=cargar_registros_obj("
									SELECT * FROM hospitalizacion 
										LEFT JOIN doctores ON hosp_doc_id=doc_id
										LEFT JOIN instituciones ON hosp_inst_id=inst_id
										JOIN pacientes ON hosp_pac_id=pac_id
										LEFT JOIN centro_costo ON hosp_centro_ruta=centro_ruta
									 WHERE  EXTRACT(YEAR FROM hosp_fecha_egr) ='".$anio."' AND hosp_folio=".$nro_folio, true
								  );

	if($h) { 
	
		$h=$h[0];	
	
		$hosp_id=$h['hosp_id'];	
	
		$h['diag1']=cnv(cargar_registros_obj("
			SELECT * FROM paciente_diagnostico JOIN diagnosticos USING (diag_cod) WHERE hosp_id=$hosp_id AND pdiag_tipo=1	
		", true), array('diag_cod','diag_desc'));
		
		$h['diag2']=cnv(cargar_registros_obj("
			SELECT * FROM paciente_diagnostico JOIN diagnosticos USING (diag_cod) WHERE hosp_id=$hosp_id AND pdiag_tipo=0		
		", true), array('diag_cod','diag_desc'));	
		
		$h['traslados']=cnv(cargar_registros_obj("
			SELECT *, ptras_fecha::date AS ptras_fecha FROM paciente_traslado JOIN centro_costo USING (centro_ruta) WHERE hosp_id=$hosp_id		
		", true), array('centro_ruta','centro_nombre','ptras_fecha'));		
		
		$h['prestaciones']=cnv(cargar_registros_obj("
			SELECT *, presta_fecha::date AS presta_fecha FROM prestacion JOIN codigos_prestacion ON presta_codigo=codigo WHERE hosp_id=$hosp_id		
		", true), array('presta_codigo','glosa','presta_fecha'));

		$h['partos']=cnv(cargar_registros_obj("
			SELECT * FROM hospitalizacion_partos WHERE hosp_id=$hosp_id ORDER BY hospp_orden		
		", true), array('hospp_condicion','hospp_sexo','hospp_peso_gramos','hospp_apgar'));
		
	}
	
	echo json_encode($h);

?>