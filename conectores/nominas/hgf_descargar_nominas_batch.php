<?php 

  require_once('../../conectar_db.php');
  require_once('../hgf.php');
  require_once('../hgf_sqlserver.php');
  
  set_time_limit(0);
  
  $fec1=$_GET['fec1'];
  $fec2=$_GET['fec2'];
  
  $f1=explode('/',$fec1);
  $f2=explode('/',$fec2);
  
  $fecfinal=mktime(0,0,0,$f2[1],($f2[0]*1),$f2[2]);  

	$dia=0;
  
  while($fecactual<=$fecfinal) {
  
  $fecactual=mktime(0,0,0,$f1[1],($f1[0]*1)+$dia,$f1[2]);
  
  $dia++;

  $fecha=date('d/m/Y',$fecactual);
  
  print('<h1>Procesando Fecha: '.$fecha.'</h1><br><br>');
  
  $datos = mssql_query("SELECT * FROM dbo.AFC_Nomina WHERE NOM_Fecha='$fecha'", $sybase);
  
  $c=0;

	$fecc=$fecha;
	$ftmp=explode('/',$fecha);
	
	$fecc2=date('d/m/Y',mktime(0,0,0,$ftmp[1],($ftmp[0]*1)+1,$ftmp[2]));	


	$detalle=mssql_query("SELECT * FROM dbo.PCA_Agenda
								WHERE
								PCA_AGE_FechaCitac>='$fecc' AND
								PCA_AGE_FechaCitac<'$fecc2'", $sybase);

	$d=0;
	
	while($dt=mssql_fetch_object($detalle)) {

		$c++;

		if(!isset($arr[trim($dt->PCA_AGE_CodigServi)][$dt->PCA_AGE_CodigProfe])) {
			$arr[trim($dt->PCA_AGE_CodigServi)][$dt->PCA_AGE_CodigProfe]=array();
			$d++;
		}
		
		$num=sizeof($arr[trim($dt->PCA_AGE_CodigServi)][$dt->PCA_AGE_CodigProfe]);	
		$arr[trim($dt->PCA_AGE_CodigServi)][$dt->PCA_AGE_CodigProfe][$num]=$dt;
		$arr[trim($dt->PCA_AGE_CodigServi)][$dt->PCA_AGE_CodigProfe][$num]->asociado=false;

	}
	
	print('AGENDA: '.$c.' REGISTROS!!! '.$d.' GRUPOS!<br><br>');

	$detalle=mssql_query("SELECT * FROM NEW_NOM_Nomina WHERE 
			NOM_Fecha>=convert(datetime, '$fecc', 103) AND
			NOM_Fecha<convert(datetime, '$fecc2', 103)
			", $sqlserver);

	$c=0;

	while($dt=mssql_fetch_object($detalle)) {

		$c++;
		
		$arr2[pg_escape_string($dt->NOM_Folio)][$dt->PAC_Numero*1]=$dt;

	}

	print('NOMINA: '.$c.' REGISTROS<br><br>');

	//die();
	
	//print_r($arr);
	
	$c=0;



  while($r = mssql_fetch_object($datos)) {

  pg_query("START TRANSACTION;");  

	$c++;

	print('<br><br>Registro: <b>'.$c.'</b><br><br>'); 
 
	print_r($r);
	
	print('<br><br>');

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
			WHERE esp_codigo_int='".pg_escape_string(trim($r->NOM_CodigServi))."'");
			
	$esp_id=$esp['esp_id']*1;
	
	$prof=$r->NOM_CodigProfe;
	$serv=$r->NOM_CodigServi;
	
	$pdoc=explode('-',trim($r->NOM_CodigProfe));
	
	$rut_doc=($pdoc[0]*1).'-'.trim($pdoc[1]);	
	
	$doc=cargar_registro("SELECT * FROM doctores 
			WHERE doc_rut='".pg_escape_string($rut_doc)."'");
	
	if(!$doc) 
		echo '<br><br><b>ERROR:</b> M&eacute;dico no encontrado.<br><br>';	
			
	$doc_id=$doc['doc_id']*1;

	$folio=pg_escape_string($r->NOM_Folio);
	
	$chk=cargar_registro("SELECT * FROM nomina WHERE nom_folio='$folio'");
	
	if($chk) {
		print("N&oacute;mina <b>$folio</b> ya existe.<br><br>");	
		continue;
	} else {
		print("N&oacute;mina <b>$folio</b> no existe.<br><br>");		
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

	print($q.'<br><br>');

	pg_query($q);
	
	//$detalle=mssql_query("SELECT * FROM dbo.AFC_DetNomina
	//							WHERE NOM_Folio=$folio", $sybase);
								
	//while($d=mssql_fetch_object($detalle)) {

	$nom=&$arr[trim($r->NOM_CodigServi)][$r->NOM_CodigProfe];
	
	print(sizeof($nom)." REGISTROS PARA ".$r->NOM_CodigServi." ".$r->NOM_CodigProfe."<br><br>");
		
	for($i=0;$i<sizeof($nom);$i++) {	
		//print_r($d);
		
		//$pac=$d->NOM_NumerPacie;
		
		$d=&$nom[$i];
		
		if($d->asociado) continue;
		
		$d->asociado=true;
		
		$pac=$d->PCA_AGE_NumerPacie;
		
		$pac_id=cargar_paciente($pac*1);

		$hora=trim($d->PCA_AGE_HoraCitac);
				
		if($pac_id==0) {
			print('<b>ERROR:</b> Paciente no especificado.<br><br>');
			continue;	
		}
		
		print("<br>Paciente: <b>$pac_id</b> <br><br>");		

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

	$folio2=pg_escape_string($d->NOM_Folio);

  	//$detalle2=mssql_query("SELECT * FROM dbo.NEW_NOM_Nomina
	//								WHERE (NOM_Folio='$folio2' OR NOM_Folio='$folio') AND PAC_Numero=$pac_id", $sqlserver);
	
	if(isset($arr2[$folio][$pac*1]))
		$d2=&$arr2[$folio][$pac*1];
	elseif($arr2[$folio2][$pac*1])
		$d2=&$arr2[$folio2][$pac*1];	
	else 
		$d2=false;

		if($d2) {

			print_r($d2);

			$tipo=trim($d2->tipo);
			$extra=trim($d2->SobreCupo);
			$diag='';
			$sficha=trim($d2->sficha);
			$diag_cod=$d2->CodCie;
			$motivo=trim($d2->CodigoMotivo);
			$destino=trim($d2->CodigoDestino);
			$auge=trim($d2->CodigoAuge);
			$estado=trim($d2->estado);
			
		} else {
			
			print("NO TIENE REGISTRO!<br/><br/>");
			
			$tipo='';
			$extra='';
			$diag='';
			$sficha='';
			$diag_cod='';
			$motivo='';
			$destino='';
			$auge='';
			$estado='';
			
		}
		
		pg_query("INSERT INTO nomina_detalle VALUES (
			DEFAULT,
			CURRVAL('nomina_nom_id_seq'),
			$pac_id,
			'$tipo','$extra',
			'$diag','$sficha',
			'$diag_cod','$motivo',
			'$destino','$auge',
			'$estado',
			0, '$hora', 'A', '$folio2', 0				
		);");
		
	} 
	 
	pg_query("COMMIT;");
 
 
  }

	}

	pg_query("UPDATE nomina SET nom_digitar=false 
					WHERE nom_fecha='$fecha' AND nom_id NOT IN (
						SELECT nom_id FROM nomina_detalle					
					);");

	pg_query("UPDATE nomina_detalle SET nomd_nom_id=(
						SELECT nom_id FROM nomina WHERE nom_folio=nomd_folio
					) WHERE nomd_nom_id=0");
		
?>