<?php
    require_once('../../conectar_db.php');
    $tipo=$_POST['tipo']*1;
    $busca=pg_escape_string(utf8_decode($_POST['busca']));
    $ficha=false;
    if(isset($_POST['ficha']))
        $ficha=true;
    
    if($tipo==0)
    {
        $busca=str_replace('.','',trim($busca));
	$pac_w="pac_rut='$busca'";	
    }
    elseif($tipo==1)
    {
        //$busca*=1;
        $busca=trim($busca);
	$pac_w="pac_ficha='$busca'";
    }
    elseif($tipo==2)
    {
        $busca=trim($busca);
	$pac_w="nom_folio='$busca'";
    }
    elseif($tipo==3)
    {
        $busca=trim($busca);
	$pac_w="pac_pasaporte='$busca'";
    }
    elseif($tipo==4)
    {
        $busca=trim($busca);
	$pac_w="pac_id='$busca'";
    }
    $pac = cargar_registro("SELECT * FROM pacientes LEFT JOIN comunas USING (ciud_id) LEFT JOIN prevision USING (prev_id) WHERE $pac_w ", true);
    if(!$pac)
    {
        exit("<script>
        alert('Paciente no encontrado.');
        </script>");	
    }
    if(strlen($pac['prev_desc'])==1)
    {
        $pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
    }
    $consulta="
    SELECT 
    nom_fecha::date, nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
    COALESCE(diag_desc, cancela_desc) AS diag_desc, nomd_diag_cod,
    esp_desc,nomd_id,nomd_diag,nomd_inter_id,nom_motivo,nomd_extra,COALESCE(nom_estado,0) as nom_estado
    FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN pacientes USING (pac_id)
    LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
    LEFT JOIN doctores ON nom_doc_id=doc_id
    LEFT JOIN especialidades ON nom_esp_id=esp_id 
    LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
    WHERE $pac_w  
    ORDER BY nomina.nom_fecha DESC, nomd_hora
    ";
    
    //print($consulta);
    $lista = cargar_registros_obj($consulta);
    if($lista AND $tipo==2)
    {
        $nom_id=$lista[0]['nom_id']*1;
    	$n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
        LEFT JOIN doctores ON nom_doc_id=doc_id
        LEFT JOIN especialidades ON nom_esp_id=esp_id
        WHERE nom_id=$nom_id", true);

	print("
		<div class='sub-content'>
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:30%;'>Nro. N&oacute;mina:</td><td style='font-weight:bold;'>".$n['nom_folio']."</td></tr>		
		<tr><td style='text-align:right;'>Fecha:</td><td>".$n['nom_fecha']."</td></tr>		
		<tr><td style='text-align:right;'>Especialidad:</td><td>".$n['esp_desc']."</td></tr>		
		<tr><td style='text-align:right;'>Profesional:</td><td>".$n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']."</td></tr>		
		</table>	
		</div>
	");
  	
  }
?>
<input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
<?php if($tipo!=2) { ?>
    <table style='width:100%;font-size:18px;'>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>RUT:</td>
            <td class='tabla_fila' style='font-size:16px;'><?php echo $pac['pac_rut']; ?></td>
            <td style='text-align:right;' class='tabla_fila2'>Ficha:</td>
            <td class='tabla_fila' style='font-size:16px;'><?php echo $pac['pac_ficha']; ?></td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Nombre:</td>
            <td class='tabla_fila' colspan=3 style='font-size:18px;'>
                <?php echo trim($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']); ?>
            </td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Tel&eacute;fono Fijo:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['pac_fono']); ?></td>
            <td style='text-align:right;' class='tabla_fila2'>Tel&eacute;fono Celular:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['pac_celular']); ?></td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Direcci&oacute;n:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['pac_direccion']); ?></td>
            <td style='text-align:right;' class='tabla_fila2'>Ciudad:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['ciud_desc']); ?></td>
        </tr>
        <tr>
            <td style='text-align:right;' class='tabla_fila2'>Previsi&oacute;n:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['prev_desc']); ?></td>
            <td style='text-align:right;' class='tabla_fila2'>Fecha Nacimiento:</td>
            <td class='tabla_fila' style='font-size:18px;'><?php echo trim($pac['pac_fc_nac']); ?></td>
        </tr>
    </table>
<?php } ?>
<table style='width:100%;font-size:11px;' class='lista_small celdas'>
    <tr class='tabla_header'>
        <td>Fecha/Hora</td>
        <td>Especialidad</td>
        <?php if($tipo!=2) { ?>
            <td>RUT</td>
            <td>Profesional</td>
        <?php } else { ?>
            <td>Nro. Ficha</td>
            <td>RUT Paciente</td>
            <td>Nombre Paciente</td>
        <?php } ?>
        <td>Tipo</td>
        <td>Tipo Consulta</td>
        <td>Extra</td>
        <td>Estado citaci&oacute;n</td>
        <td style='width:150px;'>Diagn&oacute;stico</td>
        <td>S/Ficha</td>
        <td>CIE10</td>
        <td>Motivo</td>
        <td>Destino</td>
        <td>AUGE</td>
        <td>&nbsp;</td>
    </tr>
    <?php
  if($lista)
    for($i=0;$i<count($lista);$i++) {
        ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
        print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
            print("<td><center>".$lista[$i]['nom_fecha']."<br />".substr($lista[$i]['nomd_hora'],0,5)."</center></td>");
            print("<td><center>".htmlentities($lista[$i]['esp_desc'])."</center></td>");
            if($tipo!=2){
                if($lista[$i]['doc_rut']!=""){
                    print("<td style='text-align:right;font-weight:bold;'>".formato_rut($lista[$i]['doc_rut'])."</td>");
                } else {
                    print("<td><center>&nbsp;</center></td>");
                }
    		print("<td>".htmlentities(strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']))."</td>");
            } else {
	 	print("<td style='text-align:right;font-weight:bold;'>".($lista[$i]['pac_ficha'])."</td>");
    		print("<td style='text-align:right;font-weight:bold;'>".formato_rut($lista[$i]['pac_rut'])."</td>");
    		print("<td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>");        
            }
            print("<td><center>".$lista[$i]['nomd_tipo']."</center></td>");
            print("<td><center>".$lista[$i]['nom_motivo']."</center></td>");
            
            if($lista[$i]['nomd_extra']!="")
                print("<td><center>".$lista[$i]['nomd_extra']."</center></td>");
            else
                print("<td><center>N</center></td>");
            if($lista[$i]['nom_estado']!="-1"){
                if($lista[$i]['nomd_diag_cod']=='X') {
                    print("<td><center>Atenci&oacute;n Bloqueada</center></td>");
                }else if($lista[$i]['nomd_diag_cod']=='T') {
                    print("<td><center>Suspendida</center></td>");
                }else if($lista[$i]['nomd_diag_cod']=='OK') {
                    print("<td><center>Atendido</center></td>");
                }else if($lista[$i]['nomd_diag_cod']=='ALTA') {
                    print("<td><center>Alta especialidad</center></td>");
                }else if($lista[$i]['nomd_diag_cod']=='N') {
                    print("<td><center>No Atendido</center></td>");
                }else {
                    print("<td><center>Agendado</center></td>");
                }
            }else {
                print("<td style='background-color:#ffff80' ><center>ATENCI&Oacute;N SE ASIGNA A OTRO PROFESIONAL</center></td>");
            }
            
            if($lista[$i]['nomd_diag_cod']=='X') {
                $color='#FF0000;';
            } else {
                $color='';
            }
            if($lista[$i]['diag_desc']!="") {
                print("<td style='color:$color'><center>".htmlentities($lista[$i]['diag_desc'])."</center></td>");
            } else {
                if($lista[$i]['nomd_diag']!="") {
                    $diagnostico=explode("|",$lista[$i]['nomd_diag']);
                    if(count($diagnostico)>0)
                        print("<td style='color:$color'><center>".htmlentities($diagnostico[1])."</center></td>");
                    else
                        print("<td style='color:$color'><center>".htmlentities($lista[$i]['nomd_diag'])."</center></td>");
                } else
                    print("<td style='color:$color'><center>&nbsp;</center></td>");
            }
            print("<td><center>".$lista[$i]['nomd_sficha']."</center></td>");
            if($lista[$i]['nomd_diag']!="") {
                if(strstr($lista[$i]['nomd_diag'],'|')) {
                    $diagnosticos=explode("|",$lista[$i]['nomd_diag']);
                    if(strlen($diagnosticos[1])>30) {
                        $glosa_diag=substr($diagnosticos[1],0,30).'...';
                    } else {
                        $glosa_diag=$diagnosticos[1];
                    }
                    print("<td style='color:$color'><center>".$diagnosticos[0]."</center></td>");
                } else {
                    if($lista[$i]['nomd_diag_cod']!="OK" && $lista[$i]['nomd_diag_cod']!="ALTA" && $lista[$i]['nomd_diag_cod']!="N" && $lista[$i]['nomd_diag_cod']!="X" && $lista[$i]['nomd_diag_cod']!="T" && $lista[$i]['nomd_diag_cod']!="")  {
                        if(strlen($lista[$i]['nomd_diag'])>30) {
                            $glosa_diag=substr($lista[$i]['nomd_diag'],0,30).'...';
                        } else {
                            $glosa_diag=$lista[$i]['nomd_diag'];
                        }
                        print("<td style='color:$color'><center>".htmlentities($lista[$i]['nomd_diag_cod']).": </b>".htmlentities($glosa_diag)."</center></td>");
                    } else {
                        print("<td><center>&nbsp;</center></td>");
                    }
                } 
            }else {
                print("<td style='color:$color'><center>&nbsp;</center></td>");
            }
            //print("<td style='color:$color'><center>".$lista[$i]['nomd_diag_cod']."</center></td>");
            print("<td><center>".$lista[$i]['nomd_motivo']."</center></td>");
            print("<td><center>".$lista[$i]['nomd_destino']."</center></td>");    
            print("<td><center>".$lista[$i]['nomd_auge']."</center></td>");
            print("<td style='white-space:nowrap;'>");
                print("<center>");
                    if(!$ficha) {
                        print("<img src='iconos/layout.png'  style='cursor:pointer;' alt='Imprimir Hoja AT.' title='Imprimir Hoja AT.' onClick='imprimir_citacion2(".$lista[$i]['nomd_id'].");' />");
                        print("<img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Citaci&oacute;n' title='Imprimir Citaci&oacute;n' onClick='imprimir_citacion(".$lista[$i]['nomd_id'].");' />");
                        if(($lista[$i]['nomd_inter_id']*1)!=0) {
                            print("<img src='iconos/layout.png'  style='cursor:pointer;' alt='Ver Interconsulta' title='Ver Interconsulta' onClick='abrir_ficha(".$lista[$i]['nomd_inter_id'].");' />");
                            print("<img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Interconsulta' title='Imprimir Interconsulta' onClick='print_inter_ficha(".$lista[$i]['nomd_inter_id'].");' />");
                        }
                    } else {
                        print("&nbsp;");
                    }
                print("</center>");
            print("</td>");
        print("</tr>");
    }
?>
</table>