<?php 

	require_once('../../conectar_db.php');

	function esptxt($str) {
		if(strlen($str)>30) 
			return htmlentities(substr($str,0,27).'...');
		else 
			return htmlentities($str);	
			
	}
	
	$q=cargar_registros_obj("SELECT * FROM pacientes_queue 
					WHERE func_id=".$_SESSION['sgh_usuario_id']);
					
	

?>

<table style='width:100%;'>

<tr class='tabla_header'>
<td>R.U.T.</td>
<td style='width:30%;'>Nombre</td>
<td>Folio</td>
<td>Estado</td>
<td style='width:40%;'>Documentos</td>
<td>Ver / Recep.</td>
<td>Remover</td>
</tr>

<?php 

	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0?'tabla_fila':'tabla_fila2');
	
		$pac_id=$q[$i]['pac_id'];	
	
		$ic=cargar_registros_obj("SELECT * FROM interconsulta
				LEFT JOIN instituciones ON inter_inst_id1=inst_id
				LEFT JOIN especialidades ON inter_especialidad=esp_id
				LEFT JOIN prioridad ON inter_prioridad=prior_id 
				WHERE inter_pac_id=$pac_id 
				ORDER BY inter_ingreso DESC");

		$oa=cargar_registros_obj("SELECT *, oa_fecha::date AS oa_fecha FROM orden_atencion
				LEFT JOIN instituciones ON oa_inst_id=inst_id 
				LEFT JOIN especialidades ON oa_especialidad=esp_id
				LEFT JOIN prioridad ON oa_prioridad=prior_id 
				WHERE oa_pac_id=$pac_id AND
				(NOT oa_motivo=-1)
				ORDER BY orden_atencion.oa_fecha DESC");
		
		
		$docs="<select id='pacq_".$q[$i]['pacq_id']."' 
					style='width:100%;font-size:10px;'>";
			
		$docs.="<option value='-1'>(Seleccionar...)</option>";			
				

		if($ic)
		for($j=0;$j<sizeof($ic);$j++) {
			
			if($ic[$j]['inter_estado']==-1) 
				$e='P';
			elseif($ic[$j]['inter_estado']==0)
				$e='V';
			else 
				$e='R';
						
			
			if($q[$i]['pacq_folio']!='' AND preg_match('/'.$q[$i]['pacq_folio'].'/i',$ic[$j]['inter_folio'])) {
				$sel='SELECTED';	
			} else {
				$sel='';	
			}
			
			$docs.="<option value='IC".$ic[$j]['inter_id']."' ".$sel." >IC#".$ic[$j]['inter_folio']." [$e] | ".$ic[$j]['inter_ingreso']." | ".esptxt($ic[$j]['esp_desc'])."</option>";	

		}		

		if($oa)
		for($j=0;$j<sizeof($oa);$j++) {
			
			if($oa[$j]['oa_estado']==-1) 
				$e='P';
			elseif($oa[$j]['oa_estado']==0)
				$e='V';
			else 
				$e='R';
						
			
			if($q[$i]['pacq_folio']!='' AND preg_match('/'.$q[$i]['pacq_folio'].'/i',$oa[$j]['oa_folio'])) {
				$sel='SELECTED';	
			} else {
				$sel='';	
			}
			
			$docs.="<option value='OA".$oa[$j]['oa_id']."' ".$sel." >OA#".$oa[$j]['oa_folio']." [$e] | ".$oa[$j]['oa_fecha']." | ".esptxt($oa[$j]['esp_desc'])."</option>";	

		}		

		
		$docs.="</select>";	
		
		print("
			<tr class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td style='text-align:right;font-weight:bold;'>".formato_rut($q[$i]['pacq_rut'])."</td>
			<td>".htmlentities($q[$i]['pacq_nombre'])."</td>			
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['pacq_folio']."</td>
			<td><center>
		");
		
		if($q[$i]['pacq_procesado']=='f') {
			print('<img src="iconos/clock.png" />');	
		} else {
			print('<img src="iconos/tick.png" />');		
		}					
		
		
		print("
			</center></td>			
			<td><center>".$docs."</center></td>			
			<td>
				<center>
				<img src='iconos/magnifier.png' style='cursor:pointer;' 
				onClick='verdoc(".$q[$i]['pacq_id'].")' />
				</center>
			</td>			
			<td>
				<center>
				<img src='iconos/delete.png' style='cursor:pointer;' 
				onClick='eliminar_queue(".$q[$i]['pacq_id'].");' />
				</center>
			</td>			
			</tr>		
		");		
		
	}

?>


</table>