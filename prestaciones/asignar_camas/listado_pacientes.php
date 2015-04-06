<?php 
	ini_set('memory_limit', '256M');
	require_once('../../conectar_db.php');
	//error_reporting(E_ALL);
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']*1));
	$filtro=$_POST['filtro']*1;
	$esp_id=$_POST['esp_id']*1;
    $fecha_inicio=$_POST['fecha_hosp'];
	$fecha_termino=$_POST['fecha_hosp2'];	
	if($esp_id!=0) {
		$esp_w="(hosp_esp_id=$esp_id OR hosp_esp_id2=$esp_id)";
	} else {
		$esp_w='true';
	}
	
	$filtro_fecha = "hosp_fecha_ing>='$fecha_inicio 00:00:00' AND hosp_fecha_ing<='$fecha_termino 23:59:59'";
			
	if($filtro==0) {
		if($tcama!=-1)
			$tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_fecha_egr IS NULL AND hosp_numero_cama=0 AND hosp_anulado=0 AND $tcama_w";	
	} elseif($filtro==1) {
		if($tcama!=-1)
			$tcama_w='tcama_id='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_fecha_egr IS NULL AND NOT hosp_numero_cama<=0 AND $tcama_w";
	} elseif($filtro==3) {
		if($tcama!=-1)
			$tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_doc_id =-1 AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND $tcama_w";	
	} elseif($filtro==4) {
		$filtro_w="hosp_anulado=1";
	}elseif($filtro==5) {
		if($tcama!=-1)
			$tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="(hosp_fecha_egr IS NULL AND hosp_numero_cama=0 AND hosp_anulado=0 OR hosp_fecha_egr IS NULL AND NOT hosp_numero_cama<=0) AND $tcama_w";	
	}else {
		if($tcama!=-1)
			$tcama_w='hosp_servicio='.$tcama; else $tcama_w='true';
		$filtro_w="hosp_anulado=0 AND $tcama_w";
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
		print ("<h1><b>Reporte Gesti&oacute;n Centralizada de Camas</b></h1>");	
		print ("<br><h5><i>Gesti&oacute;n de Camas</i></h5>");	
		if($tcama!=-1) {
			$serv=cargar_registro("SELECT tcama_tipo FROM clasifica_camas WHERE tcama_id=$tcama");
			$serv_det=$serv['tcama_tipo'];
	    } else {
			$serv_det='Todos los Servicios';
		}
		print("<br><b>Servicio: $serv_det</b><br><b>Fecha:</b> $fecha_inicio al $fecha_termino");
	}
	if($_POST['cuentaCte']*1!='') {
		$hosp_id=$_POST['cuentaCte']*1;
		$filtro_cuenta="hosp_id=$hosp_id";
	} else {
		$filtro_cuenta="true";
	}
	
	if($_POST['filtro']=='1') {
		$l=cargar_registros_obj("
		SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat, 
		hosp_fecha_ing::date AS hosp_fecha_ing,
		hosp_fecha_ing::time AS hosp_hora_ing,
		hosp_fecha_egr::date,
		COALESCE(diag_desc, hosp_diagnostico) AS diag_desc,
		pac_fc_nac::date,
		ciud_desc
		FROM hospitalizacion
		JOIN pacientes ON hosp_pac_id=pac_id
		LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
		LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod			
		LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
		LEFT JOIN clasifica_camas ON tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
		LEFT JOIN comunas USING(ciud_id)
		WHERE ($filtro_w) AND ($busca_w) AND ($esp_w) AND hosp_solicitud AND ($filtro_cuenta) AND ($filtro_fecha)
		ORDER BY hosp_id, hospitalizacion.hosp_fecha_ing		
		", true);
	} elseif($_POST['filtro']=='5') {
		$l=cargar_registros_obj("
		SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat, 
		hosp_fecha_ing::date AS hosp_fecha_ing,
		hosp_fecha_ing::time AS hosp_hora_ing,
		hosp_fecha_egr::date,
		COALESCE(diag_desc, hosp_diagnostico) AS diag_desc,
		pac_fc_nac::date,
		ciud_desc
		FROM hospitalizacion
		JOIN pacientes ON hosp_pac_id=pac_id
		LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
		LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod			
		LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
		LEFT JOIN clasifica_camas ON tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
		LEFT JOIN comunas USING(ciud_id)
		WHERE ($filtro_w) AND ($busca_w) AND ($esp_w) AND hosp_solicitud AND ($filtro_cuenta) AND ($filtro_fecha)
		ORDER BY hosp_id, hospitalizacion.hosp_fecha_ing		
		", true);
	} else {
		$consulta="SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat, 
		hosp_fecha_ing::date AS hosp_fecha_ing,
		hosp_fecha_ing::time AS hosp_hora_ing,
		hosp_fecha_egr::date,
		COALESCE(diag_desc, hosp_diagnostico) AS diag_desc,
		pac_fc_nac::date,
		ciud_desc
		FROM hospitalizacion
		JOIN pacientes ON hosp_pac_id=pac_id
		LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
		LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod			
		LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
		LEFT JOIN clasifica_camas ON tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
		LEFT JOIN comunas USING(ciud_id)
		WHERE ($filtro_w) AND ($busca_w) AND ($esp_w) AND hosp_solicitud AND ($filtro_cuenta) AND ($filtro_fecha)
		ORDER BY hosp_id, hospitalizacion.hosp_fecha_ing";
		//print($consulta);
		$l=cargar_registros_obj($consulta,true);
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
<?php if($_POST['xls']!='1'){ ?>
	<table style='width:100%;'>
		<tr class='tabla_header'>	
<?php } else {
		print("<table border='1'><tr>");		
	}
?>
<!--<td>Nro. Folio</td> de momento no interesa esta informacion-->
<td>Cta. Corriente</td>
<td>Fecha Ing.</td>
<td>R.U.T.</td>
<td>Fecha Nac.</td>
<td>Comuna</td>
<td>Ficha</td>
<td>Nombre Paciente</td>
<!--<td>Especialidad</td>-->
<td>Procedencia</td>
<td style='width:100px;'>Servicio / Sala</td>
<!--<td>Nro. Cama</td>-->
<?php if($_POST['xls']!='1'){	?>
<td>CAT. D-R</td>
<td>Cod. CIE10 / Diagnostico</td>
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

	if($l){
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
				$l[$i]['hosp_procedencia']='</i>';	
			}	
			
			if($l[$i]['hosp_procedencia']=='1') {
				$l[$i]['hosp_procedencia']='Unidad De Emergencia (del mismo establecimiento)</i>';	
			}	
			
			if($l[$i]['hosp_procedencia']=='2') {
				$l[$i]['hosp_procedencia']='APS</i>';	
			}
			
			if($l[$i]['hosp_procedencia']=='3') {
				$l[$i]['hosp_procedencia']='CAE (atenci&oacute;n especialidades mismo establecimiento)</i>';	
			}	
			
			if($l[$i]['hosp_procedencia']=='4') {
				$l[$i]['hosp_procedencia']='Otro Establecimiento de la RED</i>';	
			}		
			
			if($l[$i]['hosp_procedencia']=='5') {
				$l[$i]['hosp_procedencia']='Otra Procedencia (RN proveniente de la maternidad)</i>';	
			}
			
			if($l[$i]['hosp_procedencia']=='6') {
				$l[$i]['hosp_procedencia']='AT. Ambulatoria</i>';	
			}							
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($_POST['xls']!='1'){
				print("<tr style='height:50px;' class='$clase'
						onMouseOver='this.className=\"mouse_over\";'
						onMouseOut='this.className=\"$clase\";'>");
			}else{
				print("<tr>");
			}

			print("
				<td style='text-align:center;'>".$l[$i]['hosp_id']."</td>
				<td style='text-align:center;'>".$l[$i]['hosp_fecha_ing']." - ".$l[$i]['hosp_hora_ing']."</td>
				<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
				<td style='text-align:center;'>".$l[$i]['pac_fc_nac']."</td>
				<td style='text-align:center;'>".$l[$i]['ciud_desc']."</td>
				<td style='text-aling:right;'>".$l[$i]['pac_ficha']."</td>
				<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat']." ".$l[$i]['pac_nombres'])."</td>");
				//<td style='font-size:10px;'>".$l[$i]['esp_desc']."</td>			
			print("<td style='text-align:center;'>".$l[$i]['hosp_procedencia']."</td> 	
				<td style='text-align:center;font-weight:bold;font-size:10px;' 
				id='desc_cama_".$l[$i]['hosp_id']."'>".$l[$i]['desc_cama']."</td>");	
				/**<td style='text-align:center;'>
				".(($l[$i]['hosp_numero_cama']*1-$l[$i]['tcama_num_ini']*1)+1)." 
				</td>
				");*/
				
			if($_POST['xls']!='1'){

			if(_cax(251))
			
			print("<td style='text-align:center;font-weight:bold;'>
				<img src='prestaciones/asignar_camas/variacion_crd.php?hosp_id=".$l[$i]['hosp_id']."&tipo=0&r=".microtime(false)."' />			
				</td>");
			print("<td style='text-align:left; font-size:10px'><b>".$l[$i]['hosp_diag_cod']."</b> ".$l[$i]['diag_desc']." </td>
				<td>");

			if($l[$i]['hosp_numero_cama']!='(n/a)') {
				print("<center><img src='iconos/script_edit.png' style='cursor:pointer;'
				onClick='completa_info(".$l[$i]['hosp_id'].");' /></center>");
			}else{
				if(_cax(260)){
				print("<center><img src='iconos/arrow_undo.png' style='cursor:pointer;'
				onClick='revertir_alta(".$l[$i]['hosp_id'].");' /></center>");	
				}
			}
			print("</td><td>
				<center><img src='iconos/report_magnify.png' style='cursor:pointer;'
				onClick='historial_info(".$l[$i]['hosp_id'].");' /></center>			
				</td><td>
				<center><img src='iconos/layout.png' style='cursor:pointer;'
				onClick='imprimir_deis(".$l[$i]['hosp_id'].");' /></center>			
				</td></center>");
				
				
			}
			
			print("</tr>");
			
		
		}
	}else{
		print("<tr><td colspan=13><center>Sin Registros...</center></td></tr>");
	}
		
?>
</table>
