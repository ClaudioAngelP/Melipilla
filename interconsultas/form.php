<?php    require_once('../../conectar_db.php');    $institucionhtml = desplegar_opciones("institucion_solicita", "instsol_id, instsol_desc",'','true','ORDER BY instsol_desc'); ?><script>    realizar_busqueda = function(pagina)    {        var myAjax = new Ajax.Updater('resultado', 'interconsultas/listar_interconsultas.php',         {            method: 'get',             parameters: 'tipo=estado_interconsultas&'+$('busqueda').serialize()+'&pagina='+pagina	});    }    abrir_ficha = function(id)    {        inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');	inter_ficha.focus();    }    abrir_oa = function(id)    {        inter_ficha = window.open('interconsultas/visualizar_oa.php?oa_id='+id,	'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');	inter_ficha.focus();    }        buscar_citacion_inter=function(inter_id,esp_id,pac_id)    {                /*        if($('txt_nomd_id').value!=nomd_id)        {            alert("No ha seleccionado examenes de la solicitud que desea agendar");            return;        }        if($('txt_examenes').value=='')        {            alert("No ha seleccionado examenes para realizar agendamiento");            return;        }        */        top=Math.round(screen.height/2)-250;        left=Math.round(screen.width/2)-340;        new_win = window.open('prestaciones/ingreso_nominas/buscar_citacion.php?nomd_id=0&interconsulta=1&esp_id='+esp_id+'&inter_id='+inter_id+'&pac_id='+pac_id,        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+        'menubar=no, scrollbars=yes, resizable=no, width=1200, height=600, '+        'top='+top+', left='+left);        new_win.focus();            }    //--------------------------------------------------------------------------    gestiones_citacion_inter=function(inter_id)    {        top=Math.round(screen.height/2)-250;        left=Math.round(screen.width/2)-340;        new_win = window.open('prestaciones/ingreso_nominas/gestionar_citacion.php?inter=1&inter_id='+inter_id,        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+        'top='+top+', left='+left);        new_win.focus();    }        //--------------------------------------------------------------------------		    $('buscar').focus();</script><center>    <table width='1000'>        <tr>            <td>                <div class='sub-content'>                    <div class='sub-content'>                        <img src='iconos/chart_organisation.png'> <b>Listado de Interconsultas</b>                    </div>                    <div class='sub-content'>                        <form name='busqueda' id='busqueda' onSubmit='return false;'>                            <table style='width:100%;'>                                <tr>                                    <td style='text-align: right;'>Buscar:</td>                                    <td>                                        <input type='text' name='buscar' id='buscar' size=60>                                    </td>                                </tr>                                <tr>                                    <td style='text-align: right;'>Instituci&oacute;n Solicitante:</td>                                    <td width=75% style='text-align: left;'>                                        <input type='hidden' id='inst_id1' name='inst_id1' value=''>                                        <input type='text' id='institucion1' name='institucion1' size=40>                                    </td>                                </tr>                                <tr>                                    <td style='text-align: right;'>Ordenar por:</td>                                    <td>                                        <select id='orden' name='orden'>                                            <option value=4 SELECTED>N&uacute;mero de Folio</option>                                            <option value=0>Fecha Ingreso</option>                                            <option value=1>Rut</option>                                            <option value=2>Paterno - Materno - Nombre(s)</option>                                            <option value=3>Especialidad</option>                                        </select>                                        <input type='checkbox' name='ascendente' id='ascendente' CHECKED> Ascendente                                    </td>                                </tr>                                <tr>                                    <td style='text-align:right;'>Estado Salida:</td>                                    <td>                                        <select id='tipo_salida' name='tipo_salida'>                                            <option value='-1'>(Todas las salidas...)</option>                                            <option value='0'>Para Agendar</option>                                            <option value='1'>Atenci&oacute;n Realizada</option>                                            <option value='2'>Procedimiento Informado</option>                                            <option value='3'>Indicaci&oacute;n M&eacute;dica para reevaluaci&oacute;n</option>                                            <option value='4'>Atenci&oacute;n otorgada en el extrasistema</option>                                            <option value='5'>Cambio asegurador</option>                                            <option value='6'>Renuncia o rechazo voluntario del usuario</option>                                            <option value='7'>Recuperaci&oacute;n espont&aacute;nea</option>                                            <option value='8'>Tres inasistencias</option>                                            <option value='9'>Fallecimiento</option>                                            <option value='10'>Solicitud de indicaci&oacute;n duplicada</option>                                            <option value='11'>Contacto no corresponde</option>                                            <option value='12'>No corresponde realizar cirug&iacute;a</option>                                        </select>                                    </td>                                    <td id="td_total"></td>                                </tr>                                <tr>                                    <td style='text-align:right;'>Tipo Interconsulta:</td>                                    <td>                                        <select id='tipo_inter' name='tipo_inter'>                                            <option value='-1'>(Todos los Tipos...)</option>                                            <option value='0'>Externas</option>                                            <option value='1'>Internas</option>                                        </select>                                    </td>                                    <td id="td_total"></td>                                </tr>                                <tr>                                    <td style='text-align:right;'>Estado Interconsulta:</td>                                    <td>                                        <select id='estado_inter' name='estado_inter'>                                            <option value='-2'>(Todos los Estados...)</option>                                            <option value='-1'>Sin Recepcionar</option>                                            <option value='0'>Espera Validaci&oacute;n...</option>                                            <option value='1'>Aceptado</option>                                            <option value='2'>Se Contrareferie a APS</option>                                            <option value='3'>Se Contrarefiere a Hospital de la RED</option>                                            <option value='4'>Se Deriva a Red de atenci&oacute;n</option>                                            <option value='5'>Se Deriva a Macro Red</option>                                            <option value='6'>Se Deriva a otra especialidad</option>                                            <option value='7'>Se Deriva a Procedimiento</option>                                        </select>                                    </td>                                    <td id="td_total"></td>                                </tr>                                <tr>                                    <td colspan=2>                                        <center><input type='button' value='Actualizar Listado...' onClick='realizar_busqueda(0);'></center>                                    </td>                                </tr>                            </table>                        </form>                    </div>                    <div class='sub-content2' id='resultado' style='min-height:250px;height:auto !important;height:250px;'>                        <center>(No se ha efectuado una b&uacute;squeda...)</center>                    </div>                </div>            </td>        </tr>    </table></center><script>    seleccionar_inst1 = function(d)    {        $('inst_id1').value=d[0];        $('institucion1').value=d[2].unescapeHTML();    }    autocompletar_institucion1 = new AutoComplete(      'institucion1',       'autocompletar_sql.php',      function() {        if($('institucion1').value.length<3) return false;        return {          method: 'get',          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('institucion1').value)        }      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);</script>			