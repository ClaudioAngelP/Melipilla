<?php 

	set_time_limit(0);

	require_once('../conectar_db.php');
	require_once('../conectores/hgf.php');
	
	error_reporting(E_ALL);

	/*

	$f=explode("\n",utf8_decode(file_get_contents('establecimientos.csv')));
	
	$fnd=0; $nfnd=0; $ndoc=0; $poli_malo=0;
	
	for($i=1;$i<sizeof($f);$i++) {
		
		$r=explode(';',$f[$i]);
		
		if($r[0]=='') continue;
		
		list($cod)=explode(' ',trim($r[1]));

		pg_query("UPDATE instituciones SET inst_codigo_ifl='$cod' WHERE inst_nombre='".pg_escape_string($r[0])."'");

	}
	 
	*/
	
	$f=explode("\n",(file_get_contents('LE_HGF.csv')));
	
	$fnd=0; $nfnd=0; $ndoc=0; $poli_malo=0;
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode(';',($f[$i]));

		if(!isset($r[1]) OR trim($r[1])=='') continue;

		$prut=explode('-',trim(strtoupper($r[1])));
		$pac_rut=($prut[0]*1).'-'.$prut[1];
				
		$esp_cod=$r[13]; // !!!!!!!!!!!!!
		
		if(trim($r[11])!='') {
			list($motivo_salida)=explode(' ',$r[11]);
			$motivo_salida*=1;
		} else
			$motivo_salida='0';
			
		$fecha_ingreso=str_replace('-','/',trim($r[7]));
		
		if(trim($r[10])!='')
			$fecha_salida="'".str_replace('-','/',trim($r[10]))."'";
		else
			$fecha_salida='null';
			
		$unidad_desc=str_replace("&yen;", "&Ntilde;", htmlentities(trim($r[12])));
		$unidad_desc=pg_escape_string($unidad_desc);
		
		if($unidad_desc=='OBS GINE ESTERILIDAD') $unidad_desc='OBS-GINE.INFERTILIDAD';
		if($unidad_desc=='PED NEUROLOGIA INF') $unidad_desc="PED.NEUROLOGIA INF.";
		if($unidad_desc=='REUMATOLOGIA') $unidad_desc="MED.REUMATOLOGIA";
		if($unidad_desc=='TRAUMA-L.ESPERA PROL.AD') $unidad_desc="TRAUMA-LISTA ESPERA AD.";
		if($unidad_desc=='TRAUMA-R.CL. AD. GES') continue;
		
		$poli=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='".html_entity_decode($unidad_desc)."'");
		
		if(!$poli) {
			$poli_malo++;
			echo "ESP [".($i+1)."]: $unidad_desc (POLI) NO EXISTE.<BR />";
			continue;
		} else {
			$unidad_id=$poli['esp_id'];
		}
		
		list($inst_cod)=explode(' ',$r[8]);
		$inst_cod=trim(strtoupper($inst_cod));
		
		if($inst_cod=='08-090') $inst_cod='08-101';
		
		$inst=cargar_registro("SELECT * FROM instituciones WHERE inst_codigo_ifl='$inst_cod'");
		
		if(!$inst) { echo "INST [".($i+1)."]: $inst_cod NO ENCONTRADA.<BR/>"; $inst_id=$sgh_inst_id; } else { $inst_id=$inst['inst_id']; }

			$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl_usuario='".$esp_cod."'"); // !!!!!!
			
			if(!$esp) {

				$ff=explode('/',$fecha_ingreso);
				$fi=explode('/',trim($r[4]));

				$f1=mktime(0,0,0,$fi[1],$fi[0],$fi[2]);
				$f2=mktime(0,0,0,$ff[1],$ff[0],$ff[2]);
				
				$dias=($f2-$f1)/86400;
				$anios=$dias/365.25;
				
				if($anios<15) {
					$esp_cod=substr($esp_cod,0,strlen($esp_cod)-1).'1';
				} else {
					$esp_cod=substr($esp_cod,0,strlen($esp_cod)-1).'2';					
				}

				$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl_usuario='".$esp_cod."'"); // !!!!!!

				if(!$esp) {
					echo "ESP [".($i+1)."]: $esp_cod (POLI) NO EXISTE.<BR />";
					continue;
				} else  {
				    $esp_id=$esp['esp_id'];	
				}
				
			} else {
				
				$esp_id=$esp['esp_id'];
				
			}

		
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut'");
		
		if($pac) {
			
			$pac_id=$pac['pac_id'];
			$fnd++;

			
/*
			 
CREATE TABLE interconsulta
(
  inter_id bigserial NOT NULL,
  inter_folio integer,
  inter_inst_id1 bigint,
  inter_especialidad integer,
  inter_unidad integer,
  inter_estado integer,
  inter_fundamentos text,
  inter_examenes text,
  inter_comentarios text,
  inter_pac_id bigint,
  inter_inst_id2 bigint,
  inter_notifica smallint NOT NULL,
  inter_ingreso date NOT NULL DEFAULT ('now'::text)::date,
  inter_rev_med text,
  inter_doc_id integer,
  inter_prioridad smallint NOT NULL DEFAULT 2,
  inter_diag_cod character varying(20),
  inter_motivo smallint,
  inter_otro_motivo text,
  inter_garantia_id integer,
  inter_motivo_salida smallint DEFAULT 0,
  inter_prof_id bigint,
  inter_patrama_id bigint DEFAULT 0,
  inter_fecha timestamp without time zone,
  inter_fecha_ingreso timestamp without time zone,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  func_id bigint DEFAULT 0,
  func_id2 bigint DEFAULT 0,
  func_id3 bigint DEFAULT 0,
  inter_diagnostico text,
  CONSTRAINT interconsulta_inter_id_key PRIMARY KEY (inter_id)
)
WITH (
  OIDS=FALSE
);
			  
			 */
			
			pg_query("INSERT INTO interconsulta VALUES (
				DEFAULT, 0, $sgh_inst_id, $esp_id, $unidad_id,
				1, '', '', '', $pac_id, $inst_id, 0, '$fecha_ingreso'::date, 
				'', -1, 0, '', 0, 'CARGADO AUTOMATICAMENTE L.E. ANTIGUA', 0, $motivo_salida, 0, 0,
				now(), '$fecha_ingreso', -1, 0, 7, 7, 7, '', $fecha_salida 
			);");
			
		} else {
			
			$pac_id=cargar_paciente($pac_rut, 1);

			if($pac_id==-1) {
				$nfnd++;
				echo "PAC: $pac_rut NO ENCONTRADO.<br />";
				continue;
			}
	
			pg_query("INSERT INTO interconsulta VALUES (
				DEFAULT, 0, $sgh_inst_id, $esp_id, $unidad_id,
				1, '', '', '', $pac_id, $inst_id, 0, '$fecha_ingreso'::date, 
				'', -1, 0, '', 0, 'CARGADO AUTOMATICAMENTE L.E. ANTIGUA', 0, $motivo_salida, 0, 0,
				now(), '$fecha_ingreso', -1, 0, 7, 7, 7, '', $fecha_salida
			);");
						
		}
		
	}
	
	echo "TERMINADO! FND OK: $fnd NOT FND: $nfnd POLIS MALOS: $poli_malo<BR>";

?>
