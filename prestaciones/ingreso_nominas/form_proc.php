<?php
    require_once('../../conectar_db.php');
    $ramas = cargar_registros_obj("SELECT * FROM patologias_auge_ramas ORDER BY rama_nombre");

    $nomd_id=$_GET['nomd_id']*1;
    $consulta="SELECT
    pacientes.*, nomina_detalle.*, nomina.*, diag_desc,
    date_part('year',age(pac_fc_nac)) as edad,nomd_ges,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,
    date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,
    date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
    FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN pacientes USING (pac_id)
    LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
    LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
    WHERE nomd_id=$nomd_id  ORDER BY nomd_folio";




    $ndet=cargar_registro($consulta);

    $nom_id=$ndet['nom_id']*1;
    $pac_id=$ndet['pac_id']*1;
    $esp_id=$ndet['nom_esp_id']*1;
    $doc_id=$ndet['nom_doc_id']*1;
    $ges=$ndet['nomd_ges'];
    $nom_motivo=$ndet['nom_motivo'];




    if($ndet['nomd_diag_cod']=='X' OR $ndet['nomd_diag_cod']=='T' OR $ndet['nomd_diag_cod']=='N')
        $motivo_enabled='';
    else
        $motivo_enabled='';


    $alergias=cargar_registros_obj("SELECT * FROM paciente_alergias WHERE pac_id=$pac_id");

    if($alergias)
        $al_count=sizeof($alergias);
    else
        $al_count=0;

    $contrarreferencia="false";
    $recetas=false;
    $especialidad_reg=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id");
    if($especialidad_reg)
    {
        if($especialidad_reg['esp_receta']=="t")
        {
            $recetas=true;
        }
        else
        {
            $recetas=false;
        }
        if($especialidad_reg['esp_contrarreferencia']=="t") {
            $contrarreferencia="true";
        }
    }



    $proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=$esp_id");

    $presta=cargar_registros_obj("SELECT * FROM procedimiento_codigo LEFT JOIN codigos_prestacion ON pc_codigo=codigo WHERE esp_id=$esp_id", true);



    $p=cargar_registros_obj("SELECT * FROM nomina_detalle_prestaciones JOIN codigos_prestacion ON nomdp_codigo=codigo LEFT JOIN procedimiento_codigo USING (pc_id)
    WHERE nomd_id=$nomd_id ORDER BY nomdp_codigo", true);



    $tmp=Array();


    if($p)
    {
        for($i=0;$i<sizeof($p);$i++)
        {
            $tmp[$i]->codigo=$p[$i]['nomdp_codigo'];
            $tmp[$i]->cantidad=$p[$i]['nomdp_cantidad']*1;

            if($p[$i]['pc_desc']!='')
                $tmp[$i]->desc='<b>['.$p[$i]['pc_desc'].']</b> '.$p[$i]['glosa'];
            else
                $tmp[$i]->desc=$p[$i]['glosa'];

            $tmp[$i]->pc_id=$p[$i]['pc_id']*1;

	}
    }


    if($proc['esp_orden_atencion']=='t')
    {
        $oa=cargar_registros_obj("SELECT * FROM orden_atencion WHERE (oa_pac_id=$pac_id AND oa_estado=1) OR oa_id=".$ndet['oa_id']);
	$oahtml='';
	if($oa)
        {
            for($i=0;$i<sizeof($oa);$i++)
            {
                if($oa[$i]['oa_id']==$ndet['oa_id'])
                    $sel='SELECTED';
		else
                    $sel='';

		if($oa[$i]['oa_folio']!=0)
                    $oahtml.='<option value="'.$oa[$i]['oa_id'].'" '.$sel.' >Nro. Folio: '.$oa[$i]['oa_folio'].'</option>';
                else
                    $oahtml.='<option value="'.$oa[$i]['oa_id'].'" '.$sel.' >Cod. Interno: #'.$oa[$i]['oa_id'].'</option>';
            }
        } else {


        }
    }

    $obs=cargar_registros_obj("
    SELECT nom_fecha,
    doc_nombres||' '||doc_paterno||' '||doc_materno as doc,
    nomd_observaciones FROM nomina
    LEFT JOIN nomina_detalle USING(nom_id)
    LEFT JOIN doctores ON doc_id=nom_doc_id
    WHERE pac_id=$pac_id AND nomd_observaciones!=''
    ", true);

    $observaciones=array();

    for($i=0;$i<sizeof($obs);$i++)
    {
        $n=sizeof($observaciones);
	$observaciones[$n]->fecha=$obs[$i]['nomd_fecha'];
	$observaciones[$n]->doc=htmlentities($obs[$i]['doc']);
	$observaciones[$n]->observacion=$obs[$i]['nomd_observaciones'];
    }


    $consulta="SELECT * FROM solicitud_examen_detalle
    left join procedimiento_codigo on sol_examd_cod_presta=pc_id
    WHERE sol_examd_nomd_id=$nomd_id order by sol_examd_id";
    $reg_examenes=cargar_registros_obj($consulta, true);
    if(!$reg_examenes) {
        $reg_examenes=false;
        $cant_exam=0;
        $reg_solicitud_examen=false;
    }else {
        $cant_exam=count($reg_examenes);
        $consulta="SELECT solicitud_examen.*,esp1.esp_id as esp1_id,esp1.esp_desc as esp1_desc,
        nd.nom_id, esp2.esp_id as esp2_id, esp2.esp_desc as esp2_desc,
        doc1.doc_id as doc1_id, (doc1.doc_nombres || ' ' || doc1.doc_paterno ||  ' ' || doc1.doc_materno)as doc1_nombre,doc1.doc_rut as doc1_rut,
        doc2.doc_id as doc2_id, (doc2.doc_nombres || ' ' || doc2.doc_paterno ||  ' ' || doc2.doc_materno)as doc2_nombre,doc2.doc_rut as doc2_rut,
	cc1.centro_ruta as cc1_centro_ruta,cc1.centro_nombre as cc1_centro_nombre,
	cc2.centro_ruta as cc2_centro_ruta,cc2.centro_nombre as cc2_centro_nombre
        FROM solicitud_examen
        left join especialidades esp1 on esp1.esp_id=sol_esp_solicita
        left join nomina_detalle nd on nd.nomd_id=sol_nomd_id_original
        left join nomina on nomina.nom_id=nd.nom_id
        left join especialidades esp2 on esp2.esp_id=nom_esp_id
        left join doctores doc1 on doc1.doc_id=sol_doc_id
        left join doctores doc2 on doc2.doc_id=nom_doc_id
        left join centro_costo cc1 on cc1.centro_ruta=sol_centro_ruta
        left join centro_costo cc2 on cc2.centro_ruta=esp2.esp_centro_ruta
        where sol_exam_id=".$reg_examenes[0]['sol_examd_solexam_id']."";
        //print($consulta);
        $reg_solicitud_examen=cargar_registro($consulta, true);
        if(!$reg_solicitud_examen){
            $reg_solicitud_examen=false;
            $serv_sol_centro_ruta="";
            $serv_sol_centro_nombre="";
            $esp_sol="";
            $esp_soldesc="";
            $doc_sol_id="";
            $doc_sol_rut="";
            $doc_sol_nombre="";
            $obs_exam="";
        } else {
            if($reg_solicitud_examen['sol_nomd_id_original']=="" or $reg_solicitud_examen['sol_nomd_id_original']==null or $reg_solicitud_examen['sol_nomd_id_original']==0){
                $serv_sol_centro_ruta=$reg_solicitud_examen['cc1_centro_ruta'];
                $serv_sol_centro_nombre=$reg_solicitud_examen['cc1_centro_nombre'];
                $esp_sol=$reg_solicitud_examen['esp1_id'];
                $esp_soldesc=$reg_solicitud_examen['esp1_desc'];
                $doc_sol_id=$reg_solicitud_examen['doc1_id'];
                $doc_sol_rut=$reg_solicitud_examen['doc1_rut'];
                $doc_sol_nombre=$reg_solicitud_examen['doc1_nombre'];
                $obs_exam=$reg_solicitud_examen['sol_obsgeneral'];
            } else {
                $serv_sol_centro_ruta=$reg_solicitud_examen['cc2_centro_ruta'];
                $serv_sol_centro_nombre=$reg_solicitud_examen['cc2_centro_nombre'];
                $esp_sol=$reg_solicitud_examen['esp2_id'];
                $esp_soldesc=$reg_solicitud_examen['esp2_desc'];
                $doc_sol_id=$reg_solicitud_examen['doc2_id'];
                $doc_sol_rut=$reg_solicitud_examen['doc2_rut'];
                $doc_sol_nombre=$reg_solicitud_examen['doc2_nombre'];
                $obs_exam="";
            }
        }
    }

    /*
    $consulta="select * from diagnosticos where diag_cod in (
    select distinct  trim(split_part(nomd_diag,'|',1))cod_diag from nomina_detalle
    join nomina using (nom_id)
    where nomd_diag ilike '%|%'
    and nom_esp_id=$esp_id and nom_doc_id=$doc_id
    )";
    $reg_diag_frecuentes=cargar_registros_obj($consulta, true);
    if(!$reg_diag_frecuentes)
    {
        $reg_diag_frecuentes=false;
    }
    */





?>
<html>
    <title>Registro Cl&iacute;nico de Atenci&oacute;n</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        var ver='';
        var presta=new Array();


        ver_vigencia = function(fila)
        {
            params= 'recetad_id='+encodeURIComponent(fila);
            top=Math.round(screen.height/2)-150;
            left=Math.round(screen.width/2)-200;
            new_win =
            window.open('ver_vigencia.php?'+
            params,
            'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=800, height=300, '+
            'top='+top+', left='+left);
            new_win.focus();
        }

        ver_vademecum = function(id_vademecum)
        {
            params= 'id_vademecum='+encodeURIComponent(id_vademecum);
            top=Math.round(screen.height/2)-300;
            left=Math.round(screen.width/2)-200;
            new_win =
            window.open('../../conectores/vademecum/visualizar_vademecum.php?'+
            params,
            'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=800, height=600, '+
            'top='+top+', left='+left);
            new_win.focus();
        }

        alertas_vademecum = function()
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-200;
            $('nomd_diag_cod').disabled=false;
            new_win =
            window.open('../../conectores/vademecum/alertas_vademecum.php?pac_id=<?php echo $pac_id; ?>&'+$('art_id').serialize()+'&'+$('nomd_diag_cod').serialize()+'&'+serializar_objetos($('datos_vademecum')),
            'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=800, height=500, '+
            'top='+top+', left='+left);
            $('nomd_diag_cod').disabled=true;
            new_win.focus();
        }

        ver_alergias = function()
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-200;
            new_win =
            window.open('../../conectores/vademecum/form_alergias.php?pac_id=<?php echo $pac_id; ?>',
            'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=800, height=500, '+
            'top='+top+', left='+left);
            new_win.focus();
        }

        ver_vigencia2 = function(recetad_id)
        {
            var win = new Window("recetad_vigencia", {className: "alphacube",
            top:40, left:0,
            width: 500, height: 200,
            title: 'Vigencia de la Prescripci&oacute;n',
            minWidth: 500, minHeight: 200,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: false });

            //console.log(encodeURIComponent(recetad_id));
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            win.setAjaxContent('ver_vigencia.php',
            {
                method: 'get',
                parameters: 'recetad_id='+encodeURIComponent(recetad_id),
                evalScripts: true
            });

            win.setDestroyOnClose();
            win.showCenter(false, true);
            win.show();
            return win;
        }

	function limpiar_oa()
        {

            $('centro_ruta_oa').value='';
            $('centro_nombre_oa').value='';
            $('esp_id_oa').value=0;
            $('esp_desc_oa').value='';
            $('doc_id_oa').value=0;
            $('doc_rut_oa').value='';
            $('doc_nombre_oa').value='';

            $('inst_id_oa').value=0;
            $('inst_desc_oa').value='';
            $('esp_id2_oa').value=0;
            $('esp_desc2_oa').value='';
            $('prof_id_oa').value=0;
            $('prof_rut_oa').value='';
            $('prof_nombre_oa').value='';
            $('obsgeneral').value='';
        }

	function cargar_oa()
        {
            var myAjax=new Ajax.Request('cargar_oa.php',
            {
                method:'post',
                parameters:$('oa_id').serialize()+'&'+$('nomd_id').serialize(),
                onComplete:function(r)
                {
                    var datos=r.responseText.evalJSON(true);
                    if(!datos)
                    {
                        $('fecha_oa').value='<?php echo date('d/m/Y'); ?>';
			$('tipo_oa').value=1;
                        limpiar_oa();
			fix_fields();
                        validacion_fecha($('fecha_oa'));
                        $('tipo_oa').disabled=false;
                        return;
                    }
                    $('fecha_oa').value=datos.oa_fecha;
                    limpiar_oa();
                    if(datos.doc_id!=0)
                    {
                        //console.log('interna');
			$('tipo_oa').value=1;
			$('centro_ruta_oa').value=datos.oa_centro_ruta;
                        $('centro_nombre_oa').value=datos.centro_nombre;
                        $('esp_id_oa').value=datos.oa_especialidad;
                        $('esp_desc_oa').value=datos.esp_desc.unescapeHTML();
                        $('doc_id_oa').value=datos.oa_doc_id;
                        $('doc_rut_oa').value=datos.doc_rut;
                        $('doc_nombre_oa').value=(datos.doc_paterno+' '+datos.doc_materno+' '+datos.doc_nombres).unescapeHTML();
                    }
                    else
                    {
                        //console.log('externa');
                        $('tipo_oa').value=2;
                        $('inst_id_oa').value=datos.inst_id;
                        $('inst_desc_oa').value=datos.inst_nombre.unescapeHTML();
                        $('esp_id2_oa').value=datos.esp_id;
                        $('esp_desc2_oa').value=datos.esp_desc.unescapeHTML();
                        $('prof_id_oa').value=datos.prof_id;
                        $('prof_rut_oa').value=datos.prof_rut;
                        $('prof_nombre_oa').value=(datos.prof_paterno+' '+datos.prof_materno+' '+datos.prof_nombres).unescapeHTML();
                    }
                    if(datos.oa_id!=0 || datos.inter_id!=0)
                        $('tipo_oa').disabled=true;
                    else
                        $('tipo_oa').disabled=false;

                    fix_fields();
                    validacion_fecha($('fecha_oa'));
                }
            });
        }

	function mensaje_falso()
        {
            <?php if($proc['esp_orden_atencion']=='t')
            {
            ?>
            /*
            if(!validacion_fecha($('fecha_oa')))
            {
                alert(('Debe ingresar una fecha de solicitud v&aacute;lida.').unescapeHTML());
		return;
            }

            if(!$('centro_nombre_oa').disabled && $('centro_ruta_oa').value=='')
            {
                alert('Debe seleccionar el servicio solicitante.');
		return;
            }

            if(!$('esp_desc_oa').disabled && $('esp_id_oa').value*1==0)
            {
                alert('Debe seleccionar la unidad solicitante.');
		return;
            }

            if(!$('doc_nombre_oa').disabled && $('doc_id_oa').value*1==0)
            {
                alert(('Debe seleccionar el m&eacute;dico solicitante.').unescapeHTML());
		return;
            }
            */
            <?php
            }
            ?>
            <?php
            if($presta OR sizeof($presta)>1)
            {
            ?>
                //if(presta.length==0){
                    //alert(('Debe ingresar las prestaciones realizadas.').unescapeHTML());
                    //return;
		//}

            <?php
            }
            ?>
            var params='&presta='+encodeURIComponent(presta.toJSON());
            params+='&meds='+encodeURIComponent(medicamentos.toJSON());
            params+='&obs='+$('observacion').value;
            $('nomd_diag_cod').disabled=false;
            if(!confirm(('&#191;Est&aacute; seguro que desea guardar el Registro?').unescapeHTML()))
                return;

            alert('Registro de DEMOSTRACION finalizado exitosamente.');
            window.close();
        }


        abrir_detalle_nomina=function(nomd_id)
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            new_win_pdf = window.open('nomina_detalle.php?nomd_id='+nomd_id,
            'win_nomina_pdf', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
            'top='+top+', left='+left);
            new_win_pdf.focus();
        }

        function guardar_prestacion()
        {
            if(($('select_atencion').value*1)=='-1')
            {
                alert(("Debe Ingresar Estado de Atenci\u00f3n").unescapeHTML());
                $('select_atencion').value='';
                $('select_atencion').focus();
                return;
            }
            if($('select_atencion').value=='OK' || $('select_atencion').value=='ALTA')
            {
                if(($('atencion_nueva').value*1)==1)
                {
                    if(($('nomd_prot_ficha').value*1)=='0')
                    {
                        alert(("Debe Indicar Datos de Pertinencia").unescapeHTML());
                        $('nomd_prot_ficha').value='';
                        $('nomd_prot_ficha').focus();
                        return;
                    }
                    if(($('nomd_tiempo_ficha').value*1)=='0')
                    {
                        alert(("Debe Indicar Datos de Pertinencia").unescapeHTML());
                        $('nomd_tiempo_ficha').value='';
                        $('nomd_tiempo_ficha').focus();
                        return;
                    }
                }
                <?php
                if($proc['esp_orden_atencion']!='t')
                {
                ?>
                    if($('nomd_diag_cod').value=='')
                    {
                        alert(("Debe Ingresar Diagnostico CIE10").unescapeHTML());
                        $('nomd_diagnostico').value='';
                        $('nomd_diagnostico').focus();
                        return;
                    }
                <?php
                }
                ?>

            }
            else
            {
                if($('select_atencion').value=='N')
                {
                    if($('nomd_codigo_no_atiende').value=='OK' || $('nomd_codigo_no_atiende').value=='ALTA')
                    {
                        sel_motivo_atencion($('nomd_id').value);
                        return;
                    }

                }
            }


            <?php
            if($proc['esp_orden_atencion']=='t')
            {
            ?>
                /*
                if(!validacion_fecha($('fecha_oa')))
                {
                    alert(('Debe ingresar una fecha de solicitud v&aacute;lida.').unescapeHTML()));
                    return;
                }
                if(!$('centro_nombre_oa').disabled && $('centro_ruta_oa').value=='')
                {
                    alert('Debe seleccionar el servicio solicitante.');
                    return;
                }
                if(!$('esp_desc_oa').disabled && $('esp_id_oa').value*1==0)
                {
                    alert('Debe seleccionar la unidad solicitante.');
                    return;
                }
                if(!$('doc_nombre_oa').disabled && $('doc_id_oa').value*1==0)
                {
                    alert(('Debe seleccionar el m&eacute;dico solicitante.').unescapeHTML());
                    return;
                }
                */
            <?php
            }
            ?>
            <?php
            if($presta OR sizeof($presta)>1)
            {
            ?>
                //if(presta.length==0) {
                //alert(('Debe ingresar las prestaciones realizadas.').unescapeHTML());
                //return;
                //}
            <?php
            }
            ?>
            var params='&presta='+encodeURIComponent(presta.toJSON());
            params+='&meds='+encodeURIComponent(medicamentos.toJSON());
            if(examenes)
            {
                params+='&examenes='+encodeURIComponent(examenes.toJSON());
            }
            //params+='&obs='+$('observacion').value;
            params+='&ges='+$('ges').value;
            <?php
            if($nom_motivo=="Consulta Nueva")
            {
            ?>
                //params+='&nomd_prot_ficha='+$('nomd_prot_ficha').value;
                //params+='&nomd_tiempo_ficha='+$('nomd_tiempo_ficha').value;
            <?php
            }
            ?>

            $('nomd_diag_cod').disabled=false;
            /* if(!confirm(('&#191;Est&aacute; seguro que desea guardar el Registro?').unescapeHTML()))
                return;
            */
            var myAjax=new Ajax.Request('sql_procedimientos.php',
            {
                method:'post',
                async:false,
                parameters:$('datos').serialize()+params,
                onComplete: function(data)
                {
                    if(medicamentos.length!=0)
                    {
                        recetas=data.responseText.evalJSON(true);
                        alert('Registro completado exitosamente.\nRecetas realizadas Nro:['+recetas+']');
                        medicamentos.length=0;
                        $("certificar_receta").win_obj.close();
                    }
                    else
                    {
                        alert('Registro completado exitosamente.');
                    }
                    if($('select_atencion').value!='ALTA') {
                        abrir_detalle_nomina($('nomd_id').value);
                    }
                    //recetas=data.responseText.evalJSON(true);
                    //var recetas = recetas.split("|");
                    //alert('Registro completado exitosamente.');
                    //if(medicamentos.length!=0)
                    //{
                    //    $("certificar_receta").win_obj.close();
                    //}
                    //var fn=window.opener.actualizar_nomina.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });

            window.opener.parent.abrir_nomina('<?php echo "AGENDA".$nom_id;?>', 1);
            <?php
            if($recetas)
            {
            ?>
            $('art_id').value=0;
            $('art_codigo').value='';
            $('art_nombre').value='';
            $('codigo').value='';
            $('remed').innerHTML='';
            $('campo_unidad').innerHTML='';
            $('cant').value='';
            $('manana').style.display='none';
            $('cant2').value='';
            $('cant2').style.display='none';
            $('tarde').style.display='none';
            $('cant3').value='';
            $('cant3').style.display='none';
            $('noche').style.display='none';
            $('horas').value='';
            $('horas').disabled=false;
            $('dias').value='';
            $('lista_meds').innerHTML="";
            <?php
            }
            ?>

            $('observacion').value='';

            cargar_ficha();
            cargar_observaciones();
            //window.close();

            if($('select_atencion').value!='ALTA') {
                window.close();
            }
            else {
                if($('contrarreferencia').value=='true') {
                    var win = new Window("popup_contrarreferencia",
                    {
                        className: "alphacube", top:40, left:0, width: 650, height: 600,
                        title: '<img src="../../iconos/page_white_link.png"> HOJA DE CONTRARREFERENCIA DE ESPECIALIDADES',
                        minWidth: 500, minHeight: 400,
                        maximizable: false, minimizable: false,
                        wiredDrag: true, draggable: true,
                        closable: true, resizable: false
                    });
                    win.setDestroyOnClose();
                    win.setAjaxContent('hoja_contrarreferencia.php',
                    {
                        method: 'post',
                        async:false,
                        dataType: 'json',
                        parameters: 'nomd_id='+$('nomd_id').value+'&pac_id='+$('pac_id').value+'&esp_id='+$('esp_id').value,
                    });
                    $("popup_contrarreferencia").win_obj=win;
                    win.showCenter();
                    win.show(true);
                    //window.close();
                }
            }
        }


        certificar_receta = function()
        {
            /*
            if($('codigo').value!='')
            {
                alert(('No ha agregado '+$("remed").value+' a la receta.').unescapeHTML());
                return;
            }
            /*
            if(medicamentos.length==0)
            {
                alert('No ha seleccionado medicamentos.');
                return;
            }
            */
            <?php
            if($reg_examenes)
            {
            ?>
                examenes=<?php echo json_encode($reg_examenes); ?>;
                var chk_examenes=false;
                //var cant_chk=0;
                for(var x=0;x<examenes.length;x++)
                {
                    if($j('#chk_realizado_'+x).is(':checked'))
                    {
                        chk_examenes=true;
                    }
                }
                if(chk_examenes==false)
                {
                    confirmar=confirm(('Presenta examenes solicitados no confirmados como realizados;\nEsta seguro de guardar los datos del paciente?').unescapeHTML());
                    if(!confirmar)
                    {
                        return;
                    }
                }
            <?php
            }
            else
            {
            ?>
                examenes=false;
            <?php
            }
            ?>
            if(medicamentos.length!=0)
            {
                var win = new Window("certificar_receta", {className: "alphacube",
                top:20, left:0,
                width: 700, height: 250,
                title: '<center><img src="../../iconos/page_key.png"> Certificar Emisi&oacute;n de Receta</center>',
                minWidth: 700, minHeight: 250,
                maximizable: false, minimizable: false,
                wiredDrag: true, resizable: true });
                win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
                var params='meds='+encodeURIComponent(medicamentos.toJSON());
                win.setAjaxContent('certificar_receta.php',
                {
                    method: 'post',
                    evalScripts: true,
                    parameters: params,
                    async:false,
                    onComplete: function()
                    {
                        //var fn=window.opener.guardar_prestacion.bind(window.opener);
                        //fn();
                        //window.close();
                    }
                });
                $("certificar_receta").win_obj=win;
                win.setDestroyOnClose();
                win.showCenter();
                win.show(true);
            } else {
                guardar_prestacion();
            }
        }

        fix_fields=function()
        {
            var t=$('tipo_oa').value*1;
            if(t==1)
            {
                $('serv_tr').style.display='';
                $('unid_tr').style.display='';
                $('medi_tr').style.display='';
                $('observaciones_tr').style.display='';
                $('inst_tr').style.display='none';
                $('espe_tr').style.display='none';
                $('prof_tr').style.display='none';


            }
            else
            {
                $('serv_tr').style.display='none';
                $('unid_tr').style.display='none';
                $('medi_tr').style.display='none';
                $('observaciones_tr').style.display='none';
                $('inst_tr').style.display='';
                $('espe_tr').style.display='';
                $('prof_tr').style.display='';

            }
        }

        function ver_examenes(url )
        {
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            var run=$('pac_rut').value.split('-');
            new_win = window.open('http://10.3.107.20/busquedarut.php?rut='+run[0],
            'win_examenes', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=yes, maximized=no, '+
            'top='+top+', left='+left+' width=500, height=445');
            new_win.focus();
        }

        sol_examenes= function()
        {
            var win = new Window("solicitud_examen", {className: "alphacube",
            top:20, left:0,
            width: 1100, height: 610,
            title: '<center><img src="../../iconos/table.png"> Solicitud de Ex&aacute;menes</center>',
            minWidth: 700, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: true });
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='nomd_id='+$('nomd_id').value+'&pac_id='+$('pac_id').value+'&esp_id='+$('esp_id').value;
            win.setAjaxContent('solicitud_examen.php',
            {
                method: 'post',
                evalScripts: true,
                parameters: params,
                onComplete: function()
                {
                    //var fn=window.opener.guardar_prestacion.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });
            $("solicitud_examen").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        }


        dagnosticos_frecuetes=function()
        {
            var win = new Window("diag_frecuentes", {className: "alphacube",
            top:20, left:0,
            width: 500, height: 400,
            title: '<center><img src="../../iconos/table.png">Diagnosticos de Uso Frecuente</center>',
            minWidth: 700, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: true });
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='&esp_id='+$('esp_id').value+'&doc_id='+$('doc_id').value;
            win.setAjaxContent('diagnosticos_frecuentes.php',
            {
                method: 'post',
                evalScripts: true,
                parameters: params,
                onComplete: function()
                {
                    //var fn=window.opener.guardar_prestacion.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });
            $("diag_frecuentes").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        }

        derivacion= function()
        {
            var win = new Window("form_derivacion", {className: "alphacube",
            top:20, left:0,
            width: 500, height: 500,
            title: '<center><img src="../../iconos/table.png"> Solicitud de Interconsulta Interna</center>',
            minWidth: 700, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: true });
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='nomd_id='+$('nomd_id').value+'&pac_id='+$('pac_id').value+'&esp_id='+$('esp_id').value;
            win.setAjaxContent('form_derivacion.php',
            {
                method: 'post',
                evalScripts: true,
                parameters: params,
                onComplete: function()
                {
                    //var fn=window.opener.guardar_prestacion.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });
            $("form_derivacion").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        }

        agregar_prestacion = function()
        {
            var codigo=$('cod_presta').value;
            var desc_presta=$('desc_presta').value;
            var cant=$('cantidad').value;
            cant=cant;
            var pc_id=$('pc_id').value;
            var num=presta.length;
            presta[num]=new Object();
            presta[num].codigo=codigo;
            presta[num].desc=desc_presta;
            presta[num].cantidad=cant;
            presta[num].pc_id=pc_id;
            listar_prestaciones();

            $('cod_presta').select();
            $('cod_presta').focus();
            $('cambia_presta').value=1;
        }

        eliminar_prestacion = function(id)
        {
            presta=presta.without(presta[id]);
            listar_prestaciones();
            $('cambia_presta').value=1;
        }

        listar_prestaciones=function()
        {
            var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Cant.</td><td>Descripci&oacute;n</td><td>Eliminar</td></tr>';
            for(var i=0;i<presta.length;i++)
            {
                clase=(i%2==0)?'tabla_fila':'tabla_fila2';
                if(presta[i].desc.length>37)
                    var descr=presta[i].desc.substr(0,67)+'...';
                else
                    var descr=presta[i].desc;

                html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                html+='<td style="text-align:center;font-weight:bold;">'+presta[i].codigo+'</td>';
                html+='<td style="text-align:center;">'+presta[i].cantidad+'</td><td>'+descr+'</td>';
                html+='<td><center><img src="../../iconos/delete.png" style="cursor: pointer;" onClick="eliminar_prestacion('+i+');"></center></td>';
                html+='</tr>';
            }
            html+='</table>'
            $('lista_presta').innerHTML=html;
        }


        mostrar_historia = function() {
          if($('select_historia').value=='1'){
            $('div_hist_medicamentos').show();
            $('div_hist_examenes').hide();
          } else {
            $('div_hist_medicamentos').hide();
            $('div_hist_examenes').show();
          }
        }

        informe_examen=function(sol_examd_id)
        {
            top=Math.round(screen.height/2)-325;
            left=Math.round(screen.width/2)-375;
            new_win = window.open('form_informe.php?sol_examd_id='+sol_examd_id+'&nomd_id='+$('nomd_id').value,
            'win_nomina_form', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=1000, height=750, '+
            'top='+top+', left='+left);
            new_win.focus();
        }


        sel_atencion=function()
        {
            nomd_id=$('nomd_id').value;
            if($('select_atencion').value=='N')
            {
                if(($('atencion_nueva').value*1)==1)
                {
                    $('nomd_prot_ficha').disabled=true;
                    $('nomd_prot_ficha').value=0;
                    $('nomd_tiempo_ficha').disabled=true;
                    $('nomd_tiempo_ficha').value=0;
                }
                $("nomd_diag_cod").value="";
                $("nomd_diagnostico").value="";
                $('motivo_atencion').disabled=false;
                sel_motivo_atencion(nomd_id);
                return;
            }
            else
            {
                if(($('atencion_nueva').value*1)==1)
                {
                    $('nomd_prot_ficha').disabled=false;
                    //$('nomd_prot_ficha').value=0;
                    $('nomd_tiempo_ficha').disabled=false;
                    //$('nomd_tiempo_ficha').value=0;
                }
                $('nomd_codigo_no_atiende').value=$('select_atencion').value;
                $('motivo_atencion').disabled=true;
                return;
            }


            /*
            top=Math.round(screen.height/2)-250;
            left=Math.round(screen.width/2)-340;
            var op=$('nomd_diag_cod_'+nomd_id).value;
            var valor;
            if(op=='N'){ valor=encodeURIComponent($('nomd_codigo_no_atiende_'+nomd_id).value); tipo=1; }
            new_win = window.open('prestaciones/ingreso_nominas/seleccionar_opcion.php?tipo='+tipo+'&valor='+valor+'&nomd_id='+nomd_id,
            'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
            'top='+top+', left='+left);
            new_win.focus();
            */
        }

        sel_motivo_atencion=function(nomd_id)
        {
            var op=$('select_atencion').value;
            var valor;
            if(op=='N')
            {
                valor=encodeURIComponent($('nomd_codigo_no_atiende').value);
                tipo=1;
            }

            var win = new Window("motivo_nomina_form", {className: "alphacube",
            width: 680, height: 500,
            title: '<center><img src="../../iconos/table.png"> Motivo No Atenci&oacute;n</center>',
            minWidth: 700, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: true ,close: false});
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='tipo='+tipo+'&valor='+valor+'&nomd_id='+nomd_id+'&llamada=ficha';
            win.setAjaxContent('seleccionar_opcion.php',
            {
                method: 'get',
                evalScripts: true,
                parameters: params,
                onComplete: function()
                {

                    //var fn=window.opener.guardar_prestacion.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });
            $("motivo_nomina_form").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        }

        sel_origen_atencion=function()
        {

            nomd_id=$('nomd_id').value;
            /*
            var op=$('select_atencion').value;
            var valor;
            if(op=='N')
            {
                valor=encodeURIComponent($('nomd_codigo_no_atiende').value);
                tipo=1;
            }
            */
            var win = new Window("origen_atencion_form", {className: "alphacube",
            width: 680, height: 500,
            title: '<center><img src="../../iconos/table.png"> Origen del Paciente</center>',
            minWidth: 700, minHeight: 400,
            maximizable: false, minimizable: false,
            wiredDrag: true, resizable: true });
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='tipo=2&valor=&nomd_id='+nomd_id+'&llamada=ficha';
            win.setAjaxContent('seleccionar_opcion.php',
            {
                method: 'get',
                evalScripts: true,
                parameters: params,
                onComplete: function()
                {

                    //var fn=window.opener.guardar_prestacion.bind(window.opener);
                    //fn();
                    //window.close();
                }
            });
            $("origen_atencion_form").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        }

        cancelar = function()
        {
            $("motivo_nomina_form").win_obj.close();
        }


        cambiar_auto_cie10 = function()
        {
            $("nomd_diag_cod").value="";
            $("nomd_diagnostico").value="";
            $("nomd_diagnostico_tapsa").value="";
            $("diag_personal").value="";
            $('chk_save_tapsa').checked = false;
            if($('chk_tapsa').checked)
            {
                $('nomd_diagnostico').style.display='none';
                $('nomd_diagnostico_tapsa').style.display='block';
                $('diag_personal').disabled=true;
                $('chk_save_tapsa').disabled=true;
            }
            else
            {
                $('nomd_diagnostico').style.display='block';
                $('nomd_diagnostico_tapsa').style.display='none';
                $('diag_personal').disabled=false;
                $('chk_save_tapsa').disabled=false;
            }
        }

        mostrar_observacion=function(opcion,nomd_id)
        {
            //if(opcion*1==0)
            //{

            //}
            //if(opcion*1==1)
            //{

                var myAjax=new Ajax.Request('buscar_observacion.php',
                {
                    method:'post',
                    parameters:'nomd_id='+nomd_id,
                    onComplete:function(r)
                    {
                        var obs=r.responseText;
                        $('observacion').value=obs.unescapeHTML();
                    }
                });

            //}
        }


        cargar_ultima_receta=function(receta_id){
            alert("En Desarrollo por el momento");
            //return;
            if(receta_id==undefined){
                params='';
            } else {
                params='&receta_id='+receta_id;
            }
            var myAjax=new Ajax.Request('../../ficha_clinica/cargar_ultima_receta.php',
            {
                method:'post',
                parameters:'pac_id=<?php echo $pac_id; ?>&bodega_id=36'+params+'&esp_id=<?php echo $esp_id; ?>&nomina_detalle=1',
                onComplete:function(datosrecetas)
                {


                }
            });
        }


        mostrar_examenes=function() {
          if($('select_examen').value=='1'){
            $('lista_exam_pendientes').show();
            $('lista_exam_realizados').hide();
          } else {
            $('lista_exam_pendientes').hide();
            $('lista_exam_realizados').show();
          }
        }




    </script>
    <body class='fuente_por_defecto popup_background' id='__contenido'>
        <form id='datos' name='datos' onSubmit='return false;'>
            <table style='width:100%;height:500px;'>
                <tr>
                    <td valign='top'>
                        <div class='sub-content'>
                            <img src='../../iconos/table_edit.png' />
                            <b>Registro Cl&iacute;nico de Atenci&oacute;n</b>
                        </div>
                        <input type='hidden' id='nomd_id' name='nomd_id' value='<?php echo $nomd_id; ?>' />
                        <input type='hidden' id='esp_id' name='esp_id' value='<?php echo $esp_id; ?>' />
                        <input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id; ?>' />
                        <input type='hidden' id='pac_id' name='pac_id' value='<?php echo $pac_id; ?>' />
                        <input type='hidden' id='paciente' name='paciente' value='<?php echo $pac_id; ?>' />
                        <input type='hidden' id='pac_rut' name='pac_rut' value='<?php echo $ndet['pac_rut']; ?>' />
                        <input type='hidden' id='cambia_presta' name='cambia_presta' value='0' />
                        <input type='hidden' id='nomd_codigo_no_atiende' name='nomd_codigo_no_atiende' value='' />
                        <input type='hidden' id='cant_exam' name='cant_exam' value='<?php echo $cant_exam; ?>' />
                        <input type='hidden' id='contrarreferencia' name='contrarreferencia' value='<?php echo $contrarreferencia; ?>' />
                        <?php
                        $reg_inter=cargar_registro("select * from interconsulta where inter_nomd_id=$nomd_id");
                        if($reg_inter)
                        {
                            $consulta="SELECT e1.esp_desc, inter_fundamentos, inter_examenes,inter_comentarios,
                            inter_estado, inter_rev_med,inter_prioridad,i1.inst_nombre,
                            inter_inst_id1,inter_motivo,inter_diag_cod,inter_diagnostico,COALESCE(garantia_nombre, ''),COALESCE(garantia_id, 0),
                            i2.inst_nombre AS inst_nombre2, inter_inst_id2,inter_ingreso, ice_icono, ice_desc,unidad.esp_desc AS unidad_desc,
                            inter_unidad,inter_motivo_salida,icc_desc,
                            inter_fecha_salida ,inter_id,casos_auge.ca_patologia,inter_pat_id
                            FROM interconsulta
                            LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
                            LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id
                            LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id
                            LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id
                            LEFT JOIN interconsulta_estado ON inter_estado=ice_id
                            LEFT JOIN especialidades AS unidad ON inter_unidad=unidad.esp_id
                            LEFT JOIN interconsulta_cierre ON inter_motivo_salida=icc_id
                            LEFT JOIN casos_auge ON casos_auge.id_sigges=id_caso WHERE inter_id=".$reg_inter['inter_id']."";
                            $datos2 = pg_query($consulta);
                            $inter2 = pg_fetch_row($datos2);
                            print("
                            <div class='sub-content' style='font-size: 12px;'><img src='../../iconos/chart_organisation.png'> <b>Informaci&oacute;n de Interconsulta</b></div>
                                <div class='sub-content2'>
                                    <table>
                                        <tr style='font-size: 12px;'>
                                            <td style='text-align:right;'>Especialidad:</td>
                                            <td width=60%><b>".$inter2[0]."</b></td>
                                        </tr>
                            ");
                            if($inter2[19]!='')
                            print("<tr style='font-size: 12px;'><td style='text-align:right;'>Unidad Receptora:</td>
                            <td width=60%><b>".htmlentities($inter2[19])."</b></td></tr>");
                            switch($inter2[9])
                            {
                                case 0: $motivo='Confirmaci&oacute;n Diagn&oacute;stica'; break;
                                case 1: $motivo='Realizar Tratamiento'; break;
                                case 2: $motivo='Seguimiento'; break;
                                case 3: $motivo='Control Especialidad'; break;
                                case 4: $motivo='Otro Motivo...'; break;
                            }
                            print("
                                <tr style='font-size: 12px;'><td style='text-align:right;'>Motivo Derivaci&oacute;n:</td>
                                <td width=60%>".$motivo."</td></tr>
                                <tr style='font-size: 12px;'><td style='text-align:right;' valign='top'>Diagn&oacute;stico (Pres.):</td>
                                <td width=60%><b>".$inter2[10]."</b><br>".htmlentities($inter2[11])."</td></tr>
                                <tr style='font-size: 12px;'><td style='text-align:right;' valign='top'>Patolog&iacute;a GES:</td>
                            ");
                            if($inter2[25]!=''){
                                print("<td><b>".htmlentities($caso['ca_patologia'])."</b></td>");
                            } else {
                                if(($inter2[26]*1)!=0) {
                                    $reg_pat=cargar_registro("SELECT * FROM patologias_auge WHERE pat_id=".($inter2[26]*1)."");
                                    if($reg_pat) {
                                        print("<td><b>".htmlentities($reg_pat['pat_glosa'])."</b></td>");
                                    } else {
                                        print("<td><b>Error al Encontrar Patologia.</b></td>");
                                    }
                                } else {
                                    print("<td><b>No hay sospecha.</b></td>");
                                }
                            }
                            print("</tr>");
                            print("<tr style='font-size: 12px;'><td style='text-align:right;' valign='top'>Fundamentos Cl&iacute;nicos:</td>
                            <td>".$inter2[1]."</td></tr>
                            ");
                            if(trim($inter2[2])!="")
                                print("
                                <tr style='font-size: 12px;'><td style='text-align:right;' valign='top'>Ex&aacute;menes Comp.:</td>
                                <td>".$inter2[2]."</td></tr>");

                            if(trim($inter2[3])!="")
                                print("
                                <tr style='font-size: 12px;'><td style='text-align:right;' valign='top'>Comentarios:</td>
                                <td>".$inter2[3]."</td></tr>");

                            print("
                        </table>
                        </div>");
                        }
                        ?>
                        <?php
                        if ($proc['esp_orden_atencion']=='t')
                        {
                        ?>
                        <div class='sub-content'>
                            <table style='width:100%;'>
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Orden de Atenci&oacute;n:
                                    </td>
                                    <td class='tabla_fila'>
                                        <select id='oa_id' name='oa_id' onChange='cargar_oa();'>
                                            <?php echo $oahtml; ?>
                                            <option value='0'>(Sin Orden de Atenci&oacute;n...)</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Tipo:
                                    </td>
                                    <td class='tabla_fila'>
                                        <select id='tipo_oa' name='tipo_oa' onChange='fix_fields();'>
                                            <option value='1'>Local</option>
                                            <option value='2'>Externa</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Fecha Solicitud:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='text' id='fecha_oa' name='fecha_oa' style='text-align:center;' value='<?php echo date('d/m/Y'); ?>' size=15 onKeyUp='validacion_fecha(this);' />
                                    </td>
                                </tr>
                                <tr id='serv_tr'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Servicio Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='centro_ruta_oa' name='centro_ruta_oa' value='<?php echo $serv_sol_centro_ruta;?>' />
                                        <input type='text' id='centro_nombre_oa' name='centro_nombre_oa' value='<?php echo $serv_sol_centro_nombre;?>' size=35 onDblClick='$("centro_ruta_oa").value=""; $("centro_nombre_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='unid_tr'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Unidad Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='esp_id_oa' name='esp_id_oa' value='<?php echo $esp_sol;?>' />
                                        <input type='text' id='esp_desc_oa' name='esp_desc_oa' value='<?php echo $esp_soldesc?>' size=35 onDblClick='$("esp_id_oa").value=""; $("esp_desc_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='medi_tr'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        M&eacute;dico Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='doc_id_oa' name='doc_id_oa' value='<?php echo $doc_sol_id;?>' />
                                        <input type='text' id='doc_rut_oa' name='doc_rut_oa' value='<?php echo $doc_sol_rut;?>' size=15 style='text-align:center;' DISABLED />
                                        <input type='text' id='doc_nombre_oa' name='doc_nombre_oa' value='<?php echo $doc_sol_nombre;?>' size=35 onDblClick='$("doc_id_oa").value=""; $("doc_rut_oa").value=""; $("doc_nombre_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='inst_tr' style='display:none;'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Instituci&oacute;n Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='inst_id_oa' name='inst_id_oa' value='' />
                                        <input type='text' id='inst_desc_oa' name='inst_desc_oa' value='' size=35 onDblClick='$("inst_id_oa").value=""; $("inst_desc_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='espe_tr' style='display:none;'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Especialidad Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='esp_id2_oa' name='esp_id2_oa' value='' />
                                        <input type='text' id='esp_desc2_oa' name='esp_desc2_oa' value='' size=35 onDblClick='$("esp_id2_oa").value=""; $("esp_desc2_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='prof_tr' style='display:none;'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Profesional Solicitante:
                                    </td>
                                    <td class='tabla_fila'>
                                        <input type='hidden' id='prof_id_oa' name='prof_id_oa' value='' />
                                        <input type='text' id='prof_rut_oa' name='prof_rut_oa' value='' size=15 style='text-align:center;' DISABLED />
                                        <input type='text' id='prof_nombre_oa' name='prof_nombre_oa' value='' size=35 onDblClick='$("prof_id_oa").value=""; $("prof_rut_oa").value=""; $("prof_nombre_oa").value="";' />
                                    </td>
                                </tr>
                                <tr id='observaciones_tr'>
                                    <td style='text-align:right;' class='tabla_fila2'>
                                        Observaciones:
                                    </td>
                                    <td class='tabla_fila'>
                                        <textarea id="obsgeneral" name="obsgeneral" style="width:50%;height:45px;" readonly><?php echo $obs_exam;?></textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php
                        }
                        ?>
                        <div class='sub-content'>
                            <table style='width:100%;'>
                                <tr>
                                    <td style='width:100px;text-align:right;' class='tabla_fila2'>
                                        R.U.T.:
                                    </td>
                                    <td class='tabla_fila' style='text-align:left;font-size:16px;'>
                                        <?php echo $ndet['pac_rut']; ?>
                                    </td>
                                    <td style='width:100px;text-align:right;' class='tabla_fila2'>
                                        Nro. Ficha:
                                    </td>
                                    <td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
                                        <?php echo $ndet['pac_ficha']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='width:100px;text-align:right;' class='tabla_fila2'>
                                        Nombre Paciente:
                                    </td>
                                    <td colspan=3 class='tabla_fila' style='text-align:left;font-weight:bold;font-size:16px;'>
                                        <?php echo htmlentities(strtoupper($ndet['pac_nombres'].' '.$ndet['pac_appat'].' '.$ndet['pac_apmat'])); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='width:100px;text-align:right;' class='tabla_fila2'>
                                        Edad:
                                    </td>
                                    <td class='tabla_fila' style='text-align:left;font-size:16px;'>
                                        <?php echo $ndet['edad_anios'].' A&Ntilde;OS '.$ndet['edad_meses'].' Meses '.$ndet['edad_dias'].' d&iacute;as'; ?>
                                    </td>
                                    <td style='width:100px;text-align:right;' class='tabla_fila2'>
                                        Fecha Nacimiento:
                                    </td>
                                    <td class='tabla_fila' style='text-align:center;font-size:16px;'>
                                        <?php echo $ndet['pac_fc_nac']; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>Or&iacute;gen:</td>
                                    <td class='tabla_fila' colspan=3>
                                        <select id='origen' name='origen' disabled="">
                                            <option selected="" value="">(Sin Info)</option>
                                            <option value='' <?php if($ndet['nomd_origen']=='') echo 'SELECTED'; ?>>(Sin Info)</option>
                                            <option value='A' <?php if($ndet['nomd_origen']=='A') echo 'SELECTED'; ?>>APS</option>
                                            <option value='U' <?php if($ndet['nomd_origen']=='U') echo 'SELECTED'; ?>>URGENCIA</option>
                                            <option value='C' <?php if($ndet['nomd_origen']=='C') echo 'SELECTED'; ?>>CAE</option>
                                        </select>
                                        <input type="button" style="border:0px;margin:0px;" onclick="sel_origen_atencion();" value="[S]" name="origen_atencion" id="origen_atencion" >
                                        <!--
                                        <select id='origen' name='origen'>
                                            <option value='A' <?php if($ndet['nomd_origen']=='A') echo 'SELECTED'; ?>>Ambulatorio</option>
                                            <option value='H' <?php if($ndet['nomd_origen']=='H') echo 'SELECTED'; ?>>Hospitalizado</option>
                                            <option value='U' <?php if($ndet['nomd_origen']=='U') echo 'SELECTED'; ?>>Urgencias</option>
                                        </select>
                                        -->
                                        &nbsp;Estado Atenci&oacute;n:&nbsp;
                                        <select name="select_atencion" id="select_atencion" style="width:100px;" onchange="sel_atencion();">
                                            <option value='-1' <?php if($ndet['nomd_diag_cod']=='') echo 'SELECTED'; ?>>Seleccionar Estado</option>
                                            <option value='OK' <?php if($ndet['nomd_diag_cod']=='OK') echo 'SELECTED'; ?>>ATENDIDO</option>
                                            <option value='ALTA' <?php if($ndet['nomd_diag_cod']=='ALTA') echo 'SELECTED'; ?>>ALTA DE ESPECIALIDAD</option>
                                            <option value='N' <?php if($ndet['nomd_diag_cod']=='N') echo 'SELECTED'; ?>>NO ATENDIDO</option>
                                        </select>
                                        <input type="button" style="border:0px;margin:0px;" onclick="sel_atencion();" value="[S]" name="motivo_atencion" id="motivo_atencion" <?php echo $motivo_enabled;?>>
					     <?php
                                        $palabra = 'Nueva';
                                        $encontrada = strrpos(strtoupper($nom_motivo), strtoupper($palabra));
                                        if($encontrada)
                                        {
                                        ?>
                                            <input type='hidden' id='atencion_nueva' name='atencion_nueva' value='1' />
                                            &nbsp;&nbsp;Pertinente:&nbsp;&nbsp;Prot->
                                            <select name="nomd_prot_ficha" id="nomd_prot_ficha">
                                                <option value="0" <?php if($ndet['nomd_motivo'][0]=='') echo 'SELECTED'; ?>>Indicar</option>
                                                <option value="S" <?php if($ndet['nomd_motivo'][0]=='S') echo 'SELECTED'; ?>>S</option>
                                                <option value="N" <?php if($ndet['nomd_motivo'][0]=='N') echo 'SELECTED'; ?>>N</option>
                                            </select>
                                            &nbsp;&nbsp;Tiempo->
                                            <select name="nomd_tiempo_ficha" id="nomd_tiempo_ficha">
                                                <option value="0" <?php if($ndet['nomd_motivo'][1]=='') echo 'SELECTED'; ?>>Indicar</option>
                                                <option value="S" <?php if($ndet['nomd_motivo'][1]=='S') echo 'SELECTED'; ?>>S</option>
                                                <option value="N" <?php if($ndet['nomd_motivo'][1]=='N') echo 'SELECTED'; ?>>N</option>
                                            </select>
                                        <?php
                                        }
                                        else
                                        {
                                        ?>
                                            <input type='hidden' id='atencion_nueva' name='atencion_nueva' value='0' />
                                        <?php
                                        }
                                        ?>
                                   </td>
                                </tr>
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>Diag. CIE10:</td>
                                    <td class='tabla_fila' colspan=3>
                                        <?php
                                        if(strstr($ndet['nomd_diag'],'|'))
                                        {
                                            $diagnostico=explode("|",$ndet['nomd_diag']);
                                            $diagnostico_cod=$diagnostico[0];
                                            $diagnostico_string=$diagnostico[1];
                                            if($diagnostico_cod!='')
                                            {
                                                $reg_cie=cargar_registro("SELECT * FROM diagnosticos WHERE upper(diag_cod)=upper('".$diagnostico_cod."')",true);
                                                if($reg_cie)
                                                {

                                                    if(strtoupper($reg_cie['diag_desc'])!=strtoupper($diagnostico_string))
                                                    {
                                                        $diag_personal=$diagnostico_string;
                                                        $diagnostico_string=strtoupper($reg_cie['diag_desc']);
                                                    }
                                                    else
                                                    {
                                                        $diag_personal="";
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $diagnostico_cod="";
                                            $diagnostico_string=$ndet['nomd_diag'];
                                        }
                                        ?>

                                            <input type='text' id='nomd_diag_cod' name='nomd_diag_cod' value='<?php echo $diagnostico_cod; ?>' DISABLED size=5 style='font-weight:bold;text-align:center;' />
                                            <input type='text' id='nomd_diagnostico' value='<?php echo $diagnostico_string; ?>' name='nomd_diagnostico' size=30 onDblClick='$("nomd_diag_cod").value=""; $("nomd_diagnostico").value="";' alt="<?php echo $diagnostico_string; ?>" title="<?php echo $diagnostico_string; ?>" onchange=''/>
                                            <input type='text' id='nomd_diagnostico_tapsa' value='<?php echo $diagnostico_string; ?>' name='nomd_diagnostico_tapsa' size=30 onDblClick='$("nomd_diag_cod").value=""; $("nomd_diagnostico_tapsa").value="";' style="display:none;" alt="<?php echo $diagnostico_string; ?>" title="<?php echo $diagnostico_string; ?>"/>
                                            <input type="checkbox" id="chk_tapsa" name="chk_tapsa" onchange="cambiar_auto_cie10();" style="display: none"><!--(Buscar Diag Tapsa).--->
                                            &nbsp;&nbsp;&nbsp;&nbsp;Paciente GES:&nbsp;&nbsp;
                                            <select id='ges'>
                                                <option value='false' <?php if($ndet['nomd_ges']=='f' OR $ndet['nomd_ges']=='') echo 'SELECTED'; ?>>NO</option>
                                                <option value='true' <?php if($ndet['nomd_ges']=='t') echo 'SELECTED'; ?>>SI</option>
                                            </select>
                                            <!--
                                            <input type='button' id='' name='' onClick='ver_examenes();' value='--- Ver Examenes ---' />
                                            -->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;' class='tabla_fila2'>Diag.- Personal:</td>
                                        <td class='tabla_fila' colspan="3">
                                            <input type='text' id='diag_personal' name='diag_personal' onKeyUp='' value='<?php echo $diag_personal;?>' size=90 />
                                            <input type="checkbox" id="chk_save_tapsa" name="chk_save_tapsa" onchange="" style="display: none;" /><!--Guardar Tapsa-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <?php
                                            if($proc['esp_orden_atencion']!='t')
                                            {
                                            ?>
                                                <input type='button' id='btn_sol_examen' name='btn_sol_examen' onClick='sol_examenes();' value='Solicitud de Ex&aacute;menes' />
                                                <input type='button' id='btn_inter' name='btn_inter' onClick='derivacion();' value='Solicitud Interconsulta' />
                                            <?php
                                            }
                                            ?>
                                            <input type='button' id='btn_diagfrecuentes' name='btn_diagfrecuentes' onClick='dagnosticos_frecuetes();' value='Diag. Frecuentes' />
                                        </td>
                                    </tr>
                                    <tr style="display: none">
                                        <td style='text-align:right;' class='tabla_fila2'>Patolog&iacute;a:</td>
                                        <td>
                                            <input type='hidden' id='pat_id' name='pat_id' value=''>
                                            <input type='text' id='pat_desc' name='pat_desc' value='' size=40>
                                        </td>
                                    </tr>
                                <!--
                                <tr>
                                    <td style='text-align:right;' class='tabla_fila2'>Subgrupo/subproblema AUGE:</td>
                                    <td>
                                        <div id='patrama' style="width: 30%">
                                            <select id='patrama_id' name='patrama_id'>
                                                <option value=-1>(Seleccione Patolog&iacute;a...)</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                -->


                                <!--
                                <tr class='tabla_header'><td style='text-align:center;font-weight:bold;' class='tabla_fila2' valign='top' colspan=4>
                                <u>Observaciones Generales</u>
                                </td></tr>
                                <tr>
                                <td colspan=4 class='tabla_fila' style='text-align:left;font-weight:bold;'>
                                <textarea id='observaciones' name='observaciones' style='width:100%;height:180px;'><?php echo $ndet['nomd_observaciones']; ?></textarea>
                                </td></tr>
                                -->
                            </table>
                        </div>
                        <?php
                        if($reg_examenes) {
                        ?>
                            <div class='sub-content'>
                                <img src='../../iconos/table.png'>
                                <b>Ex&aacute;menes Asociados a la Atenci&oacute;n</b>
                                <select id='select_examen' name='select_examen' onChange='mostrar_examenes();'>
                                  <option value='1'>Ex&aacute;menes Pendientes</option>
                                  <option value='2'>Ex&aacute;menes Realizados</option>
                                </select>
                            </div>
                            <div class='sub-content2' id='lista_exam_pendientes' style='height:120px;overflow:auto;'>
                                <table style="width:100%;font-size:11px;">
                                    <tr class="tabla_header">
                                        <td>C&oacute;digo</td>
                                        <td>Cant.</td>
                                        <td>Descripci&oacute;n</td>
                                        <td>Disparos</td>
                                        <td>Realizado</td>
                                        <td>Informe</td>
                                    </tr>
                                    <?php
                                    for($i=0;$i<count($reg_examenes);$i++) {
                                      if($reg_examenes[$i]['sol_examd_estado']!=0){
                                          continue;
                                      }
                                      if($reg_examenes[$i]['sol_examd_estado']==0)
                                        $checked = '';
                                      else
                                        $checked = 'DISABLED';

                                      $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                                      print('<tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';">');
                                      print('<td style="text-align:center;font-weight:bold;">'.$reg_examenes[$i]['pc_codigo'].'</td>');
                                      print('<td style="text-align:center;font-weight:bold;">'.$reg_examenes[$i]['sol_examd_cant'].'</td>');
                                      print('<td style="text-align:left;font-weight:bold;">'.$reg_examenes[$i]['pc_desc'].'</td>');
                                      print('<td style="text-align:center;font-weight:bold;">');
                                      print('<input type="text" id="disparos_'.$i.'" name="disparos_'.$i.'" value="'.$reg_examenes[$i]['sol_examd_disparos'].'" style="width:30px;" '.$checked.'>');
                                      print('</td>');
                                      print('<td style="text-align:center;font-size:10px;">');
                                      if($reg_examenes[$i]['sol_examd_estado']==0) {
                                        print('<input type="checkbox" id="chk_realizado_'.$i.'" name="chk_realizado_'.$i.'" onChange="actualizar_listado ">');
                                      } else {
                                        print('<input type="checkbox" id="chk_realizado_'.$i.'" name="chk_realizado_'.$i.'" onChange="" disabled checked>');
                                      }
                                      print('</td>');
                                      print('<td>');
                                      print('<center>');
                                      print("<img src='../../iconos/script_edit.png'  style='cursor:pointer;' onClick='informe_examen(".($reg_examenes[$i]['sol_examd_id']).");' />");
                                      print('</center>');
                                      print('</td>');
                                      print('</tr>');
                                    }
                                    ?>
                                </table>
                            </div>
                            <div class='sub-content2' id='lista_exam_realizados' style='height:120px;overflow:auto;display:none'>
                                <table style="width:100%;font-size:11px;">
                                    <tr class="tabla_header">
                                        <td>C&oacute;digo</td>
                                        <td>Cant.</td>
                                        <td>Descripci&oacute;n</td>
                                        <td>Disparos</td>
                                        <td>Realizado</td>
                                        <td>Informe</td>
                                    </tr>
                                    <?php
                                    for($i=0;$i<count($reg_examenes);$i++) {
                                      if($reg_examenes[$i]['sol_examd_estado']==0){
                                          continue;
                                      }

                                      if($reg_examenes[$i]['sol_examd_estado']==0)
                                        $checked = '';
                                      else
                                        $checked = 'DISABLED';

                                      $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                                      print('<tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';">');
                                      print('<td style="text-align:center;font-weight:bold;">'.$reg_examenes[$i]['pc_codigo'].'</td>');
                                      print('<td style="text-align:center;font-weight:bold;">'.$reg_examenes[$i]['sol_examd_cant'].'</td>');
                                      print('<td style="text-align:left;font-weight:bold;">'.$reg_examenes[$i]['pc_desc'].'</td>');
                                      print('<td style="text-align:center;font-weight:bold;">');
                                      print('<input type="text" id="disparos_'.$i.'" name="disparos_'.$i.'" value="'.$reg_examenes[$i]['sol_examd_disparos'].'" style="width:30px;" '.$checked.'>');
                                      print('</td>');
                                      print('<td style="text-align:center;font-size:10px;">');
                                      if($reg_examenes[$i]['sol_examd_estado']==0) {
                                        print('<input type="checkbox" id="chk_realizado_'.$i.'" name="chk_realizado_'.$i.'" onChange="actualizar_listado ">');
                                      } else {
                                        print('<input type="checkbox" id="chk_realizado_'.$i.'" name="chk_realizado_'.$i.'" onChange="" disabled checked>');
                                      }
                                      print('</td>');
                                      print('<td>');
                                      print('<center>');
                                      print("<img src='../../iconos/script_edit.png'  style='cursor:pointer;' onClick='informe_examen(".($reg_examenes[$i]['sol_examd_id']).");' />");
                                      print('</center>');
                                      print('</td>');
                                      print('</tr>');
                                    }
                                    ?>
                                </table>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        //if(!_cax(1000) AND (!$presta OR sizeof($presta)>1))
                        //{
                        if($presta OR sizeof($presta)>1)
                        {
                        ?>
                        <div class='sub-content'>
                            <img src='../../iconos/table.png'>
                            <b>Registro de Prestaciones</b>
                            &nbsp;
                            <input type="checkbox" onchange="" name="chk_solexamenes" id="chk_solexamenes">(Solo Examenes)
                        </div>
                        <div class='sub-content2' id='lista_presta' style='height:120px;overflow:auto;'>
                        </div>
                        <div class='sub-content'>
                            <table style='width:100%;' cellpadding=0 cellspacing=0>
                                <tr>
                                    <td style='width:15px;'>
                                        <center>
                                            <img src='../../iconos/add.png' />
                                        </center>
                                    </td>
                                    <td style='width:100px;text-align:right;'>Agregar Prest.:</td>
                                    <td>
                                        <input type='hidden' id='desc_presta' name='desc_presta' value='' />
                                        <input type='hidden' id='pc_id' name='pc_id' value='0' />
                                        <input type='text' id='cod_presta' name='cod_presta' size=10 />
                                    </td>
                                    <td style='text-align:right;'>
                                        Cant.:
                                    </td>
                                    <td>
                                        <input type='text' id='cantidad' name='cantidad' onKeyUp='if(event.which==13) agregar_prestacion();' size=3 />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php
                        }
                        elseif(!_cax(1000))
                        {
                        ?>
                        <!--
                        <div class='sub-content'>
                            <center><b><u>Prestaci&oacute;n Recibida</u></b></center>
                            <br />
                            <table style='width:100%;'>
                                <tr>
                                    <td style='text-align:center;width:100px;font-weight:bold;font-size:14px;'>
                                        <?php //echo $presta[0]['pc_codigo']; ?>
                                    </td>
                                    <td style='text-align:justify;'>
                                        <?php //echo $presta[0]['glosa']; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        -->
                        <?php
                        }
                        ?>
                        <?php
                        if($recetas)
                        {
                        ?>
                        <!--
                        <div class='sub-content' id='datos_vademecum'>
                            <table style='width:100%;'>
                                <tr>
                                    <td><input type='checkbox' id='chk_embarazo' name='chk_embarazo' />Embarazo</td>
                                    <td><input type='checkbox' id='chk_lactancia' name='chk_lactancia' />Lactancia</td>
                                    <td><input type='checkbox' id='chk_fotosensible' name='chk_fotosensible' />Fotosensibilidad</td>
                                </tr>
                            </table>
                            <table style='width:100%;'>
                                <tr>
                                    <td style='text-align:right;'>Peso (gr):</td>
                                    <td><input type='text' id='inp_peso' name='inp_peso' /></td>
                                    <td style='text-align:right;'>Nivel Aclaramiento Creatinina (ml/min):</td>
                                    <td><input type='text' id='inp_renal' name='inp_renal' /></td>
                                </tr>
                            </table>
                        </div>
                        -->
                        <div class='sub-content'>
                            <img src='../../iconos/pill.png'>
                            <b>Emisi&oacute;n de Receta</b>
                            <select id='tipo_receta' name='tipo_receta' onChange='mod_tipo_receta();' style='display:none;'>
                                <option value='0'>Aguda</option>
                                <option value='1'>Cr&oacute;nica</option>
                            </select>
                            <input type="button" onclick="cargar_ultima_receta();" value="[Copiar Ultima Receta de Especialidad]" name="" id="boton_copiar" style="">
                        </div>
                        <div class='sub-content'>
                            <table style='width:100%;' cellpadding=0 cellspacing=0>
                                <tr>
                                    <td style='width:15px;'>
                                        <center>
                                            <img src='../../iconos/add.png' />
                                        </center>
                                    </td>
                                    <td style='width:100px;text-align:right;'>B&uacute;squeda Med.:&nbsp;</td>
                                    <td>
                                        <input type='hidden' id='art_id' name='art_id' value='0' />
                                        <input type='hidden' id='art_codigo' name='art_codigo' value='' />
                                        <input type='hidden' id='art_nombre' name='art_nombre' value='' />
                                        <input type='hidden' id='art_stock' name='art_stock' value='' />
                                        <input type='hidden' id='campo_ua' name='campo_ua' value='' />
                                        <input type='hidden' id='campo_ua_cant' name='campo_ua_cant' value='' />
                                        <input type='hidden' id='campo_tipo_adm' name='campo_tipo_adm' value='' />
                                        <input type='text' id='codigo' name='codigo' size=11 />
                                    </td>
                                    <td>&nbsp;</td>
                                    <td style='text-align:left;font-weight:bold;width:70%;' id='remed' name='remed'></td>
                                    <td>
                                        <!--<input type='button' id='' value='[Alertas Vademecum&copy;]' onClick='alertas_vademecum();' />-->
                                    </td>
                                </tr>
                                <tr style='display:none;' name='tr_coments' id='tr_coments'>
                                    <td colspan=4>&nbsp;</td>
                                    <td bgcolor='#FFFF00'>
                                        <label style='color:#FF0000; font-size:20px;' id='art_comentarios' name='art_comentarios'></label>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=4>
                                        <center>
                                            <span id='vigente' name='vigente'></span>
                                        </center>
                                    </td>
                                </tr>
                            </table>
                            <div class='sub-content'>
                                <table style='text-align:right;width:100%;' cellpadding=0 cellspacing=0>
                                    <tr>
                                        <td style='text-align:right;width:10%;'>Cant.:&nbsp;</td>
                                        <td style='text-align:left;width:10%;'>
                                            <div>
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <input type='text' id='cant' name='cant' size=3 onKeyUp='if(event.which==13) $("horas").focus();'/>
                                                        </td>
                                                        <td id='manana' style='display:none;'><i>Ma&ntilde;ana</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type='text' id='cant2' name='cant2' size=3 style='display:none;'/>
                                                        </td>
                                                        <td id='tarde' style='display:none;'><i>Tarde</i></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input type='text' id='cant3' name='cant3' size=3 style='display:none;'/>
                                                        </td>
                                                        <td id='noche' style='display:none;'><i>Noche</i></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                        <td style='text-align:center;font-weight:bold;' id='campo_unidad'></td>
                                        <td>&nbsp;</td>
                                        <td style='text-align:right;width:10%;' id='campo_horas'><i>cada</i>&nbsp;&nbsp;Hrs.:&nbsp;</td>
                                        <td style='text-align:left;width:10%;'>
                                            <input type='text' id='horas' name='horas' onKeyUp='if(event.which==13) $("dias").focus();' size=3/>
                                        </td>
                                        <td style='text-align:right;width:10%;' id='campo_dias'><i>durante</i>&nbsp;&nbsp;D&iacute;as:&nbsp;</td>
                                        <td style='text-align:left;width:10%;'>
                                            <input type='text' id='dias' name='dias' onKeyUp='if(event.which==13){$("med_indicaciones").focus(); agregar_medicamento();}' size=3/>
                                        </td>
                                        <td style='text-align:center;' id='campo_stock'>Saldo: <b>0</b></td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:right;'>Indicaciones:&nbsp;</td>
                                        <td style='text-align:left;' colspan=9>
                                            <input type='text' id='med_indicaciones' name='med_indicaciones' onKeyUp='if(event.which==13) agregar_medicamento();' style='width:100%;'/>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class='sub-content2' id='lista_meds' style='height:300px;overflow:auto;'>
                        </div>
                        <?php
                        }
                        ?>
                        <?php
                        if($proc['esp_campos']!='')
                        {
                        /*print("<div class='sub-content'>");
                            print("<table style='width:100%;'>");
                            $campos=explode('|', $proc['esp_campos']);
                            $valores=cargar_registros_obj("SELECT * FROM nomina_detalle_campos WHERE nomd_id=$nomd_id ORDER BY nomdc_offset");
                            for($i=0;$i<sizeof($campos);$i++)
                            {
                                if(strstr($campos[$i],'>>>'))
                                {
                                    $cmp=explode('>>>',$campos[$i]);
                                    $nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
                                }
                                else
                                {
                                    $cmp=$campos[$i]; $tipo=2;
                                }
                                print("<tr>");
                                    print("<td style='width:200px;text-align:right;' class='tabla_fila2'>$nombre :</td>");
                                    print("<td class='tabla_fila'>");
                                    if($tipo==0)
                                    {
                                        if(isset($valores[$i]['nomdc_valor']))
                                            $vact=($valores[$i]['nomdc_valor']=='true')?'CHECKED':'';
                                        else
                                            $vact='';
                                        print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");
                                    }
                                    elseif($tipo==1)
                                    {
                                        if(isset($valores[$i]['nomdc_valor']))
                                            $vact=($valores[$i]['nomdc_valor']=='true')?'CHECKED':'';
                                        else
                                            $vact='CHECKED';

                                        print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");
                                    }
                                    elseif($tipo==5)
                                    {
                                        $opts=explode('//', $cmp[2]);

                                        if(isset($valores[$i]['nomdc_valor']))
                                            $vact=$valores[$i]['nomdc_valor'];
                                        else
                                            $vact='';

                                        print("<select id='campo_$i' name='campo_$i'>");
                                        for($k=0;$k<sizeof($opts);$k++)
                                        {
                                            $opts[$k]=trim($opts[$k]);
                                            if($vact==$opts[$k])
                                                $sel='SELECTED';
                                            else
                                                $sel='';
                                            print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");
                                        }
                                        print("</select>");
                                    }
                                    else
                                    {
                                        if(isset($valores[$i]['nomdc_valor']))
                                            $vact=$valores[$i]['nomdc_valor'];
                                        else
                                            $vact='';

                                        //print("<input type='text' id='campo_$i' name='campo_$i' value='$vact' />");
                                    }
                                    print("</td>");
                                print("</tr>");
                            }
                            print("</table>");
                        print("</div>");*/
                        }
                        ?>
                    </td>
                    <td style='width:45%;' valign='top'>
                        <div class='sub-content'>
                            <img src='../../iconos/user.png' />
                            <b>Historial Cl&iacute;nico del Paciente</b>
                        </div>
                        <div class='sub-content2' id='ficha_clinica' style='height:300px;overflow:auto;'>
                        </div>
                        <!--
                        <input type='button' id='boton_alergias' name='boton_alergias' value='REGISTRO DE ALERGIAS (<?php echo $al_count; ?>)' style='font-size:24px;width:100%;' onClick='ver_alergias();' />
                        -->
                        <div class='sub-content'>
                            <img src='../../iconos/user.png'  />
                            <b>Observaciones Generales</b>
                            <!--<input type='text' id='observacion' name='observacion' style='width:100%;height:30px;'>-->
                            <textarea id='observacion' name='observacion' style='width:100%;height:180px;'></textarea>
                                <!--<img src='../../iconos/add.png' onClick='agregar_observacion();' />-->
                        </div>
                        <div class='sub-content2' id='lista_observaciones' style='height:215px;overflow:auto;'></div>
                    </td>
                </tr>
            </table>
            <?php
            if(_cax(2000))
            {
            ?>
                <center>
                <br />
                <input type='button' id='' name='' onClick='mensaje_falso();' value='--- Guardar Registro ---' />
                </center>
            <?php
            }
            else
            {
            ?>
                <center>
                <br />
                <input type='button' id='' name='' onClick='certificar_receta();' value='--- Guardar Registro ---' />
                </center>
            <?php
            }
            ?>
        </form>
    </body>
</html>
<script>
    $('nomd_codigo_no_atiende').value=$('select_atencion').value;
    var ramas=<?php echo json_encode($ramas); ?>;

    actualizar_ramas = function()
    {
        var pat_id=$('pat_id').value;
        var c=0;
        var s='<select id="patrama_id" name="patrama_id">';
        if(pat_id.charAt(0)=='P')
        {
            pat_id=pat_id.replace('P','');
            for(var i=0;i<ramas.length;i++)
            {
                if(ramas[i].pat_id==pat_id)
                {
                    c++;
                    s+='<option value="'+ramas[i].patrama_id+'">'+ramas[i].rama_nombre+'</option>';
                }
            }
        }
        if(!c)
            s+='<option value="0">(No posee ramas...)</option>';
        s+='</select>';
        $('patrama').innerHTML=s;
    }

    seleccionar_patologia = function(d)
    {
        $('pat_id').value=d[0];
        $('pat_desc').value=d[2].unescapeHTML();
        //actualizar_ramas();
    }

    autocompletar_patologias = new AutoComplete(
    'pat_desc',
    '../../autocompletar_sql.php',
    function(){
    if($('pat_desc').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=garantias_patologias&'+$('pat_desc').serialize()
    }
    }, 'autocomplete', 400, 100, 150, 1, 3, seleccionar_patologia);

    /*
    autocompletar_medicamentos = new AutoComplete(
    'codigo',
    '../../autocompletar_sql.php',
    function() {
    if($('codigo').value.length<3) return false;
    return {
        method: 'get',
        parameters: 'tipo=buscar_meds&'+$('codigo').serialize()+'&'+$('esp_id').serialize()
    }
    }, 'autocomplete', 350, 200, 250, 1, 3, ingreso_medicamentos);
    */

    /*
    seleccionar_prestacion = function(presta)
    {
        //$('codigo_prestacion').value=presta[0];
        //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
        //if(presta[3]!='')
        //    $('desc_presta').value='<b>['+presta[2]+']</b> '+presta[3];
        //else
        $('desc_presta').value=presta[2];

        $('pc_id').value=presta[4]*1;
        $('cantidad').value='1';

        $('cantidad').select();
        $('cantidad').focus();
    }
    */
   /*

    autocompletar_prestaciones = new AutoComplete('cod_presta','../../autocompletar_sql.php',
        function(){
            if($('cod_presta').value.length<3)return false;
            return {
                method: 'get',
                parameters: 'tipo=prestacion&cod_presta='+encodeURIComponent($('cod_presta').value)
            }
        }, 'autocomplete', 450, 100, 150, 1, 3, seleccionar_prestacion);
   */
    //presta=<?php echo json_encode($tmp); ?>;
    presta=[];
    presta_examen_ima=[];
    presta_examen_lab=[];
    presta_examen_eco=[];

    medicamentos=[];
    <?php
    if($obs)
    {
    ?>
        observaciones=<?php echo json_encode($observaciones); ?>;
    <?php
    }
    else
    {
    ?>
        observaciones=[];
    <?php
    }
    ?>
    <?php
    //if(!_cax(1000) AND (!$presta OR sizeof($presta)>1))
    if($presta OR sizeof($presta)>1)
    {
    ?>
        //lista_prestaciones=function()
        //{
        //    if($('cod_presta').value.length<3)
        //        return false;

         //   var params='tipo=proc_prestacion&'+$('esp_id').serialize()+'&'+$('cod_presta').serialize();
            /*if($('auge').checked) {
            params='tipo=prestacion_patologia&pat_id=';
            params+=getRadioVal('info_prestacion','pat_id')+'&'+$('cod_presta').serialize();;
            }*/
            ///return
            //{
            //    method: 'get',
             //   parameters: params
           // }
        //}

        seleccionar_prestacion = function(presta)
        {
            //$('codigo_prestacion').value=presta[0];
            //$('desc_presta').innerHTML='<center><b><u>Descripci&oacute;n de la Prestaci&oacute;n</u></b></center>'+presta[2];
            $('desc_presta').value=presta[2];

            $('pc_id').value=presta[4]*1;
            $('cantidad').value='1';

            $('cantidad').select();
            $('cantidad').focus();
        }

        autocompletar_prestaciones = new AutoComplete('cod_presta','../../autocompletar_sql.php',
        function(){
            if($('cod_presta').value.length<3)return false;
            if($('chk_solexamenes').checked == false) {
                var params='tipo=prestacion&cod_presta='+encodeURIComponent($('cod_presta').value);
            }else {
                var params='tipo=proc_prestacion&cod_presta='+encodeURIComponent($('cod_presta').value)+'&esp_id=0&exam=1';
            }
            return {
                method: 'get',
                parameters: params
            }
        }, 'autocomplete', 450, 100, 150, 1, 3, seleccionar_prestacion);
        /*
        autocompletar_prestaciones = new AutoComplete('cod_presta','../../autocompletar_sql.php',
        function()
        {
            if($('cod_presta').value.length<2)return false;
            return {
                method: 'get',
                parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre_oa').value)
            }
            }, 'autocomplete', 450, 100, 150, 1, 3, seleccionar_prestacion);
            */
            //listar_prestaciones();
    <?php
    }
    ?>
    <?php
    if($proc['esp_orden_atencion']=='t')
    {
    ?>
        $('select_atencion').value="OK";
        seleccionar_centro = function(d)
        {
            $('centro_ruta_oa').value=d[0];
            $('centro_nombre_oa').value=d[2].unescapeHTML();
        }

        autocompletar_centro = new AutoComplete(
        'centro_nombre_oa',
        '../../autocompletar_sql.php',
        function() {
        if($('centro_nombre_oa').value.length<2) return false;
        return {
        method: 'get',
        parameters: 'tipo=centros_pabellon&cadena='+encodeURIComponent($('centro_nombre_oa').value)
        }
        }, 'autocomplete', 250, 200, 150, 2, 3, seleccionar_centro);

        seleccionar_especialidad = function(d)
        {
            $('esp_id_oa').value=d[0];
            $('esp_desc_oa').value=d[2].unescapeHTML();
        }

        autocompletar_especialidades = new AutoComplete(
        'esp_desc_oa',
        '../../autocompletar_sql.php',
        function() {
        if($('esp_desc_oa').value.length<3) return false;
        return {
        method: 'get',
        parameters: 'tipo=subespecialidad&cadena='+encodeURIComponent($('esp_desc_oa').value)
        }
        }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad);

        ingreso_rut=function(datos_medico)
        {
            $('doc_id_oa').value=datos_medico[3];
            $('doc_rut_oa').value=datos_medico[1];
            $('doc_nombre_oa').value=datos_medico[0].unescapeHTML();
        }

        autocompletar_medicos = new AutoComplete(
        'doc_nombre_oa',
        '../../autocompletar_sql.php',
        function() {
        if($('doc_nombre_oa').value.length<3) return false;
        return {
        method: 'get',
        parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('doc_nombre_oa').value)
        }
        }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);

        seleccionar_inst1 = function(d)
        {
            $('inst_id_oa').value=d[0];
            $('inst_desc_oa').value=d[2].unescapeHTML();
        }

        autocompletar_institucion1 = new AutoComplete(
        'inst_desc_oa',
        '../../autocompletar_sql.php',
        function() {
        if($('inst_desc_oa').value.length<3) return false;
        return {
        method: 'get',
        parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('inst_desc_oa').value)
        }
        }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);

        seleccionar_especialidad2 = function(d)
        {
            $('esp_id2_oa').value=d[0];
            $('esp_desc2_oa').value=d[2].unescapeHTML();
        }

        autocompletar_especialidades2 = new AutoComplete(
        'esp_desc2_oa',
        '../../autocompletar_sql.php',
        function() {
        if($('esp_desc2_oa').value.length<3) return false;
        return {
        method: 'get',
        parameters: 'tipo=especialidad_sigges&cadena='+encodeURIComponent($('esp_desc2_oa').value)
        }
        }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad2);

        ingreso_rut2=function(datos_medico)
        {
            $('prof_id_oa').value=datos_medico[3];
            $('prof_rut_oa').value=datos_medico[1];
            $('prof_nombre_oa').value=datos_medico[0].unescapeHTML();
        }

        autocompletar_profesionales = new AutoComplete(
        'prof_nombre_oa',
        '../../autocompletar_sql.php',
        function() {
            if($('prof_nombre_oa').value.length<3) return false;
        return {
            method: 'get',
            parameters: 'tipo=profesionales&nombre_profesional='+encodeURIComponent($('prof_nombre_oa').value)
        }
        }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut2);

        <?php if(!$reg_examenes) { ?>
            cargar_oa();
        <?php }?>
<?php
    }
?>

    ingreso_diagnosticos=function(datos_diag)
    {
        //var cie10=datos_diag[0].charAt(0)+datos_diag[0].charAt(1)+datos_diag[0].charAt(2);
        //cie10+='.'+datos_diag[0].charAt(3);
	var cie10=datos_diag[0];
        $('nomd_diag_cod').value=cie10;
      	$('nomd_diagnostico').value=datos_diag[2].unescapeHTML();
    }

    autocompletar_diagnosticos = new AutoComplete(
    'nomd_diagnostico',
    '../../autocompletar_sql.php',
    function() {
    if($('nomd_diagnostico').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('nomd_diagnostico').value)
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_diagnosticos);

    agregar_medicamento=function()
    {
        if($('art_id').value==0)
        {
            alert('Debe seleccionar el medicamento a recetar.'); return;
        }

	if($('campo_tipo_adm').value==1)
        {
            var msj_hora = 'los d&iacute;as';
            var msj_dia = 'meses';
        }
        else
        {
            var msj_hora = 'las horas';
            var msj_dia = 'd&iacute;as';
        }

        if($('cant').value=='' || ($('cant').value*1)!=$('cant').value)
        {
            alert('Debe ingresar una cantidad.'); $('cant').focus(); return;
        }

        if($('campo_tipo_adm').value==2)
        {
            if($($('cant2').value=='' || ($('cant2').value*1)!=$('cant2').value))
            {
                alert('Debe ingresar una cantidad.'); $('cant2').focus(); return;
            }

            if($('cant3').value=='' || ($('cant3').value*1)!=$('cant3').value)
            {
                alert('Debe ingresar una cantidad.'); $('cant3').focus(); return;
            }
        }

        if($('horas').value==0 || $('horas').value=='' || ($('horas').value*1)!=$('horas').value)
        {
            alert(('Debe ingresar '+msj_hora+'.').unescapeHTML()); $('horas').focus(); return;
        }

        if($('dias').value==0 || $('dias').value=='' || ($('dias').value*1)!=$('dias').value)
        {
            alert(('Debe ingresar los '+msj_dia+'.').unescapeHTML()); $('dias').focus(); return;
        }

        if($('campo_tipo_adm').value==1)
        {
            $('horas').value=$('horas').value*24;
            $('dias').value=$('dias').value*30;
        }
        else if($('campo_tipo_adm').value==2)
        {
            var txt_indicaciones='[</b>';
            if($('cant').value!='')
            {
                if($('cant').value!=0)
                {
                    txt_indicaciones+=$('cant').value+' <b>'+$('campo_unidad').innerHTML+'</b>  en la Ma&ntilde;ana ';
		 }
            }

            if($('cant2').value!='')
            {
                if($('cant2').value!=0)
                {
                    txt_indicaciones+=$('cant2').value+' <b>'+$('campo_unidad').innerHTML+'</b>  en la Tarde ';
                }
            }

            if($('cant3').value!='')
            {
                if($('cant3').value!=0)
                {
                    txt_indicaciones+=$('cant3').value+' <b>'+$('campo_unidad').innerHTML+'</b>  en la Noche ';
                }
            }

            txt_indicaciones+='<b>]</b>';
            txt_indicaciones=txt_indicaciones.unescapeHTML();
            $('med_indicaciones').value=txt_indicaciones+' - '+$('med_indicaciones').value;
            $('horas').value=24;
            $('cant').value=($('cant').value)*1+($('cant2').value)*1+($('cant3').value)*1;
        }

        medicamentos.push( [
            $('art_id').value,
            $('art_codigo').value,
            $('art_nombre').value,
            $('cant').value.replace(',','.'),
            $('horas').value,
            $('dias').value,
            $('med_indicaciones').value,
            $('campo_unidad').innerHTML,
            $('art_stock').value*1,
            $('campo_ua').value,
            $('campo_ua_cant').value,
            $('campo_tipo_adm').value
        ] );

        $('art_id').value=0;
        $('art_codigo').value='';
        $('art_nombre').value='';
        $('codigo').value='';
        $('remed').innerHTML='';
        $('campo_unidad').innerHTML='';
        $('cant').value='';
        $('manana').style.display='none';
        $('cant2').value='';
        $('cant2').style.display='none';
        $('tarde').style.display='none';
        $('cant3').value='';
        $('cant3').style.display='none';
        $('noche').style.display='none';
        $('horas').value='';
        $('horas').disabled=false;
        $('dias').value='';
        $('med_indicaciones').value='';
        listar_medicamentos();
        $('codigo').value='';
        $('codigo').focus();
    }

    listar_medicamentos=function()
    {
        var val_cr=$('tipo_receta').value*1;
        if(val_cr==0)
        {
            // Aguda
            var txt_horas='horas';
            var txt_dias='d&iacute;as';
        }
        else
        {
            // Crónica
            var txt_horas='d&iacute;as';
            var txt_dias='meses';
        }
        var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>C&oacute;digo</td><td>Descripci&oacute;n</td><td>Indicaciones</td><td>Total/Saldo</td><td>Unidad</td><td>Eliminar</td></tr>';
        for(var i=0;i<medicamentos.length;i++)
        {
            clase=(i%2==0)?'tabla_fila':'tabla_fila2';
            if(medicamentos[i][2].length>37)
                var descr=medicamentos[i][2].substr(0,67)+'...';
            else
                var descr=medicamentos[i][2];

            html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
            html+='<td style="text-align:center;font-weight:bold;">'+medicamentos[i][1]+'</td>';
            html+='<td style="text-align:left;">'+medicamentos[i][2]+'</td>';



            if(medicamentos[i][4]*1<24)
            {
                var div_h=1;
                var txt_horas='horas';
            }
            else
            {
                if((medicamentos[i][4]%24)==0)
                {
                    var div_h=24;
                    var txt_horas='d&iacute;a(s)';
                }
                else
                {
                    var div_h=1;
                    var txt_horas='horas';
                }
            }



            if((medicamentos[i][5]*1)<=30)
            {
                var div_d=1;
                var txt_dias='d&iacute;a(s)';
            }
            else
            {
                if(medicamentos[i][5]%30==0)
                {
                    var div_d=30;
                    var txt_dias='mes(es)';
                }
                else
                {
                    var div_d=1;
                    var txt_dias='d&iacute;a(s)';
                }
            }

            txt_dosis='<i>'+medicamentos[i][3]+' <b>'+medicamentos[i][7]+'</b> cada '+(medicamentos[i][4]/div_h)+' '+txt_horas+' por '+medicamentos[i][5]/div_d+' '+txt_dias+'.</i>';
            html+='<td style="text-align:center;">'+txt_dosis+'</td>';

            if(medicamentos[i][11]*1==0 || medicamentos[i][5]*1<=30)
            {
                var total=Math.ceil(1*((medicamentos[i][5]*24))/(medicamentos[i][4])*(medicamentos[i][3]));
                var total_adm=Math.ceil(1*((medicamentos[i][5]*24))/(medicamentos[i][4])*(medicamentos[i][3]));
                html+='<td style="text-align:right;font-weight:bold;">'+total+'</td><td style="text-align:left;">'+medicamentos[i][7]+'</td>';
            }
            else
            {
                var total=Math.floor((medicamentos[i][5]*24)/(medicamentos[i][4])*(medicamentos[i][3]));
                var total_adm=Math.floor(total/(medicamentos[i][5]/30));
                html+='<td style="text-align:right;font-weight:bold;">'+total_adm+' ('+total+')</td><td style="text-align:left;">'+medicamentos[i][7]+'</td>';
            }

            html+='<td rowspan=2><center><img src="../../iconos/delete.png" style="cursor: pointer;" onClick="eliminar_medicamento('+i+');"></center></td></tr>';

            if(medicamentos[i][6]!='')
                html+='<tr class="'+clase+'"><td style="text-align:right;">Otras Indicaciones:</td><td colspan=2><i>'+medicamentos[i][6]+'</i></td>';
            else
                html+='<tr class="'+clase+'"><td style="text-align:right;">Otras Indicaciones:</td><td colspan=2 style="color:#555555;"><i>(Sin indicaciones adicionales...)</i></td>';

            var cantdisp=Math.ceil(total_adm/medicamentos[i][10]*1);
            if(cantdisp*1<=medicamentos[i][8]*1)
                color='blue';
            else
                color='red';

                html+='<td style="text-align:right;font-weight:bold;color:'+color+'">'+cantdisp+' / '+medicamentos[i][8]+'</td><td style="text-align:left;">'+medicamentos[i][9]+'</td></tr>';
        }
        html+='</table>'
        $('lista_meds').innerHTML=html;
    }

    eliminar_medicamento = function(id)
    {
        medicamentos=medicamentos.without(medicamentos[id]);
        listar_medicamentos();
    }

    ingreso_medicamentos=function(med)
    {
        if($('art_id').value==med[5])
        {

        }
      	$('codigo').value=med[0].unescapeHTML();
      	$('remed').value=med[2].unescapeHTML();
      	$('art_id').value=med[5];
      	$('art_codigo').value=med[0].unescapeHTML();
      	$('art_nombre').value=med[2].unescapeHTML();
      	$('art_comentarios').innerHTML=med[10].unescapeHTML();
      	$('cant').focus();
      	$('campo_stock').innerHTML='<img src="../../imagenes/ajax-loader1.gif" />';
      	if(med[10]!='')
            $('tr_coments').style.display='';
        else
            $('tr_coments').style.display='none';
        var myAjax=new Ajax.Request('datos_medicamento.php',
        {
            method:'post',
            parameters:'art_id='+med[5]+'&pac_id='+$('pac_id').value,
            onComplete:function(r)
            {
                var d=r.responseText.evalJSON(true);
		$('campo_unidad').innerHTML=d.art_unidad_administracion;
		$('remed').innerHTML=d.art_glosa;
		$('campo_ua').value=d.forma_nombre;
		$('campo_ua_cant').value=d.art_unidad_cantidad_adm;
		if(d.cnt>0)
                {
                    $('vigente').innerHTML='vigente';
		}
                else
                {
                    $('vigente').innerHTML='';
		}
		$('art_stock').value=d.stock*1;
		$('campo_stock').innerHTML='Saldo: <b>'+d.stock+'</b>';
		$('campo_tipo_adm').value=d.art_tipo_adm;
		if($('campo_tipo_adm').value*1==1)
                {
                    $('manana').style.display='none';
                    $('cant2').style.display='none';
                    $('tarde').style.display='none';
                    $('cant3').style.display='none';
                    $('noche').style.display='none';
                    $('horas').disabled=false;
                    $('horas').value='';
                    $('campo_horas').innerHTML='cada D&iacute;as:';
                    $('campo_dias').innerHTML='por Meses:';
		}
                else if($('campo_tipo_adm').value*1==2)
                {
                    //console.log($('campo_tipo_adm').value);
                    $('manana').style.display='';
                    $('cant2').style.display='';
                    $('tarde').style.display='';
                    $('cant3').style.display='';
                    $('noche').style.display='';
                    $('horas').disabled=true;
                    $('horas').value=24;
                    $('campo_horas').innerHTML='cada Hrs.:';
                    $('campo_dias').innerHTML='por D&iacute;as:';
                }
                else
                {
                    var val_cr=$('tipo_receta').value*1;
                    if(val_cr==0)
                    {
                        // Aguda
                        var txt_horas='Cada Hrs.:';
                        var txt_dias='Durante D&iacute;as.:';
                    }
                    else
                    {
                        // Crónica
                        var txt_horas='D&iacute;as';
                        var txt_dias='Meses';
                    }
                    $('campo_horas').innerHTML=txt_horas;
                    $('campo_dias').innerHTML=txt_dias;


                    $('horas').disabled=false;
                    $('horas').value='';
                    $('manana').style.display='none';
                    $('cant2').style.display='none';
                    $('tarde').style.display='none';
                    $('cant3').style.display='none';
                    $('noche').style.display='none';
                }
            }
        });
    }
    <?php
    if($recetas)
    {
    ?>
        autocompletar_medicamentos = new AutoComplete(
        'codigo',
        '../../autocompletar_sql.php',
        function() {
        if($('codigo').value.length<3) return false;
        return {
            method: 'get',
            parameters: 'tipo=buscar_meds&'+$('codigo').serialize()+'&bodega_id=36'
        }
        }, 'autocomplete', 350, 200, 250, 1, 3, ingreso_medicamentos);
    <?php
    }
    ?>
    cargar_ficha=function()
    {



        var myAjax=new Ajax.Updater('ficha_clinica','../../prestaciones/ingreso_nominas/historial_medicamentos.php',
        {
            method:'get',
            parameters:$('paciente').serialize()+'&'+'div=rec&'+$('nomd_id').serialize(),
        });
    }

    cargar_observaciones=function()
    {

        var myAjax=new Ajax.Updater('lista_observaciones','../../recetas/entregar_recetas/historial_medicamentos.php',
        {
            method:'get',
            parameters:$('paciente').serialize()+'&'+'div=obs'+'&nomd_id='+<?php echo $nomd_id; ?>
        });
    }

    agregar_observacion = function()
    {
        var observacion=$('observacion').value;
 	observacion = observacion.replace(/^\s*|\s*$/g,"");
        if(observacion=='')
        {
            $('observacion').value='';
            return;
        }
        var fecha= new Date();
        if(fecha.getDate()<10)
        {
            var dia='0'+fecha.getDate()
        }
        else
        {
            var dia=fecha.getDate()
        }
	if(fecha.getDay()<10)
        {
            var mes='0'+(fecha.getMonth()+1)
        }
        else
        {
            var mes=(fecha.getFullMonth()+1)
        }
        fecha = (dia+'/'+mes+'/'+fecha.getFullYear());
        var profesional=<?php echo $_SESSION['sgh_usuario_id']?>;
        var num=observaciones.length;
        observaciones[num]=new Object();
        observaciones[num].obs_id=0;
        observaciones[num].fecha=fecha;
        observaciones[num].profesional=profesional;
        observaciones[num].observacion=observacion;
        $('observacion').value='';
        listar_obs();
    }

    listar_obs=function()
    {
        var html='<table style="width:100%;">';
            html+='<tr class="tabla_header">';
                //html+='<td>Fecha</td><td>Prof.</td>';
                html+='<td>Observaciones</td>';
                //html+='<td>Eliminar</td></tr>';
        for(var i=0;i<observaciones.length;i++)
        {
            clase=(i%2==0)?'tabla_fila':'tabla_fila2';
            html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\';">';
                html+='<td style="text-align:center;">'+observaciones[i].fecha+'</td>';
                html+='<td style="text-align:center;">'+observaciones[i].doc+'</td>';
                html+='<td>'+observaciones[i].observacion+'</td>';
		//html+='<td><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="eliminar_signos('+i+');" ></center></td></tr>';
        }
        html+='</table>'
        $('lista_observaciones').innerHTML=html;
    }

    mod_tipo_receta=function()
    {
        var val=$('tipo_receta').value*1;
        if(val==0)
        {
            // Aguda
            $('campo_horas').innerHTML='Cada Hrs.:';
            $('campo_dias').innerHTML='Durante D&iacute;as.:';
        }
        else
        {
            // Cronica
            $('campo_horas').innerHTML='D&iacute;as:';
            $('campo_dias').innerHTML='Meses:';
        }



    }

    var inputs=$('__contenido').getElementsByTagName('input');
    for(var i=0;i<inputs.length;i++)
    {
        Event.observe(inputs[i], 'focus', function()
        {
            this.setStyle({border: '2px solid red'});
        });
        Event.observe(inputs[i], 'blur', function()
        {
            this.setStyle({border: ''});
        });
    }
    var inputs=$('__contenido').getElementsByTagName('select');
    for(var i=0;i<inputs.length;i++)
    {
        Event.observe(inputs[i], 'focus', function()
        {
            this.setStyle({border: '3px solid red'});
	});

        Event.observe(inputs[i], 'blur', function()
        {
            this.setStyle({border: ''});
        });
    }
    cargar_ficha();
    cargar_observaciones();
    window.moveTo(0, 0);
    window.resizeTo(screen.availWidth,screen.availHeight)
</script>
