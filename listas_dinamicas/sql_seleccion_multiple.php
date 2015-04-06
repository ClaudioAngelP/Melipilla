<?php 

	require_once('../conectar_db.php');
	
	$cant=$_POST['cantidad']*1;
	$ids=explode('|',$_POST['ids']);
    
	$lista_id=pg_escape_string($_POST['lista_id']);
	$observaciones=pg_escape_string(utf8_decode($_POST['in_comentarios']));

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
                        $_fevento=pg_escape_string(trim($_POST['campo_'.$i.'_0']));
                } elseif($tipo==0 OR $tipo==1) {
			$valores.=(isset($_POST['campo_'.$i.'_0']))?'true':'false';	
		} else {
			$valores.=pg_escape_string(utf8_decode($_POST['campo_'.$i.'_0']));
		}
		
		if($i<(sizeof($campos)-1)) $valores.='|';	
				
	}
	
	print_r($ids);


	for($i=0;$i<sizeof($ids)-1 AND $i<$cant;$i++) {
				
		$monr_id=$ids[$i]*1;
		
		$mr=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE monr_id=$monr_id");

		$id_condicion=$mr['monr_clase'];
		$id_bandeja=$mr['monr_subclase'];

        	$fevento=$mr['monr_fecha_evento'];
	        if($fevento=='') $fevento='null'; else $fevento="'$fevento'";
       		$cual=pg_escape_string($mr['monr_subcondicion']);
		
		$mon_id=$mr['mon_id'];

		pg_query("START TRANSACTION;");

	        pg_query("UPDATE monitoreo_ges_registro SET monr_valor='$valores', monr_estado=1 WHERE monr_id=$monr_id;");

	        pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0");

		$val=pg_escape_string($_POST['sel_0']);

	        if($val=='0') {
        	        $clase=$id_condicion;
                	$subclase=$id_bandeja;
        	} else {
                	list($clase, $subclase)=explode('|', $val);
                	if($clase=='0') $clase=$id_condicion;
                	if($subclase=='') $subclase=$id_bandeja;
        	}
          
          if(isset($_POST['codigo_bandeja_n']) AND $_POST['codigo_bandeja_n']!='') {
            $clase=$id_condicion;
            $subclase=pg_escape_string($_POST['codigo_bandeja_n']);
          }

	if($clase=='') $clase=$mr['monr_clase'];

        	if($_fevento!='') $fevento="'$_fevento'";

        	pg_query("INSERT INTO monitoreo_ges_registro (mon_id, monr_fecha, monr_clase, monr_subclase, monr_fecha_evento, monr_subcondicion, monr_func_id, monr_observaciones, monr_valor, monr_estado) VALUES ($mon_id, CURRENT_TIMESTAMP, '$clase', '$subclase', $fevento, '$cual', ".$_SESSION['sgh_usuario_id'].", '$observaciones', '', 0);");
		pg_query("update monitoreo_ges_registro set monr_fecha_evento=foo.monr_fecha::date from (
                        select monr_id, monr_fecha from monitoreo_ges_registro where monr_id=CURRVAL('monitoreo_ges_registro_monr_id_seq') AND
                        monr_clase IN (SELECT id_condicion::text FROM lista_dinamica_condiciones WHERE digitacion_evento)
                        ) AS foo WHERE monitoreo_ges_registro.monr_id=foo.monr_id;");


        	pg_query("COMMIT;");
		
	}
	


?>
