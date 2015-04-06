<?php 
	require_once('../../conectar_db.php');
	
	function graficar_censos($censos,$iqs) {
		$datos=explode('|', $censos);
		$min=0;
		$min_n=0;
		$max=0;
		$min_n=0;
		$grafico=Array();
		for($i=0;$i<sizeof($datos);$i++) {
			$tmp=explode('//',$datos[$i]);
			$v = $tmp[1];
			$letra=$v[0];
			switch($letra) {
				case 'A': $val= 13-($v[1]*1); break;
				case 'B': $val= 10-($v[1]*1); break;
				case 'C': $val=  7-($v[1]*1); break;
				case 'D': $val=  4-($v[1]*1); break;
				default:  $val=0;
			}
			$grafico[$i][0]=$val;
			$grafico[$i][1]=$v;
			if($tmp[0]!='') {
				$_tmp=explode('/',$tmp[0]);
				$grafico[$i][2]=mktime(0,0,0,$_tmp[1],$_tmp[0],$_tmp[2]);
			}
			if($i==0) {
				$max=$val; $min=$val; $max_n=$i; $min_n=$i; $max_t=$v; $min_t=$v;
			} else {
				if($val>=$max) { $max=$val; $max_n=$i; $max_t=$v; }
				if($val<$min) { $min=$val; $min_n=$i; $min_t=$v; }
			}
			if($i==sizeof($datos)-1) { $ult=$val; $ult_n=$i; $ult_t=$v; }
		}
		if(sizeof($datos)==1) {
			// Si tiene solo un registro agrega otro para que aparezca una línea y no un punto...
			$grafico[$i][0]=$val;
			$grafico[$i][1]=$v;
			if($tmp[0]!='') {
				$_tmp=explode('/',$tmp[0]);
				$grafico[$i][2]=mktime(0,0,0,$_tmp[1],$_tmp[0],$_tmp[2]);
			}
			$ult=$val; $ult_n=$i; $ult_t=$v;
		}
		$svg='<svg width="175px" height="40px">';
		$nregs=sizeof($grafico);
		if($min!=$max)
			$dif=35/($max-$min);
		else
			$dif=-1;
		  
		$offsetx=5;
		$offsety=2.5;
		  
		for($i=1;$i<$nregs;$i++) {
			$x=round($offsetx+(150/($nregs-1))*($i-1),4);
			if($dif!=-1)
				$y=round(40-($offsety+(($grafico[$i-1][0]-$min)*$dif)),4);
			else
				$y=35;
			$x2=round($offsetx+(150/($nregs-1))*$i,4);
			if($dif!=-1)
				$y2=round(40-($offsety+(($grafico[$i][0]-$min)*$dif)),4);
			else
				$y2=35;
			if($grafico[$i][1][0]=='A') {
				$color="red";
			} elseif($grafico[$i][1][0]=='B') {
				$color="orange";
			} elseif($grafico[$i][1][0]=='C') {
				$color="green";
			} else {
				$color="blue";	  
			}
			$svg.="<line x1='$x' y1='$y' x2='$x2' y2='$y2' style='stroke-width: 1.5; stroke: ".$color.";'/>";
			$svg.="<circle cx='$x2' cy='$y2' r='1' style='fill: ".$color."; stroke: ".$color.";'/>";
		}
		  
		if($ult_t[0]=='A') {
			$color="red";
		} elseif($ult_t[0]=='B') {
			$color="orange";
		} elseif($ult_t[0]=='C') {
			$color="green";
		} else {
			$color="blue";	  
		}

		if($max_t[0]=='A') {
			$color2="red";
		} elseif($max_t[0]=='B') {
			$color2="orange";
		} elseif($max_t[0]=='C') {
			$color2="green";
		} else {
			$color2="blue";	  
		}
		  
		if($ult_t=='') $ult_t='??';
		  
		$svg.="<text x='160' y='25' font-weight='bold' 
		font-family='Verdana' font-size='10' fill='$color' >
		".$ult_t."
		</text>";
		/*
		if($tipo==1) {
			if($max_t!=$ult_t)
				$grafico->setFeaturePoint($max_n, $max, $color2 , $max_t, TEXT_TOP,  FONT_1, 3);
			
			$grafico->setFeaturePoint($ult_n, $ult, $color , $ult_t, TEXT_RIGHT, FONT_2, 6);
		} else {
			if($max_t!=$ult_t)
				$grafico->setFeaturePoint($max_n, $max, $color2 , $max_t, TEXT_TOP, FONT_1, 2);
			
			$grafico->setFeaturePoint($ult_n, $ult, $color , $ult_t, TEXT_RIGHT, FONT_1, 3);  
		}
		*/
		if($iqs!='') {
			$min_date=$grafico[0][2];
			$max_date=$grafico[sizeof($grafico)-1][2];
			//print($max_date.'max '.$min_date.'min ');
			if($min_date!=$max_date)
				$diff_date=150/($max_date-$min_date);
			else
				$diff_date=-1;
			
			$datos=explode('|', $iqs);
			
			for($i=0;$i<sizeof($datos);$i++) {
				$_tmp=explode('/',$datos[$i]);
				$_date=mktime(0,0,0,$_tmp[1],$_tmp[0],$_tmp[2]);
				//print($_date.'?? ');
				if($_date<$min_date OR $_date>$max_date) {
					//print($_date.'XX ');
				}
		
				if($diff_date!=-1) {
					$x=$offsetx+(($_date-$min_date)*$diff_date);
					//print($x.'## ');
				} else
					$x=$offsetx+150;
				
				$svg.="<line x1='$x' y1='0' x2='$x' y2='40' style='stroke-width: 1.5; stroke: black;'/>";		
			}
		}
		$svg.="</svg>";
		return $svg;
	}
	$fecha=pg_escape_string(utf8_decode($_POST['fecha']));
	$hora=pg_escape_string(utf8_decode($_POST['hora']));
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']));
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
		header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_CENSO.xls\";");			
		
	}
	
	$consulta="
	SELECT *,
	CASE WHEN tcama_correlativo=true THEN
		((CASE WHEN hosp_numero_cama>0 THEN hosp_numero_cama ELSE hosp_cama_egreso END *1)-(tcama_num_ini*1)+1)
	ELSE 	
		(((cama_censo*1)-(cama_num_ini*1))+1)
	END AS nro_cama,
	array_to_string(ARRAY(
			
				SELECT DISTINCT (censo_fecha || '//' || censo_diario) FROM (
				
				SELECT * FROM (
			  
				SELECT censo_fecha::date, censo_diario FROM censo_diario WHERE censo_diario.hosp_id=foo.hosp_id AND censo_fecha::time='11:00:00'
				UNION
				SELECT hosp_fecha_ing::date AS censo_fecha, hosp_criticidad AS censo_diario FROM hospitalizacion AS h2 WHERE h2.hosp_id=foo.hosp_id
				
				) AS temp_foo 
				
				ORDER BY censo_fecha
				
				) AS foooo
								
			),'|') AS censos,
			array_to_string(ARRAY(
			
				SELECT DISTINCT fap_fecha::date FROM fap_pabellon WHERE pac_id=foo.hosp_pac_id AND fap_fecha BETWEEN foo.hosp_fecha_ing AND CURRENT_DATE
								
			),'|') AS iqs
			
			FROM (
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
	";
	
	//print($consulta);
	$l=cargar_registros_obj($consulta, true);
	
	$cant_q = cargar_registro("SELECT count(*) as cant FROM(
				SELECT *,  COALESCE((
								SELECT ptras_cama_destino 
								FROM paciente_traslado AS p1
								WHERE p1.hosp_id=h1.hosp_id AND ptras_fecha<='$fecha $hora'
								ORDER BY ptras_fecha DESC, ptras_id DESC
								LIMIT 1
							), hosp_numero_cama) AS cama_censo
				FROM censo_diario
				JOIN hospitalizacion as h1 USING(hosp_id)
				LEFT JOIN clasifica_camas ON tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
				WHERE censo_fecha='$fecha $hora' AND tcama_id=$tcama) foo");
	$cant_censados=$cant_q['cant']*1;

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
		<?php
		if(!(isset($_POST['xls']) AND $_POST['xls']=='1')) {  
		?>
		<td colspan=2>Acciones</td> <?php }?>
	</tr>
	<?php 
	$e=array('A1','A2','A3','B1','B2','B3','C1','C2','C3','D1','D2','D3');
	function combocat($v) {
		GLOBAL $e;		
		$chtml='';
		if($v=='') {
			$chtml.='<option value="" SELECTED>(X)</option>';	
		}
		for($x=0;$x<sizeof($e);$x++) {
			if($e[$x]==$v)
				$sel='SELECTED';
			else
				$sel='';
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
			<tr style='height:50px;' class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
				<td style='text-align:center;font-weight:bold;'>
					".$l[$i]['nro_cama']." 
				</td>
				<td style='text-align:center;'>
					".$l[$i]['hosp_fecha_ing']."<br />
					".$l[$i]['hosp_hora_ing']."
				</td>
				<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
				<td style='font-size:10px;'>".($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
				<td style='text-align:center;font-weight:bold;font-size:10px;' id='desc_cama_".$l[$i]['id']."'>".$l[$i]['desc_cama']."</td>
				<td style='text-align:center;font-weight:bold;'>
					".graficar_censos($l[$i]['censos'],$l[$i]['iqs'])."
				</td>
			");
			/*
			<td style='text-align:center;font-weight:bold;'>".$l[$i]['hosp_criticidad']."</td>
			*/
			if($_POST['xls']*1==0) {
				if($l[$i]['censo_diario']!='') {
					$option="<option value='".$l[$i]['censo_diario']."' SELECTED>".$l[$i]['censo_diario']."</option>";
					$tipo=1; $id_ver=$l[$i]['censo_id'];
				} else { 
					$option="";
					$tipo=0; $id_ver=$l[$i]['id'];
				}
				print("
				<td style='text-align:center;'>
					<input type='hidden' id='sel_".$l[$i]['id']."' name='sel_".$l[$i]['id']."' value='".$l[$i]['censo_riesgodependencia']."'>
					<select onClick='categorizar_paciente($tipo,$id_ver,".$l[$i]['id'].");' id='clase_".$l[$i]['id']."' name='clase_".$l[$i]['id']."'>
						<option value=''>(X)</option>
						".$option."
					</select>
				</td>
				");
				
				print("
				<td>
					<center>
						<img src='iconos/script_edit.png' style='cursor:pointer;' onClick='completa_info(".$l[$i]['id'].");' />
					</center>
				</td>
				<td>
					<center>
						<img src='iconos/report_magnify.png' style='cursor:pointer;' onClick='historial_info(".$l[$i]['id'].");' />
					</center>
				</td>
				");
			} else 
				print("<td style='text-align:center;'>".$l[$i]['censo_diario']."</td>");
			
			print("
				</td>
			</tr>
			");
		}
		?>
</table>
<script>
	$('td_reg_t').innerHTML="<?php 
	if($l)
		print(''.sizeof($l).'');
	else
		print('0'); ?>";
	$('td_reg_c').innerHTML="<?php print($cant_censados); ?>";
</script>



