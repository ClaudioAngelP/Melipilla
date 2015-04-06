<?php 

  require_once('../config.php');
  require_once('../conectores/sigh.php');
  require_once('../conectores/hgf.php');
  
  $fecha1=cargar_registros_obj("SELECT MIN(NOM_Fecha)::date AS fecha FROM NOMINA2");
  $fecha2=cargar_registros_obj("SELECT MAX(NOM_Fecha)::date AS fecha FROM NOMINA2");
  
  $fecha1=$fecha1[0]['fecha'];  
  $fecha2=$fecha2[0]['fecha'];

  echo '<center><h1>Descargando Fechas: '.$fecha1.' a '.$fecha2.'</h1></center><br><br>';

  flush(); 
  
  $c=0;

	/*
	$f=explode("\n",file_get_contents('nomxprof.csv'));
	$np=array();
	
	for($i=0;$i<sizeof($f);$i++) {

		$r=explode('|',pg_escape_string($f[$i]));
		pg_query("UPDATE NOMINA2 SET CodProf='".$r[2]."', CodEspec='".$r[3]."' WHERE NOM_Folio='".$r[1]."'");
		echo $c.' '; $c++; flush();

	}

	flush();

	die();
	
	*/

	$anio=2010;

	echo 'DESCARGANDO A&Ntilde;O '.$anio.'<br><br>
	
	';

	$detalle=pg_query("SELECT *, nom_fecha::date AS nom_fecha FROM NOMINA2 
								WHERE 
									extract('year' from nom_fecha)=$anio AND
									extract('month' from nom_fecha) IN (5,6) AND
									NOT (codprof='' OR codespec='')
								ORDER BY nomina2.NOM_Fecha, NOM_Folio");
	$arr=array();
	
	$c=0; $cc=0;
	
	while($dt=pg_fetch_object($detalle)) {

		$fecha=pg_escape_string(trim($dt->nom_fecha));
		$prof=trim($dt->codprof);
		$espec=trim($dt->codespec);
		
		$cc++;

		if($cc<100) print($fecha.' .. '.$prof.' .. '.$espec.'<br>');

		if(!isset($arr[$fecha][$espec][$prof])) { 
			$arr[$fecha][$espec][$prof]=array();
			$c++;
		}
		
		$arr[$fecha][$espec][$prof][]=$dt;	

	}
	
	pg_free_result($detalle);
	
	echo '<br><br>Numero de Registros ('.pg_num_rows($detalle).') Nominas ('.$c.') <br><br>';

	$detalle=pg_query("SELECT DISTINCT 
								nom_folio, nom_fecha::date AS nom_fecha,
								codespec, codprof 
							 FROM NOMINA2 
								WHERE 
									extract('year' from nom_fecha)=$anio AND
									extract('month' from nom_fecha) IN (5,6) AND
									NOT (codprof='' OR codespec='')
								ORDER BY nom_fecha::date, NOM_Folio, codespec, codprof");

	$c=0;

  while($r = pg_fetch_object($detalle)) {

  pg_query("START TRANSACTION;");  

	$c++;

	if(!isset($_GET['win'])) print('<br><br>Registro: <b>'.$c.'</b><br><br>'); 
 
	if(!isset($_GET['win'])) print_r($r);
	
	if(!isset($_GET['win'])) print('<br><br>');

/*


CREATE TABLE nomina
(
  nom_id bigserial NOT NULL,
  nom_folio bigint,
  nom_esp_id bigint,
  nom_doc_id bigint,
  nom_centro_ruta character varying(100),
  nom_tipo smallint,
  nom_urgente boolean,
  nom_fecha timestamp without time zone,
  nom_orden smallint,
  nom_autorizado boolean,
  nom_func_id bigint,
  nom_func_id2 bigint,
  nom_estado smallint,
  nom_motivo text,
  CONSTRAINT nomina_nom_id_key PRIMARY KEY (nom_id)
)
WITH (OIDS=FALSE);


*/

	$esp=cargar_registro("SELECT * FROM especialidades 
			WHERE NOT esp_padre_id=0 AND esp_codigo_int='".pg_escape_string(trim($r->codespec))."'");
			
	$esp_id=$esp['esp_id']*1;
	
	$prof=trim($r->codprof);
	$serv=trim($r->codespec);
	
	$pdoc=explode('-',trim($r->codprof));
	
	$rut_doc=($pdoc[0]*1).'-'.trim($pdoc[1]);	
	
	$doc=cargar_registro("SELECT * FROM doctores 
			WHERE doc_rut='".pg_escape_string($rut_doc)."'");
	
	if(!$doc) 
		if(!isset($_GET['win'])) echo '<br><br><b>ERROR:</b> M&eacute;dico no encontrado.<br><br>';	
			
	$doc_id=$doc['doc_id']*1;

	$folio=pg_escape_string(trim($r->nom_folio));
	$fecha=pg_escape_string(trim($r->nom_fecha));
	
	$chk=cargar_registro("SELECT * FROM nomina WHERE nom_folio='$folio'");
	
	if($chk) {

		if(!isset($_GET['win'])) print("N&oacute;mina <b>$folio</b> ya existe.<br><br>");	

		$nom=&$arr[$fecha][trim($r->codespec)][trim($r->codprof)];
	
		for($i=0;$i<sizeof($nom);$i++) {
			if($nom[$i]->nom_folio==$folio)
				$nom[$i]->asociado=true;	
		}	

		continue;
		
	} else {
		
		if(!isset($_GET['win'])) print("N&oacute;mina <b>$folio</b> no existe.<br><br>");
				
	}

	$q="INSERT INTO nomina VALUES (
		DEFAULT, '$folio',
		$esp_id, $doc_id,
		'$centro_ruta',
		0,
		false,
		'$fecha',
		0,
		true,
		0,0,0,
		''
	);";

	if(!isset($_GET['win'])) print($q.'<br><br>');

	pg_query($q);
	
	$nom=&$arr[$fecha][trim($r->codespec)][trim($r->codprof)];
	
	if(!isset($_GET['win'])) print(sizeof($nom)." REGISTROS PARA ".$r->codespec." ".$r->codprof."<br><br>");
		
	for($i=0;$i<sizeof($nom);$i++) {	
		
		$d=&$nom[$i];
		
		if($d->asociado) continue;
		
		$d->asociado=true;
		
		$pac=$d->pac_numero;
		
		$pac_id=cargar_paciente($pac*1);

		$hora='';
		
		
		if($pac_id==0) {
			if(!isset($_GET['win'])) print('<b>ERROR:</b> Paciente no especificado.<br><br>');
			continue;	
		}
		
		if(!isset($_GET['win'])) print("<br>Paciente: <b>$pac_id</b> <br><br>");		

/*

CREATE TABLE nomina_detalle
(
  nomd_id bigserial NOT NULL,
  nom_id bigint,
  pac_id bigint,
  nomd_tipo character varying(1),
  nomd_extra character varying(1),
  nomd_diag character varying(100),
  nomd_sficha character varying(1),
  nomd_diag_cod character varying(20),
  nomd_motivo character varying(2),
  nomd_destino character varying(2),
  nomd_auge character varying(2),
  nomd_estado character varying(1),
  presta_id bigint,
  CONSTRAINT nomina_detalle_nomd_id_key PRIMARY KEY (nomd_id)
)
WITH (OIDS=FALSE);

*/

	$folio2=pg_escape_string(trim($d->nom_folio));

  	//$detalle2=mssql_query("SELECT * FROM dbo.NEW_NOM_Nomina
	//								WHERE (NOM_Folio='$folio2' OR NOM_Folio='$folio') AND PAC_Numero=$pac_id", $sqlserver);
	

		if(!isset($_GET['win'])) print_r($d);

		$tipo=strtoupper(trim($d->tipo));
		$extra=strtoupper(trim($d->sobrecupo));
		$diag='';
		$sficha=strtoupper(trim($d->sficha));
		$diag_cod=strtoupper(trim($d->codcie));
		$motivo=trim($d->codigomotivo);
		$destino=trim($d->codigodestino);
		$auge=strtoupper(trim($d->codigoauge));
		$estado=strtoupper(trim($d->estado));
		

		pg_query("INSERT INTO nomina_detalle VALUES (
			DEFAULT,
			CURRVAL('nomina_nom_id_seq'),
			$pac_id,
			'$tipo','$extra',
			'$diag','$sficha',
			'$diag_cod','$motivo',
			'$destino','$auge',
			'$estado',
			0, '00:00', 'A', '$folio2', 0				
		);");			

		$d->asociado=true;
						
	} 
	
   flush();
	 
	pg_query("COMMIT;");
  
  }

	pg_free_result($detalle);
	
	
	pg_query("UPDATE nomina_detalle SET nomd_nom_id=(
						SELECT nom_id FROM nomina WHERE nom_folio=nomd_folio
					) WHERE nomd_nom_id=0");

	pg_query("UPDATE nomina SET nom_digitar=false 
					WHERE nom_id IN (
						SELECT nomd_nom_id FROM nomina_detalle
						JOIN nomina USING (nom_id)
						WHERE NOT nomd_nom_id=nomina_detalle.nom_id					
					);");

?>