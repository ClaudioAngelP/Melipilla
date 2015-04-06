<?php 
    require_once('../../conectar_db.php');
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

    $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where (esp_codigo_ifl_usuario = '' or esp_codigo_ifl_usuario is null) ORDER BY esp_desc", NULL, '', '');
?>
<script>
    buscar_citacion=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win =
        window.open('prestaciones/ingreso_nominas/buscar_citacion.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    gestiones_citacion=function(nomd_id)
    {
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win =
        window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?nomd_id='+nomd_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }

    listar_reasignar = function()
    {
        var myAjax = new Ajax.Updater('lista_citaciones','prestaciones/ingreso_nominas/buscar_citaciones_reasignar.php',
        {
            method:'post',
            evalScripts:true,
            parameters:$('form_reasignar').serialize()
        });
    }
</script>
<html>
    <center>
        <form id='form_reasignar'>
            <div class='sub-content' style='width:950px;'>
                <div class='sub-content'>
                    <img src='iconos/arrow_refresh.png' />
                    <b>Listado Cupos a Reasignar</b>
                </div>
                <div class='sub-content'>
                <table style='width:100%;font-size:10px;'>
                    <tr>
                        <td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
                        <td class='tabla_fila'>
                            <input type='hidden' id='pac_id' name='pac_id' value='0' />
                            <input type='text' size=10 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' style='font-size:16px;' />
                        </td>
                        <td class='tabla_fila'>
                            <input type='text' id='paciente' name='paciente'  onDblClick='limpiar_paciente();' style='text-align:left;font-size:16px;' DISABLED size=40 />
                        </td>
                    </tr>
                    <tr>
                        <td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
                        <td class='tabla_fila'>
                            <input type='hidden' id='doc_id' name='doc_id' value='0' />
                            <input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;'  />
                        </td>
                        <td class='tabla_fila'>
                            <input type='text' id='profesional' name='profesional'  onDblClick='limpiar_profesional();' style='text-align:left;font-size:16px;' DISABLED size=40 />
                        </td>
                    </tr>
                    <tr>
                        <td class='tabla_fila2' style='text-align:right;'>Especialidad</td>
                        <td class='tabla_fila' id='select_especialidades'>
                            <select id='esp_id' name='esp_id' onChange='listar_reasignar();'>
                                <option value=-1 SELECTED>(Todas las Especialidades...)</option>
                                <?php echo $espechtml; ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan='3'>
                            <center>
                                <input type='button' id='lista_reasignar' onClick='listar_reasignar();' value='Actualizar Listado...'>
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content2' style='height:350px;overflow:auto;' id='lista_citaciones' >
                <table style='width:100%;font-size:10px;'>
                    <tr class='tabla_header'> 
                        <td>&nbsp;</td>
                        <td>Fecha</td>
                        <td>Especialidad</td>
                        <td>Profesional</td>
                        <td>R.U.N.</td>
                        <td>Ficha</td>
                        <td>Paciente</td>
                        <td>Motivo</td>
                        <td>Gestionar</td>
                    </tr>
                    <?php 
                    if($c)
                        for($i=0;$i<sizeof($c);$i++)
                        {
                            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
                            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
                            <td style='text-align:right;font-size:18px;'><i>".($i+1)."</td>
                            <td style='text-align:center;font-size:14px;'>".substr($c[$i]['nom_fecha'],0,10)."<br />".substr($c[$i]['nomd_hora'],0,5)."</td>
                            ");
                            print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
                            print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
                            print("<td style='text-align:right;'>".$c[$i]['pac_rut']."</td>");
                            print("<td style='text-align:center;'>".$c[$i]['pac_ficha']."</td>");
                            print("<td style='text-align:left;'>".$c[$i]['pac_nombres']." ".$c[$i]['pac_appat']." ".$c[$i]['pac_apmat']."</td>");
                            print("<td style='text-align:left;'>".$c[$i]['cancela_desc']."</td>");	
                            print("<td><center>
                            <img src='iconos/date_magnify.png'  style='cursor:pointer;' alt='Citaciones Similares' title='Citaciones Similares' onClick='buscar_citacion(".$c[$i]['nomd_id'].");' />
                            <img src='iconos/phone.png'  style='cursor:pointer;' alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion(".$c[$i]['nomd_id'].");' />
                            </center>
                            </td>");
                            print("</tr>");
                        }
                    ?>
                    </table>
                </div>
            </div>
        </form>
    </center>
</html>
<script>
    seleccionar_profesional = function(d)
    {
        $('doc_rut').value=d[1];
        $('profesional').value=d[2].unescapeHTML();
        $('doc_id').value=d[0];
    }

    limpiar_profesional = function(d)
    {
        $('doc_rut').value='';
	$('profesional').value='';
	$('doc_id').value=0;
    }

    autocompletar_profesionales = new AutoComplete('doc_rut','autocompletar_sql.php',
    function() {
    if($('doc_rut').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
    }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);

    seleccionar_paciente = function(d)
    {
        $('pac_rut').value=d[0];
	$('paciente').value=d[2].unescapeHTML();
	$('pac_id').value=d[4];
    }

    limpiar_paciente = function(d)
    {
        $('pac_rut').value='';
	$('paciente').value='';
	$('pac_id').value=0;
    }

    autocompletar_pacientes = new AutoComplete(
    'pac_rut', 'autocompletar_sql.php',
    function() {
    if($('pac_rut').value.length<2) return false;
    return {
    method: 'get',
    parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
    }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);
</script>