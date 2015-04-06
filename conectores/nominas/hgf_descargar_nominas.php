<?php

  //require_once('../../conectar_db.php');

  if(isset($_GET['fecha']))
  		$fecha=pg_escape_string($_GET['fecha']);
  else {
  		// Requerido por Servidor del HGF
  		@chdir('/var/www/sgh/conectores/nominas');
  		$fecha=date('d/m/Y');
  }

  if(isset($_GET['win'])) ob_start();

  require_once('../../config.php');
  require_once('../sigh.php');
  require_once('../hgf.php');
  require_once('../hgf_sqlserver.php');

  if(isset($_GET['win'])) ob_end_clean();


	if(isset($_GET['win'])) {
		echo '<html><title>Actualizando N&oacute;minas...</title>
				<body><center><br><br>
				<img src="../../imagenes/ajax-loader3.gif" />
				<h2>Espere un momento...</h2></center>';
		flush();
	}

  echo '<center><h1>Descargando Fecha: '.$fecha.'</h1></center>';

  if(isset($_GET['win'])) flush();

  $datos = mssql_query("SELECT * FROM dbo.AFC_Nomina WHERE NOM_Fecha='$fecha'", $sybase);

  $c=0;

	$fecc=$fecha;
	$ftmp=explode('/',$fecha);

	$fecc2=date('d/m/Y',mktime(0,0,0,$ftmp[1],($ftmp[0]*1)+1,$ftmp[2]));


	$detalle=mssql_query("SELECT * FROM dbo.PCA_Agenda, dbo.PAC_Paciente, dbo.PAC_Carpeta
								WHERE PCA_AGE_NumerPacie=dbo.PAC_Paciente.PAC_PAC_Numero AND
								dbo.PAC_Paciente.PAC_PAC_Numero=dbo.PAC_Carpeta.PAC_PAC_Numero AND
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

	if(!isset($_GET['win'])) print('AGENDA: '.$c.' REGISTROS!!! '.$d.' GRUPOS!<br><br>');

	/*

	$detalle=mssql_query("SELECT * FROM NEW_NOM_Nomina WHERE
			NOM_Fecha>=convert(datetime, '$fecc', 103) AND
			NOM_Fecha<convert(datetime, '$fecc2', 103)
			", $sqlserver);

	$c=0;

	while($dt=mssql_fetch_object($detalle)) {

		$c++;

		$arr2[pg_escape_string($dt->NOM_Folio)][$dt->PAC_Numero*1]=$dt;
		$arr2[pg_escape_string($dt->NOM_Folio)][$dt->PAC_Numero*1]->asociado=false;

	}

	if(!isset($_GET['win'])) print('NEW NOMINA: '.$c.' REGISTROS<br><br>');

	*/

	//die();

	//print_r($arr);

	$c=0; $regnum=mssql_num_rows($datos);



  while($r = mssql_fetch_object($datos)) {

  //pg_query("START TRANSACTION;");

	$c++;

	if($i%10==0) {
		print("  ".number_format(($c*100)/$regnum,1,',','.').'%...');
		flush();
	}


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
			WHERE NOT esp_padre_id=0 AND esp_codigo_int='".pg_escape_string(trim($r->NOM_CodigServi))."'");

	if(!$esp) {

		$espec=mssql_query("SELECT * FROM dbo.SER_Servicios
								WHERE SER_SER_Codigo='".trim($r->NOM_CodigServi)."'", $sybase);

		$e=mssql_fetch_object($espec);

		pg_query("INSERT INTO especialidades VALUES (
			DEFAULT, '".pg_escape_string($e->SER_SER_Descripcio)."', 0, 1, '', '".pg_escape_string(trim($e->SER_SER_Codigo))."'
		);");

	}

	$esp_id=$esp['esp_id']*1;

	$prof=$r->NOM_CodigProfe;
	$serv=$r->NOM_CodigServi;

	$pdoc=explode('-',trim($r->NOM_CodigProfe));

	$rut_doc=($pdoc[0]*1).'-'.trim($pdoc[1]);

		$doc=cargar_registro("SELECT * FROM doctores
			WHERE doc_rut='".pg_escape_string($rut_doc)."'");

		// Inserta médico nuevo...

		$prof=mssql_query("SELECT * FROM dbo.SER_Profesiona
								WHERE SER_PRO_Rut='".$r->NOM_CodigProfe."'", $sybase);

		$p=mssql_fetch_object($prof);

		$rut_doc=pg_escape_string($rut_doc);
		$paterno=pg_escape_string($p->SER_PRO_ApellPater);
		$materno=pg_escape_string($p->SER_PRO_ApellMater);
		$nombres=pg_escape_string($p->SER_PRO_Nombres);

		if(!$doc) {

			if(!isset($_GET['win'])) echo '<br><br><b>ERROR:</b> M&eacute;dico no encontrado.<br><br>';

			pg_query("INSERT INTO doctores VALUES (
				DEFAULT, '$rut_doc', '$paterno', '$materno', '$nombres'
			);");

			$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=CURRVAL('doctores_doc_id_seq');");

			// FIX DE ORDEN
			pg_query("UPDATE doctores SET
					doc_paterno=replace(doc_paterno, '¥', 'Ñ'),
					doc_materno=replace(doc_materno, '¥', 'Ñ'),
					doc_nombres=replace(doc_nombres, '¥', 'Ñ')
					WHERE doc_id=CURRVAL('doctores_doc_id_seq');");

			$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=CURRVAL('doctores_doc_id_seq');");

			if(!isset($_GET['win'])) {
				echo '<br><br><b>INSERTADO:</b>.<br><br>';
				print_r($doc);
				echo '<br><br>';
			}


		} else {

			pg_query("UPDATE doctores SET
					doc_paterno='$paterno',
					doc_materno='$materno',
					doc_nombres='$nombres'
					WHERE doc_id=".$doc['doc_id']);

			pg_query("UPDATE doctores SET
					doc_paterno=replace(doc_paterno, '¥', 'Ñ'),
					doc_materno=replace(doc_materno, '¥', 'Ñ'),
					doc_nombres=replace(doc_nombres, '¥', 'Ñ')
					WHERE doc_id=".$doc['doc_id']);


			$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=".$doc['doc_id']);

			if(!isset($_GET['win'])) {
				echo '<br><br><b>ACTUALIZADO:</b>.<br><br>';
				print_r($doc);
				echo '<br><br>';
			}

		}

	//}

	$doc_id=$doc['doc_id']*1;

	$folio=pg_escape_string($r->NOM_Folio);

	$chk=cargar_registro("SELECT * FROM nomina WHERE nom_folio='$folio'");

	if($chk) {

		if(!isset($_GET['win'])) print("N&oacute;mina <b>$folio</b> ya existe.<br><br>");

		$nom=&$arr[trim($r->NOM_CodigServi)][$r->NOM_CodigProfe];

		for($i=0;$i<sizeof($nom);$i++) {
			if($nom[$i]->NOM_Folio==$folio)
				$nom[$i]->asociado=true;
		}

		$nom_id=$chk['nom_id']*1;

	} else {

		if(!isset($_GET['win'])) print("N&oacute;mina <b>$folio</b> no existe.<br><br>");

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

		$nom_id="CURRVAL('nomina_nom_id_seq')";

	}


	$nom=&$arr[trim($r->NOM_CodigServi)][$r->NOM_CodigProfe];

	if(!isset($_GET['win'])) print(sizeof($nom)." REGISTROS PARA ".$r->NOM_CodigServi." ".$r->NOM_CodigProfe."<br><br>");

	for($i=0;$i<sizeof($nom);$i++) {

		$d=&$nom[$i];

		//if($d->asociado) continue;

		//$d->asociado=true;

		$pac_rut=trim($d->PAC_PAC_Rut);
		$trut=explode('-',$pac_rut);
		$pac_rut=($trut[0]*1).'-'.$trut[1];

		$pac_ficha=$d->PAC_CAR_NumerFicha;

		if($pac_rut!='0-0')
			$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut' OR pac_ficha='$pac_ficha' LIMIT 1;");
		else
			$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_ficha='$pac_ficha' LIMIT 1;");


		if(!$pac) {
			$pac_id=cargar_paciente($d->PCA_AGE_NumerPacie);
		} else {
			$pac_id=$pac['pac_id']*1;
		}

		$hora=trim($d->PCA_AGE_HoraCitac);

		$tipo=trim($d->PCA_AGE_PacNueCont);
		$extra=trim($d->PCA_AGE_Sobrecupo);

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

	$chk2=cargar_registro("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nom_fecha='$fecha 00:00:00' AND nom_esp_id=$esp_id AND nomina_detalle.pac_id=$pac_id");
	
	if($chk2) {
		
		if(!isset($_GET['win'])) print("PACIENTE ".$pac['pac_rut']." ".$pac['pac_ficha']." YA EXISTE EN LA NOMINA.<br><br>");
		continue;
		
	}

	$folio2=pg_escape_string($d->NOM_Folio);

	$diag='';
	$sficha='';
	$diag_cod='';
	$motivo='';
	$destino='';
	$auge='';
	$estado='';
			
	pg_query("INSERT INTO nomina_detalle VALUES (
			DEFAULT,
			$nom_id,
			$pac_id,
			'$tipo','$extra',
			'$diag','$sficha',
			'$diag_cod','$motivo',
			'$destino','$auge',
			'$estado',
			0, '$hora', 'A', '$folio2', 0				
		);");			
			
	} 
	  
  }

	
	
	pg_query("UPDATE nomina_detalle SET nomd_nom_id=(
						SELECT nom_id FROM nomina WHERE nom_folio=nomd_folio LIMIT 1
					) WHERE nomd_nom_id=0;");

	pg_query("UPDATE nomina_detalle AS n2 SET nomd_nom_id=nom_id, nomd_folio=(
						SELECT nom_folio FROM nomina AS n1 WHERE n2.nom_id=n1.nom_id LIMIT 1
					) WHERE nomd_nom_id IS NULL;");

	pg_query("UPDATE nomina SET nom_digitar=false 
					WHERE nom_fecha='$fecha' AND nom_id IN (
						SELECT nomd_nom_id FROM nomina_detalle
						JOIN nomina USING (nom_id)
						WHERE nom_fecha='$fecha' AND 
						NOT nomd_nom_id=nomina_detalle.nom_id					
					);");


	if(isset($_GET['win'])) {
		echo '<center><h2>Actualizaci&oacute;n Finalizada...</h2></center>
		</body></html>
		<script> 
			
			var fn=window.opener.listar_nominas.bind(window.opener);
			fn();	
			setTimeout("window.close();",1000); 
			
		</script>';	
	}		

		
?>
