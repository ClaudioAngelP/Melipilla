<?php
    require_once('../../conectar_db.php');
    $e=cargar_registros_obj("SELECT * FROM especialidades ORDER BY esp_desc;");
    $p=cargar_registros_obj("SELECT * FROM doctores ORDER BY doc_nombres");	
    $t=cargar_registros_obj("SELECT DISTINCT nom_motivo FROM nomina ORDER BY nom_motivo");
?>
<html>
    <title>Deshacer Bloqueos de Agenda</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        function validacion_fecha2(obj) {
            var obj=$(obj);
            if(trim(obj.value)=='') {
                obj.value='';
                obj.style.background='skyblue';
                return true;
            } else
                return validacion_fecha(obj);
        }

        function validacion_hora2(obj) {
            var obj=$(obj);
            if(trim(obj.value)=='') {
                obj.value='';
                obj.style.background='skyblue';
                return true;
            } else
                return validacion_hora(obj);
        }

        function listado() {
            var myAjax=new Ajax.Updater('listado','listar_bloqueos.php',
            {
                method:'post',
		parameters:$('filtro').serialize()
            });
        }

        function recuperar() {
            if(!confirm("&iquest;Est&aacute; seguro que desea recuperar los cupos bloqueados?".unescapeHTML())) return;
            var myAjax=new Ajax.Updater('listado','sql_recuperar_bloqueos.php',
            {
                method:'post',
                parameters:$('filtro').serialize()
            });
        }

        configurar_calendarios=function() {
            Calendar.setup({
                inputField     :    'fecha1',         // id of the input field
                ifFormat       :    '%d/%m/%Y',       // format of the input field
                showsTime      :    false,
                button         :   'fecha1_boton'
            });
    
            Calendar.setup({
                inputField     :    'fecha2',
                ifFormat       :    '%d/%m/%Y',
                showsTime      :    false,
                button         :   'fecha2_boton'
            });
        
            validacion_hora2($('hora1'));
            validacion_hora2($('hora2'));
        }
    </script>
    <body class='fuente_por_defecto popup_background' onLoad='configurar_calendarios();'>
        <div class='sub-content'>
            <img src='../../iconos/calendar_delete.png'>
            <b>Deshacer Bloqueos de Agenda</b>
        </div>
        <form id='filtro' name='filtro' onSubmit='return false;'>
            <table style='width:100%'>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Fecha:</td>
                    <td>
                        <input type="text" id="fecha1" name="fecha1" size=10 style='text-align:center;'>
                        <img src="../../iconos/calendar.png" id="fecha1_boton">
                        <input type="text" id="fecha2" name="fecha2" size=10 style='text-align:center;'>
                        <img src="../../iconos/calendar.png" id="fecha2_boton">
                    </td>
                </tr>
                <tr>
                    <td class='tabla_fila2' style='text-align:right;'>Hora:</td>
                    <td colspan=2>
                        desde <input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:16px;' />
                        hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:16px;' />
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Especialidades:</td>
                    <td>
                        <select id='esp_id' name='esp_id'>
                            <option value=''>(Todas...)</option>
                            <?php 
                            for($i=0;$i<sizeof($e);$i++) {
                                print("<option value='".$e[$i]['esp_id']."'>".$e[$i]['esp_desc']."</option>");
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Profesionales:</td>
                    <td>
                        <select id='doc_id' name='doc_id'>
                            <option value=''>(Todos...)</option>
                            <?php
                            for($i=0;$i<sizeof($p);$i++) {
                                print("<option value='".$e[$i]['doc_id']."'>".$p[$i]['doc_nombres']." ".$p[$i]['doc_paterno']." ".$p[$i]['doc_materno']."</option>");
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align:right;' class='tabla_fila2'>Tipo Atenci&oacute;n:</td>
                    <td>
                        <select id='nom_motivo' name='nom_motivo'>
                            <option value=''>(Todos...)</option>
                            <?php
                            for($i=0;$i<sizeof($t);$i++) {
                                print("<option value='".$t[$i]['nom_motivo']."'>".$t[$i]['nom_motivo']."</option>");
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <center>
                            <input type='button' id='' name='' value='Realizar B&uacute;squeda...' onClick='listado();' />
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        <div class='sub-content2' style='height:300px;overflow:auto;' id='listado'>
        </div>
        <center>
            <input type='button' id='' name='' value='Recuperar Cupos de Atenci&oacute;n Bloqueados...' onClick='recuperar();' /><br/><br/>
        </center>
    </body>
</html>