<?php
    require_once('../../conectar_db.php');
    $ramas = cargar_registros_obj("SELECT * FROM patologias_auge_ramas ORDER BY rama_nombre");
    $esp_id2=($_POST['esp_id']*1);
    $nomd_id2=($_POST['nomd_id']*1);
    $pac_id2=($_POST['pac_id']*1);
    
    $reg_esp=cargar_registro("SELECT * FROM especialidades where esp_id=$esp_id2");
    if($reg_esp)
    {
        $esp_nombre_orig=$reg_esp['esp_desc'];
        $esp_id_orig=$reg_esp['esp_id'];
    }
    else
    {
        $esp_nombre_orig="";
        $esp_id_orig="";
    }
    
    $reg_doc=cargar_registro("select * from doctores where doc_id=(
    select nom_doc_id from nomina_detalle join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=$nomd_id2
    )");
    if($reg_doc)
    {
        $doc_id_orig=$reg_doc['doc_id'];
        $doc_rut_orig=$reg_doc['doc_rut'];
        $doc_nom_orig=$reg_doc['doc_paterno']." ".$reg_doc['doc_materno']." ".$reg_doc['doc_nombres'];
    }
    else
    {
        $doc_id_orig="";
        $doc_rut_orig="";
        $doc_nom_orig="";
    }
    
    
    /*
    $c=cargar_registros_obj("
    SELECT *, to_char(nom_fecha, 'D') AS dow  FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    JOIN pacientes USING (pac_id)
    LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
    WHERE nomd_diag_cod = 'X' AND (nomd_estado IS NULL OR nomd_estado!='1')
    ORDER BY nom_fecha, nomd_hora
    ", true);
    */
    
    
    //$espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_recurso=true ORDER BY esp_desc", NULL, '', '');
?>
<script>
    
    
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
    
    
    verifica_tabla_inter = function()
    {
        if($('esp_id_inter').value=='')
        {
            alert('Debe seleccionar Especialidad.'.unescapeHTML());
            return;
	}
        if(trim($('inter_funda').value)=='')
        {
            alert('Fundamento Cl&iacute;nico de la Interconsulta est&aacute; vac&iacute;o.'.unescapeHTML());
            return;
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        
        var params='paciente_id='+<?php echo $pac_id2;?>+'&esp_id='+$('esp_id_inter').value;
        params+='&'+$('inter_funda').serialize()+'&'+$('inter_comenta').serialize()+'&'+$('diag_cod').serialize()+'&'+$('motivo').serialize();
        params+='&'+$('pat_id').serialize()+'&'+$('patrama_id').serialize()+'&prof_id='+$('doc_id').value+'&inter=ficha_clinica';
        params+='&priori_inter='+$('priori_inter').value+'&esp_orig='+$('esp_id2_oa').value+'&nomd_id_origen='+<?php echo $nomd_id2;?>;
        
        
        
        var myAjax = new Ajax.Request('../../interconsultas/ingreso_inter/sql.php', 
        {
            method: 'post', 
            parameters: params,
            onComplete: function (pedido_datos)
            {
                var resp=pedido_datos.responseText.split("|");
                if(resp[0]=='OK')
                {
                    alert('Interconsulta ingresada exitosamente.');
                    print_inter_ficha(resp[1]*1);
                    $("form_derivacion").win_obj.close();
                    //cambiar_pagina('interconsultas/ingreso_inter/form.php');
                }
                else
                {
                    alert('ERROR:\\n'+pedido_datos.responseText.unescapeHTML());
                }
            }
        });
    }
    
    print_inter_ficha=function(id)
    {
        inter_ficha_pdf = window.open('../../interconsultas/inter_ficha_pdf.php?tipo=inter_ficha&inter_id='+id,
        'inter_ficha_pdf', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
	inter_ficha_pdf.focus();
    }
</script>
<html>
    <center>
        <form id='form_derivacion'>
            <div class='sub-content'>
                <table style='width:100%;'>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>
                            Tipo:
                        </td>
                        <td class='tabla_fila'>
                            <select id='tipo_oa' name='tipo_oa' onChange=''>
                                <option value='1'>Local</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>
                            Fecha Solicitud:
                        </td>
                        <td class='tabla_fila'>
                            <input type='text' id='fecha_oa' name='fecha_oa' style='text-align:center;' value='<?php echo date('d/m/Y'); ?>' size=15 onKeyUp='' disabled=""/>
                        </td>
                    </tr>
                    
                    <tr id='medi_tr'>
                        <td style='text-align:right;' class='tabla_fila2'>
                            M&eacute;dico Solicitante:
                        </td>
                        <td class='tabla_fila'>
                            <input type='hidden' id='doc_id' name='doc_id' value='<?php echo $doc_id_orig;?>' />
                            <input type='text' id='rut_medico' name='rut_medico' value='<?php echo $doc_rut_orig;?>' size=15 style='text-align:center;' DISABLED />
                            <input type='text' id='nombre_medico' name='nombre_medico' value='<?php echo $doc_nom_orig;?>' size=35 onDblClick='$("doc_id").value=""; $("rut_medico").value=""; $("nombre_medico").value="";' />
                        </td>
                    </tr>
                    <tr id='espe_tr'>
                        <td style='text-align:right;' class='tabla_fila2'>
                            Especialidad Solicitante:
                        </td>
                        <td class='tabla_fila'>
                            <input type='hidden' id='esp_id2_oa' name='esp_id2_oa' value='<?php echo $esp_id_orig;?>' />
                            <input type='text' id='esp_desc2_oa' name='esp_desc2_oa' value='<?php echo $esp_nombre_orig;?>' size=35 disabled="" />
                        </td>
                    </tr>
                </table>
                <div class='sub-content'>
                    <div class='sub-content'><img src='../../iconos/chart_organisation.png'> <b>Datos de Interconsulta</b></div>
                    <table>
                        <tr>
                            <td style='text-align: right;'>Especialidad Cl&iacute;nica:</td>
                            <td>
                                <input type='hidden' id='esp_id_inter' name='esp_id_inter' value=''>
                                <input type='text' id='esp_desc' name='esp_desc' value='' size=40>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:right;'>Se env&iacute;a consulta para:</td>
                            <td>
                                <select id='motivo' name='motivo'>
                                    <option value=0>Confirmaci&oacute;n Diagn&oacute;stica</option>
                                    <option value=1>Realizar Tratamiento</option>
                                    <option value=2>Seguimiento</option>
                                    <option value=3>Control Especialidad</option>
                                    <option value=4>Otro Motivo</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>C&oacute;digo Diag. Presuntivo:</td>
                            <td>
                                <input type='text' id='diag_cod' name='diag_cod' style='text-align:center;' size=10>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Diagn&oacute;stico Presuntivo:</td>
                            <td width=70% style='text-align:left;'>
                                <span id='diagnostico' style='font-weight: bold;'>
                                (No Asociado...)
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td valign='top' style='text-align: right;'>&iquest;Sospecha problema AUGE?</td>
                            <td>
                                <input type='checkbox' id='' name='' onClick='
                                if(!this.checked)
                                {
                                    $("pat_desc").disabled=true; $("pat_id").value="G1"; 
                                    $("pat_desc").value=""; actualizar_ramas();
                                }
                                else
                                {
                                    $("pat_desc").disabled=false; $("pat_id").value=""; $("pat_desc").focus();
                                }
                                ' CHECKED> Sospecha Patolog&iacute;a AUGE
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <input type='hidden' id='pat_id' name='pat_id' value=''>
                                <input type='text' id='pat_desc' name='pat_desc' value='' size=60>
                            </td>
                        </tr>
                        <tr>
                            <td valign='top' style='text-align: right;'>Subgrupo o subproblema AUGE:</td>
                            <td>
                                <div id='patrama'>
                                    <select id='patrama_id' name='patrama_id'>
                                        <option value=-1>(Seleccione Patolog&iacute;a...)</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td valign='top' style='text-align: right;'>Fundamentos Cl&iacute;nicos:</td>
                            <td><textarea cols=50 rows=6 id='inter_funda' name='inter_funda'></textarea></td>
                        </tr>
                        <!--
                        <tr>
                            <td valign='top' style='text-align: right;'>Ex&aacute;menes Complementarios:</td>
                            <td><textarea cols=50 rows=6 id='inter_examen' name='inter_examen'></textarea></td>
                        </tr>
                        -->
                        <tr>
                            <td valign='top' style='text-align: right;'>Comentarios:</td>
                            <td><textarea cols=50 rows=6 id='inter_comenta' name='inter_comenta'></textarea></td>
                        </tr>
                        <tr>
                            <td valign="top" class="form_field_name" style="text-align:right;width:30%;white-space:nowrap;">PRIORIDAD :</td>
                            <td class="form_field">
                                <select style="" name="priori_inter" id="priori_inter">
                                    <option selected="" value="0">S/P</option>
                                    <option value="1">BAJA</option>
                                    <option value="2">MEDIA</option>
                                    <option value="3">ALTA</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class='sub-content'>
            <center>
                <table>
                    <tr>
                        <td>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='../../iconos/accept.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='verifica_tabla_inter();'>Ingresar Interconsulta...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <!--
                        <td>
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='../../iconos/delete.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='cambiar_pagina("form_.php");'>Limpiar Formulario...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        -->
                    </tr>
                </table>
            </center>
        </div>
        </div>
    
        </form>
    </center>
</html>
<script>
    
    seleccionar_especialidad = function(d)
    {
        $('esp_id_inter').value=d[0];
        $('esp_desc').value=d[2].unescapeHTML();
    }
    
    
    seleccionar_patologia = function(d)
    {
        $('pat_id').value=d[0];
        $('pat_desc').value=d[2].unescapeHTML();
        actualizar_ramas();
    }

    seleccionar_diagnostico = function(d)
    {
        $('diag_cod').value=d[0];
        $('diagnostico').innerHTML='['+d[0]+'] '+d[2];
    }

    
    
    autocompletar_especialidades = new AutoComplete(
    'esp_desc', 
    '../../autocompletar_sql.php',
    function(){
    if($('esp_desc').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=especialidad&'+$('esp_desc').serialize()
    }
    }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad);
    
    
    
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
    
    autocompletar_diagnostico = new AutoComplete(
    'diag_cod', 
    '../../autocompletar_sql.php',
    function() {
    if($('diag_cod').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diag_cod').value)
    }
    }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico);
    
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
</script>