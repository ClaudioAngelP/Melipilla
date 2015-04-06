<?php
    require_once('../../conectar_db.php');
    $servs="'".str_replace(',','\',\'',_cav2(311))."'";
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
    length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
    centro_medica AND centro_ruta IN (".$servs.")
    ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");
    $func_id=$_SESSION['sgh_usuario_id']*1;
    $esp_permiso=_cav(311);
    
    
    
    ///if($esp_permiso!='')
    //{
        $disabled="";
        $style_display="display:block;";
        $display="";
        ///$especialidades = desplegar_opciones("especialidades", "esp_id, esp_desc",'1','esp_id IN ('._cav(311).')', 'ORDER BY esp_desc');
    
        
    //}
    //else
    //{
        //$disabled=" Disabled ";
        //$style_display="display:none;";
        //$display="none";
        
        $especialidades=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
        //print("No presenta Permisos en especialidades Para Ingresar al modulo de Agendas");
        //exit();
    //}
    
	
    $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
    
    $medicohtml="";
    
?>
<script>
    
    var calendario;
    var fechas_ocupadas = "";
    var fechas_ausente = "";
    
    dnomina='';
    lnomina='';
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    
    
    
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    listar_nominas = function(index)
    {
        $j('#listado_nominas').html('<center><table><tr><td><img src=imagenes/loading_small.gif></td></tr></table></center>');
        $('lista_nominas').value='Actualizar Listado...';
        $('guardar_registros').style.display='none';
	$('copiar_registros').style.display='<?php echo $display;?>';
	$('buscar_nominas').style.display='';
	$('datos_nomina').style.display='none';
        $('listado_nominas').style.height='380px';
        $('folio_nomina').disabled=false;
        $('agregar_pacientes').style.display='none';
        $('select_nominas').innerHTML='';
        $('select_nominas').style.display='none';
        //$('crear_nominas').style.display='';
        $('eliminar_nominas').style.display='none';
        if(index==1)
        {
            var fecha1=$('fecha1').value;
            dias=-1;
            f=fecha1.split('/');
            f=f[1]+'/'+f[0]+'/'+f[2];
            hoy=new Date(f);
            hoy.setTime(hoy.getTime()+dias*24*60*60*1000);
            mes=hoy.getMonth()+1;
            hoy_x=hoy.getDate()
            if(hoy_x<=9) hoy_x='0'+hoy_x;
            if(mes<=9) mes='0'+mes;
            fecha_x=hoy_x+'/'+mes+'/'+hoy.getFullYear();
            fecha1=fecha_x;   
            $('fecha1').value=fecha1;
        }
        if(index==2)
        {
            
            var fecha1=$('fecha1').value;
            dias=1;
            f=fecha1.split('/');
            f=f[1]+'/'+f[0]+'/'+f[2];
            hoy=new Date(f);
            hoy.setTime(hoy.getTime()+dias*24*60*60*1000);
            mes=hoy.getMonth()+1;
            hoy_x=hoy.getDate()
            if(hoy_x<=9) hoy_x='0'+hoy_x;
            if(mes<=9) mes='0'+mes;
            fecha_x=hoy_x+'/'+mes+'/'+hoy.getFullYear();
            fecha1=fecha_x;
            $('fecha1').value=fecha1;
        }
        
        
        var myAjax = new Ajax.Updater('listado_nominas','prestaciones/ingreso_nominas/listar_nominas.php',
        {
            method:'post',
            evalScripts:true,
            parameters:$('info_nominas').serialize()
        });
        
        if($('esp_id').value=='-1')
        {
            return;
        }
        if($('doc_id').value=='-1')
        {
            return;
        }
        $j.ajax({
            url: 'prestaciones/ingreso_nominas/ausencias_ocupados.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {doc_id:encodeURIComponent($('doc_id').value*1),esp_id:encodeURIComponent($('esp_id').value*1)},
            success: function(data)
            {
                    fechas_ocupadas = data[0];
                    fechas_ausente = data[1];
            }
        });
    }


    abrir_nomina=function(nom_id, tipo)
    {
        if(tipo==0)
            var params='nom_id='+nom_id;
	else
            var params='nom_folio='+nom_id;
	
        if($('folios_nominas')!=null)	
            params+='&'+$('folios_nominas').serialize();
			
	params+='&'+$('orden').serialize();
		
	var myAjax = new Ajax.Updater('listado_nominas','prestaciones/ingreso_nominas/abrir_nomina.php',
        {
            method:'post',
            parameters:params,
            evalScripts:true,
            asynchronous: false,
            onComplete: function(resp)
            {
                try {
                    if(resp.responseText=='')
                    {
                        alert('N&oacute;mina no encontrada.'.unescapeHTML());
			return;	
                    }

                    $('folio_nomina').disabled=true;
                    $('buscar_nominas').style.display='none';
                    $('datos_nomina').style.display='';
                    $('listado_nominas').style.height='380px';
                    $('agregar_pacientes').style.display='<?php echo $display;?>';
                    $('lista_nominas').value='Volver Atr&aacute;s...'.unescapeHTML();
                    
                    $('guardar_registros').style.display='none';                    
                    //$('copiar_registros').style.display='';
                    $('listado_nominas').scrollTop=0;
                    //$('crear_nominas').style.display='none';
                }
                catch(err)
                {
                    alert(err);
                }
            }
        });
    }
    
    function compare_dates(fecha, fecha2)
    {
        var xMonth=fecha.substring(3, 5);
        var xDay=fecha.substring(0, 2);
        var xYear=fecha.substring(6,10);
        var yMonth=fecha2.substring(3, 5);
        var yDay=fecha2.substring(0, 2);
        var yYear=fecha2.substring(6,10);
        if (xYear> yYear)
        {
            return(true)
        }
        else
        {
          if (xYear == yYear)
          { 
            if (xMonth> yMonth)
            {
                return(true)
            }
            else
            { 
              if (xMonth == yMonth)
              {
                if (xDay >= yDay)
                  return(true);
                else
                  return(false);
              }
              else
                return(false);
            }
          }
          else
            return(false);
        }
    }

    




    calcular_totales=function(index)
    {
        //return;
        var num_ausente=0;
        var num_presente=0;
        var num_nuevo=0;
        var num_control=0;	
        var num_extra=0;
        var num_masc=0;
        var num_feme=0;
        var num_altas=0;
        var geta=[0,0,0,0,0,0];
        var getn=['< 10','10-14','15-19','20-24','25-64','> 65'];
        
        
        <?php if($func_id!=7){?>
            if (!compare_dates($('fecha1').value, $('fecha_actual').value))
            {
                alert("No se puede modificar Nominas anteriores a la fecha actual!");
                abrir_nomina($("folio_nomina").value, 1);
                return;
            }
        <?php } ?>

        
        
        
        //for(var i=0;i<dnomina.length;i++)
        //{
            r=dnomina[index];		
            var nomd_id=r.nomd_id;
            //console.log('nomd_id='+nomd_id);
            if(r.pac_id==0)
                return;
                
            
            if($('nomd_diag_cod_'+nomd_id).value=='N' || $('nomd_diag_cod_'+nomd_id).value=='T')
            {

                $('motivo_'+nomd_id).disabled=false;
                if($('nomd_diag_cod_'+nomd_id).value=='N' && $('nomd_codigo_no_atiende_'+nomd_id).value=='')
                {
                    $('nomd_codigo_susp_'+nomd_id).value='';
                    sel_motivo(nomd_id,index);
                    return;
                }
                if($('nomd_diag_cod_'+nomd_id).value=='T' && $('nomd_codigo_susp_'+nomd_id).value=='')
                {
                    $('nomd_codigo_no_atiende_'+nomd_id).value='';
                    sel_motivo(nomd_id,index);
                    return;
                }
            }
            else
            {
                $('motivo_'+nomd_id).disabled=true;
                $('nomd_codigo_susp_'+nomd_id).value='';
                $('nomd_codigo_no_atiende_'+nomd_id).value='';
            }
            if($('nomd_origen_'+nomd_id).value=='A')
            {
                $('origen_'+nomd_id).disabled=false;
                if($('nomd_institucion_'+nomd_id).value=='' || $('nomd_institucion_'+nomd_id).value=='0')
                    sel_origen(nomd_id,index);
                
            }
            else
            {
                $('origen_'+nomd_id).disabled=true;
                $('nomd_institucion_'+nomd_id).value='';
            }
            
            var myAjax=new Ajax.Request('prestaciones/ingreso_nominas/sql_ligero.php', {method:'post',asynchronous:false,parameters:$('info_nominas').serialize()+'&nomd_id='+nomd_id+'&index='+index});
            //console.log('ok!');
        //}
        
            
        var posScrollTop=$j('#div_pos').val();
        $j('#listado_nominas').html('<center><table><tr><td><img src=imagenes/loading_small.gif></td></tr></table></center>');
        abrir_nomina($("folio_nomina").value, 1);
        
        $j('#listado_nominas').scrollTop(posScrollTop);
        //abrir_nomina(\"".$lista[$i]['nom_folio']."\", 1);'>")
        return;
        /*
        for(var i=0;i<dnomina.length;i++)
        {
            if($('proc')==null)
            {
                var val=$('nomd_diag_cod_'+r.nomd_id).value;
                if(val=='NSP')
                    num_ausente++;
		else
                    num_presente++;	
            }
            else
            {
                var val=$('nomd_diag_cod_'+r.nomd_id).checked;
                if(!val)
                {
                    num_ausente++;
                    val='NSP';
                }
                else
                {
                    num_presente++;
                    val='';
                }	
            }	
            if(val!='NSP')
            {
                var val=$('nomd_tipo_'+r.nomd_id).value;
		
                if(val=='N')
                    num_nuevo++;
		else
                    num_control++;

		var val=$('nomd_extra_'+r.nomd_id).value;
                
                if(val=='S')
                    num_extra++;
		
                if($('proc')==null)
                {
                    var val=$('nomd_destino_'+r.nomd_id).value*1;
                    if(val==6 || val==9)
                        num_altas++;
                }
                
                var val=$('nomd_edad_'+r.nomd_id).innerHTML*1;
                if(val<10) { geta[0]++; }
                if(val>=10 && val<=14) { geta[1]++; }
                if(val>=15 && val<=19) { geta[2]++; }
                if(val>=20 && val<=24) { geta[3]++; }
                if(val>=25 && val<=64) { geta[4]++; }
                if(val>=65) { geta[5]++; }
                var val=$('nomd_sexo_'+r.nomd_id).innerHTML;
                if(val=='M')
                    num_masc++;
		else
                    num_feme++;
            }
        }
        if(dnomina.length>0 && num_presente>0)
        {
            var factor=100/dnomina.length;
            var factor2=100/num_presente;
        }
        else
        {
            var factor=0;
            var factor2=0;
        }
	var html='<table style="width:100%;font-size:8px;"><tr><td>';
        html+='<table style="width:100%;font-size:8px;" cellpadding=0 cellspacing=0><tr class="tabla_header"><td colspan=3>Indicadores de la N&oacute;mina</td></tr>';
	html+='<tr class="tabla_fila"><td style="text-align:right;width:40%;">Asisten:</td><td style="font-weight:bold;text-align:center;width:20%;">'+num_presente+'</td><td style="text-align:center;">'+number_format(num_presente*factor,2,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila2"><td style="text-align:right;">Ausentes:</td><td style="font-weight:bold;text-align:center;">'+num_ausente+'</td><td style="text-align:center;">'+number_format(num_ausente*factor,2,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila"><td style="text-align:right;">Pac. Nuevos:</td><td style="font-weight:bold;text-align:center;">'+num_nuevo+'</td><td style="text-align:center;">'+number_format(num_nuevo*factor2,2,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila2"><td style="text-align:right;">Pac. Control:</td><td style="font-weight:bold;text-align:center;">'+num_control+'</td><td style="text-align:center;">'+number_format(num_control*factor2,2,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila"><td style="text-align:right;">Cant. Extras:</td><td style="font-weight:bold;text-align:center;">'+num_extra+'</td><td style="text-align:center;">'+number_format(num_extra*factor,2,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila2"><td style="text-align:right;">Masc./Fem.:</td><td style="font-weight:bold;text-align:center;">'+num_masc+'/'+num_feme+'</td><td style="text-align:center;">'+number_format(num_masc*factor2,0,',','.')+'%/'+number_format(num_feme*factor2,0,',','.')+'%</td></tr>';	
	html+='<tr class="tabla_fila"><td style="text-align:right;">Altas:</td><td style="font-weight:bold;text-align:center;">'+num_altas+'</td><td style="text-align:center;">'+number_format(num_altas*factor2,2,',','.')+'%</td></tr>';
	html+='</table>';
	html+='</td><td>';
        html+='<table style="width:100%;">';
	html+='<table style="width:100%;" cellpadding=0 cellspacing=0><tr class="tabla_header"><td colspan=3>Grupos Et&aacute;reos</td></tr>';
	for(var j=0;j<getn.length;j++)
        {
            var clase=(j%2==0)?'tabla_fila':'tabla_fila2';		
            html+='<tr class="'+clase+'"><td style="text-align:right;width:40%;">'+getn[j]+':</td><td style="font-weight:bold;text-align:center;width:20%;">'+geta[j]+'</td><td style="text-align:center;">'+number_format(geta[j]*factor2,2,',','.')+'%</td></tr>';
	}	
	html+='</table>'
	html+='</td></tr></table>';
	$('indicadores').innerHTML=html;
        */
    }
	
    guardar_registros=function()
    {
        var myAjax = new Ajax.Request('prestaciones/ingreso_nominas/sql.php',
        {
            method:'post',
            parameters: $('info_nominas').serialize(),
            onComplete:function()
            {
                alert('Registro guardado exitosamente.');
                limpiar_paciente();
		listar_nominas(0);
            }	
	});	
    }
	
    cargar_diagnostico=function(nomd_id)
    {
        var val=trim($('nomd_diag_cod_'+nomd_id).value);
        $('nomd_diag_cod_'+nomd_id).value=val;
	if(val=='NSP')
        {
            $('nomd_diag_'+nomd_id).value='';
            //calcular_totales();
            return;
	}	
	$('nomd_diag_'+nomd_id).value='(Cargando...)';	
	var myAjax=new Ajax.Request('prestaciones/ingreso_nominas/diagnosticos.php',
        {
            method:'post',
            parameters:'diag_cod='+encodeURIComponent($('nomd_diag_cod_'+nomd_id).value),
            onComplete:function(resp)
            {
                $('nomd_diag_'+nomd_id).value=resp.responseText;
		//calcular_totales();
            }	
	});	
    }
	
    limpiar_paciente=function()
    {
        $('pac_rut').value='';
	$('paciente').value='';
	$('pac_id').value='0';
        $('txt_paciente').value='';
    }
	
    agregar_paciente=function()
    {
        <?php if($func_id!=7){?>
        if (!compare_dates($('fecha1').value, $('fecha_actual').value))
        {
            alert("No se puede modificar Nominas anteriores a la fecha actual!");
            abrir_nomina($("folio_nomina").value, 1);
            limpiar_paciente();
            return;
        }
        <?php } ?>
        if($('paciente_tipo_id').value==0)
        {
            if($('pac_rut').value=="")
            {
                alert("No se ha Ingresado Paciente Para Asignar Hora de Atencion");
                return;
            }
            if(!comprobar_rut($('pac_rut').value))
            {
                alert("El Rut Ingresado No es VALIDO");
                $('pac_rut').select();
                $('pac_rut').focus();
                return;
            }
        }
        else
        {
            if($('txt_paciente').value=="")
            {
                alert("No se ha ingresa dato para realizar busqueda del paciente");
                return;
            }
        }
        top=Math.round(screen.height/2)-250;
	left=Math.round(screen.width/2)-325;
        new_win = window.open('prestaciones/ingreso_nominas/form_paciente.php?'+$('nom_id').serialize()+'&'+$('pac_id').serialize()+'&'+$('nomd_hora').serialize()+'&'+$('nomd_hora_extra').serialize()+'&'+$('duracion').serialize()+'&'+$('pac_rut').serialize()+'&'+$('txt_paciente').serialize()+'&'+$('paciente_tipo_id').serialize(),
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=650, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();					
	return;
        // Guardar Cupo tomado (ahora es en el formulario de pacientes!!!)...
        /*
	var myAjax=new Ajax.Request('prestaciones/ingreso_nominas/sql_tomar_cupo.php',
        {
            method:'post',
            parameters:$('nom_id').serialize()+'&'+$('pac_id').serialize()+'&'+$('nomd_hora').serialize(),
            onComplete:function(resp2)
            {
                imprimir_citacion(resp2.responseText*1);
		//$('paciente').disabled=false;	
		abrir_nomina($("folio_nomina").value, 1);
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value='0';
            }	
        });
        */
        
    }
	
    eliminar=function(nomd_id)
    {
        var conf=confirm( "&iquest;Desea eliminar el registro de la n&oacute;mina?".unescapeHTML() );
	if(!conf)
            return;
        var myAjax=new Ajax.Request('prestaciones/ingreso_nominas/sql_eliminar_cupo.php',
        {
            method:'post',
            parameters:'nomd_id='+nomd_id,
            onComplete:function()
            {
                abrir_nomina($("nom_id").value*1, 0);														
            }	
	});
    }

    buscar_cupos=function()
    {
        top=Math.round(screen.height/2)-275;
        left=Math.round(screen.width/2)-400;
        new_win = window.open('prestaciones/ingreso_nominas/form_buscar.php',
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=800, height=550, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
	
    solicitar_ficha=function()
    {
        top=Math.round(screen.height/2)-275;
        left=Math.round(screen.width/2)-400;
        new_win = window.open('prestaciones/archivo_fichas/solicitar_ficha.php',
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=800, height=250, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    registrar=function(nomd_id)
    {
        <?php if($func_id!=7){?>
            if (!compare_dates($('fecha1').value, $('fecha_actual').value))
            {
                alert("No se puede modificar Nominas anteriores a la fecha actual!");
                abrir_nomina($("folio_nomina").value, 1);
                return;
            }
        <?php } ?>
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('prestaciones/ingreso_nominas/form_proc.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    informe=function(nomd_id)
    {
        top=Math.round(screen.height/2)-325;
        left=Math.round(screen.width/2)-375;
        new_win = window.open('prestaciones/ingreso_nominas/form_informe.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=850, height=650, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    imprimir_listado = function()
    {
        _general = $('datos_nomina').innerHTML;
        _detalle = $('listado_nominas').innerHTML;
        _separador2 = '<hr><h3>Detalle de N&oacute;mina</h3></hr>';
        imprimirHTML(_general+_separador2+_detalle);	
    }
	
    copiar_nomina=function()
    {
        if($j('#nom_id').length)
        {
            top=Math.round(screen.height/2)-165;
            left=Math.round(screen.width/2)-340;
            new_win = window.open('prestaciones/ingreso_nominas/form_copiar.php?nom_id='+$('nom_id').value*1,
            'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
            'top='+top+', left='+left);
            new_win.focus();
        }
        else
        {
            alert("Debe Seleccionar una nomina primero para poder copiar nomina");
            return;
        }
    }

    eliminar_nomina=function()
    {
        var conf=confirm( "&iquest;Desea eliminar la n&oacute;mina? -- No hay opciones para deshacer.".unescapeHTML() );
	if(!conf)
            return;
	
        var myAjax=new Ajax.Request('prestaciones/ingreso_nominas/sql_eliminar.php',
        {
            method:'post',
            parameters:$('nom_id').serialize(),
            onComplete:function(r)
            {
                alert('N&oacute;mina eliminada exitosamente.'.unescapeHTML());
                listar_nominas(0);
            }						
        });
    }

    crear_nomina=function()
    {
        top=Math.round(screen.height/2)-165;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/form_nomina.php',
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=330, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    imprimir_citacion=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/citaciones.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    imprimir_citacion2=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/citaciones2.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    gestiones_citacion=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
        
    verificar_cupo=function(pos,hora)
    {
        $j('select#nomd_hora option').removeAttr("selected");
        $j('#nomd_hora option[value="'+hora+'"]').attr("selected",true);
        $('pac_rut').select();
        $('pac_rut').focus();
    }

    sel_motivo=function(nomd_id,index)
    {
        <?php if($func_id!=7){?>
            if (!compare_dates($('fecha1').value, $('fecha_actual').value))
            {
                alert("No se puede modificar Nominas anteriores a la fecha actual!");
                abrir_nomina($("folio_nomina").value, 1);
                return;
            }
        <?php } ?>
        
        
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        var op=$('nomd_diag_cod_'+nomd_id).value;
        var valor;
        if(op=='N'){ valor=encodeURIComponent($('nomd_codigo_no_atiende_'+nomd_id).value); tipo=1; }
	if(op=='T'){ valor=encodeURIComponent($('nomd_codigo_susp_'+nomd_id).value); tipo=0; }
        new_win = window.open('prestaciones/ingreso_nominas/seleccionar_opcion.php?tipo='+tipo+'&valor='+valor+'&nomd_id='+nomd_id+'&index='+index,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();	
    }

    sel_origen=function(nomd_id,index)
    {
        <?php if($func_id!=7){?>
            if (!compare_dates($('fecha1').value, $('fecha_actual').value))
            {
                alert("No se puede modificar Nominas anteriores a la fecha actual!");
                abrir_nomina($("folio_nomina").value, 1);
                return;
            }
        <?php } ?>
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        var valor=encodeURIComponent($('nomd_institucion_'+nomd_id).value);
        new_win = window.open('prestaciones/ingreso_nominas/seleccionar_opcion.php?tipo=2&valor='+valor+'&nomd_id='+nomd_id+'&index='+index,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    
    tipo_busqueda = function()
    {
        if($('paciente_tipo_id').value==0)
        {
            $('pac_rut').style.display='block';
            $('txt_paciente').style.display='none';
            
        }
        else
        {
            $('pac_rut').style.display='none';
            $('txt_paciente').style.display='block';
        }
        limpiar_paciente();
    }
    
    busqueda_pacientes = function(objetivo, callback_func)
    {
        
        $('pac_rut').style.display='block';
        $('txt_paciente').style.display='none';
        $('paciente_tipo_id').value = 0;
        
        
        top=Math.round(screen.height/2)-150;
        left=Math.round(screen.width/2)-250;
        new_win = window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
        'top='+top+', left='+left);
        new_win.objetivo_cod = objetivo;
        new_win.onCloseFunc = callback_func;
        new_win.focus();
    }
    
    
    verificar_rut = function()
    {
        var texto = $('pac_rut').value;
        if(texto.charAt(0)=='R')
        {
            $('paciente_tipo_id').value=0;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        else if(texto.charAt(0)=='P')
        {
            $('paciente_tipo_id').value=1;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        else if(texto.charAt(0)=='I')
        {
            $('paciente_tipo_id').value=2;
            $('pac_rut').value=texto.substring(1,texto.length);
        }
        if($('paciente_tipo_id').value==0)
        {
            if(comprobar_rut($('pac_rut').value))
            {
                //$('pac_rut').style.background='inherit';
                //buscar_paciente();
            }
            else
            {
                //$('pac_rut').style.background='red';
            }
        }
        else if($('paciente_tipo_id').value>0)
        {
            //$('pac_rut').style.background='yellowgreen';
            //buscar_paciente();
        }
    }
    
    
    Calendar.setup(
    {
            inputField      :   'fecha1',         // id of the input field
            ifFormat        :   '%d/%m/%Y',       // format of the input field
            showsTime       :   false,
            button          :   'fecha1_boton',
            dateStatusFunc  :   estado_fecha
            
            
    });
    
    
    function estado_fecha(date, y, m, d)
    {
        var data="";
        // Devuelve 'disabled' para desactivar la fecha...
        // Devuelve false para dejar la fecha intacta...
        // Devuelve '(string)' para usar esa clase...
        if($('esp_id').value=='-1')
        {
            return;
        }
        if($('doc_id').value=='-1')
        {
            return;
        }
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
        {
            for(var i=0;i<fechas_ausente.length;i++)
            {
                if(fecha3==fechas_ausente[i])
                    clase='ausente';
            }
        }
        if(fechas_ocupadas!==false)
        {
            for(var i=0;i<fechas_ocupadas.length;i++)
            {
                if(fecha2==fechas_ocupadas[i].cupos_fecha)
                {
                    var esp_id=$('esp_id').value;
                    if(fechas_ocupadas[i].esp_id==esp_id)
                        clase='fechaset';
                    //else
                        //clase='fechaset_other_esp';
                }
            }
        }   
        if(clase=='')
            return false;
        else
            return clase;

        
    }
    
    $j('#listado_nominas').scroll(function()
    {
        var divObj = document.getElementById("listado_nominas");
        $j('#div_pos').val(divObj.scrollTop);
        
    });
    
    
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
<center>
    <div class='sub-content' style='width:95%;'>
        <form id='info_nominas' onSubmit='return false;'>
            <input type='hidden' id='total_sobrecupos' name='total_sobrecupos' value='0' />
            <input type='hidden' id='nomina_sobrecupos' name='nomina_sobrecupos' value='0' />
            <input type='hidden' name='fecha_actual' id='fecha_actual' size=10 value='<?php echo date("d/m/Y"); ?>'>
            <input type='hidden' id='div_pos' name='div_pos' value='0' />
            <div class='sub-content'>
                <table style='width:100%;' cellpadding=0 cellspacing=0>
                    <tr>
                        <td style='width:30px;'>
                            <img src='iconos/table_edit.png'>
                        </td>
                        <td style='font-size:14px;width:200px;'><b>N&oacute;minas de Atenci&oacute;n</b></td>
                        <td>
                            <select id='orden' name='orden' style='font-size:11px;'>
                                <option value='0'>Ordenar por Folio, Nro. Ficha</option>
                                <option value='1'>Ordenar por Nro. Ficha</option>
                                <option value='2'>Ordenar por Paterno, Materno, Nombres</option>
                            </select>
                        </td>
                        <td style='width:100px;text-align:right;'>Nro. N&oacute;mina:</td>
                        <td style='width:100px;'>
                            <input type='text' id='folio_nomina' name='folio_nomina' size=10 style='text-align:center;' onKeyUp='if(event.which==13) abrir_nomina($("folio_nomina").value, 1);'>
                        </td>
                        <td style='text-align:center;' style='width:250px;display:none;font-size:10px;' id='select_nominas'>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content' id='buscar_nominas'>
                <table>
                    <tr>
                        <td>
                            <input type="button" id="btn_back_fecha" name="btn_back_fecha" onClick="listar_nominas(1);" value="<<" />
                        </td>
                        <td>Fecha:</td>
                        <td>
                            <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y"); ?>' onChange='listar_nominas(0);'>
                            <img src='iconos/date_magnify.png' id='fecha1_boton'>
                            <input type='button' value='[VER HOY: <?php echo date('d/m/Y'); ?>]' onClick='$("fecha1").value="<?php echo date("d/m/Y"); ?>";$("esp_id").value="-1";listar_nominas();' >
                        </td>
                        <td>
                            <input type="button" id="btn_next_banco" name="btn_next_banco" onClick="listar_nominas(2);" value=">>" />
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style='text-align:right;'>Especialidad:</td>
                        <td id='select_especialidades'>
                            <select id='esp_id' name='esp_id' onChange='listar_nominas(0);'>
                                <option value=-1 SELECTED>(Todas las Especialidades...)</option>
                                <?php echo $especialidades; ?>
                                <!--<?php echo $espechtml; ?>-->
                            </select>
                        </td>
                        <td>&nbsp;&nbsp;</td>
                        <td style='text-align:right;'>Medico:</td>
                        <td id='select_medico'>
                            <select id='doc_id' name='doc_id' onChange='listar_nominas(0);'>
                                <option value="-1" SELECTED>(Todos los Medicos o Sala...)</option>
                                <?php echo $medicohtml; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content' id='datos_nomina' style='display:none;'>
                <table style='width:100%;' border="0">
                    <tr>
                        <td style='width:100px;text-align:right;'>Nro. N&oacute;mina:</td>
                        <td id='nro_nomina' style='font-size:16px;font-weight:bold;'></td>
                        <td style='width:100px;text-align:right;'>Profesional:</td>
                        <td id='medico_nomina' style='font-size:20px;'></td>
                    </tr>
                    <tr>
                        <td style='width:100px;text-align:right;'>Fecha:</td>
                        <td id='fecha_nomina' style='font-size:24px;font-weight:bold;'></td>
                        <td style='width:100px;text-align:right;'>Especialidad:</td>
                        <td id='esp_nomina' style='font-size:18px;'></td>
                    </tr>
                    <tr>
                        <td style='width:100px;text-align:right;'>Estado:</td>
                        <td>
                            <select id='estado_nomina' name='estado_nomina'>
                                <option value=0>Completa</option>
                                <option value=1>Incompleta</option>
                                <option value=2>Vac&iacute;a</option>
                                <option value=3>Ausencia del Profesional</option>
                            </select>
                        </td>
                        <td style='width:120px;text-align:right;'>Extras Disponibles:</td>
                        <td id='extras_disponibles' style='font-size:15px;font-weight:bold;color: red'></td>
                    </tr>
                </table>
                <center>
                    <input type='button' id='lista_nominas' onClick='listar_nominas(0);' value='Actualizar Listado...'>
                </center>
            </div>
            <div class='sub-content' id='agregar_pacientes' style='display:none;'>
                <table style='width:100%;'>
                    <tr>
                        <td style='width:20px;'><img src='iconos/add.png' /></td>
                        <td style='width:100px;text-align:right;'>Agregar Paciente:</td>
                        <td id='td_horas' style='text-align:center;'>
                            <select id='nomd_hora' name='nomd_hora'>
                                <option value='00:00'>EXTRA</option>
                            </select>
                        </td>
                        <td id='td_horas_extra' style='text-align:center;display:none;'>
                            <select id='nomd_hora_extra' name='nomd_hora_extra'>
                            </select>
                        </td>
                        <td style="width:50px;">
                            <select id="paciente_tipo_id" name="paciente_tipo_id" style="font-size:10px;" onchange="tipo_busqueda();">
                                <option value=0 SELECTED>R.U.T.</option>
                                <option value=3>Nro. Ficha</option>
                                <option value=1>Pasaporte</option>
                                <option value=2>Cod. Interno</option>
                            </select>
                        </td>
                        <td>
                            <img src='iconos/zoom_in.png' id='buscar_paciente' onClick='busqueda_pacientes("pac_rut", function() { verificar_rut(); });' onKeyUp="fix_bar(this);" alt='Buscar Paciente...' title='Buscar Paciente...'>
                            <img src='imagenes/ajax-loader1.gif' id='cargando' style='display: none;'>
                        </td>
                        <td style='width:150px;text-align:center;'>
                            <input type='hidden' id='pac_id' name='pac_id' value='0' />
                            <input type='text' size=20 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' />
                            <input type='text' size=20 id='txt_paciente' name='txt_paciente' value='' onDblClick='limpiar_paciente();' style="display:none"/>
                        </td>
                        <td id='td_duracion' style='display:none;'>
                            <select id='duracion' name='duracion'>
                                <option value='1'>15 min</option>
                                <option value='2'>30 min</option>
                                <option value='3'>45 min</option>
                                <option value='4'>1 hr</option>
                                <option value='6'>1 hr 30 min</option>
                                <option value='8'>2 hr</option>
                                <option value='10'>2 hr 30 min</option>
                                <option value='12'>3 hr</option>
                            </select>
                        </td>
                        <td>
                            <input type='text' id='paciente' name='paciente' style='text-align:left;' DISABLED size=45 />
                            <input type='button' id='btn_agregar' value='[[ AGREGAR ]]' onClick='agregar_paciente();' />
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content2' style='height:380px;overflow:auto;' id='listado_nominas'>
            </div>
            <center>
                <table>
                    <tr>
                        <td id='guardar_registros' style='display:none;'>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/disk.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='guardar_registros();'> Guardar Registros...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td id='buscar_cupos' style='<?php echo $style_display; ?>'>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/date_magnify.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='buscar_cupos();'> Buscador de Cupos...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/date_magnify.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='solicitar_ficha();'> Solicitar Pr&eacute;stamo de Ficha...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <!---
                        <td id='crear_nominas' style='display:none;'>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/pencil.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='crear_nomina();'> Crear N&oacute;mina Nueva...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        -->
                        <td>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/printer.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='imprimir_listado();'> Imprimir Listado...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        
                        <td id='copiar_registros' style='<?php echo $style_display; ?>'>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/disk_multiple.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='copiar_nomina();'> Copiar N&oacute;mina...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td id='eliminar_nominas' style='display:none;'>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/cross.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='eliminar_nomina();'> Eliminar N&oacute;mina...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </center>
        </form>
    </div>
</center>
<script>
    listar_nominas(0);
    seleccionar_paciente = function(d)
    {
        $('pac_rut').value=d[0];
	$('paciente').value=d[2].unescapeHTML();
	$('pac_id').value=d[5];
	//$('pac_ficha').innerHTML=d[3];
	//$('prev_desc').innerHTML=d[6];
	//$('pac_fc_nac').innerHTML=d[7];
	//$('pac_edad').innerHTML='Edad: <b>'+d[11]+'</b>';    
        //$('prev_id').value=d[12];
	//$('ciud_id').value=d[13];
        //$('sincroniza').style.display='';
	//cargar_casos();
	//listar_recetas(d[4]);
    }

    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      'autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes_edad&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 4, seleccionar_paciente);
</script>