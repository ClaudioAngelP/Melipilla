<?php 
    require_once('../../conectar_db.php');
    $hosp_id=$_GET['hosp_id']*1;
    
    $r=cargar_registros_obj("SELECT *, upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat, 
    c1.tcama_tipo AS tcama_tipo, 
    c1.tcama_num_ini AS tcama_num_ini,
    c2.tcama_tipo AS tcama_tipo_ing,
    hosp_fecha_ing::date AS hosp_fecha_ing,
    hosp_fecha_ing::time AS hosp_hora_ing,
    e1.esp_desc AS esp_desc,
    e2.esp_desc AS esp_desc2
    FROM hospitalizacion 
    JOIN pacientes ON pac_id=hosp_pac_id
    LEFT JOIN especialidades_gestion_camas AS e1 ON hosp_esp_id=e1.esp_id
    LEFT JOIN especialidades_gestion_camas AS e2 ON hosp_esp_id2=e2.esp_id
    LEFT JOIN doctores ON doc_id=hosp_doc_id
    LEFT JOIN comunas USING (ciud_id)
    LEFT JOIN tipo_camas AS t1 ON
    t1.cama_num_ini<=hosp_numero_cama AND t1.cama_num_fin>=hosp_numero_cama
    LEFT JOIN clasifica_camas AS c1 ON 
    c1.tcama_num_ini<=hosp_numero_cama AND c1.tcama_num_fin>=hosp_numero_cama
    LEFT JOIN clasifica_camas AS c2 ON hosp_servicio=c2.tcama_id
    WHERE hosp_id=".$hosp_id);
											
    $ccamas = cargar_registros_obj("SELECT * FROM clasifica_camas ORDER BY tcama_num_ini", true);
    $ccamas2 = cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini", true);
    $tcamas=cargar_registros_obj("SELECT * FROM tipo_camas ORDER BY cama_num_ini;", true);
    $html='';

    //$necesidad=htmlentities($r[0]['hosp_necesidades']);
    $uval=pg_query("SELECT * FROM hospitalizacion_registro WHERE hosp_id = $hosp_id ORDER BY hreg_fecha DESC LIMIT 1;");
	
    if($v=pg_fetch_assoc($uval))
    {
        $hest_id=$v['hest_id']*1; //hest_id NO hosp_id...
	$hcon_id=$v['hcon_id']*1; // idem
    }
    else
    {
        $hest_id=1;
	$hcon_id=1;
    }	
?>
<html>
    <title>Completar Informaci&oacute;n de Solicitud</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        ccamas=<?php echo json_encode($ccamas) ?>;
	tcamas=<?php echo json_encode($ccamas2) ?>;
        
        function guardar_info()
        {
            //if(!validacion_fecha($('hosp_fecha_ing')) || !validacion_hora($('hosp_hora_ing')))
            //{
                //alert('Debe ingresar fecha y hora de ingreso correctamente.');
		//return;
            //}
            if($('tcama_id').value!='-1' && $('tcama_id').value!='-2' && ($('cama_id')==null || $('cama_id').value=='-1'))
            {
                alert('Debe seleccionar correctamente la cama de destino.');
		return;
            }
		
            if($('tcama_id').value=='-2'  && $('hosp_numero_cama').value==0 && $('hosp_destino').value!=6)
            {
                alert('No puede realizar un alta sin cama asignada.');
		return;
            }
		
            //valida diagnostico 
           if($('tcama_id').value=='-2' && (trim($('diagnostico2').value)=='') && $('hosp_destino').value!=6)
           {
               alert('Debe ingresar un diagnostico de egreso.');
               return;
           }		
		
            if($('intercambio').value=='-1')
            {
                alert( 'La cama seleccionada est&aacute; BLOQUEADA.'.unescapeHTML() );
		return;
            }
		
            if($('tcama_id').value!='-1' && $('tcama_id').value!='-2' && $('intercambio').value=='1')
            {
                if($('hosp_numero_cama').value*1<=0)
                {
                    alert( 'Debe seleccionar una cama vac&iacute;a para ingresar al paciente.'.unescapeHTML() );
                    return;
		}
                else
                {
                    var confi=confirm('&iquest;Esta seguro que desea INTERCAMBIAR los pacientes de cama?'.unescapeHTML());
                    if(!confi) { return; }
		}
            }
	
            if($('tcama_id').value=='-2' && $('hosp_destino').value*1==0)
            {
                alert( 'Debe seleccionar un destino de alta.'.unescapeHTML() );
		return;
            }

            if($('tcama_id').value=='-2' && $('hosp_destino').value*1==2 && $('inst_id').value*1==0)
            {
                alert( 'Debe seleccionar una instituci&oacute;n de derivaci&oacute;n para el alta.'.unescapeHTML() );
		return;
            }

            if($('tcama_id').value=='-2' && $('hosp_destino').value*1==5 && trim($('hosp_otro_destino').value)=='')
            {
                alert( 'Debe especificar el destino del alta.'.unescapeHTML() );
		return;
            }
		
            if($('tcama_id').value=='-2' && $('hosp_destino').value!=6)
            {
                if(!confirm((("&iquest;Est&aacute; seguro que el diagn&oacute;stico de egreso es correcto?\n\n"+$('diag_cod2').value+' '+$('diagnostico2').value)).unescapeHTML()))
                    return;
            }
            
            $('diag_cod').disabled=false;
            $('diag_cod2').disabled=false;

            var myAjax=new Ajax.Request('sql_info.php',
            {
                method:'post',
		parameters: $('info').serialize(),
		onComplete:function(resp)
                {
                    var fn=window.opener.listado.bind(window.opener);
                    fn();
                    window.close();
		}	
            });	
        }
	
        cambiar_pantalla=function()
        {
            var val=$('opciones').value*1;
            switch(val)
            {
                case 0:
                    $('datos_generales').show();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;
                
                case 1:
                    $('datos_generales').hide();
                    $('registro_prestaciones').show();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;

                case 2:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').show();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;

		case 3:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').show();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;

                case 4:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').show();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;

                case 5:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').show();
                    $('registro_epicrisis').hide();
                    $('registro_receta').hide();
                    break;

		case 6:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').show();
                    $('registro_receta').hide();
                    break;
                                
                case 7:
                    $('datos_generales').hide();
                    $('registro_prestaciones').hide();
                    $('registro_hoja_cargo').hide();
                    $('registro_recien_nacidos').hide();
                    $('registro_antibioticos').hide();
                    $('solicitud_traslado').hide();
                    $('registro_epicrisis').hide();
                    $('registro_receta').show();
                    break;
            }
        }
	
	listado_hoja_cargo=function()
        {
            var myAjax=new Ajax.Updater('listado_hoja_cargo','listado_hoja_cargo.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>'
            });
        }

	agregar_hoja_cargo=function()
        {
            if($('art_id').value=='')
            {
                alert('Debe seleccionar el art&iacute;culo a cargar al paciente.'.unescapeHTML());
		return;
            }
		
            var myAjax=new Ajax.Request('sql_agregar_hoja_cargo.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>&'+
		$('art_id').serialize()+'&'+
		$('art_cantidad').serialize(),
		onComplete:function()
                {
                    listado_hoja_cargo();
                    $('art_id').value='';
                    $('art_codigo').value='';
                    $('art_glosa').value='';
                    $('art_cantidad').value='1';
                    $('art_forma').innerHTML='';
                    $('art_codigo').focus();
		}
            });
	}
	
        eliminar_hc=function(hosphc_id)
        {
            var myAjax=new Ajax.Request('sql_agregar_hoja_cargo.php',
            {
                method:'post',
		parameters:'hosphc_id='+hosphc_id,
		onComplete:function()
                {
                    listado_hoja_cargo();
		}
            });
        }

        listado_prestaciones=function()
        {
            var myAjax=new Ajax.Updater('listado_prestaciones','listado_prestaciones.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>'
            });
	}
	
	agregar_prestacion=function()
        {
            if($('nombre_presta').value=='')
            {
                alert('Debe seleccionar la prestaci&oacute;n a cargar al paciente.'.unescapeHTML());
		return;
            }
		
            var myAjax=new Ajax.Request('sql_agregar_prestacion.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>&'+$('codigo_presta').serialize()+'&'+
		$('nombre_presta').serialize()+'&'+
		$('cant_presta').serialize(),
		onComplete:function()
                {
                    //alert('Su solicitud se ha enviado a Infectolog&iacite;a. \n Consulte el estado de su solicitud en esta misma pantalla.'.unescapeHTML());
                    listado_prestaciones();
                    $('codigo_presta').value='';
                    $('nombre_presta').value='';
                    $('cant_presta').value='1';
                    $('codigo_presta').focus();
		}
            });
        }

        realizar_hospp=function(hospp_id)
        {
            var myAjax=new Ajax.Request('sql_agregar_prestacion.php',
            {
                method:'post',
		parameters:'hospp_id='+hospp_id,
		onComplete:function()
                {
                    listado_prestaciones();
		}
            });
	}

	listado_recien_nacidos=function()
        {
            var myAjax=new Ajax.Updater('listado_recien_nacidos','listado_recien_nacidos.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>'
            });
	}

        agregar_recien_nacido=function()
        {
            if($('rn_peso').value*1==0)
            {
                alert('Debe ingresar el peso en gramos del reci&eacute;n nacido.'.unescapeHTML());
		return;
            }

            if($('rn_apgar').value*1==0)
            {
                alert('Debe ingresar el APGAR del reci&eacute;n nacido.'.unescapeHTML());
		return;
            }
		
            var myAjax=new Ajax.Request('sql_agregar_recien_nacido.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>&'+
		$('rn_condicion').serialize()+'&'+
		$('rn_sexo').serialize()+'&'+
		$('rn_peso').serialize()+'&'+
		$('rn_apgar').serialize(),
		onComplete:function()
                {
                    listado_recien_nacidos();
                    $('rn_condicion').value='0';
                    $('rn_sexo').value='0';
                    $('rn_peso').value='';
                    $('rn_apgar').value='';
		}
            });
        }

	eliminar_parto=function(hospp_id)
        {
            var myAjax=new Ajax.Request('sql_agregar_recien_nacido.php',
            {
                method:'post',
		parameters:'hospp_id='+hospp_id,
		onComplete:function()
                {
                    listado_recien_nacidos();
		}
            });
	}

        listado_autoriza_meds=function()
        {
            var myAjax=new Ajax.Updater('listado_autoriza_meds','listado_autoriza_meds.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>'
            });
	}

	agregar_autoriza_meds=function()
        {
            if($('art_id2').value=='')
            {
                alert('Debe seleccionar el medicamento a solicitar al paciente.'.unescapeHTML());
		return;
            }
		
            var myAjax=new Ajax.Request('sql_agregar_autoriza_meds.php',
            {
                method:'post',
		parameters:'hosp_id=<?php echo $hosp_id; ?>&'+
		$('doc_id2').serialize()+'&'+
		$('art_id2').serialize()+'&'+
		$('art_cantidad2').serialize()+'&'+
		$('art_horas').serialize()+'&'+
		$('art_dias').serialize()+'&'+
		$('art_observa').serialize()+'&'+
		$('art_terapia').serialize()+'&'+
		$('art_motivo').serialize()+'&'+
		$('art_forma2').serialize(),
		onComplete:function(r)
                {
                    resp=r.responseText.evalJSON(true);
                    if(resp=='OK')
                    {
                        alert('Su solicitud se ha enviado a Infectolog&iacute;a. \nConsulte el estado de su solicitud en esta misma pantalla.'.unescapeHTML());
			listado_autoriza_meds();
			$('art_id2').value='';
			$('art_codigo2').value='';
			$('art_cantidad2').value='1';
			$('art_horas').value='';
			$('art_dias').value='';
			$('art_observa').value='';
			$('art_motivo').value='Inicio Tratamiento';
			$('art_terapia').value='Terapia Emp&iacute;rica';
			//$('art_forma2').innerHTML='';
			$('art_codigo2').focus();
                    }
                    else
                    {
                        alert(resp);
			$('art_codigo2').focus();		
                    }
		}
            });
	}

	eliminar_am=function(hospam_id)
        {
            var myAjax=new Ajax.Request('sql_agregar_autoriza_meds.php',
            {
                method:'post',
		parameters:'hospam_id='+hospam_id,
		onComplete:function()
                {
                    listado_autoriza_meds();
		}
            });
        }

	continuacion = function()
        {
            //console.log($('art_motivo').value);
            if($('art_motivo').value=='Continuaci&oacute;'.unescapeHTML())
            {
                var myAjax=new Ajax.Updater('cont_lbl','continuacion.php',
		{
                    method:'post',
                    parameters:'pac_id=<?php echo $r[0]['pac_id']; ?>'
		});		
            }
	}

        function tr_traslado()
        {
            var val=$('motivo').value*1;
            if(val==0)
            {
                $('tr1').show();
		$('tr2').show();
		$('tr3').hide();
		$('tr4').hide();
		$('tr5').hide();
		$('tr6').hide();
            }
            else if(val==1)
            {
                $('tr1').hide();
		$('tr2').hide();
		$('tr3').show();
		$('tr4').hide();
		$('tr5').hide();
		$('tr6').hide();			
            }
            else if(val==2)
            {
                $('tr1').hide();
		$('tr2').hide();
		$('tr3').show();
		$('tr4').hide();
		$('tr5').hide();
		$('tr6').hide();						
            }
            else if(val==3)
            {
                $('tr1').hide();
		$('tr2').hide();
		$('tr3').hide();
		$('tr4').show();
		$('tr5').show();
		$('tr6').hide();			
            }
            else if(val==4)
            {
                $('tr1').hide();
		$('tr2').hide();
		$('tr3').hide();
		$('tr4').hide();
		$('tr5').hide();
		$('tr6').show();			
            }
	}
    </script>
    <body class='fuente_por_defecto popup_background'>
        <div class='sub-content'>
            <img src='../../iconos/script_edit.png' />
            <b>Actualizaci&oacute;n de Datos del Paciente</b>
        </div>
        <form id='info' name='info' onSubmit='return false;'>
            <div class='sub-content'>
                <input type='hidden' id='hosp_id' name='hosp_id' value='<?php echo $hosp_id; ?>' />
                <input type='hidden' id='hosp_numero_cama' name='hosp_numero_cama' value='<?php echo $r[0]['hosp_numero_cama']; ?>' />
                <input type='hidden' id='intercambio' name='intercambio' value='0' />
                <input type='hidden' id='hosp_id2' name='hosp_id2' value='0' />
                <table style='width:100%;'>
                    <tr>
                        <td style='text-align:right;width:100px;'>R.U.T.:</td>
                        <td style='font-weight:bold;font-size:16px;color:green;'><?php echo $r[0]['pac_rut']; ?></td>
                    </tr>
                    <tr>
                        <td style='text-align:right;width:100px;'>Ficha:</td>
                        <td style='font-weight:bold;font-size:16px;color:green;'><?php echo $r[0]['pac_ficha']; ?></td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Nombre:</td>
                        <td style='font-weight:bold;font-size:16px;color:yellowgreen;'><?php echo $r[0]['pac_nombres'].' '.$r[0]['pac_appat'].' '.$r[0]['pac_apmat']; ?></td>
                    </tr>
                </table>
            </div>
            <center>
                <table style='width:95%;font-size:20px;'>
                    <tr class='tabla_header'>
                        <td style='text-align:right;font-weight:bold;'>Secci&oacute;n:</td>
                        <td>
                            <select id='opciones' name='opciones' style='font-size:20px;width:100%;' onChange='cambiar_pantalla();'>
                                <option value='0' SELECTED>Datos Generales Hospitalizaci&oacute;n</option>
                                <!--- <option value='6'>Registro de Epicrisis</option> -->
                                <option value='1'>Registro de Prestaciones</option>
                                <option value='2'>Registro de Hoja de Cargo</option>
                                <option value='3'>Registro de Reci&eacute;n Nacidos</option>
                                <option value='4'>Solicitud de Antibi&oacute;ticos Restringidos</option>
                                <option value='7'>Prescripci&oacute;n de Receta</option>
                                <!--- <option value='5'>Solicitud de Traslado</option> -->
                            </select>
                        </td>
                    </tr>
                </table>
            </center>
            <div class='sub-content' id='datos_generales'>
                <table style='width:100%;'>
                    <tr>
                        <td id='tag_esp' style='text-align:right;'>Fecha de Ingreso:</td>
                        <td>
                            <input type='text' id='hosp_fecha_ing'  name='hosp_fecha_ing' style='text-align:center' onBlur='validacion_fecha(this);' value='<?php echo $r[0]['hosp_fecha_ing']; ?>' onDblClick='' size='10'>
                            <input type='text' id='hosp_hora_ing'  name='hosp_hora_ing' style='text-align:center;' onBlur='validacion_hora(this);' value='<?php echo substr($r[0]['hosp_hora_ing'],0,5); ?>' onDblClick='' size='5'>
                        </td>
                    </tr>
                    <tr>
                        <td id='tag_esp' style='text-align:right;'>Especialidad:</td>
                        <td>
                            <input type='hidden' id='esp_id' name='esp_id' value='<?php echo $r[0]['hosp_esp_id']*1; ?>'>
                            <input type='text' id='especialidad'  name='especialidad' value='<?php echo $r[0]['esp_desc']; ?>' onDblClick='$("esp_id").value=""; $("especialidad").value="";' size=35>
                        </td>
                    </tr>
                    <tr>
                        <td id='tag_esp' style='text-align:right;'>Subespecialidad:</td>
                        <td>
                            <input type='hidden' id='esp_id2' name='esp_id2' value='<?php echo $r[0]['hosp_esp_id2']*1; ?>'>
                            <input type='text' id='especialidad2'  name='especialidad2' value='<?php echo $r[0]['esp_desc2']; ?>' onDblClick='$("esp_id2").value=""; $("especialidad2").value="";' size=35>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;width:30%;'>Servicio Ingreso:</td>
                        <td>
                            <input type="hidden" id='centro_ruta0' name='centro_ruta0' value='<?php echo $r[0]['hosp_servicio']; ?>'>
                            <input type="text" id='servicios0' name='servicios0' onDblClick='$("centro_ruta0").value=""; $("servicios0").value="";' value='<?php echo $r[0]['tcama_tipo_ing']; ?>'>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>R.U.T. M&eacute;dico:</td>
                        <td>
                            <input type='text' id='rut_medico' name='rut_medico' size=10 style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' disabled>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>M&eacute;dico Tratante:</td>
                        <td>
                            <input type='hidden' id='doc_id' name='doc_id' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
                            <input type='text' id='nombre_medico' name='nombre_medico' size=35 value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' onDblClick='$("doc_id").value=""; $("nombre_medico").value="";' />
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Ubicaci&oacute;n Actual:</td>
                        <td style='font-size:16px;color:#3333FF;'>
                            <?php
                            if($r[0]['hosp_numero_cama']*1!=0)
                            {
                            ?>
                                <b><?php echo $r[0]['tcama_tipo'].' - '.$r[0]['cama_tipo']; ?></b>
                                <i>Cama:</i><b><?php echo $r[0]['hosp_numero_cama']*1-$r[0]['tcama_num_ini']*1+1; ?></b>
                            <?
                            }
                            else
                            {
                            ?>
                                <i>(Sin Cama Asignada...)</i>
                            <?
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;white-space:no-wrap;'>Modificar Serv. / Sala / Cama:</td>
                        <td>
                            <span id='tcama' name='tcama'></span>
                            <span id='ccama' name='ccama'></span>
                            <span id='cama' name='cama'></span>
                            <span id='imagen' name='imagen'></span>
                        </td>
                    </tr>
                    <tr id='pac_cama_tr' style='display:none;'>
                        <td style='text-align:right;' id='pac_mot' valign='top'>Pac. en Cama:</td>
                        <td id='pac_en_cama' style='font-size:16px;color:#FF3333;'></td>
                    </tr>
                    <tr id='destino_tr' style='display:none;'>
                        <td style='text-align:right;'>Destino del Alta:</td>
                        <td style=''>
                            <select id='hosp_destino' name='hosp_destino' onChange='ver_destino();'>
                                <option value="0" SELECTED>(Seleccionar...)</option>
                                <option value="1">Alta a Domicilio</option>
                                <option value="6">No Hospitalizado</option>
                                <option value="2">Derivaci&oacute;n</option>
                                <option value="3">Fallecido</option>
                                <option value="4">Fugado</option>
                                <option value="5">Otro...</option>
                            </select>
                            <span id='otro_destino' style='display:none;'>
                                Destino: <input type='text' id='hosp_otro_destino' name='hosp_otro_destino' value='' size=25 />
                            </span>
                        </td>
                    </tr>
                    <tr id='inst_tr' style='display:none;'>
                        <td style='text-align: right;'>Instituci&oacute;n de Destino:</td>
                        <td style='text-align: left;' colspan=3>
                            <input type='hidden' id='inst_id' name='inst_id' value=''>
                            <input type='text' id='institucion' name='institucion' size=40>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Estado:</td>
                        <td>
                            <select id='hest_id' name='hest_id' >
                            <?php
                            $e=pg_query("SELECT * FROM hospitalizacion_estado");
                            while($r2=pg_fetch_assoc($e))
                            {
                                if($r2['hest_id']==$hest_id) $sel='SELECTED'; else $sel='';
                                print('<option value="'.$r2['hest_id'].'" '.$sel.'>'.$r2['hest_nombre'].'</option>');
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Condici&oacute;n:</td>
                        <td>
                            <select id='hcon_id' name='hcon_id' >
                                <?php
                                $e=pg_query("select * from hospitalizacion_condicion");
                                while($r2=pg_fetch_assoc($e))
                                {
                                    if($r2['hcon_id']==$hcon_id) $sel='SELECTED'; else $sel='';
                                    print('<option value="'.$r2['hcon_id'].'" '.$sel.'>'.$r2['hcon_nombre'].'</option>');
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Diagn&oacute;stico Ingreso CIE10:</td>
                        <td colspan=3>
                            <input type='text' id='diag_cod' name='diag_cod' value='<?php echo $r[0]['hosp_diag_cod']; ?>' DISABLED size=5 style='font-weight:bold;text-align:center;' />
                            <input type='text' id='diagnostico' name='diagnostico' value='<?php echo $r[0]['hosp_diagnostico']; ?>' size=60 onDblClick='$("diag_cod").value=""; $("diagnostico").value="";' />
                        </td>
<tr>
  <td style='text-align:right;'>Diagn&oacute;stico Egreso CIE10:</td>
  <td colspan=3><input type='text' id='diag_cod2' name='diag_cod2' 
value='<?php echo $r[0]['hosp_diag_cod2']; ?>' disabled size=5 style='font-weight:bold;text-align:center;' />
    <input type='text' id='diagnostico2' 
value='<?php echo $r[0]['hosp_diagnostico2']; ?>' name='diagnostico2' size=35
onDblClick='$("diag_cod2").value=""; $("diagnostico2").value="";' /></td>
</tr>
<tr>
<td style='text-align:right;' valign="top">Observaciones:</td>
<td colspan=3><label>
<textarea name="observacion" id="observacion" cols="52"></textarea>
</label></td>
</tr>

<tr>
<td style='text-align:right;' valign="top">Necesidades:</td>
<td colspan=3><label>
<textarea name="necesidad" id="necesidad" cols="52"></textarea>
</label></td>
</tr>

</table>

<center>
<br />
<input type='button' id='guardar' name='guardar'
 onClick='guardar_info();'value='-- Guardar Registro... ---'>           
<br /><br />
</center>

</div>

</form>



<div class='sub-content' id='registro_prestaciones' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/building.png' /> <b>Registro de Prestaciones</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Prestaci&oacute;n:</td>
		<td>
		<input type='text' id='codigo_presta' name='codigo_presta' value='' />
		</td>
		<td>
		<input type='text' size=40 id='nombre_presta' name='nombre_presta' READONLY />
		</td>
		<td style='text-align:right;'>Cant:</td>
		<td>
		<input type='text' size=5 id='cant_presta' name='cant_presta' value="1" />
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_prestacion();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_prestaciones' style='height:300px;overflow:auto;'>

</div>

</div>

<div class='sub-content' id='registro_hoja_cargo' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/pill.png' /> <b>Hoja de Cargo Paciente</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Art&iacute;culo:</td>
		<td>
		<input type='hidden' id='art_id' name='art_id' value='' />
		<input type='text' id='art_codigo' name='art_codigo' value='' />
		</td>
		<td>
		<input type='text' size=40 id='art_glosa' name='art_glosa' READONLY />
		</td>
		<td style='text-align:right;'>Cant:</td>
		<td>
		<input type='text' size=5 id='art_cantidad' name='art_cantidad' style='text-align:right;' value='1' />
		<span id='art_forma' style='font-weight:bold;'></span>
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_hoja_cargo();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_hoja_cargo' style='height:300px;overflow:auto;'>

</div>

</div>


<div class='sub-content' id='registro_recien_nacidos' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/user_add.png' /> <b>Registro de Reci&eacute;n Nacidos</b> 
</div>


<table style='width:100%;'>
	<tr>
		<td> 
		<center><img src='../../iconos/add.png'></center>
		</td>
		<td style='text-align:right;'>Datos R.N.:</td>
		<td>
		<select id='rn_condicion' name='rn_condicion'>
		<option value='0'>VIVO</option>
		<option value='1'>FALLECIDO</option>
		</select>
		</td>
		<td>
		<select id='rn_sexo' name='rn_sexo'>
		<option value='0'>MASCULINO</option>
		<option value='1'>FEMENINO</option>
		<option value='2'>INDEFINIDO</option>
		</select>
		</td>
		<td style='text-align:right;'>Peso:</td><td>
		<input type='text' size=10 id='rn_peso' name='rn_peso' value=''>
		</td>
		<td style='text-align:right;'>APGAR:</td><td>
		<input type='text' size=10 id='rn_apgar' name='rn_apgar' value=''>
		</td>
		<td>
		<input type='button' value='-- Agregar --' onClick='agregar_recien_nacido();' />
		</td>
	</tr>
</table>


<div class='sub-content2' id='listado_recien_nacidos' style='height:300px;overflow:auto;'>

</div>


</div>




<div class='sub-content' id='registro_antibioticos' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/pill.png' /> <b>Solicitud de Antibi&oacute;ticos Restringidos</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td rowspan=8> 
		<center><img src='../../iconos/add.png' style='width:32px;height:32px;'></center>
		</td>
		<td style='text-align:right;'>M&eacute;dico:</td>
		<td>
		<input type='text' id='rut_medico2' name='rut_medico2' size=10
		style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' disabled>

		<input type='hidden' id='doc_id2' name='doc_id2' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
		<input type='text' id='nombre_medico2' name='nombre_medico2' size=35
		   value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' onDblClick='$("doc_id").value=""; $("nombre_medico").value="";' />
		</td>
		<td rowspan=8>
		<input type='button' value='-- Agregar --' onClick='agregar_autoriza_meds();' />
		</td>
	</tr>		
	<tr>
		<td style='text-align:right;'>Art&iacute;culo:</td>
		<td><input type='hidden' id='art_id2' name='art_id2' value='' />
			<input type='text' size=10 id='art_codigo2' name='art_codigo2' value='' />
			<input type='text' size=45 id='art_glosa2' name='art_glosa2' READONLY />
		</td>
	</tr>
	<tr>
		<td style='text-align:right;'>Dosis:</td>
		<td><input type='text' size=3 id='art_cantidad2' name='art_cantidad2' style='text-align:right;' value='1' />
			&nbsp;<select id='art_forma2' name='art_forma2'>
				<option value='mg'>Miligramos</option>
				<option value='gr'>Gramos</option>
			  </select>cada&nbsp;
			<input type='text' size=3 id='art_horas' name='art_horas' style='text-align:right;' value='1' />
			&nbsp;horas por&nbsp;
			<input type='text' size=3 id='art_dias' name='art_dias' style='text-align:right;' value='1' />
			&nbsp;d&iacute;as.
		</td>
	</tr>
	<tr>
		<td style='text-align:right;'>Tipo Terapia:</td>
		<td><select id='art_motivo' name='art_motivo' onChange='continuacion();'>
			<option value='Inicio Tratamiento'>Inicio Tratamiento</option>
			<option value='Modificaci&oacute;n'>Modificaci&oacute;n</option>
			<option value='Continuaci&oacute;n'>Continuaci&oacute;n</option>
			</select>&nbsp;
			<select id='art_terapia' name='art_terapia'
			onChange="if(this.value==1) {$('bichol').style.display=''; $('bicho').style.display=''; }else{$('bichol').style.display='none'; $('bicho').style.display='none';}">
			<option value='0'>Terapia Emp&iacute;rica</option>
			<option value='1'>Terapia Espec&iacute;fica</option>
			</select>
			&nbsp;
		</td>
	</tr>
	<tr><td style='text-align:right;'><span style='display:none;' id='bichol' name='bichol'>Cultivo:</span></td><td>
		<input style='display:none;' type='text' name='bicho' id='bicho' size=35>
		</td>
	</tr>	
	<tr id='tr_cont' name='tr_cont' style='display:none;'><td>Continuaci&oacute;n:</td>
		<td><span id='cont_lbl' name='cont_lbl'></span></td>
	</tr>
	<tr>
		<td style='text-align:right;' name='diag_lbl' id='diag_lbl' >Diagn&oacute;stico:</td>
		<td style='text-align:left;'>
			<select id='terapia_diag' name='terapia_diag'
			onChange="if(this.value==-1) {$('espe').style.display=''; $('terapia_esp').style.display=''; }else{$('espe').style.display='none'; $('terapia_esp').style.display='none';}">
				<option value='0'>Abdomen agudo</option>
				<option value='1'>Apendicitis aguda</option>
				<option value='2'>Bacteremia asociada a CVC</option>
				<option value='3'>Infeccion torrente sanguineo  asoc. a CVC</option>
				<option value='4'>ITS asociada a CVC</option>
				<option value='5'>ITU (Infeccion aguda Tracto urinario)</option>
				<option value='6'>Neumonia asoc. a ventilaci&oacute;n mec&aacute;nica</option>
				<option value='7'>Neumonia aspirativa</option>
				<option value='8'>Neumonia de la comunidad</option>
				<option value='9'>Neumonia intrahospitalaria</option>
				<option value='10'>Pie diab&eacute;tico complicado</option>
				<option value='11'>Pielonefritis aguda</option>
				<option value='12'>Sepsis urinaria</option>
				<option value='-1'>OTRO</option>
			</select>
		</td></tr><tr><td style='text-align:right;'>
			<span style='display:none;' id='espe' name='espe'>Especif&iacute;que Diag.:</span></td><td style='text-align:left;'>
			<input type='text' name='terapia_esp' id='terapia_esp' style='display:none;' size=35/>
		</td>
	</tr>
	<tr>	
		<td style='text-align:right;'>Observaciones:</td>
		<td>
		<input type='text' size=45 id='art_observa' name='art_observa' value='' />
		</td>		
	</tr>
		
	
</table>


<div class='sub-content2' id='listado_autoriza_meds' style='height:250px;overflow:auto;'>

</div>

</div>

<div class='sub-content' id='solicitud_traslado' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/building_go.png' /> <b>Solicitud de Traslado de Paciente</b> 
</div>

<table style='width:100%;'>
	<tr>
		<td style='text-align:right;'>Motivo del Traslado:</td>
		<td><select id='motivo' name='motivo' onChange='tr_traslado();'>
		<option value='0'>Alta a Domicilio</option>
		<option value='1'>Rescate a Domicilio</option>
		<option value='2'>Rescate a Cl&iacute;nica/Hospital</option>
		<option value='3'>Procedimiento</option>
		<option value='4'>Traslado a la Red</option>
		</select></td>
	</tr>
	<tr id='tr1'>
		<td style='text-align:right;'>Direcci&oacute;n:</td>
		<td>
			<input type='text' id='direccion' name='direccion' size=40 />
		</td>
	</tr>
	<tr id='tr2'>
		<td style='text-align:right;'>Tel&eacute;fono:</td>
		<td>
			<input type='text' id='fono' name='fono' />
		</td>
	</tr>
	<tr id='tr3' style='display:none;'>
		<td style='text-align:right;'>Desde/Hacia:</td>
		<td>
			<input type='text' id='desde' name='desde' />
			<input type='text' id='hacia' name='hacia' />
		</td>
	</tr>
	<tr id='tr4' style='display:none;'>
		<td style='text-align:right;'>Procedimiento:</td>
		<td>
			<input type='text' id='proc' name='proc' size=40 />
		</td>
	</tr>
	<tr id='tr5' style='display:none;'>
		<td style='text-align:right;'>Lugar/Hora Citaci&oacute;n:</td>
		<td>
			<input type='text' id='proc' name='proc' size=30 />
			<input type='text' id='hora' name='hora' size=10 />
		</td>
	</tr>
	<tr id='tr6' style='display:none;'>
		<td style='text-align:right;'>Desde/Hacia:</td>
		<td>
			<input type='text' id='desde2' name='desde2' />
			<input type='text' id='hacia2' name='hacia2' />
		</td>
	</tr>
	<tr>
		<td style='text-align:right;'>Nombre de Contacto:</td>
		<td>
			<input type='text' id='contacto' name='contacto' size=40 />
		</td>
	</tr>
</table>

<table style='width:100%;'>
	<tr>
		<td colspan=8 class='tabla_fila2'><b>Condiciones del Paciente</b></td>
	</tr>
	<tr>
		<td><input type='checkbox' id='cond1' name='cond1' value='Grave' /></td>
		<td style='width:15%;'>Grave</td>
		<td><input type='checkbox' id='cond2' name='cond2' value='Postrado' /></td>
		<td style='width:15%;'>Postrado</td>
		<td><input type='checkbox' id='cond3' name='cond3' value='Desmovilizado' /></td>
		<td style='width:15%;'>Desmovilizado</td>
		<td><input type='checkbox' id='cond4' name='cond4' value='Autovalente' /></td>
		<td style='width:15%;'>Autovalente</td>
	</tr>
	<tr>
		<td colspan=8 class='tabla_fila2'><b>Requisitos del Traslado</b></td>
	</tr>
	<tr>
		<td style='width:5%;'><input type='checkbox' id='req1' name='req1' value='Sentado' /></td>
		<td>Sentado</td>
		<td style='width:5%;'><input type='checkbox' id='req2' name='req2' value='Bomba de Infusi&oacute;n' /></td>
		<td>Bomba de Infusi&oacute;n</td>
		<td style='width:5%;'><input type='checkbox' id='req3' name='req3' value='Sujeciones' /></td>
		<td>Sujeciones</td>
		<td style='width:5%;'><input type='checkbox' id='req4' name='req4' value='Monitores' /></td>
		<td>Monitores</td>
	</tr>
	<tr>
		<td style='width:5%;'><input type='checkbox' id='req5' name='req5' value='Sentado' /></td>
		<td>V. Mec&acute;nica</td>
		<td style='width:5%;'><input type='checkbox' id='req6' name='req6' value='Bomba de Infusi&oacute;n' /></td>
		<td>Incubadora</td>
		<td style='width:5%;'><input type='checkbox' id='req7' name='req7' value='Sujeciones' /></td>
		<td>Camilla</td>
		<td style='width:5%;'><input type='checkbox' id='req8' name='req8' value='Monitores' /></td>
		<td>V. No Invasiva</td>
	</tr>
	<tr>
		<td style='width:5%;'><input type='checkbox' id='req9' name='req9' value='Ox&iacute;geno' /></td>
		<td>Ox&iacute;geno</td>
		<td colspan=2><input type='text' id='req9_txt' name='req9_txt' value='' /></td>
		<td style='width:5%;'><input type='checkbox' id='req10' name='req10' value='Otros' /></td>
		<td>Otros</td>
		<td colspan=2><input type='text' id='req10_txt' name='req10_txt' value='' /></td>
	</tr>
</table>
<br /><br />
<center>
<input type='button' id='' name='' value='Guardar Traslado...' />
</center>

</div>


<div class='sub-content' id='registro_epicrisis' style='display:none;'>

<div class='sub-content'>
<img src='../../iconos/layout_edit.png' /> <b>Registro de Epicrisis Paciente Hospitalizado</b> 
</div>

</div>

<div class='sub-content' id='registro_receta' style='display:none;'>

    <div class='sub-content'>
        <img src='../../iconos/layout_edit.png' /> <b>Prescripci&oacute;n de Receta</b> 
    </div>

</div>    
    
    


</body>
</html>

<script>

     ingreso_especialidades=function(datos_esp) {
      	$('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2].unescapeHTML();
      }
      
       autocompletar_especialidades = new AutoComplete(
      'especialidad', 
      '../../autocompletar_gcamas.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=especialidad&esp_desc='+encodeURIComponent($('especialidad').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

     ingreso_especialidades2=function(datos_esp) {
      	$('esp_id2').value=datos_esp[0];
      	$('especialidad2').value=datos_esp[2].unescapeHTML();
      }
      
       autocompletar_especialidades2 = new AutoComplete(
      'especialidad2', 
      '../../autocompletar_gcamas.php',
      function() {
        if($('especialidad2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=subespecialidad&esp_desc='+encodeURIComponent($('especialidad2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades2);


    seleccionar_serv2 = function(d) {

		$('centro_ruta0').value=d[0].unescapeHTML();
		$('servicios0').value=d[2].unescapeHTML(); 

    }

    autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      '../../autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);


      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      '../../autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);

      ingreso_diagnosticos=function(datos_diag) {
      	
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('diag_cod').value=cie10;
      	$('diagnostico').value=datos_diag[2].unescapeHTML();
      	
      }

      autocompletar_diagnosticos = new AutoComplete(
      	'diagnostico', 
      	'../../autocompletar_sql.php',
      function() {
        if($('diagnostico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('diagnostico').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);
      
      ingreso_diagnosticos2=function(datos_diag) {
      	
      	var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
      	cie10+='.'+datos_diag[0].charAt(3);
      	
      	$('diag_cod2').value=cie10;
      	$('diagnostico2').value=datos_diag[2].unescapeHTML();
      	
      }

      autocompletar_diagnosticos2 = new AutoComplete(
      	'diagnostico2', 
      	'../../autocompletar_sql.php',
      function() {
        if($('diagnostico2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=diagnostico_tapsa&cadena='+encodeURIComponent($('diagnostico2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos2);

	seleccionar_inst = function(d) {
    
      $('inst_id').value=d[0];
      $('institucion').value=d[2].unescapeHTML();
    
    }
	
	autocompletar_institucion = new AutoComplete(
      'institucion', 
      '../../autocompletar_sql.php',
      function() {
        if($('institucion').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('institucion').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst);


      
   function select_ccamas(){

		var val=$('tcama_id').value;
	   
	   $('destino_tr').hide();
	   $('hosp_destino').value=0;
	   ver_destino();

	   if(val=='-1' || val=='-2') {
		
		$("ccama").innerHTML='';
		$("cama").innerHTML='';
		$("imagen").innerHTML='';
		
		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		if(val=='-2') {
			
			$('destino_tr').show();
			
		}
		
		return;
	   
	   }
	      
		var id=$('tcama_id').value.split(';');
		
		//console.log('ID:'+id);
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;

		//console.log('tcama_id:'+id);
		//console.log('tcama_num_ini:'+tcama_num_ini);
		//console.log('tcama_num_fin:'+tcama_num_fin);
		
		html="<select id='ccama_id' name='ccama_id' onchange='select_camas();'><option value='-1'>(Seleccionar...)</option>";

		for (i=0;i<tcamas.length;i++){
		
			//console.log('tcama_num_ini:'+tcamas[i].cama_num_ini+' >= '+tcama_num_ini+' AND <= '+tcama_num_fin);
			//console.log('tcama_num_fin:'+tcamas[i].cama_num_fin);
		
			if(tcamas[i].cama_num_ini*1>=tcama_num_ini && tcamas[i].cama_num_ini*1<=tcama_num_fin) {
				html+="<option value='"+tcamas[i].cama_id+";"+tcamas[i].cama_num_ini+";"+tcamas[i].cama_num_fin+"'>"+tcamas[i].cama_tipo+"</option>";	
			} 
	
		}
	
		html+="</select>";
		
		$("ccama").innerHTML=html;
		$("cama").innerHTML='';
		$("imagen").innerHTML='';
		   
   }
   
   function select_camas(){

		var val=$('ccama_id').value;
	   
		if(val=='-1') {
		
		$("cama").innerHTML='';
		$("imagen").innerHTML='';

		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		return;
	   
		}
   
		var id=$('tcama_id').value.split(';');
		
		var tcama_id=id[0]*1;
		var tcama_num_ini=id[1]*1;
		var tcama_num_fin=id[2]*1;
   
		var id=$('ccama_id').value.split(';');
		
		var cama_id=id[0]*1;
		var cama_num_ini=id[1]*1;
		var cama_num_fin=id[2]*1;

		
		html="<select id='cama_id' name='cama_id' onchange='verificar_cama();'><option value='-1'>(Seleccionar...)</option>";

		for (i=cama_num_ini;i<=cama_num_fin;i++){
		
			html+="<option value='"+i+"'>"+((i-tcama_num_ini)+1)+"</option>";	
	
		}
	
		html+="</select>";
		
		
		$("cama").innerHTML=html;
		$("imagen").innerHTML='';

	}
	
	function verificar_cama(){

		var val=$('cama_id').value;
	   
		if(val=='-1') {
		
		$("imagen").innerHTML='';

		$('pac_cama_tr').hide();
					
		$('intercambio').value=0;
		$('hosp_id2').value=0;
		
		return;
	   
		}
	
		var myajax=new Ajax.Request(
			'verificar_cama.php',
			{ 
				method:'post',
				parameters:$('cama_id').serialize(),
				onComplete:function(r){
					
					var dd=r.responseText.evalJSON(true);
					
					if(!dd[0]) {
						
						var d=dd[1];
						
						$("imagen").innerHTML="<img src='../../iconos/lock.png' style='width:18px;height:18px;' />";
							
						$('pac_cama_tr').show();
						
						var observa='<br /><font color="blue">'+d.bloq_observaciones+'</font>';
						
						$('pac_en_cama').innerHTML='<b><u>'+d.bmot_desc+'</u></b> <i>['+d.func_nombre+']</i>'+observa;

						$('intercambio').value=-1;
						$('hosp_id2').value=d.hosp_id;
						
						$('pac_mot').innerHTML='Bloqueada:';
						
					} else {
						
						var d=dd[1];

						$('pac_mot').innerHTML='Pac. en Cama:';
					
						if(!d) {
						
							$("imagen").innerHTML="<img src='../../iconos/tick.png' style='width:18px;height:18px;' />";						

							$('pac_cama_tr').hide();
							
							$('intercambio').value=0;
							$('hosp_id2').value=0;
							
						}else{
							 
							$("imagen").innerHTML="<img src='../../iconos/cross.png' style='width:18px;height:18px;' />";
							
							$('pac_cama_tr').show();
							$('pac_en_cama').innerHTML='<b>'+d.pac_rut+'</b> '+d.pac_appat+' '+d.pac_apmat+' '+d.pac_nombres;

							$('intercambio').value=1;
							$('hosp_id2').value=d.hosp_id;
											
						}
					
					}			
				}			
			}		
		); 
	
	}
		
	html="<select id='tcama_id' name='tcama_id' onchange='select_ccamas();'><option value='-1'>(Sin Movimiento...)</option><option value='-2'>(Paciente de Alta Administrativa...)</option>";

	for (i=0;i<ccamas.length;i++){
		html+="<option value='"+ccamas[i].tcama_id+";"+ccamas[i].tcama_num_ini+";"+ccamas[i].tcama_num_fin+"'>"+ccamas[i].tcama_tipo+"</option>";	
	
	}
	
	html+="</select>";
	
	$("tcama").innerHTML=html;

	ver_destino = function() {
		
		var val=$('hosp_destino').value*1;
		
		if(val==2) {
			$('inst_tr').show();
		} else {
			$('inst_tr').hide();			
		}
		
		if(val==5) {
			$('otro_destino').show();
		} else {
			$('otro_destino').hide();			
		}
		
		
		
	}
	
	//validacion_fecha($('hosp_fecha_ing'));
	validacion_hora($('hosp_hora_ing'));








      ingreso_presta=function(datos_presta) {
      	      	
      	$('codigo_presta').value=datos_presta[0];
      	$('nombre_presta').value=datos_presta[2].unescapeHTML();
      	
      }

      autocompletar_prestaciones = new AutoComplete(
      	'codigo_presta', 
      	'autocompletar_sql.php',
      function() {
		  
        if($('codigo_presta').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=prestacion&cod_presta='+encodeURIComponent($('codigo_presta').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_presta);


      ingreso_hc=function(datos_art) {
      	      	
      	$('art_id').value=datos_art[0];
      	$('art_codigo').value=datos_art[1];
      	$('art_glosa').value=datos_art[2].unescapeHTML();
      	$('art_forma').innerHTML=datos_art[3];
      	$('art_cantidad').focus();
      	
      }

      autocompletar_hoja_cargo = new AutoComplete(
      	'art_codigo', 
      	'autocompletar_sql.php',
      function() {
        if($('art_codigo').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=articulo&art_codigo='+encodeURIComponent($('art_codigo').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_hc);






      ingreso_rut2=function(datos_medico) {
      	$('doc_id2').value=datos_medico[3];
      	$('rut_medico2').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico2', 
      '../../autocompletar_sql.php',
      function() {
        if($('nombre_medico2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('nombre_medico2').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut2);


      ingreso_ab=function(datos_art) {
      	      	
      	$('art_id2').value=datos_art[0];
      	$('art_codigo2').value=datos_art[1];
      	$('art_glosa2').value=datos_art[2].unescapeHTML();
      	//$('art_forma2').innerHTML=datos_art[3];
      	$('art_cantidad2').focus();
      	
      }

      autocompletar_medicamentos = new AutoComplete(
      	'art_codigo2', 
      	'autocompletar_sql.php',
      function() {
        if($('art_codigo2').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicamento_restringido&art_codigo='+encodeURIComponent($('art_codigo2').value)
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_ab);
	
	
	listado_hoja_cargo();
	listado_prestaciones();
	listado_recien_nacidos();
	listado_autoriza_meds();


</script>
