<?php
    require_once('../../conectar_db.php');
    require_once('../../conectores/fonasa/cargar_paciente_fonasa.php');
    $pac_id=$_GET['pac_id']*1;
    $nom_id=$_GET['nom_id']*1;
    $nomd_hora=pg_escape_string($_GET['nomd_hora']);
    $nomd_hora_extra=pg_escape_string($_GET['nomd_hora_extra']);
    $duracion='';
    
    if(isset($_GET['duracion']))
        $duracion=$_GET['duracion']*1;
    $hora="";
    
    if($nomd_hora=='00:00') {
        if(strstr($nomd_hora_extra,'_')) {
            $cmp=explode('_',$nomd_hora_extra);
            $nom_id=$cmp[1]*1;
            $nomd_hora_extra=$cmp[0];
            $hora=$cmp[0];
        } else {
            $hora=$nomd_hora_extra;
        }
    } else {
        if(strstr($nomd_hora,'_')) {
            $cmp=explode('_',$nomd_hora);
            if(count($cmp)>2) {
                $nom_id=$cmp[2]*1;
            }
            $hora=$cmp[0];
        }
    }
    
    $consulta="SELECT * FROM nomina LEFT JOIN especialidades ON nom_esp_id=esp_id LEFT JOIN doctores ON nom_doc_id=doc_id WHERE nom_id=$nom_id";
    $n=cargar_registro($consulta);
    if($n) {
        $esp_id=$n['nom_esp_id'];
    }
    
    if(strstr($nomd_hora,'_')) {
		$cmp=explode('_',$nomd_hora);
        $nomina_detalle=true;
        //$consulta="select nomd_extra from nomina_detalle where nom_id=$nom_id and nomd_hora='$cmp[0]'";
    } else {
        $nomina_detalle=false;
        //$consulta="select nomd_extra from nomina_detalle where nom_id=$nom_id and nomd_hora='$nomd_hora'";
    }
    
    if($nomina_detalle) {
		$consulta="select nomd_id from nomina_detalle where nom_id=$nom_id and nomd_hora='$cmp[0]'";
        $reg_cupo=cargar_registro($consulta);
        if(!$reg_cupo) {
            print("Error al Encontrar Nomina_detalle bloque");
            die();
        }
        $nomd_id=$reg_cupo['nomd_id'];
    }
    /*
    $reg_cupo=cargar_registro($consulta);
    if(!$reg_cupo) {
        print("Error al Encontrar Nomina_detalle");
        die();
    } else {
        $extra=$reg_cupo['nomd_extra'];
    }
    */
    
    $nofonasa=false;
    if($pac_id!="0" && $pac_id!="") {
        $consulta="SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_id=$pac_id";
        $pac=cargar_registro($consulta, true);
        $consulta="
        SELECT * FROM nomina_detalle 
        JOIN nomina USING (nom_id)
        JOIN especialidades ON nom_esp_id=esp_id
        JOIN doctores ON nom_doc_id=doc_id
        WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))
        ORDER BY nom_fecha, nomd_hora;
        ";
        $nd=cargar_registros_obj($consulta);
    } else {
        if(isset($_GET['paciente_tipo_id'])) {
            $tipo = $_GET['paciente_tipo_id']*1;
        } else {
            $tipo = 0;    
        }
        
        if($tipo!=2) {
            if($tipo==0)
                $id = pg_escape_string($_GET['pac_rut']);
            else
                $id = pg_escape_string($_GET['txt_paciente']);
        } else
			$id = $_GET['txt_paciente']*1;
        
        if($tipo==0) {
            if(isset($_GET['pac_rut'])) {
				if($_GET['pac_rut']!='') {
					$pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE upper(pac_rut)=upper('".$_GET['pac_rut']."')", true);
                    if(!$pac) {
						pac_fonasa($_GET['pac_rut'],0);
                    }
                    
                    $pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE upper(pac_rut)=upper('".$_GET['pac_rut']."')", true);
                    if($pac) {
						$pac_id=$pac['pac_id'];
						$nd=cargar_registros_obj("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) JOIN especialidades ON nom_esp_id=esp_id JOIN doctores ON nom_doc_id=doc_id WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))  ORDER BY nom_fecha, nomd_hora;");
                    } else {
						echo 'Error al procesar paciente datos no encontrados en fonasa';
                        echo '<br>';
                        echo 'Ingrese datos de forma Manual';
                        if(($_SESSION['sgh_usuario_id']*1)!=7)
                            die();
                        $nofonasa=true;
                    }
                } else {
                    echo 'Error al procesar paciente';
                    die();
                }
            } else {
                echo 'Error al procesar paciente';
                die();
            }
        } else if($tipo==3) {
			$pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE upper(pac_ficha)=upper('".$id."')", true);
            if($pac) {
				$pac_id=$pac['pac_id'];
				$nd=cargar_registros_obj("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) JOIN especialidades ON nom_esp_id=esp_id JOIN doctores ON nom_doc_id=doc_id WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))  ORDER BY nom_fecha, nomd_hora;");
            } else {
                echo 'Error al procesar paciente';
                die();
            }
        } else if($tipo==1) {
			$pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE upper(pac_pasaporte)=upper('".$id."')", true);
            if($pac) {
				$pac_id=$pac['pac_id'];
				$nd=cargar_registros_obj("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) JOIN especialidades ON nom_esp_id=esp_id JOIN doctores ON nom_doc_id=doc_id WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))  ORDER BY nom_fecha, nomd_hora;");
            } else {
                echo 'Error al procesar paciente';
                die();
            }
        } else {
			$pac=cargar_registro("SELECT *,(SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_id=$id", true);
            if($pac) {
				$pac_id=$pac['pac_id'];
				$nd=cargar_registros_obj("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) JOIN especialidades ON nom_esp_id=esp_id JOIN doctores ON nom_doc_id=doc_id WHERE pac_id=$pac_id AND ((nom_fecha>=(CURRENT_DATE-('2 days'::interval)) AND nomd_diag_cod NOT IN ('B')) OR (nomd_diag_cod='X' AND nomd_estado IS NULL OR NOT nomd_estado='1'))  ORDER BY nom_fecha, nomd_hora;");
            } else {
                echo 'Error al procesar paciente';
                die();
            }
        }
    }
    
    $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades WHERE (esp_codigo_ifl_usuario='' or esp_codigo_ifl_usuario is null) ORDER BY esp_desc", NULL, '', '');
    $pac_encontrado="false";
    $pac_encontrado_hora="false";
    $consulta="SELECT * FROM nomina_detalle WHERE nom_id=$nom_id and pac_id=$pac_id and nomd_diag_cod not in ('T')";
    $reg_nd=cargar_registros_obj($consulta);
    
    if($reg_nd) {
        $pac_encontrado="true";
        for($i=0;$i<count($reg_nd);$i++) {
            if(substr($reg_nd[$i]['nomd_hora'],0,5)==$hora) {
                $pac_encontrado_hora="true";
                break;
            }
        }
    }
    
    //$espext_html=desplegar_opciones_sql("SELECT DISTINCT from interconsulta where inter_espdesc!='' order by inter_espdesc
    
    //$servhtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_ruta ILIKE '.subdireccionmedica.%' and centro_medica ORDER BY centro_nombre", NULL, '', ""); 
    
    $servhtml = desplegar_opciones_sql("
	SELECT centro_ruta, centro_nombre FROM centro_costo 
	WHERE centro_ruta in (
	'.subdireccionmedica.centroderesponsabilidadapoyoclinico',
	'.subdireccionmedica.centroderesponsabilidadatencionabierta',
	'.subdireccionmedica.centroderesponsabilidadatencioncerrada',
	'.subdireccionmedica.centroderesponsabilidadurgencia'
	) 
	and centro_medica ORDER BY centro_nombre
	", NULL, '', ""); 
    
    
?>
<html>
    <title>Actualizar Datos del Paciente</title>
    <?php cabecera_popup('../..'); ?>
    <script type="text/javascript">
        presta_examen=[];
        presta_sort=[];
        bloquear_ingreso=false;
        
        function imprimir_citacion(nomd_id)
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            new_win = window.open('citaciones2.php?nomd_id='+nomd_id,
            '_self', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
            'top='+top+', left='+left);
            new_win.focus();
        }
        
        function guardar_cupo() {
			
			if(bloquear_ingreso) {
                alert('Se esta procesando un ingreso previo intente nuevamente.'.unescapeHTML());
                return;
            }
            
            if($('pac_fc_nac').value=='') {
				alert("Debe Ingresar Fecha de Nacimiento del paciente");
                return;
            }
            
            if(!validacion_fecha($('pac_fc_nac'))) {
                alert('Debe ingresar una fecha v&aacute;lida para la fecha de nacimiento.'.unescapeHTML());
				return;
            }
            
            if($('pac_encontrado_hora').value=='true') {
                alert('El Paciente ya ha sido ingresado en la nomina correspondiente con el misma hora de atenci&oacute;n. \nNO SE REALIZARA EL INGRESO'.unescapeHTML());
				return;
            }
            
            if($('pac_encontrado').value=='true') {
                if(!confirm(('El paciente ya se encuentra ingresado el la NOMINA ACTUAL. &#191;Est&aacute; seguro que desea asignar la hora de todas Formas?').unescapeHTML()))
                    return;	
            }
            
            if(($('cant_presta').value*1)>0) {
				var anychecked = $$('#lista_presta input[type=checkbox]').any(function(n) {
					return n.checked;
				});
			
				if(!anychecked){
					alert('DEBE SELECCIONAR AL MENOS UNA PRESTACI&Oacute;N PARA LA ATENCI&Oacute;N. \nNO SE REALIZARA EL INGRESO'.unescapeHTML());
					return;
				}
				
			} else {
				if(!confirm(('LA ATENCI&Oacute;N NO PRESENTA PRESTACIONES ASOCIADAS. &#191;Est&aacute; seguro que desea asignar la hora de todas Formas?').unescapeHTML()))
                    return;	
			}
            
            var params='';
            if(presta_examen.length>0) {
				
				if($('tipo_sol').value=='1') {
					
					if($('opcion_prof').value=='0') {
						/*
						if($('doc_id_exam').value=='0') {
							alert('Debe Ingresar M&eacute;dico Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						*/
						
					} else {
						
						/*
						if($('prof_rut').value=='') {
							alert('Debe Ingresar Rut del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_nombres').value=='') {
							alert('Debe Ingresar Nombre del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_paterno').value=='') {
							alert('Debe Ingresar Apellido Paterno del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_materno').value=='') {
							alert('Debe Ingresar Apellido Materno del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if(!comprobar_rut($('prof_rut').value)) {
							alert('El rut del profesional externo solicitante no es valido'.unescapeHTML());
							$('prof_rut').style.background='red';
							return;
						} else {
							$('prof_rut').style.background='inherit';
						}
						*/
						
					}
					
					/*
					if($('esp_exam_solicita').value=='-1') {
						alert("Debe Ingresar Especialidad del M&eacute;dico Solicitante de ex&aacute;menes".unescapeHTML());
						return;
					}
					*/
					
					if($('serv_exam_solicita').value=='-1') {
						alert("Debe Ingresar Servicio Solicitante de ex&aacute;menes".unescapeHTML());
						return;
					}
					
					if($('centros_cant').value!="0"){
						if($('centro_exam_solicita').value==""){
							alert("Debe Seleccionar un Centro Valido");
							return;
						}
					}
				
				} else {
					
					if($('inst_id_sol').value=='') {
						alert("Debe Ingresar Insititucion Solicitante de ex&aacute;menes".unescapeHTML());
						return;
					}
					
					if($('esp_id2_sol').value=='') {
						alert("Debe Ingresar Especialidad Solicitante de ex&aacute;menes".unescapeHTML());
						return;
					}
					
					if($('opcion_prof').value=='0') {
						/*
						if($('prof_id_sol').value=='') {
							alert('Debe Ingresar Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						*/
						
					} else {
						/*
						if($('prof_rut').value=='') {
							alert('Debe Ingresar Rut del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_nombres').value=='') {
							alert('Debe Ingresar Nombre del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_paterno').value=='') {
							alert('Debe Ingresar Apellido Paterno del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if($('prof_materno').value=='') {
							alert('Debe Ingresar Apellido Materno del Profesional Externo Solicitante de ex&aacute;menes.'.unescapeHTML());
							return;
						}
						
						if(!comprobar_rut($('prof_rut').value)) {
							alert('El rut del profesional externo solicitante no es valido'.unescapeHTML());
							$('prof_rut').style.background='red';
							return;
						} else {
							$('prof_rut').style.background='inherit';
						}
						*/
					}
				}
                
                if($('obsgeneral').value=='') {
                    alert("Debe Ingresar alguna Observaci&oacute;n General de la solicitud de ex&aacute;menes".unescapeHTML());
                    return;
                }
                params+='&examenes='+encodeURIComponent(presta_examen.toJSON());
            } 
            
            bloquear_ingreso=true;
            var myAjax=new Ajax.Request('sql_tomar_cupo.php',
            {
                method:'post',
                parameters:$('datos_pac').serialize()+params,
                onComplete:function(resp2)
                {
                    if(resp2.responseText!="X")
                    {
                        bloquear_ingreso=false;
                        window.opener.parent.limpiar_paciente();
                        var fn2=window.opener.abrir_nomina.bind(window.opener);
                        fn2(window.opener.$("folio_nomina").value, 1);
                        imprimir_citacion(resp2.responseText*1);
                        
                    }
                    else
                    {
                        bloquear_ingreso=false;
                        alert("LA HORA SELECCIONADA YA HA SIDO ASIGNADA");
                        return;
                    }
                    //window.close();
                }
            });		
        }
        
        function ver_leyenda()
        {
            alert("LEYENDA SEGUN COLORES:\n\nAtenciones con letras de color ROJO: [Atenciones Anteriores en Misma Especialidad]\n\nAtenciones con fondo de color AMARILLO: [Atenciones Bloqueadas]\n\nAtenciones con fondo de color ROSA: [Atenciones Suspendidas]");
            return;
            
        }
    
        function init()
        {
            <?php if($pac) { ?> 
                var myAjax=new Ajax.Request('certificar_paciente.php',
                {
                    method:'post', parameters:'pac_rut=<?php echo $pac['pac_rut']; ?>',
                    onComplete:function(r)
                    {
                        //alert(r.responseText);
                        try
                        {
                            var datos=r.responseText.evalJSON(true);
                            $('prev_id').value=datos.prev_id;
                            $('ult_act').innerHTML=datos.fecha_fonasa.substr(0,19);
                            $('cargar_fonasa').hide();
                        }
                        catch(err)
                        {
                            //alert(r.responseText);
                            $('ult_act').innerHTML="<font color=red>Se ha encontrado Problema al actualizar Fonasa</font>";
                            $('cargar_fonasa').hide();
                        }
                    }
                });
            <?php } else { ?>
                $('ult_act').innerHTML="<font color=red>Se ha encontrado Problema al buscar datos del paciente</font>";
                $('cargar_fonasa').hide();
            <?php } ?>
        }
        
        mostrar_examenes=function(index) {
            if(index==1) {
                $('span_examen').show();
                $('tb_examenes').hide();
                $('span_prestaciones').hide();
                $('tb_prestaciones').show();
                
                
            }
            if(index==2) {
                $('span_prestaciones').show();
                $('tb_prestaciones').hide();
                $('span_examen').hide();
                $('tb_examenes').show();
            }
            return;
        }
        
        
        examenes=function(tabs_index)
        {
            if(tabs_index==1)
            {
                tab_down('tab_examenes_solicitud');
                tab_up('tab_examenes_historia');
                /*
                listar_examen_historia();
                */
            }
            if(tabs_index==2)
            {
                tab_down('tab_examenes_historia');
                tab_up('tab_examenes_solicitud');
                /*
                document.getElementById('esp_exam').value = '0';
                document.getElementById('tipo_exam').value = '0';
                $('tipo_exam').style.display='none';
                presta_examen.length=0;
                $j('#lista_examen_favorito').html('');
                $j('#lista_presta_examen').html('');
                */
            }
        }
        
        actualizar=function() {
            $('cod_presta_examen').value='';
            $('cod_prestacion').value='0';
            $('desc_presta_examen').value='';
            $('pc_id_examen').value='';
            $('kit_exam').value='0';
            $('cantidad_examen').value='';
            $('obs_examen').value='';
            var myAjax=new Ajax.Updater('td_grupo','actualizar_grupos.php', { method:'post',parameters:$('esp_exam').serialize() });
            $('lista_presta_examen').innerHTML='';
           
        }
        
        lista_examenes=function() {
            if($('esp_exam').value=='-1'){
                alert(("Debe seleccionar Tipo Ex&aacute;men para Visualizar Lista de Ex&aacute;menes").unescapeHTML());
                return;
            }
            if($('grupo_exam').value=='-1'){
                alert(("Debe seleccionar Grupo de Ex&aacute;men para Visualizar Lista de Ex&aacute;menes").unescapeHTML());
                return;
            }
            var win = new Window("popup_listexamen",
            {
                className: "alphacube", top:40, left:0, width: 800, height: 600, 
                title: '<img src="../../iconos/page_white_link.png"> LISTA DE EX&Aacute;MENES',
                minWidth: 500, minHeight: 400,
                maximizable: false, minimizable: false,
                wiredDrag: true, draggable: true,
                closable: true, resizable: false 
            });
            win.setDestroyOnClose();
            win.setAjaxContent('lista_examenes.php', 
            {
                method: 'post',
                async:false,
                dataType: 'json',
                parameters: 'esp_id='+$('esp_exam').value+'&grupo_exam='+$('grupo_exam').value,
            });
            $("popup_listexamen").win_obj=win;
            win.showCenter();
            win.show(true);
        }
        
        
        
        agregar_prestacion_examen = function(index,llamada,tipo)
        {
            var esp_exam=($('esp_exam').value*1);
            var cant=0;
            var esp='';
            var desc_presta='';
            var tipo_examen='';
            if(llamada==1) {
                x='';
                if(esp_exam==6117) {
                    if($('grupo_exam').value=='ECOTOMOGRAFIA') {
                        if($('presta_codigo_'+index).value=='0404016' && $('presta_desc_'+index).value=='PARTES BLANDAS') {
                            var organo=prompt("Favor indicar Organo para ECOTOMOGRAFIA: ["+$('desc_presta_examen').value+"]","");
                            if(organo=='' || organo==undefined) return;
                            if (organo!=null) {
                                x=organo;
                            } else {
                                x='';
                            }
                        }
                    }
                }
                var codigo=$('presta_codigo_'+index).value;
                if(x!='') {
                    desc_presta=$('presta_desc_'+index).value+" "+"["+x+"]";
                } else {
                    desc_presta=$('presta_desc_'+index).value;
                }
                cant=1;
                esp=($('esp_exam').value*1);
                var pc_id=$('pc_id_'+index).value;
                tipo_examen=$('grupo_exam').value;
                var observ_examen='';
                
            }
            if(llamada==2) {
                
                if($('cod_prestacion').value==0 || $('cod_prestacion').value=="0") {
                    alert("Debe Seleccionar prestacion para ingresar".unescapeHTML());
                    return;
                }
                if($('cod_presta_examen').value=="") {
                    alert("Debe Seleccionar prestacion para ingresar".unescapeHTML());
                    return;
                }
                if($('cantidad_examen').value=="") {
                    alert(("Debe Ingresar la cantidad validad de ex&aacute;menes a solicitar").unescapeHTML());
                    $('cantidad_examen').select();
                    $('cantidad_examen').focus();
                    return;
                }
                if(($('cantidad_examen').value*1)==0 || ($('cantidad_examen').value*1)<0) {
                    alert(("Debe Ingresar la cantidad validad de ex&aacute;menes a solicitar").unescapeHTML());
                    $('cantidad_examen').select();
                    $('cantidad_examen').focus();
                    return;
                }
                x='';
                if(esp_exam==6117) {
                    if($('grupo_exam').value=='ECOTOMOGRAFIA') {
                        if($('cod_presta_examen').value=='0404016' && $('desc_presta_examen').value=='PARTES BLANDAS') {
                            var organo=prompt("Favor indicar Organo para ECOTOMOGRAFIA: ["+$('desc_presta_examen').value+"]","");
                            if(organo=='' || organo==undefined) return;
                            if (organo!=null) {
                                x=organo;
                            } else {
                                x='';
                            }
                        }
                    }
                }
                codigo=$('cod_presta_examen').value;
                if(x!='') {
                    desc_presta=$('desc_presta_examen').value+" "+"["+x+"]";
                } else {
                    desc_presta=$('desc_presta_examen').value;
                }
                cant=$('cantidad_examen').value;
                esp=$('esp_exam').value;
                var pc_id=$('pc_id_examen').value;
                tipo_examen=$('grupo_exam').value;
                var observ_examen=$('obs_examen').value;
            }
            var encontrado=false;
            for(var i=0;i<presta_examen.length;i++){
                if(presta_examen[i].pc_id==pc_id){
                    alert("Ya ha ingresado la prestaci&oacute;n seleccionada".unescapeHTML());
                    return;
                    //presta_examen[i].cantidad=(presta_examen[i].cantidad*1)+(cant*1);
                    //encontrado=true;
                }
            }
            
            if(encontrado==false) {
                if((tipo*1)!=1) {
                    var num=presta_examen.length;
                    presta_examen[num]=new Object();
                    presta_examen[num].esp=esp;
                    presta_examen[num].codigo=codigo;
                    presta_examen[num].desc=desc_presta;
                    presta_examen[num].cantidad=cant;
                    presta_examen[num].pc_id=pc_id;
                    presta_examen[num].tipo_examen=tipo_examen;
                    presta_examen[num].obs_examen=observ_examen;
                    listar_prestaciones_examen();
                } else {
                    var myAjax=new Ajax.Request('cargar_kit_examen.php',
                    {
                        method:'post',
                        parameters:'kit_examen_id='+pc_id+'&esp_id='+esp_exam+'&cant='+cant,
                        async:true,
                        onComplete:function(r)
                        {
                            try 
                            {
                                var det=r.responseText.evalJSON(true);
                                for(var d=0;d<det.length;d++)
                                {
                                    var fnd=false;
                                    for(var i=0;i<presta_examen.length;i++)
                                    {
                                        if(presta_examen[i].pc_id==det[d].pc_id)
                                        {
                                            presta_examen[i].cantidad=det[d].cantidad*1;
                                            fnd=true;
                                            break;
                                        }
                                    }
                                    if(fnd)
                                        continue;
                                    
                                    var num=presta_examen.length;
                                    presta_examen[num]=new Object();
                                    presta_examen[num].esp=esp*1;
                                    presta_examen[num].codigo=det[d].codigo;
                                    presta_examen[num].desc=det[d].pc_desc;
                                    presta_examen[num].cantidad=det[d].cantidad;
                                    presta_examen[num].pc_id=det[d].pc_id;
                                    presta_examen[num].tipo_examen=det[d].pc_grupo_examen;
                                    presta_examen[num].obs_examen=observ_examen;
                                }
                                listar_prestaciones_examen();
                            }
                            catch(err) { alert(err); }
                        }
                    });
                }
                limpiar_campos();
            }
        }
        
        
        listar_prestaciones_examen=function()
        {
            var esp_exam=($('esp_exam').value*1);
            if(esp_exam===0 || esp_exam==="0"){
                return;
            }
            var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Descripci&oacute;n</td><td>Tipo Ex&aacute;men</td><td>Cantidad</td><td>Eliminar</td></tr>';    
            if(presta_examen.length>0){
                for(var i=(presta_examen.length-1);i>=0;i--) {
                    clase=(i%2==0)?'tabla_fila':'tabla_fila2';
                    if(presta_examen[i].desc.length>100)
                        var descr=presta_examen[i].desc.substr(0,100)+'...';
                    else
                        var descr=presta_examen[i].desc;
                    if(presta_examen[i].esp==esp_exam){
                        html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                            html+='<td style="text-align:left;font-weight:bold;">'+presta_examen[i].codigo+'</td>';
                            html+='<td>'+descr+'</td>';
                            html+='<td style="text-align:center;">'+presta_examen[i].tipo_examen+'</td>';
                            html+='<td style="text-align:center;">'+presta_examen[i].cantidad+'</td>';
                            html+='<td><center><img src="../../iconos/delete.png" style="cursor: pointer;" onClick="eliminar_prestacion_examen('+i+');"></center></td>';
                        html+='</tr>';
                    }
                }
            }
            html+='</table>'
            $('lista_presta_examen').innerHTML=html;
            $('cant_examenes').innerHTML='Ex&aacute;menes Solicitados (&nbsp;<b><font color=red>'+presta_examen.length+'</font></b>&nbsp;)';
        }
        
        limpiar_examenes=function()
        {
            presta_examen.length = [];
            limpiar_campos();
            listar_prestaciones_examen();
        }
        
        eliminar_prestacion_examen = function(id)
        {
            presta_examen=presta_examen.without(presta_examen[id]);
            listar_prestaciones_examen();
        }
        
        limpiar_campos=function() {
            $('cod_presta_examen').value='';
            $('cod_prestacion').value='0';
            $('desc_presta_examen').value='';
            $('pc_id_examen').value='';
            $('kit_exam').value='0';
            $('cantidad_examen').value='1';
            $('obs_examen').value='';
            $('cod_presta_examen').select();
            $('cod_presta_examen').focus();
            $('td_nom_exam').innerHTML='';
        }
        
        
        fix_fields=function()
        {
            var t=$('tipo_sol').value*1;
            if(t==1) {
				$('div_titulo_medico').innerHTML='<b>Profesional Interno:</b> Ingreso de nuevos datos del m&eacute;dico';
                $('serv_tr').style.display='';
                $('serv_centros_tr').style.display='';
                //$('unid_tr').style.display='';
                $('medi_tr').style.display='';
                if($('serv_exam_solicita').value==".subdireccionmedica.centroderesponsabilidadurgencia") {
					$('tr_dau').style.display='';
				} else {
					$('tr_dau').style.display='none';
				}
                $('inst_tr').style.display='none';
                $('espe_tr').style.display='none';
                $('prof_tr').style.display='none';
                $('add_prof_tr').style.display='none';
                $('observ_tr').style.display='';
            } else {
				$('div_titulo_medico').innerHTML='<b>Profesional Externo:</b> Ingreso de nuevos datos del m&eacute;dico';
                $('serv_tr').style.display='none';
                $('serv_centros_tr').style.display='none';
                //$('unid_tr').style.display='none';
                $('medi_tr').style.display='none';
                $('tr_dau').style.display='none';
                $('inst_tr').style.display='';
                $('espe_tr').style.display='';
                $('prof_tr').style.display='';
                $('add_prof_tr').style.display='none';
                $('observ_tr').style.display='';
            }
            
            $('doc_id_exam').value='';
			$('nombre_medico').value='';
			$('rut_medico').value='';
			$('serv_exam_solicita').value='-1';
			$('centro_exam_solicita').value='';
			$('txt_dau').value='';
                                
			$('inst_id_sol').value='';
			$('inst_desc_sol').value='';
                
			$('esp_id2_sol').value='';
			$('esp_desc2_sol').value='';
                
			$('prof_id_sol').value='';
			$('prof_rut_sol').value='';
			$('prof_nombre_sol').value='';
                
			$('prof_rut').value='';
            $('prof_nombres').value='';
            $('prof_paterno').value='';
			$('prof_materno').value='';
            $('obsgeneral').value='';
            
        }
        
		actualizar_centro=function() {
			if($('serv_exam_solicita').value=="") {
				$('td_centros').innerHTML="<select id='centro_exam_solicita' name='centro_exam_solicita'><option value=''>(Seleccione Centro...)</option></select>";
				return;
			}
			var myAjax=new Ajax.Updater('td_centros','select_centros.php',
			{
				method:'post',
				evalScripts:true,
				parameters:$('serv_exam_solicita').serialize()
			});
			
			if($('serv_exam_solicita').value==".subdireccionmedica.centroderesponsabilidadurgencia") {
				$('tr_dau').style.display='';
			} else {
				$('tr_dau').style.display='none';
			}
		}
		
        agregar_profesional=function() {
			if($('tipo_sol').value==1) { 
				$('medi_tr').style.display='none';
			} else {
				$('prof_tr').style.display='none';
			}
			$('add_prof_tr').style.display='';
			$('prof_id_sol').value='';
			$('prof_rut_sol').value='';
			$('prof_nombre_sol').value='';
			$('opcion_prof').value='1';
		}
		
		cancelar_profesional=function() {
			if($('tipo_sol').value==1) { 
				$('medi_tr').style.display='';
				$('doc_id_exam').value='0';
				$('nombre_medico').value='';
				$('rut_medico').value='';
			} else {
				$('prof_tr').style.display='';
				$('prof_id_sol').value='';
				$('prof_rut_sol').value='';
				$('prof_nombre_sol').value='';
				$('opcion_prof').value='0';
			}
			$('add_prof_tr').style.display='none';
		}
		
		verificar_rut_prof = function() {
			if($('prof_rut').value!='') {
				$('prof_rut').value=trim($('prof_rut').value);
				if(!comprobar_rut($('prof_rut').value)) {
					$('prof_rut').style.background='red';
				} else {
					$('prof_rut').style.background='inherit';
				}
			}
		}
		
    </script>
    <body class='fuente_por_defecto popup_background' onLoad='init();'>
    <div class='sub-content'>
        <img src='../../iconos/user_go.png' />
        <b>Actualizar Datos de Paciente</b>
        <?php if($nd) { ?>
            <input type='button' value='[[ Ver Leyenda]]' onClick='ver_leyenda();' style='font-size:12px;margin:5px;' />
        <?php } ?>
    </div>
    <form id='datos_pac' name='datos_pac' onSubmit='return false;'>
        <input type='hidden' id='pac_id' name='pac_id' value='<?php echo $pac_id; ?>' />
        <input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
        <input type='hidden' id='nomd_hora' name='nomd_hora' value='<?php echo $nomd_hora; ?>' />
        <input type='hidden' id='nomd_hora_extra' name='nomd_hora_extra' value='<?php echo $nomd_hora_extra; ?>' />
        <input type='hidden' id='hora_extra' name='hora_extra' value='<?php echo $extra; ?>' />
        <input type='hidden' id='duracion' name='duracion' value='<?php echo $duracion; ?>' />
        <input type='hidden' id='pac_encontrado' name='pac_encontrado' value='<?php echo $pac_encontrado; ?>' />
        <input type='hidden' id='pac_encontrado_hora' name='pac_encontrado_hora' value='<?php echo $pac_encontrado_hora; ?>' />
        <input type='hidden' id='esp_id2' name='esp_id2' value='<?php echo $esp_id; ?>' />
        <input type='hidden' id='opcion_lista_presta' name='opcion_lista_presta' value='1' />
        <input type='hidden' id='opcion_prof' name='opcion_prof' value='0' />
        <input type='hidden' id='centros_cant' name='centros_cant' value='0' />
        <?php if($nd) { ?>
            <div class='sub-content2' style='width:100%;height:200px;overflow:auto;'>
            <table style='width:100%;font-size:11px;'>
                <tr class='tabla_header'><td colspan=5><u>Alerta de Citaciones Recientes</u></td></tr>
                <tr class='tabla_header'>
                    <td>Fecha</td>
                    <td>Hora</td>
                    <td>Especialidad</td>
                    <td>Profesional/Recurso</td>
                    <td>Asignaci&oacute;n</td>
                </tr>
        <?php 
            for($i=0;$i<sizeof($nd);$i++)
            {
                $clase=$i%2==0?'tabla_fila':'tabla_fila2';
                $color=($nd[$i]['esp_id']*1==$n['nom_esp_id']*1)?'red':'black';
                if($nd[$i]['nomd_diag_cod']=='X')
                {
                    $bg_color='background-color:yellow';
                }
                else
                {
                    if($nd[$i]['nomd_diag_cod']=='T')
                        $bg_color='background-color:pink';
                    else
                        $bg_color='';
                }
                print("
                <tr class='$clase' style='color:$color;$bg_color'>
                    <td style='text-align:center;'>".substr($nd[$i]['nom_fecha'],0,10)."</td>
                    <td style='text-align:center;'>".substr($nd[$i]['nomd_hora'],0,5)."</td>
                    <td style='text-align:left;font-weight:bold;'>".$nd[$i]['esp_desc']."</td>
                    <td style='text-align:left;'>".$nd[$i]['doc_paterno']." ".$nd[$i]['doc_materno']." ".$nd[$i]['doc_nombres']."</td>
                    <td style='text-align:center;'>".substr($nd[$i]['nomd_fecha_asigna'],0,16)."</td>
                </tr>");
            }
        ?>
            </table>
            </div>
    <?php } ?>
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:right;width:25%;' class='tabla_fila2'>R.U.N.:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_rut' name='pac_rut' value='<?php echo $pac['pac_rut']; ?>' style='font-size:14px;font-weight:bold;' onBlur='validacion_rut(this);' readonly/>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Pasaporte/ID:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_pasaporte' name='pac_pasaporte' value='<?php echo $pac['pac_pasaporte']; ?>' style='font-size:14px;font-weight:bold;' />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>N&uacute;mero Ficha:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_ficha' name='pac_ficha' value='<?php echo $pac['pac_ficha']; ?>' style='font-size:14px;font-weight:bold;' readonly/>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Nombres:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_nombres' name='pac_nombres' value='<?php echo $pac['pac_nombres']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Apellido Paterno:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_appat' name='pac_appat' value='<?php echo $pac['pac_appat']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Apellido Materno:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_apmat' name='pac_apmat' value='<?php echo $pac['pac_apmat']; ?>' style='font-size:12px;font-weight:bold;' size=30 readonly/>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Fecha de Nacimiento:</td>
                    <td style='tabla_fila'>
                        <?php
                        if($pac['pac_fc_nac']!="")
                        {
                        ?>
                            <input type='text' id='pac_fc_nac' name='pac_fc_nac' value='<?php echo $pac['pac_fc_nac']; ?>' style='text-align:center;' onBlur="validacion_fecha(this);" size=10 readonly/>
                        <?php 
                        }
                        else
                        {
                        ?>
                            <input type='text' id='pac_fc_nac' name='pac_fc_nac' value='<?php echo $pac['pac_fc_nac']; ?>' onBlur="validacion_fecha(this);" style='text-align:center;' size=10 />
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Sexo:</td>
                    <td style='tabla_fila'>
                        <select id='sex_id' onMouseOver="" onMouseOut="">
                        <?php 
                            $sexs=cargar_registros_obj("SELECT * FROM sexo ORDER BY sex_id;", true);
                            for($i=0;$i<sizeof($sexs);$i++)
                            {
                                if($pac['sex_id']==$sexs[$i]['sex_id'])
                                    $sel='SELECTED';
                                else
                                    $sel='';
                                print("<option value='".$sexs[$i]['sex_id']."' $sel >".$sexs[$i]['sex_desc']."</option>");
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Direcci&oacute;n:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_direccion' name='pac_direccion' value='<?php echo $pac['pac_direccion']; ?>' size=40 />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Comuna:</td>
                    <td style='tabla_fila'>
                        <select id='ciud_id' name='ciud_id' style='font-size:14px;'>
                        <?php 
                            $coms=cargar_registros_obj("SELECT * FROM comunas ORDER BY ciud_desc;", true);
                            for($i=0;$i<sizeof($coms);$i++)
                            {
                                if($pac['ciud_id']==$coms[$i]['ciud_id'])
                                    $sel='SELECTED';
                                else
                                    $sel='';
                                print("<option value='".$coms[$i]['ciud_id']."' $sel >".$coms[$i]['ciud_desc']."</option>");
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Tel&eacute;fono:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_fono' name='pac_fono' value='<?php echo $pac['pac_fono']; ?>' size=20 />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Celular:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_celular' name='pac_celular' value='<?php echo $pac['pac_celular']; ?>' size=20 />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Recados:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_recados' name='pac_recados' value='<?php echo $pac['pac_recados']; ?>' size=20 />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>e-mail:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_mail' name='pac_mail' value='<?php echo $pac['pac_mail']; ?>' size=30 />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Ocupaci&oacute;n:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_ocupacion' name='pac_ocupacion' value='<?php echo $pac['pac_ocupacion']; ?>' style='font-size:14px;font-weight:bold;' />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Representante:</td>
                    <td style='tabla_fila'>
                        <input type='text' id='pac_padre' name='pac_padre' value='<?php echo $pac['pac_padre']; ?>' style='font-size:14px;font-weight:bold;' />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Previsi&oacute;n:</td>
                    <td style='tabla_fila'>
                        <select id='prev_id' name='prev_id' style='font-size:14px;' DISABLED>
                        <?php 
                            $prvs=cargar_registros_obj("SELECT * FROM prevision ORDER BY prev_id;", true);
                            for($i=0;$i<sizeof($prvs);$i++)
                            {
                                if($pac['prev_id']==$prvs[$i]['prev_id'])
                                    $sel='SELECTED';
                                else
                                    $sel='';
                                print("<option value='".$prvs[$i]['prev_id']."' $sel >".$prvs[$i]['prev_desc']."</option>");
                            }
                        ?>
                        </select>
                        <i>&Uacute;ltima Actualizaci&oacute;n: <span id='ult_act' style='font-weight:bold;'><?php echo substr($pac['fecha_fonasa'],0,16); ?></span></i> <img src='../../imagenes/ajax-loader1.gif' id='cargar_fonasa' />
                    </td>
                </tr>
            </table>
            <div class='sub-content'>
                <img src='../../iconos/table.png'>
                <b>Prestaciones</b>
                <?php if(_cax(316)) { ?>
                    <span id="span_prestaciones" class="texto_tooltip" style="display: none;"  title="Mostrar Prestaciones Asociadas a la Especialidad" alt="Mostrar Prestaciones Asociadas a la Especialidad" onclick="mostrar_examenes(1);">
                    <i>Mostrar Prestaciones de Citaci&oacute;n</i>
                    </span>
                    <span id="span_examen" class="texto_tooltip" title="Permite Incorporar Ex&aacute;menes a la Nueva Citaci&oacute;n" alt="Permite Incorporar Ex&aacute;menes a la Nueva Citaci&oacute;n" onclick="mostrar_examenes(2);">
                    <i>Incorporar Ex&aacute;menes</i>
                    </span>
                    &nbsp;&nbsp;
                    <span id="cant_examenes">Ex&aacute;menes Solicitados (0)</span>
                <?php } ?>
            </div>
            <div class='sub-content' id='lista_presta'>
                <table id="tb_prestaciones" style='width:100%;font-size:16px;'>
                    <tr class='tabla_header'>
                        <td>&nbsp;</td>
                        <td>C&oacute;digo</td>
                        <td>Descripci&oacute;n</td>
                    </tr>
                    <?php 
                        $esp_desc=$n['esp_desc'];
                        $doc_nombres=$n['doc_nombres'];
                        $doc_id=$n['doc_id'];
                        $nom_motivo=$n['nom_motivo'];
                        //$consulta="SELECT DISTINCT presta_codigo, glosa FROM prestaciones_tipo_atencion LEFT JOIN codigos_prestacion ON presta_codigo=codigo WHERE esp_desc ILIKE '$esp_desc' AND doc_nombres ILIKE '$doc_nombres' AND nom_motivo ILIKE '$nom_motivo';";
                        $consulta="SELECT DISTINCT presta_codigo, COALESCE(glosa,presta_desc)as glosa FROM prestaciones_tipo_atencion LEFT JOIN codigos_prestacion ON presta_codigo=codigo WHERE esp_desc ILIKE '$esp_desc' AND nom_motivo ILIKE '$nom_motivo' AND doc_id=$doc_id AND activado;";

                        $p=cargar_registros_obj($consulta, true);
                        $cods='';
                        if($p)
                        {
							print("<input type='hidden' id='cant_presta' name='cant_presta' value='".sizeof($p)."' />");
                            for($i=0;$i<sizeof($p);$i++)
                            {
                                $clase=($i%2==0?'tabla_fila':'tabla_fila2');
                                print("
                                <tr class='$clase'>
                                    <td>
                                        <center>
                                            <input type='checkbox' id='presta_".$i."_".$p[$i]['presta_codigo']."' name='presta_".$i."_".$p[$i]['presta_codigo']."' value='0'  />
                                        </center>
                                    </td>
                                    <td style='font-weight:bold;text-align:center;'>".$p[$i]['presta_codigo']."</td>
                                    <td>".$p[$i]['glosa']."</td>
                                </tr>");
                                $cods.=$p[$i]['presta_codigo']."|";
                            }
                        } else {
							print("<input type='hidden' id='cant_presta' name='cant_presta' value='0' />");
						}
                        $cods=trim($cods,'|');
                    ?>
                </table>
                <table id="tb_examenes" style='width:100%;font-size:16px;display: none;'>
                <tr>
                    <td>
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
                                <div class='tabbed_content' id='tab_examenes_historia_content' style='height: 150px; overflow:auto;'>

                                </div>
                                <div class='tabbed_content' id='tab_examenes_solicitud_content' style='display:none'>
                                    <div class='sub-content'>
                                        <table style='width:100%;'>
                                            <tr>
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Tipo:</td>
                                                <td class='tabla_fila'>
                                                    <select id='tipo_sol' name='tipo_sol' onChange='fix_fields();'>
                                                        <option value='1'>Local</option>
                                                        <option value='2'>Externa</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Fecha Solicitud:</td>
                                                <td class='tabla_fila'>
                                                    <input type='text' name='fecha_sol_exam' id='fecha_sol_exam' size=10 style='text-align: center;' value='<?php echo date('d-m-Y') ?>' readonly=""/>
                                                    <img src='../../iconos/date_magnify.png' id='fecha_sol_exam_boton'>
                                                </td>
                                            </tr>
                                            <tr id='medi_tr'>
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Medico Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <input type="hidden" id="doc_id_exam" name="doc_id_exam" value="0" size="45"/>
                                                    <input type="text" id="nombre_medico" name="nombre_medico" OnClick="" ondblclick="$('doc_id_exam').value=''; $('nombre_medico').value='';" value="" size="45"/>
                                                    <input type='text' id='rut_medico' name='rut_medico' size=20 style='text-align: center;' value='' disabled>
                                                    <img src="../../iconos/add.png"  style="cursor:pointer;" onClick="agregar_profesional();" title="AGREGAR NUEVO PROFESIONAL EXTERNO" alt="AGREGAR NUEVO PROFESIONAL EXTERNO"/>
                                                </td>
                                            </tr>
                                            <!--
                                            <tr id='unid_tr'>
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Especialidad Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <select id='esp_exam_solicita' name='esp_exam_solicita' onChange=''>
                                                        <option value=-1 SELECTED>Seleccionar Especialidad......</option>
                                                        <?php echo $espechtml; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            -->
                                            <!--
                                            <tr id="serv_tr">
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Servicio Clínico Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <select id='serv_exam_solicita' name='serv_exam_solicita' onChange=''>
                                                        <option value=-1 SELECTED>Seleccionar Servicio......</option>
                                                        <?php echo $servhtml; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            -->
                                            <tr id="serv_tr">
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Servicio Clínico Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <select id='serv_exam_solicita' name='serv_exam_solicita' onChange='actualizar_centro();'>
                                                        <option value="-1" SELECTED>Seleccionar Servicio......</option>
                                                        <?php echo $servhtml; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="serv_centros_tr">
												<td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">Centros Clínico Solicitante:</td>
                                                <td id="td_centros" class='tabla_fila'>
                                                    <select id='centro_exam_solicita' name='centro_exam_solicita' onChange=''>
                                                        <option value="" SELECTED>Seleccionar Centro......</option>
                                                    </select>
                                                </td>
											</tr>
											<tr id='tr_dau'>
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2">DAU:</td>
                                                <td class='tabla_fila'>
                                                    <input type="text" id="txt_dau" name="txt_dau" value="" size="10"/>
                                                </td>
                                            </tr>
                                            <tr id='inst_tr' style='display:none;'>
                                                <td style='text-align:right;' class='tabla_fila2'>Instituci&oacute;n Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <input type='hidden' id='inst_id_sol' name='inst_id_sol' value='' />
                                                    <input type='text' id='inst_desc_sol' name='inst_desc_sol' value='' size=35 onDblClick='$("inst_id_sol").value=""; $("inst_desc_sol").value="";' />
                                                </td>
                                            </tr>
                                            <tr id='espe_tr' style='display:none;'>
                                                <td style='text-align:right;' class='tabla_fila2'>Especialidad Solicitante:</td>
                                                <td class='tabla_fila'>
                                                    <input type='hidden' id='esp_id2_sol' name='esp_id2_sol' value='' />
                                                    <input type='text' id='esp_desc2_sol' name='esp_desc2_sol' value='' size=35 onDblClick='$("esp_id2_sol").value=""; $("esp_desc2_sol").value="";' />
                                                </td>
                                            </tr>
                                            <tr id='prof_tr' style='display:none;'>
                                                <td style='text-align:right;' class='tabla_fila2'>
                                                    Profesional Solicitante:
                                                </td>
                                                <td class='tabla_fila'>
													<table>
														<tr>
															<td>
																<input type='hidden' id='prof_id_sol' name='prof_id_sol' value='' />
															</td>
															<td>
																<input type='text' id='prof_rut_sol' name='prof_rut_sol' value='' size=15 style='text-align:center;' DISABLED />
															</td>
															<td>
																<input type='text' id='prof_nombre_sol' name='prof_nombre_sol' value='' size=35 onDblClick='$("prof_id_sol").value=""; $("prof_rut_sol").value=""; $("prof_nombre_sol").value="";' />
															</td>
															<td>
																<center><img src="../../iconos/add.png"  style="cursor:pointer;" onClick="agregar_profesional();" title="AGREGAR NUEVO PROFESIONAL EXTERNO" alt="AGREGAR NUEVO PROFESIONAL EXTERNO"/></center>
															</td>
														</tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr id='add_prof_tr' style='display:none;'>
												<td colspan=2>
													<div class="sub-content2">
															<div class="sub-content" id="div_titulo_medico"><b>Profesional Interno:</b> Ingreso de nuevos datos del m&eacute;dico</div>
															<table border=0>
																<tr>
																	<td style='font-weight: bold;text-align:center;'>R.U.T.</td>
																	<td style='text-align:center;'>Nombre(s)</td>
																	<td style='text-align:center;'>Apellido Paterno</td>
																	<td style='text-align:center;'>Apellido Materno</td>
																</tr>
																<tr>
																	<td width=100>
																		<input type='text' id='prof_rut' name='prof_rut' size=11 style='text-align: center;font-size:13px;' onKeyUp='if(event.which==13){this.value=this.value.toUpperCase();verificar_rut_prof();$("prof_nombres").focus();}' onBlur='verificar_rut_prof();' maxlength=11>
																	</td>
																	<td>
																		<input type='text' id='prof_nombres' name='prof_nombres' size='22' onKeyUp='if(event.which==8 && this.value.length==0) $("prof_rut").focus();' maxlength=100>
																	</td>
																	<td>
																		<input type='text' id='prof_paterno' name='prof_paterno' size='22' onKeyUp='if(event.which==8 && this.value.length==0) $("prof_nombre").focus();' maxlength=50>
																	</td>
																	<td>
																		<input type='text' id='prof_materno' name='prof_materno' size='22' onKeyUp='if(event.which==8 && this.value.length==0) $("prof_paterno").focus();' maxlength=50>
																	</td>
																	<td>
																		<input type='button'  id='btn_cancelar_profe' name='btn_cancelar_profe' Onclick='cancelar_profesional();' value='Cancelar'/>
																	</td>
																</tr>
															</table>
													</div>
												</td>
                                            </tr>
                                            <tr id="observ_tr">
                                                <td style="text-align:right;width:150px;white-space:nowrap;" class="tabla_fila2" valign="top">Observaci&oacute;n General:</td>
                                                <td class='tabla_fila'>
                                                    <textarea id="obsgeneral" name="obsgeneral" style="width:50%;height:45px;"></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class='sub-content'>
                                            <table border='0'>
                                                <tr>
                                                    <td colspan="7">
                                                        <table>
                                                        <tr>
                                                            <td>
                                                                <select id='esp_exam' name='esp_exam' onChange='actualizar();'>
                                                                    <option value='-1' selected>Seleccionar Tipo Ex&aacute;men</option>
                                                                    <option value='6117' >IMAGENOLOG&Iacute;A HME</option>
                                                                    <option value='6120' >LABORATORIO HME</option>
                                                                </select>
                                                            </td>
                                                            <td id="td_grupo">
                                                                <select id='grupo_exam' name='grupo_exam' onChange='listar_prestaciones_examen();' disabled>
                                                                    <option value='-1' selected>Seleccionar grupo ex&aacute;men....</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <center>
                                                                    <img style="cursor: pointer;" alt="Visualizar lista de ex&aacute;menes seg&uacute;n filtro" title="Visualizar lista de ex&aacute;menes seg&uacute;n filtro" onclick="lista_examenes();" src='../../iconos/page_find.png' />
                                                                </center>
                                                            </td>
                                                            <td>
                                                                <center>
                                                                    <input type='button' value='[ Limpiar Lista Ex&aacute;menes ]' onClick='limpiar_examenes();'>
                                                                </center>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <center><img src='../../iconos/add.png' /></center>
                                                    </td>
                                                    <td style="width: 85px;">C&oacute;digo Prest.:</td>
                                                    <td>
                                                        <input type='hidden' id='cod_prestacion' name='cod_prestacion' value='0' />
                                                        <input type='hidden' id='desc_presta_examen' name='desc_presta_examen' value='' />
                                                        <input type='hidden' id='pc_id_examen' name='pc_id_examen' value='0' />
                                                        <input type='hidden' id='kit_exam' name='kit_exam' value='0' />
                                                        <input type='text' id='cod_presta_examen' name='cod_presta_examen' size=14 />
                                                    </td>
                                                    <td>Cant.:</td>
                                                    <td>
                                                        <input type='text' id='cantidad_examen' name='cantidad_examen' onKeyUp="if(event.which==13) {$('obs_examen').select(); $('obs_examen').focus();}" size=3 value='1'/>
                                                    </td>
                                                    <td>
                                                        Observaci&oacute;n:
                                                    </td>
                                                    <td>
                                                        <input type='text' id='obs_examen' name='obs_examen' onKeyUp="if(event.which==13) agregar_prestacion_examen(0,2,($('kit_exam').value*1));" size=50 />
                                                        &nbsp;&nbsp;
                                                        <input type='button' value='[ Agregar ]' onClick="agregar_prestacion_examen(0,2,($('kit_exam').value*1));" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="7" id="td_nom_exam" style="text-align: left;width: 100%;"></td>
                                                </tr>
                                            </table>
                                        </div>
                                        <table style="width: 100%">
                                            <tr class="tabla_header" style="text-align:left;">
                                                <td><b>Ex&aacute;menes solicitados</b></td>
                                            </tr>
                                        </table>
                                        <div class='sub-content2' id='lista_presta_examen' style='height:120px;overflow:auto;'>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
            </div>
            <center>
                <input type='button' id='guarda' name='guarda' value='--[[ Guardar Datos de Paciente... ]]--' onClick='guardar_cupo();' style='font-size:18px;margin:5px;' />
            </center>
        </div>
        <input type='hidden' id='codigos' name='codigos' value='<?php echo $cods; ?>' />
    </form>
    </body>
</html>
<script type="text/javascript">
    
    Calendar.setup({
    inputField     :    'fecha_sol_exam',         // id of the input field
    ifFormat       :    '%d/%m/%Y',       // format of the input field
    showsTime      :    false,
    button          :   'fecha_sol_exam_boton'
    });
    
    seleccionar_doc=function(datos_medico) {
        $('doc_id_exam').value=datos_medico[3];
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
    }, 'autocomplete', 350, 200, 250, 1, 2, seleccionar_doc);
    
    seleccionar_prestacion_examen = function(presta_examen) {
        $('cod_prestacion').value=presta_examen[0];
        $('cod_presta_examen').value=presta_examen[1];
        $('desc_presta_examen').value=(presta_examen[2].unescapeHTML()).toUpperCase();;
        $('pc_id_examen').value=presta_examen[5]*1;
        $('kit_exam').value=(presta_examen[6]*1);
        $('td_nom_exam').innerHTML='<b>&nbsp;'+(presta_examen[2].unescapeHTML()).toUpperCase();+'</b>';
        $('obs_examen').value='';
        $('cantidad_examen').value='1';
        $('cantidad_examen').select();
        $('cantidad_examen').focus();
    }
    
    autocompletar_prestaciones = new AutoComplete('cod_presta_examen','../../autocompletar_sql.php',
    function(){
    if($('cod_presta_examen').value.length<3)return false;
    if($('esp_exam').value=='-1') {
        alert('Debe seleccionar tipo de examen');
        $('cod_presta_examen').value='';
        return false;
    }
    if($('grupo_exam').value=='-1') {
        alert('Debe seleccionar Grupo de examen');
        $('cod_presta_examen').value='';
        return false;
    }
    
    return {
        method: 'get',
        parameters: 'tipo=proc_prestacion_examen&cod_presta='+encodeURIComponent($('cod_presta_examen').value)+'&esp_id='+encodeURIComponent($('esp_exam').value)+'&agenda=1&grupo_exam='+encodeURIComponent($('grupo_exam').value)
    }
    }, 'autocomplete', 700, 300, 150, 1, 3, seleccionar_prestacion_examen);
    
    
    seleccionar_inst1 = function(d) {
        $('inst_id_sol').value=d[0];
        $('inst_desc_sol').value=d[2].unescapeHTML();
    }
    
    autocompletar_institucion1 = new AutoComplete(
    'inst_desc_sol', 
    '../../autocompletar_sql.php',
    function() {
    if($('inst_desc_sol').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('inst_desc_sol').value)
    }
    }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);
    
    
    seleccionar_especialidad2 = function(d) {
        $('esp_id2_sol').value=d[0];
        $('esp_desc2_sol').value=d[2].unescapeHTML();
    }
    
    autocompletar_especialidades2 = new AutoComplete('esp_desc2_sol', '../../autocompletar_sql.php',
    function() {
    if($('esp_desc2_sol').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=especialidad_sigges&cadena='+encodeURIComponent($('esp_desc2_sol').value)
    }
    }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad2);
    
    
    ingreso_rut2=function(datos_medico) {
        $('prof_id_sol').value=datos_medico[0];
        $('prof_rut_sol').value=datos_medico[1];
        $('prof_nombre_sol').value=datos_medico[2].unescapeHTML();
    }
       
    
    autocompletar_profesionales = new AutoComplete(
    'prof_nombre_sol', 
    '../../autocompletar_sql.php',
    function() {
    if($('prof_nombre_sol').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=profesional_externo&profesional='+encodeURIComponent($('prof_nombre_sol').value)
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut2);

</script>
