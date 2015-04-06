<?php
    require_once('../../conectar_db.php');
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(19),')','ORDER BY bod_glosa');
    $centroshtml = desplegar_opciones("centro_costo", "centro_ruta, centro_nombre, length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel, centro_medica", '1', 'centro_medica AND centro_nivel=2',	'ORDER BY centro_ruta');
    $servicioshtml = desplegar_opciones("centro_costo", "centro_ruta, centro_nombre, length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel, centro_medica", '1', 'centro_medica AND centro_nivel=3',	'ORDER BY centro_ruta');
    $doctoreshtml = desplegar_opciones("doctores", "doc_rut, doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS nombre", '1', '1=1', 'ORDER BY nombre');
?>

<script>
    
    marcar_todo = function ()
    {
        var estado;
        estado = false;
        $('centro_c').checked = estado;
        ver_centros(estado);
        $('fecha_c').checked = estado;
	ver_fecha(estado);
        $('med_c').checked = estado;
	ver_medico(estado);
        $('pac_c').checked = estado;
	ver_paciente(estado);
        $('art_c').checked = estado;
	ver_articulo(estado);
        $('receta_c').checked = estado;
	ver_receta(estado);
	
    }
    
    __fecha_hoy='<?php echo date('d/m/Y')?>';
    comprobar_fecha=function()
    {
        if($('fecha').disabled)
            return;

        if(!isDate($('fecha').value))
        {
            $('fecha').style.background='red';
        }
        else
        {
            $('fecha').style.background='';
        }
    }

//****************************************************
    ver_fecha = function(ver)
    {
        if(!ver)
        {
            $('fecha1').value='';
            $('fecha1').disabled=true;
            $('fecha1_boton').style.display='none';
            $('fecha2').value='';
            $('fecha2').disabled=true;
            $('fecha2_boton').style.display='none';
        }
        else
        {
            $('fecha1').value=__fecha_hoy;
            $('fecha1').disabled=false;
            $('fecha1_boton').style.display='';
            $('fecha2').value=__fecha_hoy;
            $('fecha2').disabled=false;
            $('fecha2_boton').style.display='';
        }
    }
//****************************************************
    ver_paciente = function(ver)
    {
        if(!ver)
        {
            $('rut_paciente').value='';
            $('rut_paciente').disabled=true;
            $('buscar_paciente').style.display='none';
            $('pac_id').value=-1;
            $('nombre_paciente').innerHTML='';
            $('rut_paciente').value='';
        }
        else
        {
            $('rut_paciente').disabled=false;
            $('buscar_paciente').style.display='';
        }
    }
//****************************************************
    ver_articulo = function(ver)
    {
        if(!ver)
        {
            $('art_codigo').value='';
            $('art_codigo').disabled=true;
            $('buscar_articulo').style.display='none';
            $('nombre_articulo').innerHTML='';
            $('nombre_articulo').style.display='none';
            $('art_id').value=-1;
        }
        else
        {
            $('art_codigo').disabled=false;
            $('buscar_articulo').style.display='';
        }
    }
//****************************************************
    ver_medico = function(ver)
    {
        if(!ver)
        {
            $('rut_medico').value='';
            $('nombre_medico').value=-1;
            $('rut_medico').disabled=true;
            $('nombre_medico').disabled=true;
        }
        else
        {
            $('rut_medico').disabled=false;
            $('nombre_medico').disabled=false;
        }
    }
//****************************************************

    ver_receta = function(ver)
    {
        if(!ver)
        {
            $('receta_num').value='';
            $('receta_num').disabled=true;
            $('receta_id').value=-1;
        }
        else
        {
            $('receta_num').disabled=false;
        }
    }
//****************************************************

    ver_centros = function(ver)
    {
        if(!ver)
        {
            $('centro_costo').disabled=true;
            $('centro_servicio').disabled=true;
        }
        else
        {
            $('centro_costo').disabled=false;
            $('centro_servicio').disabled=false;
        }
    }
//****************************************************

    abrir_paciente = function()
    {
        $('cargar_paciente').style.display='';
        var myAjax = new Ajax.Request(
        'recetas/ver_recetas/abrir_paciente.php',
        {
            method: 'get',
            parameters: 'pac_rut='+$('rut_paciente').value,
            onComplete: function(respuesta)
            {
                $('cargar_paciente').style.display='none';
                try
                {
                    datos = respuesta.responseText.evalJSON(true);
                }
                catch (err)
                {
                    alert('ERROR:\n\n'+err);
                }
                if(datos!=false)
                {
                    $('pac_id').value=datos[1][0];
                    $('nombre_paciente').innerHTML=datos[1][1];
                    $('nombre_paciente').style.display='';
                }
                else
                {
                    $('pac_id').value=-1;
                    $('nombre_paciente').innerHTML='';
                    $('nombre_paciente').style.display='none';
                }
            }
        }
        );
    }
//****************************************************

    abrir_articulo = function()
    {
        $('cargar_articulo').style.display='';
        var myAjax = new Ajax.Request(
        'recetas/ver_recetas/abrir_articulo.php',
        {
            method: 'get',
            parameters: 'art_codigo='+$('art_codigo').value,
            onComplete: function(respuesta)
            {
                $('cargar_articulo').style.display='none';
                try
                {
                    datos = respuesta.responseText.evalJSON(true);
                }
                catch (err)
                {
                    alert('ERROR:\n\n'+err);
                }
                if(datos!=false)
                {
                    $('nombre_articulo').innerHTML=datos[1][1];
                    $('nombre_articulo').style.display='';
                    $('art_id').value=datos[1][0];
                }
                else
                {
                    $('nombre_articulo').innerHTML='';
                    $('nombre_articulo').style.display='none';
                    $('art_id').value=-1;
                }
            }
        }
        );
    }

//****************************************************

    generar_informe = function()
    {
        if($('nombre_medico').value==-1 && $('med_c').checked)
        {
            alert('No ha seleccionado un m&eacute;dico v&aacute;lido.'.unescapeHTML());
            return;
        }
        if($('pac_id').value==-1 && $('pac_c').checked)
        {
            alert('No ha seleccionado un paciente v&aacute;lido.'.unescapeHTML());
            return;
        }
        if($('art_id').value==-1 && $('art_c').checked)
        {
            alert('No ha seleccionado un medicamento v&aacute;lido.'.unescapeHTML());
            return;
        }
        if (($('receta_num').value=='' && $('receta_c').checked) || (!/^([0-9])*$/.test($('receta_num').value) ))
        {
            alert('No ha seleccionado un numero de receta v&aacute;lido.'.unescapeHTML());
            return;
        }
        
        var win = new Window("win_informe", {className: "alphacube", top:40, left:0,
                          width: 700, height: 450,
                          title: '<img src="iconos/pill.png"> B&uacute;squeda de Recetas Despachadas',
                          minWidth: 600, minHeight: 450,
                          maximizable: false, minimizable: false,
                          wiredDrag: true });

        win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
        win.setAjaxContent('recetas/ver_recetas/buscar_recetas.php',
        {
            method: 'get',
			parameters: $('ops').serialize(),
			evalScripts: true
        }
        );
        $('win_informe').win_obj = win;
        win.setDestroyOnClose();
        win.showCenter();
        win.show();
    }
//****************************************************

    imprimir_informe = function()
    {
        if( !document.getElementById("centro_c").checked)
        {
            alert('Seleccione Servicio v&aacute;lido.'.unescapeHTML());
            return;
        }
        if( !document.getElementById("fecha_c").checked)
        {
            alert('Seleccione fecha v&aacute;lida.'.unescapeHTML());
            return;
        }
        consulta_enviada=true;
        var myAjax = new Ajax.Request(
        'recetas/ver_recetas/recetas_servicios.php',
        {
            method: 'get',
            parameters: $('ops').serialize(),
            onComplete: function(informe)
            {
                imprimirHTML(informe.responseText);
                consulta_enviada=false;
            }
        });
    }
//****************************************************

    cargar_servicios = function ()
    {
        centros = $('centro_costo');
        servs = $('centro_servicio');
        valor = centros.value;
        servicios = servs.options;
        seleccionado = false;
        for(i=0;i < servicios.length;i++)
        {
            valoropt = servicios[i].value.substring(0,valor.length);
            if(valoropt==valor || servicios[i].value==-1)
            {
                servicios[i].style.display='';
            }
            else
            {
                servicios[i].style.display='none';
            }
        }
        servs.value=-1;
    }
//****************************************************

    ocultar_servicios = function()
    {
        servs = $('centro_servicio');
        servicios = servs.options;
        for(i=0;i < servicios.length;i++)
        {
            if(servicios[i].value==-1)
            {
                servicios[i].style.display='';
            }
            else
            {
                servicios[i].style.display='none';
            }
        }
    }

//****************************************************

    nombre2rut = function()
    {
        $('rut_medico').value=$('nombre_medico').value;
    }

//****************************************************
      
    rut2nombre = function()
    {
        $('rut_medico').value=trim($('rut_medico').value);
        valor = $('rut_medico').value;
        opciones = $('nombre_medico').options;
        for(i=0;i<opciones.length;i++)
        {
            if(valor==opciones[i].value)
            {
                $('nombre_medico').value=valor;
                return;
            }
        }
        $('nombre_medico').value=-1;
    }
 
//****************************************************
marcar_todo();

</script>

<center>
    <table width=650>
        <tr>
            <td>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/pill.png'>
                        <b>B&uacute;squeda de Recetas</b>
                    </div>
                    <div class='sub-content'>
                        <form id='ops' name='ops' onSubmit='return false;'>
                            <table>
                                <tr>
                                    <td style='text-align: right;'>
                                        Ubicaci&oacute;n:
                                    </td>
                                    <td>
                                        <select id='bodega_id' name='bodega_id'>
                                            <?php echo $bodegashtml?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>
                                        Centro de Costo:
                                    </td>
                                    <td colspan=2>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='centro_c' name='centro_c' onClick='ver_centros(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <select id='centro_costo' name='centro_costo' onClick='cargar_servicios();'>
                                                        <?php echo $centroshtml?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select id='centro_servicio' name='centro_servicio'>
                                                        <option value=-1>(Seleccionar...)</option>
                                                        <?php echo $servicioshtml?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>
                                        Fecha:
                                    </td>
                                    <td colspan=2>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='fecha_c' name='fecha_c' onClick='ver_fecha(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <input type='text' name='fecha1' id='fecha1' onBlur='validacion_fecha();'
                                                    value='<?php echo date('d/m/Y');?>'
                                                    style='text-align: center;' size=15>
                                                    <img src='iconos/date_magnify.png' id='fecha1_boton' alt='Buscar Fecha...'
                                                    title='Buscar Fecha...'>
                                                    <input type='text' name='fecha2' id='fecha2' onBlur='validacion_fecha();'
                                                    value='<?php echo date('d/m/Y');?>'
                                                    style='text-align: center;' size=15>
                                                    <img src='iconos/date_magnify.png' id='fecha2_boton' alt='Buscar Fecha...'
                                                    title='Buscar Fecha...'>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>M&eacute;dico:</td>
                                    <td colspan=2>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='med_c' name='med_c' onClick='ver_medico(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <input type='text' id='rut_medico' name='rut_medico' size=15
                                                    onChange='rut2nombre();' style='text-align: center;'>
                                                </td>
                                                <td>
                                                    <select id='nombre_medico' name='nombre_medico' onChange='nombre2rut();'>
                                                        <option value=-1>(Seleccionar...)</option>
                                                        <?php echo $doctoreshtml?>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>Paciente:</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='pac_c' name='pac_c' onClick='ver_paciente(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <input type='hidden' id='pac_id' name='pac_id' value=-1>
                                                    <input type='text' name='rut_paciente' id='rut_paciente' size=15
                                                    style='text-align: center;' onChange='abrir_paciente();'>
                                                </td>
                                                <td>
                                                    <img src='iconos/zoom_in.png' id='buscar_paciente'
                                                    onClick='
                                                    busqueda_pacientes("rut_paciente", function() { abrir_paciente(); });
                                                    '
                                                    alt='Buscar Paciente...'
                                                    title='Buscar Paciente...'>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width=450>
                                        <img src='imagenes/ajax-loader1.gif' id='cargar_paciente' style='display: none;'>
                                            <span id='nombre_paciente'></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>Medicamento:</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='art_c' name='art_c' onClick='ver_articulo(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <input type='hidden' id='art_id' name='art_id' value=-1>
                                                    <input type='text' name='art_codigo' id='art_codigo' size=15
                                                    onChange='abrir_articulo();' style='text-align: center;'>
                                                </td>
                                                <td>
                                                    <img src='iconos/zoom_in.png' id='buscar_articulo'
                                                    onClick='
                                                    buscar_articulos("art_codigo", function() { abrir_articulo(); } );
                                                    '
                                                    alt='Buscar Medicamento...'
                                                    title='Buscar Medicamento...'>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <img src='imagenes/ajax-loader1.gif' id='cargar_articulo' style='display: none;'>
                                        <span id='nombre_articulo'></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>N&deg; Receta:</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>
                                                    <input type='checkbox' id='receta_c' name='receta_c' onClick='ver_receta(this.checked);' checked>
                                                </td>
                                                <td>
                                                    <input type='hidden' id='receta_id' name='receta_id' value=-1 >
                                                    <input type='text' name='receta_num' id='receta_num' size=15 style='text-align: center;'>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='text-align: right;'>Receta de Alta:</td>
									<td colspan=2>
                                        <select id='receta_a' name='receta_a'>
                                        <option value='0'>(Todas...)</option>
                                        <option value='1'>Altas</option>
                                        <option value='2'>Normales</option>
                                        </select>
                                    </td>
                                </tr>


                            </table>
                        </form>
                    </div>
                    <center>
                        <table>
                            <tr>
                                <td>
                                    <div class='boton' id='generar_boton'>
                                        <table>
                                            <tr>
                                                <td>
                                                    <img src='iconos/layout.png'>
                                                </td>
                                                <td>
                                                    <a href='#' onClick='generar_informe();'> Generar Informe...</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td>
                                    <div class='boton' id='imprimir_boton'>
                                        <table>
                                            <tr>
                                                <td>
                                                    <img src='iconos/printer.png'>
                                                </td>
                                                <td>
                                                    <a href='#' onClick='imprimir_informe();'> Receta Hospitalizados</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </center>
                </div>
            </td>
        </tr>
    </table>
</center>

<!--</div>
</td></tr>
</table>


</center>-->

<script>
  
    Calendar.setup({
        inputField     :    'fecha1',   // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });

    Calendar.setup({
        inputField     :    'fecha2',   // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'
    });
    
</script>
