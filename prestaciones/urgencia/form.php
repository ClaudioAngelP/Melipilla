<?php
    require_once('../../conectar_db.php');
    $servs="'".str_replace(',','\',\'',_cav2(50))."'";
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
    length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
    centro_medica AND centro_ruta IN (".$servs.")
    ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 
?>
<script>
    presta=[];
    
    validar_hora=function(obj)
    {
        var val=trim(obj.value);
	var hr=val;
	if(val=='') {
            obj.style.background='';
            return true;
	}	
	
        if(!val.match(/^[0-9]{2}:*[0-9]{2}:*[0-9]{0,2}$/)) {
            obj.style.background='red';
            return false;														
	}		
	if(val.search(/\:/)==-1) {
            if(val.length==4) {
                hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3);
            }
            else if(val.length==6) { 
                hr=val.charAt(0)+val.charAt(1)+':'+val.charAt(2)+val.charAt(3)+':'+val.charAt(4)+val.charAt(5);
            }
            else {
                obj.style.background='red';
		return false;										
            }	
        } 
	var chk = hr.split(':');
	if((chk.length==2 || chk.length==3) && chk[0]*1>=0 && chk[0]*1<24) {
            for(var i=0;i<chk.length;i++) {
                if(chk[i].length!=2 || chk[i]*1<0 || (i>0 && chk[i]*1>=60)) {
                    obj.style.background='red';
                    return false;	
		}							
            }
            obj.style.background='';
            obj.value=hr;
            return true;
	}
        else {
            obj.style.background='red';
            return false;
	}							 
    }	 	

    agregar_prestacion = function() {
    	var codigo=$('cod_presta').value;
	var desc_presta=$('desc_presta').value;
	var cant=$('cantidad').value;
        if($('tipo').value!=5) {
            var num=presta.length;
            presta[num]=new Object();
            presta[num].codigo=codigo;
            presta[num].desc=desc_presta;
            presta[num].cantidad=cant;
            listar_prestaciones();
            $('cod_presta').select();
            $('cod_presta').focus();
            $('cambia_presta').value=1;
        }
        else {
            var myAjax=new Ajax.Request('prestaciones/urgencia/definir_tipo_prestacion.php',{
                method:'post',
		parameters:$('pac_id').serialize()+'&codigo='+encodeURIComponent(codigo),
		onComplete:function(r) {
                    var valor=r.responseText;
                    var num=presta.length;
                    presta[num]=new Object();
                    presta[num].codigo=codigo;
                    presta[num].desc=desc_presta;
                    presta[num].cantidad=cant;
                    presta[num].fappr_tipo=valor;
                    listar_prestaciones();
                    $('cod_presta').select();
                    $('cod_presta').focus();
                    $('cambia_presta').value=1;
		}
            });
        }
    }
    
    listar_prestaciones=function() {
        var html='<table style="width:100%;font-size:8px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Cant.</td><td>Descripci&oacute;n</td>';
	if($('tipo').value*1==5) 
            html+='<td>Tipo</td>';
        
        html+='<td>Eliminar</td></tr>';
        for(var i=0;i<presta.length;i++) {
            clase=(i%2==0)?'tabla_fila':'tabla_fila2';
            if(presta[i].desc.length>37)
                var descr=presta[i].desc.substr(0,37)+'...';
            else
                var descr=presta[i].desc;	
            html+='<tr class="'+clase+'" ';
            html+='onMouseOver="this.className=\'mouse_over\';" ';
            html+='onMouseOut="this.className=\''+clase+'\';">';
            html+='<td style="text-align:center;">'+presta[i].codigo+'</td><td style="text-align:center;">'+presta[i].cantidad+'</td><td>'+descr+'</td>';
            if($('tipo').value*1==5)
                html+='<td style="text-align:center;font-size:10px;font-weight:bold;">'+presta[i].fappr_tipo+'</td>';
            html+='<td><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="eliminar_prestacion('+i+');"></center></td></tr>';		
	}   
	html+='</table>' 
        $('lista_presta').innerHTML=html;
    }

    eliminar_prestacion = function(id) {
        presta=presta.without(presta[id]);
	listar_prestaciones();
        $('cambia_presta').value=1;
    }

    listar_fap = function() {
        try {
            $('listado_fap').style.height='280px';
            $('lista_fap').value='Actualizar Listado...'.unescapeHTML();
            $('buscador').style.display='';
            $('ver_regs').style.display='none';
            $('listado_fap').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader2.gif" /><br />Cargando...'
            var myAjax = new Ajax.Updater('listado_fap','prestaciones/urgencia/listar_fap.php',
            {
                method:'post',
                parameters:$('info_fap').serialize(),
                evalScripts: true
            });
        } catch(err) {
            alert(err);
        }
    }
   
    var cursor=0;
    
    boton_guardar=function() {
        if(guardar_fap())
            alert('Registro FAP guardado exitosamente.');	
    }     
    
    ver_siguiente=function() {
        if(guardar_fap()) {		
            cursor++;
            if(cursor>(datos_fap.length-1))
                cursor=0;
            else if(cursor<0)
                cursor=datos_fap.length()-1;
            abrir_fap(datos_fap[cursor].fap_id, cursor);
	}		
    }    

    ver_anterior=function() {
        if(guardar_fap()) {
            cursor--;		
            if(cursor>(datos_fap.length-1))
                cursor=0;
            else if(cursor<0)
                cursor=datos_fap.length-1;
		
            abrir_fap(datos_fap[cursor].fap_id, cursor);
	}		
    }    

    eliminar_fap=function() {
        var conf=confirm("&iquest;Desea eliminar el FAP seleccionado? - No hay opciones para deshacer.".unescapeHTML());
	if(!conf)
            return;
	var myAjax=new Ajax.Request('prestaciones/urgencia/sql_eliminar.php',
        {
            method:'post',
            parameters:'fap_id='+datos_fap[cursor].fap_id,
            onComplete: function() {
                alert('FAP eliminado exitosamente.');	
                datos_fap=datos_fap.without(datos_fap[cursor]);					
		if(datos_fap.length>0) {
                    if(cursor==datos_fap.length)
                        cursor--;
                    abrir_fap(datos_fap[cursor].fap_id, cursor);
                }
                else
                    listar_fap();		
            }						
	});
    }    
	
    guardar_fap=function() {
        var params='&'+$('tipo').serialize()+'&presta='+encodeURIComponent(presta.toJSON());
	if($('tipo').value*1==5) {
            for(var i=2;i<9;i++) {
                if(trim($('fap_pab_hora'+i).value)!='' && !validar_hora($('fap_pab_hora'+i))) {
                    alert('Horario flujo paciente contiene horas mal digitadas.');
                    return false;
		}	
            }				
            guardar_lista_equipos();
            params+='&equipo='+encodeURIComponent(Object.toJSON(datos_equipo));
            for(var i=1;i<4;i++)
                $('fap_diag_cod_'+i).disabled=false;
        }
        else {
            if(trim($('fap_hora_atencion').value)!='' && !validar_hora($('fap_hora_atencion'))) {
                alert('La hora de atenci&oacute;n es incorrecta.'.unescapeHTML());
		return false;
            }		
            if(trim($('fap_hora_alta').value)!='' && !validar_hora($('fap_hora_alta'))) {
                alert('La hora de alta es incorrecta.');
		return false;
            }		
            if(trim($('fap_hora_nsp').value)!='' && !validar_hora($('fap_hora_nsp'))) {
                alert('La hora de no presentaci&oacute;n (NSP) es incorrecta.'.unescapeHTML());
                return false;
            }		
            if($('fap_tipo_consulta').value*1==1 || $('fap_tipo_consulta').value*1==2 || $('fap_tipo_consulta').value*1==9) {
                if(presta.length==0) {
                    alert( 'Debe seleccionar por lo menos una prestaci&oacute;n otorgada.'.unescapeHTML() );
                    return false;	
		}
            }
            else {
                if(presta.length>0) {
                    alert( 'El tipo de FAP ingresado no debe registrar prestaciones.'.unescapeHTML() );
                    return false;	
		}
            }		
	}	
	var myAjax=new Ajax.Request('prestaciones/urgencia/sql.php',
        {
            method:'post',
            parameters:$('info_fap').serialize()+params,
            onComplete:function(r) {
            }	
	});	
        if($('tipo').value*1==5) {
            for(var i=1;i<4;i++)
                $('fap_diag_cod_'+i).disabled=true;
        }
	return true;	
    }
    
    abrir_fap=function(fap_id, i) {
        $('ver_regs').style.display='';
	$('ver_ubica').innerHTML='Registro <b>'+(i+1)+'</b> de <b>'+datos_fap.length+'</b>';
	var r=datos_fap[i];
	$('ver_paciente').innerHTML=r.pac_appat+' '+r.pac_apmat+' '+r.pac_nombres;
	$('fap_fechas').innerHTML=r.fap_fecha;
	$('nro_ficha').innerHTML=r.pac_ficha;
	cursor=i;
        var myAjax = new Ajax.Updater('listado_fap','prestaciones/urgencia/abrir_fap.php',
        {
            method:'post',
            parameters:'fap_id='+fap_id+'&'+$('tipo').serialize(),
            evalScripts:true,
            onComplete: function() {
                try {
                    $('listado_fap').scrollTop=0;
                    $('lista_fap').value='Volver Atr&aacute;s...'.unescapeHTML();
                    $('buscador').style.display='none';
                    $('listado_fap').style.height='360px';
                    crear_buscadores();
                }
                catch(err) {
                    alert(err);
                }			
            }
        });
    }
    
    seleccionar_diagnostico = function(d) {
        $('diag_cod').value=d[0];
        $('diagnostico').innerHTML=d[2];
    }

    seleccionar_centro = function(d) {
        $('centro_ruta').value=d[0];
        $('centro_nombre').value=d[2];
    }

    seleccionar_centro2 = function(d) {
        $('centro_ruta2').value=d[0];
        $('centro_nombre2').value=d[2];
    }

    seleccionar_diagnostico1 = function(datos_diag) {
        var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
        $('fap_diag_cod_1').value=cie10;
      	$('fap_diagnostico_1').value=datos_diag[2].unescapeHTML();
    }

    seleccionar_diagnostico2 = function(datos_diag) {
        var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
        $('fap_diag_cod_2').value=cie10;
        $('fap_diagnostico_2').value=datos_diag[2].unescapeHTML();
    }

    seleccionar_diagnostico3 = function(datos_diag) {
        var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
        $('fap_diag_cod_3').value=cie10;
      	$('fap_diagnostico_3').value=datos_diag[2].unescapeHTML();
    }
    
    seleccionar_paciente = function(d) {
        $('pac_rut').value=d[0];
	$('pac_nombre').innerHTML=d[2];
	$('pac_id').value=d[4];
	$('pac_ficha').innerHTML=d[3];
	$('prev_desc').innerHTML=d[6];
	$('pac_fc_nac').innerHTML=d[7];
	$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    
    }

    seleccionar_medico=function(datos_medico) {
        $('fap_doc_id').value=datos_medico[3];
        $('rut_medico').value=datos_medico[1];
    }
    
    lista_prestaciones=function() {
        if($('cod_presta').value.length<3)
            return false;
        var params='tipo=prestacion&'+$('cod_presta').serialize();
        /*
        if($('auge').checked) {
            params='tipo=prestacion_patologia&pat_id=';
            params+=getRadioVal('info_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
        }
        */
        return {
            method: 'get',
            parameters: params
        }
    }
    
    seleccionar_prestacion = function(presta) {
        //$('codigo_prestacion').value=presta[0];
        //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
        $('desc_presta').value=presta[2];
        $('cantidad').value='1';
        $('cantidad').select();
        $('cantidad').focus();
    }

    autocompletar_diagnostico='';
    autocompletar_medicos='';

    crear_buscadores=function() {
        if($('tipo').value*1!=5) {
            autocompletar_diagnostico = new AutoComplete(
            'diag_cod', 
            'autocompletar_sql.php',
            function() {
            if($('diag_cod').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diag_cod').value)
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico);
        
            autocompletar_medicos = new AutoComplete(
            'nombre_medico', 
            'autocompletar_sql.php',
            function() {
            if( $('nombre_medico').value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
            }
            }, 'autocomplete', 350, 200, 250, 1, 2, seleccionar_medico);
        }
        else {
            autocompletar_pacientes = new AutoComplete(
            'pac_rut', 
            'autocompletar_sql.php',
            function() {
            if($('pac_rut').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_paciente);

            autocompletar_diagnostico1 = new AutoComplete(
            'fap_diagnostico_1', 
            'autocompletar_sql.php',
            function() {
            if($('fap_diagnostico_1').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_1').value)
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico1);

            autocompletar_diagnostico2 = new AutoComplete(
            'fap_diagnostico_2', 
            'autocompletar_sql.php',
            function() {
            if($('fap_diagnostico_2').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_2').value)
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico2);

            autocompletar_diagnostico3 = new AutoComplete(
            'fap_diagnostico_3', 
            'autocompletar_sql.php',
            function() {
            if($('fap_diagnostico_3').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('fap_diagnostico_3').value)
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico3);
            
            autocompletar_centro = new AutoComplete(
            'centro_nombre', 
            'autocompletar_sql.php',
            function() {
            if($('centro_nombre').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre').value)
            }
            }, 'autocomplete', 250, 200, 150, 2, 3, seleccionar_centro);

            autocompletar_centro2 = new AutoComplete(
            'centro_nombre2', 
            'autocompletar_sql.php',
            function() {
            if($('centro_nombre2').value.length<2) return false;
            return {
            method: 'get',
            parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre2').value)
            }
            }, 'autocomplete', 250, 200, 150, 2, 3, seleccionar_centro2);
        }
   
        autocompletar_prestaciones = new AutoComplete(
        'cod_presta', 
        'autocompletar_sql.php',
        lista_prestaciones, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_prestacion);
    }

    actualizar_fecha=function() {
        top=Math.round(screen.height/2)-165;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('conectores/fap/hgf_descargar_fap.php?win=1&fecha='+encodeURIComponent($('fecha1').value),
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    datos_equipo=[];
    
    combo_equipo=function(v) {
        v*=1;
	var html='';
	html+='<option value="0" '+(v==0?'SELECTED':'')+'>Normal</option>';	
	html+='<option value="1" '+(v==1?'SELECTED':'')+'>Turno</option>';	
	html+='<option value="2" '+(v==2?'SELECTED':'')+'>Convenio</option>';	
	html+='<option value="3" '+(v==3?'SELECTED':'')+'>Privado</option>';
	html+='<option value="4" '+(v==4?'SELECTED':'')+'>Docencia</option>';	
	return html;	
    }
	
    guardar_lista_equipos=function() {
        var num=$('cant_equipos').value*1;
	for(var n=1;n<=num;n++) {
            datos_equipo[n]=[];
            if($(n+'_cir')==null) {
                for(var j=0;j<11;j++) {
                    datos_equipo[n][j]=new Object();
                    datos_equipo[n][j].id='';
                    datos_equipo[n][j].rut='';
                    datos_equipo[n][j].nombre='';						
                    datos_equipo[n][j].turno=false;						
		}
		continue;
            }
            datos_equipo[n][0]=new Object();
            datos_equipo[n][0].id=$(n+'_cir_id').value;
            datos_equipo[n][0].rut=$(n+'_cir').value;
            var obj2=$(n+'_cir').next();
            datos_equipo[n][0].nombre=obj2.innerHTML;
            datos_equipo[n][0].turno=$(n+'_cir_t').value*1;				
            datos_equipo[n][1]=new Object();
            datos_equipo[n][1].id=$(n+'_cir2_id').value;
            datos_equipo[n][1].rut=$(n+'_cir2').value;
            var obj2=$(n+'_cir2').next();
            datos_equipo[n][1].nombre=obj2.innerHTML;				
            datos_equipo[n][1].turno=$(n+'_cir2_t').value*1;				

            datos_equipo[n][2]=new Object();
            datos_equipo[n][2].id=$(n+'_cir3_id').value;
            datos_equipo[n][2].rut=$(n+'_cir3').value;
            var obj2=$(n+'_cir3').next();
            datos_equipo[n][2].nombre=obj2.innerHTML;				
            datos_equipo[n][2].turno=$(n+'_cir3_t').value*1;				

            datos_equipo[n][3]=new Object();
            datos_equipo[n][3].id=$(n+'_ane1_id').value;
            datos_equipo[n][3].rut=$(n+'_ane1').value;
            var obj2=$(n+'_ane1').next();
            datos_equipo[n][3].nombre=obj2.innerHTML;				
            datos_equipo[n][3].turno=$(n+'_ane1_t').value*1;				

            datos_equipo[n][4]=new Object();
            datos_equipo[n][4].id=$(n+'_ane2_id').value;
            datos_equipo[n][4].rut=$(n+'_ane2').value;
            var obj2=$(n+'_ane2').next();
            datos_equipo[n][4].nombre=obj2.innerHTML;				
            datos_equipo[n][4].turno=$(n+'_ane2_t').value*1;				

            datos_equipo[n][5]=new Object();
            datos_equipo[n][5].id=$(n+'_ins_id').value;
            datos_equipo[n][5].rut=$(n+'_ins').value;
            var obj2=$(n+'_ins').next();
            datos_equipo[n][5].nombre=obj2.innerHTML;				

            datos_equipo[n][6]=new Object();
            datos_equipo[n][6].id=$(n+'_pab_id').value;
            datos_equipo[n][6].rut=$(n+'_pab').value;
            var obj2=$(n+'_pab').next();
            datos_equipo[n][6].nombre=obj2.innerHTML;				

            datos_equipo[n][7]=new Object();
            datos_equipo[n][7].id=$(n+'_tec_ane_id').value;
            datos_equipo[n][7].rut=$(n+'_tec_ane').value;
            var obj2=$(n+'_tec_ane').next();
            datos_equipo[n][7].nombre=obj2.innerHTML;				

            datos_equipo[n][8]=new Object();
            datos_equipo[n][8].id=$(n+'_tec_perf_id').value;
            datos_equipo[n][8].rut=$(n+'_tec_perf').value;
            var obj2=$(n+'_tec_perf').next();
            datos_equipo[n][8].nombre=obj2.innerHTML;				

            datos_equipo[n][9]=new Object();
            datos_equipo[n][9].id=$(n+'_tec_rx_id').value;
            datos_equipo[n][9].rut=$(n+'_tec_rx').value;
            var obj2=$(n+'_tec_rx').next();
            datos_equipo[n][9].nombre=obj2.innerHTML;				

            datos_equipo[n][10]=new Object();
            datos_equipo[n][10].id=$(n+'_tec_recu_id').value;
            datos_equipo[n][10].rut=$(n+'_tec_recu').value;
            var obj2=$(n+'_tec_recu').next();
            datos_equipo[n][10].nombre=obj2.innerHTML;				
        }					
    }

    generar_lista_equipos = function(guarda) {
        var num=$('cant_equipos').value*1;
        var html="<table style='width:100%;font-size:11px;'>";
        if(typeof(guarda)!='boolean')
            guardar_lista_equipos();
	
        for(var n=1;n<=num;n++) {
            html+='<tr><td class="sub-content" colspan=2><center><u>Equipo <b>'+n+'</b></u></center></td><td class="sub-content"><center>Tipo</center></td></tr>';
            html+="<tr><td class='tabla_fila2' style='text-align:right;width:25%;'>";
            html+="1er. Cirujano:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_cir_id' name='"+n+"_cir_id' size=10 value='"+datos_equipo[n][0].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"cir\");' id='"+n+"_cir' name='"+n+"_cir' size=10 value='"+datos_equipo[n][0].rut+"' onFocus='$(\"listado_fap\").scrollTop=618+(320*"+(n-1)+");' /><span>"+datos_equipo[n][0].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="<select style='font-size:8px;' id='"+n+"_cir_t' name='"+n+"_cir_t'>"+combo_equipo(datos_equipo[n][0].turno)+"</select>";		
            html+="</center></td></tr>";
            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="2o. Cirujano:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_cir2_id' name='"+n+"_cir2_id' size=10 value='"+datos_equipo[n][1].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"cir2\");' id='"+n+"_cir2' name='"+n+"_cir2' size=10 value='"+datos_equipo[n][1].rut+"' /><span>"+datos_equipo[n][1].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="<select style='font-size:8px;' id='"+n+"_cir2_t' name='"+n+"_cir2_t'>"+combo_equipo(datos_equipo[n][1].turno)+"</select>";		
            html+="</center></td></tr>";
            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="3er. Cirujano:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_cir3_id' name='"+n+"_cir3_id' size=10 value='"+datos_equipo[n][2].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"cir3\");' id='"+n+"_cir3' name='"+n+"_cir3' size=10 value='"+datos_equipo[n][2].rut+"' /><span>"+datos_equipo[n][2].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="<select style='font-size:8px;' id='"+n+"_cir3_t' name='"+n+"_cir3_t'>"+combo_equipo(datos_equipo[n][2].turno)+"</select>";		
            html+="</center></td></tr>";
            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="Anestesi&oacute;logo 1:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_ane1_id' name='"+n+"_ane1_id' size=10 value='"+datos_equipo[n][3].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"ane1\");' id='"+n+"_ane1' name='"+n+"_ane1' size=10  value='"+datos_equipo[n][3].rut+"' /><span>"+datos_equipo[n][3].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="<select style='font-size:8px;' id='"+n+"_ane1_t' name='"+n+"_ane1_t'>"+combo_equipo(datos_equipo[n][3].turno)+"</select>";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="Anestesi&oacute;logo 2:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_ane2_id' name='"+n+"_ane2_id' size=10 value='"+datos_equipo[n][4].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"ane2\");' id='"+n+"_ane2' name='"+n+"_ane2' size=10  value='"+datos_equipo[n][4].rut+"' /><span>"+datos_equipo[n][4].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="<select style='font-size:8px;' id='"+n+"_ane2_t' name='"+n+"_ane2_t'>"+combo_equipo(datos_equipo[n][4].turno)+"</select>";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="Instrumentista:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_ins_id' name='"+n+"_ins_id' size=10 value='"+datos_equipo[n][5].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"ins\");' id='"+n+"_ins' name='"+n+"_ins' size=10 value='"+datos_equipo[n][5].rut+"' /><span>"+datos_equipo[n][5].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";
            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="Pabellonera:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_pab_id' name='"+n+"_pab_id' size=10 value='"+datos_equipo[n][6].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"pab\");' id='"+n+"_pab' name='"+n+"_pab' size=10 value='"+datos_equipo[n][6].rut+"' /><span>"+datos_equipo[n][6].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="T&eacute;c. Anestesista:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_tec_ane_id' name='"+n+"_tec_ane_id' size=10 value='"+datos_equipo[n][7].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"tec_ane\");' id='"+n+"_tec_ane' name='"+n+"_tec_ane' size=10  value='"+datos_equipo[n][7].rut+"' /><span>"+datos_equipo[n][7].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="T&eacute;cnico Perf.:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_tec_perf_id' name='"+n+"_tec_perf_id' size=10 value='"+datos_equipo[n][8].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"perf\");' id='"+n+"_tec_perf' name='"+n+"_tec_perf' size=10  value='"+datos_equipo[n][8].rut+"' /><span>"+datos_equipo[n][8].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="T&eacute;cnico Rayos:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_tec_rx_id' name='"+n+"_tec_rx_id' size=10 value='"+datos_equipo[n][9].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"tec_rx\");' id='"+n+"_tec_rx' name='"+n+"_tec_rx' size=10  value='"+datos_equipo[n][9].rut+"' /><span>"+datos_equipo[n][9].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";

            html+="<tr><td class='tabla_fila2' style='text-align:right;'>";
            html+="T&eacute;cnico Recup.:";
            html+="</td><td class='tabla_fila'>";
            html+="<input type='hidden' id='"+n+"_tec_recu_id' name='"+n+"_tec_recu_id' size=10 value='"+datos_equipo[n][10].id+"' />";
            html+="<input type='text' onDblClick='quitar_pp("+n+", \"tec_recu\");' id='"+n+"_tec_recu' name='"+n+"_tec_recu' size=10  value='"+datos_equipo[n][10].rut+"' /><span>"+datos_equipo[n][10].nombre+"</span>";
            html+="</td><td style='width:20px;' class='tabla_fila2'><center>";
            html+="&nbsp;";		
            html+="</center></td></tr>";
        }

	html+='</table>';
	$('lista_equipos').innerHTML=html;
	autocompletar_medicos_cir=[];
	autocompletar_medicos_cir2=[];
	autocompletar_medicos_cir3=[];

        autocompletar_medicos_ane1=[];
	autocompletar_medicos_ane2=[];

	autocompletar_medicos_ins=[];
	autocompletar_medicos_pab=[];
	autocompletar_medicos_tec_ane=[];
	autocompletar_medicos_tec_perf=[];
	autocompletar_medicos_tec_rx=[];
	autocompletar_medicos_tec_recu=[];
        
        for(var n=1;n<=num;n++) {
            autocompletar_medicos_cir[n] = new AutoComplete(
            n+'_cir', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=0&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];	
            });
     
            autocompletar_medicos_cir2[n] = new AutoComplete(
            n+'_cir2', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=0&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });

            autocompletar_medicos_cir3[n] = new AutoComplete(
            n+'_cir3', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=0&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });
            
            autocompletar_medicos_ane1[n] = new AutoComplete(
            n+'_ane1', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=1&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });
            
            autocompletar_medicos_ane2[n] = new AutoComplete(
            n+'_ane2', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=1&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });
            
            autocompletar_medicos_ins[n] = new AutoComplete(
            n+'_ins', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];	
            });

            autocompletar_medicos_pab[n] = new AutoComplete(
            n+'_pab', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];	
            });
            
            autocompletar_medicos_tec_ane[n] = new AutoComplete(
            n+'_tec_ane', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });

            autocompletar_medicos_tec_perf[n] = new AutoComplete(
            n+'_tec_perf', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];
            });

            autocompletar_medicos_tec_rx[n] = new AutoComplete(
            n+'_tec_rx', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];	
            });

            autocompletar_medicos_tec_recu[n] = new AutoComplete(
            n+'_tec_recu', 
            'autocompletar_sql.php',
            function() {
            if( $(this.object).value.length < 3 ) return false;
            return {
            method: 'get',
            parameters: 'tipo=personal_pabellon&tpers=2&cadena='+encodeURIComponent($(this.object).value)
            }
            }, 'autocomplete', 400, 200, 250, 1, 5, function(d) {
                $(this.object).value=d[1];	
                var obj2 = $(this.object).next();
                var obj3 = $(this.object).previous();
                obj2.innerHTML=d[2]+' '+d[3]+' '+d[4];	
                obj3.value=d[0];	
            });
        }		
    }

    quitar_pp=function(idx, nom) {
        $(idx+'_'+nom+'_id').value='';
	$(idx+'_'+nom).value='';
	var obj2=$(idx+'_'+nom).next();
	obj2.innerHTML='';			
    }

    generar_fap = function() {
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        new_win = 
        window.open('prestaciones/urgencia/generar_fap.php?'+($('tipo').serialize()),
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=750, height=400, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    
    generar_fap_urgencia=function() {
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        win_ingreso_urgencia = 
        window.open('prestaciones/urgencia/generar_fap.php?urgencia=1&'+($('tipo').serialize()),
        'win_ingreso_urgencia', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=750, height=400, '+
        'top='+top+', left='+left);
        win_ingreso_urgencia.focus();
    }
    
    reabrir_fap = function(fap_id) {
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        new_win = 
        window.open('prestaciones/urgencia/generar_fap.php?'+($('tipo').serialize())+'&fap_id='+fap_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=750, height=400, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    cargo_fap = function(fap_id) {
        top=Math.round(screen.height/2)-165;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('prestaciones/urgencia/hoja_cargo_fap.php?fap_id='+fap_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=730, height=630, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    
   

    imprimir_fap = function(fap_id) {
        top=Math.round(screen.height/2)-165;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('prestaciones/urgencia/imprimir_fap.php?fap_id='+fap_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
	
    imprimir_lista_fap=function() {
        top=Math.round(screen.height/2)-165;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('prestaciones/urgencia/listado_faps.php?'+$('tipo').serialize()+
        '&fecha='+encodeURIComponent($('fecha1').value),
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
</script>
<center>
    <div class='sub-content' style='width:950px;'>
        <form id='info_fap' onSubmit='return false;'>
            <div class='sub-content'>
                <img src='iconos/table_edit.png'> <b>Registros Urgencia (DAU)</b>
            </div>
            <div class='sub-content' id='buscador'>
                <table style='width:100%;'>
                    <tr>
                        <td style='width:100px;text-align:right;'>Tipo:</td>
                        <td>
                            <select id='tipo' name='tipo' <?php if(_cax(209)) { ?> onChange='
                                /*
                                if(this.value*1==5) {
                                    $("genera_fap").style.display="";
                                } else {
                                    $("genera_fap").style.display="none";		
                                }
                                */
                                '
                            <?php } ?>
                            >
                                <?php if(_cax(207)) { ?> <option value='1'>Infantil</option> <?php } ?>
                                <?php if(_cax(206)) { ?> <option value='2'>Maternal</option> <?php } ?>
                                <?php if(_cax(205)) { ?> <option value='3'>Adulto</option> <?php } ?>
                                <!--<?php if(_cax(208) OR _cax(209)) { ?> <option value='5'>Pabell&oacute;n</option> <?php } ?>-->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='width:100px;text-align:right;'>Fecha:</td>
                        <td>
                            <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='listar_fap();'>
                            <img src='iconos/date_magnify.png' id='fecha1_boton'>
                            <!--<input type='button' value='Actualizar Fecha...'  onClick='actualizar_fecha();' style='font-size:10px;' />-->
                        </td>
                        <td id='ver_refs' style='text-align:center;'>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Filtrar Lista:</td>
                        <td>
                            <input type='text' size=50 id='filtro' name='filtro' value='' />
                        </td>
                    </tr>
                    <tr>
                        <td colspan=3>
                            <center>
                                <input type='button' id='genera_fap_urgencia' onClick='generar_fap_urgencia();' value='Ingreso Paciente...'>
                                <!--
                                <?php if(_cax(209)) { ?>
                                    <input type='button' id='genera_fap' style='display:block;' onClick='generar_fap();' value='Generar FAP Nuevo...'>
                                <?php } ?>
                                -->
                                <input type='button' id='lista_fap' onClick='listar_fap();' value='Actualizar Listado...'>
                                <input type='button' id='imprime_fap' onClick='imprimir_lista_fap();' value='Imprimir Listado...'>
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content' id='ver_regs' style='display:none;'>
                <table style='width:100%;'>
                    <tr>
                        <td id='ver_ubica' style='width:130px;text-align:center;'>
                        </td>
                        <td style='text-align:right;'>Fecha FAP:</td>
                        <td id='fap_fechas' style='text-align:center;font-weight:bold;font-size:9px;width:60px;'></td>
                        <td style='text-align:right;'>Ficha Nro.:</td>
                        <td id='nro_ficha' style='text-align:center;font-weight:bold;width:60px;'></td>
                        <td style='text-align:right;'>Nombre:</td>
                        <td id='ver_paciente' style='width:350px;font-weight:bold;'></td>
                    </tr>
                </table>
            </div>
            <div class='sub-content2' style='height:280px;overflow:auto;' id='listado_fap'>
            </div>
        </form>
    </div>
</center>
<script>
    Calendar.setup({
    inputField     :    'fecha1',         // id of the input field
    ifFormat       :    '%d/%m/%Y',       // format of the input field
    showsTime      :    false,
    button          :   'fecha1_boton'
    });

    listar_fap();
    
    /*
    if($('tipo').value*1==5) {
        $("genera_fap").style.display="";
    } else {
        $("genera_fap").style.display="none";		
    }
    */
</script>
