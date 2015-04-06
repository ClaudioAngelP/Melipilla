<?php 

	require_once('../conectar_db.php');
	
	$monr_id=$_POST['monr_id']*1;
	$observaciones=pg_escape_string(utf8_decode($_POST['in_comentarios']));

	$m=cargar_registro("SELECT *,
                                                 (CURRENT_TIMESTAMP::date-monr_fecha::date)::integer AS dias,
                                                 (SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut LIMIT 1) AS pac_id
                                                 FROM monitoreo_ges_registro
                                                 JOIN monitoreo_ges USING (mon_id)
                                                 LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
                                                 LEFT JOIN patologias_auge USING (pst_patologia_interna)
                                                 WHERE monr_id=$monr_id");

	$mon_id=$m['mon_id']*1;


	$lista_id=$m['monr_subclase'];
		
	$li=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$lista_id';");	
	
	$campos=explode('|', $li['lista_campos_tabla']);

	$valores=''; $_fevento='';
	
	for($i=0;$i<sizeof($campos);$i++) {

		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
		} else {
			$cmp=$campos[$i]; $tipo=2;
		}

		if($tipo==3) {
			$_fevento=pg_escape_string(trim($_POST['campo_'.$i]));
		} elseif($tipo==0 OR $tipo==1) {
			$valores.=(isset($_POST['campo_'.$i]))?'true':'false';	
		} else {
			$valores.=pg_escape_string(utf8_decode($_POST['campo_'.$i]));
		}
		
		if($i<(sizeof($campos)-1)) $valores.='|';	
				
	}
	
	$id_condicion=$m['monr_clase'];
	$id_bandeja=$m['monr_subclase'];
	$fevento=$m['monr_fecha_evento'];
	if($fevento=='') $fevento='null'; else $fevento="'$fevento'";
	$cual=pg_escape_string($m['monr_subcondicion']);
	
//	pg_query("INSERT INTO monitoreo_ges_registro (mon_id, monr_fecha, monr_clase, monr_subclase, monr_fecha_evento, monr_subcondicion, monr_func_id, monr_observaciones, monr_valor, monr_estado) VALUES ($mon_id, CURRENT_TIMESTAMP, '$id_condicion', '$id_bandeja', $fevento, '$cual', ".$_SESSION['sgh_usuario_id'].", '$observaciones', '$valores', 1);"); 

	pg_query("START TRANSACTION;");

	pg_query("UPDATE monitoreo_ges_registro SET monr_valor='$valores', monr_estado=1 WHERE monr_id=$monr_id;");

	pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0");
	
	$val=pg_escape_string($_POST['sel_estado']);
	
	if($val=='0') {
		$clase=$id_condicion;
		$subclase=$id_bandeja; 
	} else {
		list($clase, $subclase)=explode('|', $val);
		if($clase=='0') $clase=$id_condicion;
		if($subclase=='') $subclase=$id_bandeja;
	}

	 if($clase=='') $clase=$id_condicion;

	if($_fevento!='') $fevento="'$_fevento'";

	pg_query("INSERT INTO monitoreo_ges_registro (mon_id, monr_fecha, monr_clase, monr_subclase, monr_fecha_evento, monr_subcondicion, monr_func_id, monr_observaciones, monr_valor, monr_estado) VALUES ($mon_id, CURRENT_TIMESTAMP, '$clase', '$subclase', $fevento, '$cual', ".$_SESSION['sgh_usuario_id'].", '$observaciones', '', 0);");
	pg_query("update monitoreo_ges_registro set monr_fecha_evento=foo.monr_fecha::date from (
                        select monr_id, monr_fecha from monitoreo_ges_registro where monr_id=CURRVAL('monitoreo_ges_registro_monr_id_seq') AND
                        monr_clase IN (SELECT id_condicion::text FROM lista_dinamica_condiciones WHERE digitacion_evento)
                        ) AS foo WHERE monitoreo_ges_registro.monr_id=foo.monr_id;");		

	pg_query("COMMIT;");

?>
