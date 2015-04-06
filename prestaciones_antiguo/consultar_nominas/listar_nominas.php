<?php 

	require_once('../../conectar_db.php');
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
	$esp_id=$_POST['esp_id']*1;
	$doc_id=$_POST['doc_id']*1;
	$orden=$_POST['orden']*1;

   if($esp_id!=-1) {
  	 $w_esp='nom_esp_id='.$esp_id;	
   } else {
  	 $w_esp='true';
   }

   if($doc_id!=-1) {
  	 $w_doc='nom_doc_id='.$doc_id;	
   } else {
  	 $w_doc='true';
   }
   
   if($_POST['xls']*1==1) {
   
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_nominas.xls\";");   
   	
	}
	
	if($orden==0) {
		$orden='nom_folio';	
	} elseif($orden==1) {
		$orden='nom_fecha, nom_folio';
	} else {
		$orden='esp_desc, nom_folio';	
	}	
	   	
	$l=cargar_registros_obj("
		SELECT nomina.*, 
		nom_fecha::date,
		date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
		nomd_tipo, nomd_sficha, nomd_destino, nomd_motivo, nomd_auge, 
		nomd_diag_cod, nomd_extra,
		sex_id, esp_desc,
		pac_rut, pac_appat, pac_apmat, pac_nombres,
		doc_rut, doc_paterno, doc_materno, doc_nombres  
 		FROM nomina
 		LEFT JOIN especialidades ON nom_esp_id=esp_id
		LEFT JOIN doctores ON nom_doc_id=doc_id
		LEFT JOIN nomina_detalle USING (nom_id)
		LEFT JOIN pacientes USING (pac_id)
		WHERE 
			nomina_detalle.pac_id>0 AND
			nom_fecha::date>='$fecha1' AND 
			nom_fecha::date<='$fecha2' AND
			$w_esp AND $w_doc
		ORDER BY $orden
	", true);	


	
		$num_ausente=0;
		$num_presente=0;
		$num_nuevo=0;
		$num_control=0;	
		$num_extra=0;
		$num_masc=0;
		$num_feme=0;
		$num_altas=0;
		
		$geta=array(0,0,0,0,0,0);
		$getn=array('< 10','10-14','15-19','20-24','25-64','> 65');
	
		for($i=0;$i<sizeof($l);$i++) {
		
			$r=$l[$i];		
		
			$val=$r['nomd_diag_cod'];
			
			if($val=='NSP')
				$num_ausente++;
			else
				$num_presente++;		
			
			if($val!='NSP') {
			
				$val=$r['nomd_tipo'];
				
				if($val=='N')
					$num_nuevo++;
				else
					$num_control++;
					
				$val=$r['nomd_extra'];
				
				if($val=='S')
					$num_extra++;
					
				$val=$r['edad']*1;

				if($val<10) { $geta[0]++; }
				if($val>=10 AND $val<=14) { $geta[1]++; }
				if($val>=15 AND $val<=19) { $geta[2]++; }
				if($val>=20 AND $val<=24) { $geta[3]++; }
				if($val>=25 AND $val<=64) { $geta[4]++; }
				if($val>=65) { $geta[5]++; }
				
				$val=$r['sex_id']*1;
				
				if($val==0)
					$num_masc++;
				else
					$num_feme++;
					
				$val=$r['nomd_destino']*1;
				
				if($val==6)
					$num_altas++;
				
			}
				
		}
		
		if(sizeof($l)>0) {
			$factor=100/sizeof($l);
			$factor2=100/$num_presente;
		} else {
			$factor=0;
			$factor2=0;
		}
		
		$html='<table style="width:100%;"><tr><td>';
		
		$html.='<table style="width:100%;"><tr class="tabla_header"><td colspan=3>Indicadores de las N&oacute;minas</td></tr>';
		
		$html.='<tr class="tabla_fila"><td style="text-align:right;width:40%;">Asisten:</td><td style="font-weight:bold;text-align:center;width:20%;">'.$num_presente.'</td><td style="text-align:center;">'.number_format($num_presente*$factor,2,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila2"><td style="text-align:right;">Ausentes:</td><td style="font-weight:bold;text-align:center;">'.$num_ausente.'</td><td style="text-align:center;">'.number_format($num_ausente*$factor,2,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila"><td style="text-align:right;">Pac. Nuevos:</td><td style="font-weight:bold;text-align:center;">'.$num_nuevo.'</td><td style="text-align:center;">'.number_format($num_nuevo*$factor2,2,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila2"><td style="text-align:right;">Pac. Control:</td><td style="font-weight:bold;text-align:center;">'.$num_control.'</td><td style="text-align:center;">'.number_format($num_control*$factor2,2,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila"><td style="text-align:right;">Cant. Extras:</td><td style="font-weight:bold;text-align:center;">'.$num_extra.'</td><td style="text-align:center;">'.number_format($num_extra*$factor,2,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila2"><td style="text-align:right;">Masc./Fem.:</td><td style="font-weight:bold;text-align:center;">'.$num_masc.'/'.$num_feme.'</td><td style="text-align:center;">'.number_format($num_masc*$factor2,0,',','.').'%/'.number_format($num_feme*$factor2,0,',','.').'%</td></tr>';	
		$html.='<tr class="tabla_fila"><td style="text-align:right;">Altas:</td><td style="font-weight:bold;text-align:center;">'.$num_altas.'</td><td style="text-align:center;">'.number_format($num_altas*$factor,2,',','.').'%</td></tr>';	
		
		$html.='</table>';
		
		$html.='</td><td>';
		
		$html.='<table style="width:100%;"><tr class="tabla_header"><td colspan=3>Grupos Et&aacute;reos</td></tr>';
		
		for($j=0;$j<sizeof($getn);$j++) {
			$clase=($j%2==0)?'tabla_fila':'tabla_fila2';		
			$html.='<tr class="'.$clase.'"><td style="text-align:right;width:40%;">'.$getn[$j].':</td><td style="font-weight:bold;text-align:center;width:20%;">'.$geta[$j].'</td><td style="text-align:center;">'.number_format($geta[$j]*$factor2,2,',','.').'%</td></tr>';
		}	
				
		$html.='</table>';
		
		$html.='</td></tr></table>';

		echo $html;



?>

<table style='width:100%;font-size:8px;'>
<tr class='tabla_header'>

<td>#</td>

<td>Fecha</td>

<td>Nro. Folio</td>
<td>Especialidad</td>

<td>RUT M&eacute;dico</td>
<td>Nombre M&eacute;dico</td>
<td>RUT Paciente</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombre</td>
<td>Sexo</td>
<td>Edad</td>

<td>Tipo</td>
<td>Extra</td>
<td>S/Ficha</td>
<td>CIE10</td>
<td>Motivo</td>
<td>Destino</td>
<td>AUGE</td>

</tr>

<?php 

	if($l) {
			
	
		for($i=0;$i<sizeof($l);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($l[$i]['sex_id']==0)
		    	$sexo='M';
		    elseif($l[$i]['sex_id']==1)
		    	$sexo='F';
		    else 
		    	$sexo='I';

			print("
				<tr class='$clase'>
				
				<td align='center'>".($i+1)."</td>
				
				<td align='center'>".$l[$i]['nom_fecha']."</td>
				
				<td align='center'><b>".$l[$i]['nom_folio']."</b></td>
				<td align='center'>".$l[$i]['esp_desc']."</td>

				<td align='right'>".$l[$i]['doc_rut']."</td>
				<td>".(strtoupper($l[$i]['doc_nombres'].' '.$l[$i]['doc_paterno'].' '.$l[$i]['doc_materno']))."</td>    

				<td align='right'>".$l[$i]['pac_rut']."</td>
				<td align='left'>".$l[$i]['pac_appat']."</td>
				<td align='left'>".$l[$i]['pac_apmat']."</td>
				<td align='left'>".$l[$i]['pac_nombres']."</td>
				
				<td align='center'>".$sexo."</td>
				<td align='center'>".$l[$i]['edad']."</td>
				
				<td align='center'>".$l[$i]['nomd_tipo']."</td>
				<td align='center'>".$l[$i]['nomd_extra']."</td>
				<td align='center'>".$l[$i]['nomd_sficha']."</td>
				<td align='center'>".$l[$i]['nomd_diag_cod']."</td>
				<td align='center'>".$l[$i]['nomd_motivo']."</td>
				<td align='center'>".$l[$i]['nomd_destino']."</td>
				<td align='center'>".$l[$i]['nomd_auge']."</td>	

				</tr>			
			");		
			
			flush();			
			
		}
		
	}

?>

</table>
