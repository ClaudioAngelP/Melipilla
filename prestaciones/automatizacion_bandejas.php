<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	//error_reporting(E_ALL);

	$inst_id=$sgh_inst_id;

	echo "AUTOMATIZACION DE BANDEJAS WORKFLOW ".date('d/m/Y H:i:s')."\n\n";
  
  $csv='';	
	
	// CRUZA CONTRA PACIENTES CITADOS(1), FALTA CONTROL(3), FALTA PROC/EX(5), NO CITADOS(7), NSP1(8), NSP2(9), CITACION CONFIRMADA(33), NO CONTACTADO (40)...
	
	$d=pg_query("
		
		SELECT 
		monitoreo_ges_registro.mon_id AS real_mon_id, monitoreo_ges_registro.monr_id AS real_monr_id, mon_rut, mon_nombre, 
		pst_patologia_interna, pst_garantia_interna, 
		codigos_sic,
		pacientes.pac_id, nombre_condicion, nom_fecha::date, nomd_hora, nomd_diag_cod, esp_cod_especialidad, esp_desc, doc_rut, doc_paterno, doc_materno, doc_nombres,
		monr_clase, monr_subclase, nomd_hora, mon_fecha_inicio, mon_fecha_limite,monr_fecha_evento
		FROM monitoreo_ges_registro 
		JOIN monitoreo_ges USING (mon_id)
		JOIN lista_dinamica_condiciones ON monr_clase=id_condicion::text
		JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
		JOIN pacientes on pac_rut=mon_rut
		JOIN nomina_detalle ON nomina_detalle.pac_id=pacientes.pac_id
		JOIN nomina ON nomina_detalle.nom_id=nomina.nom_id AND nom_fecha::date>=monr_fecha::date
		LEFT JOIN especialidades ON nom_esp_id=esp_id
		LEFT JOIN doctores ON nom_doc_id=doc_id
		WHERE NOT mon_estado AND monr_estado=0 AND 
		((monr_clase IN ('5', '7') AND monr_subclase='A') OR 
		monr_clase IN ('1', '3', '8', '9', '33', '40')) AND NOT monr_subclase='B' AND
		nomina.nom_id IS NOT NULL AND 
		((codigos_sic ILIKE '%' || esp_cod_especialidad || '%') OR (mon_cod_especialidad=esp_cod_especialidad))
		AND NOT (monr_clase = '8' AND (mon_fecha_limite-mon_fecha_inicio)<=40)
		ORDER BY mon_id, nom_fecha;
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
			$g[$r['real_monr_id']]['nsp']=0;
			$g[$r['real_monr_id']]['ok']=0;
			$g[$r['real_monr_id']]['canceladas']=0;
			$g[$r['real_monr_id']]['pendientes']=0;
			$g[$r['real_monr_id']]['fecha_evento_ok']='';			
			$g[$r['real_monr_id']]['fecha_evento_cit']='';
			$g[$r['real_monr_id']]['doctor_cit']='';			
			$g[$r['real_monr_id']]['poli_cit']='';
			$g[$r['real_monr_id']]['fecha_evento_nsp']='';			
		}
		
		if($r['nomd_diag_cod']=='NSP') {
			
      $g[$r['real_monr_id']]['nsp']++;			
			
			$g[$r['real_monr_id']]['fecha_evento_nsp']=$r['nom_fecha'];			
			
		} else if($r['nomd_diag_cod']=='X' OR $r['nomd_diag_cod']=='T')  {
			
			$g[$r['real_monr_id']]['canceladas']++;			
			
		} else if($r['nomd_diag_cod']=='') {
			
			$g[$r['real_monr_id']]['pendientes']++;			
			
			if($g[$r['real_monr_id']]['fecha_evento_cit']=='') {
				$g[$r['real_monr_id']]['fecha_evento_cit']=$r['nom_fecha'];			
				$g[$r['real_monr_id']]['hora_evento_cit']=substr($r['nomd_hora'],0,5);
				$g[$r['real_monr_id']]['doctor_cit']=$r['doc_paterno'].' '.$r['doc_materno'].' '.$r['doc_nombres'];
				$g[$r['real_monr_id']]['poli_cit']=$r['esp_desc'];			
			}

		} else {
			
			$g[$r['real_monr_id']]['ok']++;			
			
			if($g[$r['real_monr_id']]['fecha_evento_ok']=='') {
				$g[$r['real_monr_id']]['fecha_evento_ok']=$r['nom_fecha'];			
				$g[$r['real_monr_id']]['hora_evento_ok']=substr($r['nomd_hora'],0,5);			
			}
				
		}
		
	}
	
	//print_r($g); exit();
	
	pg_query("START TRANSACTION;");
	
	foreach($g AS $monr_id => $data) {
	
		$cual='';	

		if($data['ok']>0) {
			//$id_condicion=22;
			//$id_bandeja=utf8_decode('X');
			//$estado="PRESTACIÓN OTORGADA ".$data['fecha_evento_ok']." a las ".$data['hora_evento_ok'];
			//$fevento=$data['fecha_evento_ok'];
			$estado="";
			$fevento='';									
		} else if($data['pendientes']>0) {
			$id_condicion=1;
			$id_bandeja='R';
			$estado="CITADO ".$data['fecha_evento_cit']." a las ".$data['hora_evento_cit']." Profesional: ".$data['doctor_cit']." Poli: ".$data['poli_cit'];						
			$fevento="'".$data['fecha_evento_cit']."'";
		} else if($data['nsp']==1) {
			$id_condicion=8;
			$id_bandeja='A';
			$estado="NSP1";			
			$fevento="'".$data['fecha_evento_nsp']."'";
		} else if($data['nsp']==2) {
			$id_condicion=9;
			$id_bandeja='I';
			$estado="NSP2";						
			$fevento="'".$data['fecha_evento_nsp']."'";
		} else if($data['nsp']>=3) {
			$id_condicion=9;
			$id_bandeja='I';
			$estado="NSP".$data['nsp']." -- EXCEPCIÓN Y CORREO";						
			$fevento="'".$data['fecha_evento_nsp']."'";
		} else {
			// DEVOLVER AL ESTADO ANTERIOR 
			$tmp=cargar_registro("select * from monitoreo_ges_registro where mon_id=".$data['real_mon_id']." and monr_id<".$data['real_monr_id']." and monr_clase in ('3', '5', '7', '8') ORDER BY monr_fecha DESC LIMIT 1;");
			if($tmp) {
				$id_condicion=$tmp['monr_clase'];
				$id_bandeja='A';
				$cual=pg_escape_string($tmp['monr_subcondicion']);
				if($id_condicion=='8') $estado="NSP1";
				if($id_condicion=='7') $estado="NO CITADO";
				if($id_condicion=='5') $estado="FALTA EX/PROC";
				if($id_condicion=='3') $estado="FALTA CONTROL";
			} else {
				$id_condicion='7';
				$id_bandeja='A';
				$estado='NO CITADO';
			}
			$fevento='null';									
		}
		
		if($estado=="") continue;
		
		$comentarios=pg_escape_string(utf8_decode("GIS DETECTÓ CITACIONES --- ATENDIDO:".$data['ok']." NSP:".$data['nsp']." PENDIENTES:".$data['pendientes']." CANCELADAS:".$data['canceladas']." --- ESTADO: ".$estado));

		print("($mon_id / $monr_id) $comentarios <br/><br/>");

		// Actualizar Bandejas...
				
		$mon_id=$data['mon_id'];
		$clase=$data['clase'];
		$subclase=$data['subclase'];
		
		// Solo actualiza si hay cambios...
		if($clase*1==$id_condicion AND $subclase==$id_bandeja) continue;
		
		// Si esta con citacion confirmada no lo deja como citado...
		if($clase*1==33 AND $id_condicion==1) continue;

		if($clase*1==40 AND $id_condicion==1) continue;

    $tmp=cargar_registro("SELECT * FROM lista_dinamica_condiciones WHERE id_condicion=$id_condicion");
    $tmp2=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$id_bandeja';");
    
	 // Actualizar Monitoreo...
        $tmp=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE mon_id=$mon_id AND monr_estado=0;");

	// Vuelve a chequear si es que no fue modificado mientras se procesaba el registro...
	if($tmp['monr_id']!=$monr_id) continue;

	$csv.=$mon_id.';'.$data['mon_rut'].';'.$data['mon_nombre'].';'.$data['mon_fecha_inicio'].';'.$data['mon_fecha_limite'].';'.$data['pst_patologia_interna'].';'.$data['pst_garantia_interna'].';'.$data['nombre_condicion'].';'.$data['monr_fecha_evento'].';'.$tmp['nombre_condicion'].';'.$tmp2['nombre_bandeja'].';'.$fevento.";$estado;$comentarios\r\n";

        pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0;");

        pg_query("INSERT INTO monitoreo_ges_registro VALUES (
                                DEFAULT, $mon_id, 7, now(), '$id_condicion', '$id_bandeja', '$comentarios', null, 'AUTOMATIZACION SIDRA', '$cual', $fevento
                        );");

        pg_query("UPDATE lista_dinamica_caso SET monr_id=CURRVAL('monitoreo_ges_registro_monr_id_seq') WHERE monr_id=".$tmp['monr_id']);

	
	}

	pg_query("COMMIT;");
  
  file_put_contents('tmp_logs/log_ges_'.date('d-m-Y_h-i').'.csv',$csv);

?>
