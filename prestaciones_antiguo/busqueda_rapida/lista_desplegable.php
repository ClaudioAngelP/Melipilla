<?php 

	require_once('../../conectar_db.php');
	//error_reporting(E_ALL);
	$fecha=pg_escape_string(trim($_POST['fecha']));
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']*1));
	$filtro=$_POST['filtro']*1;
	
	if($fecha!='') {
		$fecha_w="hosp_fecha_ing::date >= '$fecha'";
	} else {
		$fecha_w='true';	
	}	
	
	if($filtro==1) {
			} else {
		$filtro_w="true";
	}	
	
	$busca=trim(pg_escape_string(utf8_decode($_POST['busqueda'])));
	
	if($busca!='') {
		$busca_w="
	    to_tsvector('spanish', pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres || ' ' || pac_ficha )
			@@ plainto_tsquery('".$busca."')
		";	
	} else {
		$busca_w="true";
	}					
	
	if($tcama  AND $_POST['filtro']=='1'){
		
   if($tcama!=-1)		
		$filtro2="AND tcama_id=$tcama";
	else
		$filtro2="";
	
	$l=cargar_registros_obj("
			SELECT *, 
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date, 
			(COALESCE(hosp_fecha_egr::date, current_date)-hosp_fecha_ing::date) AS dias_hosp
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
			WHERE ($filtro_w)	AND ($busca_w) AND ($fecha_w) AND hosp_solicitud
			AND NOT hosp_numero_cama = 0 $filtro2
			ORDER BY hospitalizacion.hosp_fecha_ing		
	", true);
	} else {
		$l=cargar_registros_obj("
			SELECT *, 
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(COALESCE(hosp_fecha_egr::date, current_date)-hosp_fecha_ing::date) AS dias_hosp 
			FROM hospitalizacion
			left JOIN doctores ON hosp_doc_id=doc_id
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
			WHERE ($filtro_w)	AND ($busca_w) AND ($fecha_w) AND hosp_solicitud
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
			$l[$i]['desc_cama']=$l[$i]['tcama_tipo'].'</b> / '.$l[$i]['cama_tipo'].'';	
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
<td>Nro. Folio</td>
<td>Fecha Ing.</td>
<td>Hora Ing.</td>
<td>R.U.T. / Ficha</td>
<td>Nombre Completo</td>
<td>Nro. Cama</td>
<td>Diagnostico</td>
<td style='width:100px;'>Estado</td>
<td>Ultima Categorizacion</td>
<td>Medico</td>
<td>Dias Hospitalizado</td>
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
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		print("
			<tr style='height:50px;' class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td style='text-align:center;font-weight:bold;'>".$l[$i]['hosp_folio']."</td>
			<td style='text-align:center;'>".$l[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'>".$l[$i]['hosp_hora_ing']."</td>
			<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
			<td style='font-size:10px;'>".htmlentities($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
			<td style='text-align:center;'>
			".(($l[$i]['hosp_numero_cama']*1-$l[$i]['tcama_num_ini']*1)+1)." 
			</td>
			<td style='text-align:center;'>".$l[$i]['hosp_diag_cod']." 
			</td>
			<td style='text-align:center;font-weight:bold;font-size:10px;' 
			id='desc_cama_".$l[$i]['hosp_id']."'>".$l[$i]['desc_cama']."</td>
			<td style='text-align:center;font-weight:bold;'>
			".$l[$i]['hosp_criticidad']."			
			</td><td style='text-align:center;font-weight:bold;'>
			".$l[$i]['doc_nombres']." ".$l[$i]['doc_paterno']." ".$l[$i]['doc_materno']."			
			</td></td><td style='text-align:center;font-weight:bold;'>
			".$l[$i]['dias_hosp']."			
			</td> </tr>");
		
			
	}
		
?>
</table>