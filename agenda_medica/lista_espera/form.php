<?php
    require_once('../../conectar_db.php');
    $especialidadhtml = desplegar_opciones("especialidades", "esp_id, esp_desc",'','true','ORDER BY esp_desc'); 
?>
<script>
    listado = function() {
        var params=$('esp_id').serialize()+'&'+$('tipo').serialize()+'&'+$('carp_id').serialize();
        params+='&'+$('filtro').serialize()+'&'+$('ordenar').serialize();
        params+='&'+$('ver').serialize()+'&'+$('ver_c').serialize();
        $('tab_listado_content').innerHTML='<br><br><img src="imagenes/ajax-loader2.gif"><br><br>Cargando'
        //if($('tipo').value!='H')
	script_lista='lista.php';
        //else
	//script_lista='lista_cerrada.php';
        var myAjax=new Ajax.Updater('tab_listado_content','agenda_medica/lista_espera/'+script_lista,
        {
            method:'post', evalScripts: true, 
            parameters: params
        });
    }

    listado_xls = function() {
        if($('tipo').value!='H')
            $('le_form').action='agenda_medica/lista_espera/lista.php';
        else
            $('le_form').action='agenda_medica/lista_espera/lista_cerrada.php';
        $('le_form').submit();
    }

    asignar_hora = function(inter_id,control_id) {
        l=(screen.availWidth/2)-340;
        t=(screen.availHeight/2)-200;
        params='inter_id='+inter_id+'&control_id='+control_id;
        win = window.open('agenda_medica/lista_espera/asignar_hora.php?'+params, 
        '_asigna_hora',
        'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
        'resizable=no, width=680, height=415');
        win.focus();
    }

    notificar = function(inter_id,control_id) {
        l=(screen.availWidth/2)-325;
        t=(screen.availHeight/2)-240;
        params='inter_id='+inter_id+'&control_id='+control_id;
        win = window.open('agenda_medica/lista_espera/notificar.php?'+params, 
        '_asigna_hora',
        'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
        'resizable=no, width=650, height=480');
        win.focus();
    }

    abrir_ic = function(id) {
        inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
        inter_ficha.focus();
    }

    abrir_oa = function(id) {
        inter_ficha = window.open('interconsultas/visualizar_oa.php?oa_id='+id,
	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
        inter_ficha.focus();
    }

    remover_ic = function(id) {
        inter_ficha = window.open('agenda_medica/lista_espera/remover_ic.php?inter_id='+id,
	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
        inter_ficha.focus();
    }

    remover_oa = function(id) {
        inter_ficha = window.open('agenda_medica/lista_espera/remover_ic.php?oa_id='+id,
	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
        inter_ficha.focus();
    }

    filtrar_carpeta = function(){
        if($('tipo').value=='H'){
            $('tag_carp').show();
            $('tag_esp').hide();
            $('carp_id').show();
            $('especialidad').hide();			
            $('ver_c').show();
	} else {
            $('tag_carp').hide();
            $('tag_esp').show();
            $('carp_id').hide();
            $('especialidad').show();	
            $('ver_c').hide();
	}
    }
</script>
<center>
    <form id='le_form' name='le_form' onSubmit='return false;' method='post' action='agenda_medica/lista_espera/lista.php'>
        <input type='hidden' id='xls' name='xls' value='1' />
        <div class='sub-content' style='width:950px;'>
            <div class='sub-content'>
                <img src='iconos/script.png'>
                <b>Lista de Espera Unificada</b>
            </div>
            <div class='sub-content'>
                <table>
                    <tr>
                        <td id='tag_esp' style='text-align:right;'>Especialidad:</td>
                        <td id='tag_carp' style='display: none;' style='text-align:right;'>Carpeta:</td>
                        <td>
                            <input type='hidden' id='esp_id' name='esp_id' value=''>
                            <input type='text' id='especialidad' value='' name='especialidad' size=30>
                            <select id='carp_id' name='carp_id' style='width:350px;display:none;'>
                                <option value='0'>(Totales por Carpeta...)</option>
                                <option value='-1'>(Todas las Carpetas...)</option>
                                <?php
                                $carp=cargar_registros_obj("SELECT * FROM orden_carpeta ORDER BY carp_nombre;");
                                for($i=0;$i<sizeof($carp);$i++) {
                                    print("<option value='".$carp[$i]['carp_id']."'>".htmlentities($carp[$i]['carp_nombre'])."</option>");
                                }
                                ?>
                            </select>
                            <select id='tipo' name='tipo' onClick='filtrar_carpeta();'>
                                <option value='N'>Nuevos</option>
                                <option value='C'>Control</option>
                                <option value='H'>Cerrada</option>
                            </select>
                            <select id='ordenar' name='ordenar'>
                                <option value='F'>Ordenar por Fecha</option>
                                <option value='P'>Ordenar por Prioridad</option>
                            </select>
                            <input type='button' value='Actualizar...'  onClick='listado();'>
                        </td>
                    </tr>
                    <tr>
                        <td style='text-align:right;'>Filtrar:</td>
                        <td>
                            <input type='text' id='filtro' name='filtro' size=40 onKeyPress='if(event.which==13) listado();'>
                            <select id='ver_c' name='ver_c' style='display:none;'>
                                <option value='-1' SELECTED>(Cualquier Tipo...)</option>
                                <option value='0'>Hosp. Quir&uacute;rgica</option>
                                <option value='1'>Hosp. M&eacute;dica</option>
                                <option value='2'>Procedimiento</option>
                            </select>
                            <select id='ver' name='ver'>
                                <option value='P'>Solo Pendientes</option>
                                <option value='R'>Solo Realizadas</option>
                                <option value='T'>Visualizar Todas</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='tabbed_content' style='height:270px;overflow:auto;font-size:10px!important;' id='tab_listado_content'>
            </div>
            <center>
                <input type='button' value='Descargar Listado en XLS...' onClick='listado_xls();' />
            </center>
        </div>
    </form>
</center>
<script> 
    ingreso_especialidades=function(datos_esp) {
        $('esp_id').value=datos_esp[0];
        $('especialidad').value=datos_esp[2];
    }
      
    /*
    seleccionar_carpeta = function(d) {
        $('carp_id').value=d[0];
	$('carpeta').value=d[2].unescapeHTML();
    }
		
    autocompletar_carpeta = new AutoComplete(
    'carpeta', 
    'autocompletar_sql.php',
    function() {
    if($('carpeta').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=carpeta2&'+$('carpeta').serialize()
    }
    }, 'autocomplete', 350, 100, 150, 2, 3, seleccionar_carpeta);
    */
      
    autocompletar_especialidades = new AutoComplete(
    'especialidad', 
    'autocompletar_sql.php',
    function() {
    if($('especialidad').value.length<3) return false;
    return {
    method: 'get',
    parameters: 'tipo=subespecialidad&cadena='+encodeURIComponent($('especialidad').value)
    }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);
    /*listado();*/ 
</script>