<?php 

	require_once('../../config.php');
	require_once('../../conectores/sigh.php');


	$tipo=$_POST['tipo']*1;
	
	if($tipo==0) {
		
		$q=pg_query("

			SELECT * FROM (
		
			SELECT tcama_id, tcama_tipo, COUNT(*) AS cantidad
			
			FROM hospitalizacion
			
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama

			WHERE hosp_numero_cama>0 AND hosp_fecha_egr IS NULL 
			
			GROUP BY tcama_id, tcama_tipo

			) AS foo ORDER BY tcama_tipo;

		");
		
		print("
			<table style='width:90%;font-size:28px;margin:20px;' cellpadding=0 cellspacing=0>
				<tr class='header_tabla'>
					<td>Servicio</td>
					<td>Cant. Pacientes</td>
				</tr>
		");
		
		$c=0;
		
		while($r=pg_fetch_assoc($q)) {
			
			$clase=($c++)%2==0?'fila_tabla':'fila_tabla2';
			
			print("
				<tr class='$clase' onClick='listado(3, ".$r['tcama_id'].");'>
				<td>".htmlentities($r['tcama_tipo'])."</td>
				<td style='text-align:right;'>".$r['cantidad']."</td>
				</tr>
			");
		}

		print("</table>");

		
	} elseif($tipo==1) {

		$q=pg_query("

			SELECT * FROM (
		
			SELECT doc_id, doc_rut, doc_nombres, doc_paterno, doc_materno, COUNT(*) AS cantidad
			
			FROM hospitalizacion
			
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			WHERE hosp_numero_cama>0 AND hosp_fecha_egr IS NULL 
			
			GROUP BY doc_id, doc_rut, doc_nombres, doc_paterno, doc_materno

			) AS foo ORDER BY doc_paterno, doc_materno, doc_nombres;

		");
		
		print("
			<table style='width:90%;font-size:28px;margin:20px;' cellpadding=0 cellspacing=0>
				<tr class='header_tabla'>
					<td>M&eacute;dico Tratante</td>
					<td>Cant. Pacientes</td>
				</tr>
		");
		
		$c=0;
		
		while($r=pg_fetch_assoc($q)) {
			
			$clase=($c++)%2==0?'fila_tabla':'fila_tabla2';
			
			if($r['doc_rut']!='') {
				$med_tratante=htmlentities($r['doc_paterno']." ".$r['doc_materno']." ".$r['doc_nombres']);
				$med_tratante=str_replace('(AGEN)','',$med_tratante);
				$med_tratante=str_replace('(PAC-NEFRO)','',$med_tratante);
			} else
				$med_tratante='<i>(Sin Asignar...)</i>';
			
			print("
				<tr class='$clase' onClick='listado(4, ".$r['doc_id'].");'>
				<td>".($med_tratante)."</td>
				<td style='text-align:right;'>".$r['cantidad']."</td>
				</tr>
			");
		}

		print("</table>");

		
	} elseif($tipo==2) {
		
	} elseif($tipo==3 OR $tipo==4) {
	
			// LISTADO POR SERVICIO
			
		if($tipo==3) {
			$tcama_id=$_POST['id']*1;
			$condicion="tcama_id=$tcama_id";
		} elseif($tipo==4) {
			$doc_id=$_POST['id']*1;
			$condicion="hosp_doc_id=$doc_id";			
		}
		
		$q=pg_query("
			SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat, 
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			COALESCE(diag_desc, hosp_diagnostico) AS diag_desc
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod			
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
			WHERE $condicion AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL 
			ORDER BY tcama_tipo, cama_tipo, hosp_numero_cama
		");
		
?>

<table style='width:90%;font-size:14px;margin:20px;' cellpadding=0 cellspacing=0>
<tr class='header_tabla'>
<!--<td>Fecha Ing.</td>-->
<td>R.U.T.</td>
<td>Ficha</td>
<td style='width:30%;'>Nombre Paciente</td>
<td>Especialidad</td>
<!--<td>Procedencia</td>-->
<td style='width:20%;'>Servicio / Sala</td>
<td>Nro. Cama</td>
<td style='width:15%;'>Cod. CIE10 / Diagnostico</td>
<td>CAT. D-R</td>
</tr>

<?php

		while($r=pg_fetch_assoc($q)) {
			
		$i++;
		
		if($r['hosp_folio']=='-1') {
			$r['hosp_folio']='<i>'.$r['hosp_id'].'</i>';	
		}				
	
		if($r['hosp_numero_cama']=='0') {
			$r['hosp_numero_cama']='<i>(n/a)</i>';	
			$r['desc_cama']='<i>(Sin Asignar...)</i>';	
		}	
	
		if($r['hosp_numero_cama']==-1) {
			$r['hosp_numero_cama']='(n/a)';
			$r['desc_cama']='Alta / Fecha: '.$r['hosp_fecha_egr'];	
		}
		
		if($r['hosp_procedencia']=='0') {
			$r['hosp_procedencia']='UEA</i>';	
		}	
		
		if($r['hosp_procedencia']=='1') {
			$r['hosp_procedencia']='UEI</i>';	
		}	
		
		if($r['hosp_procedencia']=='2') {
			$r['hosp_procedencia']='UEGO</i>';	
		}
		
		if($r['hosp_procedencia']=='3') {
			$r['hosp_procedencia']='Otro Hospital</i>';	
		}	
		
		if($r['hosp_procedencia']=='4') {
			$r['hosp_procedencia']='Obstetricia y Ginecolog&iacute;a</i>';	
		}		
		
		if($r['hosp_procedencia']=='5') {
			$r['hosp_procedencia']='Hospitalizaci&oacute;n</i>';	
		}
		
		if($r['hosp_procedencia']=='6') {
			$r['hosp_procedencia']='AT. Ambulatoria</i>';	
		}							
	
		if($r['pac_rut']=='0-0') $r['pac_rut']='';
	
		$r['desc_cama']='<b>'.htmlentities($r['tcama_tipo']).'</b> / '.htmlentities($r['cama_tipo']).'';	
	
		$clase=($i%2==0)?'fila_tabla':'fila_tabla2';

			//<td style='text-align:center;'>".$r['hosp_fecha_ing']."<br />".$r['hosp_hora_ing']."</td>
			//<td style='text-align:center;'>".($r['hosp_procedencia'])."</td> 	

		
		print("
			<tr style='height:50px;' class='$clase'>
			<td style='text-align:right;font-weight:bold;color:blue;'>".$r['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;color:green;'>".$r['pac_ficha']."</td>
			<td style='font-size:12px;'>".htmlentities($r['pac_appat'].' '.$r['pac_apmat'].' '.$r['pac_nombres'])."</td>
			<td style='font-size:10px;'>".htmlentities($r['esp_desc'])."</td>			
			<td style='text-align:center;font-weight:bold;font-size:10px;' 
			id='desc_cama_".$r['hosp_id']."'>".$r['desc_cama']."</td>			
			<td style='text-align:center;'>
			".(($r['hosp_numero_cama']*1-$r['tcama_num_ini']*1)+1)." 
			</td>
			<td style='text-align:center;font-size:10px;'><b>".$r['hosp_diag_cod']."</b> ".htmlentities($r['diag_desc'])." 
			</td>
			<td style='text-align:center;font-weight:bold;'>
			<img src='variacion_crd.php?hosp_id=".$r['hosp_id']."&tipo=0&r=".microtime(false)."' />			
			</td></tr>");
			
	}
	
	print("</table>");
		
		
	} 


?>
