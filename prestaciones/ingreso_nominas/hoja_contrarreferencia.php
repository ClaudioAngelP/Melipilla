<?php
    require_once("../../conectar_db.php");
    $nomd_id=$_POST['nomd_id']*1;
    $pac_id=$_POST['pac_id']*1;
    $esp_id=$_POST['esp_id']*1;
    $llamada=false;
    if(isset($_POST['llamada'])) {
        $llamada=true;
    }
    $pac=cargar_registro("SELECT *,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,
    date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,
    date_part('day',age(now()::date, pac_fc_nac)) as edad_dias 
    FROM pacientes WHERE pac_id=$pac_id",true);
    
    
    $nomdr_fecha_ingreso='';
    $nomdr_fecha_alta='';
    $nomdr_diagnostico1='';
    $reg_inter=cargar_registro("select * from interconsulta where inter_nomd_id=$nomd_id",true);
    if($reg_inter){
        $nomdr_diagnostico1=$reg_inter['inter_fundamentos'];
    }
    $nomdr_diagnostico2='';
    $nomdr_biopsia_nro='';
    $nomdr_est_id=0;
    $nomdr_est_id2=0;
    $nomdr_fecha2='';
    $nomdr_detalle='';
    $nomdr_tratamiento='';
    $nomdr_indicaciones='';
    $nomdr_control='';
    $nomdr_control_aps='';
    $nomdr_pertinencia='';
    $nomdr_porque='';
    /*
    print("SELECT nomina_detalle_referencia.*,inst_nombre,date_trunc('Second',nomdr_fecha) as fecha_referencia ,
    funcionario.*,
    esp_desc,
    doc_nombres,
    doc_paterno,
    doc_materno
    FROM nomina_detalle_referencia 
    LEFT JOIN instituciones on inst_id=nomdr_est_id
    LEFT JOIN funcionario on func_id=nomdr_func_id
    LEFT JOIN nomina_detalle as nd on nd.nomd_id=nomina_detalle_referencia.nomd_id
    LEFT JOIN nomina on nomina.nom_id=nd.nom_id
    LEFT JOIN doctores on doc_id=nomina.nom_doc_id
    LEFT JOIN especialidades on esp_id=nomina.nom_esp_id
    WHERE nomina_detalle_referencia.nomd_id=$nomd_id");
     * 
     */
    
    $referencia=cargar_registro("SELECT nomina_detalle_referencia.*,
    inst1.inst_nombre as inst_nombre,date_trunc('Second',nomdr_fecha) as fecha_referencia , 
    funcionario.*, esp_desc, doc_nombres, doc_paterno, doc_materno,
    inst2.inst_nombre as inst_nombre2
    FROM nomina_detalle_referencia 
    LEFT JOIN instituciones inst1 on inst_id=nomdr_est_id 
    LEFT JOIN instituciones inst2 on inst2.inst_id=nomdr_est_id2 
    LEFT JOIN funcionario on func_id=nomdr_func_id 
    LEFT JOIN nomina_detalle as nd on nd.nomd_id=nomina_detalle_referencia.nomd_id 
    LEFT JOIN nomina on nomina.nom_id=nd.nom_id 
    LEFT JOIN doctores on doc_id=nomina.nom_doc_id 
    LEFT JOIN especialidades on esp_id=nomina.nom_esp_id 
    WHERE nomina_detalle_referencia.nomd_id=$nomd_id",true);
    $nomdr_id="";
    if($referencia) {
        $nomdr_id=($referencia['nomdr_id']*1);
        $nomdr_fecha_ingreso=$referencia['nomdr_fecha_ingreso'];
        $nomdr_fecha_alta=$referencia['nomdr_fecha_alta'];
        $nomdr_diagnostico1=$referencia['nomdr_diagnostico1'];
        $nomdr_diagnostico2=$referencia['nomdr_diagnostico2'];
        
        if(($referencia['nomdr_biopsia_nro']*1)==0)
            $nomdr_biopsia_nro="";
        else
            $nomdr_biopsia_nro=$referencia['nomdr_biopsia_nro'];
        
        $nomdr_est_id=$referencia['nomdr_est_id'];
        $nomdr_est_id2=$referencia['nomdr_est_id2'];
        $nomdr_inst_nombre=$referencia['inst_nombre'];
        $nomdr_inst_nombre2=$referencia['inst_nombre2'];
        $nomdr_fecha2=$referencia['nomdr_fecha2'];
        $nomdr_detalle=$referencia['nomdr_detalle'];
        $nomdr_tratamiento=$referencia['nomdr_tratamiento'];
        $nomdr_indicaciones=$referencia['nomdr_indicaciones'];
        $nomdr_control=$referencia['nomdr_control'];
        $nomdr_control_aps=$referencia['nomdr_control_aps'];
        $nomdr_pertinencia=$referencia['nomdr_pertinencia'];
        $nomdr_porque=$referencia['nomdr_porque'];
    }
    
    
    
?>
<script type="text/javascript" >
    bloquear_ref=false;
    
    cancelar_contrarreferencia = function() {
        window.close();
        $("popup_contrarreferencia").win_obj.close();
    }
    
    guardar_contrarreferencia= function() {
        if(bloquear_ref)
            return;
        
        if(!validacion_fecha($('ref_fecha1'))) {
            alert('Debe ingresar una fecha v&aacute;lida para el INGRESO A ESPECIALIDAD.'.unescapeHTML());
            return;
        }
        
        if(!validacion_fecha($('ref_fecha2'))) {
            alert('Debe ingresar una fecha v&aacute;lida para el ALTA DE ESPECIALIDAD.'.unescapeHTML());
            return;
        }
        
        
        if($('ref_diagnostico1').value=='') {
            alert(("Debe ingresar Diagnostico de Derivaci&oacute;n").unescapeHTML());
            return;
        }
        if($('ref_diagnostico2').value=='') {
            alert(("Debe ingresar Diagnostico de Especialidad").unescapeHTML());
            return;
        }
        
        
        if(!validacion_fecha($('ref_fecha3')) && $('ref_fecha3').value!='') {
            alert('Debe ingresar una fecha v&aacute;lida para de EXAMEN.'.unescapeHTML());
            return;
        }
        
        bloquear_ref=true;
        <?php if(!$llamada) {?>
            var ruta="sql_referencia.php";
        <?php } else {?>
            var ruta="prestaciones/ingreso_nominas/sql_referencia.php";
        <?php } ?>
        var myAjax=new Ajax.Request(ruta,
        {
            method:'post',
            async:false,
            parameters:$('datos_referencia').serialize(),
            onComplete: function(data)
            {
                reg_ref=data.responseText.evalJSON(true);
                //alert('Registro completado exitosamente.\nHOJA CONTRARREFERENCIA Nro:['+reg_ref+']');
                <?php if(!$llamada) {?>
                    $('nomdr_id').value=reg_ref*1;
                    alert('Registro completado exitosamente.\nHOJA CONTRARREFERENCIA Nro:['+reg_ref+']');
                    imprimir_contrarreferencia();
                    window.close();
                <?php } ?>
                $("popup_contrarreferencia").win_obj.close();
            }
        });
        bloquear_ref=false;
    }
    
    imprimir_contrarreferencia= function() {
        <?php if(!$llamada) {?>
                var ruta='referencia_detalle.php';
        <?php } else {?> 
            var ruta='prestaciones/ingreso_nominas/referencia_detalle.php';
        <?php } ?> 
        var nomdr_id=$('nomdr_id').value;
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_winreferencia_pdf = window.open(ruta+'?nomdr_id='+nomdr_id,
        'win_referencia_pdf', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_winreferencia_pdf.focus();
    }
    
    
    
    
</script>
<br/>
<div class="sub-content">
    <form id='datos_referencia' name='datos_referencia' onSubmit='return false;'>
        <input type="hidden" id="nomd_id" name="nomd_id" value="<?php echo $nomd_id;?>" />
        <input type="hidden" id="nomdr_id" name="nomdr_id" value="<?php echo $nomdr_id;?>" />
        <?php if($referencia) {?>
        <table style="font-size:12px;width: 100%;">
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">N&deg; Referencia:</td>
                <td class='tabla_fila'><b><?php echo $referencia['nomdr_id'] ; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Fecha Referencia:</td>
                <td class='tabla_fila'><b><?php echo $referencia['fecha_referencia'] ; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Registrado por:</td>
                <td class='tabla_fila'><b><?php echo $referencia['func_nombre'] ; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Especialidad:</td>
                <td class='tabla_fila'><b><?php echo $referencia['esp_desc'] ; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">M&eacute;dico Asignado:</td>
                <td class='tabla_fila'><b><?php echo $referencia['doc_nombres']." ".$referencia['doc_paterno']." ".$referencia['doc_materno'] ; ?></b></td>
            </tr>
            
        </table>
        <?php } ?>
        <table style="font-size:12px;width: 100%;">
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left"><i>Datos del Paciente:</i></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Paciente:</td>
                <td class='tabla_fila'><b><?php echo ($pac['pac_nombres'] ." ". $pac['pac_appat']." ". $pac['pac_apmat']); ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Edad:</td>
                <td class='tabla_fila'><b><?php echo "".$pac['edad_anios']." A&Ntilde;OS ".$pac['edad_meses']." Meses ".$pac['edad_dias']." d&iacute;as"; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">N&deg;: Ficha:</td>
                <td class='tabla_fila'><b><?php echo $pac['pac_ficha']; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Rut:</td>
                <td class='tabla_fila'><b><?php echo $pac['pac_rut']; ?></b></td>
            </tr>
        </table>
        <?php 
        if($reg_inter) {
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
            LEFT JOIN casos_auge ON casos_auge.id_sigges=id_caso
            WHERE inter_id=".$reg_inter['inter_id']."";
            //print($consulta);
            $datos2 = pg_query($consulta);
            $inter2 = pg_fetch_row($datos2);
            print('<table style="font-size:12px;width: 100%;">');
                print('<tr class="tabla_header">');
                    print('<td colspan="2" style="text-align:left"><i>Datos de Interconsulta de Origen:</i></td>');
                print('</tr>');
                print('<tr>');
                    print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Especialidad:</td>");
                    print("<td class='tabla_fila'><b>".$inter2[0]."</b></td>");
                print('</tr>');
                
                if($inter2[19]!=''){
                    print("<tr>");
                        print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Unidad Receptora:</td>");
                        print("<td class='tabla_fila'><b>".htmlentities($inter2[19])."</b></td>");
                    print("</tr>");
                }
                switch($inter2[9])
                {
                    case 0: $motivo='Confirmaci&oacute;n Diagn&oacute;stica'; break;	
                    case 1: $motivo='Realizar Tratamiento'; break;	
                    case 2: $motivo='Seguimiento'; break;	
                    case 3: $motivo='Control Especialidad'; break;	
                    case 4: $motivo='Otro Motivo...'; break;
                }
                print("<tr>");
                    print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Motivo Derivaci&oacute;n:</td>");
                    print("<td class='tabla_fila'><b>".$motivo."</b></td>");
                print("</tr>");
                print("<tr>");
                    print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Diagn&oacute;stico (Pres.):</td>");
                    print("<td class='tabla_fila' style='font-size: 12px;'><b>".$inter2[10]."</b><br>".htmlentities($inter2[11])."</td>");
                print("</tr>");
                print("<tr>");
                    print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Patolog&iacute;a GES:</td>");
                    $dic=cargar_registro("SELECT * FROM interconsulta WHERE inter_id=".$reg_inter['inter_id']."");
                    $caso=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=".$dic['id_caso']);
                    if($inter2[25]!=''){
                        print("<td class='tabla_fila'><b>".htmlentities($inter2[25])."</b></td>");
                    } else {
                        if(($inter2[26]*1)!=0) {
                            $reg_pat=cargar_registro("SELECT * FROM patologias_auge WHERE pat_id=".($inter2[26]*1)."");
                            if($reg_pat) {
                                print("<td class='tabla_fila'><b>".htmlentities($reg_pat['pat_glosa'])."</b></td>");
                            } else {
                                print("<td class='tabla_fila'><b>Error al Encontrar Patologia.</b></td>");
                            }
                        } else {
                            print("<td class='tabla_fila'><b>No hay sospecha.</b></td>");
                        }
                    }
                print("</tr>");
                print("<tr>");
                    print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Fundamentos Cl&iacute;nicos Origen:</td>");
                    print("<td class='tabla_fila' style='font-size: 12px;'><b>".htmlentities($inter2[1])."</b></td>");
                print("</tr>");
                /*
                if(trim($inter2[2])!=""){
                    print("<tr>");
                        print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Ex&aacute;menes Comp.:</td>");
                        print("<td class='tabla_fila' style='font-size: 12px;'><b>".htmlentities($inter2[2])."</b></td>");
                    print("</tr>");
                }
                if(trim($inter2[3])!="") {
                    print("<tr>");
                        print("<td style='text-align:left;width:150px;white-space:nowrap;' valign='top' class='tabla_fila2'>Comentarios:</td>");
                        print("<td class='tabla_fila' style='font-size: 12px;'><b>".htmlentities($inter2[3])."</b></td>");
                    print("</tr>");
                }
                 * 
                 */
            print('</table>');
            
        }
        ?>
        <div class="sub-content">
        <table style="font-size:12px;width: 100%;">
            <tr class="tabla_header">
                <td colspan="2" style="text-align:center"><b>Datos Generales de Contrarreferencia:</b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Establecimiento Derivador</td>
                <td class='tabla_fila'>
                    <input type="hidden" id="est_id2" name="est_id2" OnClick=""  value="<?php echo $nomdr_est_id2;?>" size="45"/>
                    <input type="text" id="ref_esta2" name="ref_esta2" OnClick="" ondblclick='$("est_id2").value=""; $("ref_esta2").value="";' value="<?php echo $nomdr_inst_nombre2;?>" size="45"/>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Fecha Ingreso Especialidad:</td>
                <td class='tabla_fila'>
                    <input type="text" id="ref_fecha1" name="ref_fecha1" OnClick="" value="<?php echo $nomdr_fecha_ingreso;?>" size="10"/>
                    <?php if(!$llamada) {?>
                        <img src='../../iconos/date_magnify.png' id='ref_fecha1_boton'>
                    <?php } else {?>
                        <img src='iconos/date_magnify.png' id='ref_fecha1_boton'>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Fecha Alta Especialidad:</td>
                <td class='tabla_fila'>
                    <input type="text" id="ref_fecha2" name="ref_fecha2" OnClick="" value="<?php echo $nomdr_fecha_alta;?>" size="10"/>
                    <?php if(!$llamada) {?>
                        <img src='../../iconos/date_magnify.png' id='ref_fecha2_boton'>
                    <?php } else {?>
                        <img src='iconos/date_magnify.png' id='ref_fecha2_boton'>
                    <?php } ?>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">Diagnostico Derivaci&oacute;n:</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="ref_diagnostico1" name="ref_diagnostico1" style="width:98%;height:45px;"><?php echo $nomdr_diagnostico1;?></textarea>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">Diagnostico Especialidad:</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="ref_diagnostico2" name="ref_diagnostico2" style="width:98%;height:45px;"><?php echo $nomdr_diagnostico2;?></textarea>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">EX&Aacute;MENES DE APOYO <span class="texto_tooltip" title="(Anamnesis, examen fisico, estudio de LAB e imagenologia y otros)" alt="(Anamnesis, examen fisico, estudio de LAB e imagenologia y otros)"><i>(?)</i></span></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Biopsia N&deg;:</td>
                <td class='tabla_fila'><input type="text" id="ref_nro_biopsia" name="ref_nro_biopsia" OnClick="" value="<?php echo $nomdr_biopsia_nro;?>" size="45"/></td>
            </tr>
            <tr style="display: none;">
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Establecimiento:</td>
                <td class='tabla_fila'>
                    <input type="hidden" id="est_id" name="est_id" OnClick=""  value="<?php echo $nomdr_est_id;?>" size="45"/>
                    <input type="text" id="ref_esta" name="ref_esta" OnClick="" ondblclick='$("est_id").value=""; $("ref_esta").value="";' value="<?php echo $nomdr_inst_nombre;?>" size="45"/>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Fecha de Muestra:</td>
                <td class='tabla_fila'>
                    <input type="text" id="ref_fecha3" name="ref_fecha3" OnClick="" value="<?php echo $nomdr_fecha2;?>" size="10"/>
                    <?php if(!$llamada) {?>
                        <img src='../../iconos/date_magnify.png' id='ref_fecha3_boton'>
                    <?php } else {?>
                        <img src='iconos/date_magnify.png' id='ref_fecha3_boton'>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Detalle:</td>
                <td>
                    <textarea id="ref_detalle" name="ref_detalle" style="width:98%;height:45px;"><?php echo $nomdr_detalle;?></textarea>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">TRATAMIENTO: M&eacute;dico o Quir&uacute;rgico <span class="texto_tooltip" title="(se&ntilde;alar tipo y fecha de intervenci&oacute;n)" alt="(se&ntilde;alar tipo y fecha de intervenci&oacute;n)"><i>(?)</i></span></td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="ref_tratamiento" name="ref_tratamiento" style="width:98%;height:45px;"><?php echo $nomdr_tratamiento;?></textarea>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">INDICACIONES: </td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="ref_indicaciones" name="ref_indicaciones" style="width:98%;height:45px;"><?php echo $nomdr_indicaciones;?></textarea>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Se sugiere control por especialistas:</td>
                <td class='tabla_fila'>
                    <select id="ref_control_esp" name="ref_control_esp">
                        <option value="0" <?php if(($nomdr_control=='0') or ($nomdr_control=='')) echo 'SELECTED'; ?>>Seleccionar..</option>
                        <option value="1" <?php if($nomdr_control=='1') echo 'SELECTED'; ?>>Sin Control</option>
                        <option value="2" <?php if($nomdr_control=='2') echo 'SELECTED'; ?>>6 Meses</option>
                        <option value="3" <?php if($nomdr_control=='3') echo 'SELECTED'; ?>>1 A&ntilde;o</option>
                    </select>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">(Derivar con nueva interconsulta desde APS)</td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">CONTROLAR APS:</td>
                <td class='tabla_fila'>
                    <select id="ref_control_aps" name="ref_control_aps">
                        <option value="0" <?php if(($nomdr_control_aps=='0') or ($nomdr_control_aps=='')) echo 'SELECTED'; ?>>Seleccionar..</option>
                        <option value="1" <?php if($nomdr_control_aps=='1') echo 'SELECTED'; ?>>1 Mes</option>
                        <option value="2" <?php if($nomdr_control_aps=='2') echo 'SELECTED'; ?>>3 Meses</option>
                        <option value="3" <?php if($nomdr_control_aps=='3') echo 'SELECTED'; ?>>6 Meses</option>
                        <option value="4" <?php if($nomdr_control_aps=='4') echo 'SELECTED'; ?>>1 A&ntilde;o</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Pertinencia derivaci&oacute;n:</td>
                <td>
                    <select id="ref_pertinecia" name="ref_pertinecia">
                        <option value="0" <?php if(($nomdr_pertinencia=='0') or ($nomdr_pertinencia=='')) echo 'SELECTED'; ?>>Seleccionar..</option>
                        <option value="1" <?php if($nomdr_pertinencia=='1') echo 'SELECTED'; ?>>SI</option>
                        <option value="2" <?php if($nomdr_pertinencia=='2') echo 'SELECTED'; ?>>NO</option>
                    </select>
                </td>
            </tr>
            <tr class="tabla_header">
                <td colspan="2" style="text-align:left">(si la derivaci&oacute;n no fu&eacute; pertinente, se&ntilde;alar &iquest;por que?)</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="ref_motivo" name="ref_motivo" style="width:98%;height:45px;"><?php echo $nomdr_porque;?></textarea>
                </td>
            </tr>
        </table>
        </div>
    </form>
</div>
<div>
    <center>
        <table>
            <tr>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <center>
                            <table>
                                <tr>
                                    <td>
                                        <?php if(!$llamada) {?>
                                            <img src='../../iconos/page_white_swoosh.png'>
                                        <?php } else {?>
                                            <img src='iconos/page_white_swoosh.png'>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href='#' onClick='guardar_contrarreferencia();'>Aceptar</a>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </div>
                </td>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <table>
                            <tr>
                                <td>
                                    <?php if(!$llamada) {?>
                                        <img src='../../iconos/cancel.png'>
                                    <?php } else {?>
                                        <img src='iconos/cancel.png'>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href='#' onClick='cancelar_contrarreferencia();'>Cancelar</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <?php 
                if($llamada) {
                    if($referencia) {
                ?>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <table>
                            <tr>
                                <td>
                                    <img src='iconos/printer.png'>
                                </td>
                                <td>
                                    <a href='#' onClick='imprimir_contrarreferencia();'>Imprimir</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <?php 
                    } 
                }
                ?>
            </tr>
        </table>
    </center>
</div>
<script>
    Calendar.setup({
        inputField     :    'ref_fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'ref_fecha1_boton'
    });
    
    Calendar.setup({
        inputField     :    'ref_fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'ref_fecha2_boton'
    });
    
    Calendar.setup({
        inputField     :    'ref_fecha3',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'ref_fecha3_boton'
    });
    
    seleccionar_inst = function(d)
    {
        $('est_id').value=d[0];
        $('ref_esta').value=d[2].unescapeHTML();
    }
    
    autocompletar_institucion = new AutoComplete(
    'ref_esta', 
    '../../autocompletar_sql.php',
    function() {
    if($('ref_esta').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('ref_esta').value)
    }
    }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst);
    
    
    seleccionar_inst = function(d)
    {
        $('est_id2').value=d[0];
        $('ref_esta2').value=d[2].unescapeHTML();
    }
    
    autocompletar_institucion = new AutoComplete(
    'ref_esta2', 
    '../../autocompletar_sql.php',
    function() {
    if($('ref_esta2').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('ref_esta2').value)
    }
    }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst);
    
</script>