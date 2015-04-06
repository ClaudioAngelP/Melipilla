<?php 
    require_once('../../conectar_db.php');
    $nom_id=$_GET['nom_id'];
    error_reporting(E_ALL);
    $n=cargar_registro("SELECT *, nom_fecha::date,
    (SELECT COUNT(*) FROM nomina_detalle WHERE nomina_detalle.nom_id=nomina.nom_id AND nomd_diag_cod not in ('T')) AS cantidad 
    FROM nomina
    LEFT JOIN doctores ON nom_doc_id=doc_id
    LEFT JOIN especialidades ON nom_esp_id=esp_id
    WHERE nom_id=$nom_id", true);
    
    //$proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$n['nom_esp_id']);
    
    
    $tipo_contrato=cargar_registros_obj("SELECT DISTINCT nom_tipo_contrato from nomina where nom_tipo_contrato is not null order by nom_tipo_contrato", true);
    $tipocontratohtml='';
    for($i=0;$i<count($tipo_contrato);$i++)
    {
        if($tipo_contrato[$i]['nom_tipo_contrato']==$n['nom_tipo_contrato'])
            $selected="Selected";
        else
            $selected="";
        
        $tipocontratohtml.='<option value="'.$tipo_contrato[$i]['nom_tipo_contrato'].'" '.$selected.'>'.$tipo_contrato[$i]['nom_tipo_contrato'].'</option>';
    }
?>
<html>
<title>Copiar N&oacute;mina de Atenci&oacute;n</title>
<?php cabecera_popup('../..'); ?>
<script>
    function guardar_copia() {
        if(!$('proc').checked && $('doc_id').value*1==0) {
            alert('ERROR: Debe seleccionar un profesional tratante.');
            return;
	}
		
	var conf=confirm('&iquest;Desea realizar la copia de esta n&oacute;mina?'.unescapeHTML());
	
	if(!conf) return;	
	
	var myAjax=new Ajax.Request('sql_copiar.php',
        {
            method:'post',
            parameters:$('nom_id').serialize()+'&'+$('doc_id').serialize()+'&'+$('fecha1').serialize()+'&'+$('datos').serialize()+'&'+$('select_nom_contrato').serialize(),
            onComplete:function() {
                alert('Copia de N&oacute;mina realizada exitosamente.'.unescapeHTML());
                return;
                window.close();
                window.opener.listar_nominas(0).bind(window.opener);
            }	
        });	
    }
</script>
<body class='fuente_por_defecto cabecera_popup'>
    <input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />
    <div class='sub-content'>
        <img src='../../iconos/disk_multiple.png' /> 
        <b>Copiar N&oacute;mina de Atenci&oacute;n</b>
    </div>
    <div class='sub-content'>
        <table style='width:100%;'>
            <tr>
                <td style='text-align:right;width:30%;'>Nro. de Folio:</td>
                <td style='font-weight:bold;font-size:16px;'>
                    <?php echo $n['nom_folio']; ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Profesional:</td>
                <td>
                    <?php echo $n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']; ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Especialidad:</td>
                <td style='font-weight:bold;font-size:16px;'>
                    <?php echo $n['esp_desc']; ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Tipo de Consulta:</td>
                <td style='font-weight:bold;font-size:16px;'>
                    <?php echo $n['nom_motivo']; ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Tipo de Contrato:</td>
                <td style='font-weight:bold;font-size:16px;'>
                    <?php echo $n['nom_tipo_contrato']; ?>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Nro. de Registros:</td>
                <td style='font-weight:bold;font-size:16px;'>
                    <?php echo $n['cantidad']; ?>
                </td>
            </tr>
        </table>
    </div>
    <div class='sub-content'>
        <table style='width:100%;'>
            <tr>
                <td style='width:150px;text-align:right;'>Fecha:</td>
                <td>
                    <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo $n['nom_fecha'] ?>' />
                    <img src='../../iconos/date_magnify.png' id='fecha1_boton'>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>R.U.T. Profesional:</td>
                <td>
                    <input type='text' id='rut_medico' name='rut_medico' size=10 style='text-align: center;' value='' disabled>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Profesional Tratante:</td>
                <td>
                    <input type='hidden' id='doc_id' name='doc_id' value=''>
                    <input type='text' id='nombre_medico' value='' name='nombre_medico' size=35>
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
            <tr>
                <td></td>
                <td>
                    <input type='checkbox' id='proc' name='proc' onClick='fix_fields();' />
                    <b>N&oacute;mina de Procedimientos/Ex&aacute;menes.</b>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type='checkbox' id='datos' name='datos' onClick='' />
                    <b>Copiar datos del registro.</b>
                </td>
            </tr>
        </table>
        <center>
            <input type='button' id='copiar' name='copiar' onClick='guardar_copia();' value='-- Realizar Copia de la N&oacute;mina... --' />
        </center>
    </div>
</body>
</html>

<script>
    
    Calendar.setup({
    inputField     :    'fecha1',         // id of the input field
    ifFormat       :    '%d/%m/%Y',       // format of the input field
    showsTime      :    false,
    button          :   'fecha1_boton'
    });

    fix_fields=function() {
        if($('proc').checked) {
            $('doc_id').value='';
            $('rut_medico').value='';
            $('nombre_medico').value='';
            $('nombre_medico').disabled=true;
        } else {
            $('nombre_medico').disabled=false;
        }				
    }

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
      
    $('nombre_medico').focus();
</script>