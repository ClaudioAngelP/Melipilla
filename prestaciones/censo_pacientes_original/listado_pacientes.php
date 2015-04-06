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
			SELECT * FROM (
			
			SELECT *, h1.hosp_fecha_ing::date AS hosp_fecha_ingreso, 
			h1.hosp_id AS id, COALESCE((
				SELECT ptras_cama_destino 
				FROM paciente_traslado AS p1
				WHERE p1.hosp_id=h1.hosp_id AND ptras_fecha<='$fecha $hora'
				ORDER BY ptras_fecha DESC, ptras_id DESC
				LIMIT 1
			),hosp_numero_cama) AS cama_censo
			FROM hospitalizacion AS h1
			WHERE (h1.hosp_fecha_egr>='$fecha $hora' OR h1.hosp_fecha_egr IS NULL) AND COALESCE(h1.hosp_fecha_hospitalizacion, h1.hosp_fecha_ing)<='$fecha $hora'
			AND NOT hosp_numero_cama = 0
			AND hosp_solicitud
			
			) AS foo
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN censo_diario ON 
				censo_diario.hosp_id=foo.hosp_id AND
				censo_diario.censo_fecha='$fecha $hora'
			LEFT JOIN tipo_camas ON
				cama_num_ini<=cama_censo AND cama_num_fin>=cama_censo
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=cama_censo AND tcama_num_fin>=cama_censo
			WHERE 
				tcama_id=$tcama
				
			ORDER BY cama_censo, foo.hosp_fecha_ing		
	", true);

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		if($l[$i]['cama_censo']!=0) {
			$icono='accept.png';
			$msg='Asignado';
			$l[$i]['desc_cama']=($l[$i]['tcama_tipo']).' / '.($l[$i]['cama_tipo']);	
		} else {
			$icono='error.png';
			$l[$i]['desc_cama']='<i>Sin Asignar</i>';	
		}
	
	
	}

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Nro. Cama</td>
<td>Fecha Ing.</td>
<td>R.U.T. / Ficha</td>
<td>Nombre Completo</td>
<td>Tipo Cama</td>
<td>Cat. &Uacute;ltima</td>
<td>Cat. Paciente</td>
<?php if(!(isset($_POST['xls']) AND $_POST['xls']=='1')) {  ?> <td colspan=2>Acciones</td> <?php }?>
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
			".((($l[$i]['cama_censo']*1)-($l[$i]['tcama_num_ini']*1))+1)." 
			</td>
			<td style='text-align:center;'>
			".$l[$i]['hosp_fecha_ing']."<br />
			".$l[$i]['hosp_hora_ing']."</td>
			<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
			<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
			<td style='text-align:center;font-weight:bold;font-size:10px;' 
			id='desc_cama_".$l[$i]['id']."'>".$l[$i]['desc_cama']."</td>
			
			<td style='text-align:center;font-weight:bold;'>
			<img src='prestaciones/asignar_camas/variacion_crd.php?hosp_id=".$l[$i]['id']."&tipo=0&r=".microtime(false)."' />	
			</td>
			");
			
		/*
		 <td style='text-align:center;font-weight:bold;'>
			".$l[$i]['hosp_criticidad']."</td>
			
		 * */

		if($_POST['xls']*1==0) {
			
			print("
			<td style='text-align:center;'><select 
			id='clase_".$l[$i]['id']."' name='clase_".$l[$i]['id']."'>
			".combocat($l[$i]['censo_diario'])."			
			</select></td>
			");
			
			print("<td>
			<center><img src='iconos/script_edit.png' style='cursor:pointer;'
			onClick='completa_info(".$l[$i]['id'].");' /></center>			
			</td><td>
			<center><img src='iconos/report_magnify.png' style='cursor:pointer;'
			onClick='historial_info(".$l[$i]['id'].");' /></center>			
			</td></center>");

		} else 
			print("<td style='text-align:center;'>".$l[$i]['censo_diario']."</td>");
						
		print("			
			</td>			
			</tr>		
		");
			
	}
		
?>
</table>
