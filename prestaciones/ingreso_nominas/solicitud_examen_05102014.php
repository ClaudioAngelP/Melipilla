<?php
    require_once('../../conectar_db.php');
    $nomd_id=$_POST['nomd_id'];
    $pac_id=$_POST['pac_id'];
    $esp_id=$_POST['esp_id'];
    
    function graficar($tramite, $total)
    {
        if(($tramite*1)>0)
        {
            $tramite=round($tramite*100/$total);
            $resto=100-($tramite);
	}
        else
        {
            $html="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0>
            <tr>";
            $html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
            $html.="</tr></table>";
            return $html;
        }
	$html="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0>
	<tr>";
	if($tramite>0)
            $html.="<td style='width:$tramite%;background-color:#22CC22;'>&nbsp;</td>";
        if($resto>0)
            $html.="<td style='width:$resto%;background-color:#dddddd;'>&nbsp;</td>";
	$html.="</tr></table>";
	return $html;
    }
    
    //$espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_recurso=true ORDER BY esp_desc", NULL, '', '');
?> 
<script>
    var bloquear_ajax=false;
    var registros_examenes="";
    var registros_prestaciones="";
    presta_examen=[];
    //presta_lab=[];
    //presta_eco=[];
    
    
    examenes=function(tabs_index)
    {
        if(tabs_index==1)
        {
            tab_down('tab_examenes_solicitud');
            tab_up('tab_examenes_historia');
            listar_examen_historia();
        }
        if(tabs_index==2)
        {
            
            tab_down('tab_examenes_historia');
            tab_up('tab_examenes_solicitud');
            document.getElementById('esp_exam').value = '0';
            document.getElementById('tipo_exam').value = '0';
            $('tipo_exam').style.display='none';
            presta_examen.length=0;
            $j('#lista_examen_favorito').html('');
            $j('#lista_presta_examen').html('');
        }
        
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    mod_tipo_examen=function(index)
    {
        var esp_examen=($('esp_exam').value*1);
        /*
        if(index==1)
        {
            presta_examen.length=0;
            $j('#lista_examen_favorito').html('');
            $j('#lista_presta_examen').html('');
        }
        */
        if(index==0)
        {
            $j.ajax(
            {
                url: 'listar_tipo_examen.php',
                type: 'POST',
                dataType: 'json',
                async:false,
                data: {esp_examen:esp_examen},
                success: function(data)
                {
                    tipo_examen=data;
                    if(tipo_examen!=false)
                    {
                        $('tipo_exam').style.display='';
                        $j('#tipo_exam option[value!="0"]').remove();
                        $j('tipo_exam').empty();
                        var string_option="";
                        for(var i=0;i<tipo_examen[0].length;i++)
                        {
                            if(tipo_examen[0][i]['option']=="0")
                            {
                                string_option="Todos los examenes....";
                            }
                            else
                            {
                                string_option=tipo_examen[0][i]['option'];
                            }
                            $j('#tipo_exam').append('<option value="'+tipo_examen[0][i]['option']+'">'+string_option+'</option>');
                        }
                    }
                }
            });
        }
        $('desc_presta_examen').value='';
        $('cod_presta_examen').value='';
        $('pc_id_examen').value='';
        $('cantidad_examen').value='1';
        $('lista_examen_favorito').innerHTML='';
        $('lista_presta_examen').innerHTML='';
        if(esp_examen!=0)
        {
            listar_presta_especialidad();
            listar_prestaciones_examen();
        }
        $('cod_presta_examen').select();
        $('cod_presta_examen').focus();
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    agregar_prestacion_examen = function(index,llamada)
    {
        var esp_exam=($('esp_exam').value*1);
        if(esp_exam==0)
        {
            alert("Debe ingresar Tipo de Examenes a solicitar".unescapeHTML());
            return;
        }
        if(llamada==1)
        {
            x='';
            if(esp_exam==6117)
            {
                if($('tipo_exam').value=='Ecotomografia')
                {
                    if(registros_prestaciones[0][index]['codigo']=='0404016' && registros_prestaciones[0][index]['pc_desc']=='Partes Blandas')
                    {
                        var organo=prompt("Favor ingresar Organo","");
                        if(organo=='' || organo==undefined) return;
                        if (organo!=null)
                        {
                            x=organo;
                        }
                        else
                        {
                            x='';
                        }
                    }
                    
                }
                
            }
            var codigo=registros_prestaciones[0][index]['codigo'];
            if(x!='')
            {
                var desc_presta=registros_prestaciones[0][index]['pc_desc']+" "+"["+x+"]";
            }
            else
            {
                var desc_presta=registros_prestaciones[0][index]['pc_desc'];
            }
            var cant=1;
            var esp=$('esp_exam').value;
            var pc_id=registros_prestaciones[0][index]['pc_id'];
            var tipo_examen=registros_prestaciones[0][index]['pc_grupo_examen'];
	     var observ_examen=$('obs_examen').value;
        }
        if(llamada==2)
        {
            if($('cod_prestacion').value==0 || $('cod_prestacion').value=="0")
            {
                alert("Debe Seleccionar prestacion para ingresar".unescapeHTML());
                return;
            }
            if($('cod_presta_examen').value=="")
            {
                alert("Debe Seleccionar prestacion para ingresar".unescapeHTML());
                return;
            }
            codigo=$('cod_presta_examen').value;
            desc_presta=$('desc_presta_examen').value;
            //var cant=$('cantidad_examen').value;
            var cant=1;
            var esp=$('esp_exam').value;
            var pc_id=$('pc_id_examen').value;
            var tipo_examen="";
            var observ_examen=$('obs_examen').value;
        }
        var encontrado=false;
        for(var i=0;i<presta_examen.length;i++)
        {
            if(presta_examen[i].pc_id==pc_id)
            {
                alert("Ya ha ingresado la prestaci&oacute;n seleccionada".unescapeHTML());
                return;
                //presta_examen[i].cantidad=(presta_examen[i].cantidad*1)+(cant*1);
                //encontrado=true;
            }
        }
        if(encontrado==false)
        {
            var num=presta_examen.length;
            presta_examen[num]=new Object();
            presta_examen[num].esp=esp;
            presta_examen[num].codigo=codigo;
            presta_examen[num].desc=desc_presta;
            presta_examen[num].cantidad=cant;
            presta_examen[num].pc_id=pc_id;
            presta_examen[num].tipo_examen=tipo_examen;
            presta_examen[num].obs_examen=observ_examen;
            
        }
        listar_prestaciones_examen();
        if(llamada==2)
        {
            $('cod_prestacion').value=0;
            $('cod_presta_examen').value='';
            $('desc_presta_examen').value='';
            $('cantidad_examen').value='1';
            $('pc_id_examen').value='';
            $('cod_presta_examen').select();
            $('obs_examen').value='';
            $('td_nom_exam').innerHTML='';
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    listar_prestaciones_examen=function()
    {
        var esp_exam=($('esp_exam').value*1);
        if(esp_exam===0 || esp_exam==="0")
        {
            return;
        }
        var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Descripci&oacute;n</td><td>Tipo Ex&aacute;men</td><td>Eliminar</td></tr>';    
        
        for(var i=0;i<presta_examen.length;i++)
        {
            clase=(i%2==0)?'tabla_fila':'tabla_fila2';
            if(presta_examen[i].desc.length>100)
                var descr=presta_examen[i].desc.substr(0,100)+'...';
            else
                var descr=presta_examen[i].desc;	
            if(presta_examen[i].esp==esp_exam)
            {
                html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                    html+='<td style="text-align:center;font-weight:bold;">'+presta_examen[i].codigo+'</td>';
                    //html+='<td style="text-align:center;">'+presta_examen[i].cantidad+'</td>';
                    html+='<td>'+descr+'</td>';
                    html+='<td style="text-align:center;">'+presta_examen[i].tipo_examen+'</td>';
                    html+='<td><center><img src="../../iconos/delete.png" style="cursor: pointer;" onClick="eliminar_prestacion_examen('+i+');"></center></td>';
                html+='</tr>';
            }
        }
        html+='</table>'
        $('lista_presta_examen').innerHTML=html;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    listar_presta_especialidad=function()
    {
        if(bloquear_ajax)
        {
            return;
        }
        var esp_exam=($('esp_exam').value*1);
        var tipo_exam=$('tipo_exam').value;
        
        bloquear_ajax=true;
        $j('#lista_examen_favorito').html('<center><table><tr><td><img src=../../imagenes/loading_small.gif></td></tr></table></center>');
        $j.ajax(
        {
            url: 'buscar_prestaciones.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {esp_id:esp_exam,tipo_exam:tipo_exam},
            success: function(data)
            {
                registros_prestaciones=data;
                dibujar_favoritos();
            }
        });
        //array_docs=new Array();
        bloquear_ajax=false;
        
        
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    dibujar_favoritos=function()
    {
        if(registros_prestaciones[0]==false)
        {
            $j('#lista_examen_favorito').html('<center><table><tr><td>NO SE HAN ENCONTRADO PRESTACIONES</td></tr></table></center>');
        }
        else
        {
            var html;
            html='<table style="width:100%;">';
            html+='<tr class="tabla_header" style="font-size:12px;">';
                html+='<td style="text-align:center;font-size:12px;">CODIGO</td>';
                html+='<td style="text-align:center;font-size:12px;">PRESTACI&Oacute;N</td>';
                html+='<td style="text-align:center;font-size:12px;">SECTOR</td>';
                html+='<td style="text-align:center;font-size:12px;">TIPO</td>';
            html+='</tr>';
            for(var i=0;i<registros_prestaciones[0].length;i++)
            {
                var descr="";
                if(registros_prestaciones[0][i]['pc_desc']!=null)
                {
                    var descr=registros_prestaciones[0][i]['pc_desc'];
                    if(descr.length>100)
                        descr=descr.substr(0,100)+'...';
                }
                if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                html+="<tr class="+clase+" style='cursor: pointer;' onMouseOver=this.className=\'mouse_over\' onMouseOut=this.className=\'"+clase+"\' ondblclick='agregar_prestacion_examen("+i+",1);'>";
                    /*
                    html+="<input type='hidden' id='cod_prestacion_"+i+"' name='cod_prestacion_"+i+"' value='0' />";
                    html+="<input type='hidden' id='desc_presta_examen_"+i+"' name='desc_presta_examen_"+i+"' value='0' />";
                    html+="<input type='hidden' id='pc_id_examen_"+i+"' name='pc_id_examen_"+i+"' value='0' />";
                    html+="<input type='hidden' id='cod_presta_examen_"+i+"' name='cod_presta_examen_"+i+"' value='0' />";
                    */
                    html+='<td style="font-size:12px;text-align:center;">'+registros_prestaciones[0][i]['codigo']+'</td>';
                    html+='<td style="font-size:12px;text-align:left;">'+descr+'</td>';
                    html+='<td style="font-size:12px;text-align:center;">'+registros_prestaciones[0][i]['pc_grupo']+'</td>';
                    html+='<td style="font-size:12px;text-align:center;">'+registros_prestaciones[0][i]['pc_grupo_examen']+'</td>';
                    //html+='<td style="font-size:12px;text-align:left;">'+descr+'</td>';
                html+='</tr>';
            }
            html+='</table>';
            $j('#lista_examen_favorito').html(html)
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    eliminar_prestacion_examen = function(id)
    {
        presta_examen=presta_examen.without(presta_examen[id]);
	listar_prestaciones_examen();
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    solicitar_examenes= function()
    {
        if(bloquear_ajax==true)
        {
            return;
        }
        if(presta_examen.length==0)
        {
            alert("NO HA INGRESADO PRESTACIONES DE EXAMENES PARA REALIZAR LA SOLICITUD".unescapeHTML());
            return;
        }
        bloquear_ajax=true;
        $j.ajax(
        {
            url: 'sql_examen.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {presta_examen: presta_examen,nomd_id:$('nomd_id').value,pac_id:$('pac_id').value,esp_id:$('esp_id').value},
            success: function(data)
            {
                resp=data;
            }
        });
        document.getElementById('esp_exam').value = '0';
        presta_examen.length=0;
        $j('#lista_examen_favorito').html('');
        $j('#lista_presta_examen').html('');
        bloquear_ajax=false;
        examenes(1);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    listar_examen_historia = function()
    {
        if(bloquear_ajax)
        {
            return;
        }
        var pac_id=$j('#pac_id').val();
        var esp_id=$j('#esp_id').val();
        var examen_propios="";
        if($j("#examen_propios").is(':checked')){
            examen_propios=1;
        }
        else
        {
            examen_propios=0;
        }
        var list=1;
        bloquear_ajax=true;
        $j('#list_examenes_historia').html('<center><table><tr><td><img src=../../imagenes/loading_small.gif></td></tr></table></center>');
        $j.ajax(
        {
            url: 'buscar_examenes.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {pac_id:pac_id,esp_id:esp_id,examen_propios:examen_propios,list:list},
            success: function(data)
            {
                registros_examenes=data;
                dibujar_historia_examen();
            }
        });
        //array_docs=new Array();
        bloquear_ajax=false;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    dibujar_historia_examen=function()
    {
        if(registros_examenes[0]==false)
        {
            $j('#list_examenes_historia').html('<center><table><tr><td>NO SE HAN ENCONTRADO EX&Aacute;MENES</td></tr></table></center>');
        }
        else
        {
            var html;
            html='<table style="width:100%;">';
            html+='<tr class="tabla_header" style="font-size:12px;">';
                html+='<td style="text-align:center;font-size:12px;">Especialidad</td>';
                html+='<td style="text-align:center;font-size:12px;">Tipo Ex&aacute;men</td>';
                html+='<td style="text-align:center;font-size:12px;">Detalle</td>';
                html+='<td style="text-align:center;font-size:12px;">Fecha Solicitud</td>';
                html+='<td style="text-align:center;font-size:12px;">Solicitado por</td>';
                html+='<td style="text-align:center;font-size:12px;">Estado Ex&aacute;men</td>';
                html+='<td style="text-align:center;font-size:12px;">&nbsp;</td>';
            html+='</tr>';
            for(var i=0;i<registros_examenes[0].length;i++)
            {
                if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                html+="<tr class="+clase+" onMouseOver=this.className=\'mouse_over\' onMouseOut=this.className=\'"+clase+"\' onClick=''>";
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['esp_desc']+'</td>';
                    if(registros_examenes[0][i]['sol_tipo_examen']=="")
                    {
                        if(registros_examenes[0][i]['pc_grupo_examen']=="")
                        {
                            html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['pc_grupo']+'</td>';
                        }
                        else
                        {
                            html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['pc_grupo_examen']+'</td>';
                        }
                    }
                    else
                    {
                        html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['sol_tipo_examen']+'</td>';
                    }
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['pc_desc']+'</td>';
                    html+='<td style="font-size:12px;text-align:center;">'+registros_examenes[0][i]['fecha_solicitud']+'</td>';
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['func_nombre']+'</td>';
                    if(registros_examenes[0][i]['sol_examd_realizado']=="t")
                    {
                        html+='<td style="font-size:12px;text-align:center;">Realizado</td>';
                    }
                    else
                    {
                        html+='<td style="font-size:12px;text-align:center;">Pendiente</td>';
                    }
                    if(registros_examenes[0][i]['sol_examd_informe']=="" || registros_examenes[0][i]['sol_examd_informe']==null)
                    {
                        html+='<td style="text-align:center;font-size:12px;">&nbsp;</td>';
                    }
                    else
                    {
                        html+='<td style="text-align:center;font-size:12px;"><center><img src="../../iconos/script_edit.png"  style="cursor:pointer;" onClick="informe_examen('+(registros_examenes[0][i]['sol_examd_id'])+');" /></center></td>';
                    }
                    
                html+='</tr>';
                /*
                html+="<tr class="+clase+" onMouseOver=this.className=\'mouse_over\' onMouseOut=this.className=\'"+clase+"\' onClick=''>";
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['nom_esp']+'</td>';
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['sol_tipo_examen']+'</td>';
                    html+='<td style="font-size:12px;text-align:center;">'+registros_examenes[0][i]['solicitud_fecha']+'</td>';
                    html+='<td style="font-size:12px;text-align:left;">'+registros_examenes[0][i]['func_nombre']+'</td>';
                    if((registros_examenes[0][i]['sol_estado']*1)==0)
                    {
                        html+='<td style="font-size:12px;text-align:center;">NO AGENDADO</td>';
                    }
                    else
                    {
                        html+='<td style="font-size:12px;text-align:center;">AGENDADO</td>';
                    }
                    html+='<td style="text-align: center;"><center>'+graficar(registros_examenes[0][i]['realizadas'],registros_examenes[0][i]['total'])+'<b>('+registros_examenes[0][i]['realizadas']+'/'+registros_examenes[0][i]['total']+')</b></center></td>';
                    html+='<td style="text-align: center;" style="backgroun-color:#FFFFFF;" title="Ver Ex&aacute;menes Solicitados">';
                        html+="<b><i><img src='../../iconos/zoom.png' style='cursor:pointer;' onClick='ver_examen_sol("+registros_examenes[0][i]['sol_exam_id']+");'></i></b>";
                    html+='</td>';
                    html+="<td><center><img OnClick='cancelar_solicitud("+i+");' style='cursor: pointer;' src='../../iconos/delete.png'></center></td>";
                    
                html+='</tr>';
                */
            }
            html+='</table>';
            $j('#list_examenes_historia').html(html)
        }
    }
    //--------------------------------------------------------------------------
    graficar=function(tramite, total)
    {
        if((tramite*1)>0)
        {
            tramite=Math.round(tramite*100/total);
            resto=100-(tramite);
	}
        else
        {
            var html2="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0><tr>";
                html2+="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
            html2+="</tr></table>";
            return html2;
        }
	var html2="<table style='width:100px;border:1px solid black;' cellpadding=0 cellspacing=0><tr>";
	if(tramite>0)
            html2+="<td style='width:$tramite%;background-color:#22CC22;'>&nbsp;</td>";
        if(resto>0)
            html2+="<td style='width:$resto%;background-color:#dddddd;'>&nbsp;</td>";
	html2+="</tr></table>";
	return html2;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    ver_examen_sol=function(index)
    {
        var titulo="Examenes Solicitados";
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        var win = new Window("form_examenes",
        {
            className: "alphacube", top:top, left:left, width: 750, height: 300, 
            title: '<img src="../../iconos/page_white_link.png"> '+titulo+'',
            minWidth: 500, minHeight: 150,
            maximizable: false, minimizable: false,
            wiredDrag: true, draggable: true,
            closable: true, resizable: false 
        });
        win.setDestroyOnClose();
        win.setAjaxContent('detalle_examenes.php', 
        {
            method: 'post',
            parameters: 'sol_exam_id='+index,
            evalScripts: true
        });
        $("form_examenes").win_obj=win;
        win.showCenter();
        win.show(true);
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
</script>
<br>
<div class='sub-content'>
    <input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />
    <input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />
    <input type='hidden' id='pac_id' name='pac_id' value='<?php echo $pac_id; ?>' />
        <table width=100% cellpadding=0 cellspacing=0>
            <tr>
                <td>
                    <table cellpadding=0 cellspacing=0>
                        <tr>
                            <td>
                                <div class='tabs' id='tab_examenes_historia' style='cursor: default;' onClick='examenes(1);'>
                                    <img src='../../iconos/report.png'>
                                    Historial de Ex&aacute;menes
                                </div>
                            </td>
                            <td>
                                <div class='tabs_fade' id='tab_examenes_solicitud' style='cursor: pointer;' onClick='examenes(2);'>
                                    <img src='../../iconos/report.png'>
                                    Solicitud de Ex&aacute;menes
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <div class='tabbed_content' id='tab_examenes_historia_content'>
                        <div class='sub-content2' id='list_examenes_historia' name='list_examenes_historia' style='height:250px;overflow:auto;'>
                        </div>
                    </div>
                    <div class='tabbed_content' id='tab_examenes_solicitud_content' style='height: 470px; overflow:auto;display:none;'>
                        <div class='sub-content2' id='list_examenes_solicitud' name='list_examenes_solicitud' style='height: 450px; overflow:auto;'>
                            <br/>
                            <table>
                                <tr>
                                    <td>
                                        <select id='esp_exam' name='esp_exam' onChange='mod_tipo_examen(0);' style='display:block;'>
                                            <option value='0' selected>Seleccionar Tipo Ex&aacute;men</option>
                                            <option value='6117' >IMAGENOLOG&Iacute;A HME</option>
                                            <option value='6120' >LABARATORIO HME</option>
                                            <?php
                                            if($_SESSION['sgh_usuario_id']==7)
                                            {
                                            ?>
                                            <option value='99999' >LABARATORIO HME V2</option>
                                            <?php
                                            }
                                            ?>
                                            <!--<?php //echo $espechtml; ?>-->
                                        </select>
                                    </td>
                                    <td>
                                        <select id='tipo_exam' name='tipo_exam' onChange='mod_tipo_examen(1);' style='display:none;'>
                                            
                                            <option value='Radiografia' selected>Radiograf&iacute;a</option>
                                            <option value='Ecotomografia' >Ecotomograf&iacute;a</option>
                                            <option value='Gine Ecotomografia'>Gine Ecotomograf&iacute;a</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                            <br/>
                            <table style="width: 100%">
                                <tr class="tabla_header" style="text-align:left;">
                                    <td><b>Ex&aacute;menes de Especialidad</b></td>
                                </tr>
                            </table>
                            <div class='sub-content2' id='lista_examen_favorito' style='height:120px;overflow:auto;'>
                            </div>
                            <br/>
                            <table style="width: 100%">
                                <tr class="tabla_header" style="text-align:left;">
                                    <td><b>Ex&aacute;menes solicitados</b></td>
                                </tr>
                            </table>
                            <div class='sub-content' style="display: block">
                                <table style='width:100%;' cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style='width:15px;'>
                                            <center>
                                                <img src='../../iconos/add.png' />
                                            </center>
                                        </td>
                                        <td style='width:100px;text-align:right;'>Agregar Prest.:</td>
                                        <td>
                                            <input type='hidden' id='cod_prestacion' name='cod_prestacion' value='0' />
                                            <input type='hidden' id='desc_presta_examen' name='desc_presta_examen' value='' />
                                            <input type='hidden' id='pc_id_examen' name='pc_id_examen' value='0' />
                                            <input type='text' id='cod_presta_examen' name='cod_presta_examen' size=10 />
                                        </td>
                                        <td style='width:100px;text-align:left;'>
                                            Observaci&oacute;n:
                                        </td>
                                        <td>
                                            <input type='text' id='cantidad_examen' name='cantidad_examen_examen' onKeyUp='if(event.which==13) agregar_prestacion_examen(0,2);' size=3 style="display: none;"/>
                                            <input type='text' id='obs_examen' name='obs_examen_examen' onKeyUp='if(event.which==13) agregar_prestacion_examen(0,2);' size=100 />
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" id="td_nom_exam" style="text-align: left;width: 100%;"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class='sub-content2' id='lista_presta_examen' style='height:120px;overflow:auto;'>
                            </div>
                            <br>
                            <center>
                                <input type='button' value='[ Solicitar ]' onClick='solicitar_examenes();'>
                            </center>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    
    
</div>
    
    
<script>
    
    seleccionar_prestacion_examen = function(presta_examen)
    {
        $('cod_prestacion').value=presta_examen[0];
        $('cod_presta_examen').value=presta_examen[0];
        $('desc_presta_examen').value=presta_examen[2];
        $('pc_id_examen').value=presta_examen[4]*1;
        
        $('obs_examen').value='';
        $('obs_examen').select();
        $('obs_examen').focus();
        $('td_nom_exam').innerHTML='<b>&nbsp;'+presta_examen[2]+'</b>';
    }
    
    
    autocompletar_prestaciones = new AutoComplete('cod_presta_examen','../../autocompletar_sql.php',
    function(){
    if($('cod_presta_examen').value.length<3)return false;
    return {
        method: 'get',
        parameters: 'tipo=proc_prestacion&cod_presta='+encodeURIComponent($('cod_presta_examen').value)+'&esp_id='+encodeURIComponent($('esp_exam').value)
    }
    }, 'autocomplete', 450, 300, 150, 1, 3, seleccionar_prestacion_examen);
    
    listar_examen_historia();
</script>
