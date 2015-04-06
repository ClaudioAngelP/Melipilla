<?php 

    require_once('../../conectar_db.php');

	function checkDateFormat($date)
	{
	  //match the format of the date
	  if (preg_match ("/^([0-9]{2})\/([0-9]{2})\/([0-9]{2,4})$/", $date, $parts))
	  {
		//check weather the date is valid of not
			if(checkdate($parts[2],$parts[1],$parts[3]))
			  return true;
			else
			 return false;
	  }
	  else
		return false;
	}

   
	/*


drop table lista_dinamica_caso;
drop table lista_dinamica_instancia;
drop table monitoreo_ges;
drop table monitoreo_ges_registro;
drop table lista_dinamica_proceso;
drop table lista_dinamica_condiciones;
drop table lista_dinamica_bandejas;
drop table patologias_sigges_traductor;


	 * */
   
    function tfecha($str) {
    
        $str = explode('/', trim($str));
        
        //return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)-1),(($str[2]*1)-4)));
        return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)),(($str[2]*1))));
    
    }


	$csv_errores='';
	
	
	function log_error($ln, $msg) {
		
		GLOBAL $csv_errores, $registros;
		
		$csv_errores.=str_replace("\r",'',$registros[$ln]).";$msg;\r\n";
		
	}
	
	
    $editadas=0;
    $error=0;
	$log="<table style='width:100%;' border=1>
			  <tr style='text-align:center;font-weight:bold;'>
				  <td>Fila</td>
				  <td>Error</td>
			  </tr>";
   
    $tipo_carga=$_POST['tipo_carga']*1;
	$registros=explode("\n",(file_get_contents($_FILES['vigentes']['tmp_name'])));

	$totalr=sizeof($registros);

	for($i=0;$i<$totalr;$i++) {

		$reg=explode(";",trim($registros[$i]));
		
		for($k=0;$k<sizeof($reg);$k++) {
			$reg[$k]=trim($reg[$k],'"');
		}
		
		$test=explode('/',trim(str_replace('-','/',$reg[4])));
		$test2=explode('/',trim(str_replace('-','/',$reg[5])));
		$test3=explode('/',trim(str_replace('-','/',$reg[6])));
			
		if($tipo_carga==0) {
			if(count($test)!=3 OR count($test2)!=3) { continue; }
		} else {
			if(count($test3)!=3) { continue; }
		}

		$rut=(trim($reg[1]));
			
		$pat=trim(preg_replace('/\s+/', ' ',$reg[8]));
		$nombre=pg_escape_string(trim($reg[3]));
		$finicio=tfecha($reg[4]);
		$flimite=tfecha($reg[5]);
		$garantia=trim(preg_replace('/\s+/', ' ',$reg[9]));
        $rama=trim(preg_replace('/\s+/', ' ', $reg[10]));
        
        $clase=trim(preg_replace('/\s+/', ' ', $reg[11]));
        $cual=trim(preg_replace('/\s+/', ' ', $reg[12]));
        
		$fevento=trim(str_replace('-','/',$reg[6]));
		
		if($fevento=='')
			$fevento='null';
		else {
			if(!checkDateFormat($fevento)) {
				$log.="<tr><td style='text-align:right;'>".($i+1)."</td><td>Fecha de evento no es v&aacute;lida.</td></tr>";
				log_error($i, 'FECHA DE EVENTO NO ES VALIDA');
				$error++;
				continue;				
			}
			$fevento="'$fevento'";
		}
			
		$observaciones=trim($reg[13]);
		
		if($tipo_carga==0 OR $tipo_carga==2) {
		
			$ptrad=cargar_registros_obj("
				SELECT * FROM patologias_sigges_traductor 
				WHERE   pst_patologia_interna ilike '".pg_escape_string($pat)."' AND
						(pst_garantia ilike '".pg_escape_string($garantia)."' OR pst_garantia_interna ilike '".pg_escape_string($garantia)."') AND
						pst_rama_interna ilike '".pg_escape_string($rama)."'
			");
			
			if(!$ptrad) {
				$log.="<tr><td style='text-align:right;'>".($i+1)."</td><td>Patolog&iacute;a, garant&iacute;a y/o rama especificada no es v&aacute;lida.</td></tr>";
				log_error($i, 'NO EXISTE X PROBLEMA/X GARANTIA');
				$error++;
				continue;
			}
		
		}

		if($clase=='') continue;

        $condicion=cargar_registro("
            SELECT * FROM lista_dinamica_condiciones where nombre_condicion ilike '".pg_escape_string($clase)."'
        ");
        
        if(!$condicion) {
			$log.="<tr><td style='text-align:right;'>".($i+1)."</td><td>Condici&oacute;n [".htmlentities($clase)."] no es v&aacute;lida.</td></tr>";
			log_error($i, 'NO EXISTE CONDICION');
			$error++;
			continue;
		}
		
		$id_condicion=$condicion['id_condicion'];
		$id_bandeja='';

		if($tipo_carga==0 OR $tipo_carga==2) {

			$chk=cargar_registro("SELECT * FROM monitoreo_ges 
					WHERE mon_rut='$rut' AND 
					mon_fecha_inicio='$finicio'::date AND
					mon_fecha_limite='$flimite'::date AND
					mon_pst_id IN (
					
						SELECT pst_id FROM patologias_sigges_traductor 
						WHERE   pst_patologia_interna ilike '".pg_escape_string($pat)."' AND
								(pst_garantia ilike '".pg_escape_string($garantia)."' OR pst_garantia_interna ilike '".pg_escape_string($garantia)."') AND
								pst_rama_interna ilike '".pg_escape_string($rama)."'
					
					) 
					");
					
		} else {

			$chk=cargar_registro("SELECT * FROM monitoreo_ges 
					WHERE mon_rut='$rut' AND NOT mon_estado AND 
					mon_pst_id IN (SELECT pst_id FROM patologias_sigges_traductor WHERE (pst_patologia_interna ILIKE 'vicios%' OR pst_patologia_interna ILIKE 'estrabismo%') AND pst_garantia_interna ILIKE 'tratamiento%')
					");
			
		}
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
					
		if($chk) {		
		
			$mon_id=$chk['mon_id']*1;
			
			$chk2=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE mon_id=$mon_id AND monr_estado=0 ORDER BY monr_fecha DESC LIMIT 1;");
			
			
			if($tipo_carga!=3) {
				
					// No existe clasificacion o es distinta a la actual...
					if(!$chk2 OR ($chk2 AND ($id_condicion!=$chk2['monr_clase'] OR $func_id!=$chk2['monr_func_id']))) {
					
					$tmp=cargar_registro("SELECT * FROM monitoreo_ges_registro  WHERE mon_id=$mon_id AND monr_estado=0;");
			
					pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0;");
			
					pg_query("INSERT INTO monitoreo_ges_registro VALUES (
						DEFAULT, $mon_id, $func_id, now(), '$id_condicion', '$id_bandeja', '$observaciones', null, 'CARGA MASIVA', '$cual', $fevento
					);");
					
					$monr_id="CURRVAL('monitoreo_ges_registro_monr_id_seq')";
			
					// Si se mantiene la misma clasificación se mantiene el workflow del caso anterior...
					if($id_condicion==$tmp['monr_clase']) {
						pg_query("UPDATE lista_dinamica_caso SET monr_id=$monr_id WHERE monr_id=".$tmp['monr_id']);
						pg_query("UPDATE monitoreo_ges_registro SET monr_subclase='".$tmp['monr_subclase']."' WHERE monr_id=$monr_id;");
					}
							
					} else {
						
						$monr_id=$chk2['monr_id']*1;
					
						pg_query("UPDATE monitoreo_ges_registro SET
						monr_clase='$id_condicion',
						monr_fecha_evento=$fevento,
						monr_observaciones='$observaciones',
						monr_subcondicion='$cual',
						monr_fecha=now(),
						monr_func_id=$func_id,
						monr_descripcion='CARGA MASIVA'
						WHERE monr_id=$monr_id;");
						
					}
					
			} else if($tipo_carga==3) {

				if($fevento=='null') {
					$log.="<tr><td style='text-align:right;'>".($i+1)."</td><td>Fecha de Evento/Registro es obligatoria.</td></tr>";
					log_error($i, 'FECHA DE EVENTO ES OBLIGATORIA');
					$error++;
					continue;
				}
				
				pg_query("INSERT INTO monitoreo_ges_registro VALUES (
					DEFAULT, $mon_id, $func_id, $fevento, '$id_condicion', '$id_bandeja', '$observaciones', null, 'CARGA MASIVA HISTORICOS', '$cual', $fevento, 1
				);");

			}
			
			if($tipo_carga==2) {
				
				$chk2=cargar_registro("
				SELECT *, lc.caso_id AS real_caso_id FROM lista_dinamica_caso AS lc
				LEFT JOIN lista_dinamica_instancia AS li ON li.caso_id=lc.caso_id AND li.in_estado=0
				WHERE monr_id=$monr_id LIMIT 1;
				");
				
				$id_bandeja_sda='E';
				$id_bandeja_abast='N';
				$id_pendiente_oc='20';
				$id_pendiente_prestador='21';
					
				$valores_sda=pg_escape_string($reg[15]).'|'.pg_escape_string($reg[16]);
				$valores_abast=pg_escape_string($reg[17]).'|'.pg_escape_string($reg[18]);

				
				if(!$chk2) {
					
					// SIN CASO CREADO...
					
					$pacrut=pg_escape_string(trim($m['mon_rut']));
			
					$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pacrut' LIMIT 1;");
					
					$pac_id=$pac['pac_id']*1;
					
					pg_query("INSERT INTO lista_dinamica_caso VALUES (DEFAULT, 0, $pac_id, 'ORIGINADO DESDE MONITOREO GES', $mon_id, $monr_id);");
					
					if($valores_sda!='|')
						pg_query("INSERT INTO lista_dinamica_instancia VALUES (DEFAULT, CURRVAL('lista_dinamica_caso_caso_id_seq'), 0, current_timestamp, ".$_SESSION['sgh_usuario_id'].", 1, '$valores_sda', 'CARGA MASIVA COMPRAS', '', '$id_bandeja_sda', ".$id_pendiente_oc.");");
					if($valores_abast!='|')
						pg_query("INSERT INTO lista_dinamica_instancia VALUES (DEFAULT, CURRVAL('lista_dinamica_caso_caso_id_seq'), 0, current_timestamp, ".$_SESSION['sgh_usuario_id'].", 1, '$valores_abast', 'CARGA MASIVA COMPRAS', '', '$id_bandeja_abast', ".$id_pendiente_oc.");");

				} else {
					
					$caso_id=$chk2['real_caso_id']*1;
					$in_id=$chk2['in_id']*1;
					
					if($valores_sda!='|')
						pg_query("INSERT INTO lista_dinamica_instancia VALUES (DEFAULT, $caso_id, 0, current_timestamp, ".$_SESSION['sgh_usuario_id'].", 1, '$valores_sda', 'CARGA MASIVA COMPRAS', '', '$id_bandeja_sda', ".$id_pendiente_oc.");");
					if($valores_abast!='|')
						pg_query("INSERT INTO lista_dinamica_instancia VALUES (DEFAULT, $caso_id, 0, current_timestamp, ".$_SESSION['sgh_usuario_id'].", 1, '$valores_abast', 'CARGA MASIVA COMPRAS', '', '$id_bandeja_abast', ".$id_pendiente_oc.");");
					
				}

				
			}


			$editadas++;
			
		} else {


			$log.="<tr><td style='text-align:right;'>".($i+1)."</td><td>Paciente no encontrado en monitoreo.</td></tr>";
			log_error($i, 'PACIENTE/GARANTIA NO ENCONTRADO');
			$error++;		
		
		}
	 	
	 }

  $log.='</table>';
  
  
  $lfname='dump_'.date('Ymdhis');
  file_put_contents('tmp_logs/'.$lfname.'.csv', $csv_errores);
      
?>


<html>
<title>Carga Masiva de Monitoreo GES</title>

<?php cabecera_popup('../..'); ?>

<script>

var puntero=0; var total=<?php echo $total; ?>

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/cog.png'> Carga Masiva de Monitoreo GES</b>
</div>

<div class='sub-content'>
<img src='../../iconos/cog_go.png'> Resultado</b>
</div>

<div class='sub-content2' id='resultado' style='font-size:16px;'>
<?php print("<br/>EDITADOS: $editadas<br/>ERRORES: $error<br/><br/>"); 
if($error>0) { print($log); ?>
<br />
<br />
<a href='tmp_logs/<?php echo $lfname; ?>.csv'>
<div style='border:1px solid black;background-color:#cccccc;padding:3px;margin:3px;'>
<img src='../../iconos/application_put.png' style='width:24px;height:24px;' /><br />Descargar Log de Errores...
</div>
</a>
<?php } ?>
</div>


</body>
</html>
