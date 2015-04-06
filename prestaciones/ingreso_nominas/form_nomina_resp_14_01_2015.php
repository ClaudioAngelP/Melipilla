<?php 
    require_once('../../conectar_db.php');
    $tipo_atencion=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo");
    $tipoatencionhtml='';
    for($i=0;$i<count($tipo_atencion);$i++)
    {
        $tipoatencionhtml.='<option value="'.$tipo_atencion[$i]['nom_motivo'].'">'.$tipo_atencion[$i]['nom_motivo'].'</option>';
    }
    
    $tipo_contrato=cargar_registros_obj("SELECT DISTINCT nom_tipo_contrato from nomina where nom_tipo_contrato is not null order by nom_tipo_contrato");
    $tipocontratohtml='';
    for($i=0;$i<count($tipo_contrato);$i++)
    {
        $tipocontratohtml.='<option value="'.$tipo_contrato[$i]['nom_tipo_contrato'].'">'.$tipo_contrato[$i]['nom_tipo_contrato'].'</option>';
    }
    
    
    
?>
<html>
    <title>Crear N&oacute;mina de Atenci&oacute;n</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        var proc=false;
        function guardar_nomina()
        {
            if($('esp_id').value*1==0)
            {
                alert('ERROR: Debe seleccionar una especialidad.');
		return;
            }
	
            //if(!$('proc').checked && $('doc_id').value*1==0)
            if($('doc_id').value*1==0)
            {
                alert('ERROR: Debe seleccionar un profesional tratante.');
		return;
            }
            
            
            if($('select_nom_motivo').value*1==-1)
            {
                var conf=confirm('&iquest;Esta Seguro de Crear la nomina sin tipo de Atenci&oacute;n?'.unescapeHTML());
                if(!conf)
                    return;	
            }
            
            if($('select_nom_contrato').value*1==-1)
            {
                var conf=confirm('&iquest;Esta Seguro de Crear la nomina sin tipo de Contrato?'.unescapeHTML());
                if(!conf)
                    return;	
            }
            
	
            var conf=confirm('&iquest;Desea generar una nueva n&oacute;mina?'.unescapeHTML());
	
            if(!conf)
                return;
	
            var myAjax=new Ajax.Request('sql_nomina.php',
            {
                method:'post',
                parameters:$('fecha').serialize()+'&'+$('esp_id').serialize()+'&'+$('doc_id').serialize()+'&'+$('select_nom_motivo').serialize()+'&'+$('select_nom_contrato').serialize(),
                onComplete:function(r)
                {
                    var folio=r.responseText.evalJSON(true);
                    if(folio=="X")
                    {
                        alert("La nomina que pretende crear ya EXISTE");
                        return;
                    }
                    var fn=window.opener.abrir_nomina.bind(window.opener);
                    fn(folio, 1);
                    window.close();
		}	
            });	
	}
    </script>
    <body class='fuente_por_defecto cabecera_popup'>
        <input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
        <div class='sub-content'>
            <img src='../../iconos/disk_multiple.png' />
            <b>Crear N&oacute;mina de Atenci&oacute;n</b>
        </div>
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr>
                    <td style='text-align:right;width:150px;'>Fecha:</td>
                    <td>
                        <input type='text' id='fecha'  name='fecha' style='text-align:center;' value='<?php echo date("d/m/Y"); ?>' size=10>
                        <img src='../../iconos/date_magnify.png' id='fecha_boton'>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;'>Especialidad:</td>
                    <td>
                        <input type='hidden' id='esp_id' name='esp_id' value=''>
                        <input type='text' id='especialidad' value='' name='especialidad' size=35>
                    </td>
                </tr>
                <tr id='rut_tr'>
                    <td style='text-align:right;'>R.U.T. Profesional:</td>
                    <td>
                        <input type='text' id='rut_medico' name='rut_medico' size=10 style='text-align: center;' value='' disabled>
                    </td>
                </tr>
                <tr id='nom_tr'>
                    <td style='text-align:right;'>Profesional Tratante:</td>
                    <td>
                        <input type='hidden' id='doc_id' name='doc_id' value=''>
                        <input type='text' id='nombre_medico' value='' name='nombre_medico' size=35>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;'>Tipo de Atenci&oacute;n</td>
                    <td>
                        <select id='select_nom_motivo' name='select_nom_motivo'>
                            <?php echo $tipoatencionhtml;?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;'>Tipo de Contrato</td>
                    <td>
                        <select id='select_nom_contrato' name='select_nom_contrato'>
                            <option value="-1" SELECTED>Sin Asignar Tipo Contrato</option>
                            <?php echo $tipocontratohtml;?>
                        </select>
                    </td>
                </tr>
                <!--
                <tr>
                    <td></td>
                    <td>
                        <input type='checkbox' id='proc' name='proc' onClick='fix_fields();' />
                        <b>N&oacute;mina de Procedimientos/Ex&aacute;menes.</b>
                    </td>
                </tr>
                -->
            </table>
            <center>
                <br /><br />
                <input type='button' id='copiar' name='copiar' onClick='guardar_nomina();' value='-- Crear Nueva N&oacute;mina... --' />
            </center>
        </div>
    </body>
</html>
<script>
    fix_fields=function()
    {
        if($('proc').checked)
        {
            $('doc_id').value='';
            $('rut_medico').value='';
            $('nombre_medico').value='';
            $('nombre_medico').disabled=true;
	}
        else
        {
            $('nombre_medico').disabled=false;
	}				
    }
    
    ingreso_especialidades=function(datos_esp)
    {
        $('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2].unescapeHTML();
    }

    autocompletar_especialidades = new AutoComplete(
    'especialidad', 
    '../../autocompletar_sql.php',
    function() {
    if($('especialidad').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=subespecialidad_nominas&cadena='+encodeURIComponent($('especialidad').value)
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

    
    ingreso_rut=function(datos_medico)
    {
        $('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
        $('nombre_medico').value=(datos_medico[0].trim()).unescapeHTML();
    }

    autocompletar_medicos = new AutoComplete(
    'nombre_medico', 
    '../../autocompletar_sql.php',
    function() {
    if($('nombre_medico').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=medicos&'+$('nombre_medico').serialize()+'&receta=false'
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);
    $('especialidad').focus();

    Calendar.setup({
    inputField     :    'fecha',         // id of the input field
    ifFormat       :    '%d/%m/%Y',       // format of the input field
    showsTime      :    false,
    button          :   'fecha_boton'
    });
</script>