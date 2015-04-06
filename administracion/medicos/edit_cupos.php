<?php
    require_once('../../conectar_db.php');
    $motivo_original=pg_escape_string($_POST['motivo_original']);
    $desde_original=pg_escape_string($_POST['desde_original']);
    $hasta_original=pg_escape_string($_POST['hasta_original']);
    $cantn_original=($_POST['cantn_original']*1);
    $cantc_original=($_POST['cantc_original']*1);
    $ficha_original=pg_escape_string($_POST['ficha_original']);
    $adr_original=pg_escape_string($_POST['adr_original']);
    $fecha=pg_escape_string($_POST['fecha']);
    $cupo_id=($_POST['cupo_id']*1);
    $doc_id=($_POST['doc_id']*1);
    $esp_id=($_POST['esp_id']*1);
    $nom_id=($_POST['nom_id']*1);
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $tipo_atencion_edit=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo", true);
    $tipoatencionhtml_edit='';
    for($i=0;$i<count($tipo_atencion_edit);$i++)
    {
        $selected='';
        if($motivo_original==$tipo_atencion_edit[$i]['nom_motivo'])
        {
            $selected="SELECTED";
        }
        $tipoatencionhtml_edit.='<option value="'.$tipo_atencion_edit[$i]['nom_motivo'].'" '.$selected.'>'.$tipo_atencion_edit[$i]['nom_motivo'].'</option>';
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $horashtml_edit='';
    for($i=0;$i<24;$i++)
    {
        ($i<10) ? $h='0'.$i : $h=$i;
        if($h=='08')
            $sel='SELECTED';
        else
            $sel='';
        $horashtml_edit.='<option value="'.$h.':00" '.$sel.'>'.$h.':00</option>';
        $horashtml_edit.='<option value="'.$h.':05">'.$h.':05</option>';
        $horashtml_edit.='<option value="'.$h.':10">'.$h.':10</option>';
        $horashtml_edit.='<option value="'.$h.':15">'.$h.':15</option>';
        $horashtml_edit.='<option value="'.$h.':20">'.$h.':20</option>';
        $horashtml_edit.='<option value="'.$h.':25">'.$h.':25</option>';
        $horashtml_edit.='<option value="'.$h.':30">'.$h.':30</option>';
        $horashtml_edit.='<option value="'.$h.':35">'.$h.':35</option>';
        $horashtml_edit.='<option value="'.$h.':40">'.$h.':40</option>';
	$horashtml_edit.='<option value="'.$h.':45">'.$h.':45</option>';
        $horashtml_edit.='<option value="'.$h.':50">'.$h.':50</option>';
        $horashtml_edit.='<option value="'.$h.':55">'.$h.':55</option>';
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    $consulta="SELECT 
    pacientes.*, nomina_detalle.*, diag_desc, nom_motivo, esp_recurso,
    date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
    nom_esp_id, cancela_desc  
    FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    LEFT JOIN pacientes USING (pac_id)
    LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
    LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
    where nom_doc_id=$doc_id AND nom_esp_id=$esp_id AND nom_fecha='$fecha' AND nom_motivo='$motivo_original'
    AND nomd_hora between '$desde_original' AND '$hasta_original' AND nomina_detalle.nom_id=$nom_id
    AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
    ORDER BY nomd_hora,nomd_extra DESC,nomd_folio,pac_appat,pac_apmat,pac_nombres";
    $lista = cargar_registros_obj($consulta);
    if(!$lista)
    {
        $lista=false;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script>
    var asignados=0;
    var bloquear_modificacion="";
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    visualizar_nuevo=function(index)
    {
        var asignados=$j('#txt_asignados').val();
        if(index==1)
        {
            var cantn_edit=($('cantn_original').value*1);
            var cantc_edit=($('cantc_original').value*1);
            var h1=$('desde_original').value.split(":");
            var h2=$('hasta_original').value.split(":");
           
        }
        if(index==2)
        {
            var cantn_edit=($('cantn_edit').value*1);
            var cantc_edit=($('cantc_edit').value*1);
            var h1=$('desde_edit').value.split(":");
            var h2=$('hasta_edit').value.split(":");
        }
        
        if((asignados*1)==0)
        {
            var hh1=(h1[0]*60)+(h1[1]*1);
            var hh2=(h2[0]*60)+(h2[1]*1);
            var dif=(hh2-hh1)/(cantn_edit+cantc_edit);
            var html="";
            html+="<table style='width:100%;font-size:11px;' class='lista_small celdas' cellspacing=0>";
            html+="<tr class='tabla_header'>";
            html+="<td>#</td>";
            html+="<td>Hora</td>";
            html+="<td>RUT/Ficha</td>";
            html+="<td>Paciente</td>";
            html+="<td>Sobrecupo</td>";
            html+="<td>Estado</td>";
            for(var i=0;i<((cantn_edit*1)+(cantc_edit*1));i++)
            {
                _hora=hh1+(dif*i);
                hora=Math.floor(_hora/60);
                minutos=Math.floor(_hora%60);
                //minutos=minutos.toFixed();
                if(minutos<10)
                    minutos='0'+minutos;
                hr=hora+':'+minutos;
                if(i%2==0) color='#BBDDBB'; else color='#BBEEBB';
                var motivo='<?php echo $motivo_original;?>';
                var cestado='';
                if(motivo!='')
                    cestado='DISPONIBLE <span id="span_tipo_atencion" class="texto_tooltip" ondblclick=""><i>('+motivo+')<i></span>';
                else
                    cestado='DISPONIBLE';
                       

                
                html+="<tr style='height:30px;background-color:"+color+"' onMouseOver=this.style.background=\'#dddddd\' onMouseOut='this.style.background=\""+color+"\";' >";
                    html+="<td style='text-align:right;font-weight:bold;' class='tabla_header'>"+(i+1)+"</td>";
                    html+="<td style='text-align:center;font-weight:bold;'>"+hr+"</td>";
                    html+="<td style='text-align:center;font-weight:bold;' colspan='12'><i>CUPO "+cestado+"</i></td>";
                html+="</tr>";
            }
            html+="</table>";
            if(index==1)
            {
                $('list_agenda_original').innerHTML=html;
            }
            if(index==2)
            {
                $('list_agenda_remplazo').innerHTML=html;
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    calcular_total_cupos_edit=function(index)
    {
        if(index==1)
        {
            $('cantt_original').value=($('cantn_original').value*1)+($('cantc_original').value*1);
        }
        if(index==2)
        {
            $('cantt_edit').value=($('cantn_edit').value*1)+($('cantc_edit').value*1);
        }
        visualizar_nuevo(index);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    check_cupos_edit=function(index)
    {
        if(index==1)
        {
            if($('cantc_original').value=='')
            {
                $('cantc_original').value=0;
                calcular_total_cupos_edit(index);
            }
        }
        if(index==2)
        {
            if($('cantc_edit').value=='')
            {
                $('cantc_edit').value=0;
                calcular_total_cupos_edit(index);
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    cambiar_tipo_atencion=function(nomd_id,nomd_hora)
    {
      
        var fecha="<?php echo $fecha;?>";
        var titulo="Cambiar Tipo de Atenci&oacute;n";
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        var win = new Window("form_change_motivo",
        {
            className: "alphacube", top:top, left:left, width: 500, height: 150, 
            title: '<img src="../../iconos/page_white_link.png"> '+titulo+'',
            minWidth: 500, minHeight: 150,
            maximizable: false, minimizable: false,
            wiredDrag: true, draggable: true,
            closable: true, resizable: false 
        });
        win.setDestroyOnClose();
        win.setAjaxContent('edit_tipo_atencion.php', 
        {
            method: 'post',
            parameters: 'nomd_id='+nomd_id+'&doc_id='+<?php echo $doc_id;?>+'&esp_id='+<?php echo $esp_id;?>+'&fecha='+fecha+'&nomd_hora='+nomd_hora+'&cupo_id='+<?php echo $cupo_id;?>,
            evalScripts: true
        });
        $("form_change_motivo").win_obj=win;
        win.showCenter();
        win.show(true);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    modificar_cupos=function()
    {
        if(bloquear_modificacion)
        {
            return;
        }
        var cupos_original=<?php echo $cantn_original;?>;
        var cant_cupos=$j('#cantn_original').val();
        var accion="false";
        if((cupos_original*1)!=(cant_cupos*1))
        {
            accion="modificar_cupo";
        }
        bloquear_modificacion=true;
        $j.ajax(
        {
            url: 'sql_cupos.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {accion: accion,cantn_cupos:cant_cupos,cantc_cupo:$j('#cantc_original').val(),fecha:<?php echo $fecha;?>,cupo_id:<?php echo $cupo_id;?>,doc_id:<?php echo $doc_id;?>,esp_id:<?php echo $esp_id;?>},
            success: function(data)
            {
                resp=data;
                //mostrar_registros();
                /*
                if(resp[0]==true)
                {
                    alert('Ingreso de documento realizado exitosamente.');
                    visualizador_documentos('Visualizar Paquete de Documentos', 'paquete_id='+encodeURIComponent(resp[1]));
                }
                */
            }
        });
        $("editar_cupos").win_obj.close();
        bloquear_modificacion=false;
        cargar_horas(<?php echo $fecha;?>);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
</script>
<html>
    <input type="hidden" id="txt_asignados" name="txt_asignados" value="0" />
    <table style="font-size:12px;width: 100%;">
        <tr>
            <td>
                <div class="sub-content">
                    <table style="width: 100%">
                        <tr class="tabla_header" style="text-align:left;">
                            <td><b>Hora Atenci&oacute;n Original</b></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="vertical-align: top">
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:110px;white-space:nowrap;' valign='top' class='tabla_fila2'>Tipo de Atenci&oacute;n:</td>
                                        <td class='tabla_fila'>
                                            <select id='select_nom_motivo_original' name='select_nom_motivo_original'/>
                                                <option value="<?php echo $motivo_original;?>"><?php echo $motivo_original;?></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:40px;white-space:nowrap;' valign='top' class='tabla_fila2'>Hora: </td>
                                        <td class='tabla_fila'>
                                            <select id='desde_original' name='desde_original'>
                                                <option value="<?php echo $desde_original;?>"><?php echo $desde_original;?></option>
                                            </select>
                                            &nbsp;&nbsp;Hasta:&nbsp;&nbsp;
                                            <select id='hasta_original' name='hasta_original'>
                                                <option value="<?php echo $hasta_original;?>"><?php echo $hasta_original;?></option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'>Cupos:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantn_original' name='cantn_original' size=5  style='text-align:center;' onKeyUp='calcular_total_cupos_edit(1);' onblur="check_cupos_edit(1);" value="<?php echo $cantn_original;?>" >
                                        </td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'>Sobrecupos:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantc_original' name='cantc_original' size=5  style='text-align:center;'  onKeyUp="" onblur="" value="<?php echo $cantc_original;?>">
                                        </td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_header'>Total:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantt_original' name='cantt_original' size=5 DISABLED style='text-align:center;' value="<?php echo ($cantn_original+$cantc_original);?>">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:60px;white-space:nowrap;vertical-align: top;' class='tabla_fila2'><input type='checkbox' id='ficha_original' name='ficha_original' CHECKED />Requiere Ficha</td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;vertical-align: top;' class='tabla_fila2'><input type='checkbox' id='adr_original' name='adr_original' CHECKED />Imprime ADR</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='sub-content2' id='list_agenda_original' name='list_agenda_original' style='height:400px;overflow:auto;'>
                    <?php
                        $asignados=0;
                        $nombrecupo=$nom_recurso?'BLOQUE':'CUPO';
                        $cc=0;
                        if($lista!=false)
                        {
                            print("
                            <table style='width:100%;font-size:11px;' class='lista_small celdas' cellspacing=0>
                                <tr class='tabla_header'>
                                    <td>#</td>
                                    <td>Hora</td>
                                    <td>RUT/Ficha</td>
                                    <td>Paciente</td>
                                    <td>Sobrecupo</td>
                                    <td>Estado</td>
                                </tr>");
                            for($i=0;$i<count($lista);$i++)
                            {
                                ($cc%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                                $cc++;
                                if($lista[$i]['pac_id']==0)
                                {
                                    ($cc%2==0) ? $color='#BBDDBB' : $color='#BBEEBB';
                                    if($lista[$i]['nomd_diag_cod']=='X')
                                    {
                                        $asignados=$asignados+1;
                                        ($cc%2==0) ? $color='#ff8888' : $color='#ee8888';
                                        $cestado='BLOQUEADO ('.$lista[$i]['cancela_desc'].')';
                                        $boton1='';
                                    }
                                    else
                                    {
                                        $string_extra="";
                                        if($lista[$i]['nomd_extra']=="S")
                                        {
                                            $color='#ff9933';
                                            //$string_extra="EXTRA";
                                        }
                                        
                                        $ntipo=$lista[$i]['nom_motivo'];	
                                        if($ntipo!='')
                                            $cestado='DISPONIBLE <span id="span_tipo_atencion" class="texto_tooltip" ondblclick=cambiar_tipo_atencion('.$lista[$i]['nomd_id'].',"'.substr($lista[$i]['nomd_hora'],0,5).'");><i>('.$ntipo.')<i></span>';
                                        else
                                            $cestado='DISPONIBLE';
                    
                                        //$horas_html.="<option value='".substr($lista[$i]['nomd_hora'],0,5)."'>".substr($lista[$i]['nomd_hora'],0,5)."</option>";
                                        //$boton1="<img src='iconos/pencil.png'  style='cursor:pointer;' onClick='asignar(".($lista[$i]['nomd_id']).");' />";
                                    }
                                    //$hora_arr=str_replace(":",".",substr($lista[$i]['nomd_hora'],0,5));
                                    //$hora_arr=explode(":",$hora_arr);
                                    print("
                                    <tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick='');>
                                        <td style='text-align:right;font-weight:bold;' class='tabla_header'>".($i+1)."</td>
                                        <td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
                                        <td style='text-align:center;font-weight:bold;' colspan=12><i>$nombrecupo $string_extra $cestado</i></td>
                                    </tr>");
                                    continue;
                                }
                                if($lista[$i]['nomd_diag_cod']!='X' AND $lista[$i]['nomd_diag_cod']!='T')
                                {
                                    $asignados=$asignados+1;
                                    
                                    if($lista[$i]['nomd_extra']=="S")
                                    {
                                        $color='#ff9933';
                                        //$string_extra="EXTRA";
                                    }
                                    print("<tr class='$clase' style='background-color:$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' onClick=''>");
                                }
                                else
                                {
                                    $asignados=$asignados+1;
                                    ($cc%2==0) ? $color='#ff8888' : $color='#ee8888';
                                    print("<tr style='background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";'>");
                                }
                                if($lista[$i]['nomd_diag_cod']=='X' OR $lista[$i]['nomd_diag_cod']=='T' OR $lista[$i]['nomd_diag_cod']=='N')
                                    $motivo_enabled='';
                                else
                                    $motivo_enabled='DISABLED';
                                if($lista[$i]['nomd_origen']=='A')
                                    $origen_enabled='';
                                else
                                    $origen_enabled='DISABLED';

                                    print("<td style='text-align:center;font-weight:bold;' class='tabla_header'>".($i+1)."</td>");
                                    print("<td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>");
                                    print("<td style='text-align:center;font-weight:bold;'>".($lista[$i]['pac_rut']!=''?$lista[$i]['pac_rut']:$lista[$i]['pac_ficha'])."</td>");
                                    $ord=1;
                                    if($ord!=2)
                                        print("<td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>");
                                    else
                                        print("<td>".htmlentities(strtoupper($lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat'].' '.$lista[$i]['pac_nombres']))."</td>");   
                                
                                    print("<td style='text-align:center;font-weight:bold;font-size:16px;'>".$lista[$i]['nomd_extra']."</td>");
                                    
                                    if($lista[$i]['nomd_diag_cod']=='')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>AGENDADO</td>");
                                    }
                                    if($lista[$i]['nomd_diag_cod']=='OK')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>ATENDIDO</td>");
                                    }
                                    if($lista[$i]['nomd_diag_cod']=='ALTA')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>ALTA DE ESPECIALIDAD</td>");
                                    }
                                    if($lista[$i]['nomd_diag_cod']=='N')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>NO ATENDIDO</td>");
                                    }
                                    if($lista[$i]['nomd_diag_cod']=='X')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>BLOQUEADO</td>");
                                    }
                                    if($lista[$i]['nomd_diag_cod']=='T')
                                    {
                                        print("<td style='text-align:center;font-weight:bold;font-size:16px;'>SUSPENDIDO</td>");
                                    }
                                    
                                    
                                print("</tr>");
                            }
                            print("</table>");
                            
                        }
                        else
                        {
                            print("<table><tr><td>No se han encontrado cupos en esta selecci&oacute;n</td><tr></table>");
                        }                        
                    ?>
                </div>
                <center>
                    <input type="button" id="btn_modificar_ori" name="btn_modificar_ori" value="Modificar" onclick="modificar_original();">
                </center>
                
            </td>
            <td style="display: none">
                <div class="sub-content" >
                    <table style="width: 100%">
                        <tr class="tabla_header" style="text-align:left;">
                            <td><b>Hora Atenci&oacute;n Nueva</b></td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="vertical-align: top">
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:110px;white-space:nowrap;' valign='top' class='tabla_fila2'>Tipo de Atenci&oacute;n:</td>
                                        <td class='tabla_fila'>
                                            <select id='select_nom_motivo_edit' name='select_nom_motivo_edit'/>
                                                <?php echo $tipoatencionhtml_edit;?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:40px;white-space:nowrap;' valign='top' class='tabla_fila2'>Hora: </td>
                                        <td class='tabla_fila'>
                                            <select id='desde_edit' name='desde_edit'>
                                                <?php echo $horashtml_edit; ?>
                                            </select>
                                            &nbsp;&nbsp;Hasta:&nbsp;&nbsp;
                                            <select id='hasta_edit' name='hasta_edit'>
                                                <?php echo $horashtml_edit; ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top">
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'>Cupos:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantn_edit' name='cantn_edit' size=5 style='text-align:center;' onKeyUp='calcular_total_cupos_edit();'>
                                        </td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_fila2'>Sobrecupos:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantc_edit' name='cantc_edit' size=5 style='text-align:center;' onKeyUp="" onblur="">
                                        </td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;' valign='top' class='tabla_header'>Total:&nbsp;</td>
                                        <td class='tabla_fila'>
                                            <input type='text' id='cantt_edit' name='cantt_edit' size=5 DISABLED style='text-align:center;'>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td style='text-align:left;width:60px;white-space:nowrap;vertical-align: top;' class='tabla_fila2'><input type='checkbox' id='ficha_edit' name='ficha_edit' CHECKED />Requiere Ficha</td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td style='text-align:left;width:60px;white-space:nowrap;vertical-align: top;' class='tabla_fila2'><input type='checkbox' id='adr_edit' name='adr_edit' CHECKED />Imprime ADR</td>
                                        <td>
                                            <input type="button" id="btn_visualizar" name="btn_visualizar" value="Visualizar" onclick="visualizar_nuevo();">
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='sub-content2' id='list_agenda_remplazo' name='list_agenda_remplazo' style='height:400px;overflow:auto;'>
                    
                </div>
            </td>
        </tr>
    </table>
</html>
<script>
    asignados=<?php echo $asignados*1;?>;
    if((asignados*1)!=0)
    {
        $j('#txt_asignados').val(asignados);
        $j('#desde_original').attr('disabled','disabled');
        $j('#hasta_original').attr('disabled','disabled');
        $j('#cantn_original').attr('disabled','disabled');
    }
</script>