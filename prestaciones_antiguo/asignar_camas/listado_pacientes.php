<?php 

	require_once('../../conectar_db.php');
	//error_reporting(E_ALL);
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']*1));
	$filtro=$_POST['filtro']*1;
	$esp_id=$_POST['esp_id']*1;	
	
	if($esp_id!=0) {
		$esp_w="(hosp_esp_id=$esp_id OR hosp_esp_id2=$esp_id)";
	} else {
		$esp_w='true';
	}
		
	if($filtro==0) {
		if($tcama!=-1) $tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_fecha_egr IS NULL AND hosp_numero_cama=0 AND $tcama_w";	
	} elseif($filtro==1) {
		if($tcama!=-1) $tcama_w='tcama_id='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_fecha_egr IS NULL AND NOT hosp_numero_cama<=0 AND $tcama_w";
	} elseif($filtro==3) {
		if($tcama!=-1) $tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_doc_id =-1 AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND $tcama_w";	
	} else {
		if($tcama!=-1) $tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="$tcama_w";
	}	
	
	$busca=trim(pg_escape_string(utf8_decode($_POST['busqueda'])));
	
	if($busca!='') {

		$pbusca=preg_replace('/[^A-Za-z0-9 ]/','_', $busca);
		
		$pbusca=preg_replace('/\s{2,}/', ' ', $pbusca);
		
		$pbusca=str_replace(' ', '%', $pbusca);
		
		$busca_w="
	    (to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha )
			@@ plainto_tsquery('".$busca."') )
		OR pac_rut='$busca' OR pac_ficha='$busca' OR 
		upper(pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$pbusca%'
		";		
		
	} else {
		
		$busca_w="true";
		
	}	
	
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
	
  	    header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: filename=\"Informacion_CAMAS_GESTION.xls\";");
       print ("<table><tr><td><h1><b>Reporte Gesti&oacute;n Centralizada de Camas</b></h1></td></tr>");	
       print ("<tr><td><h6><i>Gesti&oacute;n de Camas</i></h6></td></tr>");
       print ("</table>");			
		
	}				
	
	if($_POST['filtro']=='1') {
		
		$l=cargar_registros_obj("
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
			WHERE ($filtro_w) AND ($busca_w) AND ($esp_w) AND hosp_solicitud 
			ORDER BY hospitalizacion.hosp_fecha_ing		
		", true);
		
	} else {
		
		$l=cargar_registros_obj("
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
			WHERE ($filtro_w) AND ($busca_w) AND ($esp_w) AND hosp_solicitud 
			ORDER BY hospitalizacion.hosp_fecha_ing 
					
		",true);
	
	}
	
	$ids='';

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$ids.=$l[$i]['hosp_id'].'|';

		if($l[$i]['hosp_numero_cama']!=0) {
			$icono='accept.png';
			$msg='Asignado';
			$l[$i]['desc_cama']='<b>'.$l[$i]['tcama_tipo'].'</b> / '.$l[$i]['cama_tipo'].'';	
		} else {
			$icono='error.png';
			$l[$i]['desc_cama']='<i>(n/a)</i>';	
		}
	
	
	}

	$ids=substr($ids,0,-1);

?>

<input type='hidden' id='ids' name='ids' value='<?php echo $ids; ?>'>
<table style='width:100%;'>
<tr class='tabla_header'>
<!--<td>Nro. Folio</td> de momento no interesa esta informacion-->
<td>Fecha Ing.</td>
<td>R.U.T.</td>
<td>Ficha</td>
<td>Nombre Paciente</td>
<td>Especialidad</td>
<td>Procedencia</td>
<td style='width:100px;'>Servicio / Sala</td>
<td>Nro. Cama</td>
<td>Cod. CIE10 / Diagnostico</td>
<td>CAT. D-R</td>
<?php if(!$xls){	?>
<?php if(_cax(251)) { ?>
<td>Editar</td>
<td>Historial</td>
<td>DEIS</td>
<?php } ?>
<?php } ?>
</tr>

<?php 

	$e=array('A1','A2','A3',
				'B1','B2','B3',
				'C1','C2','C3',
				'D1','D2','D3');

	$chtml='';
	
	for($x=0;$x<sizeof($e);$x++) {
		$chtml.='<option value="'.$e[$x].'">'.$e[$x].'</option>';
	}

	if($l)
	for($i=0;$i<sizeof($l);$i++) {
	
		if($l[$i]['hosp_folio']=='-1') {
			$l[$i]['hosp_folio']='<i>'.$l[$i]['hosp_id'].'</i>';	
		}				
	
		if($l[$i]['hosp_numero_cama']=='0') {
			$l[$i]['hosp_numero_cama']='<i>(n/a)</i>';	
			$l[$i]['desc_cama']='<i>(Sin Asignar...)</i>';	
		}	
	
		if($l[$i]['hosp_numero_cama']==-1) {
			$l[$i]['hosp_numero_cama']='(n/a)';
			$l[$i]['desc_cama']='Alta / Fecha: '.$l[$i]['hosp_fecha_egr'];	
		}
		
		if($l[$i]['hosp_procedencia']=='0') {
			$l[$i]['hosp_procedencia']='UEA</i>';	
		}	
		
		if($l[$i]['hosp_procedencia']=='1') {
			$l[$i]['hosp_procedencia']='UEI</i>';	
		}	
		
		if($l[$i]['hosp_procedencia']=='2') {
			$l[$i]['hosp_procedencia']='UEGO</i>';	
		}
		
		if($l[$i]['hosp_procedencia']=='3') {
			$l[$i]['hosp_procedencia']='Otro Hospital</i>';	
		}	
		
		if($l[$i]['hosp_procedencia']=='4') {
			$l[$i]['hosp_procedencia']='Obstetricia y Ginecolog&iacute;a</i>';	
		}		
		
		if($l[$i]['hosp_procedencia']=='5') {
			$l[$i]['hosp_procedencia']='Hospitalizaci&oacute;n</i>';	
		}
		
		if($l[$i]['hosp_procedencia']=='6') {
			$l[$i]['hosp_procedencia']='AT. Ambulatoria</i>';	
		}							
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		print("
			<tr style='height:50px;' class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td style='text-align:center;'>".$l[$i]['hosp_fecha_ing']."<br />".$l[$i]['hosp_hora_ing']."</td>
			<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
			<td style='text-aling:right;'>".$l[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
			<td style='font-size:10px;'>".$l[$i]['esp_desc']."</td>			
			<td style='text-align:center;'>".$l[$i]['hosp_procedencia']."</td> 	
			<td style='text-align:center;font-weight:bold;font-size:10px;' 
			id='desc_cama_".$l[$i]['hosp_id']."'>".$l[$i]['desc_cama']."</td>			
			<td style='text-align:center;'>
			".(($l[$i]['hosp_numero_cama']*1-$l[$i]['tcama_num_ini']*1)+1)." 
			</td>
			<td style='text-align:center;'><b>".$l[$i]['hosp_diag_cod']."</b> ".$l[$i]['diag_desc']." 
			</td>
			<td style='text-align:center;font-weight:bold;'>
			<img src='prestaciones/asignar_camas/variacion_crd.php?hosp_id=".$l[$i]['hosp_id']."&tipo=0&r=".microtime(false)."' />			
			</td>");

		if(!$xls){	

		if(_cax(251))	
		 	
		print("<td>
			<center><img src='iconos/script_edit.png' style='cursor:pointer;'
			onClick='completa_info(".$l[$i]['hosp_id'].");' /></center>			
			</td><td>
			<center><img src='iconos/report_magnify.png' style='cursor:pointer;'
			onClick='historial_info(".$l[$i]['hosp_id'].");' /></center>			
			</td><td>
			<center><img src='iconos/layout.png' style='cursor:pointer;'
			onClick='imprimir_deis(".$l[$i]['hosp_id'].");' /></center>			
			</td></center>");
			
			
		}
		
		print("</tr>");
		
	
	}
		
?>
</table>
