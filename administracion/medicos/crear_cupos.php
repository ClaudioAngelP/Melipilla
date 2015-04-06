<?php
    ini_set('memory_limit','2048M');
    ini_set('max_execution_time',300);
    require_once('../../conectar_db.php');
    $esp_id=$_GET['esp_id']*1;
    $doc_id=$_GET['doc_id']*1;
    $consulta="SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id, 
    cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
    esp_desc, nom_motivo,nom_tipo_contrato,
    nom_id,
    (select count(*) from nomina_detalle where nomina_detalle.nom_id=nomina.nom_id and (pac_id!=0 and pac_id is not null) AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL))as cant,
    esp_id
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    LEFT JOIN nomina USING (nom_id)
    WHERE cupos_doc_id=$doc_id ORDER BY cupos_fecha, cupos_horainicio;";
    
    
    $fechas = cargar_registros_obj($consulta, true);
    
    
    $fechas_ausencias = cargar_registros_obj("SELECT DISTINCT ausencia_fechainicio, ausencia_fechafinal FROM ausencias_medicas WHERE (doc_id=$doc_id OR doc_id=0);", true);
    $ausencias=cargar_registros_obj("SELECT * FROM ausencias_medicas JOIN ausencias_motivos ON motivo_id=ausencia_motivo WHERE doc_id=$doc_id ORDER BY ausencia_fechainicio", true);
    $fechas_ausente=Array();
    
    
    for($i=0;$i<count($fechas_ausencias);$i++)
    {
        if($fechas_ausencias[$i]['ausencia_fechafinal']==''){
            $fechas_ausente[count($fechas_ausente)]=$fechas_ausencias[$i]['ausencia_fechainicio'];
		}
        else
        {
			
            $finicio=explode('/',$fechas_ausencias[$i]['ausencia_fechainicio']);
            $ffinal=explode('/',$fechas_ausencias[$i]['ausencia_fechafinal']);
            $fi=mktime(0,0,0,$finicio[1],$finicio[0],$finicio[2]);
            $ff=mktime(0,0,0,$ffinal[1],$ffinal[0],$ffinal[2]);
            for(;$fi<=$ff;$fi+=86400)
            {
                //$fechas_ausente[count($fechas_ausente)]=date('d/m/Y',$fi);
                $fechas_ausente[]=date('d/m/Y',$fi);
            } 
                      
        }  
    }
    
    $medico=cargar_registro("SELECT * FROM doctores WHERE doc_id=$doc_id");
    $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id");
    $horashtml='';
    for($i=0;$i<24;$i++)
    {
        ($i<10) ? $h='0'.$i : $h=$i;
        if($h=='08')
            $sel='SELECTED';
        else
            $sel='';
    
        $horashtml.='<option value="'.$h.':00" '.$sel.'>'.$h.':00</option>';
        $horashtml.='<option value="'.$h.':05">'.$h.':05</option>';
        $horashtml.='<option value="'.$h.':10">'.$h.':10</option>';
        $horashtml.='<option value="'.$h.':15">'.$h.':15</option>';
        $horashtml.='<option value="'.$h.':20">'.$h.':20</option>';
        $horashtml.='<option value="'.$h.':25">'.$h.':25</option>';
        $horashtml.='<option value="'.$h.':30">'.$h.':30</option>';
        $horashtml.='<option value="'.$h.':35">'.$h.':35</option>';
        $horashtml.='<option value="'.$h.':40">'.$h.':40</option>';
	$horashtml.='<option value="'.$h.':45">'.$h.':45</option>';
        $horashtml.='<option value="'.$h.':50">'.$h.':50</option>';
        $horashtml.='<option value="'.$h.':55">'.$h.':55</option>';
    }

    $tipo_atencion=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo", true);
    $tipoatencionhtml='';
    for($i=0;$i<count($tipo_atencion);$i++)
    {
        $tipoatencionhtml.='<option value="'.$tipo_atencion[$i]['nom_motivo'].'">'.$tipo_atencion[$i]['nom_motivo'].'</option>';
    }
    
    $tipo_contrato=cargar_registros_obj("SELECT DISTINCT nom_tipo_contrato from nomina where nom_tipo_contrato is not null order by nom_tipo_contrato", true);
    $tipocontratohtml='';
    for($i=0;$i<count($tipo_contrato);$i++)
    {
        $tipocontratohtml.='<option value="'.$tipo_contrato[$i]['nom_tipo_contrato'].'">'.$tipo_contrato[$i]['nom_tipo_contrato'].'</option>';
    }
    
?>
<html>
    <title>Definir Cupos para Atenci&oacute;n</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        var calendario;
        var fechas_ocupadas = <?php echo json_encode($fechas); ?>;
        var fechas_ausente = <?php echo json_encode($fechas_ausente); ?>;
        var hrs_sel;
        var ausencias_medicas="";
        for(var i=0;i<=23;i++)
        {
            for(var j=0;j<60;j+=15)
            {
                if(i<10) si='0'+i; else si=i;
                if(j<10) sj='0'+j; else sj=j;
                var hr = si+':'+sj; 
                hrs_sel+='<option value="'+hr+'">'+hr+'</option>';
            }
        }
        
        function init()
        {
            calendario=Calendar.setup(
            {
                flat : "calendario", 
                flatCallback : cambio_fecha,
                dateStatusFunc : estado_fecha
            });
        }
  
        function cambio_fecha(calendar)
        {
            // Beware that this function is called even if the end-user only
            // changed the month/year.  In order to determine if a date was
            // clicked you can use the dateClicked property of the calendar:
            if (calendar.dateClicked)
            {
                var y = calendar.date.getFullYear();
                var m = calendar.date.getMonth();     // integer, 0..11
                var d = calendar.date.getDate();      // integer, 1..31
                if(d<10)
                    d='0'+d;
                if((m+1)<10)
                    m='0'+(m+1);
                else
                    m=(m+1);
    
                var fecha=d+'/'+m+'/'+y;

		/*if(fechas_ausente!==false)  
		for(var i=0;i<fechas_ausente.length;i++) {
		  
		  if(fecha==fechas_ausente[i]) return;
		  
		}*/
                
                $('fecha_horas').style.display='';
                $('no_fechas').style.display='none';
                $('mostrar_fecha').innerHTML=fecha;
                $('mostrar_fecha2').innerHTML=fecha;
                $('fecha').value=fecha;
                cargar_horas(fecha);
            }
        };
  
        /*
        cargar_horas=function(fecha)
        {
            var html='<table style="width:100%;font-size:12px;">';
            html+='<tr class="tabla_header"><td>Inicio</td><td>T&eacute;rmino</td>';
            html+='<td>C</td><td>E</td><td>F</td><td>ADR</td>';
            html+='<td>Especialidad</td><td>Tipo Atenci&oacute;n</td><td>Acci&oacute;n</td></tr>';
            var c=0;
            fecha+=' 00:00:00';
    
            var ver_replicar=false;
            if(fechas_ocupadas!==false)
            for(var i=0;i<fechas_ocupadas.length;i++)
            {
                if(fechas_ocupadas[i].cupos_fecha==fecha)
                {
                    (c%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
                    c++;
                    if(fechas_ocupadas[i].cupos_ficha=='t')
                        var f='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
                    else
                        var f='<center><img src="../../iconos/cross.png" width=8 height=8></center>';

                    if(fechas_ocupadas[i].cupos_adr=='t')
                        var a='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
                    else
                        var a='<center><img src="../../iconos/cross.png" width=8 height=8></center>';
                    
                    html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horainicio+'</td>';
                        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horafinal+'</td>';
                        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cantidad_n+'</td>';
                        html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cantidad_c+'</td>';
                        html+='<td style="text-align:center;">'+f+'</td>';
                        html+='<td style="text-align:center;">'+a+'</td>';
                        html+='<td>'+fechas_ocupadas[i].esp_desc+'</td>';
                        html+='<td>'+fechas_ocupadas[i].nom_motivo+'</td>';
                        html+='<td><center>';
                            html+='<img src="../../iconos/delete.png" style="cursor:pointer;" onClick="eliminar_rango('+fechas_ocupadas[i].cupos_id+');">';
                        html+='</center></td>';
                    html+='</tr>';
                    ver_replicar=true;
                }
            }
            if(ver_replicar)
            {
                $('replicar_agenda').style.display='';
            }
            else
            {
                $('replicar_agenda').style.display='none';
            }
            html+='</table>';
            $('rango').innerHTML=html;
        }
        */
           
        cargar_horas=function(fecha)
        {
            var esp_id=<?php echo $esp_id;?>;
            var html='<table style="width:100%;font-size:10px;">';
            html+='<tr class="tabla_header"><td>Inicio</td><td>T&eacute;rmino</td>';
            html+='<td>C</td><td>E</td><td>F</td><td>ADR</td>';
            html+='<td>Especialidad</td><td>Tipo Atenci&oacute;n</td><td>Tipo Contrato</td><td>Cant</td><td>Acci&oacute;n</td></tr>';
            var c=0;
            fecha+=' 00:00:00';
            var ver_replicar=false;
            if(fechas_ocupadas!==false)
                for(var i=0;i<fechas_ocupadas.length;i++)
                {
                    if(fechas_ocupadas[i].cupos_fecha==fecha)
                    {
                        (c%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
                        c++;
                        if(fechas_ocupadas[i].cupos_ficha=='t')
                            var f='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
                        else
                            var f='<center><img src="../../iconos/cross.png" width=8 height=8></center>';
                        
                        if(fechas_ocupadas[i].cupos_adr=='t')
                            var a='<center><img src="../../iconos/tick.png" width=8 height=8></center>';
                        else
                            var a='<center><img src="../../iconos/cross.png" width=8 height=8></center>';
                        
                        html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                            html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horainicio+'</td>';
                            html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_horafinal+'</td>';
                            html+='<td style="text-align:center;">'+fechas_ocupadas[i].cupos_cantidad_n+'</td>';
                            html+="<td style='text-align:center;'><center>"+fechas_ocupadas[i].cupos_cantidad_c+" <img src='../../iconos/pencil.png' onClick='modificar_extra(\""+fechas_ocupadas[i].cupos_id+"\", \""+fechas_ocupadas[i].nom_motivo+"\","+fechas_ocupadas[i].cupos_cantidad_c+");' style='cursor:pointer;' /></center></td>";
                            html+='<td style="text-align:center;">'+f+'</td>';
                            html+='<td style="text-align:center;">'+a+'</td>';
                            html+='<td style="text-align:center;">'+fechas_ocupadas[i].esp_desc+'</td>';
                            html+='<td style="text-align:center;">'+fechas_ocupadas[i].nom_motivo+'</td>';
                            if(fechas_ocupadas[i].nom_tipo_contrato!=null && fechas_ocupadas[i].nom_tipo_contrato!='')
                            {
                                html+='<td style="text-align:center;">'+fechas_ocupadas[i].nom_tipo_contrato+'</td>';
                            }
                            else
                            {
                                html+='<td>&nbsp;</td>';
                            }
                            html+='<td style="text-align:center;color:red;" title="Cupos Asignados">'+fechas_ocupadas[i].cant+'</td>';
                            if(fechas_ocupadas[i].esp_id==esp_id)
                            {
                            html+='<td>';
                                html+='<center>';
                                html+='<table>';
                                    html+='<tr>';
                                        html+='<td>';
                                            html+='<img src="../../iconos/page_edit.png" style="cursor:pointer;" title="Editar Rango" onClick="cargar_rango('+fechas_ocupadas[i].cupos_id+','+fechas_ocupadas[i].nom_id+');">';
                                        html+='</td>';
                                        html+='<td>';
                                        //html+='<center>';
                                            html+='<img src="../../iconos/delete.png" style="cursor:pointer;" title="Eliminar Rango"  onClick="eliminar_rango('+fechas_ocupadas[i].cupos_id+','+fechas_ocupadas[i].cant+');">';
                                        html+='</td>';
                                    html+='</tr>';
                                html+='</table>';
                                html+='</center>';
                            html+='</td>';
                            }
                            else
                            {
                                html+='<td>&nbsp;</td>';
                            }
                        html+='</tr>';
                        if(fechas_ocupadas[i].esp_id==esp_id)
                            ver_replicar=true;
                           
                        
                    }
                }
            if(ver_replicar)
            {
                $('replicar_agenda').style.display='';
            }
            else
            {
                $('replicar_agenda').style.display='none';
            }
            html+='</table>';
            $('rango').innerHTML=html;
        }

        function estado_fecha(date, y, m, d)
        {
            // Devuelve 'disabled' para desactivar la fecha...
            // Devuelve false para dejar la fecha intacta...
            // Devuelve '(string)' para usar esa clase...
            var clase='';
            var fecha=d+'/'+(m+1)+'/'+y;
            
            if(d<10)
                d='0'+d;
            
            if((m+1)<10)
                m='0'+(m+1);
            else
                m=(m+1);
            
            var fecha2=d+'/'+m+'/'+y+' 00:00:00';
            var fecha3=d+'/'+m+'/'+y;
            if(fechas_ausente!==false)
                for(var i=0;i<fechas_ausente.length;i++)
                {
                    if(fecha3==fechas_ausente[i])
                        clase='ausente';
                }
            if(fechas_ocupadas!==false)
                for(var i=0;i<fechas_ocupadas.length;i++)
                {
                    if(fecha2==fechas_ocupadas[i].cupos_fecha)
                    {
                        var esp_id=<?php echo $esp_id; ?>;
                        if(fechas_ocupadas[i].esp_id==esp_id)
                            clase='fechaset';
                        else
                            clase='fechaset_other_esp';
                    }
                }
            
            if(clase=='')
                return false;
            else
                return clase;
        }  
  
        function guardar_cupos()
        {
            params = $('form_cupos').serialize();
            params += '&fechas='+encodeURIComponent(fechas_sel.toJSON()); 
            var myAjax=new Ajax.Request('sql_cupos.php',
            {
                method: 'post',
                parameters: params,
                onComplete: function (resp)
                {
                    try {
                        datos=resp.responseText.evalJSON(true);          
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }
            });
        }
  
        agregar_rango=function()
        {
            var fecha=$('fecha').value;
            if(fecha=="")
            {
                alert("No se ha selecionado Fecha para ingresar rango de atención");
                return;
            }
            if($('select_nom_contrato').value=="-1")
            {
                if(!confirm('Est&aacute; seguro de NO Asignar tipo de contrato a esta nominda de atenci&oacute;n?'.unescapeHTML()))
                {
                    $("select_nom_contrato").focus();
                    return;
                }
            }
            
            
            
            var dia=fecha.substring(0,2);
            var mes=fecha.substring(3,5);
            var ano=fecha.substring(6,10);
            mes=mes-1;
            fecha=new Date(ano,mes,dia);
            ausencias_medicas=<?php echo json_encode($ausencias); ?>;
            var ausencia=false;
            for(var i=0;i<ausencias_medicas.length;i++)
            {
                var diaInicio=ausencias_medicas[i].ausencia_fechainicio.substring(0,2);
                var mesInicio=ausencias_medicas[i].ausencia_fechainicio.substring(3,5);
                var anoInicio=ausencias_medicas[i].ausencia_fechainicio.substring(6,10);
                mesInicio=mesInicio-1;
                var f1 =  new Date(anoInicio,mesInicio,diaInicio);
                var f2='';
                if(ausencias_medicas[i].ausencia_fechafinal!='' || ausencias_medicas[i].ausencia_fechafinal!=null)
                {
                    var diaFin=ausencias_medicas[i].ausencia_fechafinal.substring(0,2);
                    var mesFin=ausencias_medicas[i].ausencia_fechafinal.substring(3,5);
                    var anoFin=ausencias_medicas[i].ausencia_fechafinal.substring(6,10);
                    mesFin=mesFin-1;
                    f2 =  new Date(anoFin,mesFin,diaFin);
                }
                else
                {
                    f2 =  f1;
                }
                if(fecha>=f1 && fecha<=f2)
                {
                    if(ausencias_medicas[i].hora_inicio=='')
                    {
                        ausencias_medicas[i].hora_inicio='00:00:00';
                    }
                    if(ausencias_medicas[i].hora_final=='')
                    {
                        ausencias_medicas[i].hora_final='23:59:59';
                    }
                    
                    var hr_inicio=ausencias_medicas[i].hora_inicio;
                    var hr_final='';
                    if(ausencias_medicas[i].hora_final!=null)
                    {
                        hr_final=ausencias_medicas[i].hora_final;
                    }
                    else
                    {
                        hr_final=hr_inicio;
                    }
                    var desde=$('desde').value;
                    var hasta=$('hasta').value;

                    desde = toSeconds(desde);
                    hasta = toSeconds(hasta);
                    
                    hr_inicio=toSeconds(hr_inicio);
                    hr_final=toSeconds(hr_final);
                    /*
                    if(desde>=hr_inicio && desde<=hr_final)
                    {
                        ausencia=true;
                    
                    }
                    else if(hasta>=hr_inicio && hasta<=hr_final)
                    {
                        ausencia=true;
                    }
                    */
                    
                    if(desde>=hr_inicio && desde<=hr_final)
                    {
                        ausencia=true;
                    
                    }
                    else if(hasta>=hr_inicio && hasta<=hr_final)
                    {
                        ausencia=true;
                    }
                    else if(desde<=hr_inicio && hasta>=hr_final)
                    {
                        ausencia=true;
                    }
                    
                    
                    
                    
                    
                }
            }
            if(ausencia==true)
            {
                alert("El rango ingresado se encuentra dentro de o las fechas de ausencias medicas");
                return;

            }
            
            var myAjax=new Ajax.Request('sql_cupos.php',
            {
                method:'post',
                parameters:$('form_cupos').serialize(),
                asynchronous: true,
                onComplete: function(resp)
                {
                    if(resp.responseText=='1')
                    {
                        alert('ERROR: Ya hay cupos creados para este profesional en el rango especificado.');
                        return;
                    }
                    if(resp.responseText=='2')
                    {
                        alert('ERROR: Cupos sin nomina, contacte al administrador.');
                        return;
                    }
                    fechas_ocupadas=resp.responseText.evalJSON(true);
                    cargar_horas($('fecha').value);
                    
                    
                }
            });
        }
  
        function toSeconds(t)
        {
            var bits = t.split(':');
            if(bits.length==3)
            {
                return bits[0]*3600 + bits[1]*60 + bits[2]*1;
            }
            else
            {
                return bits[0]*3600 + bits[1]*60;
            }
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        eliminar_rango=function(cupos_id,cant_agendados)
        {
            if(parseInt(cant_agendados)>0)
            {
                alert("No puede eliminar el rango de atención ya que existen pacientes asignados");
                return;
            }
            var myAjax=new Ajax.Request('sql_cupos.php',
            {
                method:'post',
                parameters: 'eliminar=1&doc_id=<?php echo $doc_id; ?>&cupos_id='+cupos_id+'&esp_id=<?php echo $esp_id; ?>',
                asynchronous: true,
                onComplete: function(resp)
                {
                    fechas_ocupadas=resp.responseText.evalJSON(true);
                    cargar_horas($('fecha').value);
                    
                    
                }
            });
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        calcular_total_cupos=function()
        {
            $('cantt').value=($('cantn').value*1)+($('cantc').value*1);
            //$('cantti').value=$('cantt').value;
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        calcular_total_tipo=function()
        {
            var d=($('cantti').value*1)+($('cantth').value*1)+($('cantte').value*1);
            if($('cantt').value!=d)
            {
                $('cantt').style.color='red';
            }
            else
            {
                $('cantt').style.color='';
            }
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        replicar_agenda = function()
        {
            var params='doc_id=<?php echo $doc_id; ?>&'+$('fecha').serialize()+'&esp_id=<?php echo $esp_id;?>';
            var l=(screen.width/2)-290;
            var t=(screen.height/2)-200;
            replicar = window.open('replicar_agenda.php?'+params,'replicar', 'left='+l+',top='+t+',width=580,height=400,status=0,scrollbars=1');
            replicar.focus();
        }
        //--------------------------NUEVO---------------------------------------
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        cargar_rango=function(cupos_id,nom_id)
        {
            var cupo_encontrado=false;
            if(fechas_ocupadas!==false)
            {
                for(var i=0;i<fechas_ocupadas.length;i++)
                {
                    if(cupos_id==fechas_ocupadas[i].cupos_id)
                    {
                        var pos=i;
                        cupo_encontrado=true;
                        break;
                    }
                }
            }
            if(cupo_encontrado)
            {
                $('pos_hora').value=pos;
                var pos=$('pos_hora').value;
                var width=950;
                var titulo="Modificaci&oacute;n de Agenda";
                var params=$('form_cupos').serialize()+'&cupo_id='+fechas_ocupadas[pos].cupos_id+'&motivo_original='+fechas_ocupadas[pos].nom_motivo;
                params+='&desde_original='+fechas_ocupadas[pos].cupos_horainicio.substring(0,5)+'&hasta_original='+fechas_ocupadas[pos].cupos_horafinal.substring(0,5);
                params+='&cantn_original='+fechas_ocupadas[pos].cupos_cantidad_n+'&cantc_original='+fechas_ocupadas[pos].cupos_cantidad_c;
                params+='&ficha_original='+fechas_ocupadas[pos].cupos_ficha+'&adr_original='+fechas_ocupadas[pos].cupos_adr+'&nom_id='+nom_id;
                var win = new Window("editar_cupos",
                {
                    className: "alphacube", top:40, left:0, width: width, height: 600, 
                    title: '<img src="../../iconos/page_white_link.png"> '+titulo+'',
                    minWidth: 950, minHeight: 600,
                    maximizable: false, minimizable: false,
                    wiredDrag: true, draggable: true,
                    closable: true, resizable: false 
                });
                win.setDestroyOnClose();
                win.setAjaxContent('edit_cupos.php', 
                {
                    method: 'post',
                    async:false,
                    dataType: 'json',
                    parameters:params,
                });
                $("editar_cupos").win_obj=win;
                win.showCenter();
                win.show(true);
                
                
                
                
                
                /*
                $('pos_hora').value=pos;
                $('select_nom_motivo').value=fechas_ocupadas[pos].nom_motivo;
                $('desde').value=fechas_ocupadas[pos].cupos_horainicio.substring(0,5);
                $('hasta').value=fechas_ocupadas[pos].cupos_horafinal.substring(0,5);
                $('cantn').value=fechas_ocupadas[pos].cupos_cantidad_n;
                $('cantc').value=fechas_ocupadas[pos].cupos_cantidad_c;
                $('cantt').value=(fechas_ocupadas[pos].cupos_cantidad_n*1)+(fechas_ocupadas[pos].cupos_cantidad_c*1);
                if(fechas_ocupadas[pos].cupos_ficha=='t')
                    $('ficha').checked = true;
                else
                    $('ficha').checked = false;
                if(fechas_ocupadas[pos].cupos_adr=='t')
                    $('adr').checked = true;
                else
                    $('adr').checked = false;


                $('add_rango').style.display='none';
                $('edit_rango').style.display='';
                $('cancel').style.display='';
                */
            }
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        modificar_rango=function()
        {
            if(fechas_ocupadas!==false)
            {
                
                
                /*
                var cupo_edit=false;
                var pos=$('pos_hora').value;
                var modificaciones='';
                if(fechas_ocupadas[pos].nom_motivo!=$('select_nom_motivo').value)
                {
                    cupo_edit=true;
                    modificaciones+='1|';
                }
                if(fechas_ocupadas[pos].cupos_horainicio.substring(0,5)!=$('desde').value)
                {
                    cupo_edit=true;
                    modificaciones+='2|';
                }
                if(fechas_ocupadas[pos].cupos_horafinal.substring(0,5)!=$('hasta').value)
                {
                    cupo_edit=true;
                    modificaciones+='3|';
                }
                if(fechas_ocupadas[pos].cupos_cantidad_n!=$('cantn').value)
                {
                    cupo_edit=true;
                    modificaciones+='4|';
                }
                if(fechas_ocupadas[pos].cupos_cantidad_c!=$('cantc').value)
                {
                    cupo_edit=true;
                    modificaciones+='5|';
                }
                if(fechas_ocupadas[pos].cupos_ficha=='t')
                {
                    if(!$('ficha').checked)
                    {
                        cupo_edit=true;
                        modificaciones+='6|';
                    }
                }
                else
                {
                    if($('ficha').checked)
                    {
                        cupo_edit=true;
                        modificaciones+='6|';
                    }
                }
                if(fechas_ocupadas[pos].cupos_adr=='t')
                {
                    if(!$('adr').checked)
                    {
                        cupo_edit=true;
                        modificaciones+='7';
                    }
                }
                else
                {
                    if($('adr').checked)
                    {
                        cupo_edit=true;
                        modificaciones+='7';
                    }
                }
                if(cupo_edit)
                {
                    var myAjax=new Ajax.Request('check_cupo.php',
                    {
                        method:'post',
                        parameters:$('form_cupos').serialize()+'&cupos_id='+fechas_ocupadas[pos].cupos_id+'&opcion=1&modificaciones='+modificaciones,
                        onComplete: function(resp)
                        {
                            if(resp.responseText=='1')
                            {
                                
                                
                            }
                            if(resp.responseText=='2')
                            {
                                alert('ERROR: No se ha encontrado Nomina, contacte al administrador.');
                                return;
                            }
                            fechas_ocupadas=resp.responseText.evalJSON(true);
                            cargar_horas($('fecha').value);
                            cancelar_mod();
                            
                        }
                    });
                    */
               
                    
            }
            
            
            
            
            
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        cancelar_mod=function()
        {
            $('select_nom_motivo').value='C. Nueva No Prg';
            $('desde').value='08:00';
            $('hasta').value='08:00';
            $('cantn').value='';
            $('cantc').value='';
            $('cantt').value='';
            $('pos_hora').value='';
            $('ficha').checked = false;
            $('adr').checked = false;
            $('add_rango').style.display='';
            //$('edit_rango').style.display='none';
            //$('cancel').style.display='none';
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        check_cupos=function()
        {
            if($('cantc').value=='')
            {
               $('cantc').value=0;
               calcular_total_cupos();
                
            }
        }
        //--------------------------NUEVO---------------------------------------
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        modificar_extra=function(cupo_id,motivo_atencion,cant_actual)
        {
            //var cant_extra=prompt("Ingrese Cantidad de Cupos Extras \n"+centro_nombre+"\n\nItem Presupuestario: "+item_codigo+".");
            var cant_extra=prompt("Ingrese Cantidad de Cupos Extras \nMotivo Atención: "+motivo_atencion+".");
            if(cant_extra=='' || cant_extra==undefined) return;
            if(parseInt(cant_extra)<parseInt(cant_actual))
            {
                alert("La cantidad ingresada no puede ser menor a la cantidad de cupos extras ya ingresada");
                return;
            }
            var myAjax = new Ajax.Request(
		'sql_extra.php',
		{
                    method:'post',
                    parameters:'cant_extra='+cant_extra+'&cupo_id='+encodeURIComponent(cupo_id)+'&esp_id='+<?php echo $esp_id;?>+'&doc_id='+<?php echo $doc_id;?>,
                    onComplete:function(resp)
                    {
                        fechas_ocupadas=resp.responseText.evalJSON(true);
                        cargar_horas($('fecha').value);
                    }
		});
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
    </script>
    <style>
        .fechaset
        {
            background-color: #99EE99;
            font-weight:bold;
            color: #FFAAAA;
        }
        
        .fechaset_other_esp
        {
            background-color: #6699FF;
            font-weight:bold;
            color: #FFAAAA;
        }
  
        .horas {
            background-color: #FFFFFF;
            border: 1px solid black;
        }
  
        .horas td {
            border: 1px solid black;
        }
  
        .libre {
            background-color: #99EE99;
        }
  
        .ocupado {
            background-color: #DDDDDD;
    }
  
    .ausente {
        background-color: black;
    }
</style>
<body class='fuente_por_defecto popup_background' onLoad='init();'>
    <div class='sub-content'>
        <table>
            <tr>
                <td style='text-align:right;'>Profesional:</td>
                <td style='font-weight:bold;'>
                    <?php echo htmlentities($medico['doc_rut'].' - '.$medico['doc_paterno'].' '.$medico['doc_materno'].' '.$medico['doc_nombres']); ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Especialidad:</td>
                <td style='font-weight:bold;'><?php echo htmlentities($esp['esp_desc']); ?></td>
            </tr>
	</table>
    </div>
    <table>
        <tr>
            <td valign='top'>
                <div id='calendario' name='calendario'>
                </div>
                <center>
                    <input type='button' id='replicar_agenda' name='replicar_agenda' style='display:none;' onClick='replicar_agenda();' value='Replicar este Agendamiento...'>
                </center>
            </td>
            <td>
                <div class='sub-content2' style='width:550px;height:600px;overflow:auto;'>
                    <table style='width:100%; height:300px;' id='no_fechas'>
                        <tr>
                            <td>
                                <center>Seleccione fecha para crear <br>cupos de atenci&oacute;n.</center>
                            </td>
                        </tr>
                    </table>
                    <form id='form_cupos' name='form_cupos' onClick=''>
                        <input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>'>
                        <input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id; ?>'>
                        <input type='hidden' id='fecha' name='fecha' value=''>
                        <input type='hidden' id='pos_hora' name='pos_hora' value=''>
                        <div id='fecha_horas' name='fecha_horas' style='width:100%; height:570px;overflow:auto;display:none;'>
                            <div class='sub-content'>
                                Definir Rango de Horas para el <span id='mostrar_fecha' style='font-weight:bold;font-size:14px;'></span>
                            </div>
                            <table style='width:100%;'>
                                <tr>
                                    <td style='text-align:right;'>Tipo de Atenci&oacute;n</td>
                                    <td colspan=4>
                                        <select id='select_nom_motivo' name='select_nom_motivo'>
                                            <?php echo $tipoatencionhtml;?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;'>Tipo de Contrato</td>
                                    <td colspan=4>
                                        <select id='select_nom_contrato' name='select_nom_contrato'>
                                            <option value="-1" SELECTED>Sin Asignar Tipo Contrato</option>
                                            <?php echo $tipocontratohtml;?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;'>Hora:</td>
                                    <td colspan=4>
                                        <select id='desde' name='desde'>
                                            <?php echo $horashtml; ?>
                                        </select>
                                        hasta
                                        <select id='hasta' name='hasta'>
                                            <?php echo $horashtml; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:center;font-weight:bold;font-size:11px;' colspan=5>Cupos de Atenci&oacute;n</td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;'>Cupos:</td>
                                    <td>
                                        <input type='text' id='cantn' name='cantn' size=5 style='text-align:center;' onKeyUp='calcular_total_cupos();'>
                                    </td>
                                    <td>
                                        <input type='checkbox' id='ficha' name='ficha' CHECKED /> Requiere Ficha
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;'>Sobrecupos:</td>
                                    <td>
                                        <input type='text' id='cantc' name='cantc' size=5 style='text-align:center;' onKeyUp='calcular_total_cupos();'>
                                    </td>
                                    <td>
                                        <input type='checkbox' id='adr' name='adr' CHECKED /> Imprime ADR 
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;'>Total:</td>
                                    <td>
                                        <input type='text' id='cantt' name='cantt' size=5 DISABLED style='text-align:center;'>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=5>
                                        <center>
                                            <input type='button' value='Agregar Rango...' onClick='agregar_rango();'>
                                        </center>
                                    </td>
                                </tr>
                            </table>
                            <div class='sub-content'>
                                Horas de Atenci&oacute;n durante el d&iacute;a <span id='mostrar_fecha2' style='font-weight:bold;font-size:14px;'></span>
                            </div>
                            <div class='sub-content2' style='height:300px;overflow:auto;' id='rango'>
                            </div>
                        </div>
                    </form>
                </div>
            </td>
            <td>
                <div class='sub-content2' style='width:550px;height:600px;overflow:auto;'>
                    <div class='sub-content'>
                        <img src='../../iconos/calendar.png'>
                        <b>Ausencias M&eacute;dicas Definidas</b>
                    </div>
                    <?php
                    if(!$ausencias)
                    {
                    ?>
                        <table><tr><td>No se han encontrado ausencias m&eacute;dicas</td></tr></table>
                    <?php
                    }
                    else
                    {
                    ?>
                    <table style="width:100%;">
                        <tr class="tabla_header">
                            <td>Fecha Inicio</td>
                            <td>Fecha T&eacute;rmino</td>
                            <td>Hora Inicio</td>
                            <td>Hora T&eacute;rmino</td>
                            <td>Motivo</td>
                        </tr>
                        <?php
                        for($i=0;$i<count($ausencias);$i++) 
                        {
                            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                            print('<tr class="'.$clase.'">');
                            if($ausencias[$i]['ausencia_fechafinal']!=null)
                            {
                                print('<td style="text-align:center;">'.$ausencias[$i]['ausencia_fechainicio'].'</td>');  
                                print('<td style="text-align:center;">'.$ausencias[$i]['ausencia_fechafinal'].'</td>');    
                            }
                            else
                            {
                                print('<td style="text-align:center;" colspan=2>'.$ausencias[$i]['ausencia_fechainicio'].'</td>');  
                            }
                            if($ausencias[$i]['hora_final']!=null) 
                            {
                                print('<td style="text-align:center;">'.$ausencias[$i]['hora_inicio'].'</td>');
                                print('<td style="text-align:center;">'.$ausencias[$i]['hora_final'].'</td>');
                            }
                            else
                            {
                                print('<td style="text-align:center;" valign="top" colspan=2>'.$ausencias[$i]['hora_inicio'].'</td>');
                            }
                            print('<td style="text-align:center;">'.$ausencias[$i]['motivo_desc'].'</td>');
                        }
                        ?>
                    </table>
                    <?php
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
