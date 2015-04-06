<?php

	require_once('../../conectar_db.php');

	if(isset($_POST['fs_id'])) {
		$fs_id=$_POST['fs_id']*1;
		pg_query("DELETE FROM fap_series WHERE fs_id=$fs_id");
	}

	$fap_id=$_POST['fap_id']*1;

	if(isset($_POST['fs_nombre'])) {
		$color=$_POST['fs_color'];
		$nombre=pg_escape_string(utf8_decode($_POST['fs_nombre']));
		$val_min=$_POST['fs_val_min']*1;
		$val_max=$_POST['fs_val_max']*1;	
		pg_query("INSERT INTO fap_series VALUES (DEFAULT, $fap_id, '$nombre', '$color', 100, $val_min, $val_max, '');");

	}
	
	$series=cargar_registros_obj("SELECT * FROM fap_series WHERE fap_id=$fap_id ORDER BY fs_id", true);
	
	$_horas=Array(); $__horas=Array(); $cnt=0;
	$_series=Array();
	
	for($i=0;$i<sizeof($series);$i++) {
		if($series[$i]['fs_datos']=='') continue;
		$tmp=explode("\n", $series[$i]['fs_datos']);
		for($k=0;$k<sizeof($tmp);$k++) {
			if($tmp[$k]=='') continue;
			$r=explode(' ',$tmp[$k]);
			$_series[$i][$r[0]]=$r[1];
			if(!in_array($r[0],$_horas)) {
				$_horas[$cnt]=$r[0]; 
				$tmpp=explode(':',$r[0]);
				$__horas[$cnt]=mktime($tmpp[0]*1,$tmpp[1]*1);
				$cnt++;
			}
		}
	}
	
	array_multisort($__horas, SORT_ASC, $_horas);
		
	$horas='';
	
	for($i=0;$i<10;$i++) {
		$horas.='<td style="width:60px;"><input type="text" id="horas_'.$i.'" name="horas_'.$i.'" style="width:100%;height:100%;border:none;padding:0px;margin:2px;background-color:blue;color:white;text-align:center;font-weight:bold;" value="'.(isset($_horas[$i])?$_horas[$i]:'').'" /></td>';
	}

	print("	
	<table style='width:100%;font-size:11px;' cellpadding=0 cellspacing=0><tr class='tabla_header'><td>Color</td><td>Nombre</td><td>Minimo</td><td>M&aacute;ximo</td>$horas<td>&nbsp;</td></tr>");
	

	if(!$series) {
		$series=Array();
		$series[0]['fs_id']=0;
		$series[0]['fs_nombre']='P/A';
		$series[0]['fs_color']='red';
		$series[0]['fs_val_min']='0';
		$series[0]['fs_val_max']='100';

		$series[1]['fs_id']=0;
		$series[1]['fs_nombre']='PAM';
		$series[1]['fs_color']='green';
		$series[1]['fs_val_min']='0';
		$series[1]['fs_val_max']='100';

		$series[2]['fs_id']=0;
		$series[2]['fs_nombre']='FC';
		$series[2]['fs_color']='blue';
		$series[2]['fs_val_min']='0';
		$series[2]['fs_val_max']='100';

		$series[3]['fs_id']=0;
		$series[3]['fs_nombre']='FR';
		$series[3]['fs_color']='orange';
		$series[3]['fs_val_min']='0';
		$series[3]['fs_val_max']='100';

		$series[4]['fs_id']=0;
		$series[4]['fs_nombre']='Sat O2';
		$series[4]['fs_color']='skyblue';
		$series[4]['fs_val_min']='0';
		$series[4]['fs_val_max']='100';

		$series[5]['fs_id']=0;
		$series[5]['fs_nombre']='EVA';
		$series[5]['fs_color']='violet';
		$series[5]['fs_val_min']='1';
		$series[5]['fs_val_max']='10';

		$series[6]['fs_id']=0;
		$series[6]['fs_nombre']='PVC';
		$series[6]['fs_color']='magenta';
		$series[6]['fs_val_min']='0';
		$series[6]['fs_val_max']='100';

		$series[7]['fs_id']=0;
		$series[7]['fs_nombre']='Sedación';
		$series[7]['fs_color']='yellowgreen';
		$series[7]['fs_val_min']='0';
		$series[7]['fs_val_max']='100';

		$series[8]['fs_id']=0;
		$series[8]['fs_nombre']='Bloq. Motor';
		$series[8]['fs_color']='black';
		$series[8]['fs_val_min']='0';
		$series[8]['fs_val_max']='100';

		$series[9]['fs_id']=0;
		$series[9]['fs_nombre']='Diuresis';
		$series[9]['fs_color']='yellow';
		$series[9]['fs_val_min']='0';
		$series[9]['fs_val_max']='100';

		$series[10]['fs_id']=0;
		$series[10]['fs_nombre']='Temperatura';
		$series[10]['fs_color']='purple';
		$series[10]['fs_val_min']='30';
		$series[10]['fs_val_max']='45';
		
		$series[11]['fs_id']=0;
		$series[11]['fs_nombre']='HGT';
		$series[11]['fs_color']='gray';
		$series[11]['fs_val_min']='0';
		$series[11]['fs_val_max']='100';
		
	}

	if($series)
	for($i=0;$i<sizeof($series);$i++) {
		$id=$series[$i]['fs_id']*1;
		$color=($i%2==0)?'#eeeeee':'#ffffff';

		$horas='';
		for($kk=0;$kk<10;$kk++) {
			if(isset($_series[$i][$_horas[$kk]]))
				$val=$_series[$i][$_horas[$kk]];
			else
				$val='';
			$horas.='<td style="border:1px solid lightgray;"><input type="text" id="valores_'.$i.'_'.$kk.'" name="valores_'.$i.'_'.$kk.'" style="text-align:center;border:none;margin:0px;padding:0px;width:100%;height:100%;background-color:transparent;" value="'.$val.'" /></td>';
		}
		
		
		print("<input type='hidden' id='serie_".$i."' name='serie_".$i."' value='".$id."' /><tr 
		style='background-color:$color;'><td style='width:5%;'>
		<input type='button' onClick='color_serie($id);' id='color_serie_".$i."' name='color_serie_".$i."'  value='".$series[$i]['fs_color']."' style='background-color:".$series[$i]['fs_color'].";width:100%;'>
		</td><td style='width:20%;'><input type='text' id='nombre_serie_".$i."' name='nombre_serie_".$i."' value='".$series[$i]['fs_nombre']."' style='width:100%;border:1px solid black;background-color:transparent;font-weight:bold;' size=10 /></td>
		<td style='text-align:right;'><input type='text' id='val_min_".$i."' name='val_min_".$i."' value='".$series[$i]['fs_val_min']."' size=5 style='text-align:right;' /></td>
		<td style='text-align:right;'><input type='text' id='val_max_".$i."' name='val_max_".$i."' value='".$series[$i]['fs_val_max']."' size=5 style='text-align:right;' /></td>
		$horas
		<td style='width:15%;'>
		<center><img src='../../iconos/delete.png' style='cursor:pointer;' onClick='borrar_serie($i);' /></center></td></tr><tr id='tr_editor_serie_$id' style='background-color:$color;display:none;'><td colspan=6><textarea style='width:100%;height:120px;color:yellowgreen;background-color:black;' id='editor_serie_".$i."' name='editor_serie_".$i."'>".$series[$i]['fs_datos']."</textarea></td></tr>");
	}

	
	$horas='';
	
	for($i=0;$i<10;$i++) {
		$horas.='<td>&nbsp;</td>';
	}

?>



<tr><td style='width:5%;'><input type='text' id='fs_color' name='fs_color' value='#ff0000' size=10 /></td>
<td style='width:20%;'><input type='text' id='fs_nombre' name='fs_nombre' value='Serie <?php echo $series?sizeof($series)+1:'1'; ?>' size=10 /></td>
<td style='width:5%;'><input type='text' id='fs_val_min' name='fs_val_min' value='1' size=5 /></td>
<td style='width:5%;'><input type='text' id='fs_val_max' name='fs_val_max' value='100' size=5 /></td>
<?php echo $horas; ?>
<td><center><img src='../../iconos/add.png' onClick='agregar_serie();' style='cursor:pointer;'/></center></td>

<?php

	print("</table>
	
		<input type='button' style='cursor:pointer;width:100%;font-size:24px;' onClick='guardar_serie();' value='Guardar Datos...' />

	
	<input type='hidden' id='num_series' name='num_series' value='".sizeof($series)."' />");

?>
