<?php
    require_once('../../conectar_db.php');
    $fecha1=date('d/m/Y');
    $fecha2=date('d/m/Y', mktime(0,0,0,date('m'),date('d')+3,date('Y')));
    
    //--------------------------------------------------------------------------
    $nomd_id=$_GET['nomd_id']*1;
    if(isset($_GET['interconsulta']))
    {
        if(($_GET['interconsulta']*1)==1)
        {
            $esp_id=$_GET['esp_id']*1;
            $pac_id=$_GET['pac_id']*1;
            $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id", true);
            //--------------------------------------------------------------------------
            $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id;");
            //--------------------------------------------------------------------------
            $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
            $esp_examen=false;
            $pac_id=0;
            $doc_id=0;
            ///-----------------------------------------------------------------
            $f1=pg_escape_string(date('d/m/Y'));
            $f2='';
            $h1='';
            $h2='';
            $interconsulta=true;
        }
    }
    else
    {
        $interconsulta=false;
        if(isset($_GET['esp_examen']))
        {
			//new_win = window.open('prestaciones/ingreso_nominas/buscar_citacion.php?nomd_id='+nomd_id+'&esp_examen='+esp_id+'&sol_id='+sol_id+'&txt_examenes='+$('txt_examenes').value,
            $esp_examen=$_GET['esp_examen']*1;
            $sol_id=$_GET['sol_id']*1;
            $examenes_sol=pg_escape_string($_GET['txt_examenes']);
            $sol_examd_nomd_id=$_GET['sol_examd_nomd_id']*1;
        }
        else
        {
            $esp_examen=false;
        }
        //--------------------------------------------------------------------------
        if($esp_examen!=false)
        {
            $reg_esp_examen=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_examen.";");
            if(!$reg_esp_examen)
            {
                $reg_esp_examen=false;
            }
        }
        //--------------------------------------------------------------------------
        if($nomd_id==0 and isset($_GET['esp_examen'])) {
			$solicitud=cargar_registro("SELECT * FROM solicitud_examen JOIN pacientes ON pac_id=sol_pac_id WHERE sol_exam_id=".$sol_id.";");
			if($solicitud){
				//print_r($solicitud);
				$pac_id2=$solicitud['pac_id']*1;
				$pac_id=0;
				$esp_id=$solicitud['sol_esp_id']*1;
				$doc_id=$solicitud['sol_doc_id']*1;
				$f1=pg_escape_string(date('d/m/Y'));
				$f2='';
				$h1='';
				$h2='';
				
			} else {
				print("<center><h2>Problemas al solicitar informaci&oacute;n.</h2></center>");
				exit();
			}
		} else {
			$nd=cargar_registro("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomd_id=".$nomd_id.";");
			$pac_id2=$nd['pac_id']*1;
			$pac_id=0;
			$esp_id=$nd['nom_esp_id']*1;
			$doc_id=$nd['nom_doc_id']*1;
			$f1=pg_escape_string(date('d/m/Y'));
			$f2='';
			$h1='';
			$h2='';
		}
        //--------------------------------------------------------------------------
        $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=".$pac_id2."", true);
        //--------------------------------------------------------------------------
        $esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=".$esp_id.";");
        //--------------------------------------------------------------------------
        if(!$esp_examen)
            $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
        else
            $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_id=".$esp_examen." ORDER BY esp_desc", $esp_examen, '', '');
        //--------------------------------------------------------------------------
        $doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=$doc_id;");
        //--------------------------------------------------------------------------
    }
    if($pac_id!=0)
        $pac_w="pac_id=$pac_id";
    else
        $pac_w='pac_id=0';
    //--------------------------------------------------------------------------
    if($esp_examen)
    {
        $doc_id=0;
    }
    //--------------------------------------------------------------------------
    if($doc_id!=0)
        $doc_w="nom_doc_id=$doc_id";
    else
        $doc_w='true';
    //--------------------------------------------------------------------------
    if(!$esp_examen)
    {
        if($esp_id!=0)
            $esp_w="nom_esp_id=$esp_id";
        else
            $esp_w='true';
    }
    else
    {
        if($esp_id!=0)
            $esp_w="nom_esp_id=$esp_examen";
        else
            $esp_w='true';
    }
    //--------------------------------------------------------------------------
    if($f1!='')
        $f1_w="nom_fecha>='$f1'";
    else
        $f1_w='true';
    //--------------------------------------------------------------------------
    if($f2!='')
        $f2_w="nom_fecha<='$f2'";
    else
        $f2_w='true';
    //--------------------------------------------------------------------------
    if($h1!='')
        $h1_w="nomd_hora>='$h1'";
    else
        $h1_w='true';
    //--------------------------------------------------------------------------
    if($h2!='')
        $h2_w="nomd_hora<='$h2'";
    else
        $h2_w='true';
    //--------------------------------------------------------------------------
    $tipo_atencion=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo", true);
    $tipoatencionhtml='';
    for($i=0;$i<count($tipo_atencion);$i++)
    {
        $tipoatencionhtml.='<option value="'.$tipo_atencion[$i]['nom_motivo'].'">'.$tipo_atencion[$i]['nom_motivo'].'</option>';
    }
    if($pac_w=='pac_id=0' AND $esp_w=='true' AND $doc_id=='true') 
    {
?>
        <center><h2>Ingrese par&aacute;metros para su b&uacute;squeda.</h2></center>
<?php 
        exit();	
    }
    //--------------------------------------------------------------------------
    $consulta="
    SELECT *, to_char(nom_fecha, 'D') AS dow,nom_motivo,
    (SELECT COUNT(DISTINCT nomd_hora) FROM nomina_detalle where nomina_detalle.nom_id=nomina.nom_id)as cantidad
    FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL)  AND $pac_w AND $esp_w AND $doc_w AND
    $f1_w AND $f2_w AND $h1_w AND $h2_w
    ORDER BY nom_fecha, nomd_hora
    LIMIT 50";
    
    //print($conslta);
    
    $c=cargar_registros_obj($consulta, true);
    /*
    //--------------------------------------------------------------------------
    $tmp=cargar_registro("
    SELECT count(*) AS cuenta  FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND NOT pac_id=0 AND $esp_w AND $doc_w AND
    $f1_w AND $f2_w AND $h1_w AND $h2_w
    ", true);
    //--------------------------------------------------------------------------
    $num=$tmp['cuenta']*1;
    //--------------------------------------------------------------------------
    $tmp2=cargar_registro("
    SELECT count(*) AS cuenta  FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND pac_id=0 AND $esp_w AND $doc_w AND
    $f1_w AND $f2_w AND $h1_w AND $h2_w
    ", true);
    //--------------------------------------------------------------------------
    $num2=$tmp2['cuenta']*1;
    //--------------------------------------------------------------------------
    */
    /*	
    if($num>0)
    {
        echo "<center><h3>Hay <u>$num cupos utilizados</u> y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
    }
    else
    {
        echo "<center><h3>No hay cupos utilizados y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
    }
    */
?>
<script>
    //--------------------------------------------------------------------------
    listar_cupos = function()
    {
        var myAjax = new Ajax.Updater(
        'lista_cupos',
        'buscar_cupos_reasignar.php',
        {
            method:'post',
            evalScripts:true,
            parameters:$('form_cupos_reasignar').serialize()
        });
    }
    //--------------------------------------------------------------------------
    
    
    
    usar_citacion=function(nom_id,nomd_hora)
    {
        var parameter="";
                
        if($('esp_examen').value=="")
        {
            if($('interconsulta').value=="")
            {
                if(nomd_hora=="extra")
                    parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent('00:00')+'&nomd_hora_extra='+encodeURIComponent($('nomd_hora_extra_'+nom_id).value)+'&nomd_id='+$('nomd_id').value+'&reagendar=1';
                else
                    parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&reagendar=1';
            }
            else
            {
                if(nomd_hora=="extra")
                    parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent('00:00')+'&nomd_hora_extra='+encodeURIComponent($('nomd_hora_extra_'+nom_id).value)+'&nomd_id='+$('nomd_id').value+'&interconsulta=1&inter_id='+$('inter_id').value;
                else
                    parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&interconsulta=1&inter_id='+$('inter_id').value;
                    
            }
        }
        else
        {
            if(nomd_hora=="extra")
                parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent('00:00')+'&nomd_hora_extra='+encodeURIComponent($('nomd_hora_extra_'+nom_id).value)+'&nomd_id='+$('nomd_id').value+'&examen=1&sol_exam_id='+$('sol_id').value+'&examenes_sol='+$('examenes_sol').value+'&sol_examd_nomd_id='+$('sol_examd_nomd_id').value;
            else
                parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&examen=1&sol_exam_id='+$('sol_id').value+'&examenes_sol='+$('examenes_sol').value+'&sol_examd_nomd_id='+$('sol_examd_nomd_id').value;
        }
        /*
        <?php
        if(!$esp_examen)
        {
            if(!$interconsulta)
            {
        ?>
                parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&reagendar=1';
        <?php
            }
            else
            {
        ?>
                
                parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&interconsulta=1&inter_id='+<?php echo ($_GET['inter_id']*1);?>;
        <?php
            }
        }
        else
        {
        ?>
                parameter='nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&nomd_id='+$('nomd_id').value+'&examen=1&sol_exam_id='+<?php echo $sol_id;?>+'&examenes_sol='+$j('#examenes_sol').val();
        <?php
        }
        ?>
        */
        var myAjax=new Ajax.Request('sql_tomar_cupo.php',
        {
            method:'post',
            parameters:parameter,
            onComplete:function(resp2)
            {
                if(resp2.responseText!="X")
                {
                    <?php
                    if(!$esp_examen)
                    {
                        if(!$interconsulta)
                        {
                    ?>
                            window.opener.listar_reasignar();
                    <?php
                        }
                        else
                        {
                    ?>
                            window.opener.realizar_busqueda(0);
                    <?php
                        }
                    }
                    else
                    {
                    ?>
                        window.opener.listar_solicitudes();
                    <?php
                    }
                    ?>
                    <?php
                    if(!$interconsulta)
                    {
                    ?>
                        imprimir_citacion(resp2.responseText*1);
                    <?php
                    }
                    else
                    {
                    ?>
                        imprimir_citacion(resp2.responseText*1);
                        //gestiones_citacion_inter(<?php //echo ($_GET['inter_id']*1);?>)
                        //window.close();
                    <?php
                    }
                    ?>
                }
                else
                {
                    alert("CUPO SELECCIONADO YA SE ENCUENTRA ASIGANDO");
                    return;
                }
            }
        });
    }
    //--------------------------------------------------------------------------
    function imprimir_citacion(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win =
        window.open('citaciones2.php?nomd_id='+nomd_id,
        '_self', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    //--------------------------------------------------------------------------
    gestiones_citacion_inter=function(inter_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        
        new_win = new window.open('gestionar_citacion.php?inter=1&inter_id='+inter_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
        
        
        /*
        var win = new Window("gestion_citacion", {className: "alphacube", 
            top:top, left:left, 
            width: 680, height: 500, 
            title: '<center><img src="../../iconos/table.png"> Gesti&oacute;n de citaci&oacute;n</center>',
            minWidth: 650, minHeight: 450,
            maximizable: false, minimizable: false, 
            wiredDrag: true, resizable: true }); 
            win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
            var params='inter=1&inter_id='+inter_id;
            win.setAjaxContent('gestionar_citacion.php', 
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
            $("gestion_citacion").win_obj=win;
            win.setDestroyOnClose();
            win.showCenter();
            win.show(true);
        */
        
    }
    
    mostrar_horas=function(nom_id)
    {
        var win = new Window("listar_hora", {className: "alphacube", 
        top:20, left:0, 
        width: 500, height: 400, 
        title: '<center><img src="../../iconos/table.png">Seleccionar hora para citaci&oacute;n Extra</center>',
        minWidth: 700, minHeight: 400,
        maximizable: false, minimizable: false, 
        wiredDrag: true, resizable: true }); 
        win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
        var params='nom_id='+nom_id;
        win.setAjaxContent('listar_horas.php', 
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
        $("listar_hora").win_obj=win;
        win.setDestroyOnClose();
        win.showCenter();
        win.show(true);
        
    }
    

</script>
<?php
    if(!$c) 
    {
?>
        <!--	<center><h2>No hay cupos libres similares para su b&uacute;squeda.</h2></center>-->
<?php 
        //exit();
    }
?>
<script>
    /*
    listar_cupos = function()
    {
        var myAjax = new Ajax.Updater(
        'lista_cupos',
        'buscar_cupos_reasignar.php',
        {
            method:'post',
            evalScripts:true,
            parameters:$('form_cupos_reasignar').serialize()
        });
    }
	
    usar_citacion=function(nom_id,nomd_hora)
    {
        var myAjax=new Ajax.Request('sql_tomar_cupo.php',
        {
            method:'post',
            parameters:'nom_id='+nom_id+'&pac_id='+$('pac_id').value+'&nomd_hora='+encodeURIComponent(nomd_hora)+'&reagendar=1',
            onComplete:function(resp2)
            {
                //var fn2=window.opener.abrir_nomina.bind(window.opener);
                //fn2(window.opener.$("folio_nomina").value, 1);
                imprimir_citacion(resp2.responseText*1);
                //window.close();
            }
        });
    }

    function imprimir_citacion(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('citaciones2.php?nomd_id='+nomd_id,
       	'_self', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
    */
</script>
<html>
    <title>Busqueda de Cupos</title>
    <?php cabecera_popup('../..'); ?>
    <body class='fuente_por_defecto popup_background'>
        <center>
            <form id='form_cupos_reasignar'>
                <input type="hidden" id='nomd_id' name='nomd_id' value='<?php echo $_GET['nomd_id']*1;?>'>
                <input type="hidden" id='pac_id' name='pac_id' value='<?php echo $pac['pac_id']*1;?>'>
                <input type="hidden" id='examenes_sol' name='examenes_sol' value='<?php echo $examenes_sol;?>'>
                <input type="hidden" id='interconsulta' name='interconsulta' value='<?php echo $interconsulta;?>'>
                <input type="hidden" id='esp_examen' name='esp_examen' value='<?php echo $esp_examen;?>'>
                <input type="hidden" id='sol_id' name='sol_id' value='<?php echo $sol_id;?>'>
                <input type="hidden" id='sol_examd_nomd_id' name='sol_examd_nomd_id' value='<?php echo $sol_examd_nomd_id;?>'>
                <input type="hidden" id='inter_id' name='inter_id' value='<?php echo $_GET['inter_id']*1;?>'>
                
                
                
                <div class='sub-content'>
                    <table style='width:100%;'>
                        <tr>
                            <td style='width:30%;font-size:12px;'>
                                <center>
                                    <h3>
                                        Paciente: <u><?php echo $pac['pac_rut'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'].' '.$pac['pac_nombres']; ?></u>
                                        <br/>
                                        <br/>Especialidad: <u><?php echo $esp['esp_desc']; ?></u>
                                        <br />Profesional: <?php echo '['.$doc['doc_rut'].'] '.$doc['doc_nombres'].' '.$doc['doc_paterno'].' '.$doc['doc_materno']; ?>
                                        <?php
                                        if($esp_examen!=false)
                                        {
                                        ?>
                                            <br/>
                                            <br/>
                                            <font color='blue'>
                                                SOLICITUD DE EX&Aacute;MEN: <u><?php echo $reg_esp_examen['esp_desc']; ?></u>
                                            </font>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        if($interconsulta)
                                        {
                                        ?>
                                            <br/>
                                            <br/>
                                            <font color='blue'>
                                                SOLICITUD DE AGENDAMIENTO DE INTERCONSULTA
                                            </font>
                                        <?php
                                        }
                                        ?>
                                    </h3>
                                    <br />
                                    <br />
                                </center>
                            </td>
                            <td valign='top'>
                                <table style='width:100%;font-size:12px;'>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
                                        <td colspan="2" class='tabla_fila' >
                                            <input type='hidden' id='doc_id' name='doc_id' value='0' />
                                            <input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;'  />
                                            <input type='text' id='profesional' name='profesional'  size=60 onDblClick='limpiar_profesional();' style='text-align:left;font-size:10px;' DISABLED size=35 />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Especialidad</td>
                                        <td colspan="2" class='tabla_fila' id='select_especialidades' >
                                            <select id='esp_id' name='esp_id' onChange='listar_cupos();'>
                                                <option value=-1 SELECTED>Seleccionar Especialidad......</option>
                                                <?php echo $espechtml; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Tipo de Atenci&oacute;n</td>
                                        <td colspan="2" class='tabla_fila' >
                                            <select id='select_nom_motivo' name='select_nom_motivo'/>
                                                <option value='-1'>(Todos los Tipos...)</option>
                                                <?php echo $tipoatencionhtml;?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Tipo de Cupos:</td>
                                        <td class='tabla_fila' colspan=2>
                                            <select id='select_tipo_cupos' name='select_tipo_cupos'>
                                                <option value='0' selected="">Cupos Normales</option>
                                                <option value='1'>Cupos Extras</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Fecha Inicial:</td>
                                        <td colspan="2" class='tabla_fila'>
                                            <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;'  value='<?php echo $fecha1?>'>
                                            <img src='../../iconos/date_magnify.png' id='fecha1_boton'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='tabla_fila2' style='text-align:right;'>Fecha Final:</td>
                                        <td colspan="2" class='tabla_fila'>
                                            <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo $fecha2?>'>
                                            <img src='../../iconos/date_magnify.png' id='fecha2_boton'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='3'>
                                            <center>
                                                <input type='button' id='lista_reasignar' onClick='listar_cupos();' value='Buscar Cupos...'>
                                                <?php
                                                if(isset($_GET['inter_id']))
                                                {
                                                ?>
                                                    <!--<input type='button' onclick='gestiones_citacion_inter(<?php //echo ($_GET['inter_id']*1);?>)' title='Gestiones Citaci&oacute;n' alt='Gestiones Citaci&oacute;n' style='cursor:pointer;' value='Gestiones Citaci&oacute;n..'>-->
                                                <?php
                                                }
                                                ?>
                                            </center>
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--
                    <center>
                        <h3>Paciente: <u><?php echo $pac['pac_rut'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'].' '.$pac['pac_nombres']; ?></u><br/><br/>Especialidad: <u><?php echo $esp['esp_desc']; ?></u><br />Profesional: <?php echo '['.$doc['doc_rut'].'] '.$doc['doc_nombres'].' '.$doc['doc_paterno'].' '.$doc['doc_materno']; ?></h3><br /><br />
                    </center>
                    -->
                    <!--
                    <table>
                        <tr>
                            <td class='tabla_fila2' style='text-align:right;'>Especialidad:</td>
                            <td class='tabla_fila'>
                                <input type='hidden' id='esp_id' name='esp_id' value='0' />
                                <input type='text' size=45 id='esp_desc' name='esp_desc' value='' onDblClick='limpiar_especialidad();' style='font-size:16px;' />
                            </td>
                        </tr>
                        </table>
                    -->
                </div>
                <div class='sub-content2' id='lista_cupos'>
                <?php
                if(!$c)
                {
                    print("<center><h2>No hay cupos libres similares para su b&uacute;squeda.</h2></center>");
                }
                else
                {
                ?>
                    <table style='width:100%;font-size:12px;' cellspacing=0>
                        <tr class='tabla_header' style='font-size:14px;'>
                            <td>D&iacute;a de la Semana</td>
                            <td>Fecha</td>
                            <td>Hora</td>
                            <td>Tipo Atenci&oacute;n</td>
                            <?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
                            <?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
                            <td style='width:5%;'>Reagendar N&oacute;mina</td>
                        </tr>
                        <?php
                        $dias=array('','Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');
                        for($i=0;$i<sizeof($c);$i++) 
                        {
                            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                                print("<td style='text-align:right;font-size:18px;'><i>".$dias[$c[$i]['dow']*1]."</i></td>");
                                print("<td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>");
                                print("<td style='text-align:center;font-size:20px;'>".substr($c[$i]['nomd_hora'],0,5)."</td>");
                                print("<td style='text-align:center;font-size:20px;'>".$c[$i]['nom_motivo']."</td>");
                                if($esp_w=='true')
                                    print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                                if($doc_w=='true')
                                    print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                                if(($c[$i]['cantidad']*1)!=1)
                                {
                                    print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nom_id'].",\"".substr($c[$i]['nomd_hora'],0,5)."\")'></center></td>");
                                }
                                else
                                {
                                    print("<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nom_id'].",\"".substr($c[$i]['nomd_hora'],0,5)."_".$c[$i]['nomd_id']."\")'></center></td>");
                                }
                            print("</tr>");
                        }
                        ?>
                    </table>
                <?php
                }
                ?>
                </div>
            </form>
        </center>
    </body>
</html>
<script>
    //--------------------------------------------------------------------------
    seleccionar_profesional = function(d)
    {
        $('doc_rut').value=d[1];
        $('profesional').value=d[2].unescapeHTML();
        $('doc_id').value=d[0];
    }
    //--------------------------------------------------------------------------
    limpiar_profesional = function(d)
    {
        $('doc_rut').value='';
        $('profesional').value='';
        $('doc_id').value=0;
    }
    //--------------------------------------------------------------------------
    autocompletar_profesionales = new AutoComplete(
    'doc_rut', '../../autocompletar_sql.php',
    function() {
        if($('doc_rut').value.length<2) return false;
        return {
            method: 'get',
            parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
        }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);
    
    
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });
</script>
