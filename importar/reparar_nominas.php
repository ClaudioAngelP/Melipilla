<?php 

	require_once('../conectar_db.php');
	require_once('../conectores/hgf.php');

	$nom=cargar_registros_obj("SELECT * FROM nomina");
	
	for($i=0;$i<sizeof($nom);$i++) {
	
		$folio=$nom[$i]['nom_folio'];	
	
		$datos = mssql_query("SELECT * FROM dbo.AFC_Nomina WHERE NOM_Folio=$folio", $sybase);

		if($d=mssql_fetch_object($datos)) {
			
			$esp=cargar_registro("SELECT * FROM especialidades 
					WHERE NOT esp_padre_id=0 AND esp_codigo_int='".pg_escape_string(trim($d->NOM_CodigServi))."'");
			
			if($esp)		
				$esp_id=$esp['esp_id']*1;
			else 
				$esp_id=0;
			
			$pdoc=explode('-',trim($d->NOM_CodigProfe));
			
			$rut_doc=($pdoc[0]*1).'-'.trim($pdoc[1]);	
			
			$doc=cargar_registro("SELECT * FROM doctores 
					WHERE doc_rut='".pg_escape_string($rut_doc)."'");
			
			if(!$doc) 
				echo '<br><br><b>ERROR:</b> M&eacute;dico no encontrado.<br><br>';	
					
			$doc_id=$doc['doc_id']*1;
			
			if($esp_id!=0)
			pg_query("UPDATE nomina SET
				nom_esp_id=$esp_id, nom_doc_id=$doc_id
			WHERE nom_id=".$nom[$i]['nom_id']);
					
		}
  		
	}
	
	pg_query("
		UPDATE nomina SET nom_digitar=false 
		WHERE nom_id IN (
			SELECT nomd_nom_id FROM nomina_detalle WHERE NOT nomd_nom_id=nom_id		
		)	
	");

?>