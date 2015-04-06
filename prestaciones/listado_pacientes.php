<?php 

	require_once('../../conectar_db.php');
	
	$fecha=pg_escape_string(utf8_decode($_POST['fecha']));
	$hora=pg_escape_string(utf8_decode($_POST['hora']));
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']));
	
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_CENSO.xls\";");			
		
	}	
	
	$l=cargar_registros_obj("
			SELECT *, hosp_fecha_ing::date AS hosp_fecha_ing, 
			hospitalizacion.hosp_id AS id 
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN censo_diario ON 
				censo_diario.hosp_id=hospitalizacion.hosp_id AND
				censo_diario.censo_fecha='$fecha $hora'
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
			WHERE (hosp_fecha_egr::date>='$fecha' OR hosp_fecha_egr IS NULL) AND hosp_fecha_ing::date<='$fecha'
			AND NOT hosp_numero_cama = 0 AND tcama_id=$tcama
			AND hosp_solicitud
			ORDER BY hosp_numero_cama, hospitalizacion.hosp_fecha_ing		
	", true);

//	if($l)
//	for($i=0;$i<sizeof($l);$i++) {

	//	if($l[$i]['hosp_numero_cama']!=0) {
	//	$icono='accept.png';
	//		$msg='Asignado';
	//		$l[$i]['desc_cama']=($l[$i]['tcama_tipo']).' / '.($l[$i]['cama_tipo']);	
	//	} else {
	//		$icono='error.png';
	//		$l[$i]['desc_cama']='<i>Sin Asignar</i>';	
	//	}
	
	
//	}

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Nro. Cama</td>
<td>Fecha Ing.</td>
<td>R.U.T. / Ficha</td>
<td>Nombre Completo</td>
<td>Tipo Cama</td>
<td>Cat. Actual</td>
<td>Cat. Paciente</td>
</tr>

<?php 

	$e=array('A1','A2','A3',
				'B1','B2','B3',
				'C1','C2','C3',
				'D1','D2','D3');

	function combocat($v) {
		
		GLOBAL $e;		
		
		$chtml='';

		if($v=='') {
			$chtml.='<option value="" SELECTED>(X)</option>';	
		}
	
		for($x=0;$x<sizeof($e);$x++) {
			if($e[$x]==$v) $sel='SELECTED'; else $sel='';
			$chtml.='<option value="'.$e[$x].'" '.$sel.'>'.$e[$x].'</option>';
		}
	
		return $chtml;	
	
	}
		
	if($l)
	for($i=0;$i<sizeof($l);$i++) {
	
		if($l[$i]['hosp_folio']=='-1') {
			$l[$i]['hosp_folio']="<i>".$l[$i]['id']."</i>";	
		}		
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
			<tr style='height:50px;' class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td style='text-align:center;font-weight:bold;'>
			".((($l[$i]['hosp_numero_cama']*1)-($l[$i]['tcama_num_ini']*1))+1)." 
			</td>
			<td style='text-align:center;'>
			".$l[$i]['hosp_fecha_ing']."<br />
			".$l[$i]['hosp_hora_ing']."</td>
			<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
			<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
			<td style='text-align:center;font-weight:bold;font-size:10px;' 
			id='desc_cama_".$l[$i]['id']."'>".$l[$i]['desc_cama']."</td>
			<td style='text-align:center;font-weight:bold;'>
			".$l[$i]['hosp_criticidad']."</td>
			<td style='text-align:center;'>");

		if($_POST['xls']*1==0)			
			print("
			<select 
			id='clase_".$l[$i]['id']."' name='clase_".$l[$i]['id']."'>
			".combocat($l[$i]['censo_diario'])."			
			</select>
			");
		else 
			print($l[$i]['censo_diario']);
						
		print("			
			</td>			
			</tr>		
		");
			
	}
		
?>
</table>
