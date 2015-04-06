<?php

	require_once('../../conectar_db.php');

	$fap_id=$_POST['fap_id']*1;
	$fcl_id=$_POST['selector']*1;

	if(isset($_POST['add_reg']) OR isset($_POST['remover'])) {
		if(!isset($_POST['remover'])) {
			$reg=pg_escape_string("\n".utf8_decode($_POST['add_reg']));
			pg_query("INSERT INTO fap_checklist VALUES (DEFAULT, '$reg', '');");
			$tmp=cargar_Registro("SELECT CURRVAL('fap_checklist_fcl_id_seq') AS id;");
			$id=$tmp['id']*1;
		} else {
			pg_query("DELETE FROM fap_checklist WHERE fcl_id=$fcl_id");
			$id=0;
		}

?>
<select id='selector' name='selector' style='width:100%;font-size:20px;' onChange='cargar_checklist();'>
<?php

        $cl=cargar_registros_obj("SELECT *, (SELECT count(*) FROM fap_checklist_detalle AS fcld WHERE fap_id=$fap_id AND fcld.fcl_id=fap_checklist.fcl_id) AS cnt FROM fap_checklist ORDER BY fcl_nombre;", true);

        for($i=0;$i<sizeof($cl);$i++) {
		if($cl[$i]['fcl_id']==$id) $sel='SELECTED'; else $sel='';
                if($cl[$i]['cnt']*1>0) $txt='<i>(Registrado)</i>'; else $txt='<i>(Pendiente)</i>';
                print("<option value='".$cl[$i]['fcl_id']."' $sel>".$cl[$i]['fcl_nombre']." $txt</option>");
        }

?>

<option value='-1'>(Agregar Nuevo Checklist...)</option>
</select>
<?php

		exit();
	}

	if(isset($_POST['edit_reg'])) {
                $reg=pg_escape_string(utf8_decode($_POST['edit_reg']));
                pg_query("UPDATE fap_checklist SET fcl_campos='$reg' WHERE fcl_id=$fcl_id;");
        }

	$c=cargar_registro("SELECT * FROM fap_checklist WHERE fcl_id=$fcl_id");

	$campos=explode("\n", $c['fcl_campos']);

	$d=cargar_registro("SELECT * FROM fap_checklist_detalle JOIN funcionario USING (func_id) WHERE fap_id=$fap_id AND fcl_id=$fcl_id ORDER BY fcld_id DESC LIMIT 1;");

	$dr=explode("\n", $d['fcld_datos']);

	$digest=Array();

	if($dr AND $dr!='')
	for($i=0;$i<sizeof($dr);$i++) {
		$tmp=explode('|',$dr[$i]);
		$digest[$tmp[0]]=$tmp[1];
	}
	//<td style='width:20%;text-align:right;'><img src='../../iconos/printer.png' onClick='imprimir_checklist(".$d['fcld_id'].");' style='cursor:pointer;width:32px;height:32px;display:inline;' /></td>
	if($d) {
		print("
		<center><table style='width:100%;border:1px solid black;background-color:#dddddd;'>
		<td><center><i>Registrado el <b>".substr($d['fcld_fecha'],0,16)."</b> por <b>".htmlentities($d['func_nombre'])."</b>.</i></center></td>
		</tr></table></center>
		");
	}
	
	
	print("<div id='checklist_editor' style='display:none;'><textarea style='width:100%;height:100px;' id='edit_reg' name='edit_reg'>".htmlentities($c['fcl_campos'])."</textarea><br/><input type='button' value='Modificar Checklist...' onClick='editar_reg();' style='width:100%;'></div><table style='width:100%;'>");
	
	$script='';	
	
	for($i=0;$i<sizeof($campos);$i++) {

		if(trim($campos[$i])=='') continue;
	
		if(strstr($campos[$i],'>>>')) {
			$cmp=explode('>>>',$campos[$i]);
			$nombre=($cmp[0]); $tipo=$cmp[1]*1;
			$nombrex=htmlentities($cmp[0]);
		} else {
			$cmp=$campos[$i]; $tipo=0;
			$nombre=($campos[$i]);
			$nombrex=htmlentities($campos[$i]);
		}
		
		if($tipo!=20 AND $tipo!=10 AND $tipo!=6)
			print("<tr>
			<td style='width:50%;text-align:right;' class='tabla_fila2'>$nombrex :</td>
			<td class='tabla_fila'>");
		else if($tipo==10 OR $tipo==6)
			print("<tr>
			<td style='width:50%;text-align:right;' valign='top' class='tabla_fila2'>$nombrex :</td>
			<td class='tabla_fila'>");
		else {
			print("<tr class='tabla_header'>
			<td style='text-align:center;font-size:18px;font-weight:bold;' class='tabla_fila2' colspan=2>$nombrex</td>
			</tr>");
			
			continue;
		}
			
		
		if($tipo==0) {

			if(isset($digest[$nombre])) {
				$sel1=($digest[$nombre]=='S')?'CHECKED':'';
				$sel2=($digest[$nombre]=='N')?'CHECKED':'';
			} else {
				$sel1='';
				$sel2='';
			}

			print("<input type='radio' id='campo_".$i."_s' name='campo_$i' value='S' $sel1 /> Si");	
			print("<input type='radio' id='campo_".$i."_n' name='campo_$i' value='N' $sel2 /> No");	

		} elseif($tipo==1) {

			if(isset($digest[$nombre])) {
				$sel1=($digest[$nombre]=='S')?'CHECKED':'';
				$sel2=($digest[$nombre]=='N')?'CHECKED':'';
			} else {
				$sel1='';
				$sel2='';
			}

			print("<input type='radio' id='campo_".$i."_s' name='campo_$i' value='S' $sel1 /> Si");	
			print("<input type='radio' id='campo_".$i."_n' name='campo_$i' value='N' $sel2 /> No");	
							
		} elseif($tipo==3) {

							if(isset($digest[$nombre])) 
								$vact=htmlentities($digest[$nombre]);
							else
                                $vact='';

			print("<input type='text' id='campo_$i' name='campo_$i' value='$vact' size=10 style='text-align:center;font-weight:bold;' onChange='validacion_fecha(this);' /><img src='../../iconos/date.png' id='campofec_$i' />");

			$script.=" Calendar.setup({
						inputField     :    'campo_$i',         // id of the input field
						ifFormat       :    '%d/%m/%Y',       // format of the input field
						showsTime      :    false,
						button          :   'campofec_$i'
					});
				";

        } elseif($tipo==5) {
		
			$opts=explode('//', $cmp[2]);
						
			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';

			print("<select id='campo_$i' name='campo_$i'>");
			
			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
				
				print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");	
			}			
			
			print("</select>");		
			
		} elseif($tipo==6) {
		
			$opts=explode('//', $cmp[2]);
				
			if(isset($digest[$nombre])) {
				$vact=htmlentities($digest[$nombre]);
				$sel_opts=explode('//',$vact);
			} else {
				$vact='';
				$sel_opts=Array();
			}

			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if(in_array($opts[$k],$sel_opts)) $sel='CHECKED'; else $sel='';
				
				print("<input type='checkbox' id='campo_".$i."_".$k."' name='campo_".$i."_".$k."' $sel > ".$opts[$k]."<br>");	
				
			}			
			
		} elseif($tipo==7) {
		
			$opts=explode('//', $cmp[2]);
						
			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';

			for($k=0;$k<sizeof($opts);$k++) {
				
				$opts[$k]=trim(htmlentities($opts[$k]));
				
				if($vact==$opts[$k]) $sel='CHECKED'; else $sel='';
				
				print("<input type='radio' id='campo_".$i."_$k' name='campo_$i' value='".$opts[$k]."' $sel>".$opts[$k]."  ");	
				
			}			
			
		} elseif($tipo==10) {

			if(isset($digest[$nombre])) 
				$vact=htmlentities(str_replace('<br>',"\n",$digest[$nombre]));
			else 
				$vact='';
			
			print("<textarea id='campo_$i' name='campo_$i' style='width:100%;150px;'>$vact</textarea>");
			
		} else {

			if(isset($digest[$nombre])) 
				$vact=htmlentities($digest[$nombre]);
			else 
				$vact='';
			
			print("<input type='text' id='campo_$i' name='campo_$i' value='$vact' size=40 />");
							
		}	
		
		print("</td></tr>");	
		
	}

	print("</table>");
		
	
	if($script!='')
	print("<script>".$script."</script>");

?>
