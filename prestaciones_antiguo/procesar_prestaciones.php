<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	error_reporting(E_ALL);

	$inst_id=$sgh_inst_id;

	echo "Consolidando información de prestaciones... ".date('d/m/Y H:i:s')."\n\n";	
	
	$start_time=microtime(true);
	
	flush();
	
	if(isset($_GET['fecha'])) {
		$fecha=$_GET['fecha'];
		$fecha_w="nom_fecha::date='$fecha'";
		$fecha2_w="fap_fecha::date='$fecha'";
	} else { 
		$fecha=date('m/Y');
		$fecha_w="nom_fecha>'01/".date('m/Y',mktime(0,0,0,(date('m')*1)-2))."' AND nom_fecha<'01/".date('m/Y',mktime(0,0,0,(date('m')*1)+1))."'";
		$fecha2_w="fap_fecha>'01/".date('m/Y',mktime(0,0,0,(date('m')*1)-2))."' AND fap_fecha<'01/".date('m/Y',mktime(0,0,0,(date('m')*1)+1))."'";
	}
	
	echo 'Fecha: '.$fecha."\n\n";

	echo 'Fecha: '.$fecha_w."\n\n";
	echo 'Fecha: '.$fecha2_w."\n\n";
	
	flush();

	// CONSULTAS

	echo "CONSULTAS (".(microtime(true)-$start_time)." transcurridos.)...\n\n";	
	
	$presta=pg_query("
		SELECT *, nomina_detalle.pac_id AS presta_pac_id FROM nomina_detalle
		JOIN nomina USING (nom_id)
		LEFT JOIN procedimiento ON nom_esp_id=procedimiento.esp_id
		JOIN procedimiento_codigo ON nom_esp_id=procedimiento_codigo.esp_id
		LEFT JOIN prestacion ON porigen_id=1 AND porigen_num=nomd_id 
		WHERE $fecha_w AND prestacion.presta_id IS NULL AND
		nomd_diag_cod NOT IN ('NSP', 'XX', 'R', '') AND
		procedimiento.esp_id IS NULL
	");
	
	print("TOTAL: ".pg_num_rows($presta)." REGISTROS...\n\n");
	
	while($pr=pg_fetch_assoc($presta)) {
			
			$id=$pr['nomd_id']*1;
			
			$fecha=$pr['nom_fecha'];
			$pac_id=$pr['presta_pac_id']*1;
			$extra='false';
			$esp_id=$pr['nom_esp_id']*1;
		
			$codigo=$pr['pc_codigo'];
			$desc=$pr['pc_desc'];
			$tipo=1;
			$cantidad=1;
			
			pg_query("INSERT INTO prestacion VALUES (
				DEFAULT, '$fecha', $pac_id, -1, 
				1, $id, 
				'$codigo', '$codigo', $cantidad,
				$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 0, 
				0, 0		
			);");
						
	}		


	echo "PROCEDIMIENTOS Y EXAMENES (".(microtime(true)-$start_time)." transcurridos.)...\n\n";	

	// PROCEDIMIENTOS, EXAMENES
	
	$presta=pg_query("
		SELECT *, nomina_detalle.pac_id AS presta_pac_id FROM nomina_detalle_prestaciones
		JOIN nomina_detalle USING (nomd_id)
		JOIN nomina USING (nom_id)
		LEFT JOIN codigos_prestacion ON nomdp_codigo=codigo
		LEFT JOIN prestacion ON porigen_id=2 AND porigen_num=nomdp_id 
		WHERE $fecha_w AND prestacion.presta_id IS NULL AND
		nomd_diag_cod NOT IN ('NSP', 'XX', 'R', '')
	");

	print("TOTAL: ".pg_num_rows($presta)." REGISTROS...\n\n");
	
	while($pr=pg_fetch_assoc($presta)) {
			
			$id=$pr['nomdp_id']*1;
			
			$fecha=$pr['nom_fecha'];
			$pac_id=$pr['presta_pac_id']*1;
			$codigo=$pr['nomdp_codigo'];
			$cantidad=$pr['nomdp_cantidad'];
			$extra='false';
			$esp_id=$pr['nom_esp_id']*1;
		
			$desc=pg_escape_string($pr['glosa']);
			
			pg_query("INSERT INTO prestacion VALUES (
				DEFAULT, '$fecha', $pac_id, -1, 
				2, $id, 
				'$codigo', '$codigo', $cantidad,
				$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 0, 
				0, 0		
			);");
						
	}		
	
	echo "FAP DE PABELLON (".(microtime(true)-$start_time)." transcurridos.)...\n\n";	

	// FAP DE PABELLON	
	
	$presta=pg_query("
		SELECT *, fap_pabellon.pac_id AS presta_pac_id FROM fap_prestacion
		JOIN fap_pabellon USING (fap_id)
		LEFT JOIN codigos_prestacion ON fappr_codigo=codigo
		LEFT JOIN prestacion ON porigen_id=3 AND porigen_num=fappr_id 
		WHERE $fecha2_w	 AND prestacion.presta_id IS NULL
	");

	print("TOTAL: ".pg_num_rows($presta)." REGISTROS...\n\n");

	while($pr=pg_fetch_assoc($presta)) {
			
			$id=$pr['fappr_id']*1;
			
			$fecha=$pr['fap_fecha'];
			$pac_id=$pr['presta_pac_id']*1;
			$codigo=$pr['fappr_codigo'];
			$cantidad=$pr['fappr_cantidad'];
			$extra='false';
			$esp_id=-1;
			$desc=pg_escape_string($pr['glosa']);
			
			pg_query("INSERT INTO prestacion VALUES (
				DEFAULT, '$fecha', $pac_id, -1, 
				3, $id, 
				'$codigo', '$codigo', $cantidad,
				$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 0, 
				0, 0		
			);");
						
	}		

	echo "FAP DE URGENCIAS (".(microtime(true)-$start_time)." transcurridos.)...\n\n";	

	// FAP DE URGENCIAS	
	
	$presta=pg_query("
		SELECT * FROM fap_prestacion
		JOIN fap USING (fap_id)
		LEFT JOIN codigos_prestacion ON fappr_codigo=codigo
		LEFT JOIN prestacion ON porigen_id=4 AND porigen_num=fappr_id 
		WHERE $fecha2_w	 AND prestacion.presta_id IS NULL
	");

	print("TOTAL: ".pg_num_rows($presta)." REGISTROS...\n\n");

	while($pr=pg_fetch_assoc($presta)) {
			
			$id=$pr['fappr_id']*1;
			
			$fecha=$pr['fap_fecha'];
			$pac_id=$pr['fap_pac_id']*1;
			
			$codigo=$pr['fappr_codigo'];
			$cantidad=$pr['fappr_cantidad'];
			$extra='false';
			$esp_id=-1;
			$desc=pg_escape_string($pr['glosa']);
			
			pg_query("INSERT INTO prestacion VALUES (
				DEFAULT, '$fecha', $pac_id, -1, 
				4, $id, 
				'$codigo', '$codigo', $cantidad,
				$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 0, 
				0, 0		
			);");
						
	}		

	print("TERMINADO.");

?>
