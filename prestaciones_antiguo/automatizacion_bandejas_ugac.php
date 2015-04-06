<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	//error_reporting(E_ALL);

	$inst_id=$sgh_inst_id;

	echo "AUTOMATIZACION DE BANDEJAS WORKFLOW ".date('d/m/Y H:i:s')."\n\n";
  
  $csv='';	
	
	// CRUZA CONTRA PACIENTES BRECHA REAL FALTA TRATAMIENTO (10), EN TABLA QUIRURGICA (14), PENDIENTE POR PRESTADOR (21), PACIENTE PREPARADO (42)...
	
	$d=pg_query("
		
		SELECT 
		monitoreo_ges_registro.mon_id AS real_mon_id, monitoreo_ges_registro.monr_id AS real_monr_id, 
		mon_rut, mon_nombre, 
		pst_patologia_interna, pst_garantia_interna, 
		codigos_iq,
		pacientes.pac_id, nombre_condicion, fap_fecha::date, fap_fecha::time AS fap_hora, fappr_codigo, fappr_cantidad,
		monr_clase, monr_subclase, mon_fecha_inicio, mon_fecha_limite, monr_fecha_evento
		FROM monitoreo_ges_registro 
		JOIN monitoreo_ges USING (mon_id)
		JOIN lista_dinamica_condiciones ON monr_clase=id_condicion::text
		JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
		JOIN pacientes on pac_rut=mon_rut
		JOIN fap_pabellon ON fap_pabellon.pac_id=pacientes.pac_id AND fap_fecha>=monr_fecha
		JOIN fap_prestacion USING (fap_id)
		WHERE NOT mon_estado AND monr_estado=0 AND monr_clase IN ('10', '14', '21', '42')
		AND (codigos_iq ILIKE '%' || fappr_codigo || '%')
		ORDER BY mon_id, fap_fecha;



	");
	
	//nomd_diag_cod NOT IN ('X', 'T') AND
	
	$g=array();

	while($r=pg_fetch_assoc($d)) {
		
		if(!isset($g[$r['real_monr_id']])) {
			$g[$r['real_monr_id']]=array();
			$g[$r['real_monr_id']]=$r;
			$g[$r['real_monr_id']]['mon_id']=$r['real_mon_id'];
			$g[$r['real_monr_id']]['mon_rut']=$r['mon_rut'];
			$g[$r['real_monr_id']]['clase']=$r['monr_clase'];
			$g[$r['real_monr_id']]['subclase']=$r['monr_subclase'];
		}
				
	}
	
	//print_r($g); exit();
	
	pg_query("START TRANSACTION;");
	
	foreach($g AS $monr_id => $data) {
		
		$id_condicion=29;
		$id_bandeja=utf8_decode('F');
		$estado="IQ Realizada el ".$data['fap_fecha']." a las ".$data['fap_hora'];
		$fevento=$data['fap_fecha'];
		$cual='FAP';
		
		$comentarios=pg_escape_string(utf8_decode("GIS DETECTÓ I.Q. --- COD. PRESTACIÓN: ".$data['fappr_codigo']." --- ESTADO: ".$estado));

		// Actualizar Bandejas...
		$mon_id=$data['real_mon_id']*1;
       		$monr_id=$data['real_monr_id']*1;
		print("($mon_id / $monr_id) $comentarios <br/><br/>");
	
		$clase=$data['clase'];
		$subclase=$data['subclase'];
		
    $tmp=cargar_registro("SELECT * FROM lista_dinamica_condiciones WHERE id_condicion=$id_condicion");
    
        // Actualizar Monitoreo...
        $tmp=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE mon_id=$mon_id AND monr_estado=0;");

	// Vuelve a chequear si es que no fue modificado mientras se procesaba el registro...
        if($tmp['monr_id']!=$monr_id) continue;

	 $csv.=$mon_id.';'.$data['mon_rut'].';'.$data['mon_nombre'].';'.$data['mon_fecha_inicio'].';'.$data['mon_fecha_limite'].';'.$data['pst_patologia_interna'].';'.$data['pst_garantia_interna'].';'.$data['nombre_condicion'].';'.$data['monr_fecha_evento'].';'.$tmp['nombre_condicion'].';'.$fevento.";".$comentarios."\r\n";

        pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0;");

        pg_query("INSERT INTO monitoreo_ges_registro VALUES (
                                DEFAULT, $mon_id, 7, now(), '$id_condicion', '$id_bandeja', '$comentarios', null, 'AUTOMATIZACION PABELLON', '$cual', '$fevento'
                        );");

        pg_query("UPDATE lista_dinamica_caso SET monr_id=CURRVAL('monitoreo_ges_registro_monr_id_seq') WHERE monr_id=".$tmp['monr_id']);


	}

	pg_query("COMMIT;");
  
  file_put_contents('tmp_logs/log_ges_ugac_'.date('d-m-Y_h-i').'.csv',$csv);

?>
