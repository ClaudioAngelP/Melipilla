<?php
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    require_once("../../../conectar_db.php");
    ///$especialidades = desplegar_opciones("especialidades", "esp_id, esp_desc",'1','esp_id IN ('._cav(20001),')', 'ORDER BY esp_desc');
    //$servs="'".str_replace(',','\',\'',_cav2(20001))."'";
    //$servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.") ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script type="text/javascript" >
    var bloquear_ficha=false;
    var bloquear_fusion=false;
    var datos="";
    //--------------------------------------------------------------------------
    pistolear_ficha = function (call)
    {
        if(bloquear_ficha)
        {
            return;
        }
        var ficha='';
        if(call==1)
        {
            if(trim($j('#ficha_original').val())=="")
            {
                alert("Debe Ingresar Nro de Ficha o Rut del Paciente");
                return;
            }
            if(trim($j('#ficha_original').val())=="0")
            {
                alert("Debe Ingresar Nro de Ficha o Rut del Paciente valido");
                return;
            }
            ficha=trim($j('#ficha_original').val());
        }
        if(call==2)
        {
            if(trim($j('#ficha_remplazo').val())=="")
            {
                alert("Debe Ingresar Nro de Ficha o Rut del Paciente");
                return;
            }
            if(trim($j('#ficha_remplazo').val())=="0")
            {
                alert("Debe Ingresar Nro de Ficha o Rut del Paciente valido");
                return;
            }
            ficha=trim($j('#ficha_remplazo').val());
        }
        bloquear_ficha=true;
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/fusion_fichas/buscar_ficha.php',
        {
            method:'post',
            parameters:'ficha='+ficha,
            onComplete:function(r)
            {
                datos=r.responseText.evalJSON(true);
                llenar_datos(call);
            }
        });
        bloquear_ficha=false;
    }
    //--------------------------------------------------------------------------
    llenar_datos= function (call)
    {
        if(datos[0]!=false)
        {
            if(call==1)
            {
                $('pac_id_original').value=datos[0][0]['pac_id'];
                $('txt_ficha_original').value=datos[0][0]['pac_ficha'];
                $('txt_rut_original').value=datos[0][0]['pac_rut'];
                $('txt_pasaporte_original').value=datos[0][0]['pac_pasaporte'];
                $('txt_nombres_original').value=datos[0][0]['pac_nombres'];
                $('txt_apellidos_original').value=datos[0][0]['pac_appat']+' '+datos[0][0]['pac_apmat'];
                $('txt_sexo_original').value=datos[0][0]['sex_desc'];
                $('txt_fechanac_original').value=datos[0][0]['pac_fc_nac'];
                $('txt_direccion_original').value=datos[0][0]['pac_direccion']+' '+datos[0][0]['ciud_desc'];
                $('txt_fono_original').value=datos[0][0]['pac_fono'];
                $('txt_prev_original').value=datos[0][0]['prev_desc'];
            }
            if(call==2)
            {
                $('pac_id_remplazo').value=datos[0][0]['pac_id'];
                $('txt_ficha_remplazo').value=datos[0][0]['pac_ficha'];
                $('txt_rut_remplazo').value=datos[0][0]['pac_rut'];
                $('txt_pasaporte_remplazo').value=datos[0][0]['pac_pasaporte'];
                $('txt_nombres_remplazo').value=datos[0][0]['pac_nombres'];
                $('txt_apellidos_remplazo').value=datos[0][0]['pac_appat']+' '+datos[0][0]['pac_apmat'];
                $('txt_sexo_remplazo').value=datos[0][0]['sex_desc'];
                $('txt_fechanac_remplazo').value=datos[0][0]['pac_fc_nac'];
                $('txt_direccion_remplazo').value=datos[0][0]['pac_direccion']+' '+datos[0][0]['ciud_desc'];
                $('txt_fono_remplazo').value=datos[0][0]['pac_fono'];
                $('txt_prev_remplazo').value=datos[0][0]['prev_desc'];
            }
        }
        else
        {
            limpiar_datos(call);
            alert("Datos de la ficha ingresada no han sido encontrados");
            return;
        }
    }
    //--------------------------------------------------------------------------
    limpiar_datos= function (call)
    {
        if(call==1)
        {
            $('pac_id_original').value=0;
            $('txt_ficha_original').value="";
            $('txt_rut_original').value="";
            $('txt_pasaporte_original').value="";
            $('txt_nombres_original').value="";
            $('txt_apellidos_original').value="";
            $('txt_sexo_original').value="";
            $('txt_fechanac_original').value="";
            $('txt_direccion_original').value="";
            $('txt_fono_original').value="";
            $('txt_prev_original').value="";
        }
        if(call==2)
        {
            $('pac_id_remplazo').value=0;
            $('txt_ficha_remplazo').value="";
            $('txt_rut_remplazo').value="";
            $('txt_pasaporte_remplazo').value="";
            $('txt_nombres_remplazo').value="";
            $('txt_apellidos_remplazo').value="";
            $('txt_sexo_remplazo').value="";
            $('txt_fechanac_remplazo').value="";
            $('txt_direccion_remplazo').value="";
            $('txt_fono_remplazo').value="";
            $('txt_prev_remplazo').value="";
        }
        
    }
    //--------------------------------------------------------------------------
    fusionar=function()
    {
        if(bloquear_fusion)
        {
            return;
        }
        if($('pac_id_original').value==0)
        {
            alert("No se ha seleccionado ficha original para realizar la fusi\u00f3n de fichas");
            return;
        }
        if($('pac_id_remplazo').value==0)
        {
            alert("No se ha seleccionado ficha a remplazar para realizar la fusi\u00f3n de fichas");
            return;
        }
        if($('txt_ficha_original').value==0 || $('txt_ficha_original').value=="")
        {
            alert("No se puede realiazar fusi\u00f3n ya que el paciente ingresado original no tiene ficha creada");
            return;
        }
        if($('txt_ficha_remplazo').value==0 || $('txt_ficha_remplazo').value=="")
        {
            alert("No se puede realiazar fusi\u00f3n ya que el paciente ingresado para remplazaar no tiene ficha creada");
            return;
        }
        if(($('pac_id_original').value*1)==($('pac_id_remplazo').value*1))
        {
            alert("No se puede realizar fusi\u00f3n de fichas ya que ha seleccionado la misma ficha");
            return;
        }
        var rut_igual=false;
        if($('txt_rut_original').value==$('txt_pasaporte_remplazo').value)
        {
            rut_igual=true;
        }
        if(!rut_igual)
        {
            if(!confirm('&iquest;El rut del paciente en ambas fichas son distintas desea relizar la fusion de fichas de todas formas?.'.unescapeHTML()))
            {
                return;
            }
        }
        else
        {
            if(!confirm('&iquest;Est&aacute; seguro que desea fusionar las fichas seleccionadas?.'.unescapeHTML())) {
                return;
            }
        }
        bloquear_fusion=true;
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/fusion_fichas/fusionar_ficha.php',
        {
            method:'post',
            parameters:$('pac_id_original').serialize()+'&'+$('pac_id_remplazo').serialize(),
            onComplete:function(r)
            {
                if(r.responseText=='1')
                {
                    alert('ERROR: Error al fusionar ficha original sin valor, contacte al administrador.');
                    return;
                }
                if(r.responseText=='2')
                {
                    alert('ERROR: Error al fusionar ficha remplazo sin valor, contacte al administrador.');
                    return;
                }
                resp=r.responseText.evalJSON(true);
                if(resp[0]=='OK')
                {
                    alert("La fusion de Fichas se he realizado con exito");
                    limpiar_datos(1);
                    limpiar_datos(2);
                    $('ficha_original').value="";
                    $('ficha_remplazo').value="";
                    return;
                }
                
                //llenar_datos(call);
            }
        });
        bloquear_fusion=false;
    }
    
</script>
<style type="text/css">
    
</style>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<body>
<form action="" id='form_fusion' autocomplete="off" onsubmit="return false;">
<center>
    <table>
        <tr>
            <td style="width: 1090px;">
                <form name='form_doc' id='form_doc'>
                    <input type='hidden' id='pac_id_original' name='pac_id_original' value='0' />
                    <input type='hidden' id='pac_id_remplazo' name='pac_id_remplazo' value='0' />
                    <div class='sub-content'>
                        <table width=100%>
                            <tr>
                                <td valign='top' >
                                    <div class='sub-content'>
                                        <img src='iconos/table_edit.png' />
                                        <b>Fusi&oacute;n de Fichas</b>
                                    </div>
                                    <div class='sub-content'>
                                        <div class='sub-content'>
                                            <table style="width: 100%">
                                                <tr class="tabla_header" style="text-align:left;">
                                                    <td><b>FICHA ORIGINAL</b></td>
                                                </tr>
                                            </table>
                                            <div class='sub-content'>
                                                <table id="table_codbarras_original">
                                                    <tr>
                                                        <td style="text-align:right;">
                                                            <img src="abastecimiento/hoja_cargo/barras.png" style="width: 25px;height: 30px;"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" onblur="this.style.border=&quot;&quot;;this.style.background=&quot;&quot;;" onfocus="this.style.border=&quot;3px dashed red&quot;;this.select();" onkeyup="if(event.which==13) pistolear_ficha(1);else limpiar_datos(1);" style="font-size: 15px; text-align: center;" size="25" name="ficha_original" id="ficha_original" />
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table style="width: 100%">
                                                    <tr class="tabla_header" style="text-align:left;">
                                                        <td><b>Datos Ficha</b></td>
                                                    </tr>
                                                </table>
                                                <table>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Nro Ficha:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_ficha_original' name='txt_ficha_original' size='15' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Rut:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_rut_original' name='txt_rut_original' size='15' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Pasaporte:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_pasaporte_original' name='txt_pasaporte_original' size='15' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Previsi&oacute;n:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_prev_original' name='txt_prev_original' size='40' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Nombres:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_nombres_original' name='txt_nombres_original' size='60' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Apellidos:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_apellidos_original' name='txt_apellidos_original' size='60' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Sexo:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_sexo_original' name='txt_sexo_original' size='20' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Fecha Nacimiento:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_fechanac_original' name='txt_fechanac_original' size='40' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Fono:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_fono_original' name='txt_fono_original' size='40' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Direcci&oacute;n:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_direccion_original' name='txt_direccion_original' size='60' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class='sub-content'>
                                            <table style="width: 100%">
                                                <tr class="tabla_header" style="text-align:left;">
                                                    <td><b>FICHA A REMPLAZAR</b></td>
                                                </tr>
                                            </table>
                                            <div class="sub-content">
                                                <table id="table_codbarras_remplazo">
                                                    <tr>
                                                        <td style="text-align:right;">
                                                            <img src="abastecimiento/hoja_cargo/barras.png" style="width: 25px;height: 30px;"/>
                                                        </td>
                                                        <td>
                                                            <input type="text" onblur="this.style.border=&quot;&quot;;this.style.background=&quot;&quot;;" onfocus="this.style.border=&quot;3px dashed red&quot;;this.select();" onkeyup="if(event.which==13) pistolear_ficha(2);else limpiar_datos(2);" style="font-size: 15px; text-align: center;" size="25" name="ficha_remplazo" id="ficha_remplazo" />
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table style="width: 100%">
                                                    <tr class="tabla_header" style="text-align:left;">
                                                        <td><b>Datos Ficha</b></td>
                                                    </tr>
                                                </table>
                                                <table>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Nro Ficha:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_ficha_remplazo' name='txt_ficha_remplazo' size='15' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Rut:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_rut_remplazo' name='txt_rut_remplazo' size='15' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Pasaporte:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_pasaporte_remplazo' name='txt_pasaporte_remplazo' size='15' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Previsi&oacute;n:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_prev_remplazo' name='txt_prev_remplazo' size='40' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Nombres:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_nombres_remplazo' name='txt_nombres_remplazo' size='60' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Apellidos:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_apellidos_remplazo' name='txt_apellidos_remplazo' size='60' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Sexo:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_sexo_remplazo' name='txt_sexo_remplazo' size='20' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Fecha Nacimiento:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_fechanac_remplazo' name='txt_fechanac_remplazo' size='40' value='' disabled/>
                                                        </td>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Fono:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_fono_remplazo' name='txt_fono_remplazo' size='40' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:left;white-space:nowrap;' class='tabla_fila2'>Direcci&oacute;n:</td>
                                                        <td class='tabla_fila'>
                                                            <input type='text' id='txt_direccion_remplazo' name='txt_direccion_remplazo' size='60' value='' disabled/>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <center>
                                            <input type='button' id='btn_cambiar' name='btn_cambiar' value='Fusionar Fichas' onClick='fusionar();'/>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </td>
        </tr>
    </table>
</center>
</form>
</body>
</html>
<script type="text/javascript" >
    
</script>