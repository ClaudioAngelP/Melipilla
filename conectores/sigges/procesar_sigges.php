<?php 

function procesar_documento($tipo, $id, $caso, $html) {

	GLOBAL $id_pac_sigges, $pac_id, $id_caso;
	
	// echo 'DOCUMENTO DESCARGADO ( '.round(strlen($html)/1024).' Kb )...<br /><br />';
	
	$doc = str_get_html($html);				
	
	if($tipo==1) {
	
		// INTERCONSULTA

		$folio=$doc->find('input[name=campo_3]');
		$folio=$folio[0]->attr['value']*1;
		
		$chk=cargar_registros_obj("
			SELECT * FROM interconsulta WHERE inter_folio=$folio;		
		");
		
		if($chk) return;

		$fecha=$doc->find('input[name=campo_5]');
		$hora=$doc->find('input[name=campo_8]');
		
		$fecha="'".($fecha[0]->attr['value']).' '.($hora[0]->attr['value'])."'";

		$inst_id1=-1;
		$inst=$doc->find('select[name=campo_12]');
		$inst=cargar_registro("SELECT * FROM instituciones WHERE id_sigges=".val_select($inst));
		if($inst) $inst_id1=$inst['inst_id']*1;
		else $inst_id1=insertar_institucion_sigges($doc->find('select[name=campo_12]'));

		$inst_id2=-1;
		$inst=$doc->find('select[name=campo_72]');
		$inst=cargar_registro("SELECT * FROM instituciones WHERE id_sigges=".val_select($inst));
		if($inst) $inst_id2=$inst['inst_id']*1;
		else $inst_id2=insertar_institucion_sigges($doc->find('select[name=campo_72]'));

		$esp_id=-1;
		$especialidad=$doc->find('select[name=campo_74]'); // esp. origen es campo_15
		$especialidad=val_select($especialidad);
		if($especialidad!='') {
			$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl=".($especialidad*1));
			if($esp) $esp_id=$esp['esp_id'];
			else $esp_id=insertar_especialidad_sigges($doc->find('select[name=campo_74]'));
		}
		
		$motivo=$doc->find('input[name=estadoDeriva]'); 
		$motivo=(val_radio($motivo)*1)-1;

		$fundamentos=$doc->find('textarea[name=campo_77]');
		$fundamentos=$fundamentos[0]->find('text');
		$fundamentos=$fundamentos[0];

		$examenes=$doc->find('textarea[name=campo_90]');
		$examenes=$examenes[0]->find('text');
		$examenes=$examenes[0];

		$comentarios=$doc->find('textarea[name=campo_89]');
		$comentarios=$comentarios[0]->find('text');
		$comentarios=$comentarios[0];

		$prut=$doc->find('input[name=campo_95]');
		$pdv=$doc->find('input[name=campo_97]');

		$prof_id=-1;
		$prof_rut=($prut[0]->attr['value']*1).'-'.strtoupper($pdv[0]->attr['value']);
		$prof=cargar_registro("SELECT * FROM profesionales_externos WHERE prof_rut='$prof_rut'");
		
		if(!$prof) {
			
			$papellidos=$doc->find('input[name=campo_apellido]');
			$pnombres=$doc->find('input[name=campo_93]');

			list($ppaterno, $pmaterno)=explode(' ',trim($papellidos[0]->attr['value']));
			$pnombre=pg_escape_string($pnombres[0]->attr['value']);

			pg_query("INSERT INTO profesionales_externos VALUES (
				DEFAULT,'$ppaterno','$pmaterno','$pnombre','$prof_rut'			
			);");

			$pid=cargar_registro("SELECT CURRVAL('profesionales_externos_prof_id_seq') AS id;");
			$prof_id=$pid['id']*1;

		} else {

			$prof_id=$prof['prof_id']*1;	

		}
		
		$otro_motivo='';
		$unidad=0;

	
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
  inter_diag_cod character varying(10),
  inter_motivo smallint,
  inter_otro_motivo text,
  inter_garantia_id integer,
  inter_motivo_salida smallint,
  inter_prof_id bigint,
  inter_patrama_id bigint DEFAULT 0,
  inter_fecha timestamp without time zone,
  inter_fecha_ingreso timestamp without time zone,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  CONSTRAINT interconsulta_inter_id_key PRIMARY KEY (inter_id)
)
WITH (OIDS=FALSE);

*/			
				
	pg_query("INSERT INTO interconsulta VALUES (
		DEFAULT,
		$folio, $inst_id1, $esp_id, $unidad,
		-1, '$fundamentos', '$examenes', '$comentarios', $pac_id, 
		$inst_id2, 0,
		$fecha, '', 0, -1, '', $motivo, '$otro_motivo', 0, 0, $prof_id, 0,
		now(), $fecha, $id, $id_caso
	);");				
				
	}	
	
	if($tipo==2) {

		$folio=$doc->find('input[name=campo_173]');
		$folio=$folio[0]->attr['value']*1;

		$folio2=$doc->find('input[name=folioMigracion]');
		$folio2=$folio2[0]->attr['value']*1;

		$fecha=$doc->find('input[name=campo_175]');
		$hora=$doc->find('input[name=campo_178]');
		
		$fecha="'".($fecha[0]->attr['value']).' '.($hora[0]->attr['value'])."'";

		$inst_id=-1;
		$inst=$doc->find('select[name=campo_182]');
		$inst=cargar_registro("SELECT * FROM instituciones WHERE id_sigges=".val_select($inst));
		if($inst) $inst_id=$inst['inst_id']*1;
		else $inst_id=insertar_institucion_sigges($doc->find('select[name=campo_182]'));
		

		$esp_id=-1;
		$especialidad=$doc->find('select[name=especialidad]');
		$especialidad=val_select($especialidad);
		if($especialidad!='') {
			$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl=".($especialidad*1));
			if($esp) $esp_id=$esp['esp_id'];
			else $esp_id=insertar_especialidad_sigges($doc->find('select[name=especialidad]'));
		}

		$diagnostico=$doc->find('textarea[name=campo_203]');
		$diagnostico=$diagnostico[0]->find('text');
		$diagnostico=pg_escape_string($diagnostico[0]);

		$fundamentos=$doc->find('textarea[name=campo_209]');
		$fundamentos=$fundamentos[0]->find('text');
		$fundamentos=pg_escape_string($fundamentos[0]);

		$tratamiento=$doc->find('textarea[name=campo_216]');
		$tratamiento=$tratamiento[0]->find('text');
		$tratamiento=pg_escape_string($tratamiento[0]);

		$fecha2=$doc->find('input[name=campo_218]');
		if(trim($fecha2[0]->attr['value'])!='')
			$fecha_tratamiento="'".$fecha2[0]->attr['value']."'";
		else 
			$fecha_tratamiento='null';

		$confirma=$doc->find('input[name=campo_197]'); 
		$confirma=(val_radio($confirma)*1);
		if($confirma==1) $confirma='true'; else $confirma='false';

		$prut=$doc->find('input[name=campo_225]');
		$pdv=$doc->find('input[name=campo_227]');

		$prof_id=-1;
		$prof_rut=($prut[0]->attr['value']*1).'-'.strtoupper($pdv[0]->attr['value']);
		$prof=cargar_registro("SELECT * FROM profesionales_externos WHERE prof_rut='$prof_rut'");
		if(!$prof) {
			
			$papellidos=$doc->find('input[name=campo_apellido]');
			$pnombres=$doc->find('input[name=campo_223]');

			list($ppaterno, $pmaterno)=explode(' ',trim($papellidos[0]->attr['value']));
			$pnombre=pg_escape_string($pnombres[0]->attr['value']);

			pg_query("INSERT INTO profesionales_externos VALUES (
				DEFAULT,'$ppaterno','$pmaterno','$pnombre','$prof_rut'			
			);");

			$pid=cargar_registro("SELECT CURRVAL('profesionales_externos_prof_id_seq') AS id;");
			$prof_id=$pid['id']*1;

		} else {

			$prof_id=$prof['prof_id']*1;	

		}

/*
CREATE TABLE formulario_ipd
(
  ipd_id bigserial NOT NULL,
  ipd_folio bigint,
  ipd_folio2 bigint,
  ipd_fecha timestamp without time zone,
  ipd_fecha_ingreso timestamp without time zone,
  ipd_esp_id bigint,
  ipd_pac_id bigint,
  ipd_confirma boolean DEFAULT false,
  ipd_diagnostico text,
  ipd_fundamentos text,
  ipd_tratamiento text,
  ipd_fecha_tratamiento date,
  ipd_prof_id bigint,
  id_sigges bigint,
  id_caso bigint,
  ipd_inst_id bigint,
  CONSTRAINT formulario_ipd_ipd_id_key PRIMARY KEY (ipd_id)
)
WITH (OIDS=FALSE);
*/

		pg_query("INSERT INTO formulario_ipd VALUES (
			DEFAULT, $folio, $folio2, now(), $fecha, $esp_id, $pac_id,
			$confirma, '$diagnostico', '$fundamentos', '$tratamiento',
			$fecha_tratamiento, $prof_id, $id, $id_caso, $inst_id
		);");
		
	}	
	
	if($tipo==3) {
	
	// ORDEN DE ATENCIÓN
		
		$folio=$doc->find('input[name=campo_1005]');
		$folio=$folio[0]->attr['value']*1;

		$chk=cargar_registros_obj("
			SELECT * FROM orden_atencion WHERE oa_folio=$folio;		
		");
		
		if($chk) return;

		$fecha=$doc->find('input[name=campo_1007]');
		$hora=$doc->find('input[name=hora]');
		
		$fecha="'".($fecha[0]->attr['value']).' '.($hora[0]->attr['value'])."'";

		$inst_id=-1;
		$inst=$doc->find('select[name=campo_1014]'); 
		$inst=cargar_registro("SELECT * FROM instituciones WHERE id_sigges=".val_select($inst));
		if($inst) $inst_id=$inst['inst_id']*1;
		else $inst_id=insertar_institucion_sigges($doc->find('select[name=campo_1014]'));

		$inst_id2=-1;
		$inst2=$doc->find('select[name=campo_5204]');
		$id_inst=val_select($inst2);
		if($id_inst!='') {
			$inst2=cargar_registro("SELECT * FROM instituciones WHERE id_sigges=".$id_inst);
			if($inst2) $inst_id2=$inst2['inst_id']*1;
			else $inst_id2=insertar_institucion_sigges($doc->find('select[name=campo_5204]'));
		}

		$esp_id=-1;
		$especialidad=$doc->find('select[name=especialidad2]');
		$especialidad=val_select($especialidad);
		$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl=".($especialidad*1) );
		if($esp) $esp_id=$esp['esp_id'];
		else $esp_id=insertar_especialidad_sigges($doc->find('select[name=especialidad2]'));

		$hipotesis=$doc->find('textarea[name=hipotesisDiagnostica]');
		$hipotesis=$hipotesis[0]->find('text');
		$hipotesis=$hipotesis[0];

		$extra=$doc->find('textarea[name=txtExtra]');
		$extra=$extra[0]->find('text');
		$extra=trim($extra[0]);

		$motivo=$doc->find('input[name=deriva]'); 
		$motivo=(val_radio($motivo)*1)-1;

		$codigo=$doc->find('div[id=Layer1]');
		$codigo=$codigo[0]->find('tr');
		$codigo=$codigo[1]->find('td');
		$codigo=$codigo[1]->find('text');
		$codigo=$codigo[0];

		$prut=$doc->find('input[name=campo_1067]');
		$pdv=$doc->find('input[name=campo_1069]');

		$prof_id=-1;
		$prof_rut=($prut[0]->attr['value']*1).'-'.strtoupper($pdv[0]->attr['value']);
		$prof=cargar_registro("SELECT * FROM profesionales_externos WHERE prof_rut='$prof_rut'");
		if(!$prof) {
			
			$papellidos=$doc->find('input[name=campo_apellido]');
			$pnombres=$doc->find('input[name=campo_1065]');

			list($ppaterno, $pmaterno)=explode(' ',trim($papellidos[0]->attr['value']));
			$pnombre=pg_escape_string($pnombres[0]->attr['value']);

			pg_query("INSERT INTO profesionales_externos VALUES (
				DEFAULT,'$ppaterno','$pmaterno','$pnombre','$prof_rut'			
			);");

			$pid=cargar_registro("SELECT CURRVAL('profesionales_externos_prof_id_seq') AS id;");
			$prof_id=$pid['id']*1;

		} else {

			$prof_id=$prof['prof_id']*1;	

		}

/*
CREATE TABLE orden_atencion
(
  oa_id bigserial NOT NULL,
  oa_folio integer,
  oa_fecha timestamp without time zone,
  oa_pac_id bigint,
  oa_inst_id bigint,
  oa_inst_id2 bigint,
  oa_especialidad integer,
  oa_motivo smallint,
  oa_estado smallint,
  oa_hipotesis text,
  oa_codigo character varying(30),
  oa_diag_cod character varying(20),
  oa_prof_id bigint,
  oa_doc_id bigint,
  oa_fecha_aten date,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  func_id bigint DEFAULT 0,
  func_id2 bigint DEFAULT 0,
  oa_motivo_salida smallint DEFAULT 0,
  oa_centro_ruta character varying(100) DEFAULT ''::character varying,
  oa_prioridad smallint,
  oa_especialidad2 bigint DEFAULT 0,
  oa_diagnostico text DEFAULT ''::text,
  oa_compra_extra character varying(200) DEFAULT ''::character varying,
  CONSTRAINT orden_atencion_oa_id_key PRIMARY KEY (oa_id)
)
WITH (OIDS=FALSE);
*/

		
		pg_query("
			INSERT INTO orden_atencion VALUES (
				DEFAULT,
				$folio,
				$fecha,
				$pac_id,
				$inst_id, $inst_id2,
				$esp_id,
				$motivo,
				-1,
				'$hipotesis',
				'$codigo', 
				'',
				$prof_id, 0, null,
				$id, $id_caso, 0, 0, 0, '', -1, 0, '', 
				'$extra'			
			);
		");

	}	

	if($tipo==4 OR $tipo==14) {
		
		$data=$doc->find('tr[onMouseOver]');
		$data=$data[0]->find('td');

		$codigo=$data[1]->find('text');
		$desc=$codigo[0];
		$desc=preg_replace('/\([0-9]+\)/','',$codigo[0],1);
		$desc=str_replace('&nbsp;','',$desc);
		preg_match('/\([0-9]+\)/',$codigo[0],$match);
		$codigo=substr($match[0],1,-1);

		$fecha=$data[2]->find('text');
		$fecha=$fecha[0];

		$hora=$data[4]->find('text');
		$hora=$hora[0];

		$fecha="'".$fecha.' '.$hora."'";

		$cantidad=$data[5]->find('text');
		$cantidad=$cantidad[0]*1;

		$extra=$data[6]->find('text');
		$extra=$extra[0];
		if(strtoupper($extra)=='SI') $extra='true'; else $extra='false';

		$inst_id=-1;		
		$inst=$data[8]->find('text');
		$inst=cargar_registro("SELECT * FROM instituciones WHERE inst_nombre ILIKE '%".pg_escape_string(trim($inst[0]))."%'");
		if($inst) $inst_id=$inst['inst_id'];
		
		$esp_id=-1;
		$esp=$data[10]->find('text');
		$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_desc ILIKE '%".pg_escape_string(trim($esp[0]))."%'");
		if($esp) $esp_id=$inst['inst_id'];
		
/*
CREATE TABLE prestacion
(
  presta_id bigserial NOT NULL,
  presta_fecha timestamp without time zone,
  pac_id bigint,
  prev_id smallint,
  porigen_id integer,
  porigen_num bigint,
  presta_codigo_i character varying(30),
  presta_codigo_v character varying(30),
  presta_cant smallint,
  presta_compra boolean,
  inst_id bigint,
  esp_id bigint,
  presta_diag_cod character varying(20),
  presta_desc text,
  presta_valor bigint,
  presta_copago bigint,
  presta_porcentaje smallint,
  presta_func_id1 bigint,
  presta_func_id2 bigint,
  presta_estado smallint,
  id_sigges bigint DEFAULT (-1),
  id_caso bigint DEFAULT 0,
  CONSTRAINT prestacion_presta_id_key PRIMARY KEY (presta_id)
)
WITH (OIDS=FALSE);*/

		pg_query("INSERT INTO prestacion VALUES (
			DEFAULT, $fecha, $pac_id, -1, 0, 0, '$codigo', '$codigo', $cantidad,
			$extra, $inst_id, $esp_id, '', '$desc', 0, 0, 0, -1, -1, 10, $id, $id_caso		
		);");
		
	}

	$doc->clear();
	unset($doc);
			
}


function chequear_doc_sigges($t,$id,$id2) {
	
	$chk=false;
	
	if($t==1) {
		//$chk=cargar_registros_obj("SELECT * FROM interconsulta WHERE id_sigges=".($id*1)." AND id_caso=".$id2);
		$chk=false;
	}elseif($t==2) 
		$chk=cargar_registros_obj("SELECT * FROM formulario_ipd WHERE id_sigges=".($id*1)." AND id_caso=".$id2);		
	elseif($t==3) { 
		//$chk=cargar_registros_obj("SELECT * FROM orden_atencion WHERE id_sigges=".($id*1)." AND id_caso=".$id2);
		$chk=false;		
	} elseif($t==4 or $t==14) 
		$chk=cargar_registros_obj("SELECT * FROM prestacion WHERE id_sigges=".($id*1)." AND id_caso=".$id2);		

	return ($chk==true);
		
}

function val_select($obj) {

	$opt=$obj[0]->find('option');
	
	for($i=0;$i<sizeof($opt);$i++) {
		if(isset($opt[$i]->attr['selected'])) {
				return $opt[$i]->attr['value'];	
		}				
	}

	return $opt[0]->attr['value'];

}	

function nom_select($obj) {

	$opt=$obj[0]->find('option');
	
	for($i=0;$i<sizeof($opt);$i++) {
		if(isset($opt[$i]->attr['selected'])) {
				$tmp=$opt[$i]->find('text');
				return $tmp[0];	
		}				
	}

	return '';

}	


function val_radio($obj) {

	for($i=0;$i<sizeof($obj);$i++) {
		if(isset($obj[$i]->attr['checked'])) {
				return $obj[$i]->attr['value'];	
		}				
	}

	return $obj[0]->attr['value'];

}	

function insertar_institucion_sigges($data) {

	$opt=$data[0]->find('option');
	
	for($i=0;$i<sizeof($opt);$i++) {
		if(isset($opt[$i]->attr['selected'])) {
				$sigges=$opt[$i]->attr['value'];
				$nombre=$opt[$i]->find('text');
				$nombre=$nombre[0];
				break;	
		}				
	}


	pg_query("INSERT INTO instituciones VALUES (
		DEFAULT,	'$nombre', 0, '', $sigges
	);");
	
	$id=cargar_registro("SELECT CURRVAL('instituciones_inst_id_seq') AS id;");
	
	return $id['id']*1;
}

function insertar_especialidad_sigges($data) {

	$opt=$data[0]->find('option');
	
	for($i=0;$i<sizeof($opt);$i++) {
		if(isset($opt[$i]->attr['selected'])) {
				$sigges=$opt[$i]->attr['value'];
				$nombre=$opt[$i]->find('text');
				$nombre=$nombre[0];
				$nom=explode('&nbsp', pg_escape_string($nombre));
				break;	
		}				
	}	

	pg_query("INSERT INTO especialidades VALUES (
		DEFAULT,	'".$nom[0]."', $sigges, -1, '".$nom[1]."', ''
	);");
	
	$id=cargar_registro("SELECT CURRVAL('especialidades_esp_id_seq') AS id;");
	
	return $id['id']*1;
}


function insertar_paciente_sigges($data) {
	
	GLOBAL $pac_rut;

	$nombre = $data->find('select[name=nombrepac]');
	
	$nombre = $nombre[0]->find('option');

	$id_pac_sigges = $nombre[0]->attr['value'];

	$nombre = $nombre[0]->find('text');

	$nombre_str = $nombre[0];
	
	$nom=explode(',',$nombre_str);
	$nom2=explode(' ',$nom[0]);
	$nombre=trim($nom[1]);
	$paterno=trim($nom2[0]);
	$materno=trim($nom2[1]);
	
	$direccion=$data->find('input[name=direccionpac]');
	$direccion=trim($direccion[0]->attr['value']);

	$fono=$data->find('input[name=telefonopac]');
	$fono=$fono[0]->attr['value'];

	$sexo=$data->find('select[name=sexopaciente]');
	$sexo=val_select($sexo);

	if($sexo==2) 
		$sexo=0;
	elseif($sexo==1)
		$sexo=1;
	else 
		$sexo=2;

	$fechanac=$data->find('input[name=fechanacpac]');
	$fechanac=trim($fechanac[0]->attr['value']);

	$comuna=$data->find('select[name=comunapaciente]');
	$comuna=nom_select($comuna);
	if($comuna!='') {
		$ciud=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ilike '%$comuna%'");
		if($ciud)
			$ciud_id=$ciud['ciud_id']*1;
		else 
			$ciud_id=-1;
	} else $ciud_id=-1;
	
	$prev_id=-1;
	
/*

CREATE TABLE pacientes
(
  pac_id serial NOT NULL,
  pac_rut character varying(11),
  pac_nombres character varying(100),
  pac_appat character varying(50),
  pac_apmat character varying(50),
  pac_fc_nac date,
  sex_id smallint,
  prev_id smallint,
  sector_nombre character varying(80),
  getn_id smallint,
  sang_id integer,
  pac_direccion text,
  ciud_id integer,
  nacion_id integer,
  estciv_id integer,
  pac_fono text,
  pac_padre character varying(200),
  pac_madre character varying(200),
  pac_tramo character varying(1),
  pac_pasaporte character varying(30),
  pac_ficha character varying(30),
  id_sigges bigint DEFAULT (-1),
  CONSTRAINT pac_id PRIMARY KEY (pac_id)
)
WITH (OIDS=FALSE);

*/
	pg_query("INSERT INTO pacientes VALUES (

		DEFAULT,
		'$pac_rut',
		'$nombre',
		'$paterno','$materno',
		'$fechanac', $sexo,
		$prev_id,
		'',
		-1, -1, '$direccion',
		$ciud_id, 0, -1, '$fono','','','','','',$id_pac_sigges
	
	);");
	
	$id=cargar_registro("SELECT CURRVAL('pacientes_pac_id_seq') AS pac_id;");
	
	$pac_id=$id['pac_id']*1;

	return $pac_id;

}

function sigges_login() {

GLOBAL $ch;

// SET URL FOR THE POST FORM LOGIN

curl_setopt( $ch, CURLOPT_URL, 'http://www.sigges.cl/' );
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
curl_setopt( $ch, CURLOPT_FORBID_REUSE, true );
curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 20 );
curl_setopt( $ch, CURLOPT_COOKIEFILE, 'cookies/cookiefile_'.$_SESSION['sgh_usuario_id'].'_'.str_replace(' ','-',microtime(false)).'.txt');
//curl_setopt ($ch, CURLOPT_COOKIEJAR, 'cookies/cookiefile_'.$_SESSION['sgh_usuario_id'].'_'.str_replace(' ','-',microtime(false)).'.txt');
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

// ENABLE HTTP POST
curl_setopt( $ch, CURLOPT_POST, 0);
curl_setopt( $ch, CURLOPT_POSTFIELDS,'');

// CARGA PAGINA DE LOGIN PARA OBTENER IDS DE INGRESO
$login = curl_exec ($ch);

$page = str_get_html($login);

$form = $page->find('form[name=IngresoLoginForm]');

$randomId = $page->find('input[name=randomId]');
$dispatch = $page->find('input[name=dispatch]');

$page->clear();
unset($page);

$params = codificar_url(array(
	array('randomId',$randomId[0]->attr['value']),
	array('dispatch',$dispatch[0]->attr['value']),
	array('rut','16501170'),
        array('digito','8'),
        array('password','56soto')
));

$url=($form[0]->attr['action']);

/*
echo 'URL: '.$url.'<br /><br />';
echo $params;
*/

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl'.$url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

$login2 = curl_exec($ch);

curl_setopt($ch, CURLOPT_URL, 'http://www.sigges.cl/jsp/menu/homepage.jsp');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false );
curl_setopt($ch, CURLOPT_POST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, '');

$login3 = curl_exec($ch);

$page3 = str_get_html($login3);

$link = $page3->find('frame[name=left]');

$url=$link[0]->attr['src'];

$page3->clear();
unset($page3);
	
}




function codificar_url($r) {

	$buffer='';

	for($i=0;$i<sizeof($r);$i++) {
		$buffer.=urlencode($r[$i][0]).'='.urlencode($r[$i][1]);
		if($i<sizeof($r)-1) $buffer.='&';	
	}

	return $buffer;
}




function show_data() {

GLOBAL $pac_id, $sgh_inst_id;

$ic=cargar_registros_obj("SELECT * FROM interconsulta
				LEFT JOIN instituciones ON inter_inst_id1=inst_id
				LEFT JOIN especialidades ON inter_especialidad=esp_id
				LEFT JOIN prioridad ON inter_prioridad=prior_id 
				WHERE inter_pac_id=$pac_id 
				ORDER BY inter_ingreso DESC");

$oa=cargar_registros_obj("SELECT *, oa_fecha::date AS oa_fecha FROM orden_atencion
				LEFT JOIN instituciones ON oa_inst_id=inst_id 
				LEFT JOIN especialidades ON oa_especialidad=esp_id
				LEFT JOIN prioridad ON oa_prioridad=prior_id 
				WHERE oa_pac_id=$pac_id AND
				(NOT oa_motivo=-1)
				ORDER BY orden_atencion.oa_fecha DESC");

$script='';
$ic_local=0;
$oa_local=0;

for($i=0;$i<sizeof($ic);$i++) {
	if($ic[$i]['inter_inst_id2']*1==$sgh_inst_id) {
		$ic_local++;
	} 	
}

for($i=0;$i<sizeof($oa);$i++) {
	if($oa[$i]['oa_inst_id2']*1==$sgh_inst_id) {
		$oa_local++;
	} 	
}

print("<center>
<form id='lista_ics' name='lista_ics' onSubmit='return false;'>
<table style='width:850px;'>
<tr class='tabla_header'>
<td colspan=9 style='font-weight:bold;'><u>Interconsultas Descargadas (".($ic_local)."/".($ic?sizeof($ic):'0').")</u></td>
</tr>
<tr class='tabla_header'>
<td>&nbsp;</td>
<td>Nro. Folio</td>
<td>Fecha</td>
<td>Instituci&oacute;n Solicitante</td>
<td style='width:150px;'>Especialidad</td>
<td>G.E.S.</td>
<td>Prioridad</td>
<td>Ver</td>
</tr>");

$prioridadhtml = desplegar_opciones("prioridad", "prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 
if($ic)				
for($i=0;$i<sizeof($ic);$i++) {
	
	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
	
	if($ic[$i]['inter_estado']==-1) {	
	
	if($ic[$i]['inter_inst_id2']*1!=$sgh_inst_id) {
		$color='color:#FF0000;';
		print("<input type='hidden' 
		id='inter_local_".$ic[$i]['inter_id']."' 
		name='inter_local_".$ic[$i]['inter_id']."' 
		value='0' />");	
	} else {
		$color='';
		print("<input type='hidden' 
		id='inter_local_".$ic[$i]['inter_id']."' 
		name='inter_local_".$ic[$i]['inter_id']."' 
		value='1' />");	
	}		
	
	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<input type='checkbox' 
	id='inter_".$ic[$i]['inter_id']."' name='inter_".$ic[$i]['inter_id']."'>
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$ic[$i]['inter_folio']."</td>
	<td style='text-align:center;'>".$ic[$i]['inter_ingreso']."</td>
	<td style='font-size:11px;'><i>".htmlentities($ic[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($ic[$i]['esp_desc'])."</td>
	<td><center>
	".(($ic[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center><select 
	id  ='prioridad_ic_".$ic[$i]['inter_id']."' 
	name='prioridad_ic_".$ic[$i]['inter_id']."' 
	onClick='
		if(this.value!=0) $(\"inter_".$ic[$i]['inter_id']."\").checked=true;	
	'>$prioridadhtml
	</select></center></td>
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_ficha(".$ic[$i]['inter_id'].");' />
	</center></td></tr>");
	
	} else {

	$color='';

	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<img src='iconos/tick.png' />	
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$ic[$i]['inter_folio']."</td>
	<td style='text-align:center;'>".$ic[$i]['inter_ingreso']."</td>
	<td style='font-size:11px;'><i>".htmlentities($ic[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($ic[$i]['esp_desc'])."</td>
	<td><center>
	".(($ic[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center>".htmlentities($ic[$i]['prior_desc'])."</center></td>
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_ficha(".$ic[$i]['inter_id'].");' />
	</center></td></tr>");

		
	}
			
}

print('</table>');

print("
<table style='width:850px;'>
<tr class='tabla_header'>
<td colspan=9 style='font-weight:bold;'><u>Ordenes de Atenci&oacute;n Descargadas (".($oa_local)."/".($oa?sizeof($oa):'0').")</u></td>
</tr>
<tr class='tabla_header'>
<td>&nbsp;</td>
<td>Nro. Folio</td>
<td>Fecha</td>
<td>Instituci&oacute;n Solicitante</td>
<td style='width:150px;'>Especialidad</td>
<td>G.E.S.</td>
<td>Prioridad</td>
<td>Ver</td>
</tr>");

$prioridadhtml = desplegar_opciones("prioridad", "prior_id, prior_desc",'prior_id=0','true','ORDER BY prior_id'); 

if($oa)				
for($i=0;$i<sizeof($oa);$i++) {
	
	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
	if($oa[$i]['oa_estado']==-1) {
	
	if($oa[$i]['oa_inst_id2']*1!=$sgh_inst_id) {
		$color='color:#FF0000;';
		print("<input type='hidden' 
		id='oa_local_".$oa[$i]['oa_id']."' 
		name='oa_local_".$oa[$i]['oa_id']."' 
		value='0' />");	
	} else {
		$color='';
		print("<input type='hidden' 
		id='oa_local_".$ic[$i]['oa_id']."' 
		name='oa_local_".$ic[$i]['oa_id']."' 
		value='1' />");	
	}		
	
	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<input type='checkbox' 
	id='oa_".$oa[$i]['oa_id']."' name='oa_".$oa[$i]['oa_id']."'>
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$oa[$i]['oa_folio']."</td>
	<td style='text-align:center;'>".$oa[$i]['oa_fecha']."</td>
	<td style='font-size:11px;'><i>".htmlentities($oa[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($oa[$i]['esp_desc'])."</td>
	<td><center>
	".(($oa[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center><select 
	id  ='prioridad_oa_".$oa[$i]['oa_id']."' 
	name='prioridad_oa_".$oa[$i]['oa_id']."' 
	onClick='
		if(this.value!=0) $(\"oa_".$oa[$i]['oa_id']."\").checked=true;	
	'>$prioridadhtml
	</select></center></td>	
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_oa(".$oa[$i]['oa_id'].");' />
	</center></td></tr>");
	
	} else {

	$color='';

	print("<tr class='$clase' 
	onMouseOver='this.className=\"mouse_over\";'
	onMouseOut='this.className=\"$clase\";'>
	<td><center>
	<img src='iconos/tick.png' />	
	</center></td>
	<td style='text-align:center;font-weight:bold;$color'>".$oa[$i]['oa_folio']."</td>
	<td style='text-align:center;'>".$oa[$i]['oa_fecha']."</td>
	<td style='font-size:11px;'><i>".htmlentities($oa[$i]['inst_nombre'])."</i></td>
	<td>".htmlentities($oa[$i]['esp_desc'])."</td>
	<td><center>
	".(($oa[$i]['id_caso']*1==0)?'NO':'SI')."
	</center></td>	
	<td><center>".htmlentities($oa[$i]['prior_desc'])."</center></td>	
	<td><center>
	<img src='iconos/magnifier.png' style='cursor:pointer;' 
	onClick='abrir_oa(".$oa[$i]['oa_id'].");' />
	</center></td></tr>");
		
	}	

}

print('</table>');



if($ic OR $oa)
print('<center>
<input type="button" value="-- Confirmar Ingreso de Documentos... --" 
onClick="validar_ic();" /></form>
<br><br></center>
');
else 
print('<center>
<h2>No hay documentos disponibles.</h2>
<br><br></center>');

print($stats);

	
}


?>
