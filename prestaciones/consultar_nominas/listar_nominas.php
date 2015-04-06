<?php
	ini_set("memory_limit","800M");
    require_once('../../conectar_db.php');
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    $esp_id=$_POST['esp_id']*1;
    $doc_id=$_POST['doc_id']*1;
    $orden=$_POST['orden']*1;
    //--------------------------------------------------------------------------
    if($esp_id!=-1)
    {
        $w_esp='nom_esp_id='.$esp_id;
    }
    else
    {
        $w_esp='true';
    }
    //--------------------------------------------------------------------------
    if($doc_id!=-1)
    {
        $w_doc='nom_doc_id='.$doc_id;
    }
    else
    {
        $w_doc='true';
    }
    //--------------------------------------------------------------------------
    if($_POST['xls']*1==1)
    {
        header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_nominas.xls\";");
    }
    if($orden==0)
    {
        $orden='nom_folio';
    }
    elseif($orden==1)
    {
        $orden='nom_fecha, nom_folio';
    }
    else
    {
        $orden='esp_desc, nom_folio';
    }
    
    $consulta="
    SELECT nomina.*, 
    nom_fecha::date,
    date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
    nomd_tipo, nomd_sficha, nomd_destino, nomd_motivo, nomd_auge, 
    nomd_diag_cod, nomd_extra,
    sex_id, esp_desc,
    pac_rut, pac_appat, pac_apmat, pac_nombres,
    doc_rut, doc_paterno, doc_materno, doc_nombres,nomd_diag,prev_desc,nom_motivo,nomd_codigo_no_atiende,noat_desc,nomd_origen,
    susp_desc,
    COALESCE(nom_estado,0)as estado_nomina,pac_id
    FROM nomina
    LEFT JOIN especialidades ON nom_esp_id=esp_id
    LEFT JOIN doctores ON nom_doc_id=doc_id
    LEFT JOIN nomina_detalle USING (nom_id)
    LEFT JOIN pacientes USING (pac_id)
    LEFT JOIN prevision USING (prev_id)
    LEFT JOIN nomina_codigo_no_atiende on noat_id=nomd_codigo_no_atiende
    LEFT JOIN nomina_codigo_suspende on susp_id=nomd_codigo_cancela
    WHERE 
    nom_fecha::date>='$fecha1' AND 
    nom_fecha::date<='$fecha2' AND
    $w_esp AND $w_doc
    ORDER BY $orden
    ";
    
    //print($consulta);
    
    $l=cargar_registros_obj($consulta,true);
    
    $num_ausente=0;
    $num_presente=0;
    $num_nuevo=0;
    $num_control=0;	
    $num_rep=0;
    $num_extra=0;
    $num_masc=0;
    $num_feme=0;
    $num_agendados=0;
    $num_atendidos=0;
    
    $num_altas=0;
    $num_altas2=0;
    
    $num_no_atendidos=0;
    $num_suspendidos=0;
    $num_bloqueadas=0;
    $num_bloqueadas_sinpac=0;
    $num_cupos_libres=0;
    $num_cambia_profesional=0;
    $num_sindefinir=0;
    $geta=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
    $getn=array('<=4','5-9','10-14','15-19','20-24','25-29','30-34','35-39','40-44','45-49','50-54','55-59','60-64','65-69','70-74','75-79','> 80');
    
    
    if($l){
        for($i=0;$i<count($l);$i++)
        {
            $r=$l[$i];
            $val=$r['nomd_codigo_no_atiende'];
            if($val=='1') {
                if($r['nomd_diag_cod']!="T" and $r['nomd_diag_cod']!="" and $r['nomd_diag_cod']!="X") {
                    $num_ausente++;
                }
            } else {
                if($r['nomd_diag_cod']!="T" and $r['nomd_diag_cod']!="N" and $r['nomd_diag_cod']!="" and $r['nomd_diag_cod']!="X") {
                    $num_presente++;
                }
            }

            if($r['nomd_diag_cod']=="" and ($r['pac_id']*1)!=0 and $r['estado_nomina']!="-1"){
                $num_agendados++;
            }else if($r['nomd_diag_cod']=="OK"){
                $num_atendidos++;
            }else if($r['nomd_diag_cod']=="ALTA"){
                $num_altas2++;
            }else if($r['nomd_diag_cod']=="N"){
                $num_no_atendidos++;
            }else if($r['nomd_diag_cod']=="T"){
                $num_suspendidos++;
            }else if($r['nomd_diag_cod']=="X" and ($r['pac_id']*1)!=0){
                $num_bloqueadas++;
            }else if($r['nomd_diag_cod']=="X" and ($r['pac_id']*1)==0){
                $num_bloqueadas_sinpac++;
            }else if($r['nomd_diag_cod']=="" and ($r['pac_id']*1)==0){
                $num_cupos_libres++;
            }else {
                $num_sindefinir++;
            }

            if($r['estado_nomina']=="-1" and ($r['pac_id']*1)!=0){
                $num_cambia_profesional++;
            }

            if($r['nomd_diag_cod']!="T" and $r['nomd_diag_cod']!="N" and $r['nomd_diag_cod']!="" and $r['nomd_diag_cod']!="X")
            {
                    //$val=$r['nomd_tipo'];
                    //if($val=='N')
                    $val=$r['nom_motivo'];
                    $palabra = 'Nueva';
                    $encontrada = strrpos($val, $palabra);
                    if($encontrada)
                        $num_nuevo++;
                    else
                    {
                        $palabra = 'Repetida';
                        $encontrada = strrpos($val, $palabra);
                        if($encontrada)
                            $num_rep++;
                        else
                            $num_control++;
                    }
                    $val=$r['nomd_extra'];
                    if($val=='S')
                        $num_extra++;

                    $val=$r['edad']*1;
                    if($val<=4) { $geta[0]++; }
                    if($val>=5 AND $val<=9) { $geta[1]++; }
                    if($val>=10 AND $val<=14) { $geta[2]++; }
                    if($val>=15 AND $val<=19) { $geta[3]++; }
                    if($val>=20 AND $val<=24) { $geta[4]++; }
                    if($val>=25 AND $val<=29) { $geta[5]++; }
                    if($val>=30 AND $val<=34) { $geta[6]++; }
                    if($val>=35 AND $val<=39) { $geta[7]++; }
                    if($val>=40 AND $val<=44) { $geta[8]++; }
                    if($val>=45 AND $val<=49) { $geta[9]++; }
                    if($val>=50 AND $val<=54) { $geta[10]++; }
                    if($val>=55 AND $val<=59) { $geta[11]++; }
                    if($val>=60 AND $val<=64) { $geta[12]++; }
                    if($val>=65 AND $val<=69) { $geta[13]++; }
                    if($val>=70 AND $val<=74) { $geta[14]++; }
                    if($val>=75 AND $val<=79) { $geta[15]++; }
                    if($val>=80) { $geta[16]++; }

                    $val=$r['sex_id']*1;
                    if($val==0)
                        $num_masc++;
                    else
                        $num_feme++;

                    //$val=$r['nomd_destino']*1;
                    if($r['nomd_diag_cod']=="ALTA")
                        $num_altas++;
            }
        }
    }
    
    if(sizeof($l)>0)
    {
        $factor=100/sizeof($l);
        if($num_presente>0)
            $factor2=100/$num_presente;
        else
            $factor2=0;
    }
    else
    {
        $factor=0;
		$factor2=0;
    }
    
    $html='<table style="width:100%;">';
        $html.='<tr>';
            $html.='<td>';
                $html.='<table style="width:100%;">';
                    $html.='<tr class="tabla_header">';
                        $html.='<td colspan=3>Indicadores de Estado de N&oacute;minas</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Agendados Sin Atender:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_agendados.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Atendidos:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_atendidos.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Alta Especialidad:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_altas2.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">No Atendidos:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_no_atendidos.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Suspendidos:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_suspendidos.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Bloqueados:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_bloqueadas.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Cupos libres Bloqueados:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_bloqueadas_sinpac.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Cupos libres:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_cupos_libres.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Asigandos a otro Profesional:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_cambia_profesional.'</td>';
                        $html.='<td style="text-align:center;">&nbsp;</td>';
                    $html.='</tr>';
                    
                    $html.='<tr class="tabla_header">';
                        $html.='<td colspan=3>Indicadores de las N&oacute;minas</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;width:40%;">Asisten:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;width:20%;">'.$num_presente.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_presente*$factor,2,',','.').'%</td>';
                    $html.='</tr>';	
                    $html.='<tr class="tabla_fila2">';
                        $html.='<td style="text-align:right;">Ausentes:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_ausente.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_ausente*$factor,2,',','.').'%</td>';
                    $html.='</tr>';	
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Pac. Nuevos:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_nuevo.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_nuevo*$factor2,2,',','.').'%</td>';
                    $html.='</tr>';
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Pac. Repetidos:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_rep.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_rep*$factor2,2,',','.').'%</td>';
                    $html.='</tr>';
                    
                    $html.='<tr class="tabla_fila2">';
                        $html.='<td style="text-align:right;">Pac. Control:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_control.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_control*$factor2,2,',','.').'%</td>';
                    $html.='</tr>';	
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Cant. Extras:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_extra.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_extra*$factor,2,',','.').'%</td>';
                    $html.='</tr>';	
                    $html.='<tr class="tabla_fila2">';
                        $html.='<td style="text-align:right;">Masc./Fem.:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_masc.'/'.$num_feme.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_masc*$factor2,0,',','.').'%/'.number_format($num_feme*$factor2,0,',','.').'%</td>';
                    $html.='</tr>';	
                    $html.='<tr class="tabla_fila">';
                        $html.='<td style="text-align:right;">Altas:</td>';
                        $html.='<td style="font-weight:bold;text-align:center;">'.$num_altas.'</td>';
                        $html.='<td style="text-align:center;">'.number_format($num_altas*$factor,2,',','.').'%</td>';
                    $html.='</tr>';
                $html.='</table>';
            $html.='</td>';
            $html.='<td>';
                $html.='<table style="width:100%;">';
                    $html.='<tr class="tabla_header">';
                        $html.='<td colspan=3>Grupos Et&aacute;reos</td>';
                    $html.='</tr>';
                    for($j=0;$j<sizeof($getn);$j++)
                    {
                        $clase=($j%2==0)?'tabla_fila':'tabla_fila2';		
			$html.='<tr class="'.$clase.'">';
                            $html.='<td style="text-align:right;width:40%;">'.$getn[$j].':</td>';
                            $html.='<td style="font-weight:bold;text-align:center;width:20%;">'.$geta[$j].'</td>';
                            $html.='<td style="text-align:center;">'.number_format($geta[$j]*$factor2,2,',','.').'%</td>';
                        $html.='</tr>';
                    }
                $html.='</table>';
            $html.='</td>';
        $html.='</tr>';
    $html.='</table>';
    echo $html;
?>
<table style='width:100%;font-size:8px;'>
    <tr class='tabla_header'>
        <td>#</td>
        <td>Fecha</td>
        <td>Hora</td>
        <td>Nro. Folio</td>
        <td>Especialidad</td>
        <!--<td>RUT M&eacute;dico</td>-->
        <td>Nombre M&eacute;dico</td>
        <td>RUT Paciente</td>
        <td>Paterno</td>
        <td>Materno</td>
        <td>Nombre</td>
        <td>Sexo</td>
        <td>Edad</td>
        <td>Previsi&oacute;n</td>
        <!--<td>Tipo</td>-->
        <td>Extra</td>
        <!--<td>S/Ficha</td>-->
        <td>CIE10</td>
        <td>DESC CIE10</td>
        <td>Diag. Personal</td>
        <td>Estado</td>
        <td>Tipo Atenci&oacute;n</td>
        <td>Pertinente Prot</td>
        <td>Pertinente Tiempo</td>
        <td>Procedencia</td>
        <!--<td>Destino</td>-->
        <td>AUGE</td>
        <td>Tipo de Contrato</td>
    </tr>
    <?php 
    $consulta="
    SELECT nomina.*, 
    nom_fecha::date,
    date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
    nomd_tipo, nomd_sficha, nomd_destino, nomd_motivo, nomd_auge, 
    nomd_diag_cod, nomd_extra,
    sex_id, esp_desc,
    pac_rut, pac_appat, pac_apmat, pac_nombres,
    doc_rut, doc_paterno, doc_materno, doc_nombres,nomd_diag,prev_desc,nom_motivo,nomd_codigo_no_atiende,noat_desc,nomd_origen,
    susp_desc,COALESCE(nom_estado,0)as estado_nomina,pac_id,nomd_hora,nom_tipo_contrato
    FROM nomina
    LEFT JOIN especialidades ON nom_esp_id=esp_id
    LEFT JOIN doctores ON nom_doc_id=doc_id
    LEFT JOIN nomina_detalle USING (nom_id)
    LEFT JOIN pacientes USING (pac_id)
    LEFT JOIN prevision USING (prev_id)
    LEFT JOIN nomina_codigo_no_atiende on noat_id=nomd_codigo_no_atiende
    LEFT JOIN nomina_codigo_suspende on susp_id=nomd_codigo_cancela
    WHERE 
    nomina_detalle.pac_id>0 AND
    nom_fecha::date>='$fecha1' AND 
    nom_fecha::date<='$fecha2' AND
    $w_esp AND $w_doc
    ORDER BY $orden
    ";
    $l=cargar_registros_obj($consulta, true);
    if($l)
    {
        for($i=0;$i<sizeof($l);$i++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            
            if($l[$i]['sex_id']==0)
                $sexo='M';
            elseif($l[$i]['sex_id']==1)
                $sexo='F';
            else
                $sexo='I';
            
            print("<tr class='$clase'>");
                print("<td align='center'>".($i+1)."</td>");
                print("<td align='center'>".$l[$i]['nom_fecha']."</td>");
                print("<td align='center'>".$l[$i]['nomd_hora']."</td>");
                print("<td align='center'><b>".$l[$i]['nom_folio']."</b></td>");
                print("<td align='center'>".utf8_decode($l[$i]['esp_desc'])."</td>");
                //print("<td align='right'>".$l[$i]['doc_rut']."</td>");
                print("<td>".htmlentities((strtoupper($l[$i]['doc_nombres'].' '.$l[$i]['doc_paterno'].' '.$l[$i]['doc_materno'])))."</td>");
                print("<td align='right'>".$l[$i]['pac_rut']."</td>");
                print("<td align='left'>".$l[$i]['pac_appat']."</td>");
                print("<td align='left'>".$l[$i]['pac_apmat']."</td>");
                print("<td align='left'>".$l[$i]['pac_nombres']."</td>");
                print("<td align='center'>".$sexo."</td>");
                print("<td align='center'>".$l[$i]['edad']."</td>");
                print("<td align='left'>".$l[$i]['prev_desc']."</td>");
                //print("<td align='center'>".$l[$i]['nomd_tipo']."</td>");
                print("<td align='center'>".$l[$i]['nomd_extra']."</td>");
                //print("<td align='center'>".$l[$i]['nomd_sficha']."</td>");
                if($l[$i]['nomd_diag_cod']=="N")
                {
                    print("<td align='center'>".$l[$i]['noat_desc']."</td>");
                    print("<td align='center'>&nbsp;</td>");
                    print("<td align='center'>".$l[$i]['nomd_diag']."</td>");
                    if($r['estado_nomina']!="-1"){
                        print("<td align='center'>NO SE PRESENTA</td>");
                    } else {
                        print("<td align='center'>SE ASIGNA A OTRO PROFESIONAL</td>");
                    }
                    
                }
                else
                {
                    if(strstr($l[$i]['nomd_diag'],'|'))
                    {
                        $cie10=explode("|",$l[$i]['nomd_diag']);
                        $reg_cie=cargar_registro("SELECT * FROM diagnosticos WHERE upper(diag_cod)=upper('".$cie10[0]."')",true);
                        if($reg_cie)
                        {
                            if(strtoupper($reg_cie['diag_desc'])!=strtoupper($cie10[1]))
                            {
                                print("<td align='center'><b>".$cie10[0]."</b></td>");
                                print("<td align='center'><b>".$reg_cie['diag_desc']."</b></td>");
                                print("<td align='center'><b>".$cie10[1]."</b></td>");
                            }
                            else
                            {
                                print("<td align='center'><b>".$cie10[0]."</b></td>");
                                print("<td align='center'><b>".$reg_cie['diag_desc']."</b></td>");
                                print("<td align='center'>&nbsp;</td>");
                            }
                        }
                        else
                        {
                            print("<td align='center'><b>".$cie10[0]."</b></td>");
                            print("<td align='center'><b>".$cie10[1]."</b></td>");
                            print("<td align='center'>&nbsp;</td>");
                            
                        }
                    }
                    else
                    {
                        if($l[$i]['nomd_diag_cod']=="T"){
                            print("<td align='center'>".$l[$i]['susp_desc']."</td>");
                            print("<td align='center'>&nbsp;</td>");
                            print("<td align='center'>".$l[$i]['nomd_diag']."</td>");
                        }
                        else {
                            print("<td align='center'>".$l[$i]['nomd_diag']."</td>");
                            print("<td align='center'>".$l[$i]['nomd_diag']."</td>");
                            print("<td align='center'>&nbsp;</td>");
                        }
                    }
                    if($r['estado_nomina']=="-1" and $l[$i]['nomd_diag_cod']!="T"){
                        print("<td align='center'>SE ASIGNA A OTRO PROFESIONAL</td>");
                    } else {
                        if($l[$i]['nomd_diag_cod']=="" or $l[$i]['nomd_diag_cod']==null)
                            print("<td align='center'>AGENDADO</td>");
                        else if($l[$i]['nomd_diag_cod']=="T")
                            print("<td align='center'>SUSPENDIDO</td>");
                        else if($l[$i]['nomd_diag_cod']=="OK")
                            print("<td align='center'>ATENDIDO</td>");
                        else if($l[$i]['nomd_diag_cod']=="X")
                            print("<td align='center'>BLOQUEADO</td>");
                        else
                            print("<td align='center'>".$l[$i]['nomd_diag_cod']."</td>");
                    }
                }
                
                print("<td align='center'>".$l[$i]['nom_motivo']."</td>");
                print("<td align='center'>".$l[$i]['nomd_motivo'][0]."</td>");
                print("<td align='center'>".$l[$i]['nomd_motivo'][1]."</td>");
                switch ($l[$i]['nomd_origen'])
                {
                    case "":
                        $origen="Sin Informar";
                        break;
                    
                    case "A":
                        $origen="APS";
                        break;
                    
                    case "U":
                        $origen="Urgencia";
                        break;
                    
                    case "C":
                        $origen="CAE";
                        break;
                    
                }
                print("<td align='center'>".$origen."</td>");
                //print("<td align='center'>".$l[$i]['nomd_destino']."</td>");
                print("<td align='center'>".$l[$i]['nomd_auge']."</td>");
                print("<td align='center'>".$l[$i]['nom_tipo_contrato']."</td>");
            print("</tr>");
            flush();			
        }
    }
?>
</table>
